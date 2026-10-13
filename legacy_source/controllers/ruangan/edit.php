<?php		
	$error = array();			
	$id = mysql_validasi($_POST["id"]);
	$ruanganEdit = mysql_validasi($_POST["ruangan"]);
	if (isset($_POST['finger'])) {	
		$fingerEdit = $_POST["finger"];
	}
	$n = count($fingerEdit);
	
	if (md5($id) != $_GET['edit']) {
		$error[] = "";
	}
	
	if (empty($ruanganEdit)) {
		$ruanganEditErr = '<i class="fa fa-exclamation-triangle"></i> Nama ruang kuliah tidak boleh kosong.<br>';
		$error[] = "";
	}
			
	if (count($error) == 0) {	
		db1_open();		
		$query_update = mysql_query("UPDATE ruangan SET ruangan = '$ruanganEdit' WHERE id_ruangan = '$id'") or die (mysql_error());
		$query_update = mysql_query("UPDATE mesin SET id_ruangan = '' WHERE id_ruangan = '$id'") or die (mysql_error());
		
		if (isset($_POST['finger'])) {			
			for ($i = 0; $i < $n; $i++) {
				$query_update = mysql_query("UPDATE mesin SET id_ruangan = '$id' WHERE id_mesin = '$fingerEdit[$i]'") or die (mysql_error());
			}
		}
		db1_close();
					
		echo "<meta http-equiv='refresh' content='1; url=../ruang-kuliah/?success=2'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal diperbaharui!";
	}
?>