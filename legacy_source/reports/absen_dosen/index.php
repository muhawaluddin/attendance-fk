<?php
	session_start();
	if (isset($_SESSION['log_absensi']) && isset($_SESSION['level_absensi'])) {

		include '../../library/connector.php';
		include '../../library/function.php';

		$semester = $_POST['semester'];
		$thn_ajaran = $_POST['tahun'];

        if (isset($_GET['by']) && $_GET['by'] == 'blok') {
            include "by_blok.php";
        } else {
            include "by_dosen.php";
		}
	}