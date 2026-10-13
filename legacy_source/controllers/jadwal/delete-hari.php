<?php			
	$error = array();			
	$id = mysql_validasi($_POST["id"]);
	$id_jHari = mysql_validasi($_POST["id_jHari"]);
	
	if (md5($id) != $_GET['id'] || md5($id_jHari) != $_GET['delete-day']) {
		$error[] = "";
	}
				
	if (count($error) == 0) {	
		db1_open();
		$query_delete = mysql_query("DELETE jadwal_hari, jadwal_jam, jadwal_dosen FROM jadwal_hari LEFT JOIN jadwal_jam ON jadwal_hari.id_jHari = jadwal_jam.id_jHari LEFT JOIN jadwal_dosen ON jadwal_jam.id_jJam = jadwal_dosen.id_jJam WHERE jadwal_hari.id_jHari = '$id_jHari'") or die (mysql_error());
		db1_close();
					
		echo "<meta http-equiv='refresh' content='1; url=?sub=jadwal-perkelas&id=".md5($id)."&success=3'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal dihapus!";
	}
?>