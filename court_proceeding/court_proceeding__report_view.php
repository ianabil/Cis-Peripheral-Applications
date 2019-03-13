<!DOCTYPE html>
<html lang="en">

<head>
    <title>Date Wise Court Proceeding Report</title>
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

$sql="select * from court_t where court_t.bench_section = 'B' AND court_t.cfrom_dt <= current_date AND (court_t.cto_dt>= current_date OR court_t.cto_dt IS NULL) ORDER BY court_t.cfrom_dt";

$stmt=$conn->prepare($sql);	
$result=$stmt->execute();	

// Case Type Data Fetched
$bench=$stmt->fetchAll(PDO::FETCH_ASSOC);

?>


    <div class="jumbotron text-center" style="background-color:#CCD1D1;margin-top:30px;">
    <div class="col-sm-12">
         <button type="button" class="btn" onClick="location.href='../index.php';" style="float:right;">HOME</button>
					</div>    
        <h1>Date Wise Court Proceeding Report</h1>
        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-3">
                <select class="form-control" name="bench" id="bench">
                    <option value="">Select Bench</option>
                    <?php foreach ($bench as $bench) { ?>
                        <option value="<?php echo $bench['court_no'];?>"><?php echo $bench['court_no'];?> - <?php echo $bench['bench_desc'];?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-sm-3">
                <input type="text" class="form-control date" name="date1" placeholder="Choose Date" id="date1" value="<?php echo date('d-m-Y') ?>" autocomplete="off">
                <input type="text" class="form-control date" style="display:none" name="date2" id="date2" value="<?php echo date('d-m-Y h:i a') ?>" autocomplete="off">
            </div>

           
            <div class="col-sm-1">
                <button type="button" class="btn btn-info" id="search" name="search">SEARCH</button>
            </div>
        </div>
    </div>

    <!--loader starts-->
    <div class="row">
        <div class="col-md-5"></div>
        <div class="col-md-3" id="wait" style="display:none;">
            <img src='images/ajax-loader.gif'/>
        </div>
    </div>

    <!--loader ends-->


    <div class="container text-center" style="margin-top:10px; display:none;" id="result-section">
        <h3>Search Result</h3>
            <div id="srollable">              
                  <table class="table table-striped table-bordered" id="show_data">
                            <thead>
                                <tr>
                                    <th>Proceeding Status</th>
                                    <th>Case No.</th>
                                    <th>Next Date of Hearing</th>
                                    <th>Next Purpose of Listing</th>
                                    <th>Next Sub-Purpose of Listing</th>
                                    <th>Business</th>
                                    <th>Disposal Nature</th>
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
                var bench = $("#bench option:selected").val();
                var from_date = $("#date1").val();

                if(from_date=="" || bench==""){
                    swal("Fill the mandatory fields","","error");
                    return false;
                }
                
                $('#result-section').show();
                
                var current_date = $("#date2").val() ;
               
               // Datatable Code For Showing Data
                $('#show_data').DataTable().destroy();

                var table = $("#show_data").DataTable({ 
                    "processing": true,
                    "serverSide": true,
                    "searching": false,
                    "lengthMenu": [[50, -1], [50, "All"]],
                    "pageLength": "50",
                    "ajax": {
                        "url": "court_proceeding_report_controller.php",
                        "dataType": "json",
                        "type": "POST",
                        "data": {
                                    bench:bench,
                                    from_date:from_date
                                },
                    },
                    "columns": 
                    [   
                        {"data":"Status"},             
                        {"data": "Case No" },
                        {"data": "Next Date of Hearing"},
                        {"data": "Purpose of Listing"},
                        {"data": "Sub Purpose of Listing"},
                        {"data": "Business"},
                        {"data": "Disposal Nature"}
                    ],
                  dom: 'Blfrtip',
                    buttons: [                        
                        {
                            extend: 'excelHtml5',
                            exportOptions: {
                                columns: ':visible',
                                stripNewlines: false
                            },
                            title: 'CALCUTA HIGH COURT',
                            messageTop: 'DETAILS OF THE COURT PROCEEDING ON DATE : '+from_date,
                            messageBottom: 'Printed On '+current_date
                        },
                        {
                            extend: 'pdfHtml5',
                            orientation: 'protrait',
                            pageSize: 'A4',
                            exportOptions: {
                                columns: ':visible',
                                stripNewlines: false
                            },
                            title: 'CALCUTA HIGH COURT',
                            messageTop: 'DETAILS OF THE COURT PROCEEDING ON DATE : '+from_date,
                            messageBottom: 'Printed On '+current_date,
                            customize: function(doc) {
                                doc.content[1].margin = [ 145, 0, 0, 20 ] //left, top, right, bottom                                
                                doc.content[2].margin = [ 20, 5, 0, 0 ] //left, top, right, bottom
                                doc.content[3].margin = [ 0, 70, 0, 0 ] //left, top, right, bottom                                

                            }
                        }
                    ]
                }); 
            });
        })
    </script>

</body>

</html>