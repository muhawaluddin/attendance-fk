<?php		
	$error = array();			
	$id = mysql_validasi($_POST["id"]);
	$nip = mysql_validasi($_POST["nip"]);
	
	if (md5($id) != $_GET['reset']) {
		$error[] = "";
	}
	
	if (count($error) == 0) {
		db1_open();
		$query_update = mysql_query("UPDATE users SET password = MD5('$nip') WHERE username = '$id'") or die (mysql_error());				
		db1_close();
						
		echo "<meta http-equiv='refresh' content='1; url=../dosen/?success=4'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal diperbaharui!";
	}
?>