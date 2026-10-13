<?php		
	$error = array();			
	$id = mysql_validasi($_POST["id"]);
	$menit_perjamEdit = mysql_validasi($_POST["menit_perjam"]);
	$menit_pergantianEdit = mysql_validasi($_POST["menit_pergantian"]);
	$jam_kuliahEdit = mysql_validasi($_POST["jam_kuliah"]);
	$jam_mulaiEdit = mysql_validasi($_POST["jam_mulai"]);
	$jam_selesaiEdit = mysql_validasi($_POST["jam_selesai"]);
	$jam_perhariEdit = mysql_validasi($_POST["jam_perhari"]);
	
	if (md5($id) != $_GET['edit']) {
		$error[] = "";
	}
	
	if (empty($menit_perjamEdit)) {
		$menit_perjamEditErr = '<i class="fa fa-exclamation-triangle"></i> Jumlah menit perjam tidak boleh kosong.<br>';
		$error[] = "";
	} else if (!preg_match("/^[0-9]*$/",$menit_perjamEdit)) {
		$menit_perjamEditErr = '<i class="fa fa-exclamation-triangle"></i> Hanya dapat menggunakan angka.<br>';
		$error[] = "";
	}
	
	if ($menit_pergantianEdit < 0 || $menit_pergantianEdit == "") {
		$menit_pergantianEditErr = '<i class="fa fa-exclamation-triangle"></i> Jumlah menit pergantian tidak boleh kosong.<br>';
		$error[] = "";
	} else if (!preg_match("/^[0-9]*$/",$menit_pergantianEdit)) {
		$menit_pergantianEditErr = '<i class="fa fa-exclamation-triangle"></i> Hanya dapat menggunakan angka.<br>';
		$error[] = "";
	}
	
	if (empty($jam_kuliahEdit)) {
		$jam_kuliahEditErr = '<i class="fa fa-exclamation-triangle"></i> Jam masuk perkuliahan tidak boleh kosong.<br>';
		$error[] = "";
	} else if (strtotime($jam_kuliahEdit) <= strtotime("00:00")) {
		$jam_kuliahEditErr = '<i class="fa fa-exclamation-triangle"></i> Jam masuk perkuliahan tidak valid.<br>';
		$error[] = "";
	}
	
	if (empty($jam_mulaiEdit) || empty($jam_selesaiEdit)) {
		$waktuErr = '<i class="fa fa-exclamation-triangle"></i> Jam istirahat tidak boleh kosong.<br>';
		$error[] = "";
	} else if (strtotime($jam_mulaiEdit) <= strtotime("00:00") || strtotime($jam_selesaiEdit) <= strtotime("00:00")) {
		$waktuErr = '<i class="fa fa-exclamation-triangle"></i> Jam istirahat tidak valid.<br>';
		$error[] = "";
	} else if (strtotime($jam_mulaiEdit) >= strtotime($jam_selesaiEdit)) {
		$waktuErr = '<i class="fa fa-exclamation-triangle"></i> Jam istirahat tidak valid.<br>';
		$error[] = "";
	}
	
	if (empty($jam_perhariEdit)) {
		$jam_perhariEditErr = '<i class="fa fa-exclamation-triangle"></i> Jumlah jam perhari tidak boleh kosong.<br>';
		$error[] = "";
	} else if (!preg_match("/^[0-9]*$/",$jam_perhariEdit)) {
		$jam_perhariEditErr = '<i class="fa fa-exclamation-triangle"></i> Hanya dapat menggunakan angka.<br>';
		$error[] = "";
	}
			
	if (count($error) == 0) {		
		db1_open();
		$query_update = mysql_query("UPDATE pengaturan SET menit_perjam = '$menit_perjamEdit', menit_pergantian = '$menit_pergantianEdit', jam_kuliah = '$jam_kuliahEdit', jam_mulai = '$jam_mulaiEdit', jam_selesai = '$jam_selesaiEdit', jam_perhari = '$jam_perhariEdit' WHERE id_pengaturan = '$id'") or die (mysql_error());
		db1_close();
		
		echo "<meta http-equiv='refresh' content='1; url=../pengaturan/?success=2'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal diperbaharui!";
	}
?>