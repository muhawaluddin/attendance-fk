<?php		
	$error = array();			
	$id = $_POST['id'];
	$nimEdit = mysql_validasi($_POST["nim"]);
	$namaEdit = mysql_validasi($_POST["nama"]);
	$hpEdit = mysql_validasi($_POST["hp"]);
	$fingerEdit = mysql_validasi($_POST["finger"]);
	
	if (md5($id) != $_GET['edit']) {
		$error[] = "";
	}
	
	if (empty($fingerEdit)) {
		$fingertEditErr = '<i class="fa fa-exclamation-triangle"></i> ID finger tidak boleh kosong.';
		$error[] = "";
	} else {
		db1_open();
		$query_check = mysql_query("SELECT * FROM mahasiswa WHERE id_finger = '$fingerEdit' AND id_mhs <> '$id'");
		if (mysql_num_rows($query_check) > 0) {
			$fingerEditErr = '<i class="fa fa-exclamation-triangle"></i> ID Finger telah terdaftar.';
			$error[] = "";
		}
		db1_close();
	}	
		
	if (empty($nimEdit)) {
		$nimEditErr = '<i class="fa fa-exclamation-triangle"></i> NIM tidak boleh kosong.';
		$error[] = "";
	} else {
		db1_open();
		$query_check = mysql_query("SELECT * FROM mahasiswa WHERE nim = '$nimEdit' AND id_mhs <> '$id'");
		if (mysql_num_rows($query_check) > 0) {
			$nimEditErr = '<i class="fa fa-exclamation-triangle"></i> NIM telah terdaftar.';
			$error[] = "";
		}
		db1_close();
	}
	
	if (empty($namaEdit)) {
		$namaEditErr = '<i class="fa fa-exclamation-triangle"></i> Nama tidak boleh kosong.';
		$error[] = "";
	}
	
	if (!empty($hpEdit)) {
		if (!preg_match("/^[0-9]*$/",$hpEdit)){
			$hpEditErr = '<i class="fa fa-exclamation-triangle"></i> Hanya dapat menggunakan angka.';
			$error[] = "";
		} else if ((strlen($hpEdit) < 11) ||(strlen($hpEdit) > 12)){
			$hpEditErr = '<i class="fa fa-exclamation-triangle"></i> Nomor handphone tidak valid.';
			$error[] = "";
		}
	}
			
	if (count($error) == 0) {			
		db1_open();
		$query_update = mysql_query("UPDATE mahasiswa SET id_finger = '$fingerEdit', nim = '$nimEdit', nama_mhs = '$namaEdit', hp = '$hpEdit' WHERE id_mhs = '$id'") or die (mysql_error());
		
		$query_check = mysql_query("SELECT * FROM users WHERE id_mhs = '$id'");
		if (mysql_num_rows($query_check) > 0) {
			$query_update = mysql_query("UPDATE users SET nama_user = '$namaEdit', username = '$nimEdit', password = MD5('$nimEdit') WHERE id_mhs = '$id'") or die (mysql_error());
		} else {
			$id_user = buatKode("users", "");
			$query_insert = mysql_query("INSERT INTO users (id_user, id_mhs, nama_user, username, password, level) VALUES ('$id_user', '$id', '$namaEdit', '$nimEdit', MD5('$nimEdit'), 'Mahasiswa')") or die (mysql_error()); 	
		}			
		db1_close();
		
		echo "<meta http-equiv='refresh' content='1; url=../mahasiswa/?success=2'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal diperbaharui!";
	}
?>