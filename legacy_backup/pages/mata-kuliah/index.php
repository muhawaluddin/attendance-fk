<?php session_start();	
	unset($_SESSION['page']);
	$_SESSION['page'] = "Mata Kuliah";
	require '../layout/main.php';
?>