<?php			
	$error = array();			
	$id = mysql_validasi($_POST["id"]);
	$id_jJam = mysql_validasi($_POST["id_jJam"]);
	
	if (md5($id) != $_GET['id'] || md5($id_jJam) != $_GET['delete']) {
		$error[] = "";
	}
				
	if (count($error) == 0) {	
		db1_open();
		$query_delete = mysql_query("DELETE jadwal_jam, jadwal_dosen FROM jadwal_jam LEFT JOIN jadwal_dosen ON jadwal_jam.id_jJam = jadwal_dosen.id_jJam WHERE jadwal_jam.id_jJam = '$id_jJam'") or die (mysql_error());
		db1_close();
					
		echo "<meta http-equiv='refresh' content='1; url=?sub=jadwal-perkelas&id=".md5($id)."&success=3'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal dihapus!";
	}
?>