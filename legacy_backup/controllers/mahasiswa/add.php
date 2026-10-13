<?php				
	$error = array();			
	$finger = mysql_validasi($_POST["finger"]);
	$nim = mysql_validasi($_POST["nim"]);
	$nama = mysql_validasi($_POST["nama"]);
	$hp = mysql_validasi($_POST["hp"]);
			
	if (empty($finger)) {
		$fingerErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan ID finger.';
		$error[] = "";
	} else {
		db1_open();
		$query_check = mysql_query("SELECT * FROM mahasiswa WHERE id_finger = '$finger'");
		if (mysql_num_rows($query_check) > 0) {
			$fingerErr = '<i class="fa fa-exclamation-triangle"></i> ID Finger telah terdaftar.';
			$error[] = "";
		}
		db1_close();
	}
	
	if (empty($nim)) {
		$nimErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan NIM.';
		$error[] = "";
	} else {
		db1_open();
		$query_check = mysql_query("SELECT * FROM mahasiswa WHERE nim = '$nim'");
		if (mysql_num_rows($query_check) > 0) {
			$nimErr = '<i class="fa fa-exclamation-triangle"></i> NIM telah terdaftar.';
			$error[] = "";
		}
		db1_close();
	}
	
	if (empty($nama)) {
		$namaErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan nama.';
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
		$id_mhs = buatKode("mahasiswa", "");
		$query_insert = mysql_query("INSERT INTO mahasiswa (id_mhs, id_finger, nim, nama_mhs, hp) VALUES ('$id_mhs', '$finger', '$nim', '$nama', '$hp')") or die (mysql_error()); 
		
		$id_user = buatKode("users", "");
		$query_insert = mysql_query("INSERT INTO users (id_user, id_mhs, nama_user, username, password, level) VALUES ('$id_user', '$id_mhs', '$nama', '$nim', MD5('$nim'), 'Mahasiswa')") or die (mysql_error()); 				
		db1_close();
		
		echo "<meta http-equiv='refresh' content='1; url=../mahasiswa/?success=1'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal ditambahkan!";
	}
?>