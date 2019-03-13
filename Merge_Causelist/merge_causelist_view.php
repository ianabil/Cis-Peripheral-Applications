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
    <title>Merge Causelist</title>
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
    <script src="../js/buttons.print.min.js"></script><!DOCTYPE html>
    <script src="../js/buttons.print.min.js"></script><!DOCTYPE html>
    <script src="../js/sweetalert.min.js"></script>

    
</head>

<body style="font-family:'Times New Roman', Times, serif;">
<?php
include('../connection.php');
$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
$sql="select cause_list_type_id, cause_list_type from cause_list_period WHERE showindailyproc='Y' AND display='Y' ORDER BY cause_list_type_id";

$stmt=$conn->prepare($sql);	
$result=$stmt->execute();	

// Cause List Type Data Fetched
$causelist_type=$stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<img src="../images/home.png" onClick="location.href='../index.php';" style="float:right;height:50px;;width:50px;cursor: pointer;">
<div class="jumbotron text-center" style="background-color:#6CA6CD;color:#ffffff;">   
        <h3><strong>MERGE CAUSELIST</strong></h3>
        <br>
        <div class="row">
            <div class="col-sm-4"></div>
            <div class="col-sm-2">
                <select class="form-control disable" name="causelist_type" id="causelist_type">
                    <option value="">Select Causelist Type</option>
                    <?php foreach ($causelist_type as $type) { ?>
                        <option value="<?php echo $type['cause_list_type_id'];?>"><?php echo $type['cause_list_type_id'];?> - <?php echo $type['cause_list_type'];?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-sm-1">
                <input type="text" class="form-control date merge_date" style="width:100px;" name="merge_date" placeholder="Choose Date" id="merge_date" value="<?php echo date('d-m-Y') ?>" autocomplete="off">
            </div>
            <div class="col-sm-1" id="div_prepare">
                <button type="button" class="btn btn-primary prepare" id="prepare" >Prepare</button>
            </div>
            <div class="col-sm-1" style="display:none;" id="div_publish">
                <button type="button" class="btn btn-success publish" id="publish" >Publish</button>
            </div>
        </div>
    </div>

       <div class="container text-center" style="margin-top:10px; display:none;" id="result-section">
       
    </div>
    <div class="table-responsive col-sm-offset-3 col-sm-6" id="table_display" style="display:none;">
        <table class="table" id="data_table">
            <thead >
                <tr>
                    <th>Sl No.</th>
                    <th>Bench Name</th>
                    <th style='display:none;'>File Name</th>
                    <th>Priority</th>                
                </tr>
            </thead>
            <tbody id="tbody">
            </tbody>
        </table>
    </div>

    <div class="col-sm-12 text-center" id="show_merge_causelist" style="display:none">
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
                endDate: '+1D', 
                orientation: 'auto'}
          );

        /*LOADER*/
        $(document).ajaxStart(function() {
            $("#wait").css("display", "block");
        });
        
        $(document).ajaxComplete(function() {
            $("#wait").css("display", "none");
        });
        /*LOADER*/


        /*validation and priority setting*/

        var merge_date;
        var causelist_id;

          $(document).on("click","#prepare", function() {
            causelist_id = $("#causelist_type option:selected").val();
                            
                merge_date = $("#merge_date").val();

                if(merge_date=="" || causelist_id==""){
                    swal("Fill the mandatory fields","","error");
                    return false;
                }

                
                 $.ajax({
                        url: "merge_causelist_controller.php",
                        type: "POST",
                        dataType: "json",
                        data: { merge_date:merge_date,
                                causelist_id:causelist_id,
                                type_of_job:"validation" 
                              },
                        success:function( data, textStatus, jqXHR ){                            
                            
                            if(data.success=='Marging on process')
                            {
                                
                                $("#table_display").show();
                                $("#tbody").html('');
                                $("#table_display").show();
                                $("#div_prepare").hide();
                                $("#div_publish").show();

                                $.each(jqXHR.responseJSON.data,function(index,value){
                                    $("#tbody").append("<tr><td>"+(index+1)+"</td><td class='bench_desc'>"+value.bench_desc+"</td><td class='filename' style='display:none;'>"+value.filename+"</td><td><input type='number' class='priority' style='width:50px;'></td></tr>");
                                })
                            }
                            else
                            {
                                swal(data.success,"","success");
                            }
                           
                        },
                        error:function(jqXHR, textStatus, errorThrown){ 
                            swal(jqXHR.responseJSON.error,"No causelist found","error");
                            return false;
                        }

                })
            })
            
            var flag;
            $(document).on("click","#publish", function() {                
                var filename=new Array();
                flag=1;
                console.log(filename);
                
                $(".priority").each(function(index,value){
                    var fname=$(this).closest("tr").find(".filename").text();
                    var priority=$(this).val();  
                    if(priority!="")
                    {
                        
                        if(filename[priority-1]!== undefined)
                        {
                            swal("Incorrect Priority","same priority not allowed","error");
                            flag=0;
                            return false;
                        }
                        else
                        {
                            filename[priority-1]=fname;
                        }
                    }
                    else
                    {
                        swal("Enter Priority","separete priority is required","error");
                        flag=0;
                        return false;
                    }

                  
                })
                

                if(flag==1)
                {
                    $.ajax({
                        url:"merge_causelist_controller.php",
                        type:"POST",
                        dataType:"json",
                        data:{
                            type_of_job:"publish_causelist",
                            merge_date:merge_date,
                            filename:filename
                        },
                        success:function(response){ 
                            $("#table_display").hide();
                            $("#iframe_causelist").attr("src", response);
                            $("#show_merge_causelist").show();  
                            $("#publish").hide();
                        }
                    }) 
                }               
            })
    })
    </script>

</body>

</html>