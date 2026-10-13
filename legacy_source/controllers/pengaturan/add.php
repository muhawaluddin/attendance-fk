<?php				
	$error = array();			
	$menit_perjam = mysql_validasi($_POST["menit_perjam"]);
	$menit_pergantian = mysql_validasi($_POST["menit_pergantian"]);
	$jam_kuliah = mysql_validasi($_POST["jam_kuliah"]);
	$jam_mulai = mysql_validasi($_POST["jam_mulai"]);
	$jam_selesai = mysql_validasi($_POST["jam_selesai"]);
	$jam_perhari = mysql_validasi($_POST["jam_perhari"]);
	
	if (empty($menit_perjam)) {
		$menit_perjamErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan jumlah menit perjam.<br>';
		$error[] = "";
	} else if (!preg_match("/^[0-9]*$/",$menit_perjam)) {
		$menit_perjamErr = '<i class="fa fa-exclamation-triangle"></i> Hanya dapat menggunakan angka.<br>';
		$error[] = "";
	}
	
	if ($menit_pergantian < 0 || $menit_pergantian == "") {
		$menit_pergantianErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan jumlah menit pergantian.<br>';
		$error[] = "";
	} else if (!preg_match("/^[0-9]*$/",$menit_pergantian)) {
		$menit_pergantianErr = '<i class="fa fa-exclamation-triangle"></i> Hanya dapat menggunakan angka.<br>';
		$error[] = "";
	}
	
	if (empty($jam_kuliah)) {
		$jam_kuliahErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan jam masuk perkuliahan.<br>';
		$error[] = "";
	}
	
	if (empty($jam_mulai) || empty($jam_selesai)) {
		$waktuErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan jam istirahat.<br>';
		$error[] = "";
	}
	
	if (empty($jam_perhari)) {
		$jam_perhariErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan jumlah jam perhari.<br>';
		$error[] = "";
	} else if (!preg_match("/^[0-9]*$/",$jam_perhari)) {
		$jam_perhariErr = '<i class="fa fa-exclamation-triangle"></i> Hanya dapat menggunakan angka.<br>';
		$error[] = "";
	}
			
	if (count($error) == 0) {	
		db1_open();
		$id_pengaturan = buatKode("pengaturan", "");
		$query_insert = mysql_query("INSERT INTO pengaturan (id_pengaturan, menit_perjam, menit_pergantian, jam_kuliah, jam_mulai, jam_selesai, jam_perhari) VALUES ('$id_pengaturan', '$menit_perjam', '$menit_pergantian', '$jam_kuliah', '$jam_mulai', '$jam_selesai', '$jam_perhari')") or die (mysql_error());
		db1_close();
				
		echo "<meta http-equiv='refresh' content='1; url=../pengaturan/?success=1'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal ditambahkan!";
	}
?>