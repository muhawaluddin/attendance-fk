<?php				
	$error = array();			
	$ruangan = mysql_validasi($_POST["ruangan"]);
	if (isset($_POST['finger'])) {	
		$finger = $_POST["finger"];
	}
	$n = count($finger);
	
	if (empty($ruangan)) {
		$ruanganErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan nama ruang kuliah.<br>';
		$error[] = "";
	}
			
	if (count($error) == 0) {	
		db1_open();
		$id_ruangan = buatKode("ruangan", "");
		$query_insert = mysql_query("INSERT INTO ruangan (id_ruangan, ruangan) VALUES ('$id_ruangan', '$ruangan')") or die (mysql_error());
		
		if (isset($_POST['finger'])) {
			for ($i = 0; $i < $n; $i++) {
				$query_update = mysql_query("UPDATE mesin SET id_ruangan = '$id_ruangan' WHERE id_mesin = '$finger[$i]'") or die (mysql_error());
			}
		}
		db1_close();
		
		echo "<meta http-equiv='refresh' content='1; url=../ruang-kuliah/?success=1'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal ditambahkan!";
	}
?>