<?php				
	$error = array();			
	$semester = mysql_validasi($_POST["semester"]);
	$thn_ajaran = mysql_validasi($_POST["thn_ajaran"]);
	$tgl_mulai = mysql_validasi($_POST["tgl_mulai"]);
	$tgl_selesai = mysql_validasi($_POST["tgl_selesai"]);
	$blok = mysql_validasi($_POST["blok"]);
	$koordinator = mysql_validasi($_POST["koordinator"]);
	$sekretaris = mysql_validasi($_POST["sekretaris"]);
	$pengaturan = mysql_validasi($_POST["pengaturan"]);
	
	if (isset($_POST["kelas"])) {
		$kelas = $_POST["kelas"];
		$n = count($kelas);
	}	
	
	if (empty($semester)) {
		$semesterErr = '<i class="fa fa-exclamation-triangle"></i> Pilih semester.<br>';
		$error[] = "";
	}
	
	if (empty($thn_ajaran)) {
		$thn_ajaranErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan tahun ajaran.<br>';
		$error[] = "";
	}
	
	if (empty($tgl_mulai) || empty($tgl_selesai)) {
		$tanggalErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan tanggal.<br>';
		$error[] = "";
	} else if ($tgl_mulai >= $tgl_selesai) {
		$tanggalErr = '<i class="fa fa-exclamation-triangle"></i> Tanggal tidak valid.<br>';
		$error[] = "";
	}
	
	if (empty($blok)) {
		$blokErr = '<i class="fa fa-exclamation-triangle"></i> Pilih blok.<br>';
		$error[] = "";
	}
	
	if (empty($koordinator)) {
		$koordinatorErr = '<i class="fa fa-exclamation-triangle"></i> Pilih koordinator.<br>';
		$error[] = "";
	}
	
	if (empty($sekretaris)) {
		$sekretarisErr = '<i class="fa fa-exclamation-triangle"></i> Pilih sekretaris.<br>';
		$error[] = "";
	}
	
	if (!empty($semester) && !empty($thn_ajaran) && !empty($blok)) {
		db1_open();
		$query_check = mysql_query("SELECT * FROM jadwal WHERE semester = '$semester' AND thn_ajaran = '$thn_ajaran' AND id_mk = '$blok'") or die (mysql_error());
		if (mysql_num_rows($query_check) > 0) {
			$blokErr = '<i class="fa fa-exclamation-triangle"></i> Jadwal tersebut sudah ada.<br>';
			$error[] = "";
		}
		db1_close();
	}
	
	if (empty($kelas)) {
		$kelasErr = '<i class="fa fa-exclamation-triangle"></i> Pilih kelas.<br>';
		$error[] = "";
	}
	
	if (empty($pengaturan)) {
		$pengaturanErr = '<i class="fa fa-exclamation-triangle"></i> Pilih pengaturan.<br>';
		$error[] = "";
	}
			
	if (count($error) == 0) {	
		db1_open();		
		$id_jadwal = buatKode("jadwal", "");
		$query_insert = mysql_query("INSERT INTO jadwal (id_jadwal, semester, thn_ajaran, id_mk, id_dosen_koordinator, id_dosen_sekretaris, id_pengaturan) VALUES ('$id_jadwal', '$semester', '$thn_ajaran', '$blok', '$koordinator', '$sekretaris', '$pengaturan')") or die (mysql_error());
		
		for ($i = 0; $i < $n; $i++) {
			$id_jKelas = buatKode("jadwal_kelas", "");
			$query_insert = mysql_query("INSERT INTO jadwal_kelas (id_jKelas, id_jadwal, id_kelas) VALUES ('$id_jKelas', '$id_jadwal', '$kelas[$i]')") or die (mysql_error());
			
			$tgl_periode = datePeriod($tgl_mulai, $tgl_selesai);
			foreach($tgl_periode as $tgl){
				$hari = $tgl->format("w");
				$tanggal = $tgl->format("Y-m-d");
				if ($hari != 0 && $hari != 6) {				
					$query_check = mysql_query("SELECT * FROM jadwal_hari WHERE id_jKelas = '$id_jKelas' AND tanggal = '$tanggal'");
					if (mysql_num_rows($query_check) == 0) {
						$id_jHari = buatKode("jadwal_hari", "");
						$query_insert = mysql_query("INSERT INTO jadwal_hari (id_jHari, id_jKelas, tanggal) VALUES ('$id_jHari', '$id_jKelas', '$tanggal')") or die (mysql_error());
					}
				}
			}
		}
		db1_close();
				
		echo "<meta http-equiv='refresh' content='1; url=../jadwal-kuliah/?success=1'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal ditambahkan!";
	}
?>