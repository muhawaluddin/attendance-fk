<?php session_start();	
	unset($_SESSION['page']);
	$_SESSION['page'] = "Departemen";
	require '../layout/main.php';
?>