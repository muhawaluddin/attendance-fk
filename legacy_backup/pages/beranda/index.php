<?php session_start();	
	unset($_SESSION['page']);
	$_SESSION['page'] = "Beranda";
	require '../layout/main.php';
?>