<?php				
	$error = array();			
	$mk = mysql_validasi($_POST["mk"]);
	
	if (empty($mk)) {
		$mkErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan nama mata kuliah.<br>';
		$error[] = "";
	}
			
	if (count($error) == 0) {	
		db1_open();		
		$id_mk = buatKode("mk", "");
		$query_insert = mysql_query("INSERT INTO mk (id_mk, mk) VALUES ('$id_mk', '$mk')") or die (mysql_error());
		db1_close();
				
		echo "<meta http-equiv='refresh' content='1; url=../mata-kuliah/?success=1'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal ditambahkan!";
	}
?>