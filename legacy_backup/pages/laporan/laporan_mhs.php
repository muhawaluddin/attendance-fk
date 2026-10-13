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
	<h1><i class="ace-icon fa fa-file-text-o"></i> <?php echo $_SESSION['page']; ?></h1>
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
				
				<?php
					if ($semester && $thn_ajaran && $blok && $kelas) {
				?>
				<a class="btn btn-sm btn-inverse bigger" href="../../reports/absen_mahasiswa.php?jadwal=<?php echo md5($blok); ?>&kelas=<?php echo md5($kelas).$linkTgl.$linkKeg; ?>" target="_blank">
					<i class="ace-icon fa fa-download bigger-110"></i> Download
				</a>
				<!--<div class="widget-box widget-color-blue">
					<div class="widget-header">
						<h4 class="widget-title">Data Absen</h4>

						<div class="widget-toolbar">
							<a href="#" class="light-orange" data-action="collapse">
								<i class="ace-icon fa fa-chevron-up"></i>
							</a>

							<a href="#" class="light-green" data-action="fullscreen">
								<i class="ace-icon fa fa-expand"></i>
							</a>

							<a href="#" class="light-red" data-action="close">
								<i class="ace-icon fa fa-times"></i>
							</a>
						</div>
						
						<div class="widget-toolbar no-border">
							<div class="btn-group">
								<a class="btn btn-sm btn-inverse bigger" href="../../reports/absen_mahasiswa.php?jadwal=<?php echo md5($blok); ?>&kelas=<?php echo md5($kelas).$linkTgl.$linkKeg; ?>" target="_blank">
									<i class="ace-icon fa fa-download bigger-110"></i> Download
								</a>
							</div>
						</div>
					</div>

					<div class="widget-body">	
						<div class="table-responsive">
							<table class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th class="center col-md-1" rowspan="4">No.</th>
										<th class="center col-md-1" rowspan="4">NIM</th>
										<th class="center" rowspan="4">Nama</th>
									</tr>
									<?php
										/*
										db1_open();
										$query2 = mysql_query("SELECT jadwal_jam.id_jJam, jadwal_jam.jam_masuk, DATE_ADD(CAST(jadwal_jam.jam_masuk AS TIME), INTERVAL ((jadwal_jam.jml_jam * pengaturan.menit_perjam) + ((jadwal_jam.jml_jam - 1) * pengaturan.menit_pergantian)) MINUTE) AS jam_keluar, jadwal_jam.kegiatan, jadwal_hari.tanggal FROM jadwal_jam LEFT JOIN jadwal_hari ON jadwal_jam.id_jHari = jadwal_hari.id_jHari LEFT JOIN jadwal_kelas ON jadwal_hari.id_jKelas = jadwal_kelas.id_jKelas LEFT JOIN jadwal ON jadwal_kelas.id_jadwal = jadwal.id_jadwal LEFT JOIN pengaturan ON jadwal.id_pengaturan = pengaturan.id_pengaturan WHERE jadwal.id_jadwal = '$blok' AND jadwal_kelas.id_jKelas = '$kelas' $filTgl $filKeg ORDER BY tanggal ASC");
										$jml_keg = mysql_num_rows($query2);
										while ($data2 = mysql_fetch_array($query2)) {
											$multidata[] = array("keg" => $data2['kegiatan'], "tgl" => $data2['tanggal'], "jam_msk" => $data2['jam_masuk'], "jam_klr" => substr($data2['jam_keluar'], 0, 5));
										}
									?>
									<tr>
									<?php										
										for ($i = 0; $i < $jml_keg; $i++) {
									?>
										<th class="center" colspan="2"><?php echo Hari($multidata[$i]['tgl'])." ".IndonesiaTgl($multidata[$i]['tgl']); ?></th>
									<?php
										}
									?>
									</tr>
									<tr>
									<?php										
										for ($i = 0; $i < $jml_keg; $i++) {
									?>
										<th class="center" colspan="2"><?php echo $multidata[$i]['jam_msk']."-".$multidata[$i]['jam_klr']; ?></th>
									<?php
										}
									?>
									</tr>
									<tr>
									<?php										
										for ($i = 0; $i < $jml_keg; $i++) {
									?>
										<th class="center col-md-1">Status</th>
										<th class="center col-md-1">Waktu</th>
									<?php
										}
									?>
									</tr>
									<?php
										db1_close();
									?>
								</thead>

								<tbody>
								<?php
									db1_open();									
									$no = 0;
									$query = mysql_query("SELECT mahasiswa.id_finger, mahasiswa.nim, mahasiswa.nama_mhs FROM jadwal_mhs LEFT JOIN mahasiswa ON jadwal_mhs.id_mhs = mahasiswa.id_mhs LEFT JOIN jadwal_kelas ON jadwal_mhs.id_jKelas = jadwal_kelas.id_jKelas LEFT JOIN jadwal ON jadwal_kelas.id_jadwal = jadwal.id_jadwal WHERE jadwal.id_jadwal = '$blok' AND jadwal_kelas.id_jKelas = '$kelas' ORDER BY mahasiswa.nim ASC") or die (mysql_error());
									while ($data = mysql_fetch_array($query)) {
										$no++;
								?>
									<tr>
										<td class="center"><?php echo $no; ?></td>
										<td class="center"><?php echo $data['nim']; ?></td>
										<td class="nowrap"><?php echo $data['nama_mhs']; ?></td>
										<?php
											for ($i = 0; $i < $jml_keg; $i++) {
												$query_absen = mysql_query("SELECT DISTINCT(id_absen), TIME(waktu) AS checklock FROM absen JOIN mesin ON absen.id_mesin = mesin.id_mesin JOIN ruangan ON mesin.id_ruangan = ruangan.id_ruangan JOIN jadwal_jam ON ruangan.id_ruangan = ruangan.id_ruangan WHERE absen.id_finger = '".$data['id_finger']."' AND DATE(waktu) = '".$multidata[$i]['tgl']."' AND TIME(waktu) >= '".$multidata[$i]['jam_msk']."' AND TIME(waktu) <= '".$multidata[$i]['jam_klr']."' ORDER BY waktu ASC LIMIT 1");
												$data_absen = mysql_fetch_array($query_absen);
												if (mysql_num_rows($query_absen) > 0) {
													$color = 'green';
													$status = 'H';
													$checklock = substr($data_absen['checklock'], 0, 5);
												} else {
													$color = 'red';
													$status = 'A';
													$checklock = '-';
												}
										?>
										<td class="center">
											<span class="<?php echo $color; ?>"><b><?php echo $status; ?></b></span>
										</td>
										<td class="center">
											<?php echo $checklock; ?>
										</td>
										<?php
											}
										?>
									</tr>
								<?php
									}
									
									db1_close();
									*/
								?>
								</tbody>
							</table>										
						</div>
					</div>
				</div>-->			
				<?php
					}
				?>
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