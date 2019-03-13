<?php

if(session_id() == '') {
	session_start();
}
// destroy the session 
session_destroy(); 

echo "<script> location.href='index.php'; </script>";
?>