<?php
	$error = array();
	$username = mysql_validasi($_POST["username"]);
	$password = mysql_validasi($_POST["password"]);
	
	if (empty($username)) {
		$usernameErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan username.';
		$error[] = "";
	}
	
	if (empty($password)) {
		$passwordErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan password.';
		$error[] = "";
	}
	
	if (count($error) == 0) {
		$username = mysql_real_escape_string($username);
		$password = mysql_real_escape_string($password);
								
		//CHECK USERNAME & PASSWORD USERS
		$query_login = mysql_query("SELECT * FROM users WHERE username = '$username' AND password = MD5('$password')") or die (mysql_error());
				
		if (mysql_num_rows($query_login) > 0) {	
			$data_login = mysql_fetch_array($query_login);				
			if ($data_login['status'] >= 0) {				
				//UPDATE LOGIN
				$id_user = $data_login['id_user'];
				$query_update = mysql_query("UPDATE users SET status = '1', ip_address = '$ip_address' WHERE id_user = '$id_user'");
										
				//SET SESSION &	OPEN PAGE		
				$_SESSION['log_absensi'] = $data_login['id_user'];
				$_SESSION['level_absensi'] = $data_login['level'];
				
				header("location:pages/beranda/"); 
			} else {
				$loginErr = '<i class="fa fa-exclamation-triangle"></i> Akun sedang aktif. Silahkan tunggu beberapa saat.';
				$failed = "Terjadi kesalahan. Gagal melakukan login!";
			}
		} else {
			$loginErr = '<i class="fa fa-exclamation-triangle"></i> Username atau password salah.';
			$failed = "Terjadi kesalahan. Gagal melakukan login!";
		}
	} else {
		$failed = "Terjadi kesalahan. Gagal melakukan login!";
	}
?>