<?php
include('../connection.php');
$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
$columns = array( 
    0 =>'Sl No',
    1 =>'Appl No',
    2 =>'IA Reg Dt',
	3 =>'Court No',
	4 =>'Bench Desc',
	5 =>'JO Code'
);
if(strtotime($_POST['fromdate']) < strtotime($_POST['todate'])){
    $from_date = date("Y-m-d", strtotime($_POST['fromdate'])); // From Date
    $to_date = date("Y-m-d", strtotime($_POST['todate'])); // To Date
}
else{
    $to_date = date("Y-m-d", strtotime($_POST['fromdate'])); // To Date
    $from_date = date("Y-m-d", strtotime($_POST['todate'])); // From Date
}
$application = $_POST['application'];
$limit = $_POST['length']; // For No. of rows per page
$start = $_POST['start']; // For Offset

if($limit==-1)
    $limit_data ='';
else
    $limit_data =' LIMIT '.$limit. ' OFFSET '.$start;
$report1 = array();
$totalData="";
$totalFiltered="";


if($application=="")
{
$sql_cases_count_on_type="select alias_ia.bench_desc, alias_ia.court_id, alias_ia.ia_case_type, ia_type_name, alias_ia.ia_regno, alias_ia.ia_regyear, alias_ia.type_name, alias_ia.case_regisno, alias_ia.case_regisyear, alias_ia.date_of_ia_registration, alias_ia.jocode from 
( 

( SELECT case_type_t.reg_no as case_regisno, case_type_t.reg_year as case_regisyear, * FROM ia_filing 
left outer join case_type_t on ia_filing.regcasetype = case_type_t.case_type 
left outer join court_t on ia_filing.court_no = court_t.court_no 
where ia_filing.date_of_ia_registration BETWEEN '".$from_date."' AND '".$to_date."' ) 

UNION 

( SELECT case_type_t.reg_no as case_regisno, case_type_t.reg_year as case_regisyear, * FROM ia_filing_a 
left outer join case_type_t on ia_filing_a.regcasetype = case_type_t.case_type 
left outer join court_t on ia_filing_a.court_no = court_t.court_no 
where ia_filing_a.date_of_ia_registration BETWEEN  '".$from_date."' AND '".$to_date."') 

) 

alias_ia join ia_case_type_t on alias_ia.ia_case_type=ia_case_type_t.ia_case_type order by ia_type_name".$limit_data;
				
$stmt=$conn->prepare($sql_cases_count_on_type);
$result=$stmt->execute();
$cases_count_on_type_fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql_type_count="select alias_ia.bench_desc, alias_ia.court_id, alias_ia.ia_case_type, ia_type_name, alias_ia.ia_regno, alias_ia.ia_regyear, alias_ia.type_name, alias_ia.case_regisno, alias_ia.case_regisyear, alias_ia.date_of_ia_registration, alias_ia.jocode from 
( 

( SELECT case_type_t.reg_no as case_regisno, case_type_t.reg_year as case_regisyear, * FROM ia_filing 
left outer join case_type_t on ia_filing.regcasetype = case_type_t.case_type 
left outer join court_t on ia_filing.court_no = court_t.court_no 
where ia_filing.date_of_ia_registration BETWEEN '".$from_date."' AND '".$to_date."' ) 

UNION 

( SELECT case_type_t.reg_no as case_regisno, case_type_t.reg_year as case_regisyear, * FROM ia_filing_a 
left outer join case_type_t on ia_filing_a.regcasetype = case_type_t.case_type 
left outer join court_t on ia_filing_a.court_no = court_t.court_no 
where ia_filing_a.date_of_ia_registration BETWEEN  '".$from_date."' AND '".$to_date."') 

) 

alias_ia join ia_case_type_t on alias_ia.ia_case_type=ia_case_type_t.ia_case_type order by ia_type_name";

		
$stmt=$conn->prepare($sql_type_count);
$result=$stmt->execute();
$sql_type_count_fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalData = sizeof($sql_type_count_fetched);
$totalFiltered = $totalData ;
$total_case_count = 0;
		
}





else
{
$sql_cases_count_on_type="select alias_ia.bench_desc, alias_ia.court_id, alias_ia.ia_case_type, ia_type_name, alias_ia.ia_regno, alias_ia.ia_regyear, alias_ia.type_name, alias_ia.case_regisno, alias_ia.case_regisyear, alias_ia.date_of_ia_registration, alias_ia.jocode from 
( 
( SELECT case_type_t.reg_no as case_regisno, case_type_t.reg_year as case_regisyear, * FROM ia_filing 
left outer join case_type_t on ia_filing.regcasetype = case_type_t.case_type 
left outer join court_t on ia_filing.court_no = court_t.court_no 
where ia_filing.date_of_ia_registration BETWEEN '".$from_date."' AND '".$to_date."' AND ia_filing.ia_case_type = ".$application."
) 
UNION 
( SELECT case_type_t.reg_no as case_regisno, case_type_t.reg_year as case_regisyear, * FROM ia_filing_a 
left outer join case_type_t on ia_filing_a.regcasetype = case_type_t.case_type 
left outer join court_t on ia_filing_a.court_no = court_t.court_no 
where ia_filing_a.date_of_ia_registration BETWEEN  '".$from_date."' AND '".$to_date."'
AND ia_filing_a.ia_case_type = ".$application."
) 
) 
alias_ia join ia_case_type_t on alias_ia.ia_case_type=ia_case_type_t.ia_case_type order by ia_type_name".$limit_data;
		
$stmt=$conn->prepare($sql_cases_count_on_type);
$result=$stmt->execute();
$cases_count_on_type_fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql_type_count="select alias_ia.bench_desc, alias_ia.court_id, alias_ia.ia_case_type, ia_type_name, alias_ia.ia_regno, alias_ia.ia_regyear, alias_ia.type_name, alias_ia.case_regisno, alias_ia.case_regisyear, alias_ia.date_of_ia_registration, alias_ia.jocode from 
( 
( SELECT case_type_t.reg_no as case_regisno, case_type_t.reg_year as case_regisyear, * FROM ia_filing 
left outer join case_type_t on ia_filing.regcasetype = case_type_t.case_type 
left outer join court_t on ia_filing.court_no = court_t.court_no 
where ia_filing.date_of_ia_registration BETWEEN '".$from_date."' AND '".$to_date."' AND ia_filing.ia_case_type = ".$application."
) 
UNION 
( SELECT case_type_t.reg_no as case_regisno, case_type_t.reg_year as case_regisyear, * FROM ia_filing_a 
left outer join case_type_t on ia_filing_a.regcasetype = case_type_t.case_type 
left outer join court_t on ia_filing_a.court_no = court_t.court_no 
where ia_filing_a.date_of_ia_registration BETWEEN  '".$from_date."' AND '".$to_date."'
AND ia_filing_a.ia_case_type = ".$application."
) 
) 
alias_ia join ia_case_type_t on alias_ia.ia_case_type=ia_case_type_t.ia_case_type order by ia_type_name";
		
$stmt=$conn->prepare($sql_type_count);
$result=$stmt->execute();
$sql_type_count_fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalData = sizeof($sql_type_count_fetched);
$totalFiltered = $totalData ;
$total_case_count = 0;
}	



 foreach($cases_count_on_type_fetched as $case_count){
            $record['Sl No']= $start + 1;
            $record['Appl No']= $case_count["ia_type_name"]." ".$case_count["ia_regno"]." of ".$case_count["ia_regyear"]. " in maintainance ".$case_count["type_name"]." ".$case_count["case_regisno"]." of ".$case_count["case_regisyear"];
            $record['IA Reg Dt']= $case_count['date_of_ia_registration'];     
			$record['Court No']= $case_count['court_id'];     
			$record['Bench Desc']= $case_count['bench_desc'];    
            $record['JO Code']= $case_count['jocode'];			
            $report1[] = $record;
			$start++;
 }



$json_data = array(
    "draw" => intval($_POST['draw']),
    "recordsTotal" => intval($totalData),
    "recordsFiltered" =>intval($totalFiltered),
    "data" => $report1
);

echo json_encode($json_data);

?>