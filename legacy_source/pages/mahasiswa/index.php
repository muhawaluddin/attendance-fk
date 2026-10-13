<?php session_start();	
	unset($_SESSION['page']);
	$_SESSION['page'] = "Mahasiswa";
	require '../layout/main.php';
?>