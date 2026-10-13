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

		$query2 = mysql_query("SELECT jadwal_jam.id_jJam, jadwal_jam.jam_masuk, DATE_ADD(CAST(jadwal_jam.jam_masuk AS TIME), INTERVAL ((jadwal_jam.jml_jam * pengaturan.menit_perjam) + ((jadwal_jam.jml_jam - 1) * pengaturan.menit_pergantian)) MINUTE) AS jam_keluar, jadwal_jam.kegiatan, jadwal_hari.tanggal,ruangan.ruangan  FROM jadwal_jam LEFT JOIN jadwal_hari ON jadwal_jam.id_jHari = jadwal_hari.id_jHari LEFT JOIN jadwal_kelas ON jadwal_hari.id_jKelas = jadwal_kelas.id_jKelas LEFT JOIN jadwal ON jadwal_kelas.id_jadwal = jadwal.id_jadwal LEFT JOIN pengaturan ON jadwal.id_pengaturan = pengaturan.id_pengaturan LEFT JOIN ruangan ON ruangan.id_ruangan=jadwal_jam.id_ruangan WHERE MD5(jadwal.id_jadwal) = '$jadwal' AND MD5(jadwal_kelas.id_jKelas) = '$kelas' $filTgl $filKeg ORDER BY tanggal ASC");
		$jml_keg = mysql_num_rows($query2);
		while ($data2 = mysql_fetch_array($query2)) {
			$multidata[] = array("keg" => $data2['kegiatan'],"ruangan" => $data2['ruangan'], "tgl" => $data2['tanggal'], "jam_msk" => $data2['jam_masuk'], "jam_klr" => substr($data2['jam_keluar'], 0, 5));
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
							<th class="center" colspan="2">'.Hari($multidata[$i]['tgl']).', '.IndonesiaTgl($multidata[$i]['tgl']).'</th>

		';
							}
		$html .= '
						<th align="center" rowspan="2" colspan="2">Jumlah Kehadiran</th>
						<th align="center" rowspan="3">Persentasi (%)</th>
					</tr>
					<tr>
		';
							for ($i = 0; $i < $jml_keg; $i++) {
		$html .= '
							<th class="center" colspan="2">'.$multidata[$i]['keg'].'<br>'.$multidata[$i]['jam_msk']." - ".$multidata[$i]['jam_klr'].'</th>
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
							<th class="center">H</th>
							<th class="center">A</th>
		';
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
								$percent = $hadir = $alpa = 0;
								$n = 0;
								for ($i = 0; $i < $jml_keg; $i++) {
									$query_absen = mysql_query("SELECT DISTINCT(a.sn),a.pin, TIME(a.scan_date) AS checklock, c.ruangan FROM fk.att_log a LEFT JOIN db_absenfkuh.mesin b ON a.sn = b.serial LEFT
									JOIN db_absenfkuh.ruangan c ON c.id_ruangan = b.id_ruangan WHERE a.pin = '".$data['id_finger']."' AND DATE(a.scan_date) = '".$multidata[$i]['tgl']."' AND TIME(a.scan_date) >= '".$multidata[$i]['jam_msk']."' AND TIME(a.scan_date) <= '".$multidata[$i]['jam_klr']."' ORDER BY a.scan_date ASC LIMIT 1");
									$data_absen = mysql_fetch_array($query_absen);
									if (mysql_num_rows($query_absen) > 0) {
										$n++;
										$status = '<font color="#0101DF">H';
										$checklock = substr($data_absen['checklock'], 0, 5);
										$hadir++;
									} else {
										$status = '<font color="#FF0000">A';
										$checklock = '-';
										$alpa++;

									}
		$html .= '
							<td class="center"><b>'.$status.'</b></td>
							<td class="center">'.$checklock.'</td>
		';
								}
								$percent = round(($n/$jml_keg)*100,1);
							if ($percent >= 70) {
								$color = "blue";
							} else {
								$color = "#FF0000";
							}

		$html .= '
							<td class="center" width="35px"><font color="blue"><b>'.$hadir.'</b></font></td>
							<td class="center" width="35px"><font color="#FF0000"><b>'.$alpa.'</b></font></td>
							<td class="center"><font color="'.$color.'"><b>'.$percent.' %</b></font></td>
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
				border: 1px solid #444;
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