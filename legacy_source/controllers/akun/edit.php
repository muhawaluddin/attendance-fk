<?php		
	$error = array();			
	$id = mysql_validasi($_POST["id"]);
	$namaEdit = mysql_validasi($_POST["nama"]);
	$usernameEdit = mysql_validasi($_POST["username"]);
	$passwordEdit = mysql_validasi($_POST["password"]);
	$password2Edit = mysql_validasi($_POST["password2"]);	
	
	if (md5($id) != $_GET['edit']) {
		$error[] = "";
	}

	if (empty($namaEdit)) {
		$namaEditErr = '<i class="fa fa-exclamation-triangle"></i> Nama tidak boleh kosong.<br>';
		$error[] = "";
	} else if (!preg_match("/^[a-zA-Z., ]*$/",$namaEdit)){
		$namaEditErr = '<i class="fa fa-exclamation-triangle"></i> Hanya dapat menggunakan huruf, spasi, koma dan titik.<br>';
		$error[] = "";
	}
	
	if (empty($usernameEdit)) {
		$usernameEditErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan username.<br>';
		$error[] = "";
	} else if (strlen($usernameEdit) < 3) {
		$usernameEditErr = '<i class="fa fa-exclamation-triangle"></i> Username terlalu pendek. Minimal 3 karakter.';
		$error[] = "";
	} else {		
		db1_open();
		$query_check = mysql_query("SELECT * FROM users WHERE username = '$usernameEdit' AND id_user <> '$id'");
		if (mysql_num_rows($query_check) > 0) {
			$usernameEditErr = '<i class="fa fa-exclamation-triangle"></i> Username telah digunakan.<br>';
			$error[] = "";			
		}
		db1_close();
	}
	
	if (!empty($passwordEdit)) {
		if (strlen($passwordEdit) < 8) {
			$passwordEditErr = '<i class="fa fa-exclamation-triangle"></i> Password terlalu pendek. Minimal 8 karakter.';
			$error[] = "";
		}
	
		if (empty($password2Edit)) {
			$password2EditErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan konfirmasi password.';
			$error[] = "";
		} else {
			if ($passwordEdit != $password2Edit) {
				$password2EditErr = '<i class="fa fa-exclamation-triangle"></i> Password dan konfirmasi password tidak sama.';
				$error[] = "";
			}
		}
		$query_password = ", password = '".md5($passwordEdit)."'";
	} else {
		$query_password = "";
	}
			
	if (count($error) == 0) {		
		db1_open();			
		$query_update = mysql_query("UPDATE users SET nama_user = '$namaEdit', username = '$usernameEdit' $query_password WHERE id_user = '$id'") or die (mysql_error());
		db1_close();
					
		echo "<meta http-equiv='refresh' content='1; url=../akun/?success=2'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal diperbaharui!";
	}
?>
