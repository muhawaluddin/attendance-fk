<?php		
	$error = array();			
	$nama= mysql_validasi($_POST["nama"]);
	$username = mysql_validasi($_POST["username"]);
	$passwordLama = mysql_validasi($_POST["passwordLama"]);
	$passwordBaru = mysql_validasi($_POST["passwordBaru"]);
	$password2Baru = mysql_validasi($_POST["password2Baru"]);
	
	if (empty($nama)) {
		$namaErr = '<i class="fa fa-exclamation-triangle"></i> Nama akun tidak boleh kosong.';
		$error[] = "";
	}
	
	if (empty($username)) {
		$usernameErr = '<i class="fa fa-exclamation-triangle"></i> Username tidak boleh kosong.<br>';
		$error[] = "";
	} else if (strlen($username) < 4) {
		$usernameErr = '<i class="fa fa-exclamation-triangle"></i> Username terlalu pendek. Minimal 5 karakter.';
		$error[] = "";
	} else {
		db1_open();
		$query_check = mysql_query("SELECT * FROM users WHERE username = '$username' AND id_user <> '$log'");
		if (mysql_num_rows($query_check) > 0) {
			$usernameErr = '<i class="fa fa-exclamation-triangle"></i> Username telah digunakan.<br>';
			$error[] = "";			
		}
		db1_close();
	}
			
	if (empty($passwordLama)) {
		$passwordLamaErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan password lama.';
		$error[] = "";
	} else {	
		db1_open();			
		$query_check = mysql_query("SELECT * FROM users WHERE id_user = '$log' AND password = '".md5($passwordLama)."'");
		if (mysql_num_rows($query_check) == 0) {
			$passwordLamaErr = '<i class="fa fa-exclamation-triangle"></i> Password lama salah.<br>';
			$error[] = "";			
		}
		db1_close();
	}
		
	if (empty($passwordBaru)) {
		$passwordBaruErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan password.';
		$error[] = "";
	} else if (strlen($passwordBaru) < 8){
		$passwordBaruErr = '<i class="fa fa-exclamation-triangle"></i> Password terlalu pendek. Minimal 8 karakter.';
		$error[] = "";
	}	
			
	if (empty($password2Baru)) {
		$password2BaruErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan konfirmasi password baru.';
		$error[] = "";
	} else if ($passwordBaru != $password2Baru){
		$password2BaruErr = '<i class="fa fa-exclamation-triangle"></i> Password baru tidak sama.';
		$error[] = "";
	}		
				
	if (count($error) == 0) {
		db1_open();
		$query_update_user = mysql_query("UPDATE users SET nama_user = '$nama', username = '$username', password = '".md5($passwordBaru)."', status = '0' WHERE id_user = '$log'") or die (mysql_error());
		db1_close();			
		session_destroy();
					
		echo "<meta http-equiv='refresh' content='0; url=../../'>";
	} else {
		$failed = "Terjadi kesalahan. Pengaturan gagal dilakukan!";
	}
?>