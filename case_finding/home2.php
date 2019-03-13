<?php 
   require_once('../connection.php');?>
    <html>

    <head>
        <title>Find Application Details for a Case Type</title>
        <link rel="stylesheet" href="../css/bootstrap.min.css">
    </head>

    <body style="font-family: 'Times New Roman', Times, serif;">
        <div id="about" class="about-area area-padding">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="section-headline text-center">
						    <button type="button" class="btn" onClick="location.href='../index.php';" style="float:right;">HOME</button>
                            <h2 style="background-color:#36648B;padding:30px;color:#ffffff;">FIND APPLICATION DETAILS FOR A CASE TYPE</h2>
                        </div>
                    </div>
                </div>
                <!-- <h4 style="text-align: center; padding-bottom: 5%;">PLEASE ENTER OLD APPLICATION DATA</h4>	-->
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-3">
                            <label class="control-level" style="margin-left:5%">Case Type</label>
                            <br>
                            <select class="form-control" id="case_type" name="case_type">
                                <option value="" selected="selected">Select</option>
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
                        </div>
                        <div class="col-sm-2">
                            <label class="control-level" style="margin-left:5%">Case Number</label>
                            <br>
                            <input type="text" class="form-control" id="case_no" name="case_no">
                        </div>
                        <div class="col-sm-2">
                            <label class="control-level" style="margin-left:5%">Case Year</label>
                            <br>
                            <input type="text" class="form-control" id="case_year" name="case_year">
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
                   
                        
                <div id="result_success" style="display:none;">
                    <table class="table table-striped table-bordered text-center" id="tbody">
                        <tr>
                            <td> New Application Details </td>
                            <td> Old Application Details </td>
							<td>Case Status</td>
                        <tr>
                    </table>
                </div>
                <div class="form-group" id="no_data_found" style="display:none">
                    
                        <div class="col-sm-12">
                            <div class="row" style="background-color:#e8ebee;padding:5px;border:5px solid #b4b4b4">
                                
                                <h4 style="margin:auto; text-align:center" id="data" name="data"></h4>
                                <div class="col-sm-3"></div>
                            </div>
                        </div>
                </div>
                    </div>
                </div>
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

                    $("#case_no").val('');
                    $("#case_year").val('');
                    $("#case_type").val('');
                    $("#result_success").hide();
                    $("#no_data_found").hide();

                });

                $(document).on("click", "#search", function() {
                    $(".error").html("");
                    var caseno = $("#case_no").val();
                    var caseyear = $("#case_year").val();
                    var casetype = $("#case_type").val();
                    var flag = 1;
                    $("#data").hide();
                    $("#no_data_found").hide();
				    $(".tuples").html("");
                    

                    //validation code

                    if (casetype.length < 1) {
                        $('#case_type').after('<span class="error" style="color:red;">This field is required</span>');
                        flag = 0;

                    }
                    if (caseno.length < 1) {
                        $('#case_no').after('<span class="error" style="color:red;">This field is required</span>');
                        flag = 0;

                    }
                    if (caseyear.length < 1) {
                        $('#case_year').after('<span class="error" style="color:red;">This field is required</span>');
                        flag = 0;
                    }

                    if (isNaN(caseyear)) {
                        $('#case_year').after('<span class="error" style="color:red;">Enter year properly</span>');
                        flag = 0;
                    }

                    if (isNaN(caseno)) {
                        $('#case_no').after('<span class="error" style="color:red;">Enter case no. properly</span>');
                        flag = 0;
                    }

                    if (flag == 0) {
                        return false;
                    }

                    $.ajax({
                        type: "POST",
                        url: "entry2.php",
                        data: {
                            case_no: caseno,
                            case_year: caseyear,
                            case_type: casetype
                        },

                        success: function(response) {
                            if (response[0]) {
                                var len = response.length;
                                for (i = 0; i < len; i++) {
									var status;
									if(response[i]["from_ia_filing_a"]=='Y')
										status="<span style='color:#1ABC9C;'>DISPOSED</span>";
									else
										status="<span style='color:#8B0000;'>PENDING</span>";
                                    $("#tbody").append("<tr class='tuples'><td style='color:#8B0000'>" + response[i]["ia_type_name"] + " " + response[i]["ia_regno"] + " " + "of " + response[i]["ia_regyear"] + "</td><td style='color:#8B0000;'>" + response[i]["calhc_appl_type"] + " " + response[i]["calhc_appl_no"] + " of " + response[i]["calhc_appl_year"] + "</td><td>"+status+"</td></tr>");
                                }
                                $("#result_success").show();
                            } else {
                                $("#no_data_found").show();
                                $("#data").html('No Data Found');
                                $("#data").show();
                                
                                $("#result_success").hide();
                            }

                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            alert("Some Server Error Occured! Please Try Again.");
                        }
                    })

                })

            });
        </script>
    </body>

    </html>