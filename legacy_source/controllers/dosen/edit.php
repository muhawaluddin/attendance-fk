<?php		
	$error = array();			
	$id = $_POST['id'];
	$nipEdit = mysql_validasi($_POST["nip"]);
	$namaEdit = mysql_validasi($_POST["nama"]);
	$departemenEdit = mysql_validasi($_POST["departemen"]);
	$hpEdit = mysql_validasi($_POST["hp"]);
	$fingerEdit = mysql_validasi($_POST["finger"]);
	
	if (md5($id) != $_GET['edit']) {
		$error[] = "";
	}
	
	if (empty($fingerEdit)) {
		$fingertEditErr = '<i class="fa fa-exclamation-triangle"></i> ID finger jari tidak boleh kosong.';
		$error[] = "";
	} else {
		db1_open();
		$query_check = mysql_query("SELECT * FROM dosen WHERE id_finger = '$fingerEdit' AND id_dosen <> '$id'");
		if (mysql_num_rows($query_check) > 0) {
			$fingerEditErr = '<i class="fa fa-exclamation-triangle"></i> ID Finger telah terdaftar.';
			$error[] = "";
		}
		db1_close();
	}	
		
	if (empty($nipEdit)) {
		$nipEditErr = '<i class="fa fa-exclamation-triangle"></i> NIP tidak boleh kosong.';
		$error[] = "";
	} else {
		db1_open();
		$query_check = mysql_query("SELECT * FROM dosen WHERE nip = '$nipEdit' AND id_dosen <> '$id'");
		if (mysql_num_rows($query_check) > 0) {
			$nipEditErr = '<i class="fa fa-exclamation-triangle"></i> NIP telah terdaftar.';
			$error[] = "";
		}
		db1_close();
	}
	
	if (empty($namaEdit)) {
		$namaEditErr = '<i class="fa fa-exclamation-triangle"></i> Nama tidak boleh kosong.';
		$error[] = "";
	}
	
	if (empty($departemenEdit)) {
		$departemenEditErr = '<i class="fa fa-exclamation-triangle"></i> Departemen harus dipilih.';
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
		$query_update = mysql_query("UPDATE dosen SET id_departemen = '$departemenEdit', id_finger = '$fingerEdit', nip = '$nipEdit', nama_dosen = '$namaEdit', hp = '$hpEdit' WHERE id_dosen = '$id'") or die (mysql_error());
		
		$query_check = mysql_query("SELECT * FROM users WHERE id_dosen = '$id'");
		if (mysql_num_rows($query_check) > 0) {
			$query_update = mysql_query("UPDATE users SET nama_user = '$namaEdit', username = '$nipEdit', password = MD5('$nipEdit') WHERE id_dosen = '$id'") or die (mysql_error());
		} else {
			$id_user = buatKode("users", "");
			$query_insert = mysql_query("INSERT INTO users (id_user, id_dosen, nama_user, username, password, level) VALUES ('$id_user', '$id', '$namaEdit', '$nipEdit', MD5('$nipEdit'), 'Dosen')") or die (mysql_error()); 	
		}			
		db1_close();
		
		echo "<meta http-equiv='refresh' content='1; url=../dosen/?success=2'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal diperbaharui!";
	}
?>