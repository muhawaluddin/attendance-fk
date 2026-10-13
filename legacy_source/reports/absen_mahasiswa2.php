<?php session_start();	
	if (isset($_SESSION['log_absensi']) && isset($_SESSION['level_absensi'])) {
		
		include '../library/connector.php';
		include '../library/function.php';
		
		db1_open();
		$filTgl = $filKeg = "";
		
		$jadwal = $_GET['jadwal'];
		$kelas = $_GET['kelas'];
		if (isset($_GET['tanggal'])) {
			$tanggal = $_GET['tanggal'];
			$filTgl = "AND jadwal_hari.tanggal = '$tanggal'";
		}
		if (isset($_GET['kegiatan'])) {
			$kegiatan = $_GET['kegiatan'];
			$filKeg = "AND MD5(jadwal_jam.id_jJam) = '$kegiatan'";
		}
		
		$query_ket = mysql_query("SELECT semester, thn_ajaran, mk, kelas FROM jadwal LEFT JOIN mk ON jadwal.id_mk = mk.id_mk LEFT JOIN jadwal_kelas ON jadwal.id_jadwal = jadwal.id_jadwal LEFT JOIN kelas ON jadwal_kelas.id_kelas = kelas.id_kelas WHERE MD5(jadwal.id_jadwal) = '$jadwal' AND MD5(jadwal_kelas.id_jKelas) = '$kelas'");
		$data_ket = mysql_fetch_array($query_ket);
		
		$query2 = mysql_query("SELECT jadwal_jam.id_jJam, jadwal_jam.jam_masuk, DATE_ADD(CAST(jadwal_jam.jam_masuk AS TIME), INTERVAL ((jadwal_jam.jml_jam * pengaturan.menit_perjam) + ((jadwal_jam.jml_jam - 1) * pengaturan.menit_pergantian)) MINUTE) AS jam_keluar, jadwal_jam.kegiatan, jadwal_hari.tanggal FROM jadwal_jam LEFT JOIN jadwal_hari ON jadwal_jam.id_jHari = jadwal_hari.id_jHari LEFT JOIN jadwal_kelas ON jadwal_hari.id_jKelas = jadwal_kelas.id_jKelas LEFT JOIN jadwal ON jadwal_kelas.id_jadwal = jadwal.id_jadwal LEFT JOIN pengaturan ON jadwal.id_pengaturan = pengaturan.id_pengaturan WHERE MD5(jadwal.id_jadwal) = '$jadwal' AND MD5(jadwal_kelas.id_jKelas) = '$kelas' $filTgl $filKeg ORDER BY tanggal ASC");
		$jml_keg = mysql_num_rows($query2);
		while ($data2 = mysql_fetch_array($query2)) {
			$multidata[] = array("keg" => $data2['kegiatan'], "tgl" => $data2['tanggal'], "jam_msk" => $data2['jam_masuk'], "jam_klr" => substr($data2['jam_keluar'], 0, 5));
		}
		
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=Laporan_Absen_Mahasiswa_Semester_".$data_ket['semester']."_".$data_ket['thn_ajaran']."_".str_replace(" ", "_", $data_ket['mk'])."_Kelas_".$data_ket['kelas'].".xls");
			
		$html = '			
				<h3 class="center">
					LAPORAN ABSEN MAHASISWA SEMESETER '.strtoupper($data_ket['semester']).' '.$data_ket['thn_ajaran'].'<br>
					FAKULTAS KEDOKTERAN<br>
					UNIVERSITAS HASANUDDIN				
				</h3>
				<h4>
					Blok: '.$data_ket['mk'].'<br>
					Kelas: '.$data_ket['kelas'].'<br>
				</h4>
				<table>
					<thead>
						<tr>
							<th class="center" rowspan="3">No.</th>
							<th class="center" rowspan="3">NIM</th>
							<th class="center" rowspan="3" width="300px">Nama</th>
		';										
							for ($i = 0; $i < $jml_keg; $i++) {
		$html .= '
							<th class="center" colspan="2">'.Hari($multidata[$i]['tgl']).'<br>'.IndonesiaTgl($multidata[$i]['tgl']).'</th>
		';
							}
		$html .= '
						</tr>
						<tr>
		';										
							for ($i = 0; $i < $jml_keg; $i++) {
		$html .= '
							<th class="center" colspan="2">'.$multidata[$i]['jam_msk']." - ".$multidata[$i]['jam_klr'].'</th>
		';
							}
		$html .= '
						</tr>
						<tr>
		';										
							for ($i = 0; $i < $jml_keg; $i++) {
		$html .= '
							<th class="center">Status</th>
							<th class="center">Waktu</th>
		';
							}
		$html .= '
						</tr>
					</thead>

					<tbody>
		';								
						$no = 0;
						$query = mysql_query("SELECT mahasiswa.id_finger, mahasiswa.nim, mahasiswa.nama_mhs FROM jadwal_mhs LEFT JOIN mahasiswa ON jadwal_mhs.id_mhs = mahasiswa.id_mhs LEFT JOIN jadwal_kelas ON jadwal_mhs.id_jKelas = jadwal_kelas.id_jKelas LEFT JOIN jadwal ON jadwal_kelas.id_jadwal = jadwal.id_jadwal WHERE MD5(jadwal.id_jadwal) = '$jadwal' AND MD5(jadwal_kelas.id_jKelas) = '$kelas' ORDER BY mahasiswa.nim ASC") or die (mysql_error());
						while ($data = mysql_fetch_array($query)) {
							$no++;
		$html .= '
						<tr>
							<td class="center">'.$no.'</td>
							<td class="center">'.$data['nim'].'</td>
							<td class="left" nowrap>'.$data['nama_mhs'].'</td>
		';
								for ($i = 0; $i < $jml_keg; $i++) {
									$query_absen = mysql_query("SELECT DISTINCT(id_absen), TIME(waktu) AS checklock FROM absen JOIN mesin ON absen.id_mesin = mesin.id_mesin JOIN ruangan ON mesin.id_ruangan = ruangan.id_ruangan JOIN jadwal_jam ON ruangan.id_ruangan = ruangan.id_ruangan WHERE absen.id_finger = '".$data['id_finger']."' AND DATE(waktu) = '".$multidata[$i]['tgl']."' AND TIME(waktu) >= '".$multidata[$i]['jam_msk']."' AND TIME(waktu) <= '".$multidata[$i]['jam_klr']."' ORDER BY waktu ASC LIMIT 1");
									$data_absen = mysql_fetch_array($query_absen);
									if (mysql_num_rows($query_absen) > 0) {
										$status = 'H';
										$checklock = substr($data_absen['checklock'], 0, 5);
									} else {
										$status = 'A';
										$checklock = '-';
									}
		$html .= '
							<td class="center">'.$status.'</td>
							<td class="center">'.$checklock.'</td>
		';
								}
		$html .= '
						</tr>
		';
						}
		$html .= '
					</tbody>
				</table>
		';
		
		db1_close();
?>
<html>
	<head>
		<style type="text/css">
			table, h3, h4 {
				font-family: arial, sans-serif;
				border-collapse: collapse;
			}

			td, th {
				border: 1px solid #666;
				text-align: left;
				font-size: 12px;
				padding: 3;
			}

			th {
				background-color: #EDF3F4;
			}
			
			h3, h4 {
				text-transform: uppercase;	
			}

			.center {
				text-align: center;
			}

			.left {
				text-align: left;
			}

			.right {
				text-align: right;
			}
		</style>
	</head>
	<body>
		<?php echo $html; ?>
	</body>
</html>
<?php
	}
?>