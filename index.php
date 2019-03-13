<?php 
	if(session_id() == '') {
		session_start();
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>CIS Periphery Applications</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/balloon.css">
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <style>
	.card{
		-webkit-box-shadow: 4px 4px 5px 1px rgba(0,0,0,0.60);
		-moz-box-shadow: 4px 4px 5px 1px rgba(0,0,0,0.60);
		box-shadow: 4px 4px 5px 1px rgba(0,0,0,0.60); 
		border-radius: 9px 9px 9px 9px;
		-moz-border-radius: 9px 9px 9px 9px;
		-webkit-border-radius: 9px 9px 9px 9px;
	}
	.card:active{
		transform: translateY(4px);	
		-webkit-box-shadow: 2px 2px 4px 0px rgba(0,0,0,0.7);
		-moz-box-shadow: 2px 2px 4px 0px rgba(0,0,0,0.7);
		box-shadow: 2px 2px 4px 0px rgba(0,0,0,0.7);
	}
	#header-section{
	    -webkit-box-shadow: 0px 6px 20px -1px rgba(0,0,0,0.75);
		-moz-box-shadow: 0px 6px 20px -1px rgba(0,0,0,0.75);
		box-shadow: 0px 6px 20px -1px rgba(0,0,0,0.75);
	}
  </style>
</head>

<body style="font-family:'Times New Roman', Times, serif; background-image: url('images/bg.png');">

<?php include 'header.php';?>
  
<div class="container text-center">
  
  <div class="row"><!-- row starts -->
    <div class="col-sm-offset-1 col-sm-2">
		<a id="clickhere" href="case_finding/old_to_new_view.php" style="text-decoration:none; color:#000000;">
		<div class="card" style="width:100%;border: #c8c8c8 5px solid; margin:auto;" data-balloon-length="fit" data-balloon="Enter old application type, number and year in the provided form and press search to get related new application details along with case details." data-balloon-pos="down">
		<img class="card-img-top" src="images/old_to_new.jpg" alt="old to new" style="width:100%;">
		</div></a>
    </div>
    <div class="col-sm-2">
		<a href="case_finding/new_to_old_view.php" style="text-decoration:none;color:#000000;">
		<div class="card" style="width:100%;border: #c8c8c8 5px solid;margin:auto;" data-balloon-length="fit" data-balloon="Enter case type, case no. case year & new application type, number & year in the provided form and you will get related old aplication details." data-balloon-pos="down">
		<img class="card-img-top" src="images/new_to_old.jpg" alt="new to old" style="width:100%;">
		</div></a>
    </div>
    <div class="col-sm-2">
	    <a href="case_finding/main_case_view.php" style="text-decoration:none;color:#000000;">
		<div class="card" style="width:100%;border: #c8c8c8 5px solid;margin:auto;" data-balloon-length="fit" data-balloon="Enter main case type, case no., case year in the provided form and you will get related old as well as new application details." data-balloon-pos="down">
		<img class="card-img-top" src="images/case_type_based.jpg" alt="Case type based" style="width:100%;">
		</div></a>
    </div>
	<div class="col-sm-2">
		<a href="peonbook/index.php" style="text-decoration:none;color:#000000;">
		<div class="card" style="width:100%;border: #c8c8c8 5px solid;margin:auto;" data-balloon-length="fit" data-balloon="Select any case name from drop down & pick up any date range, then click on search button to get peonbook report which can be downloaded as PDF." data-balloon-pos="down">
		<img class="card-img-top" src="images/peonbook.jpg" alt="peonbook" style="width:100%;">
		</div></a>
    </div>
	<div class="col-sm-2">
		<a href="bench_report/bench_report_view.php" style="text-decoration:none;color:#000000;">
		<div class="card" style="width:100%;border: #c8c8c8 5px solid;margin:auto;" data-balloon-length="fit" data-balloon="Select any date from datepicker and click on search button to get bench report on that date. The report can also be downloaded as PDF and Excel." data-balloon-pos="down">
		<img class="card-img-top" src="images/bench.jpg" alt="bench report" style="width:100%;">
		</div></a>
    </div>
  </div> <!-- row ends -->
  <br>
  <div class="row"><!-- row starts -->
    <div class="col-sm-offset-1 col-sm-2">
		<a href="caveat/caveat_report_view.php" style="text-decoration:none;color:#000000;">
		<div class="card" style="width:100%;border: #c8c8c8 5px solid;margin:auto;" data-balloon-length="fit" data-balloon="Select any date range from datepickers & press search button to get date wise caveat report. The report can also be downloaded as PDF and Excel." data-balloon-pos="down">
		<img class="card-img-top" src="images/date_caveat.jpg" alt="date wise caveat report" style="width:100%;">
		</div></a>
    </div>
	<div class="col-sm-2">
		<a href="caveat/caveat_search_view.php" style="text-decoration:none;color:#000000;">
		<div class="card" style="width:100%;border: #c8c8c8 5px solid;margin:auto;" data-balloon-length="fit" data-balloon="Type any caveator/caveatee name in provided text box, then click on search button to get caveat report which can be downloaded as PDF or Excel." data-balloon-pos="down">
		<img class="card-img-top" src="images/name_caveat.jpg" alt="caveator/caveatee name wise caveat report" style="width:100%;">
		</div></a>
    </div>
	<div class="col-sm-2">
		<a href="paper/paper_register_report_view.php" style="text-decoration:none;color:#000000;">
		<div class="card" style="width:100%;border: #c8c8c8 5px solid;margin:auto;" data-balloon-length="fit" data-balloon="Pick up any date range from datepicker provided and hit search to get details of paper(s) submitted for the cases registered within that date range." data-balloon-pos="down">
		<img class="card-img-top" src="images/date_paper.jpg" alt="date wise paper register" style="width:100%;">
		</div></a>
    </div>
	<div class="col-sm-2">
		<a href="paper/paper_search_view.php" style="text-decoration:none;color:#000000;">
		<div class="card" style="width:100%;border: #c8c8c8 5px solid;margin:auto;" data-balloon-length="fit" data-balloon="Select case type from dropdown, enter case no. & case reg. year in provided text boxes, hit search to get paper(s) submitted for that particular case." data-balloon-pos="down">
		<img class="card-img-top" src="images/case_paper.jpg" alt="case wise paper register" style="width:100%;">
		</div></a>
    </div>
	<div class="col-sm-2">
		<a href="court_proceeding/court_proceeding_report_view.php" style="text-decoration:none;color:#000000;">
		<div class="card" style="width:100%;border: #c8c8c8 5px solid;margin:auto;" data-balloon-length="fit" data-balloon="Report of court proceeding for a particular date and bench." data-balloon-pos="down">
		<img class="card-img-top" src="images/court_proc_report.jpg" alt="court proceeding report" style="width:100%;">
		</div></a>
    </div>
  </div> <!-- row ends -->
  <br>
  <div class="row"><!-- row starts -->
	<div class="col-sm-offset-1 col-sm-2">
		<a href="case_registration_type_wise/case_reg_type_wise_report_view.php" style="text-decoration:none;color:#000000;">
		<div class="card" style="width:100%;border: #c8c8c8 5px solid;margin:auto;" data-balloon-length="fit" data-balloon="Number of cases or applications filed within a date range." data-balloon-pos="down">
		<img class="card-img-top" src="images/maincase_inst.jpg" alt="date wise caveat report" style="width:100%;">
		</div></a>
	</div>
	<div class="col-sm-2">
		<a href="blank_cause_list/blank_cause_list_view.php" style="text-decoration:none;color:#000000;">
		<div class="card" style="width:100%;border: #c8c8c8 5px solid;margin:auto;" data-balloon-length="fit" data-balloon="Click here to generate Blank Cause List for a specific bench" data-balloon-pos="down">
		<img class="card-img-top" src="images/blankcauselist.jpg" alt="blank cause list" style="width:100%;">			
		</div></a>
	</div>
	<div class="col-sm-2">
		<a href="Merge_Causelist/merge_causelist_view.php" style="text-decoration:none;color:#000000;">
		<div class="card" style="width:100%;border: #c8c8c8 5px solid;margin:auto;" data-balloon-length="fit" data-balloon="Click here to merge causelists" data-balloon-pos="down">
		<img class="card-img-top" src="images/merge_clist.jpg" alt="merge cause list" style="width:100%;">			
		</div></a>
	</div>
	<div class="col-sm-2">
		<a href="purpose_priority/purpose_priority_view.php" style="text-decoration:none;color:#000000;">
		<div class="card" style="width:100%;border: #c8c8c8 5px solid;margin:auto;" data-balloon-length="fit" data-balloon="Click here to set purpose priority for a specific bench" data-balloon-pos="down">
		<img class="card-img-top" src="images/purpose_priority.jpg" alt="purpose priority" style="width:100%;">			
		</div></a>
	</div>
  </div><!-- row ends -->
  </div>

</body>
</html>
