<?php session_start();	
	unset($_SESSION['page']);
	if (isset($_GET['sub'])) {
		if ($_GET['sub'] == "laporan-absen-dosen") {
			$_SESSION['page'] = "Laporan Absen Dosen";
		} else if ($_GET['sub'] == "laporan-absen-mahasiswa") {
			$_SESSION['page'] = "Laporan Absen Mahasiswa";
		} else {
			echo "<meta http-equiv='refresh' content='0; url=../laporan/?sub=laporan-absen-dosen'>";
		}
	} else {
		$_SESSION['page'] = "Laporan Absen Dosen";
		echo "<meta http-equiv='refresh' content='0; url=../laporan/?sub=laporan-absen-dosen'>";
	}
	require '../layout/main.php';
?>