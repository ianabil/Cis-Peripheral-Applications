<!DOCTYPE html>
<html lang="en">
<head>
  <title>Peonbook</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="../css/bootstrap-datepicker.css">
  <link rel="stylesheet" type="text/css" href="../css/dataTables.min.css">
  <link rel="stylesheet" type="text/css" href="../css/jquery.dataTables.min.css">
  <link rel="stylesheet" type="text/css" href="../css/buttons.dataTables.min.css">
  <link rel="stylesheet" type="text/css" href="../css/fixedHeader.dataTables.min.css">
	<script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/dataTables.min.js"></script>
	<script src="../js/dataTables.fixedHeader.min.js"></script>
	<script src="../js/jquery.dataTables.min.js"></script>
	<script src="../js/bootstrap-datepicker.js"></script>
    <script src="../js/dataTables.buttons.min.js"></script>
    <script src="../js/buttons.flash.min.js"></script>
    <script src="../js/jszip.min.js"></script>
    <script src="../js/pdfmake.min.js"></script>
    <script src="../js/vfs_fonts.js"></script>
    <script src="../js/buttons.html5.min.js"></script>
    <script src="../js/buttons.print.min.js"></script>
    <script src="../js/buttons.colVis.min.js"></script>
  
</head>
<body style="font-family:'Times New Roman', Times, serif;">


<img src="../images/home.png" onClick="location.href='../index.php';" style="float:right;height:50px;;width:50px;cursor: pointer;">
<?php include('../connection.php'); ?>
<div class="jumbotron text-center" style="background-color:#6CA6CD; color:#ffffff;padding:30px;">

  <h2>DATE WISE PEONBOOK</h2>
  <div class="row">
	<div class="col-sm-6 col-sm-offset-3">
							<select class="form-control" id="case_type" name="case_type">
                                <option value="" selected="selected">Select any case name from the dropdown</option>
                                <?php
								   $query="SELECT type_name, case_type from case_type_t order by type_name";
								   $bind_param_arr=array();
								   $sqlchk=$conn->prepare($query);		 
								   $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
								   $sqlchk->execute($bind_param_arr);	
								   $rowchk=$sqlchk->fetchAll();	
								   foreach($rowchk as $row)
								   {
									echo '<option value="'.$row['case_type'].'">'.$row['type_name'].'</option>';
								   }
								?>
                            </select>
							<br>
	</div>
  </div>
  <div class="row">
	<div class="col-sm-3 col-sm-offset-3">
							<select class="form-control" id="bench" name="bench">
                                <option value="" selected="selected">Select Bench</option>
                                <?php
								   $query="select * from court_t where court_t.bench_section = 'B' AND court_t.cfrom_dt <= current_date AND (court_t.cto_dt>= current_date OR court_t.cto_dt IS NULL) ORDER BY court_t.cfrom_dt";
								   $bind_param_arr=array();
								   $sqlchk=$conn->prepare($query);		 
								   $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
								   $sqlchk->execute($bind_param_arr);	
								   $benchrows=$sqlchk->fetchAll();	
								   foreach($benchrows as $bench)
								   { 
								?>
									<option value="<?php echo $bench['court_no'];?>"><?php echo $bench['court_no'];?> - <?php echo $bench['bench_desc'];?></option>
								   <?php }  ?>
								
                            </select>
    </div>
	<div class="col-sm-3">
							<select class="form-control" id="appl" name="appl">
                                <option value="" selected="selected">Select Application Type</option>
                                <?php
								   $query1="select * from ia_case_type_t";
								   $bind_param_arr=array();
								   $sqlchk1=$conn->prepare($query1);		 
								   $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
								   $sqlchk1->execute($bind_param_arr);	
								   $applrows=$sqlchk1->fetchAll();	
								   foreach($applrows as $appl)
								   { 
								?>
									<option value="<?php echo $appl['ia_case_type'];?>"><?php echo $appl['ia_type_name'];?></option>
								   <?php }  ?>
								
                            </select>
    </div>
	
	</div><br>

  <div class="row">
	<div class="col-sm-3 col-sm-offset-3">
		<input type="text" class="form-control date" name="from_date" id="from_date" placeholder="From Date" autocomplete="off">	
    </div>
	<div class="col-sm-3">
		<input type="text" class="form-control date" name="to_date" id="to_date" placeholder="To Date" autocomplete="off">		
	</div>
  </div>
  <br>
  <div class="row">
	<div class="col-sm-4 col-sm-offset-4">
		<button type="button" class="btn btn-primary" id="search" name="search" style="width:200px;">SEARCH</button>&nbsp;&nbsp;
		<button type="button" class="btn btn-warning" id="reset" name="reset" style="width:200px;">RESET</button>
    </div>
  </div>
</div>

<!--loader starts-->
    <div class="row">
        <div class="col-md-5"></div>
        <div class="col-md-3" id="wait" style="display:none;">
            <img src='../images/loading.gif' style="width:80%; height:50%;"/>
        </div>
    </div>

    <!--loader ends-->
	

<div class="container text-center" style="margin-top:10px; display:none;" id="result-section">
  <h3>Search Result</h3> 
	
		<div id="scrollable"> 
		<table class="table table-striped table-bordered" id="show_data">      
		<thead>
		<tr>
		 <th>Sl No.</th>
         <th>Case No.</th>
		 <th>Date of Registration</th>
		 <th>Cause Title</th>
		 <th>Court No.</th>
		 <th>Bench Description with Bench ID</th>
		 <th>JO Code</th>
		 <th>Status</th>
		</tr>
		</thead>
		</table>
		</div>  
</div>

<div class="container text-center" style="margin-top:10px; display:none;" id="result-section1">
   <h3>Application Details <span id="from"></span> & <span id="to"></span></h3>
            <div id="scrollable" >              
                  <table class="table table-striped table-bordered text-center" id="show_data1">
                            <thead>
                                <tr>
									<th>Sl No.</th>
                                    <th>Application No.</th>                                                                    
                                    <th>Date of IA Registration</th>
                                    <th>Court No.</th>
									<th>Bench Description</th>
									<th>JO Code</th>
                                </tr>
                            </thead>
                  </table> 
          </div>
</div>


<script>

    $(document).ready(function(){
				
		/*LOADER*/
            $(document).ajaxStart(function() {
                $("#wait").css("display", "block");
            });
            
            $(document).ajaxComplete(function() {
                $("#wait").css("display", "none");
            });
        /*LOADER*/
		
		$(".date").datepicker({format: 'dd-mm-yyyy',maxDate: 0}); // datepicker initialization
		
		
		$(document).on("click","#reset", function() {             // when press reset button
		  $(".error").html("");
		  $("#case_type").val("");
		  $("#from_date").val("");
		  $("#to_date").val("");
		  $("#bench").val("");
		  $("#iacasetype").val("");
		  $("#appl").val("");
		  $("#result-section").hide();
		  $('#result-section1').hide();
		});
       
		
		$(document).on("click","#search", function() {            // when press click button
			        $(".error").html("");
                    var casetype = $("#case_type").val();
					var typename = $("#case_type option:selected").text();
                    var fromdate = $("#from_date").val();
                    var todate = $("#to_date").val();
					var benchid = $("#bench").val();
					var application = $("#appl").val();
                    var flag = 1;
                
				    // input fields validation code
					if (casetype.length < 1) {
                        $('#case_type').after('<span class="error" style="color:#ffffff;">This field is required</span>');
                        flag = 0;

                    }
                    if (fromdate.length < 1) {
                        $('#from_date').after('<span class="error" style="color:#ffffff;">This field is required</span>');
                        flag = 0;

                    }
                    if (todate.length < 1) {
                        $('#to_date').after('<span class="error" style="color:#ffffff;">This field is required</span>');
                        flag = 0;
                    }
					if (flag == 0) {
                        return false;
                    }
					
					
					
					var d = new Date();
					var month = d.getMonth()+1;
					var day = d.getDate();
					var current_date = (day<10 ? '0' : '') + day + '-' + (month<10 ? '0' : '') + month + '-' + d.getFullYear();
					
					$('#result-section').show();
					$('#result-section1').show();
					
					// Datatable Code
					$('#show_data').DataTable().destroy();
					var table = $("#show_data").DataTable({ 
                    "processing": true,
                    "serverSide": true,
					"searching": false,
                    "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                    "ajax": {
                        "url": "search.php",
                        "dataType": "json",
                        "type": "POST",
                        "data": {casetype:casetype,
						         typename:typename,
								 fromdate:fromdate,
								 todate:todate,
								 benchid:benchid
								}
                    },
                    "columns": [                
                      {"data": "Sl No",
                      "class":"table-data" },
                      {"data": "Case No",
                      "class":"table-data" },
                      {"data": "Date of Registration",
                      "class":"table-data" },
                      {"data": "Cause Title",
                      "class":"table-data" },
                      {"data": "Court No",
                      "class":"table-data" },
                      {"data": "Bench Description",
                      "class":"table-data" },
                      {"data": "JO Code",
                      "class":"table-data" },
					  {"data": "status",
                      "class":"table-data" }
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
                            messageTop: 'Peonbook Report From '+fromdate+' To '+todate,
                            messageBottom: 'Printed On '+current_date
                        },
                        {
                            extend: 'pdfHtml5',
                            orientation: 'landscape',
                            pageSize: 'A3',
                            exportOptions: {
                                columns: ':visible',
                                stripNewlines: false,
                            },
                            title: 'Calcutta High Court (Original Side)',
                            messageTop: 'Peonbook Report From '+fromdate+' To '+todate,
                            messageBottom: 'Printed On '+current_date,
                            customize: function(doc) {
                                doc.content[1].margin = [ 440, 0, 0, 20 ] //left, top, right, bottom                                
                                doc.content[3].margin = [ 0, 100, 0, 0 ] //left, top, right, bottom
                            }
                        }
                    ]

                }); 	
				
				$('#show_data1').DataTable().destroy();
                var table = $("#show_data1").DataTable({ 
                    "processing": true,
                    "serverSide": true,
                    "searching": false,
                    "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                    "ajax": {
                        "url": "ia_case_reg_type_wise_report_controller.php",
                        "dataType": "json",
                        "type": "POST",
                        "data": {fromdate:fromdate,
								 todate:todate,
								 application:application
								}
                    },
                    "columns": [ 
						 {"data": "Sl No"},
                         {"data": "Appl No"},
                         {"data": "IA Reg Dt"},
						 {"data": "Court No"},
						 {"data": "Bench Desc"},
						 {"data": "JO Code"}
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
                            messageTop: 'Application Count From '+fromdate+' To '+todate,
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
                            messageTop: 'Application Details From '+fromdate+' To '+todate,                            
                            messageBottom: 'Printed On '+current_date,
                            customize: function(doc) {                                
                                doc.content[1].margin = [ 270, 0, 0, 20 ] //left, top, right, bottom                                
                                doc.content[3].margin = [ 0, 100, 0, 0 ] //left, top, right, bottom
                                
                            }
                        }
                    ]
                }); 
			
				
				$("#from").html(fromdate);
				$("#to").html(todate);
				
		}); 
		
		
	})
</script>

</body>
</html>
