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
    <title>Bench Wise Purpose Priority</title>
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
// bench list
$bench=$stmt->fetchAll(PDO::FETCH_ASSOC);


$sql="select * from purpose_t order by purpose_code";

$stmt=$conn->prepare($sql);	
$result=$stmt->execute();	
// bench list
$purpose_list=$stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<img src="../images/home.png" onClick="location.href='../index.php';" style="float:right;height:50px;;width:50px;cursor: pointer;">
    <div class="jumbotron text-center" style="background-color:#6CA6CD;color:#ffffff;">
    
        <h2>Set Bench Wise Purpose Priority</h2>
        <br>
        <div class="row">
            <div class="col-md-offset-4 col-sm-3">
                <select class="form-control disable" name="bench" id="bench">
                    <option value="">Select Bench</option>
                    <?php foreach ($bench as $bench) { ?>
                        <option value="<?php echo $bench['court_no'];?>"><?php echo $bench['court_no'];?> - <?php echo $bench['bench_desc'];?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-sm-1" id="go_div">
                <button type="button" class="btn btn-success" id="go" name="go">GO</button>
            </div>
            <div class="col-sm-1" id="submit_div" style="display:none">
                <button type="button" class="btn btn-warning" id="submit" name="submit">SUBMIT</button>
            </div>
            <div class="col-sm-1">
                <button type="button" class="btn btn-danger" id="reset" name="reset">RESET</button>
            </div>
        </div>
        
    </div>

    <div id="purpose_div" style="display:none">
        <div class="row">
            <div class="col-md-7 col-md-offset-1">
                <table class="table table-bordered">
                    <tr>
                        <td>
                            <b>Purpose Master List:</b><br/>
                            <select multiple="multiple" id='lstBox1' style="width:auto; height:400px">
                                <?php foreach ($purpose_list as $purpose) { ?>
                                    <option value="<?php echo $purpose['purpose_code'];?>"><?php echo $purpose['purpose_code'];?>-<?php echo $purpose['purpose_name'];?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td style='width:50px;text-align:center;vertical-align:middle;'>
                            <input type='button' id='btnRight' value ='  &#x2192  '/>
                            <br/><input type='button' id='btnLeft' value ='  &#x2190  '/>
                        </td>
                        <td>
                            <b>Purpose List For The Selected Bench: </b><br/>
                            <select multiple="multiple" id='lstBox2' style="width:550px; height:300px"> 
                            </select>
                        </td>
                        <td style='width:50px;text-align:center;vertical-align:middle;'>                            
                            <br/><button type='button' class="up_down" id='btnUp' value ='Up'>&#x2191</button>
                            <br/><button type='button' class="up_down" id='btnDown' value ='Down'>&#x2193</button>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
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

        var bench;
        $(document).on("click","#go", function() {
                bench = $("#bench option:selected").val();

                if(bench==""){
                    swal("Please Select A Bench","","error");
                    return false;
                }
                else{

                    $("#go_div").hide();
                    $("#submit_div").show();
                    $("#purpose_div").show();
                    $("#bench").attr('disabled',true);
                    
                    $.ajax({
                        url:"purpose_priority_controller.php",
                        type:"POST",
                        data:{
                            bench:bench,
                            type_of_job:"bench_purpose_fetching"
                        },
                        success:function(response){
                           if(response.length>0){
                               $.each(response, function(index,value){
                                   $("#lstBox2").append('<option value="'+value.purpose_code+'">'+value.purpose_code+'-'+value.purpose_name+'</option>');
                               })
                           }
                        }

                    })
                }
        });


        $(document).on("click","#submit", function() {

                var purpose_name = new Array();
                var purpose_code = new Array();

                $("#lstBox2 option").each(function(index,value){
                    purpose_name.push(value.text.substr(value.text.indexOf("-")+1));
                    purpose_code.push(value.text.substr(0,value.text.indexOf("-")));
                });

                if(purpose_name.length==0){
                    swal("Invalid Input","Purpose List Can Not Be Empty","error");
                    return false;
                }
                else{
                    $.ajax({
                        url:"purpose_priority_controller.php",
                        type:"POST",
                        data:{
                            bench:bench,
                            purpose_name:purpose_name,
                            purpose_code:purpose_code,
                            type_of_job:"bench_purpose_insertion"
                        },
                        success:function(response){
                           swal("Purpose Priority Set Successfully","","success");
                           $("#submit_div").hide();
                        }
                    })
                }
        });



        $(document).on("click","#reset", function() {
                location.reload();
        });

       
        $('#btnRight').click(function(e) {
            var selectedOpts = $('#lstBox1 option:selected');
            if (selectedOpts.length == 0) {
                alert("Nothing to move.");
                e.preventDefault();
            }

            $('#lstBox2').append($(selectedOpts).clone());
            $(selectedOpts).remove();
            e.preventDefault();
        });

        $('#btnLeft').click(function(e) {
            var selectedOpts = $('#lstBox2 option:selected');
            if (selectedOpts.length == 0) {
                alert("Nothing to move.");
                e.preventDefault();
            }

            $('#lstBox1').prepend($(selectedOpts).clone());
            $(selectedOpts).remove();
            e.preventDefault();
        });

        $(".up_down").click(function(){
            var $op = $('#lstBox2 option:selected'),
                $this = $(this);
            if($op.length){
                ($this.val() == 'Up') ? 
                    $op.first().prev().before($op) : 
                    $op.last().next().after($op);
            }
        });

       
    });
    </script>

</body>

</html>