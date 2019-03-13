<?php
include('../connection.php');

$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

// For dataTable :: STARTS
$columns = array( 
    0 =>'Sl No',
    1=> 'Bench No',
    2=> 'Room No',
    3=> 'Court No',
    4=> 'Bench Type',
    5=> 'Bench Description',
    6=> 'Joined Judge',
    7=> 'From Date'
);


$from_date = date("Y-m-d", strtotime($_POST['from_date'])); // From Date


$limit = $_POST['length']; // For No. of rows per page
$start = $_POST['start']; // For Offset

if($limit==-1)
    $limit_data ='';
else
    $limit_data =' LIMIT '.$limit. ' OFFSET '.$start;

// Base Query
$sql="select * from court_t INNER JOIN bench_type ON court_t.bench_type_code = bench_type.bench_type_code INNER JOIN judge_t ON court_t.court_no = judge_t.court_no where court_t.bench_section = 'B' AND court_t.cfrom_dt <='".$from_date."' AND (court_t.cto_dt>='".$from_date."' OR court_t.cto_dt IS NULL) ORDER BY judge_t.judge_priority DESC".$limit_data;

$stmt=$conn->prepare($sql);	
$result=$stmt->execute();	

// Base Data Fetched
$rec=$stmt->fetchAll(PDO::FETCH_ASSOC);

/* For getting the total no. of data :: STARTS */
$sql_count = "select count(*) from court_t where bench_section = 'B' AND cfrom_dt <='".$from_date."' AND (cto_dt>='".$from_date."' OR cto_dt IS NULL)";

$stmt=$conn->prepare($sql_count);	
$result=$stmt->execute();
$totalData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// For getting the total no. of data which is in this case equal to the total no. of filtered data as here is no search option in the dataTable
$totalFiltered = $totalData['0']['count'];

/* For getting the total no. of data :: ENDS */

$record = array();


foreach($rec as $data)
{

    $report['Sl No']=$start+1; // Serial No.

    $report['Bench No']=$data['court_no']; // Bench No.

    $report['Room No'] = trim($data['room_no']); // Room No.

    $report['Court No'] = trim($data['court_id']); // Court No.

    $report['Bench Type'] = trim($data['bench_type_name']); // Bench Type

    $report['Bench Description'] = trim($data['bench_desc']); // Bench Description
    
    /* Joined Judge :: STARTS*/
    $report['Joined Judge'] ="";
    
    $sql2 = "SELECT * FROM judge_t INNER JOIN judge_name_t ON judge_t.judge_code = judge_name_t.judge_code WHERE judge_t.court_no = ".$data['court_no']." ORDER BY judge_t.judge_priority DESC";
    $stmt=$conn->prepare($sql2);	
    $result=$stmt->execute();	
    $judges=$stmt->fetchAll(PDO::FETCH_ASSOC); 

    foreach($judges as $judge){  
        if($report['Joined Judge']!="")
            $report['Joined Judge'] = $report['Joined Judge']."  AND <br>\n".trim($judge['judge_name']);    
        else
            $report['Joined Judge'] = trim($judge['judge_name']);
    }
    
    /* Joined Judge :: ENDS*/

    $report['From Date'] = date("d-m-Y", strtotime($data['cfrom_dt'])); // From Date
    
    $record[] = $report; // Datatable array
    $start++; // Serial No. increment

}



$json_data = array(
    "draw" => intval($_POST['draw']),
    "recordsTotal" => intval($totalData['0']['count']),
    "recordsFiltered" =>intval($totalFiltered),
    "data" => $record
);

echo json_encode($json_data);

?>