<?php				
	$error = array();			
	$kelas = mysql_validasi($_POST["kelas"]);
	
	if (empty($kelas)) {
		$kelasErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan nama kelas.<br>';
		$error[] = "";
	}
			
	if (count($error) == 0) {	
		db1_open();		
		$id_kelas = buatKode("kelas", "");
		$query_insert = mysql_query("INSERT INTO kelas (id_kelas, kelas) VALUES ('$id_kelas', '$kelas')") or die (mysql_error());
		db1_close();
				
		echo "<meta http-equiv='refresh' content='1; url=../kelas/?success=1'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal ditambahkan!";
	}
?>