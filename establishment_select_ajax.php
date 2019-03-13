<?php

if(session_id() == '') {
    session_start();
}

if(!empty($_POST['dbname'])){
    $_SESSION['dbname']=$_POST['dbname'];
    $_SESSION['est_code']=$_POST['est_code'];
}
?>