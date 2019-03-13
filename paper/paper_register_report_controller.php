<?php
include('../connection.php');

$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

// For dataTable :: STARTS
$columns = array( 
    0 =>'Case No',
    1 =>'Cause Title',
    2 =>'Advocate Name',
    3 =>'Despatched To',
    4 =>'Paper Type'
);

if(strtotime($_POST['from_date']) < strtotime($_POST['to_date'])){
    $from_date = date("Y-m-d", strtotime($_POST['from_date'])); // From Date
    $to_date = date("Y-m-d", strtotime($_POST['to_date'])); // To Date
}
else{
    $to_date = date("Y-m-d", strtotime($_POST['from_date'])); // To Date
    $from_date = date("Y-m-d", strtotime($_POST['to_date'])); // From Date
}

$limit = $_POST['length']; // For No. of rows per page
$start = $_POST['start']; // For Offset

if($limit==-1)
    $limit_data ='';
else
    $limit_data =' LIMIT '.$limit. ' OFFSET '.$start;

// Base Query
$sql="SELECT DISTINCT caseno, cino FROM index_register WHERE paperdate BETWEEN '".$from_date."' AND '".$to_date."' ORDER BY caseno ".$limit_data;


$stmt=$conn->prepare($sql);	
$result=$stmt->execute();	

// Base Data Fetched
$rec=$stmt->fetchAll(PDO::FETCH_ASSOC);

/* For getting the total no. of data :: STARTS */
$sql_count = "SELECT COUNT(DISTINCT caseno) FROM index_register WHERE paperdate BETWEEN '".$from_date."' AND '".$to_date."'";

$stmt=$conn->prepare($sql_count);	
$result=$stmt->execute();
$totalData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// For getting the total no. of data which is in this case equal to the total no. of filtered data as here is no search option in the dataTable
$totalFiltered = $totalData['0']['count'];

/* For getting the total no. of data :: ENDS */

$record = array();

foreach($rec as $data){
    if($data['caseno']!=""){

            /* For Case Number :: STARTS */
            $case_type_code = ltrim(substr($data['caseno'],1,3),'0');            
            $case_no = ltrim(substr($data['caseno'],4,7),'0');
            $case_year = substr($data['caseno'],11,4);

            $sql1 = "SELECT * FROM case_type_t WHERE case_type=".$case_type_code;
                
            $stmt=$conn->prepare($sql1);	
            $result=$stmt->execute();
            $rec1=$stmt->fetchAll(PDO::FETCH_ASSOC);

            $report['Case No'] = $rec1['0']['type_name'] . '<b>/</b>'. $case_no.'<b>/</b>'.$case_year; // Case No
            /* For Case Number :: ENDS */

            /* For Paper Type, Submit Date and Advocate Name:: STARTS */
            $sql2 = "SELECT * FROM index_register LEFT JOIN docu_type_t ON cast(index_register.description as int) = docu_type_t.docu_type WHERE index_register.caseno='".$data['caseno']."' ORDER BY index_register.srno";
            $stmt=$conn->prepare($sql2);	
            $result=$stmt->execute();
            $rec2=$stmt->fetchAll(PDO::FETCH_ASSOC);

            $report['Paper Type']=''; // Initializing with blank
            $report['Despatched To']=''; // Initializing with blank
            $report['Advocate Name']=''; // Initializing with blank

            foreach($rec2 as $data1){
                // Paper Type
                if($report['Paper Type']=="")
                    $report['Paper Type'] = "<b>".$data1['srno'].".  </b>".strtoupper($data1['docu_name']);
                else                
                    $report['Paper Type'] = $report['Paper Type'] ."<br><br><b>\n\n".$data1['srno'].".  </b>".strtoupper($data1['docu_name']);                
                
                
                // Advocate Name
                if($report['Advocate Name']==""){
                    if($data1['type']=='1')
                        $report['Advocate Name'] = "P   \t".strtoupper($data1['adv_name']);
                    else
                        $report['Advocate Name'] = "R   \t".strtoupper($data1['adv_name']);
                }
                    
                else{
                    if($data1['type']=='1')
                        $report['Advocate Name'] = $report['Advocate Name'] ."<br><br>\n\nP   \t".strtoupper($data1['adv_name']);
                    else
                        $report['Advocate Name'] = $report['Advocate Name'] ."<br><br>\n\nR   \t".strtoupper($data1['adv_name']);
                }

            }
            /* For Paper Type, Submit Date and Advocate Name:: ENDS */

            /* For Petitioner & Respondent's Name (Cause Title):: STARTS */
            $sql3 = "SELECT * FROM civil_t WHERE cino LIKE '".$data['cino']."'
                    UNION SELECT * FROM civil_t_a WHERE cino LIKE '".$data['cino']."'";
            $stmt=$conn->prepare($sql3);	
            $result=$stmt->execute();
            $rec3=$stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach($rec3 as $data3)
                $report['Cause Title'] = strtoupper($data3['pet_name'])."<br><br>\n\n".strtoupper($data3['res_name']);
            
            /* For Petitioner & Respondent's Name (Cause Title):: ENDS */
            
           

            $record[] = $report;
    }
}


$json_data = array(
    "draw" => intval($_POST['draw']),
    "recordsTotal" => intval($totalData['0']['count']),
    "recordsFiltered" =>intval($totalFiltered),
    "data" => $record
);

echo json_encode($json_data);

?>