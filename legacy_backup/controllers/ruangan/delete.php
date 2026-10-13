<?php			
	$error = array();			
	$id = mysql_validasi($_POST["id"]);
	
	if (md5($id) != $_GET['delete']) {
		$error[] = "";
	}
	
	if (count($error) == 0) {				
		db1_open();				
		$query_delete = mysql_query("DELETE FROM ruangan WHERE id_ruangan = '$id'") or die (mysql_error());
		$query_update = mysql_query("UPDATE mesin SET id_ruangan = '' WHERE id_ruangan = '$id'") or die (mysql_error());
		db1_close();
					
		echo "<meta http-equiv='refresh' content='1; url=../ruang-kuliah/?success=3'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal diperbaharui!";
	}
?>