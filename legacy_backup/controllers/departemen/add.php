<?php				
	$error = array();			
	$departemen = mysql_validasi($_POST["departemen"]);
	
	if (empty($departemen)) {
		$departemenErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan nama departemen.<br>';
		$error[] = "";
	}
			
	if (count($error) == 0) {	
		db1_open();
		$id_departemen = buatKode("departemen", "");
		$query_insert = mysql_query("INSERT INTO departemen (id_departemen, departemen) VALUES ('$id_departemen', '$departemen')") or die (mysql_error());
		db1_close();
				
		echo "<meta http-equiv='refresh' content='1; url=../departemen/?success=1'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal ditambahkan!";
	}
?>