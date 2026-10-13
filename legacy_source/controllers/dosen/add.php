<?php				
	$error = array();			
	$finger = mysql_validasi($_POST["finger"]);
	$nip = mysql_validasi($_POST["nip"]);
	$nama = mysql_validasi($_POST["nama"]);
	$departemen = mysql_validasi($_POST["departemen"]);
	$hp = mysql_validasi($_POST["hp"]);
			
	if (empty($finger)) {
		$fingerErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan ID finger jari.';
		$error[] = "";
	} else {
		db1_open();
		$query_check = mysql_query("SELECT * FROM dosen WHERE id_finger = '$finger'");
		if (mysql_num_rows($query_check) > 0) {
			$fingerErr = '<i class="fa fa-exclamation-triangle"></i> ID Finger telah terdaftar.';
			$error[] = "";
		}
		db1_close();
	}
	
	if (empty($nip)) {
		$nipErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan NIP.';
		$error[] = "";
	} else {
		db1_open();
		$query_check = mysql_query("SELECT * FROM dosen WHERE nip = '$nip'");
		if (mysql_num_rows($query_check) > 0) {
			$nipErr = '<i class="fa fa-exclamation-triangle"></i> NIP telah terdaftar.';
			$error[] = "";
		}
		db1_close();
	}
	
	if (empty($nama)) {
		$namaErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan nama.';
		$error[] = "";
	}
	
	if (empty($departemen)) {
		$departemenErr = '<i class="fa fa-exclamation-triangle"></i> Pilih departemen.';
		$error[] = "";
	}
	
	if (!empty($hp)) {
		if (!preg_match("/^[0-9]*$/",$hp)){
			$hpErr = '<i class="fa fa-exclamation-triangle"></i> Hanya dapat menggunakan angka.';
			$error[] = "";
		} else if ((strlen($hp) < 11) ||(strlen($hp) > 12)){
			$hpErr = '<i class="fa fa-exclamation-triangle"></i> Nomor handphone tidak valid.';
			$error[] = "";
		}
	}
			
	if (count($error) == 0) {	
		db1_open();
		$id_dosen = buatKode("dosen", "");
		$query_insert = mysql_query("INSERT INTO dosen (id_dosen, id_departemen, id_finger, nip, nama_dosen, hp) VALUES ('$id_dosen', '$departemen', '$finger', '$nip', '$nama', '$hp')") or die (mysql_error()); 
		
		$id_user = buatKode("users", "");
		$query_insert = mysql_query("INSERT INTO users (id_user, id_dosen, nama_user, username, password, level) VALUES ('$id_user', '$id_dosen', '$nama', '$nip', MD5('$nip'), 'Dosen')") or die (mysql_error()); 				
		db1_close();
		
		echo "<meta http-equiv='refresh' content='1; url=../dosen/?success=1'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal ditambahkan!";
	}
?>