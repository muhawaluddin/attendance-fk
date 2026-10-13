<?php session_start();	
	unset($_SESSION['page']);
	$_SESSION['page'] = "Logout";
	require '../layout/main.php';
?>