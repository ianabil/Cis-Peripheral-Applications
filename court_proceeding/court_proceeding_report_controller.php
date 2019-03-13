<?php
include('../connection.php');

$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

// For dataTable :: STARTS
$columns = array( 
    0 =>'Status',
    1 =>'Case No',
    2=> 'Next Date of Hearing',
    3=> 'Purpose of Listing',
    4=> 'Sub Purpose of Listing',
    5=> 'Business',
    6=> 'Disposal Nature'
);

$bench = $_POST['bench']; // bench id
$from_date = date("Y-m-d", strtotime($_POST['from_date'])); // From Date


$limit = $_POST['length']; // For No. of rows per page
$start = $_POST['start']; // For Offset

if($limit==-1)
    $limit_data ='';
else
    $limit_data =' LIMIT '.$limit. ' OFFSET '.$start;

// Base Query
$sql="select cause_list.case_no, cause_list.final_status, cause_list.ia_no, daily_proc.order_remark, daily_proc.next_date, purpose_t.*, subpurpose_t.*, disp_type_t.* from cause_list LEFT OUTER JOIN daily_proc ON cause_list.case_no = daily_proc.case_no AND cause_list.causelist_date = daily_proc.todays_date LEFT OUTER JOIN purpose_t ON daily_proc.purpose_code = purpose_t.purpose_code LEFT OUTER JOIN subpurpose_t ON daily_proc.subpurpose_id = subpurpose_t.subpurpose_id LEFT OUTER JOIN civil_t_a ON cause_list.case_no = civil_t_a.case_no LEFT OUTER JOIN disp_type_t ON civil_t_a.disp_nature = disp_type_t.disp_type where cause_list.for_bench_id = ".$bench." AND cause_list.causelist_date = '".$from_date."' ".$limit_data;
//echo $sql;exit;
$stmt=$conn->prepare($sql);	
$result=$stmt->execute();	

// Base Data Fetched
$rec=$stmt->fetchAll(PDO::FETCH_BOTH);

/* For getting the total no. of data :: STARTS */
$sql_count = "select count(*) from cause_list LEFT OUTER JOIN daily_proc ON cause_list.case_no = daily_proc.case_no AND cause_list.causelist_date = daily_proc.todays_date LEFT OUTER JOIN purpose_t ON daily_proc.purpose_code = purpose_t.purpose_code LEFT OUTER JOIN subpurpose_t ON daily_proc.subpurpose_id = subpurpose_t.subpurpose_id where cause_list.for_bench_id = ".$bench." AND cause_list.causelist_date = '".$from_date."' ";

$stmt=$conn->prepare($sql_count);	
$result=$stmt->execute();
$totalData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// For getting the total no. of data which is in this case equal to the total no. of filtered data as here is no search option in the dataTable
$totalFiltered = $totalData['0']['count'];

/* For getting the total no. of data :: ENDS */

$record = array();


foreach($rec as $data)
{
    $report['Disposal Nature'] = ''; // Initialized with blank
    $report['Next Date of Hearing'] = ''; // Initialized with blank
    $report['Purpose of Listing'] = ''; // Initialized with blank
    $report['Sub Purpose of Listing'] = ''; // Initialized with blank
    $report['Business'] = ''; // Initialized with blank

    

    /* For Case Number :: STARTS */
    if($data['ia_no']=="")
    {
        if(trim($data['final_status'])=='P')
            $report['Status']='<span class="badge badge-success" style="background-color:#468847">DONE</span>'; // Proceeding 
        else if(trim($data['final_status'])=='D'){
            $report['Status']='<span class="badge badge-success" style="background-color:#2E4053">DISPOSED</span>'; // Disposed Status 
            $report['Disposal Nature'] = $data['disp_name'];
        }
        else
            $report['Status']='<span class="badge badge-primary" style="background-color:#FF4500">PENDING</span>'; // Not Proceed

        $case_type_code = ltrim(substr($data['case_no'],1,3),'0');            
        $case_no = ltrim(substr($data['case_no'],4,7),'0');
        $case_year = substr($data['case_no'],11,4);

        $sql1 = "SELECT * FROM case_type_t WHERE case_type=".$case_type_code;
            
        $stmt=$conn->prepare($sql1);	
        $result=$stmt->execute();
        $rec1=$stmt->fetchAll(PDO::FETCH_ASSOC);

        $report['Case No'] = $rec1['0']['type_name'] . '<b>/</b>'. $case_no.'<b>/</b>'.$case_year; // Case No

         /* Next Hearing Date :: STARTS*/
        $next_date = date("d-m-Y", strtotime($data['next_date']));
        if($next_date!='01-01-1970') // NULL handling
            $report['Next Date of Hearing'] = date("d-m-Y", strtotime($data['next_date'])); 
        else
            $report['Next Date of Hearing'] = '';
        /* Next Hearing Date :: ENDS*/

        $report['Purpose of Listing'] = $data['purpose_name']; // Next Purpose of Listing

        $report['Sub Purpose of Listing'] = $data['sub_purpose']; // Next Sub Purpose of Listing

        $report['Business'] = $data['order_remark']; // Business
    }
    else{
        $case_type_code = ltrim(substr($data['case_no'],1,3),'0');            
        $case_no = ltrim(substr($data['case_no'],4,7),'0');
        $case_year = substr($data['case_no'],11,4);

        $sql1 = "SELECT * FROM case_type_t WHERE case_type=".$case_type_code;
            
        $stmt=$conn->prepare($sql1);	
        $result=$stmt->execute();
        $rec1=$stmt->fetchAll(PDO::FETCH_ASSOC);

        $sql2 = "select * from ia_filing INNER JOIN ia_case_type_t ON ia_filing.ia_case_type = ia_case_type_t.ia_case_type where ia_filing.ia_no = '".$data['ia_no']."' and ia_filing.case_no = '".$data['case_no']."'
                UNION 
                select * from ia_filing_a INNER JOIN ia_case_type_t ON ia_filing_a.ia_case_type = ia_case_type_t.ia_case_type where ia_filing_a.ia_no = '".$data['ia_no']."' and ia_filing_a.case_no = '".$data['case_no']."'";
        
        $stmt=$conn->prepare($sql2);	
        $result=$stmt->execute();
        $rec2=$stmt->fetchAll(PDO::FETCH_ASSOC);

        $report['Case No'] = $rec2['0']['ia_type_name']. "<b>/</b>".$rec2['0']['ia_regno']. "<b>/</b>".$rec2['0']['ia_regyear']."<br>\n(Old Case No: ".$rec2['0']['old_ia_no'].") <br>\nIn Main Case ". $rec1['0']['type_name'] . "<b>/</b>". $case_no."<b>/</b>".$case_year;
        
        

        if(trim($data['final_status'])=='P'){
            $report['Status']='<span class="badge badge-success" style="background-color:#468847">DONE</span>'; // Proceeding 
            $report['Next Date of Hearing'] = date("d-m-Y", strtotime($rec2['0']['date_of_hearing']));
        }
        else if(trim($data['final_status'])=='D'){
            $report['Status']='<span class="badge badge-success" style="background-color:#2E4053">DISPOSED</span>'; // Disposed Status 

            $sql3 = "select * FROM disp_type_t WHERE disp_type = ".$rec2['0']['disp_nature'];
            $stmt=$conn->prepare($sql3);	
            $result=$stmt->execute();
            $rec3=$stmt->fetchAll(PDO::FETCH_ASSOC);

            $report['Disposal Nature'] = $rec3['0']['disp_name'];
        }
        else
            $report['Status']='<span class="badge badge-primary" style="background-color:#FF4500">PENDING</span>'; // Not Proceed
    }
    /* For Case Number :: ENDS */

   

    $record[] = $report; // Datatable array

}



$json_data = array(
    "draw" => intval($_POST['draw']),
    "recordsTotal" => intval($totalData['0']['count']),
    "recordsFiltered" =>intval($totalFiltered),
    "data" => $record
);

echo json_encode($json_data);

?>