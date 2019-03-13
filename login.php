

<?php
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>CIS Peripherals Login</title>
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
	#header-section1{
	-webkit-box-shadow: 0px 6px 20px -1px rgba(0,0,0,0.75);
		-moz-box-shadow: 0px 6px 20px -1px rgba(0,0,0,0.75);
		box-shadow: 0px 6px 20px -1px rgba(0,0,0,0.75);
	}
  </style>
</head>

<body style="font-family:'Times New Roman', Times, serif; background-image: url('images/bg.png');">

	<?php include 'header.php';?>
  
  <div class="row" style="margin-top:40px">
		<div class="col-sm-offset-4 col-sm-4">	
			<div class="jumbotron text-center" id="header-section1" style="margin-bottom: 20px; padding:5px; background-color:#36648B; color:#ffffff; border:#C8C8C8 15px solid; border-radius: 25px;">
			<img src="images/home.png" onClick="location.href='index.php';" style="float:right;height:40px;;width:40px;cursor: pointer;">
								<div class="row">
								<div class="col-sm-offset-4 col-sm-4">	
										<h3>User Login</h3>	 
										<img src="images/CHC_logo.png" style="width:50%;height:50%;"/>
								</div>
								</div>

			<hr>
			<h5><span id="report" style="color:white;">&nbsp;</span></h5>

								<div class="row"> <!-- row starts for login id-->
								  <div class="form-group required">
										<div class="col-sm-offset-1 col-sm-3" style="margin-top:10px">							
											<label class="control-label">Login ID</label>
										</div>
									<div class="col-sm-6">																			
										<input type="text" class="form-control credencial" name="uid" id="uid" placeholder="Login ID">
									</div>																
								  </div>	
								</div> <!-- row end-->

                                <br>

								<div class="row"> <!-- row starts for password-->

										<div class="form-group required">
											<div class="col-sm-offset-1 col-sm-3" style="margin-top:10px">							
												<label class="control-label">Password</label>
											</div>
											<div class="col-sm-6 ">																			
												<input type="password" class="form-control credencial" name="pwd" id="pwd" placeholder="Password">
											</div>																
										</div>	 

								</div>	<!-- row ends for passowrd-->	
								

								<br>

								<div class="row"> <!-- row starts for submit & reset-->

									<div class="col-sm-offset-4 col-sm-3" style="margin-top:10px">		
											<div class="form-group">
													<button type="button"  class="form-control  btn-success" name="login" id="login">Login</button>
											</div>
									</div>

									<div class="col-sm-3" style="margin-top:10px">		
											<div class="form-group">
													<button type="button"  class="form-control btn btn-danger" name="reset" id="reset" style="display: none">Reset</button>
											</div>
									</div>

								</div> <!-- row ends for submit & reset-->
									


				</div>
		</div>
	</div>

	




    <script>
        $(document).ready(function() {

			$("#uid").focus();	


          /*LOADER*/
          $(document).ajaxStart(function() {
              $("#wait").css("display", "block");
         });
            
          $(document).ajaxComplete(function() {
              $("#wait").css("display", "none");
          });
          /*LOADER*/



		    // To activate reset button
            $(document).on("keyup", ".credencial", function() {
			        $("#reset").show();
                    //$("#report").html(' ');    					                               
            });


            // To reset the page end   
			
            // To reset the page start
            $(document).on("click", "#reset", function() {
			        $("#reset").hide();
					$("#report").html('&nbsp;');                   
                    $("#uid").val('');
					$("#pwd").val('');
					$("#uid").focus();					                
            });
            // To reset the page end


			//To activate click event in enter start
			$('#pwd').keypress(function(e) {
                var key = e.which;
                if (key == 13) // the enter key code
                {
                    $('#login').trigger('click');
                }
            });
            //To activate click event in enter end


			$(document).on("click", "#login", function() {

					//$('#report').html('');
					//$("#report").hide(); 

                    var uid = $("#uid").val();
                    var pwd = $("#pwd").val();                  
					
                    //validation code

                    if (uid.length < 1) {
						$('#report').html('<span class="report" style="color:white;">Enter Login ID</span>');						
                        $("#uid").focus();
                    }
                    
                    else if (pwd.length < 1) {
						
                        $('#report').html('<span class="report" style="color:white;">Enter Password</span>');						
                        $("#uid").focus();
                    }

					else
					{               

						$.ajax({
							type: "POST",
							url: "validation_login.php",
							dataType:"JSON",
							data: {
								uid: uid,
								pwd: pwd
							},
							
							
							success: function(response) {
			
								if (response>0) 
								{								
										$('#report').html('&nbsp;');
										window.location.href="index.php";						
								}
								else
								{
									
									$("#pwd").val('');
									$('#report').html('<span class="report" style="color:white;">Check Login ID or Password or Establishment</span>');
									$("#uid").focus();
								}
							}

							
						})
				
					}	//else of validation close

                })
            });
    </script>

</body>

</html>