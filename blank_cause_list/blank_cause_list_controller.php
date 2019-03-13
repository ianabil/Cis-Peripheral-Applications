<?php
 if(session_id() == '') {
    session_start();
}


if($_SESSION['username']=='')
    header('location: ../login.php');

header('Content-Type: application/json');

include('../connection.php');

include_once('../../swecourtishc/MPDF/mpdf.php');

$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

// will create both draft & final causelist depending on the parameters
function prepare_causelist($conn, $bench_no, $causelist_id, $causelist_dt, $time, $header, $footer, $roaster_desc, $court_id, $tknflg, $publish_status=NULL,$published_date=NULL, $filename=NULL){
    
    $sql = "delete from causelist_title where bench_id=:bench_id and causelist_id=:causelist_id and causelist_date=:causelist_date and published is null";
    $stmt=$conn->prepare($sql);
    $stmt->bindParam(':bench_id', $bench_no);   
    $stmt->bindParam(':causelist_date', $causelist_dt);   
    $stmt->bindParam(':causelist_id', $causelist_id);	
    $result=$stmt->execute();


    $sql = "insert into causelist_title (bench_id, cheader, cfooter, causelist_id, 
    causelist_date, published, published_date, filename, for_bench_id, cause_list_status, causelist_sr_no, 
    causelist_time, court_id, roaster_desc, cases_count) 
    values (:bench_no,:header,:footer,:causelist_id,:causelist_date,:published, :published_date, :filename,
    :for_bench_id,'P',0,:causelist_time,
    :court_id,:roaster_desc,0)";
	
	$stmt=$conn->prepare($sql);
	
	$stmt->bindParam(':bench_no', $bench_no, PDO::PARAM_INT);
	$stmt->bindParam(':header', $header, PDO::PARAM_STR);
	$stmt->bindParam(':footer', $footer, PDO::PARAM_STR);
	$stmt->bindParam(':causelist_id', $causelist_id, PDO::PARAM_INT);
	$stmt->bindParam(':causelist_date', $causelist_dt, PDO::PARAM_STR);
	$stmt->bindParam(':published', $publish_status, PDO::PARAM_NULL|PDO::PARAM_STR);
	$stmt->bindParam(':published_date', $published_date, PDO::PARAM_NULL|PDO::PARAM_STR);
	$stmt->bindParam(':filename', $filename, PDO::PARAM_NULL|PDO::PARAM_STR);
	$stmt->bindParam(':for_bench_id', $bench_no, PDO::PARAM_INT);
	$stmt->bindParam(':causelist_time', $time, PDO::PARAM_STR);
	$stmt->bindParam(':court_id', $court_id, PDO::PARAM_STR);
	$stmt->bindParam(':roaster_desc', $roaster_desc, PDO::PARAM_STR);
	$result=$stmt->execute();
	
	$mpdf=new mPDF('','','10','timesnewroman');
	
    $mpdf->setAutoTopMargin = 'stretch';
    $mpdf->setAutoBottomMargin = 'pad';
    $mpdf->autoMarginPadding = 5;
    $mpdf->useAdobeCJK = true;

    $query="select a.judge_code,a.judge_name as judge_name,a.ljudge_name from Judge_name_t as a,judge_t as b 
 			where a.display='Y' and a.judge_code=b.judge_code and b.court_no=:fcourt_no and b.from_dt IS NOT NULL 
             and b.to_dt IS NULL order by b.judge_priority";             

    $stmt=$conn->prepare($query);
    $stmt->bindParam(':fcourt_no', $bench_no);     	
    $result=$stmt->execute();
    $record_bench = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $joinedjudges='';
    foreach($record_bench as $bench)
	{
        $judge_name=$bench['judge_name'];
	 	$joinedjudges.='<b>'.strtoupper($judge_name).'</b><br>';
    }

    $query="select * from cause_list_period where cause_list_type_id = :causelist_id";             

    $stmt=$conn->prepare($query);
    $stmt->bindParam(':causelist_id', $causelist_id);         	
    $result=$stmt->execute();
    $causelist_type = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $cause_list_type = strtoupper($causelist_type['0']['cause_list_type']);

    $day_name = date('l', strtotime($causelist_dt));
    $day = ltrim(date('d', strtotime($causelist_dt)),0);
    $suffix = date('S', strtotime($causelist_dt));
    $month_text = date('F', strtotime($causelist_dt));
    $year_digit = date('Y', strtotime($causelist_dt));


    $fetch_cases="select roaster_desc,room_no,to_char(causelist_time::time,'HH12:MI AM') as causelist_time 
				  ,court_id,c_order, cheader, cfooter from causelist_title where  causelist_date=:from_date and causelist_id=:fcaselistid and 
				  for_bench_id=:for_bench_id and cause_list_status!='E'";
    
    $stmt=$conn->prepare($fetch_cases);
    $stmt->bindParam(':from_date', $causelist_dt);   
    $stmt->bindParam(':fcaselistid', $causelist_id);   
    $stmt->bindParam(':for_bench_id', $bench_no);   
    $result=$stmt->execute();
    $fetch_row= $stmt->fetchAll(PDO::FETCH_ASSOC);

    $roaster_desc=$fetch_row['0']['roaster_desc'];
    $room_no=$fetch_row['0']['room_no'];
    $causelist_time=ltrim($fetch_row['0']['causelist_time'],'0');
    $court_id=$fetch_row['0']['court_id'];
    $header=$fetch_row['0']['cheader'];
    $footer=$fetch_row['0']['cfooter'];


    $fetch_case="select bench_type_name from bench_type where bench_type_code in (select bench_type_code from court_t where court_no=:courtno)";
   
    $stmt=$conn->prepare($fetch_case);
    $stmt->bindParam(':courtno', $bench_no); 
    $result=$stmt->execute();

    $fetch_row= $stmt->fetchAll(PDO::FETCH_ASSOC);
    $bench_type_name=strtoupper($fetch_row['0']['bench_type_name']);
	
	$filename_date_prefix=date('dmY',strtotime($causelist_dt));
	
    if($tknflg!='Y')
        $uniquesession =$filename_date_prefix."_".$bench_no."_".$causelist_id."_d.pdf";
    else
        $uniquesession =$filename_date_prefix."_".$bench_no."_".$causelist_id.".pdf";

    $uniquesession1 =$filename_date_prefix."_".$bench_no."_".$causelist_id.".pdf";

	$frmdt_arr = explode('-', date('d-m-Y', strtotime($causelist_dt)));
	$report_dir = $frmdt_arr[2];
    $db_name = $_SESSION['dbname']; 

    if($tknflg!='Y')
	    $path_dir = "../../$db_name/causelist_report_draft/$report_dir";
	else
        $path_dir = "../../$db_name/causelist_report/$report_dir";
            
    if (!is_dir($path_dir)) 
    {
        mkdir($path_dir, 0777, true);       
    }
    
    if(file_exists("$path_dir/$uniquesession"))
    {
        unlink("$path_dir/$uniquesession");
    }
    $data_file = "$path_dir/$uniquesession";


    
    $line99="<table width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" align=\"center\" border=\"0\">
		<tr><td colspan='5' align=center><img src='../images/calhc_logo.jpg'></td></tr>
		<tr><td colspan='5' align=center><b><h1>In The High Court at Calcutta</h1></b></td></tr>
		<tr><td colspan='5' align=center><b><h2>".$db_name."</h2></b></td></tr>
		<tr>
		  <td colspan='5' align=center>
			<b><u>$cause_list_type CAUSELIST</u> <br>For $day_name The $day<sup>$suffix</sup> $month_text $year_digit<br></b>
		  </td>
		</tr>
		<tr><td colspan='5' align=center><b>COURT NO. $court_id</b></td></tr>
		<tr><td colspan='5' align=center><b>$bench_type_name</b></td></tr>
		<tr><td colspan='5' align=center><b>AT $causelist_time</b></td></tr>
		<tr><td colspan='5' align=center> <b>".nl2br($header)."</b> </td></tr>
		<tr><td colspan='5' align=center><b>$joinedjudges</b></td></tr>
        <tr><td colspan='5'><hr></td></tr>"; 
        
        $mpdf->WriteHTML($line99);
		   
        
        $line8=("<tbody> ");
        $mpdf->WriteHTML($line8);
        
        $footer = str_replace(' ',"&nbsp;",$footer);
        $line18="<tr><td colspan=5><b>".nl2br($footer)."</b></br></td></tr>";
        
        $line18.=("</tbody></table>");
        $mpdf->WriteHTML($line18);

        if($tknflg!='Y')
        {
            $mpdf->SetWatermarkText('DRAFT');
            $mpdf->showWatermarkText = true;
        }

        $mpdf->Output($data_file);
        $mpdf->close();

        $data['path'] = $data_file;
        $data['file'] = $uniquesession1;
        echo json_encode($data);
}



// Following code will validate the causelist generation request
$type_of_job = $_POST['type_of_job'];

if($type_of_job=='validation')
{

    $bench_no = $_POST['bench_no'];
    $causelist_id = $_POST['causelist_id'];
    $causelist_dt = Date('Y-m-d', strtotime($_POST['causelist_dt']));

    $sql1 = "SELECT * FROM causelist_title where bench_id=".$bench_no." 
            AND causelist_id=".$causelist_id." AND causelist_date='".$causelist_dt."'";

    $stmt=$conn->prepare($sql1);	
    $result=$stmt->execute();
    $record = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $count = sizeof($record);

    // checking whether cases are posted in this specific causelist
    if($count>0){ 
        if($record['0']['published']=='Y'){
            $arr = array('error' => "Causelist Already Published");
            echo json_encode($arr);
            http_response_code(422);
            exit;
        }
        else if($record['0']['cases_count']==0){
            $arr = array('success' => 'Causelist Already Prepared', 'data'=>$record);
            echo json_encode($arr); // Draft already saved, send the existing value into the view
            exit;
        }
        else if($record['0']['cases_count']>0){
            $arr = array('error' => "Causelist Already Prepared With Some Cases");
            echo json_encode($arr);
            http_response_code(422);
            exit;
        }
    }
    
    else{
		$arr = array('success' => "All Clear");
        echo json_encode($arr); //causelist_title has no entry, elligible for blank causelist        
        exit;
    }
}
else if($type_of_job=='draft_creation')
{
    $bench_no = $_POST['bench_no'];
    $causelist_id = $_POST['causelist_id'];
    $causelist_dt = Date('Y-m-d', strtotime($_POST['causelist_dt']));
    $court_id = $_POST['court_id'];
    $time = $_POST['time'];
    $header = $_POST['header'];
    $footer = $_POST['footer'];
    $roaster_desc = $_POST['roaster_desc'];

     // Draft causelist preparation
    prepare_causelist($conn, $bench_no, $causelist_id, $causelist_dt, $time, $header, $footer, $roaster_desc, trim($court_id), 'N');
       
}
else if($type_of_job=='final_publish'){

    $bench_no = $_POST['bench_no'];
    $causelist_id = $_POST['causelist_id'];
    $causelist_dt = Date('Y-m-d', strtotime($_POST['causelist_dt']));
    $court_id = $_POST['court_id'];
    $time = $_POST['time'];
    $header = $_POST['header'];
    $footer = $_POST['footer'];
    $roaster_desc = $_POST['roaster_desc'];
    $publish_status = 'Y';
    $published_date = date('Y-m-d');
    $filename = $_POST['filename'];

    // Final causelist preparation
    prepare_causelist($conn, $bench_no, $causelist_id, $causelist_dt, $time, $header, $footer, $roaster_desc, trim($court_id), 'Y', $publish_status, $published_date, $filename); 

}


?>