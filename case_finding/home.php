<?php 
require_once('../connection.php');?>
    <html>

    <head>
        <title>Finding New Application Details</title>
        <link rel="stylesheet" href="../css/bootstrap.min.css">
    </head>

    <body style="font-family: 'Times New Roman', Times, serif;">
        <div id="about" class="about-area area-padding">
            <div class="container">
                <div class="row">

                    <div class="col-sm-12">
                        <div class="section-headline text-center">
							<button type="button" class="btn" onClick="location.href='../index.php';" style="float:right;">HOME</button>
                            <h2 style="background-color:#36648B;padding:30px;color:#ffffff;">FIND NEW APPLICATION DETAILS FOR OLD APPLICATIONS</h2>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">

                        <div class="col-sm-3">
                            <label class="control-level" style="margin-left:5%">App. Type</label>
                            <br>
                            <select class="form-control" id="app_type">
                                <option value="" selected="selected">Select</option>
                                <?php
																$query="SELECT ia_type_name from ia_case_type_t order by ia_type_name";
																$bind_param_arr=array();
																$sqlchk=$conn->prepare($query);		 
																$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
																$sqlchk->execute($bind_param_arr);	
																$rowchk=$sqlchk->fetchAll();	
																foreach($rowchk as $row)
																{
																	echo '<option value="'.$row['ia_type_name'].'">'.$row['ia_type_name'].'</option>';
																}
															?>
                            </select>
                        </div>

                        <div class="col-sm-2">
                            <label class="control-level" style="margin-left:5%">App.No.</label>
                            <br>
                            <input type="text" class="form-control" id="app_no" name="app_no">
                        </div>

                        <div class="col-sm-2">
                            <label class="control-level" style="margin-left:5%">App.Year</label>
                            <br>
                            <input type="text" class="form-control" id="app_year">
                        </div>

                        <div class="col-sm-2">
                            <label class="control-level" style="margin-left:5%">&nbsp;</label>
                            <br>
                            <button type="button" class="form-control btn btn-success" value="search" id="search">Search</button>

                        </div>

                        <div class="col-sm-2">
                            <label class="control-level" style="margin-left:5%">&nbsp;</label>
                            <br>
                            <button type="button" class="form-control btn btn-danger" value="reset" id="reset">Reset</button>

                        </div>

                    </div>
                </div>
            <br>
			<div class="form-group">
                    <div class="row">

                        <div class="col-sm-12">
                            <div class="row" id="result_success" style="display:none;text-align:center; background-color:#e8ebee;padding:5px;border:5px solid #b4b4b4">

                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
        <!--@endsection-->
        <!--loader starts-->
            <div class="row">
                <div class="col-sm-5"></div>  
                        <div class="col-sm-3" id="wait" style="display:none;">
                            <img src='../images/loader.gif' width="10%" height="5%" />
                            <br>Loading Result..
                        </div>
            </div>
        <!--loader ends-->
        <script src="../js/jquery.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>
        <script>
            $(document).ready(function() {
				$(document).ajaxStart(function() {
                        $("#wait").css("display", "block");
                    });

                $(document).ajaxComplete(function() {
                        $("#wait").css("display", "none");
                    });
					
                $(document).on("click", "#reset", function() {
                    $("#app_no").val('');
                    $("#app_year").val('');
                    $("#app_type").val('');
                    $("#result_success").hide();
                });

                $(document).on("click", "#search", function() {
                    $(".error").html("");
                    var appno = $("#app_no").val();
                    var appyear = $("#app_year").val();
                    var apptype = $("#app_type").val();
                    var flag = 1;

                    //validation code

                    if (apptype.length < 1) {
                        $('#app_type').after('<span class="error" style="color:red;">This field is required</span>');
                        flag = 0;

                    }
                    if (appno.length < 1) {
                        $('#app_no').after('<span class="error" style="color:red;">This field is required</span>');
                        flag = 0;

                    }
                    if (appyear.length < 1) {
                        $('#app_year').after('<span class="error" style="color:red;">This field is required</span>');
                        flag = 0;
                    }

                    if (isNaN(appyear)) {
                        $('#app_year').after('<span class="error" style="color:red;">Please type year properly</span>');
                        flag = 0;
                    }

                    if (flag == 0) {
                        return false;
                    }

                    $.ajax({
                        type: "POST",
                        url: "entry.php",
                        data: {
                            app_no: appno,
                            app_year: appyear,
                            app_type: apptype
                        },
                        success: function(response) {
                            if (response[0]) {
                                $("#result_success").html("<div class='col-sm-5'><h5><strong>Old Application Detail</strong></h5><span id='old_application_details'></span></div><div class='col-sm-5'><h5><strong>New Application Detail</strong></h5><span id='new_application_details'></span></div><div class='col-sm-2'><h5><strong>Case Status</strong></h5><span id='case_status'></span></div>");
                                
								$("#old_application_details").html("<span style='color:#8B0000;'><strong>" + response[0]["calhc_appl_type"] + " " + response[0]["calhc_appl_no"] + " of " + response[0]["calhc_appl_year"] + "</strong></span>");
                                
								$("#new_application_details").html("<span style='color:#8B0000;'><strong>" + response[0]["ia_type_name"] + " " + response[0]["ia_regno"] + " " + "of " + response[0]["ia_regyear"] + "</strong></span>" + " in maincase " +
                                    "<span style='color:#8B0000;'><strong>" + response[0]["type_name"] + " " + response[0]["reg_no"] + " of " + response[0]["reg_year"] + "</strong></span>");
                                
								if(response[0].from_ia_filing_a=='Y')
                                    $("#case_status").html("<span style='color:#1ABC9C;'><strong>DISPOSED</strong></span>");
                                else
                                    $("#case_status").html("<span style='color:#8B0000;'><strong>PENDING</strong></span>");
								
								$("#result_success").show();
                            } else {
                                $("#result_success").html("<h4 style='margin:auto;'>No Data Found</h4>");
                                $("#result_success").show();
                            }

                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            alert("Some Server Error Occured! Please Try Again.")
                        }
                    })

                })
            });
        </script>
    </body>

    </html>