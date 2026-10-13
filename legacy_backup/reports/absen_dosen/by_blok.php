<?php
    db1_open();

    $id_jadwal = $_POST['blok'];
    $query_blok = mysql_query("SELECT mk.mk, mk.id_mk FROM jadwal LEFT JOIN mk ON jadwal.id_mk=mk.id_mk WHERE id_jadwal = '$id_jadwal'");
    $blok = mysql_fetch_array($query_blok);

    $id_jKelas = $_POST['kelas'];
    $query_kelas = mysql_query("SELECT kelas.kelas, kelas.id_kelas FROM jadwal_kelas LEFT JOIN kelas ON jadwal_kelas.id_kelas = kelas.id_kelas WHERE jadwal_kelas.id_jKelas = '$id_jKelas'");
	$kelas = mysql_fetch_array($query_kelas);

	// print_r($kelas['id_kelas']);

	$query_jadwal = mysql_query(
		"SELECT
			jadwal_jam.*,
			DATE_ADD(CAST(jadwal_jam.jam_masuk AS TIME), INTERVAL ((jadwal_jam.jml_jam * pengaturan.menit_perjam) + ((jadwal_jam.jml_jam - 1) * pengaturan.menit_pergantian)) MINUTE) AS jam_keluar,
			jadwal_hari.tanggal
		FROM jadwal_jam
		LEFT JOIN jadwal_hari ON jadwal_jam.id_jHari = jadwal_hari.id_jHari
		LEFT JOIN jadwal_kelas ON jadwal_hari.id_jKelas = jadwal_kelas.id_jKelas
		LEFT JOIN jadwal ON jadwal_kelas.id_jadwal = jadwal.id_jadwal
		LEFT JOIN mk ON jadwal.id_mk=mk.id_mk
		LEFT JOIN pengaturan ON jadwal.id_pengaturan = pengaturan.id_pengaturan
		WHERE jadwal_kelas.id_kelas = '$kelas[id_kelas]'
		AND jadwal.id_mk = '$blok[id_mk]'
		AND jadwal.semester = '$semester'
		AND jadwal.thn_ajaran = '$thn_ajaran'
		ORDER BY jadwal_hari.tanggal ASC, jadwal_jam.jam_masuk ASC"
	);
	$jadwal = [];
	while($row = mysql_fetch_object($query_jadwal)) {
		$dosens = [];
		$query_dosen = mysql_query("SELECT dosen.nama_dosen, dosen.id_finger FROM jadwal_dosen LEFT JOIN dosen ON jadwal_dosen.id_dosen = dosen.id_dosen WHERE jadwal_dosen.id_jJam = '$row->id_jJam'");
		while ($dosen = mysql_fetch_object($query_dosen)) {
			$absen = checkAbsen($dosen->id_finger, $row->tanggal, $row->jam_masuk, $row->jam_keluar);
			$dosens[] = [
				'nama_dosen' => $dosen->nama_dosen,
				'status' => $absen['status'],
				'checklock' => $absen['checklock']
			];
		}

		$jadwal[$row->tanggal][] = [
			'tanggal' => $row->tanggal,
			'jam_masuk' => $row->jam_masuk,
			'jam_keluar' => $row->jam_keluar,
			'kegiatan' => $row->kegiatan,
			'dosens' => $dosens
		];
	}

	function checkAbsen($dosen, $tanggal, $masuk, $keluar) {
		$query_absen = mysql_query(
			"SELECT DISTINCT(a.sn), TIME(a.scan_date) AS checklock, a.pin
			FROM db_fingerfk.att_log a
			LEFT JOIN db_absenfkuh.mesin b ON a.sn = b.serial
			WHERE a.pin = '$dosen'
			AND DATE(a.scan_date) = '$tanggal'
			AND TIME(a.scan_date) >= '$masuk'
			AND TIME(a.scan_date) <= '$keluar'
			ORDER BY a.scan_date ASC LIMIT 1"
		);
		$absen = mysql_fetch_object($query_absen);
		if (mysql_num_rows($query_absen) > 0) {
			$status = '<font color="#0101DF">H</font>';
			$checklock = substr($absen->checklock, 0, 5);
		} else {
			$status = '<font color="#FF0000">A</font>';
			$checklock = '-';
		}
		return ['status' => $status, 'checklock' => $checklock];
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
		<h4>
            Blok: <?= $blok[0] ?><br>
            Kelas: <?= $kelas[0] ?><br>
        </h4>
		<table width="100%">
			<thead>
				<tr>
					<th class="center" rowspan="2" width="1%">No.</th>
					<th nowrap class="center" rowspan="2">Hari / Tanggal</th>
					<th nowrap class="center" rowspan="2">Waktu</th>
					<th nowrap class="center" rowspan="2">Kegiatan</th>
					<th nowrap class="center" rowspan="2">Dosen</th>
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
						<td class="center"  rowspan="<?= count($value) ?>" nowrap><?= Hari($tanggal) ?>, <br> <?= TanggalIndo($tanggal) ?></td>
						<?php $jadwal = $value[0] ?>
						<td nowrap class="center"><?= $jadwal['jam_masuk'] ?> - <?= Waktu($jadwal['jam_keluar']) ?></td>
						<td><?= $jadwal['kegiatan'] ?></td>
						<td nowrap>
							<?php foreach ($jadwal['dosens'] as $dosen) {
								echo '- ' . $dosen['nama_dosen'] . '<br>';
							} ?>
						</td>
						<td class="center">
							<?php foreach ($jadwal['dosens'] as $dosen) {
								echo $dosen['status'] . '<br>';
							} ?>
						</td>
						<td class="center">
							<?php foreach ($jadwal['dosens'] as $dosen) {
								echo $dosen['checklock'] . '<br>';
							} ?>
						</td>
					</tr>
					<?php
						foreach ($value as $index => $jadwal) {
							if ($index > 0) {
					?>
					<tr>
					<td nowrap class="center"><?= $jadwal['jam_masuk'] ?> - <?= Waktu($jadwal['jam_keluar']) ?></td>
						<td><?= $jadwal['kegiatan'] ?></td>
						<td nowrap>
							<?php foreach ($jadwal['dosens'] as $dosen) {
								echo '- ' . $dosen['nama_dosen'] . '<br>';
							} ?>
						</td>
						<td class="center">
							<?php foreach ($jadwal['dosens'] as $dosen) {
								echo $dosen['status'] . '<br>';
							} ?>
						</td>
						<td class="center">
							<?php foreach ($jadwal['dosens'] as $dosen) {
								echo $dosen['checklock'] . '<br>';
							} ?>
						</td>
					</tr>
				<?php }}} ?>
			</tbody>
		</table>
	</body>
</html>

<?php
	db1_close();
?>