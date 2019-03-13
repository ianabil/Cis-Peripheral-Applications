<?php
include('../connection.php');
$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

//dataTable starts
$columns = array( 
    0 =>'Sl No',
    1 =>'Case No',
    2 =>'Date of Registration',
    3 =>'Cause Title',
    4 =>'Court No',
    5 =>'Bench Description',
    6 =>'JO Code',
	7 =>'status'
);
$casetype = $_POST['casetype'];
$typename = $_POST['typename'];
$fromdate = $_POST['fromdate'];
$todate = $_POST['todate'];
$benchid = $_POST['benchid'];
$newToDate = date("Y-m-d", strtotime($todate));
$newFromDate = date("Y-m-d", strtotime($fromdate));

$limit = $_POST['length']; // For No. of rows per page
$start = $_POST['start']; // For Offset
if($limit==-1)
 $limit_data ='';
else
 $limit_data =' LIMIT '.$limit. ' OFFSET '.$start;

$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);









/* if starts for bench id HAS value*/
if($benchid != ""){
$sql="(select substring(case_no,12,4) as case_year,LTRIM(substring(case_no,5,7),'0') as case_number, to_char(civil_t.dt_regis,'dd-mm-yyyy') as dt,
pet_name, res_name, court_t.court_id as c_no, court_t.bench_desc as b_desc, court_t.calhc_jocode as jocode, 'N' from_civil_t_a 
from civil_t left outer join court_t on civil_t.court_no = court_t.court_no 
where LTRIM(substring(case_no,2,3),'0')=:casetype AND dt_regis BETWEEN :newFromDate AND :newToDate AND court_t.court_no = :benchid order by civil_t.dt_regis desc) 

UNION

(select substring(civil_t_a.case_no,12,4) as case_year, LTRIM(substring(civil_t_a.case_no,5,7),'0') as case_number, to_char(civil_t_a.dt_regis,'dd-mm-yyyy') as dt,
civil_t_a.pet_name, civil_t_a.res_name,court_t.court_id as c_no, court_t.bench_desc as b_desc, court_t.calhc_jocode as jocode, 'Y' from_civil_t_a 
from civil_t_a left outer join court_t on civil_t_a.court_no = court_t.court_no 
where LTRIM(substring(civil_t_a.case_no,2,3),'0')=:casetype AND civil_t_a.dt_regis BETWEEN :newFromDate AND :newToDate AND court_t.court_no = :benchid order by civil_t_a.dt_regis desc)".$limit_data;
 
 $stmt=$conn->prepare($sql);	
 $stmt->bindParam(':casetype', $casetype);
 $stmt->bindParam(':newFromDate', $newFromDate);
 $stmt->bindParam(':newToDate', $newToDate);
 $stmt->bindParam(':benchid', $benchid);
 $result=$stmt->execute();	
/* data fetched from main query */
 $rec=$stmt->fetchAll(PDO::FETCH_ASSOC);
 
 /* query for total no. of data - STARTS */
$sql_count = "SELECT COUNT(*) FROM ((select substring(case_no,12,4) as case_year,LTRIM(substring(case_no,5,7),'0') as case_number, to_char(civil_t.dt_regis,'dd-mm-yyyy') as dt,
pet_name, res_name, court_t.court_id as c_no, court_t.bench_desc as b_desc, court_t.calhc_jocode as jocode, 'N' from_civil_t_a 
from civil_t left outer join court_t on civil_t.court_no = court_t.court_no 
where LTRIM(substring(case_no,2,3),'0')=:casetype AND dt_regis BETWEEN :newFromDate AND :newToDate AND court_t.court_no = :benchid 
order by civil_t.dt_regis desc) 
UNION
(select substring(civil_t_a.case_no,12,4) as case_year, LTRIM(substring(civil_t_a.case_no,5,7),'0') as case_number, to_char(civil_t_a.dt_regis,'dd-mm-yyyy') as dt,
civil_t_a.pet_name, civil_t_a.res_name,court_t.court_id as c_no, court_t.bench_desc as b_desc, court_t.calhc_jocode as jocode, 'Y' from_civil_t_a 
from civil_t_a left outer join court_t on civil_t_a.court_no = court_t.court_no 
where LTRIM(substring(civil_t_a.case_no,2,3),'0')=:casetype AND civil_t_a.dt_regis BETWEEN :newFromDate AND :newToDate AND court_t.court_no = :benchid 
order by civil_t_a.dt_regis desc)) AS count";

$stmt=$conn->prepare($sql_count);	
$stmt->bindParam(':casetype', $casetype);
$stmt->bindParam(':newFromDate', $newFromDate);
$stmt->bindParam(':newToDate', $newToDate);
$stmt->bindParam(':benchid', $benchid);
$result=$stmt->execute();
$totalData = $stmt->fetchAll(PDO::FETCH_ASSOC);
// For getting the total no. of data which is in this case equal to the total no. of filtered data as here is no search option in the dataTable
$totalFiltered = $totalData['0']['count'];
/* For getting the total no. of data :: ENDS */

$record = array();
  foreach($rec as $data){
    $report['Sl No'] = $start + 1;
	$report['Case No'] = $typename."/".$data['case_number']."/".$data['case_year'];
	$report['Date of Registration'] = $data['dt'];
	$report['Cause Title'] = $data['pet_name']."  VS  ".$data['res_name'];
	if($data['c_no']==0){$report['Court No'] = "";} else{$report['Court No'] = $data['c_no'];}
	$report['Bench Description'] = $data['b_desc']." (".$benchid.")";
	$report['JO Code'] = $data['jocode'];
	if($data['from_civil_t_a']=='Y'){$report['status']='<font style=\'color:green;\'>DISPOSED</font>';} else{$report['status']='PENDING';}
	$record[] = $report;
	$start++;
	}
} //if ends for benchid HAS value








/*if starts for bench id has NO value*/
if($benchid == ""){
$sql="(select substring(case_no,12,4) as case_year,LTRIM(substring(case_no,5,7),'0') as case_number, to_char(civil_t.dt_regis,'dd-mm-yyyy') as dt,
pet_name, res_name, court_t.court_id as c_no, court_t.bench_desc as b_desc, court_t.calhc_jocode as jocode, 'N' from_civil_t_a, court_t.court_no as bid 
from civil_t left outer join court_t on civil_t.court_no = court_t.court_no 
where LTRIM(substring(case_no,2,3),'0')=:casetype AND dt_regis BETWEEN :newFromDate AND :newToDate order by civil_t.dt_regis desc) 

UNION

(select substring(civil_t_a.case_no,12,4) as case_year, LTRIM(substring(civil_t_a.case_no,5,7),'0') as case_number, to_char(civil_t_a.dt_regis,'dd-mm-yyyy') as dt,
civil_t_a.pet_name, civil_t_a.res_name,court_t.court_id as c_no, court_t.bench_desc as b_desc, court_t.calhc_jocode as jocode, 'Y' from_civil_t_a, court_t.court_no as bid 
from civil_t_a left outer join court_t on civil_t_a.court_no = court_t.court_no 
where LTRIM(substring(civil_t_a.case_no,2,3),'0')=:casetype AND civil_t_a.dt_regis BETWEEN :newFromDate AND :newToDate order by civil_t_a.dt_regis desc)".$limit_data;
 
 $stmt=$conn->prepare($sql);	
 $stmt->bindParam(':casetype', $casetype);
 $stmt->bindParam(':newFromDate', $newFromDate);
 $stmt->bindParam(':newToDate', $newToDate);
 $result=$stmt->execute();	
/* data fetched from main query */
 $rec=$stmt->fetchAll(PDO::FETCH_ASSOC);
 
 /* query for total no. of data - STARTS */
$sql_count = "SELECT COUNT(*) FROM ((select substring(case_no,12,4) as case_year,LTRIM(substring(case_no,5,7),'0') as case_number, to_char(civil_t.dt_regis,'dd-mm-yyyy') as dt,
pet_name, res_name, court_t.court_id as c_no, court_t.bench_desc as b_desc, court_t.calhc_jocode as jocode, 'N' from_civil_t_a, court_t.court_no as bid 
from civil_t left outer join court_t on civil_t.court_no = court_t.court_no 
where LTRIM(substring(case_no,2,3),'0')=:casetype AND dt_regis BETWEEN :newFromDate AND :newToDate order by civil_t.dt_regis desc) 
UNION
(select substring(civil_t_a.case_no,12,4) as case_year, LTRIM(substring(civil_t_a.case_no,5,7),'0') as case_number, to_char(civil_t_a.dt_regis,'dd-mm-yyyy') as dt,
civil_t_a.pet_name, civil_t_a.res_name,court_t.court_id as c_no, court_t.bench_desc as b_desc, court_t.calhc_jocode as jocode, 'Y' from_civil_t_a, court_t.court_no as bid 
from civil_t_a left outer join court_t on civil_t_a.court_no = court_t.court_no 
where LTRIM(substring(civil_t_a.case_no,2,3),'0')=:casetype AND civil_t_a.dt_regis BETWEEN :newFromDate AND :newToDate order by civil_t_a.dt_regis desc)) AS count";

$stmt=$conn->prepare($sql_count);	
$stmt->bindParam(':casetype', $casetype);
$stmt->bindParam(':newFromDate', $newFromDate);
$stmt->bindParam(':newToDate', $newToDate);
$result=$stmt->execute();
$totalData = $stmt->fetchAll(PDO::FETCH_ASSOC);
// For getting the total no. of data which is in this case equal to the total no. of filtered data as here is no search option in the dataTable
$totalFiltered = $totalData['0']['count'];
/* For getting the total no. of data :: ENDS */


 

$record = array();
  foreach($rec as $data){
    $report['Sl No'] = $start + 1;
	$report['Case No'] = $typename."/".$data['case_number']."/".$data['case_year'];
	$report['Date of Registration'] = $data['dt'];
	$report['Cause Title'] = $data['pet_name']."  VS  ".$data['res_name'];
	if($data['c_no']==0){$report['Court No'] = "";} else{$report['Court No'] = $data['c_no'];}
	if($data['b_desc']==""){$report['Bench Description']="";}else{$report['Bench Description'] = $data['b_desc']." (".$data['bid'].")";}
	$report['JO Code'] = $data['jocode'];
	if($data['from_civil_t_a']=='Y'){$report['status']='<font style=\'color:green;\'>DISPOSED</font>';} else{$report['status']='PENDING';}
	$record[] = $report;
	$start++;
}
}//if ends for bench id has NO value
 
 
 
$json_data = array(
    "draw" => intval($_POST['draw']),
    "recordsTotal" => intval($totalData['0']['count']),
    "recordsFiltered" =>intval($totalFiltered),
    "data" => $record
);
echo json_encode($json_data);
?>