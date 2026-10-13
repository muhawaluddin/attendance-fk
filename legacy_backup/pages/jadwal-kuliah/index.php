<?php session_start();	
	unset($_SESSION['page']);
	if (!isset($_GET['sub'])) {
		unset($_SESSION['kelas']);
	}
	$_SESSION['page'] = "Jadwal Kuliah";
	require '../layout/main.php';
?>