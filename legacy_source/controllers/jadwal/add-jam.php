<?php				
	$error = array();			
	$id = mysql_validasi($_POST["id"]);
	$id_jHari = mysql_validasi($_POST["id_jHari"]);
	$jam_masuk = mysql_validasi($_POST["jam_masuk"]);
	$jml_jam = mysql_validasi($_POST["jml_jam"]);
	$kegiatan = mysql_validasi($_POST["kegiatan"]);
	if (isset($_POST["duallistbox"])) {
		$dosen = $_POST["duallistbox"];
		$z = count($dosen);
	} else {
		$z = 0;
	}	
	$departemen = mysql_validasi($_POST["departemen"]);
	$ruangan = mysql_validasi($_POST["ruangan"]);
	
	if (md5($id) != $_GET['id'] || md5($id_jHari) != $_GET['add']) {
		$error[] = "";
	}
	
	if (empty($jam_masuk)) {
		$jam_masukErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan jam masuk.<br>';
		$error[] = "";
	}
	
	if (empty($jml_jam)) {
		$jml_jamErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan jumlah jam.<br>';
		$error[] = "";
	}
	
	if (empty($kegiatan)) {
		$kegiatanErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan kegitan.<br>';
		$error[] = "";
	}
	
	if (empty($ruangan)) {
		$ruanganErr = '<i class="fa fa-exclamation-triangle"></i> Pilih ruangan.<br>';
		$error[] = "";
	}
			
	if (count($error) == 0) {			
		db1_open();
		$id_jJam = buatKode("jadwal_jam", "");
		$query_insert = mysql_query("INSERT INTO jadwal_jam (id_jJam, id_jHari, id_departemen, id_ruangan, jam_masuk, jml_jam, kegiatan) VALUES ('$id_jJam', '$id_jHari', '$departemen', '$ruangan', '$jam_masuk', '$jml_jam', '$kegiatan')") or die (mysql_error());
		
		if (!empty($dosen)) {			
			for ($i = 0; $i < $z; $i++) {
				$query_check = mysql_query("SELECT * FROM jadwal_dosen WHERE id_jJam = '$id_jJam' AND id_dosen = '$dosen[$i]'");
				if (mysql_num_rows($query_check) == 0) {
					$id_jDosen = buatKode("jadwal_dosen", "");
					$query_insert = mysql_query("INSERT INTO jadwal_dosen (id_jDosen, id_jJam, id_dosen) VALUES ('$id_jDosen', '$id_jJam', '$dosen[$i]')") or die (mysql_error());
				}
			}
		}
		db1_close();
				
		echo "<meta http-equiv='refresh' content='1; url=?sub=jadwal-perkelas&id=".md5($id)."&success=1'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal ditambahkan!";
	}
?>