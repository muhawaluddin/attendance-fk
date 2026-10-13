<?php session_start();	
	unset($_SESSION['page']);
	$_SESSION['page'] = "Akun";
	require '../layout/main.php';
?>