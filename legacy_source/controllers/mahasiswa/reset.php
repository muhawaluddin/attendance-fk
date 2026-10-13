<?php		
	$error = array();			
	$id = mysql_validasi($_POST["id"]);
	$nim = mysql_validasi($_POST["nim"]);
	
	if (md5($id) != $_GET['reset']) {
		$error[] = "";
	}
	
	if (count($error) == 0) {
		db1_open();
		$query_update = mysql_query("UPDATE users SET password = MD5('$nim') WHERE id_mhs = '$id'") or die (mysql_error());				
		db1_close();
						
		echo "<meta http-equiv='refresh' content='1; url=../mahasiswa/?success=4'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal diperbaharui!";
	}
?>