<!DOCTYPE html>
<html lang="en">

<head>
    <title>Paper Search</title>
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

<img src="../images/home.png" onClick="location.href='../index.php';" style="float:right;height:50px;;width:50px;cursor: pointer;">

<?php
include('../connection.php');
$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

$sql="SELECT * FROM case_type_t ORDER BY type_name, case_type";

$stmt=$conn->prepare($sql);	
$result=$stmt->execute();	

// Case Type Data Fetched
$case_type=$stmt->fetchAll(PDO::FETCH_ASSOC);

?>

    <div class="jumbotron text-center" style="background-color:#6CA6CD;color:#ffffff;">
        <h2>PAPER SUBMITTED FOR A SPECIFIC CASE</h2>
        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-2">
                <select class="form-control" name="case_type" id="case_type">
                    <option value="">Select Case Type</option>
                    <?php foreach ($case_type as $type) { ?>
                        <option value="<?php echo $type['case_type'];?>"><?php echo $type['type_name'];?> - <?php echo $type['case_type'];?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-sm-3">
                <input type="text" class="form-control" name="case_no" id="case_no" autocomplete="off" placeholder="Case No.">
            </div>

            <div class="col-sm-2">
                <input type="number" class="form-control" name="case_year" id="case_year" autocomplete="off" placeholder="Case Year">
            </div>

            <div class="col-sm-1">
                <button type="button" class="btn btn-primary" id="search" name="search">SEARCH</button>
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

    <div class="container text-center" style="margin-top:10px; display:none;" id="result-section">
        <h3>Search Result</h3>
            <div id="srollable">              
                  <table class="table table-striped table-bordered" id="show_data">
                            <thead>
                                <tr>
                                    <th>Case No.</th>
                                    <th>Cause Title</th>
                                    <th>Submitted By (Pet/Res)</th>
                                    <th>Despatched To</th>
                                    <th>Paper Details</th>
                                </tr>
                            </thead>
                  </table> 
          </div>
    </div>

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

            $(document).on("click","#search", function() {               
                var case_type = $("#case_type option:selected").val();
                var case_no = $("#case_no").val();
                var case_year = $("#case_year").val();

                if(case_type=="" || case_no=="" || case_year==""){
                    swal("Fill all the input fields","","error");
                    return false;
                }

                /*LOADER*/
                $(document).ajaxStart(function() {
                    $("#wait").css("display", "block");
                });
                    
                $(document).ajaxComplete(function() {
                    $("#wait").css("display", "none");
                });
                /*LOADER*/

                $('#result-section').show();


                var d = new Date();
                var month = d.getMonth()+1;
                var day = d.getDate();

                var current_date = (day<10 ? '0' : '') + day + '-' +
                             (month<10 ? '0' : '') + month + '-' + 
                              d.getFullYear() ;
               
               // Datatable Code For Showing Data
                $('#show_data').DataTable().destroy();
                var table = $("#show_data").DataTable({ 
                    "processing": true,
                    "serverSide": true,
                    "searching": false,
                    "lengthMenu": [[50, -1], [50, "All"]],
                    "ajax": {
                        "url": "paper_search_controller.php",
                        "dataType": "json",
                        "type": "POST",
                        "data": {case_type:case_type, case_no:case_no, case_year:case_year},
                    },
                    "columns": [                
                      {"data": "Case No",
                      "class":"dt-body-left" },
                      {"data": "Cause Title",
                      "class":"dt-body-left" },
                      {"data": "Advocate Name",
                      "class":"dt-body-left" },
                      {"data": "Despatched To",
                      "class":"dt-body-left" },
                      {"data": "Paper Type",
                      "class":"dt-body-left" }
                  ]
                }); 
            });
        })
    </script>

</body>

</html>