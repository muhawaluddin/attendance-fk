<?php		
	$error = array();			
	$jKelas = array();			
	$id = mysql_validasi($_POST["id"]);		
	$semesterEdit = mysql_validasi($_POST["semester"]);
	$thn_ajaranEdit = mysql_validasi($_POST["thn_ajaran"]);
	$tgl_mulaiEdit = mysql_validasi($_POST["tgl_mulai"]);
	$tgl_selesaiEdit = mysql_validasi($_POST["tgl_selesai"]);
	$blokEdit = mysql_validasi($_POST["blok"]);
	$koordinatorEdit = mysql_validasi($_POST["koordinator"]);
	$sekretarisEdit = mysql_validasi($_POST["sekretaris"]);
	$pengaturanEdit = mysql_validasi($_POST["pengaturan"]);
	if (isset($_POST["kelas"])) {
		$kelasEdit = $_POST["kelas"];
		$n = count($kelasEdit);
	}
	
	if (md5($id) != $_GET['edit']) {
		$error[] = "";
	}
	
	if (empty($semesterEdit)) {
		$semesterEditErr = '<i class="fa fa-exclamation-triangle"></i> Semester harus dipilih.<br>';
		$error[] = "";
	}
	
	if (empty($thn_ajaranEdit)) {
		$thn_ajaranEditErr = '<i class="fa fa-exclamation-triangle"></i> Tahun ajaran tidak boleh kosong.<br>';
		$error[] = "";
	}
	
	if (empty($tgl_mulaiEdit) || empty($tgl_selesaiEdit)) {
		$tanggalEditErr = '<i class="fa fa-exclamation-triangle"></i> Tanggal tidak boleh kosong.<br>';
		$error[] = "";
	} else if ($tgl_mulaiEdit >= $tgl_selesaiEdit) {
		$tanggalEditErr = '<i class="fa fa-exclamation-triangle"></i> Tanggal tidak valid.<br>';
		$error[] = "";
	}
	
	if (empty($blokEdit)) {
		$blokEditErr = '<i class="fa fa-exclamation-triangle"></i> Blok harus dipilih.<br>';
		$error[] = "";
	}
	
	if (empty($koordinatorEdit)) {
		$koordinatorEditErr = '<i class="fa fa-exclamation-triangle"></i> Koordinator harus dipilih.<br>';
		$error[] = "";
	}
	
	if (empty($sekretarisEdit)) {
		$sekretarisEditErr = '<i class="fa fa-exclamation-triangle"></i> Sekretaris harus dipilih.<br>';
		$error[] = "";
	}
	
	if (!empty($semesterEdit) && !empty($thn_ajaranEdit) && !empty($blokEdit)) {
		db1_open();		
		$query_check = mysql_query("SELECT * FROM jadwal WHERE semester = '$semesterEdit' AND thn_ajaran = '$thn_ajaranEdit' AND id_mk = '$blokEdit' AND id_jadwal <> '$id'") or die (mysql_error());
		if (mysql_num_rows($query_check) > 0) {
			$blokEditErr = '<i class="fa fa-exclamation-triangle"></i> Jadwal tersebut sudah ada.<br>';
			$error[] = "";
		}
		db1_close();
	}
	
	if (empty($kelasEdit)) {
		$kelasEditErr = '<i class="fa fa-exclamation-triangle"></i> Kelas harus dipilih.<br>';
		$error[] = "";
	}
			
	if (count($error) == 0) {	
		db1_open();			
		$query_update = mysql_query("UPDATE jadwal SET semester = '$semesterEdit', thn_ajaran = '$thn_ajaranEdit', id_mk = '$blokEdit', id_dosen_koordinator = '$koordinatorEdit', id_dosen_sekretaris = '$sekretarisEdit', id_pengaturan = '$pengaturanEdit' WHERE id_jadwal = '$id'") or die (mysql_error());
		
		$no_kls = 0;
		$query_kelas = mysql_query("SELECT * FROM jadwal_kelas WHERE id_jadwal = '$id' ORDER BY id_jKelas ASC");
		$jml_kls = mysql_num_rows($query_kelas);
		if ($jml_kls > 0) {
			while ($data_kelas = mysql_fetch_array($query_kelas)) {
				if ($no_kls >= $n) {
					$query_delete = mysql_query("DELETE jadwal_kelas, jadwal_mhs, jadwal_hari, jadwal_jam, jadwal_dosen FROM jadwal_kelas LEFT JOIN jadwal_mhs ON jadwal_kelas.id_jKelas = jadwal_mhs.id_jKelas LEFT JOIN jadwal_hari ON jadwal_kelas.id_jKelas = jadwal_hari.id_jKelas LEFT JOIN jadwal_jam ON jadwal_hari.id_jHari = jadwal_jam.id_jHari LEFT JOIN jadwal_dosen ON jadwal_jam.id_jJam = jadwal_dosen.id_jJam WHERE jadwal_kelas.id_jKelas = '$data_kelas[id_jKelas]'") or die (mysql_error());					
				} else if ($no_kls < $jml_kls) {
					$query_update = mysql_query("UPDATE jadwal_kelas SET id_kelas = '$kelasEdit[$no_kls]' WHERE id_jKelas = '$data_kelas[id_jKelas]'") or die (mysql_error());
					$no_kls += 1;
				}				
				$jKelas[] = $data_kelas['id_jKelas'];
			}
		}
		
		if ($no_kls < $n) {
			for ($i = $no_kls; $i < $n; $i++) {
				$id_jKelas = buatKode("jadwal_kelas", "");
				$query_insert = mysql_query("INSERT INTO jadwal_kelas (id_jKelas, id_jadwal, id_kelas) VALUES ('$id_jKelas', '$id', '$kelasEdit[$i]')") or die (mysql_error());	
				
				$jKelas[] = $id_jKelas;
			}
		}
			
		for ($i = 0; $i < $n; $i++) {
			$tgl_periode = datePeriod($tgl_mulaiEdit, $tgl_selesaiEdit);
			foreach($tgl_periode as $tgl){
				$hari = $tgl->format("w");
				$tanggal = $tgl->format("Y-m-d");
				if ($hari != 0 && $hari != 6) {				
					$query_hari = mysql_query("SELECT * FROM jadwal_hari WHERE id_jKelas = '$jKelas[$i]' AND tanggal = '$tanggal'");
					if (mysql_num_rows($query_hari) == 0) {
						$id_jHari = buatKode("jadwal_hari", "");
						$query_insert = mysql_query("INSERT INTO jadwal_hari (id_jHari, id_jKelas, tanggal) VALUES ('$id_jHari', '$jKelas[$i]', '$tanggal')") or die (mysql_error());
					}
				}
			}
		}
		db1_close();
					
		echo "<meta http-equiv='refresh' content='1; url=../jadwal-kuliah/?success=2'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal diperbaharui!";
	}
?>