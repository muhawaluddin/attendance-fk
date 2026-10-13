<?php session_start();	
	unset($_SESSION['page']);
	$_SESSION['page'] = "Ruang Kuliah";
	require '../layout/main.php';
?>