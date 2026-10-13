<?php				
	$error = array();			
	$nama = mysql_validasi($_POST["nama"]);
	$username = mysql_validasi($_POST["username"]);
	$password = mysql_validasi($_POST["password"]);
	$password2 = mysql_validasi($_POST["password2"]);
	
	if (empty($nama)) {
		$namaErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan nama.<br>';
		$error[] = "";
	}
	
	if (empty($username)) {
		$usernameErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan username.<br>';
		$error[] = "";
	} else {		
		db1_open();
		$query_check = mysql_query("SELECT * FROM users WHERE username = '$username'");
		if (mysql_num_rows($query_check) > 0) {
			$usernameErr = '<i class="fa fa-exclamation-triangle"></i> Username telah digunakan.<br>';
			$error[] = "";			
		}
		db1_close();
	}
	
	if (empty($password)) {
		$passwordErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan password.';
		$error[] = "";
	} else if (strlen($password) < 8) {
		$passwordErr = '<i class="fa fa-exclamation-triangle"></i> Password terlalu pendek. Minimal 8 karakter.';
		$error[] = "";
	}
	
	if (empty($password2)) {
		$password2Err = '<i class="fa fa-exclamation-triangle"></i> Masukkan konfirmasi password.';
		$error[] = "";
	} else {
		if ($password != $password2) {
			$password2Err = '<i class="fa fa-exclamation-triangle"></i> Password dan konfirmasi password tidak sama.';
			$error[] = "";
		}
	}
			
	if (count($error) == 0) {		
		db1_open();
		$id_user = buatKode("users", "");
		$query_insert = mysql_query("INSERT INTO users (id_user, nama_user, username, password, level) VALUES ('$id_user', '$nama', '$username', '".md5($password)."', 'Admin')") or die (mysql_error());
		db1_close();
				
		echo "<meta http-equiv='refresh' content='1; url=../akun/?success=1'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal ditambahkan!";
	}
?>