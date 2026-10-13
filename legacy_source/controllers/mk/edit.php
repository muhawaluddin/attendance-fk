<?php		
	$error = array();			
	$id = mysql_validasi($_POST["id"]);
	$mkEdit = mysql_validasi($_POST["mk"]);
	
	if (md5($id) != $_GET['edit']) {
		$error[] = "";
	}
	
	if (empty($mkEdit)) {
		$mkEditErr = '<i class="fa fa-exclamation-triangle"></i> Nama mata kuliah tidak boleh kosong.<br>';
		$error[] = "";
	}
			
	if (count($error) == 0) {		
		db1_open();
		$query_update = mysql_query("UPDATE mk SET mk = '$mkEdit' WHERE id_mk = '$id'") or die (mysql_error());
		db1_close();
		
		echo "<meta http-equiv='refresh' content='1; url=../mata-kuliah/?success=2'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal diperbaharui!";
	}
?>