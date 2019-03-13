<?php

	$dblist_host = "10.173.34.126";
	$dblist_user = "postgres";
	$dblist_port="5432";
	$dblist_password = "";
	$dblist_dbname= "ecourtisuserdb";
	try 
	{  
		$conn_dblist = new PDO("pgsql:host=$dblist_host;dbname=$dblist_dbname", $dblist_user, $dblist_password, array(PDO::ATTR_PERSISTENT => true));	
	}
	catch(PDOException $e) 
	{  
		 $e='Connection Failed';
		 $conn_dblist=$e;
	}  ?>
				
<div class="container text-center" id="header-section" style="margin-bottom: 20px; background-color:#36648B; color:#ffffff; border:#C8C8C8 15px solid;">
    
	<div class="row">
        <div class="col-sm-12">
            <h1>CIS PERIPHERY APPLICATIONS</h1>	  
        </div>
    </div> <!-- row ends -->
	
	<?php  if(!isset($_SESSION ['username'] )) { ?>
        <div class="row form-group">
            <div class="col-sm-offset-4 col-sm-1" style="padding-top:4px;">
                <label for="" class="control-label text-right">Establishment</label>
            </div>
            <div class="col-sm-3">            
                    <select class="form-control" id="dbname">
                    <?php
                        $query="SELECT est_dbname, estname, est_code from establishment";
                        $bind_param_arr=array();
                        $sqlchk=$conn_dblist->prepare($query);		 
                        $conn_dblist->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                        $sqlchk->execute($bind_param_arr);	
                        $rowchk=$sqlchk->fetchAll();	
                    
                        foreach($rowchk as $row)
                        {
                            echo '<option value="'.$row['est_dbname'].'" data-est_code="'.$row['est_code'].'">'.$row['estname'].'</option>';
                        }
                    ?>                
                    </select>
                
            </div>
        </div> <!-- row ends -->
    <?php } ?>
	
	
	
    <div class="row">
        <div class="col-sm-6 text-left">
        <h4><?php 
        if(isset($_SESSION ['username'] ))
            echo "Welcome ".$_SESSION ['username'];
        
        else
            echo "Guest User";
            ?>
        </h4>
        </div>

        <?php       
        if(isset($_SESSION ['username']))       
                echo '<div class="col-sm-6 text-right" name>
                      <a href="logout.php" style="color:white">Logout</a>
                      </div>';      
        else
            echo '<div class="col-sm-6 text-right" name>
                  <a href="login.php" style="color:white">Login</a>
                  </div>';  
        ?>
        
	</div>


</div>
<script type="text/javascript">
    function establishment_change(){
        $.ajax({
            type: "POST",
            url: "establishment_select_ajax.php",
            data: {
                dbname: $('#dbname').val(),
                est_code: $('#dbname option:selected').data('est_code'),
            },
            success: function(response) {
                //console.log('DB Set');
            }
        });
    }
    $(function() {
        establishment_change();
    });
    $( "#dbname" ).change(function() {
        establishment_change();
    });

</script>
