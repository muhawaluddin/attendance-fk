<?php				
	$error = array();			
	$id = mysql_validasi($_POST["id"]);
	$id_jJam = mysql_validasi($_POST["id_jJam"]);
	$jam_masukEdit = mysql_validasi($_POST["jam_masuk"]);
	$jml_jamEdit = mysql_validasi($_POST["jml_jam"]);
	$kegiatanEdit = mysql_validasi($_POST["kegiatan"]);
	if (isset($_POST["duallistbox"])) {
		$dosenEdit = $_POST["duallistbox"];
		$z = count($dosenEdit);
	} else {
		$z = 0;
	}	
	$departemenEdit = mysql_validasi($_POST["departemen"]);
	$ruanganEdit = mysql_validasi($_POST["ruangan"]);
	
	if (md5($id) != $_GET['id'] || md5($id_jJam) != $_GET['edit']) {
		$error[] = "";
	}
	
	if (empty($jam_masukEdit)) {
		$jam_masukEditErr = '<i class="fa fa-exclamation-triangle"></i> Jam masuk tidak boleh kosong.<br>';
		$error[] = "";
	}
	
	if (empty($jml_jamEdit)) {
		$jml_jamEditErr = '<i class="fa fa-exclamation-triangle"></i> Jumlah jam tidak boleh kosong.<br>';
		$error[] = "";
	}
	
	if (empty($kegiatanEdit)) {
		$kegiatanEditErr = '<i class="fa fa-exclamation-triangle"></i> Kegitan tidak boleh kosong.<br>';
		$error[] = "";
	}
	
	if (empty($ruanganEdit)) {
		$ruanganEditErr = '<i class="fa fa-exclamation-triangle"></i> Ruangan harus dipilih.<br>';
		$error[] = "";
	}
			
	if (count($error) == 0) {			
		db1_open();
		$query_insert = mysql_query("UPDATE jadwal_jam SET id_departemen = '$departemenEdit', id_ruangan = '$ruanganEdit', jam_masuk = '$jam_masukEdit', jml_jam = '$jml_jamEdit', kegiatan = '$kegiatanEdit' WHERE id_jJam = '$id_jJam'") or die (mysql_error());
		
		$query_delete = mysql_query("DELETE FROM jadwal_dosen WHERE id_jJam = '$id_jJam'") or die (mysql_error());
		
		if (!empty($dosenEdit)) {
			for ($i = 0; $i < $z; $i++) {
				$query_check = mysql_query("SELECT * FROM jadwal_dosen WHERE id_jJam = '$id_jJam' AND id_dosen = '$dosenEdit[$i]'");
				if (mysql_num_rows($query_check) == 0) {
					$id_jDosen = buatKode("jadwal_dosen", "");
					$query_insert = mysql_query("INSERT INTO jadwal_dosen (id_jDosen, id_jJam, id_dosen) VALUES ('$id_jDosen', '$id_jJam', '$dosenEdit[$i]')") or die (mysql_error());
				}
			}
		}
		db1_close();
				
		echo "<meta http-equiv='refresh' content='1; url=?sub=jadwal-perkelas&id=".md5($id)."&success=1'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal ditambahkan!";
	}
?>