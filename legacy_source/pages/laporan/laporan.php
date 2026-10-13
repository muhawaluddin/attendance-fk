<?php	
	if (isset($_SESSION['log_absensi']) && isset($_SESSION['level_absensi'])) {
		$semester = $thn_ajaran = $blok = $kelas = $tanggal = $kegiatan = "";
		$disabledBlok = $disabledKelas = $disabledTanggal = $disabledKegiatan = "disabled";
		$filTgl = $filKeg = $linkTgl = $linkKeg = "";
		
		/* FIlter
		-----------------*/
		if (isset($_POST["semester"])) {		
			include '../../controllers/laporan/filter.php';
		}
		
		db1_open();
?>

<div class="page-header">
	<h1><i class="ace-icon fa fa-file-text-o"></i> <?php echo $_SESSION['absen']; ?></h1>
</div><!-- /.page-header -->

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
						<?php
							db1_open();
						?>				
						<form role="form" action="?sub=<?php echo $_GET['sub']; ?>" method="post" class="form-horizontal form-label-left">	
							<div class="row" aria-expanded="true" id="filter">
								<div class="profile-user-info profile-user-info-striped">
									<div class="profile-info-row">
										<div class="profile-info-name"> Semester </div>
										<div class="profile-info-value">
											<div class="col-md-3">								
												<select class="chosen-select" name="semester" onchange="this.form.submit();" data-placeholder=" ">
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
										<div class="profile-info-name"> Blok </div>
										<div class="profile-info-value">
											<div class="col-md-7">								
												<select class="chosen-select" name="blok" onchange="this.form.submit();" data-placeholder=" " <?php echo $disabledBlok; ?>>
													<option value=""></option>
													<?php
														$query_pilihan = mysql_query("SELECT jadwal.id_jadwal, mk.mk FROM jadwal LEFT JOIN mk ON jadwal.id_mk = mk.id_mk WHERE jadwal.semester = '$semester' AND jadwal.thn_ajaran = '$thn_ajaran' ORDER BY mk.mk ASC");
														while ($data_pilihan = mysql_fetch_array($query_pilihan)) {
															if ($blok == $data_pilihan['id_jadwal']) {
																$chek = " selected";
																$checker = 1;
															} else {
																$chek = ""; 
															}
													?>
													<option value="<?php echo $data_pilihan['id_jadwal']; ?>" <?php echo $chek; ?>><?php echo $data_pilihan['mk']; ?></option>
													<?php
														}
													?>
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
									<div class="profile-info-row">
										<div class="profile-info-name"> Tanggal </div>
										<div class="profile-info-value">
											<div class="col-md-3">								
												<div class="input-group">
												<?php
													$query_check = mysql_query("SELECT tanggal FROM jadwal_hari LEFT JOIN jadwal_kelas ON jadwal_hari.id_jKelas = jadwal_kelas.id_jKelas LEFT JOIN jadwal ON jadwal_kelas.id_jadwal = jadwal.id_jadwal WHERE jadwal.semester = '$semester' AND jadwal.thn_ajaran = '$thn_ajaran' AND jadwal_kelas.id_jadwal = '$blok' AND jadwal_hari.id_jKelas = '$kelas'");
													if (mysql_num_rows($query_check) == 0) {
														$tanggal = "";
														$disabledTanggal = $disabledKegiatan = "disabled";
													}
												?>
													<input class="form-control date-picker" id="id-date-picker-1" type="text" data-date-format="yyyy-mm-dd" name="tanggal" value="<?php echo $tanggal; ?>" <?php echo $disabledTanggal; ?> onchange="this.form.submit();" />
													<span class="input-group-addon">
														<i class="fa fa-calendar bigger-110"></i>
													</span>
												</div>
											</div>
										</div>
									</div>
									<div class="profile-info-row">
										<div class="profile-info-name"> Kegiatan </div>
										<div class="profile-info-value">
											<div class="col-md-8">								
												<select class="chosen-select" name="kegiatan" onchange="this.form.submit();" data-placeholder=" " <?php echo $disabledKegiatan; ?>>
													<option value=""></option>
													<?php
														$query_pilihan = mysql_query("SELECT jadwal_jam.id_jJam, jadwal_jam.kegiatan FROM jadwal_jam LEFT JOIN jadwal_hari ON jadwal_jam.id_jHari = jadwal_hari.id_jHari LEFT JOIN jadwal_kelas ON jadwal_hari.id_jKelas = jadwal_kelas.id_jKelas LEFT JOIN jadwal ON jadwal_kelas.id_jadwal = jadwal.id_jadwal WHERE jadwal.semester = '$semester' AND jadwal.thn_ajaran = '$thn_ajaran' AND jadwal_kelas.id_jadwal = '$blok' AND jadwal_hari.id_jKelas = '$kelas' AND jadwal_hari.tanggal = '$tanggal' ORDER BY jadwal_jam.jam_masuk");
														while ($data_pilihan = mysql_fetch_array($query_pilihan)) {
															if ($kegiatan == $data_pilihan['id_jJam']) {
																$chek = " selected";
															} else {
																$chek = ""; 
															}
													?>
													<option value="<?php echo $data_pilihan['id_jJam']; ?>" <?php echo $chek; ?>><?php echo $data_pilihan['kegiatan']; ?></option>
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
				<a class="btn btn-sm btn-inverse bigger" href="../../reports/absen_mahasiswa.php?jadwal=<?php echo md5($blok); ?>&kelas=<?php echo md5($kelas).$linkTgl.$linkKeg; ?>" target="_blank">
					<i class="ace-icon fa fa-download bigger-110"></i> Download
				</a>
				
			</div><!-- /.span -->
		</div><!-- /.users-profile -->
	</div><!-- /.col -->
</div><!-- /.row -->
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