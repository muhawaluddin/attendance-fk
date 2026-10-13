<?php		
	$error = array();			
	$id = mysql_validasi($_POST["id"]);
	$departemenEdit = mysql_validasi($_POST["departemen"]);
	
	if (md5($id) != $_GET['edit']) {
		$error[] = "";
	}
	
	if (empty($departemenEdit)) {
		$departemenEditErr = '<i class="fa fa-exclamation-triangle"></i> Departemen tidak boleh kosong.<br>';
		$error[] = "";
	}
			
	if (count($error) == 0) {		
		db1_open();
		$query_update = mysql_query("UPDATE departemen SET departemen = '$departemenEdit' WHERE id_departemen = '$id'") or die (mysql_error());
		db1_close();
		
		echo "<meta http-equiv='refresh' content='1; url=../departemen/?success=2'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal diperbaharui!";
	}
?>