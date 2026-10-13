<?php
	if (isset($_SESSION['log_absensi']) && isset($_SESSION['level_absensi'])) {
		$semester = $thn_ajaran = $blok = $kelas = $tanggal = $kegiatan = "";
		$disabledDosen = $disabledBlok = $disabledKelas = $disabledTanggal = $disabledKegiatan = "disabled";
		$filTgl = $filKeg = $linkTgl = $linkKeg = "";

		/* FIlter
		-----------------*/
		if (isset($_POST['semester']) && !empty($_POST['semester'])) {
			include '../../controllers/laporan/filter_dosen.php';
		}
?>

<div class="page-header">
	<h1><i class="ace-icon fa fa-file-text-o"></i> <?= $_SESSION['page'] ?></h1>
</div>

<div class="row">
	<div class="col-xs-12">
		<div class="users-profile row">
			<div class="col-sm-offset- col-sm-12">
				<div class="space"></div>
				<div id="accordion" class="accordion-style1 panel-group accordion-style2">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a aria-expanded="true" href="#filter" data-parent="#filter" data-toggle="collapse" class="accordion-toggle">
								<i class="pull-right ace-icon fa fa-chevron-down" data-icon-hide="ace-icon fa fa-chevron-down" data-icon-show="ace-icon fa fa-chevron-left"></i>

								<i class="ace-icon fa fa-filter bigger-130"></i>
								&nbsp; Filter Absen
							</a>
							</h4>
						</div>
						<?php db1_open(); ?>
						<form role="form" action="?sub=<?php echo $_GET['sub']; ?>" method="post" class="form-horizontal form-label-left">
							<div class="row" aria-expanded="true" id="filter">
								<div class="profile-user-info profile-user-info-striped">
									<div class="profile-info-row">
										<div class="profile-info-name"> Semester </div>
										<div class="profile-info-value">
											<div class="col-md-3">
												<select class="chosen-select" name="semester" onchange="this.form.submit();" data-placeholder="Pilih semester:">
													<option value=""></option>
													<?php
														$query_pilihan = mysql_query("SELECT DISTINCT(thn_ajaran), semester FROM jadwal ORDER BY semester DESC, semester DESC");
														while ($data_pilihan = mysql_fetch_array($query_pilihan)) {
															if ($semester == $data_pilihan['semester'] && $thn_ajaran == $data_pilihan['thn_ajaran']) {
																$chek = " selected";
															} else {
																$chek = "";
															}
													?>
													<option value="<?php echo $data_pilihan['semester']." ".$data_pilihan['thn_ajaran']; ?>" <?php echo $chek; ?>><?php echo $data_pilihan['semester']." ".$data_pilihan['thn_ajaran']; ?></option>
													<?php
														}
													?>
												</select>
											</div>
										</div>
									</div>
									<div class="profile-info-row">
										<div class="profile-info-name"> Dosen </div>
										<div class="profile-info-value">
											<div class="col-md-5">
												<select class="chosen-select" name="id_dosen" onchange="this.form.submit();" data-placeholder="Pilih dosen:" <?= $disabledDosen ?>>
													<option value=""></option>
													<?php
														$query_dosen = mysql_query("SELECT id_dosen, nama_dosen FROM dosen");
														while ($dosen = mysql_fetch_object($query_dosen)) {
															$selected = $dosen->id_dosen == $id_dosen ? 'selected' : '';
													?>
														<option value="<?= $dosen->id_dosen ?>" <?= $selected ?>><?= $dosen->nama_dosen ?></option>
													<?php } ?>
												</select>
											</div>
										</div>
									</div>
									<div class="profile-info-row">
										<div class="profile-info-name"> Blok </div>
										<div class="profile-info-value">
											<div class="col-md-5">
												<select class="chosen-select" name="blok" onchange="this.form.submit();" data-placeholder=" " <?php echo $disabledBlok; ?>>
													<option value=""></option>
													<?php
														$query_pilihan = mysql_query(
															"SELECT jadwal.id_jadwal, mk.mk
															FROM jadwal
															LEFT JOIN mk ON jadwal.id_mk = mk.id_mk
															WHERE jadwal.semester = '$semester'
															AND jadwal.thn_ajaran = '$thn_ajaran'
															ORDER BY mk.mk ASC"
														);
														while ($data_pilihan = mysql_fetch_array($query_pilihan)) {
															$selected = $blok == $data_pilihan['id_jadwal'] ? 'selected' : '';
													?>
														<option value="<?= $data_pilihan['id_jadwal'] ?>" <?= $selected ?>><?= $data_pilihan['mk'] ?></option>
													<?php } ?>
												</select>
											</div>
										</div>
									</div>
									<div class="profile-info-row">
										<div class="profile-info-name"> Kelas </div>
										<div class="profile-info-value">
											<div class="col-md-2">
												<select class="chosen-select" name="kelas" onchange="this.form.submit();" data-placeholder=" " <?php echo $disabledKelas; ?>>
													<option value=""></option>
													<?php
														$query_pilihan = mysql_query("SELECT jadwal_kelas.id_jKelas, kelas.kelas FROM jadwal_kelas LEFT JOIN kelas ON jadwal_kelas.id_kelas = kelas.id_kelas LEFT JOIN jadwal ON jadwal_kelas.id_jadwal = jadwal.id_jadwal WHERE jadwal.semester = '$semester' AND jadwal.thn_ajaran = '$thn_ajaran' AND jadwal_kelas.id_jadwal = '$blok' ORDER BY kelas.kelas ASC");
														while ($data_pilihan = mysql_fetch_array($query_pilihan)) {
															if ($kelas == $data_pilihan['id_jKelas']) {
																$chek = " selected";
															} else {
																$chek = "";
															}
													?>
													<option value="<?php echo $data_pilihan['id_jKelas']; ?>" <?php echo $chek; ?>><?php echo $data_pilihan['kelas']; ?></option>
													<?php
														}
													?>
												</select>
											</div>
										</div>
									</div>
								</div>
							</div>
						</form>
						<?php
							db1_close();
						?>
					</div>
				</div>

				<?php
					if ($semester && $thn_ajaran && $id_dosen) {
						$params = "";
						if (!empty($blok) && !empty($kelas)) {
							$params = "?by=blok";
						}
				?>
					<form action="../../reports/absen_dosen/<?= $params ?>" method="POST" target="_blank">
						<input type="hidden" name="semester" value=<?= $semester ?>>
						<input type="hidden" name="tahun" value=<?= $thn_ajaran ?>>
						<input type="hidden" name="dosen" value=<?= md5($id_dosen) ?>>
						<?php if (!empty($blok) && !empty($kelas)) { ?>
							<input type="hidden" name="blok" value=<?= $blok ?>>
							<input type="hidden" name="kelas" value=<?= $kelas ?>>
						<?php } ?>
						<button type="submit" class="btn btn-sm btn-inverse bigger">
							<i class="ace-icon fa fa-download bigger-110"></i> Download
						</button>
					</form>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<script>
	function show() {
		alert();
	}
</script>
<?php
	} else {
		echo "<meta http-equiv='refresh' content='0; url=../../'>";
	}
?>