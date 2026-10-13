<?php			
	$error = array();			
	$id = mysql_validasi($_POST["id"]);
	
	if (md5($id) != $_GET['delete']) {
		$error[] = "";
	}
				
	if (count($error) == 0) {	
		db1_open();
		$query = mysql_query("SELECT * FROM mesin WHERE id_mesin = '$id'");
		$data = mysql_fetch_array($query);
		$sn = $data['serial'];
		$query_delete = mysql_query("DELETE FROM mesin WHERE id_mesin = '$id'") or die (mysql_error());				
		db1_close();
		
		db2_open();
		$query_delete = mysql_query("DELETE FROM device WHERE sn = '$sn'") or die (mysql_error());				
		db2_close();
					
		echo "<meta http-equiv='refresh' content='1; url=../finger-print/?success=3'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal diperbaharui!";
	}
?>