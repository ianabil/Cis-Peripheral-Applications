<!DOCTYPE html>
<html lang="en">

<head>

    <?php 
        if(session_id() == '') {
            session_start();
        }
      

        if($_SESSION['username']=='')
            header('location: ../login.php');

        $flag = 0;
        foreach($_SESSION['role_type'] as $role){
            if($role=='Supuser' || $role=='Judicial' || $role=='Admin')
                $flag = 1;
        }

        if($flag==0){
            echo "<script>
                    alert('Unauthorized Access');
                    setTimeout(function(){
                        window.location.href='../index.php';
                    },1);
                </script>";
        }

    
    ?>
    <title>Blank Cause List</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap-datepicker.css">
    <link rel="stylesheet" type="text/css" href="../css/dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="../css/buttons.dataTables.min.css">
    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/dataTables.min.js"></script>
    <script src="../js/bootstrap-datepicker.js"></script>
    <script src="../js/dataTables.buttons.min.js"></script>
    <script src="../js/buttons.flash.min.js"></script>
    <script src="../js/jszip.min.js"></script>
    <script src="../js/pdfmake.min.js"></script>
    <script src="../js/vfs_fonts.js"></script>
    <script src="../js/buttons.html5.min.js"></script>
    <script src="../js/buttons.print.min.js"></script>
    <script src="../js/buttons.colVis.min.js"></script>
    <script src="../js/sweetalert.min.js"></script>
</head>

<body style="font-family:'Times New Roman', Times, serif;">

<?php
include('../connection.php');
$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

$sql="select * from court_t where court_t.bench_section = 'B' AND (court_t.cto_dt>= current_date OR court_t.cto_dt IS NULL) ORDER BY court_t.court_no";

$stmt=$conn->prepare($sql);	
$result=$stmt->execute();	

// Case Type Data Fetched
$bench=$stmt->fetchAll(PDO::FETCH_ASSOC);

$sql="select cause_list_type_id, cause_list_type from cause_list_period WHERE showindailyproc='Y' AND display='Y' ORDER BY cause_list_type_id";

$stmt=$conn->prepare($sql);	
$result=$stmt->execute();	

// Cause List Type Data Fetched
$causelist_type=$stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<img src="../images/home.png" onClick="location.href='../index.php';" style="float:right;height:50px;;width:50px;cursor: pointer;">
    <div class="jumbotron text-center" style="background-color:#6CA6CD;color:#ffffff;">
    
        <h2>Preparation Of Blank Cause List</h2>
        <br>
        <div class="row">
            <div class="col-md-offset-1 col-sm-3">
                <select class="form-control disable" name="bench" id="bench">
                    <option value="">Select Bench</option>
                    <?php foreach ($bench as $bench) { ?>
                        <option value="<?php echo $bench['court_no'];?>" data-roaster_desc_court_id="<?php echo $bench['roaster_desc'];?>,<?php echo $bench['court_id'];?>"><?php echo $bench['court_no'];?> - <?php echo $bench['bench_desc'];?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-sm-3">
                <select class="form-control disable" name="causelist_type" id="causelist_type">
                    <option value="">Select Causelist Type</option>
                    <?php foreach ($causelist_type as $type) { ?>
                        <option value="<?php echo $type['cause_list_type_id'];?>"><?php echo $type['cause_list_type_id'];?> - <?php echo $type['cause_list_type'];?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-sm-2">
                <input type="text" class="form-control date disable" name="date1" placeholder="Causelist Date" id="date1" autocomplete="off">
            </div>
            
                <div class="col-sm-2" id="hidden_time" style="display:none">
                    <input type="text" class="form-control time" name="time" placeholder="Causelist Time (HH:MM)" id="time" autocomplete="off">
                </div>
      
            <div class="col-sm-1">
                <button type="button" class="btn btn-success" id="go" name="go">GO</button>
            </div>
        </div>

        <br>
        
       
            <div class="row" id="hidden_row" style="display:none">
                <div class="col-md-offset-1 col-sm-3">
                        <textarea class="form-control header" name="header" id="header" placeholder="Insert Header"></textarea>
                </div>
                <div class="col-sm-3">
                        <textarea class="form-control footer" name="footer" id="footer" placeholder="Insert Footer"></textarea>
                </div>
                <div class="col-sm-2">
                    <input type="text" class="form-control roaster_desc" placeholder="Roaster Description" name="roaster_desc" id="roaster_desc" autocomplete="off">
                </div>
                <div class="col-sm-1" id="div_prepare">
                    <button type="button" class="btn btn-primary" id="prepare" name="prepare">PREPARE</button>
                </div>
                <div class="col-sm-1" id="div_publish">
                    <button type="button" class="btn btn-success" id="publish" name="publish">PUBLISH</button>
                </div>
                <div class="col-sm-1"  id="div_reset">
                    <button type="button" class="btn btn-danger" id="reset" name="reset">RESET</button>
                </div>
            </div>
        
    </div>

    <div class="col-sm-12 text-center" id="show_draft_causelist" style="display:none">
        <iframe id="iframe_causelist" src="" style="width:800px; height:400px;"></iframe>
    </div>

    <!--loader starts-->
    <div class="row">
        <div class="col-md-5"></div>
        <div class="col-md-3" id="wait" style="display:none;">
            <img src='../images/ajax-loader.gif'/>
        </div>
    </div>

    <!--loader ends-->



    <script>
    $(document).ready(function() {
          $(".date").datepicker(
              {format: 'dd-mm-yyyy', 
                orientation: 'auto'
              }
          );

        /*LOADER*/
        $(document).ajaxStart(function() {
            $("#wait").css("display", "block");
        });
        
        $(document).ajaxComplete(function() {
            $("#wait").css("display", "none");
        });
        /*LOADER*/

        $(document).on("change","#bench", function(){
            var data = $(this).find(':selected').data('roaster_desc_court_id');
            var roaster = data.substr(0,data.indexOf(",")-1);
            $("#roaster_desc").val(roaster);
        })


        var bench_no;
        var selectbox_data;
        var court_id;
        var causelist_id;
        var causelist_dt;
        var time;
        var header;
        var footer;
        var roaster_desc;
        var filename;
        var filename_with_path;

        $(document).on("click","#go", function() {
            bench_no = $("#bench option:selected").val();            
            causelist_id = $("#causelist_type option:selected").val();
            causelist_dt = $("#date1").val();

            if(bench_no=="" || causelist_id=="" || causelist_dt==""){
                swal("Fill All The Fields","","error");
                //return false;
            }
            else{
                selectbox_data = $("#bench").find(':selected').data('roaster_desc_court_id');
                court_id = selectbox_data.substr(selectbox_data.indexOf(",")+1);

                $.ajax({
                    url: "blank_cause_list_controller.php",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        bench_no:bench_no,
                        court_id:court_id,
                        causelist_id:causelist_id,
                        causelist_dt:causelist_dt,
                        type_of_job:"validation"                        
                    },
                    success:function( data, textStatus, jqXHR ){
						if(data.success=='All Clear'){ // No draft saved
                            $(".disable").attr('disabled',true);
                            $("#hidden_time").show();
                            $("#hidden_row").show();
                            $("#go").hide();  
                            $("#div_publish").hide(); 
                        }
                        else{ // Draft already saved
                        console.log(jqXHR);
                            $(".disable").attr('disabled',true);
                            $("#hidden_time").show();
                            $("#hidden_row").show();
                            $("#go").hide(); 
                            $("#div_publish").hide(); 
                            
                            $("#time").val(jqXHR.responseJSON.data['0'].causelist_time);
                            $("#header").val(jqXHR.responseJSON.data['0'].cheader);
                            $("#footer").val(jqXHR.responseJSON.data['0'].cfooter);

                            if(jqXHR.responseJSON.data['0'].roaster_desc!='')
                                $("#roaster_desc").val(jqXHR.responseJSON.data['0'].roaster_desc)
                        }                  
                    },
                    error:function(jqXHR, textStatus, errorThrown){ // Already published  or prepared with some cases                        
                        swal(jqXHR.responseJSON.error,"","error");
                    }
                });
            }


        });



        $(document).on("click","#prepare", function() {
            time = $("#time").val();
            header = $("#header").val();
            footer = $("#footer").val();
            roaster_desc = $("#roaster_desc").val();

            if(time==""){
                swal("Insert Causelist Time","","error");
                return false;
            }

            $.ajax({
                    url: "blank_cause_list_controller.php",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        bench_no:bench_no,
                        court_id:court_id,
                        causelist_id:causelist_id,
                        causelist_dt:causelist_dt,
                        time:time,
                        header:header,
                        footer:footer,
                        roaster_desc:roaster_desc,
                        type_of_job:"draft_creation"                        
                    },
                    success:function(response){
                        $("#div_prepare").hide(); 
                        $("#div_publish").show();                                             
                        filename = response.file;
                        filename_with_path = response.path;
                        $("#iframe_causelist").attr("src", filename_with_path);
                        $("#show_draft_causelist").show();   
                    }

            });

        });



        
        $(document).on("click","#publish", function() {
            
            $.ajax({
                    url: "blank_cause_list_controller.php",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        bench_no:bench_no,
                        court_id:court_id,
                        causelist_id:causelist_id,
                        causelist_dt:causelist_dt,
                        time:time,
                        header:header,
                        footer:footer,
                        roaster_desc:roaster_desc,
                        filename:filename,
                        type_of_job:"final_publish"                        
                    },
                    success:function(response){
                        $("#div_publish").hide();
                        swal("Blank Causelist Published","Print It From 'Causelist Report' Menu Of CIS","success");
                        setTimeout(function(){
                            location.reload();
                        },2500);
                    }

            });

        });



        $(document).on("click","#reset", function() {
                location.reload();
        });

       
    });
    </script>

</body>

</html>