<?php session_start();	
	unset($_SESSION['page']);
	$_SESSION['page'] = "Finger Print";
	require '../layout/main.php';
?>