
<?php
 if(session_id() == '') {
    session_start();
}


if($_SESSION['username']=='')
    header('location: ../login.php');

header('Content-Type: application/json');

// header('Content-Type: application/pdf');
include_once('../connection.php');


// Require composer autoload
include_once('../../swecourtishc/MPDF/mpdf.php');

$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

//merging of pdf s and global page no has been given

function importPDF($mpdf,$anotherPdfFileName)
{
    $pagecount = $mpdf->SetSourceFile($anotherPdfFileName);
    for ($i=1; $i<=$pagecount; $i++) {
        $mpdf->AddPage();
        $tplIdx = $mpdf->ImportPage($i);
        $mpdf->SetHTMLFooter ("
        <div class='numberCircle' style='clear: both; margin: 5pt; padding: -5pt; '>
            <table width='100%'>
                <tr>
                <td width='33%' align='right'>Page <span style='border-radius: 50%; 
                width: 36px;
                height: 36px;
                padding: 8px;
                background: #fff;
                border: 2px solid #00000;
                text-align: center;'> {PAGENO}</span> Out of {nbpg}</td>
                </tr>
            </table></div>");
        $mpdf->UseTemplate($tplIdx);
    }
    
}



// Fetching the data
$causelist_date = date("Y-m-d", strtotime($_POST['merge_date'])); 
$type_of_job=$_POST['type_of_job'];
$causelist_id=$_POST['causelist_id'];
$published='Y';

if($type_of_job=="validation")
{
    
    
    /* For getting the total data for the particular date:: STARTS */
    $sql1 = "select causelist_title.bench_id, causelist_title.filename, court_t.bench_desc from court_t  
    inner join causelist_title on court_t.court_no=causelist_title.bench_id where causelist_title.causelist_date='".$causelist_date."' and causelist_title.published='".$published."' and causelist_id=".$causelist_id;
    
    $stmt=$conn->prepare($sql1);
    $result=$stmt->execute();

    $totalData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if(sizeof($totalData)>0)
    {
               
        $arr = array('success' => "Marging on process",'data'=>$totalData);
        echo json_encode($arr);         
        exit;
    }
    else
    {
        $arr = array('error' => "Causelist can not be marged");
        echo json_encode($arr);
        http_response_code(422);
        exit;
    }
}
else if($type_of_job=="publish_causelist"){
        $filename = $_POST['filename'];
        $causelist_date = $_POST['merge_date'];

        $bench_no=007; 
        $causelist_id=1001;
        $filename_date_prefix=date('dmY',strtotime($causelist_date));

        if($causelist_id==1001)
            $uniquesession = "Merged_Causelist_Daily_".$filename_date_prefix.".pdf";
        else if($causelist_id==1002)
            $uniquesession = "Merged_Causelist_Monthly_".$filename_date_prefix.".pdf";
        else if($causelist_id==1003)
            $uniquesession = "Merged_Causelist_Supplementary_".$filename_date_prefix.".pdf";
        else if($causelist_id==1004)
            $uniquesession = "Merged_Causelist_Urgent_".$filename_date_prefix.".pdf";


        $mpdf=new mPDF('','','10','timesnewroman');

        $mpdf->setAutoTopMargin = 'stretch';
        $mpdf->setAutoBottomMargin = 'pad';
        $mpdf->autoMarginPadding = 5;
        $mpdf->useAdobeCJK = true;

        $mpdf->SetImportUse();

        $frmdt_arr = explode('-', date('d-m-Y', strtotime($causelist_date)));
        $report_dir = $frmdt_arr[2];

        $db_name = $_SESSION['dbname'];
        $path_dir = "../../$db_name/causelist_report/$report_dir";//source path

        
        if(file_exists("$path_dir/$uniquesession"))
        {
            unlink("$path_dir/$uniquesession");
        }

        for($i=0;$i<sizeof($filename);$i++){
            $final= $path_dir."/".$filename[$i];
            importPDF($mpdf,$final);
            
        }

        $data_file = "$path_dir/$uniquesession";
        $mpdf->Output($data_file);
        $mpdf->close();

        echo json_encode($data_file);;
}
?>


