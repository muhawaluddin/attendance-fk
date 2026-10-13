<?php
	db1_open();
	$id_dosen = $_POST['dosen'];
	$query_dosen = mysql_query("SELECT * FROM dosen where MD5(id_dosen)='$id_dosen'");
	$dosen = mysql_fetch_object($query_dosen);
	$query_jadwal = mysql_query(
		"SELECT
			-- jadwal_dosen.*,
			jadwal_jam.jam_masuk, jadwal_jam.jml_jam, jadwal_jam.kegiatan,
			DATE_ADD(CAST(jadwal_jam.jam_masuk AS TIME), INTERVAL ((jadwal_jam.jml_jam * pengaturan.menit_perjam) + ((jadwal_jam.jml_jam - 1) * pengaturan.menit_pergantian)) MINUTE) AS jam_keluar,
			jadwal_hari.tanggal,
			mk.*,
			kelas.*
		FROM jadwal_dosen
		LEFT JOIN jadwal_jam ON jadwal_dosen.id_jJam = jadwal_jam.id_jJam
		LEFT JOIN jadwal_hari ON jadwal_jam.id_jHari = jadwal_hari.id_jHari
		LEFT JOIN jadwal_kelas ON jadwal_hari.id_jKelas = jadwal_kelas.id_jKelas
		LEFT JOIN jadwal ON jadwal_kelas.id_jadwal = jadwal.id_jadwal
		LEFT JOIN pengaturan ON jadwal.id_pengaturan = pengaturan.id_pengaturan
		LEFT JOIN kelas ON jadwal_kelas.id_kelas = kelas.id_kelas
		LEFT JOIN mk ON jadwal.id_mk=mk.id_mk
		WHERE MD5(jadwal_dosen.id_dosen) = '$id_dosen'
		AND jadwal.semester = '$semester'
		AND jadwal.thn_ajaran = '$thn_ajaran'
		ORDER BY jadwal_hari.tanggal ASC, jadwal_jam.jam_masuk ASC, kelas.kelas ASC"
	);
	$jadwal = [];
	while($row = mysql_fetch_object($query_jadwal)) {
		$jadwal[$row->tanggal][] = [
			'jam_masuk' => $row->jam_masuk,
			'jam_keluar' => $row->jam_keluar,
			'jumlah_jam' => $row->jml_jam,
			'mk' => $row->mk,
			'kegiatan' => $row->kegiatan,
			'kelas' => $row->kelas
		];
	}
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

			td {
				padding: 5px 10px;
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
		<h3 class="center">
			LAPORAN ABSEN DOSEN SEMESETER <?= $semester . ' ' . $thn_ajaran ?><br>
			FAKULTAS KEDOKTERAN<br>
			UNIVERSITAS HASANUDDIN
		</h3>
		<h4>Dosen: <?= $dosen->nama_dosen ?></h4>
		<table width="100%">
			<thead>
				<tr>
					<th class="center" rowspan="2" width="1%">No.</th>
					<th nowrap class="center" rowspan="2">Tanggal</th>
					<th nowrap class="center" rowspan="2">Waktu</th>
					<th nowrap class="center" rowspan="2">Mata Kuliah</th>
					<th nowrap class="center" rowspan="2">Kegiatan</th>
					<th nowrap class="center" rowspan="2">Kelas</th>
					<th nowrap class="center" colspan="2">Keterangan</th>
				</tr>
				<tr>
					<th class="center">Status</th>
					<th class="center">Waktu</th>
				</tr>
			</thead>
			<tbody>
				<?php $no = 1; foreach ($jadwal as $tanggal => $value) { ?>
					<tr>
						<td class="center" rowspan="<?= count($value) ?>"><?= $no++ ?>.</td>
						<td class="center" rowspan="<?= count($value) ?>" nowrap><?= Hari($tanggal) ?>, <br> <?= TanggalIndo($tanggal) ?></td>
						<?php $jadwal = $value[0] ?>
						<td nowrap class="center"><?= $jadwal['jam_masuk'] ?> - <?= Waktu($jadwal['jam_keluar']) ?></td>
						<td nowrap><?= $jadwal['mk'] ?></td>
						<td><?= $jadwal['kegiatan'] ?></td>
						<td nowrap class="center"> <?= $jadwal['kelas'] ?></td>
						<?php
							$query_absen = mysql_query(
								"SELECT DISTINCT(a.sn), TIME(a.scan_date) AS checklock, a.pin
								FROM db_fingerfk.att_log a
								LEFT JOIN db_absenfkuh.mesin b ON a.sn = b.serial
								WHERE a.pin = '$dosen->id_finger'
								AND DATE(a.scan_date) = '$tanggal'
								AND TIME(a.scan_date) >= '".$jadwal['jam_masuk']."'
								AND TIME(a.scan_date) <= '".$jadwal['jam_keluar']."'
								ORDER BY a.scan_date ASC LIMIT 1"
							);
							$absen = mysql_fetch_object($query_absen);
							if (mysql_num_rows($query_absen) > 0) {
								$status = '<font color="#0101DF">H';
								$checklock = substr($absen->checklock, 0, 5);
							} else {
								$status = '<font color="#FF0000">A';
								$checklock = '-';
							}
						?>
						<td nowrap class="center" style="font-weight:bold"><?= $status ?></td>
						<td nowrap class="center"><?= $checklock ?></td>
					</tr>
					<?php
						foreach ($value as $index => $jadwal) {
							if ($index > 0) {
					?>
						<tr>
							<td nowrap class="center"><?= $jadwal['jam_masuk'] ?> - <?= Waktu($jadwal['jam_keluar']) ?></td>
							<td nowrap><?= $jadwal['mk'] ?></td>
							<td><?= $jadwal['kegiatan'] ?></td>
							<td nowrap class="center"> <?= $jadwal['kelas'] ?></td>
							<?php
								$query_absen = mysql_query(
									"SELECT DISTINCT(a.sn), TIME(a.scan_date) AS checklock, a.pin
									FROM db_fingerfk.att_log a
									LEFT JOIN db_absenfkuh.mesin b ON a.sn = b.serial
									WHERE a.pin = '$dosen->id_finger'
									AND DATE(a.scan_date) = '$tanggal'
									AND TIME(a.scan_date) >= '".$jadwal['jam_masuk']."'
									AND TIME(a.scan_date) <= '".$jadwal['jam_keluar']."'
									ORDER BY a.scan_date ASC LIMIT 1"
								);
								$absen = mysql_fetch_object($query_absen);
								if (mysql_num_rows($query_absen) > 0) {
									$status = '<font color="#0101DF">H';
									$checklock = substr($absen->checklock, 0, 5);
								} else {
									$status = '<font color="#FF0000">A';
									$checklock = '-';
								}
							?>
							<td nowrap class="center" style="font-weight:bold"><?= $status ?></td>
							<td nowrap class="center"><?= $checklock ?></td>
						</tr>
					<?php }} ?>
				<?php } ?>
			</tbody>
		</table>
	</body>
</html>

<?php
	db1_close();
?>