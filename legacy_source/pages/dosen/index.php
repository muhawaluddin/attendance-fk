<?php session_start();	
	unset($_SESSION['page']);
	$_SESSION['page'] = "Dosen";
	require '../layout/main.php';
?>