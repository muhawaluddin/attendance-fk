<?php session_start();	
	unset($_SESSION['page']);
	$_SESSION['page'] = "Pengaturan";
	require '../layout/main.php';
?>