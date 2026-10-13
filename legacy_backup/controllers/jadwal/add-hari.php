<?php				
	$error = array();			
	$id = mysql_validasi($_POST["id"]);
	$tanggal = mysql_validasi($_POST["tanggal"]);
	
	if (md5($id) != $_GET['id']) {
		$error[] = "";
	}
	
	if (empty($tanggal)) {
		$tanggalErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan tanggal.<br>';
		$error[] = "";
	} else {
		db1_open();		
		$query_check = mysql_query("SELECT * FROM jadwal_hari WHERE id_jKelas = '$id_kelas' AND tanggal = '$tanggal'");
		if (mysql_num_rows($query_check) > 0) {
			$tanggalErr = '<i class="fa fa-exclamation-triangle"></i> Tanggal sudah ada.<br>';
			$error[] = "";
		}
		db1_close();		
	}
			
	if (count($error) == 0) {	
		db1_open();		
		$id_jHari = buatKode("jadwal_hari", "");
		$query_insert = mysql_query("INSERT INTO jadwal_hari (id_jHari, id_jKelas, tanggal) VALUES ('$id_jHari', '$id_kelas', '$tanggal')") or die (mysql_error());
		db1_close();
				
		echo "<meta http-equiv='refresh' content='1; url=?sub=jadwal-perkelas&id=".md5($id)."&success=1'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal ditambahkan!";
	}
?>