<!DOCTYPE html>
<html lang="en">

<head>
    <title>Paper Register</title>
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

    <div class="jumbotron text-center" style="background-color:#6CA6CD;color:#ffffff;">
        <h2>DATE WISE PAPER REGISTER</h2>
        <div class="row">
            <div class="col-sm-3"></div>
            <div class="col-sm-3">
                <input type="text" class="form-control date" data-provide="datepicker" placeholder="From Date" name="date1" id="date1" autocomplete="off">
            </div>

            <div class="col-sm-3">
                <input type="text" class="form-control date" data-provide="datepicker" placeholder="To Date" name="date2" id="date2" autocomplete="off">
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
                var from_date = $("#date1").val();
                var to_date = $("#date2").val();

                if(from_date=="" || to_date==""){
                    swal("Fill both the date fields","","error");
                    return false;
                }

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
                        "url": "paper_register_report_controller.php",
                        "dataType": "json",
                        "type": "POST",
                        "data": {from_date:from_date, to_date:to_date},
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
                  ],
                  dom: 'Blfrtip',
                    buttons: [                        
                        {
                            extend: 'excelHtml5',
                            exportOptions: {
                                columns: ':visible',                                
                                stripNewlines: false
                            },
                            title: 'Calcutta High Court (Original Side)',
                            messageTop: 'Main Case Register With Paper Details\n\nDate Of Registration: '+from_date+' To '+to_date,
                            messageBottom: 'Printed On '+current_date
                        },
                        {
                            extend: 'pdfHtml5',
                            orientation: 'landscape',
                            pageSize: 'A4',
                            exportOptions: {
                                columns: ':visible',
                                stripNewlines: false
                            },
                            title: 'Calcutta High Court (Original Side)',
                            messageTop: 'Main Case Register With Paper Details\n\nDate Of Registration: '+from_date+' To '+to_date,                            
                            messageBottom: 'Printed On '+current_date,
                            customize: function(doc) {                                
                                doc.content[1].margin = [ 270, 0, 0, 20 ] //left, top, right, bottom                               
                                doc.content[3].margin = [ 0, 30, 0, 0 ] //left, top, right, bottom
                                
                            }
                        }
                    ]
                }); 
            });
        })
    </script>

</body>

</html>