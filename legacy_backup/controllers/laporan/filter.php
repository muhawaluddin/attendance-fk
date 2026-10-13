<?php				
	$error = array();			
	$smsThn = explode(" ", mysql_validasi($_POST["semester"]));
	$semester = $smsThn[0];
	$thn_ajaran = $smsThn[1];
	if (isset($_POST["blok"])) {
		$blok = mysql_validasi($_POST["blok"]);
	}
	
	if (isset($_POST["kelas"])) {
		$kelas = mysql_validasi($_POST["kelas"]);
	}
	
	if (isset($_POST["tanggal"])) {
		$tanggal = mysql_validasi($_POST["tanggal"]);
	}
	
	if (isset($_POST["kegiatan"])) {
		$kegiatan = mysql_validasi($_POST["kegiatan"]);
	}
	
	if (empty($semester)) {
		$blok = "";
	} else {
		$disabledBlok = "";
	}
	
	if (empty($blok)) {
		$kelas = "";
	} else {
		$disabledKelas = "";
	}
	
	if (empty($kelas)) {
		$tanggal = "";
	} else {
		$disabledTanggal = "";
	}
	
	if (empty($tanggal)) {
		$kegiatan = "";
	} else {
		$disabledKegiatan = "";
		$filTgl = "AND jadwal_hari.tanggal = '$tanggal'";
		$linkTgl = "&tanggal=".$tanggal;
	}
	
	if (!empty($kegiatan)) {
		$filKeg = "AND jadwal_jam.id_jJam = '$kegiatan'";
		$linkKeg = "&kegiatan=".md5($kegiatan);
	}
?>