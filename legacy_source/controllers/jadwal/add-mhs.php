<?php			
	$error = array();	
	$id = mysql_validasi($_POST["id"]);
	$id_jKelas = mysql_validasi($_POST["id_jKelas"]);
	if (isset($_POST["id_mhs"])) {
		$id_mhs = $_POST['id_mhs'];
		$z = count($id_mhs);
	} else {
		$z = 0;
	}
	
	if (md5($id) != $_GET['id'] || $id_jKelas != $_SESSION['kelas']) {
		$error[] = "";
	}

	if (empty($id_mhs)) {
		$mhsErr = '<i class="fa fa-exclamation-triangle"></i> Pilih mahasiswa.<br>';
		$error[] = "";
	}
				
	if (count($error) == 0) {			
		db1_open();		
		for ($i = 0; $i < $z; $i++) {	
			$query_check = mysql_query("SELECT * FROM jadwal_mhs WHERE id_jKelas = '$id_jKelas' AND id_mhs = '$id_mhs[$i]'");
			if (mysql_num_rows($query_check) == 0) {
				$id_jMhs = buatKode("jadwal_mhs", "");
				$query_insert = mysql_query("INSERT INTO jadwal_mhs (id_jMhs, id_jKelas, id_mhs) VALUES('$id_jMhs', '$id_jKelas', '$id_mhs[$i]')") or die (mysql_error());
			}
		}
		db1_close();
				
		echo "<meta http-equiv='refresh' content='1; url=?sub=mahasiswa-perkelas&id=".md5($id)."&success=1'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal ditambahkan!";
	}
?>