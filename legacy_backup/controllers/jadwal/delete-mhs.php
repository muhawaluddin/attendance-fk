<?php			
	$error = array();			
	$id = mysql_validasi($_POST["id"]);
	$id_jMhs = mysql_validasi($_POST["id_jMhs"]);
	
	if (md5($id) != $_GET['id'] || md5($id_jMhs) != $_GET['delete']) {
		$error[] = "";
	}
				
	if (count($error) == 0) {	
		db1_open();
		$query_delete = mysql_query("DELETE FROM jadwal_mhs WHERE id_jMhs = '$id_jMhs'") or die (mysql_error());
		db1_close();
					
		echo "<meta http-equiv='refresh' content='1; url=?sub=mahasiswa-perkelas&id=".md5($id)."&success=3'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal dihapus!";
	}
?>