<?php			
	if (isset($_POST["id"])) {	
		$id = array();
		$id = $_POST['id'];
		$n = count($id);
				
		for ($i = 0; $i < $n; $i++) {		
			db1_open();
			$query_delete = mysql_query("DELETE jadwal, jadwal_kelas, jadwal_mhs, jadwal_hari, jadwal_jam, jadwal_dosen FROM jadwal LEFT JOIN jadwal_kelas ON jadwal.id_jadwal = jadwal_kelas.id_jadwal LEFT JOIN jadwal_mhs ON jadwal_kelas.id_jKelas = jadwal_mhs.id_jKelas LEFT JOIN jadwal_hari ON jadwal_kelas.id_jKelas = jadwal_hari.id_jKelas LEFT JOIN jadwal_jam ON jadwal_hari.id_jHari = jadwal_jam.id_jHari LEFT JOIN jadwal_dosen ON jadwal_jam.id_jJam = jadwal_dosen.id_jJam WHERE jadwal.id_jadwal = '$id[$i]'") or die (mysql_error());
			db1_close();
		}
				
		echo "<meta http-equiv='refresh' content='1; url=../jadwal-kuliah/?success=3'>";
	}
?>