<?php	
	if (isset($_SESSION['log_absensi']) && isset($_SESSION['level_absensi'])) {
		$semester = $semesterEdit = $semesterErr = $semesterEditErr = "";
		$thn_ajaran = $thn_ajaranEdit = $thn_ajaranErr = $thn_ajaranEditErr = "";
		$tanggal = $tgl_mulai = $tgl_selesai = $tgl_mulaiEdit = $tgl_selesaiEdit = $tanggalErr = $tanggalEditErr = "";
		$blok = $blokEdit = $blokErr = $blokEditErr = "";
		$koordinator = $koordinatorEdit = $koordinatorErr = $koordinatorEditErr = "";
		$sekretaris = $sekretarisEdit = $sekretarisErr = $sekretarisEditErr = "";
		$kelas = $kelasEdit = $kelasErr = $kelasEditErr = "";
		$pengaturan = $pengaturanEdit = $pengaturanErr = $pengaturanEditErr = "";
		$select = $selectErr = "";
		$id_kelas = "";
		$jam_masuk = $jam_masukErr = $jam_masukEdit = $jam_masukEditErr = "";
		$jml_jam = $jml_jamErr = $jml_jamEdit = $jml_jamEditErr = "";
		$kegiatan = $kegiatanErr = $kegiatanEdit = $kegiatanEditErr = "";
		$dosen = $dosenEdit = "";
		$departemen = $departemenEdit = "";
		$ruangan = $ruanganErr = $ruanganEdit = $ruanganEditErr = "";
		$id_mhs = $mhsErr = "";
		$error = "";
		
		/* Add
		-----------------*/
		if (isset($_POST["btnAdd"])) {		
			include '../../controllers/jadwal/add.php';
		}		
		
		/* Edit
		-----------------*/
		if (isset($_POST["btnEdit"])) {		
			include '../../controllers/jadwal/edit.php';
		}	
		
		/* Delete
		-----------------*/
		if (isset($_POST["btnDelete"])) {		
			include '../../controllers/jadwal/delete.php';
		}
		
		/* Delete Many
		-----------------*/
		if (isset($_POST["btnMany"])) {		
			include '../../controllers/jadwal/many.php';
		}
		
		/* Select
		-----------------*/
		if (isset($_POST["select"])) {		
			include '../../controllers/jadwal/select.php';
		}
			
		/* GET SESSION KELAS
		--------------------*/
		if (isset($_SESSION['kelas'])) {
			db1_open();
			$id_kelas = mysql_real_escape_string($_SESSION['kelas']);
			$query_kelas = mysql_query("SELECT * FROM kelas WHERE id_kelas = '$id_kelas'");
			$data_kelas = mysql_fetch_array($query_kelas);
			db1_close();
		}
		
		/* Add Jadwal Hari Perkelas
		-----------------*/
		if (isset($_POST["btnAddDay"])) {		
			include '../../controllers/jadwal/add-hari.php';
		}
		
		/* Delete Jadwal Hari Perkelas
		-----------------*/
		if (isset($_POST["btnDeleteHari"])) {		
			include '../../controllers/jadwal/delete-hari.php';
		}
		
		/* Add Jadwal Jam Perkelas
		-----------------*/
		if (isset($_POST["btnAddJam"])) {		
			include '../../controllers/jadwal/add-jam.php';
		}
		
		/* Edit Jadwal Jam Perkelas
		-----------------*/
		if (isset($_POST["btnEditJam"])) {		
			include '../../controllers/jadwal/edit-jam.php';
		}
		
		/* Delete Jadwal Jam Perkelas
		-----------------*/
		if (isset($_POST["btnDeleteJam"])) {		
			include '../../controllers/jadwal/delete-jam.php';
		}
		
		/* Add Mahasiswa Perkelas
		-----------------*/
		if (isset($_POST["btnAddMhs"])) {		
			include '../../controllers/jadwal/add-mhs.php';
		}
		
		/* Delete Mahasiswa Perkelas
		-----------------*/
		if (isset($_POST["btnDeleteMhs"])) {		
			include '../../controllers/jadwal/delete-mhs.php';
		}
		
		/* Delete Many Mahasiswa
		-----------------*/
		if (isset($_POST["btnManyMhs"])) {		
			include '../../controllers/jadwal/many-mhs.php';
		}
		
		/* GET ID
		-----------------*/
		db1_open();
		if (isset($_GET['id'])) {
			$id = mysql_real_escape_string($_GET['id']);
			if (isset($_GET['add'])) {
				$id2 = mysql_real_escape_string($_GET['add']);
			} else if (isset($_GET['edit'])) {
				$id2 = mysql_real_escape_string($_GET['edit']);
			} else if (isset($_GET['delete'])) {
				$id2 = mysql_real_escape_string($_GET['delete']);
			} else if (isset($_GET['delete-day'])) {
				$id2 = mysql_real_escape_string($_GET['delete-day']);
			}
		} else if (isset($_GET['edit'])) {
			$id = mysql_real_escape_string($_GET['edit']);
		} else if (isset($_GET['delete'])) {
			$id = mysql_real_escape_string($_GET['delete']);
		} else if (isset($_GET['reset'])) {
			$id = mysql_real_escape_string($_GET['reset']);
		}
		db1_close();
?>
<div class="page-header">
	<h1><i class="ace-icon fa fa-calendar"></i> Jadwal Kuliah</h1>
</div><!-- /.page-header -->

<div class="row">
	<?php
		if (isset($_GET['sub']) && isset($_GET['id'])) {
			db1_open();
			$query_jadwal = mysql_query("SELECT * FROM jadwal LEFT JOIN mk ON jadwal.id_mk = mk.id_mk LEFT JOIN pengaturan ON jadwal.id_pengaturan = pengaturan.id_pengaturan WHERE MD5(jadwal.id_jadwal) = '$id'");
			if (mysql_num_rows($query_jadwal) == 0) {
				//echo "<meta http-equiv='refresh' content='0; url=../jadwal-kuliah/'>";
			} else {
				$data_jadwal = mysql_fetch_array($query_jadwal);
	?>
	<div class="profile-user-info profile-user-info-striped">
		<div class="profile-info-row">
			<div class="profile-info-name"> Semester </div>
			<div class="profile-info-value"><?php echo $data_jadwal['semester']." ".$data_jadwal['thn_ajaran']; ?></div>
		</div>
		<div class="profile-info-row">
			<div class="profile-info-name"> Blok </div>
			<div class="profile-info-value"><?php echo $data_jadwal['mk']; ?></div>
		</div>
		<?php
			$query_koordinator = mysql_query("SELECT * FROM dosen WHERE id_dosen = '$data_jadwal[id_dosen_koordinator]'");
			$data_koordinator = mysql_fetch_array($query_koordinator);		
		?>
		<div class="profile-info-row">
			<div class="profile-info-name"> Koordintor </div>
			<div class="profile-info-value"><?php echo $data_koordinator['nip']." - ".$data_koordinator['nama_dosen']." (".$data_koordinator['hp'].")"; ?></div>
		</div>
		<?php
			$query_sekretaris = mysql_query("SELECT * FROM dosen WHERE id_dosen = '$data_jadwal[id_dosen_sekretaris]'");
			$data_sekretaris = mysql_fetch_array($query_sekretaris);		
		?>
		<div class="profile-info-row">
			<div class="profile-info-name"> Sekretaris </div>
			<div class="profile-info-value"><?php echo $data_sekretaris['nip']." - ".$data_sekretaris['nama_dosen']." (".$data_sekretaris['hp'].")"; ?></div>
		</div>
		<div class="profile-info-row">
			<div class="profile-info-name"> Pengaturan </div>
			<div class="profile-info-value"><?php echo "Perjam = ".$data_jadwal['menit_perjam']." Menit | Selisih = ".$data_jadwal['menit_pergantian']." Menit | Jam Istirahat = ".$data_jadwal['jam_mulai']."-".$data_jadwal['jam_selesai']; ?></div>
		</div>
		<div class="profile-info-row">
			<div class="profile-info-name"> Kelas </div>
			<div class="profile-info-value">
				<form role="form" action="?sub=<?php echo $_GET['sub']; ?>&id=<?php echo md5($data_jadwal['id_jadwal']); ?>" method="post" class="form-horizontal form-label-left">	
					<div class="col-md-2">								
						<select class="chosen-select" name="select" onchange="this.form.submit();" data-placeholder=" ">
							<option value=""></option>
							<?php
								$query_pilihan = mysql_query("SELECT * FROM jadwal_kelas LEFT JOIN kelas ON jadwal_kelas.id_kelas = kelas.id_kelas WHERE MD5(jadwal_kelas.id_jadwal) = '$id' ORDER BY kelas.id_kelas ASC");
								while ($data_pilihan = mysql_fetch_array($query_pilihan)) {
									if ($id_kelas == $data_pilihan['id_jKelas']) {
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
					<div class="col-lg-10">
						<span class="error"><?php echo $selectErr; ?></span>
					</div>
				</form>
			</div>
		</div>
	</div>
	<?php
			}
			db1_close();
		}
	?>
	<div class="col-xs-12">
		<div class="users-profile row">
			<div class="col-sm-offset- col-sm-12">
				<div class="space"></div>
				<div class="widget-box widget-color-blue">
				<?php
					db1_open();
					if (!isset($_GET['sub']) && !isset($_GET['id']) && !isset($_SESSION['kelas'])) {
				?>
				
					<div class="widget-header">
						<h4 class="widget-title">Data Jadwal Kuliah</h4>

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
								<button class="btn btn-sm btn-success bigger" data-toggle="modal" data-target="#modalAdd">
									<i class="ace-icon fa fa-plus bigger-110"></i> Tambah
								</button>
								<button class="btn btn-sm btn-danger bigger" data-toggle="modal" data-target="#modalMany">
									<i class="ace-icon fa fa-trash bigger-110"></i> Hapus
								</button>
							</div>
						</div>
					</div>

					<div class="widget-body">	
						<?php
							if (isset($query_insert) || isset($query_update) || isset($query_delete)) {
						?>
						<center>
							<br>
							<img src="../../assets/img/loading.gif" width="15%">
							<br>
							<h4><span class="red">Data Sedang Diperbaharui....</span></h4>
							<br>
						</center>
						<?php
							} else {
						?>
						<div class="table-responsive">
							<table id="dynamic-table" class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th class="center col-md-1">
											<label class="pos-rel">
												<input type="checkbox" class="ace" />
													<span class="lbl"></span>
											</label>
										</th>
										<th class="center col-md-1">No.</th>
										<th class="center">Semester</th>
										<th class="center">Blok</th>
										<th class="center">Koordinator & Sekretaris</th>
										<th class="center">Kelas</th>
										<th class="center">Pilihan</th>
									</tr>
								</thead>

								<tbody>
								<?php								
									$no = 0;
									$query = mysql_query("SELECT * FROM jadwal LEFT JOIN mk ON jadwal.id_mk = mk.id_mk ORDER BY jadwal.semester DESC, jadwal.thn_ajaran DESC, mk.mk ASC");
									while ($data = mysql_fetch_array($query)) {
										$no++;
								?>
									<tr>
										<td class="center">
											<label class="pos-rel">
												<input type="checkbox" class="ace" name="id[]" value="<?php echo $data['id_jadwal']; ?>" form="many" />
												<span class="lbl"></span>
											</label>
										</td>
										<td class="center"><?php echo $no; ?></td>
										<td class="center"><?php echo $data['semester']." ".$data['thn_ajaran']; ?></td>
										<td><?php echo $data['mk']; ?></td>
										<td>
											<ol>
											<?php
												$query_koordinator = mysql_query("SELECT * FROM dosen WHERE id_dosen = '$data[id_dosen_koordinator]'");
												$data_koordinator = mysql_fetch_array($query_koordinator);
												echo "<li>".$data_koordinator['nama_dosen']."</li>";
												$query_sekretaris = mysql_query("SELECT * FROM dosen WHERE id_dosen = '$data[id_dosen_sekretaris]'");
												$data_sekretaris = mysql_fetch_array($query_sekretaris);
												echo "<li>".$data_sekretaris['nama_dosen']."</li>";
											?>
											</ol>
										</td>
										<td>
											<?php 
												$query_kelas = mysql_query("SELECT * FROM jadwal_kelas LEFT JOIN kelas ON jadwal_kelas.id_kelas = kelas.id_kelas WHERE jadwal_kelas.id_jadwal = '".$data['id_jadwal']."'");
												while ($data_kelas = mysql_fetch_array($query_kelas)) {
													echo "[".$data_kelas['kelas']."] ";
												} 
											?>
										</td>
										<td class="center">
											<div class="hidden-sm hidden-xs action-buttons">
												<div class="btn-group">
													<a class="btn btn-xs btn-primary" href="?sub=jadwal-perkelas&id=<?php echo md5($data['id_jadwal']); ?>" data-rel="tooltip" title="Pilih" data-toggle="tooltip">
														<i class="ace-icon fa fa-check-square-o bigger-110"></i>
													</a>
													<a class="btn btn-xs btn-warning" href="?edit=<?php echo md5($data['id_jadwal']); ?>" data-rel="tooltip" title="Edit" data-toggle="tooltip">
														<i class="ace-icon fa fa-edit bigger-110"></i>
													</a>
													<a class="btn btn-xs btn-danger" href="?delete=<?php echo md5($data['id_jadwal']); ?>" data-rel="tooltip" title="Hapus" data-toggle="tooltip">
														&nbsp;<i class="ace-icon fa fa-trash-o bigger-110"></i>
													</a>
												</div>
											</div>
											<div class="hidden-md hidden-lg">
												<div class="inline pos-rel">
													<button class="btn btn-minier btn-info dropdown-toggle" data-toggle="dropdown" data-position="auto">
														<i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>
													</button>
													<ul class="dropdown-menu dropdown-only-icon dropdown-light-blue dropdown-menu-right dropdown-caret dropdown-close">
														<li>
															<a href="?sub=jadwal-perkelas&id=<?php echo md5($data['id_jadwal']); ?>" class="tooltip-dark" data-rel="tooltip" title="Pilih" data-toggle="tooltip">
																<span class="blue">
																	<i class="ace-icon fa fa-check-square-o bigger-120"></i>
																</span>
															</a>
														</li>
														<li>
															<a href="?edit=<?php echo md5($data['id_jadwal']); ?>" class="tooltip-dark" data-rel="tooltip" title="Edit" data-toggle="tooltip">
																<span class="orange">
																	<i class="ace-icon fa fa-pencil-square-o bigger-120"></i>
																</span>
															</a>
														</li>
														<li>
															<a href="?delete=<?php echo md5($data['id_jadwal']); ?>" class="tooltip-dark" data-rel="tooltip" title="Hapus" data-toggle="tooltip">
																<span class="red">
																	<i class="ace-icon fa fa-trash-o bigger-120"></i>
																</span>
															</a>
														</li>
													</ul>
												</div>
											</div>														
										</td>
									</tr>
								<?php
									}
								?>
								</tbody>
							</table>										
						</div>
						<?php
							}
						?>
					</div>
				<?php
					} else if (isset($_GET['sub']) && isset($_GET['id']) && isset($_SESSION['kelas'])) {
						if ($_SESSION['kelas']) {
							if ($_GET['sub'] == "jadwal-perkelas") {
				?>			
				
					<div class="widget-header">
						<h4 class="widget-title">Daftar Jadwal Kelas <?php echo $data_kelas['kelas']; ?></h4>

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
								<button class="btn btn-sm btn-success bigger" data-toggle="modal" data-target="#modalAddDay">
									<i class="ace-icon fa fa-plus bigger-110"></i> Tambah
								</button>
							</div>
						</div>
					</div>

					<div class="widget-body">
						<?php
							if (isset($query_insert) || isset($query_update) || isset($query_delete)) {
						?>
						<center>
							<br>
							<img src="../../assets/img/loading.gif" width="15%">
							<br>
							<h4><span class="red">Data Sedang Diperbaharui....</span></h4>
							<br>
						</center>
						<?php
							} else {
						?>					
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th class="center col-md-1">Hari / Tanggal</th>
										<th class="center">Waktu</th>
										<th class="center col-md-3">Kegiatan</th>
										<th class="center">Dosen</th>
										<th class="center">Departemen</th>
										<th class="center">Ruangan</th>
										<th class="center col-md-2" colspan="2">Pilihan</th>
									</tr>
								</thead>

								<tbody>
								<?php
									$row = 0;
									$tgl = $keg = $jam_mulai_kuliah = $jam_selesai_kuliah = "";
									$query = mysql_query("SELECT jadwal.id_jadwal, pengaturan.*, jadwal_hari.id_jHari, jadwal_hari.tanggal, jadwal_jam.id_jJam, jadwal_jam.jml_jam, jadwal_jam.jam_masuk, jadwal_jam.kegiatan, departemen.departemen, ruangan.ruangan FROM jadwal LEFT JOIN pengaturan ON jadwal.id_pengaturan = pengaturan.id_pengaturan LEFT JOIN jadwal_kelas ON jadwal.id_jadwal = jadwal_kelas.id_jadwal LEFT JOIN jadwal_hari ON jadwal_kelas.id_jKelas = jadwal_hari.id_jKelas LEFT JOIN jadwal_jam ON jadwal_hari.id_jHari = jadwal_jam.id_jHari LEFT JOIN departemen ON jadwal_jam.id_departemen = departemen.id_departemen LEFT JOIN ruangan ON jadwal_jam.id_ruangan = ruangan.id_ruangan WHERE jadwal_kelas.id_jKelas = '$id_kelas' ORDER BY jadwal_hari.tanggal ASC, jadwal_jam.jam_masuk ASC");
									while ($data = mysql_fetch_array($query)) {
										if ($data['jml_jam'] < 1) {
											$n = 1;
										} else {
											$n = $data['jml_jam'];
										}
										
										for ($i = 0; $i < $n; $i++) {	
								?>
									<tr>
									<?php
											//HANYA TAMPIL JIKA PINDAH TANGGAL
											if ($tgl != $data['id_jHari']) {
												$query_row = mysql_query("SELECT SUM(jml_jam) AS jml_row FROM jadwal_jam WHERE id_jHari = '$data[id_jHari]'");	
												$data_row = mysql_fetch_array($query_row);											
												$row = $data_row['jml_row'];
									?>
										<td class="center" rowspan="<?php echo $row; ?>">
											<?php echo Hari($data['tanggal']).", ".TanggalIndo($data['tanggal']); ?>
										</td>
									<?php
											} else {
									?>
										<td class="hidden"></td>
									<?php
											}
											if ($data['jml_jam'] > 0) {
									?>
										<td class="center">
										<?php
											if ($keg != $data['id_jJam']) {
												$jam_mulai_kuliah = $data['jam_masuk'];
											} else {
												if ($jam_selesai_kuliah == $data['jam_selesai']) {
													$jam_mulai_kuliah = $data['jam_selesai'];
												} else {
													$jam_mulai_kuliah = timeCounter($jam_selesai_kuliah, $data['menit_pergantian']);
												}
											}
											$jam_selesai_kuliah = timeCounter($jam_mulai_kuliah, $data['menit_perjam']);
											
											echo $jam_mulai_kuliah."-".$jam_selesai_kuliah;
											
											if ($jam_selesai_kuliah == $data['jam_mulai']) {
												$jam_selesai_kuliah = $data['jam_selesai'];
											}
											
										?>
										</td>									
									<?php
											} else {
									?>
										<td></td>
									<?php
											}
											//HANYA TAMPIL JIKA PINDAH KEGIATAN
											if ($keg != $data['id_jJam'] || $row == "") {
									?>
										<td rowspan="<?php echo $data['jml_jam']; ?>"><?php echo $data['kegiatan']; ?></td>
										<td rowspan="<?php echo $data['jml_jam']; ?>">
										<?php
											$query_dosen = mysql_query("SELECT * FROM jadwal_dosen LEFT JOIN dosen ON jadwal_dosen.id_dosen = dosen.id_dosen WHERE jadwal_dosen.id_jJam = '$data[id_jJam]'");
											while ($data_dosen = mysql_fetch_array($query_dosen)) {
												echo $data_dosen['nama_dosen']."<br>";
											}
										?>
										</td>
										<td class="center" rowspan="<?php echo $data['jml_jam']; ?>"><?php echo $data['departemen']; ?></td>
										<td class="center" rowspan="<?php echo $data['jml_jam']; ?>"><?php echo $data['ruangan']; ?>
										</td>
										<?php
												if ($data['jml_jam'] > 0) {
										?>
										<td class="center" rowspan="<?php echo $data['jml_jam']; ?>">
											<div class="hidden-sm hidden-xs action-buttons">
												<div class="btn-group">									
													<a class="btn btn-xs btn-warning" href="?sub=jadwal-perkelas&id=<?php echo md5($data['id_jadwal']); ?>&edit=<?php echo md5($data['id_jJam']); ?>" data-rel="tooltip" title="Edit" data-toggle="tooltip">
														<i class="ace-icon fa fa-edit bigger-110"></i>
													</a>
													<a class="btn btn-xs btn-danger" href="?sub=jadwal-perkelas&id=<?php echo md5($data['id_jadwal']); ?>&delete=<?php echo md5($data['id_jJam']); ?>" data-rel="tooltip" title="Hapus" data-toggle="tooltip">
														&nbsp;<i class="ace-icon fa fa-trash-o bigger-110"></i>
													</a>
												</div>
											</div>
											<div class="hidden-md hidden-lg">
												<div class="inline pos-rel">
													<button class="btn btn-minier btn-info dropdown-toggle" data-toggle="dropdown" data-position="auto">
														<i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>
													</button>
													<ul class="dropdown-menu dropdown-only-icon dropdown-light-blue dropdown-menu-right dropdown-caret dropdown-close">
														<li>
															<a href="?sub=jadwal-perkelas&id=<?php echo md5($data['id_jadwal']); ?>&edit=<?php echo md5($data['id_jJam']); ?>" class="tooltip-warning" data-rel="tooltip" title="Edit" data-toggle="tooltip">
																<span class="orange">
																	<i class="ace-icon fa fa-pencil-square-o bigger-120"></i>
																</span>
															</a>
														</li>
														<li>
															<a href="?sub=jadwal-perkelas&id=<?php echo md5($data['id_jadwal']); ?>&deletes=<?php echo md5($data['id_jJam']); ?>" class="tooltip-error" data-rel="tooltip" title="Hapus" data-toggle="tooltip">
																<span class="red">
																	<i class="ace-icon fa fa-trash-o bigger-120"></i>
																</span>
															</a>
														</li>
													</ul>
												</div>
											</div>														
										</td>
										<?php		
												} else {
										?>
										<td></td>
									<?php
												}
												//PINDAH KEGIATAN
												$keg = $data['id_jJam'];
											} else {
									?>
										<td class="hidden"></td>
										<td class="hidden"></td>
										<td class="hidden"></td>
										<td class="hidden"></td>
										<td class="hidden"></td>									
									<?php
											}
										
											//HANYA TAMPIL JIKA PINDAH TANGGAL
											if ($tgl != $data['id_jHari']) {						
									?>
										<td class="center" rowspan="<?php echo $row; ?>">
											<div class="hidden-sm hidden-xs action-buttons">
												<div class="btn-group btn-group-vertical">
													<a class="btn btn-xs btn-success" href="?sub=jadwal-perkelas&id=<?php echo md5($data['id_jadwal']); ?>&add=<?php echo md5($data['id_jHari']); ?>" data-rel="tooltip" title="Tambah" data-toggle="tooltip">
														&nbsp;<i class="ace-icon fa fa-plus bigger-110"></i>
													</a>	
													<a class="btn btn-xs btn-danger" href="?sub=jadwal-perkelas&id=<?php echo md5($data['id_jadwal']); ?>&delete-day=<?php echo md5($data['id_jHari']); ?>" data-rel="tooltip" title="Hapus" data-toggle="tooltip">
														&nbsp;<i class="ace-icon fa fa-trash bigger-110"></i>
													</a>
												</div>
											</div>
											<div class="hidden-md hidden-lg">
												<div class="inline pos-rel">
													<button class="btn btn-minier btn-info dropdown-toggle" data-toggle="dropdown" data-position="auto">
														<i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>
													</button>
													<ul class="dropdown-menu dropdown-only-icon dropdown-light-blue dropdown-menu-right dropdown-caret dropdown-close">
														<li>
															<a href="?sub=jadwal-perkelas&id=<?php echo md5($data['id_jadwal']); ?>&add=<?php echo md5($data['id_jHari']); ?>" class="tooltip-warning" data-rel="tooltip" title="Tambah" data-toggle="tooltip">
																<span class="green">
																	<i class="ace-icon fa fa-plus bigger-120"></i>
																</span>
															</a>
														</li>
														<li>
															<a href="?sub=jadwal-perkelas&id=<?php echo md5($data['id_jadwal']); ?>&delete-day=<?php echo md5($data['id_jHari']); ?>" class="tooltip-error" data-rel="tooltip" title="Hapus" data-toggle="tooltip">
																<span class="red">
																	<i class="ace-icon fa fa-trash bigger-120"></i>
																</span>
															</a>
														</li>
													</ul>
												</div>
											</div>														
										</td>
									<?php
												//PINDAH TANGGAL
												$tgl = $data['id_jHari'];	
											} else {
									?>
										<td class="hidden"></td>
									<?php
											}
									?>
									</tr>
								<?php
										}										
									}
								?>
								</tbody>
							</table>										
						</div>
						<?php
							}
						?>
					</div>
				<?php
							} else if ($_GET['sub'] == "mahasiswa-perkelas") {
				?>				
					<div class="widget-header">
						<h4 class="widget-title">Daftar Mahasiswa Kelas <?php echo $data_kelas['kelas']; ?></h4>

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
								<button class="btn btn-sm btn-success bigger" data-toggle="modal" data-target="#modalAddMhs">
									<i class="ace-icon fa fa-plus bigger-110"></i> Tambah
								</button>
								<button class="btn btn-sm btn-danger bigger" data-toggle="modal" data-target="#modalManyMhs">
									<i class="ace-icon fa fa-trash bigger-110"></i> Hasus
								</button>
							</div>
						</div>
					</div>

					<div class="widget-body">
						<?php
							if (isset($query_insert) || isset($query_update) || isset($query_delete)) {
						?>
						<center>
							<br>
							<img src="../../assets/img/loading.gif" width="15%">
							<br>
							<h4><span class="red">Data Sedang Diperbaharui....</span></h4>
							<br>
						</center>
						<?php
							} else {
						?>					
						<div class="table-responsive">
							<table id="dynamic-table" class="table table-bordered">
								<thead>
									<tr>
										<th class="center col-md-1" rowspan="2">
											<label class="pos-rel">
												<input type="checkbox" class="ace" />
													<span class="lbl"></span>
											</label>
										</th>
										<th class="center col-md-1" rowspan="2">No.</th>
										<th class="center" rowspan="2">NIM</th>
										<th class="center" rowspan="2">Nama</th>
										<th class="center" colspan="2">Kehadiran</th>
										<th class="center col-md-1" rowspan="2">Pilihan</th>
									</tr>
									<tr>
										<th class="center col-md-1">Masuk</th>
										<th class="center col-md-1">Tidak Masuk</th>
									</tr>
								</thead>

								<tbody>
									<?php
										$no = 0;
										$query = mysql_query("SELECT * FROM jadwal_mhs LEFT JOIN jadwal_kelas ON jadwal_mhs.id_jKelas = jadwal_kelas.id_jKelas LEFT JOIN mahasiswa ON jadwal_mhs.id_mhs = mahasiswa.id_mhs WHERE jadwal_mhs.id_jKelas = '$id_kelas'");
										while ($data = mysql_fetch_array($query)) {
											$no++;
									?>
									<tr>
										<td class="center">
											<label class="pos-rel">
												<input type="checkbox" class="ace" name="id_jMhs[]" value="<?php echo $data['id_jMhs']; ?>" form="manyMhs" />
												<span class="lbl"></span>
											</label>
										</td>
										<td class="center"><?php echo $no; ?></td>
										<td><?php echo $data['nim']; ?></td>
										<td><?php echo $data['nama_mhs']; ?></td>
										<td></td>
										<td></td>										
										<td class="center">
											<div class="hidden-sm hidden-xs action-buttons">
												<div class="btn-group">
													<a class="btn btn-xs btn-danger" href="?sub=mahasiswa-perkelas&id=<?php echo md5($data['id_jadwal']); ?>&delete=<?php echo md5($data['id_jMhs']); ?>" data-rel="tooltip" title="Hapus" data-toggle="tooltip">
														&nbsp;<i class="ace-icon fa fa-trash-o bigger-110"></i>
													</a>
												</div>
											</div>
											<div class="hidden-md hidden-lg">
												<div class="inline pos-rel">
													<button class="btn btn-minier btn-info dropdown-toggle" data-toggle="dropdown" data-position="auto">
														<i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>
													</button>
													<ul class="dropdown-menu dropdown-only-icon dropdown-light-blue dropdown-menu-right dropdown-caret dropdown-close">
														<li>
															<a href="?sub=mahasiswa-perkelas&id=<?php echo md5($data['id_jMhs']); ?>&delete=<?php echo md5($data['id_jadwal']); ?>" class="tooltip-dark" data-rel="tooltip" title="Hapus" data-toggle="tooltip">
																<span class="red">
																	<i class="ace-icon fa fa-trash-o bigger-120"></i>
																</span>
															</a>
														</li>
													</ul>
												</div>
											</div>														
										</td>
									</tr>
									<?php
										}
									?>
								</tbody>
							</table>										
						</div>
						<?php
							}
						?>
					</div>
				<?php
							}
						}
					}
					db1_close();
				?>
				</div>
			</div><!-- /.span -->
		</div><!-- /.users-profile -->
	</div><!-- /.col -->
</div><!-- /.row -->
<?php	
		db1_open();
		if (!isset($_GET['sub']) && !isset($_GET['id']) && !isset($_SESSION['kelas'])) {
?>
<!-- Modal Tambah -->
<form role="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" class="form-horizontal form-label-left">
	<div class="modal fade" id="modalAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header modal-green">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">
						<i class="fa fa-plus fa-fw"></i> Tambah Data Jadwal Kuliah
					</h4>
				</div>
				<div class="modal-body">
					<fieldset>	
						<div class="form-group">
							<label class="control-label col-md-3">Semester <span class="red">*</span> : </label>
							<div class="col-sm-2">
								<select class="chosen-select" name="semester" data-placeholder=" ">
									<?php
										$pilihan	= array("", "Awal", "Akhir");
										foreach ($pilihan as $nilai) {
											if ($semester == $nilai) {
												$chek = " selected";
											} else {
												$chek = ""; 
											}
									?>
									<option value="<?php echo $nilai; ?>" id=<?php echo $nilai; ?> <?php echo $chek; ?>><?php echo $nilai; ?></option>
									<?php
										}
									?>
								</select>
							</div>
							<div class="col-md-2">
								<input type="text" class="form-control input-mask-year" name="thn_ajaran" value="<?php echo substr($thn_ajaran, 2, 2).substr($thn_ajaran, 7, 2); ?>">
							</div>
							<div class="col-lg-12">
								<div class="row">
									<div class="col-lg-2 col-lg-offset-3">
										<span class="error"><?php echo $semesterErr; ?></span>
									</div>
									<div class="col-lg-7">
										<span class="error"><?php echo $thn_ajaranErr; ?></span>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Tanggal <span class="red">*</span> :</label>
							<div class="col-md-3">
								<div class="input-group">
									<input class="form-control date-picker" id="id-date-picker-1" type="text" data-date-format="yyyy-mm-dd" name="tgl_mulai" value="<?php echo $tgl_mulai; ?>" />
									<span class="input-group-addon">
										<i class="fa fa-calendar bigger-110"></i>
									</span>
								</div>
							</div>
							<label class="control-label col-md-1 center">S/D</label>
							<div class="col-md-3">
								<div class="input-group">
									<input class="form-control date-picker" id="id-date-picker-1" type="text" data-date-format="yyyy-mm-dd" name="tgl_selesai" value="<?php echo $tgl_selesai; ?>" />
									<span class="input-group-addon">
										<i class="fa fa-calendar bigger-110"></i>
									</span>
								</div>
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $tanggalErr; ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Blok <span class="red">*</span> :</label>
							<div class="col-md-7">
								<select class="chosen-select" name="blok" data-placeholder=" ">
									<option value=""></option>
								<?php
									$query_pilihan = mysql_query("SELECT * FROM mk ORDER BY mk ASC");
									while ($data_pilihan = mysql_fetch_array($query_pilihan)) {
										if ($blok == $data_pilihan['id_mk']) {
											$chek = " selected";
										} else {
											$chek = ""; 
										}
								?>
									<option value="<?php echo $data_pilihan['id_mk']; ?>" <?php echo $chek; ?>><?php echo $data_pilihan['mk']; ?></option>
								<?php
									}
								?>
								</select>
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $blokErr; ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Koordinator <span class="red">*</span> :</label>
							<div class="col-md-7">
								<select class="chosen-select" name="koordinator" data-placeholder=" ">
									<option value=""></option>
								<?php
									$query_pilihan = mysql_query("SELECT * FROM dosen ORDER BY nama_dosen ASC");
									while ($data_pilihan = mysql_fetch_array($query_pilihan)) {
										if ($koordinator == $data_pilihan['id_dosen']) {
											$chek = " selected";
										} else {
											$chek = ""; 
										}
								?>
									<option value="<?php echo $data_pilihan['id_dosen']; ?>" <?php echo $chek; ?>><?php echo $data_pilihan['nip']." - ".$data_pilihan['nama_dosen']; ?></option>
								<?php
									}
								?>
								</select>
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $koordinatorErr; ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Sekretaris <span class="red">*</span> :</label>
							<div class="col-md-7">
								<select class="chosen-select" name="sekretaris" data-placeholder=" ">
									<option value=""></option>
								<?php
									$query_pilihan = mysql_query("SELECT * FROM dosen ORDER BY nama_dosen ASC");
									while ($data_pilihan = mysql_fetch_array($query_pilihan)) {
										if ($sekretaris == $data_pilihan['id_dosen']) {
											$chek = " selected";
										} else {
											$chek = ""; 
										}
								?>
									<option value="<?php echo $data_pilihan['id_dosen']; ?>" <?php echo $chek; ?>><?php echo $data_pilihan['nip']." - ".$data_pilihan['nama_dosen']; ?></option>
								<?php
									}
								?>
								</select>
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $sekretarisErr; ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Kelas <span class="red">*</span> :</label>
							<div class="col-md-7">
								<select multiple="" class="chosen-select form-control tag-input-style" id="form-field-select-4" name="kelas[]" data-placeholder=" ">
									<?php		
										$query_pilihan = mysql_query("SELECT * FROM kelas ORDER BY kelas ASC");
										while ($data_pilihan = mysql_fetch_array($query_pilihan)) {
									?>
										<option value="<?php echo $data_pilihan['id_kelas']; ?>"><?php echo $data_pilihan['kelas']; ?></option>
									<?php
										}
									?>
								</select>
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $kelasErr; ?></span>
							</div>
						</div>
						
						<div class="form-group">
							<label class="control-label col-md-3">Pengaturan <span class="red">*</span> :</label>
							<div class="col-md-7">
								<select class="chosen-select" name="pengaturan" data-placeholder=" ">
									<option value=""></option>
								<?php
									$query_pilihan = mysql_query("SELECT * FROM pengaturan ORDER BY id_pengaturan DESC");
									while ($data_pilihan = mysql_fetch_array($query_pilihan)) {
										if ($pengaturan == $data_pilihan['id_pengaturan']) {
											$chek = " selected";
										} else {
											$chek = ""; 
										}
								?>
									<option value="<?php echo $data_pilihan['id_pengaturan']; ?>" <?php echo $chek; ?>><?php echo "Perjam = ".$data_pilihan['menit_perjam']." Menit | Selisih = ".$data_pilihan['menit_pergantian']." Menit | Jam Istirahat = ".$data_pilihan['jam_mulai']."-".$data_pilihan['jam_selesai']; ?></option>
								<?php
									}
								?>
								</select>
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $pengaturanErr; ?></span>
							</div>
						</div>
					</fieldset>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success btn-sm" name="btnAdd">
						<i class="fa fa-save fa-fw"></i> Simpan
					</button>
					<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">
						<i class="fa fa-times fa-fw"></i> Batal
					</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
</form>

<!-- Modal Many -->
<form role="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" class="form-horizontal form-label-left" id="many">
	<div class="modal fade" id="modalMany" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-red">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">
						<i class="fa fa-trash fa-fw"></i> Hapus Data Jadwal Kuliah
					</h4>
				</div>
				<div class="modal-body">
					<fieldset>							
						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<p><b>Apakah anda yakin ingin menghapus data-data tersebut?</b></p>
							</div>				
						</div>		
					</fieldset>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success btn-sm" name="btnMany">
						<i class="fa fa-check fa-fw"></i> Ya
					</button>
					<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">
						<i class="fa fa-times fa-fw"></i> Batal
					</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
</form>

<?php		
			if (!isset($_POST["id"]) || $failed) {
				if (isset($_GET['edit'])) {
					$query = mysql_query("SELECT * FROM jadwal WHERE MD5(id_jadwal) ='$id'");
					if (mysql_num_rows($query) > 0) {
						$data = mysql_fetch_array($query);
						$query_tgl = mysql_query("SELECT MIN(tanggal) AS tgl_mulai, MAX(tanggal) AS tgl_selesai FROM jadwal_hari LEFT JOIN jadwal_kelas ON jadwal_hari.id_jKelas = jadwal_kelas.id_jKelas WHERE MD5(jadwal_kelas.id_jadwal) = '$id'");
						$data_tgl = mysql_fetch_array($query_tgl);
?>
<!-- Modal Edit -->
<form role="form" action="?edit=<?php echo md5($data['id_jadwal']); ?>" method="post" class="form-horizontal form-label-left">
	<div class="modal fade in" style="display: block; padding-right: 17px;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
		<div style="height: 1000px;" class="modal-backdrop fade in"></div>
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header modal-orange">
					<a href="../jadwal-kuliah/" class="close">&times;</a>
					<h4 class="modal-title" id="myModalLabel">
						<i class="fa fa-edit fa-fw"></i> Edit Data Jadwal Kuliah
					</h4>
				</div>
				<div class="modal-body">
					<fieldset>	
						<input type="text" name="id" value="<?php echo $data['id_jadwal']; ?>" hidden>
						<div class="form-group">
							<label class="control-label col-md-3">Semester <span class="red">*</span> : </label>
							<div class="col-sm-2">
								<select class="chosen-select" name="semester" data-placeholder=" ">
									<?php
										$pilihan	= array("", "Awal", "Akhir");
										foreach ($pilihan as $nilai) {
											if ($data['semester'] == $nilai) {
												$chek = " selected";
											} else {
												$chek = ""; 
											}
									?>
									<option value="<?php echo $nilai; ?>" id=<?php echo $nilai; ?> <?php echo $chek; ?>><?php echo $nilai; ?></option>
									<?php
										}
									?>
								</select>
							</div>
							<div class="col-md-2">
								<input type="text" class="form-control input-mask-year" name="thn_ajaran" value="<?php echo $data['thn_ajaran']; ?>">
							</div>
							<div class="col-lg-12">
								<div class="row">
									<div class="col-lg-2 col-lg-offset-3">
										<span class="error"><?php echo $semesterEditErr; ?></span>
									</div>
									<div class="col-lg-7">
										<span class="error"><?php echo $thn_ajaranEditErr; ?></span>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Tanggal <span class="red">*</span> :</label>
							<div class="col-md-3">
								<div class="input-group">
									<input class="form-control date-picker" id="id-date-picker-1" type="text" data-date-format="yyyy-mm-dd" name="tgl_mulai" value="<?php echo $data_tgl['tgl_mulai']; ?>" />
									<span class="input-group-addon">
										<i class="fa fa-calendar bigger-110"></i>
									</span>
								</div>
							</div>
							<label class="control-label col-md-1 center">S/D</label>
							<div class="col-md-3">
								<div class="input-group">
									<input class="form-control date-picker" id="id-date-picker-1" type="text" data-date-format="yyyy-mm-dd" name="tgl_selesai" value="<?php echo $data_tgl['tgl_selesai']; ?>" />
									<span class="input-group-addon">
										<i class="fa fa-calendar bigger-110"></i>
									</span>
								</div>
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $tanggalEditErr; ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Blok <span class="red">*</span> :</label>
							<div class="col-md-7">
								<select class="chosen-select" name="blok" data-placeholder=" ">
									<option value=""></option>
								<?php
									$query_pilihan = mysql_query("SELECT * FROM mk ORDER BY mk ASC");
									while ($data_pilihan = mysql_fetch_array($query_pilihan)) {
										if ($data['id_mk'] == $data_pilihan['id_mk']) {
											$chek = " selected";
										} else {
											$chek = ""; 
										}
								?>
									<option value="<?php echo $data_pilihan['id_mk']; ?>" <?php echo $chek; ?>><?php echo $data_pilihan['mk']; ?></option>
								<?php
									}
								?>
								</select>
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $blokEditErr; ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Koordinator <span class="red">*</span> :</label>
							<div class="col-md-7">
								<select class="chosen-select" name="koordinator" data-placeholder=" ">
									<option value=""></option>
								<?php
									$query_pilihan = mysql_query("SELECT * FROM dosen ORDER BY nama_dosen ASC");
									while ($data_pilihan = mysql_fetch_array($query_pilihan)) {
										if ($data['id_dosen_koordinator'] == $data_pilihan['id_dosen']) {
											$chek = " selected";
										} else {
											$chek = ""; 
										}
								?>
									<option value="<?php echo $data_pilihan['id_dosen']; ?>" <?php echo $chek; ?>><?php echo $data_pilihan['nip']." - ".$data_pilihan['nama_dosen']; ?></option>
								<?php
									}
								?>
								</select>
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $koordinatorEditErr; ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Sekretaris <span class="red">*</span> :</label>
							<div class="col-md-7">
								<select class="chosen-select" name="sekretaris" data-placeholder=" ">
									<option value=""></option>
								<?php
									$query_pilihan = mysql_query("SELECT * FROM dosen ORDER BY nama_dosen ASC");
									while ($data_pilihan = mysql_fetch_array($query_pilihan)) {
										if ($data['id_dosen_sekretaris'] == $data_pilihan['id_dosen']) {
											$chek = " selected";
										} else {
											$chek = ""; 
										}
								?>
									<option value="<?php echo $data_pilihan['id_dosen']; ?>" <?php echo $chek; ?>><?php echo $data_pilihan['nip']." - ".$data_pilihan['nama_dosen']; ?></option>
								<?php
									}
								?>
								</select>
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $sekretarisEditErr; ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Kelas <span class="red">*</span> :</label>
							<div class="col-md-7">
								<select multiple="" class="chosen-select form-control tag-input-style" id="form-field-select-4" name="kelas[]" data-placeholder=" ">
									<?php		
										$query_pilihan = mysql_query("SELECT * FROM kelas ORDER BY kelas ASC");
										while ($data_pilihan = mysql_fetch_array($query_pilihan)) {
											$query_kelas = mysql_query("SELECT * FROM jadwal_kelas WHERE id_jadwal = '$data[id_jadwal]' AND id_kelas = '$data_pilihan[id_kelas]'");
											if (mysql_num_rows($query_kelas) > 0) {
												$chek = " selected";
											} else {
												$chek = ""; 
											}
									?>
										<option value="<?php echo $data_pilihan['id_kelas']; ?>" <?php echo $chek; ?>><?php echo $data_pilihan['kelas']; ?></option>
									<?php
										}
									?>
								</select>
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $kelasEditErr; ?></span>
							</div>
						</div>
						
						<div class="form-group">
							<label class="control-label col-md-3">Pengaturan <span class="red">*</span> :</label>
							<div class="col-md-7">
								<select class="chosen-select" name="pengaturan" data-placeholder=" ">
									<option value=""></option>
								<?php
									$query_pilihan = mysql_query("SELECT * FROM pengaturan ORDER BY id_pengaturan DESC");
									while ($data_pilihan = mysql_fetch_array($query_pilihan)) {
										if ($data['id_pengaturan'] == $data_pilihan['id_pengaturan']) {
											$chek = " selected";
										} else {
											$chek = ""; 
										}
								?>
									<option value="<?php echo $data_pilihan['id_pengaturan']; ?>" <?php echo $chek; ?>><?php echo "Perjam = ".$data_pilihan['menit_perjam']." Menit | Selisih = ".$data_pilihan['menit_pergantian']." Menit | Jam Istirahat = ".$data_pilihan['jam_mulai']."-".$data_pilihan['jam_selesai']; ?></option>
								<?php
									}
								?>
								</select>
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $pengaturanEditErr; ?></span>
							</div>
						</div>
					</fieldset>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success btn-sm" name="btnEdit">
						<i class="fa fa-save fa-fw"></i> Simpan
					</button>
					<a href="../jadwal-kuliah/" class="btn btn-danger btn-sm" data-dismiss="modal">
						<i class="fa fa-times fa-fw"></i> Batal
					</a>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
</form>

<?php	
					}
				}
				
				if (isset($_GET['delete'])) {
					$query = mysql_query("SELECT *, (SELECT nama_dosen FROM dosen WHERE id_dosen = jadwal.id_dosen_koordinator) AS koordinator, (SELECT nama_dosen FROM dosen WHERE id_dosen = jadwal.id_dosen_sekretaris) AS sekretaris FROM jadwal LEFT JOIN mk ON jadwal.id_mk = mk.id_mk LEFT JOIN pengaturan ON jadwal.id_pengaturan = pengaturan.id_pengaturan WHERE MD5(jadwal.id_jadwal) = '$id'");
					if (mysql_num_rows($query) > 0) {
						$data = mysql_fetch_array($query);	
?>
<!-- Modal Delete -->
<form role="form" action="?delete=<?php echo md5($data['id_jadwal']); ?>" method="post" class="form-horizontal form-label-left">
	<div class="modal fade in" style="display: block; padding-right: 17px;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
		<div style="height: 1000px;" class="modal-backdrop fade in"></div>
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-red">
					<a href="../jadwal-kuliah/" class="close">&times;</a>
					<h4 class="modal-title" id="myModalLabel"><i class="fa fa-trash fa-fw"></i> Hapus Data Jadwal Kuliah</h4>
				</div>
				<div class="modal-body">
					<fieldset>							
						<input type="text" name="id" value="<?php echo $data['id_jadwal']; ?>" hidden>
						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<p><b>Apakah anda yakin ingin menghapus data ini?</b></p>
							</div>
							<div class="col-md-2 col-md-offset-1">
								<i class="fa fa-calendar fa-5"></i>
							</div>
							<div class="col-md-7">
								<ul class="list-unstyled">									
									<li>Semester : <?php echo $data['semester']." ".$data['thn_ajaran']; ?></li>
									<li>Blok : <?php echo $data['mk']; ?></li>
									<li>Koordinator : <?php echo $data['koordinator']; ?></li>
									<li>Sekretaris : <?php echo $data['sekretaris']; ?></li>
									<li>
										Kelas : 
										<?php
											$query_kelas = mysql_query("SELECT * FROM jadwal_kelas LEFT JOIN kelas ON jadwal_kelas.id_kelas = kelas.id_kelas WHERE jadwal_kelas.id_jadwal = '$data[id_jadwal]'");
											while ($data_kelas = mysql_fetch_array($query_kelas)) {
												echo "[$data_kelas[kelas]] ";
											}
										?>
									</li>
									<li>Pengaturan : <?php echo "Perjam = ".$data['menit_perjam']." Menit | Selisih = ".$data['menit_perjam']." Menit | Jam Istirahat = ".$data['jam_mulai']."-".$data['jam_selesai']; ?></li>
								</ul>
							</div>
						</div>
					</fieldset>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success btn-sm" name="btnDelete">
						<i class="fa fa-check fa-fw"></i> Ya
					</button>
					<a href="../jadwal-kuliah/" class="btn btn-danger btn-sm" data-dismiss="modal">
						<i class="fa fa-times fa-fw"></i> Batal
					</a>
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
</form>
<?php
					}
				}
			}
		} else if (isset($_GET['sub']) && isset($_GET['id']) && isset($_SESSION['kelas'])) {
			if ($_GET['sub'] == "jadwal-perkelas") {
				$query = mysql_query("SELECT jadwal.id_jadwal, jadwal_hari.* FROM jadwal LEFT JOIN jadwal_kelas ON jadwal.id_jadwal = jadwal_kelas.id_jadwal LEFT JOIN jadwal_hari ON jadwal_kelas.id_jKelas = jadwal_hari.id_jKelas WHERE jadwal_kelas.id_jKelas = '$id_kelas' AND MD5(jadwal.id_jadwal) = '$id'");
					if (mysql_num_rows($query) > 0) {
						$data = mysql_fetch_array($query);
?>
<!-- Modal Tambah Hari Perkelas -->
<form role="form" action="?sub=jadwal-perkelas&id=<?php echo md5($data['id_jadwal']); ?>" method="post" class="form-horizontal form-label-left">
	<div class="modal fade" id="modalAddDay" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header modal-green">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">
						<i class="fa fa-plus fa-fw"></i> Tambah Data Jadwal Kelas <?php echo $data_kelas['kelas']; ?>
					</h4>
				</div>
				<div class="modal-body">
					<fieldset>	
						<input type="text" name="id" value="<?php echo $data['id_jadwal']; ?>" hidden>
						<div class="form-group">
							<label class="control-label col-md-3">Tanggal <span class="red">*</span> :</label>
							<div class="col-md-3">
								<div class="input-group">
									<input class="form-control date-picker" id="id-date-picker-1" type="text" data-date-format="yyyy-mm-dd" name="tanggal" value="<?php echo $tanggal; ?>" />
									<span class="input-group-addon">
										<i class="fa fa-calendar bigger-110"></i>
									</span>
								</div>
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $tanggalErr; ?></span>
							</div>
						</div>	
					</fieldset>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success btn-sm" name="btnAddDay">
						<i class="fa fa-save fa-fw"></i> Simpan
					</button>
					<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">
						<i class="fa fa-times fa-fw"></i> Batal
					</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
</form>
<?php
					}
				if (!isset($_POST["id"]) || $failed) {
					if (isset($_GET['add'])) {
						$query = mysql_query("SELECT jadwal.id_jadwal, pengaturan.*, jadwal_hari.id_jHari, jadwal_hari.tanggal FROM jadwal LEFT JOIN pengaturan ON jadwal.id_pengaturan = pengaturan.id_pengaturan LEFT JOIN jadwal_kelas ON jadwal.id_jadwal = jadwal_kelas.id_jadwal LEFT JOIN jadwal_hari ON jadwal_kelas.id_jKelas = jadwal_hari.id_jKelas WHERE jadwal_kelas.id_jKelas = '$id_kelas' AND MD5(jadwal.id_jadwal) = '$id' AND MD5(jadwal_hari.id_jHari) = '$id2'");
						if (mysql_num_rows($query) > 0) {
							$data = mysql_fetch_array($query);
?>
<!-- Modal Tambah Jadwal Jam Perkelas -->
<form role="form" action="?sub=jadwal-perkelas&id=<?php echo md5($data['id_jadwal']); ?>&add=<?php echo md5($data['id_jHari']); ?>" method="post" class="form-horizontal form-label-left">
	<div class="modal fade in" style="display: block; padding-right: 17px; overflow: auto;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
		<div style="height: 1000px;" class="modal-backdrop fade in"></div>
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header modal-green">
					<a href="?sub=jadwal-perkelas&id=<?php echo md5($data['id_jadwal']); ?>" class="close">&times;</a>
					<h4 class="modal-title" id="myModalLabel">
						<i class="fa fa-plus fa-fw"></i> Tambah Data Jadwal Tanggal <?php echo TanggalIndo($data['tanggal']); ?>
					</h4>
				</div>
				<div class="modal-body">
					<fieldset>	
						<input type="text" name="id" value="<?php echo $data['id_jadwal']; ?>" hidden>
						<input type="text" name="id_jHari" value="<?php echo $data['id_jHari']; ?>" hidden>
						<div class="form-group">
							<label class="control-label col-md-3">Jam Masuk <span class="red">*</span> :</label>
							<div class="col-md-1">
								<input type="text" class="form-control input-mask-time" name="jam_masuk" value="<?php echo $jam_masuk; ?>">
							</div>
							<label class="control-label col-md-3">Jumlah Jam <span class="red">*</span> :</label>
							<div class="col-md-1">
								<input type="text" class="form-control input-mask-hour right" name="jml_jam" value="<?php echo $jml_jam; ?>">
							</div>
							<label class="control-label col-md-1 center">x</label>
							<div class="col-md-1">
								<input type="text" class="form-control" value="<?php echo $data['menit_perjam']; ?>" readonly>
							</div>
							<label class="control-label col-md-1 left">Menit</label>
							<div class="col-lg-4 col-lg-offset-3">
								<span class="error"><?php echo $jam_masukErr; ?></span>
							</div>
							<div class="col-lg-5">
								<span class="error"><?php echo $jml_jamErr; ?></span>
							</div>
						</div>	
						<div class="form-group">
							<label class="control-label col-md-3">Kegiatan <span class="red">*</span> :</label>
							<div class="col-md-8">
								<input type="text" class="form-control" name="kegiatan" value="<?php echo $kegiatan; ?>">
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $kegiatanErr; ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Dosen :</label>
							<div class="col-md-8">
								<select multiple="multiple" size="10" name="duallistbox[]" id="duallist">
								<?php
									$query_pilihan = mysql_query("SELECT * FROM dosen ORDER BY nip ASC, id_finger ASC");
									while ($data_pilihan = mysql_fetch_array($query_pilihan)) {
								?>
									<option value="<?php echo $data_pilihan['id_dosen']; ?>"><?php echo $data_pilihan['nama_dosen']; ?></option>
								<?php
									}
								?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Departemen :</label>
							<div class="col-md-7">
								<select class="chosen-select" name="departemen" data-placeholder=" ">
									<option value=""></option>
								<?php
									$query_pilihan = mysql_query("SELECT * FROM departemen ORDER BY departemen ASC");
									while ($data_pilihan = mysql_fetch_array($query_pilihan)) {
										if ($departemen == $data_pilihan['id_departemen']) {
											$chek = " selected";
										} else {
											$chek = ""; 
										}
								?>
									<option value="<?php echo $data_pilihan['id_departemen']; ?>" <?php echo $chek; ?>><?php echo $data_pilihan['departemen']; ?></option>
								<?php
									}
								?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Ruangan <span class="red">*</span> :</label>
							<div class="col-md-7">
								<select class="chosen-select" name="ruangan" data-placeholder=" ">
									<option value=""></option>
								<?php
									$query_pilihan = mysql_query("SELECT * FROM ruangan ORDER BY ruangan ASC");
									while ($data_pilihan = mysql_fetch_array($query_pilihan)) {
										if ($ruangan == $data_pilihan['id_ruangan']) {
											$chek = " selected";
										} else {
											$chek = ""; 
										}
								?>
									<option value="<?php echo $data_pilihan['id_ruangan']; ?>" <?php echo $chek; ?>><?php echo $data_pilihan['ruangan']; ?></option>
								<?php
									}
								?>
								</select>
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $ruanganErr; ?></span>
							</div>
						</div>
					</fieldset>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success btn-sm" name="btnAddJam">
						<i class="fa fa-save fa-fw"></i> Simpan
					</button>
					<a href="?sub=jadwal-perkelas&id=<?php echo md5($data['id_jadwal']); ?>" class="btn btn-danger btn-sm" data-dismiss="modal">
						<i class="fa fa-times fa-fw"></i> Batal
					</a>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
</form>	
<?php
						}
					}
					
					if (isset($_GET['edit'])) {
						$query = mysql_query("SELECT jadwal.id_jadwal, pengaturan.*, jadwal_hari.id_jHari, jadwal_hari.tanggal, jadwal_jam.* FROM jadwal LEFT JOIN pengaturan ON jadwal.id_pengaturan = pengaturan.id_pengaturan LEFT JOIN jadwal_kelas ON jadwal.id_jadwal = jadwal_kelas.id_jadwal LEFT JOIN jadwal_hari ON jadwal_kelas.id_jKelas = jadwal_hari.id_jKelas LEFT JOIN jadwal_jam ON jadwal_hari.id_jHari = jadwal_jam.id_jHari WHERE jadwal_kelas.id_jKelas = '$id_kelas' AND MD5(jadwal.id_jadwal) = '$id' AND MD5(jadwal_jam.id_jJam) = '$id2'");
						if (mysql_num_rows($query) > 0) {
							$data = mysql_fetch_array($query);
?>
<!-- Modal Edit Jadwal Jam Perkelas -->
<form role="form" action="?sub=jadwal-perkelas&id=<?php echo md5($data['id_jadwal']); ?>&edit=<?php echo md5($data['id_jJam']); ?>" method="post" class="form-horizontal form-label-left">
	<div class="modal fade in" style="display: block; padding-right: 17px; overflow: auto;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
		<div style="height: 1000px;" class="modal-backdrop fade in"></div>
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header modal-green">
					<a href="?sub=jadwal-perkelas&id=<?php echo md5($data['id_jadwal']); ?>" class="close">&times;</a>
					<h4 class="modal-title" id="myModalLabel">
						<i class="fa fa-edit fa-fw"></i> Edit Data Jadwal Tanggal <?php echo TanggalIndo($data['tanggal']); ?>
					</h4>
				</div>
				<div class="modal-body">
					<fieldset>	
						<input type="text" name="id" value="<?php echo $data['id_jadwal']; ?>" hidden>
						<input type="text" name="id_jJam" value="<?php echo $data['id_jJam']; ?>" hidden>
						<div class="form-group">
							<label class="control-label col-md-3">Jam Masuk <span class="red">*</span> :</label>
							<div class="col-md-1">								
								<input type="text" class="form-control input-mask-time" name="jam_masuk" value="<?php echo $data['jam_masuk']; ?>">
							</div>
							<label class="control-label col-md-3">Jumlah Jam <span class="red">*</span> :</label>
							<div class="col-md-1">
								<input type="text" class="form-control input-mask-hour right" name="jml_jam" value="<?php echo $data['jml_jam']; ?>">
							</div>
							<label class="control-label col-md-1 center">x</label>
							<div class="col-md-1">
								<input type="text" class="form-control" value="<?php echo $data['menit_perjam']; ?>" readonly>
							</div>
							<label class="control-label col-md-1 left">Menit</label>
							<div class="col-lg-4 col-lg-offset-3">
								<span class="error"><?php echo $jam_masukEditErr; ?></span>
							</div>
							<div class="col-lg-5">
								<span class="error"><?php echo $jml_jamEditErr; ?></span>
							</div>
						</div>	
						<div class="form-group">
							<label class="control-label col-md-3">Kegiatan <span class="red">*</span> :</label>
							<div class="col-md-8">
								<input type="text" class="form-control" name="kegiatan" value="<?php echo $data['kegiatan']; ?>">
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $kegiatanEditErr; ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Dosen :</label>
							<div class="col-md-8">
								<select multiple="multiple" size="10" name="duallistbox[]" id="duallist">
								<?php
									$query_pilihan = mysql_query("SELECT * FROM dosen ORDER BY nip ASC, id_finger ASC");
									while ($data_pilihan = mysql_fetch_array($query_pilihan)) {
										$query_dosen = mysql_query("SELECT * FROM jadwal_dosen WHERE id_jJam = '$data[id_jJam]' AND id_dosen = '$data_pilihan[id_dosen]'");
										if (mysql_num_rows($query_dosen) > 0) {
											$check = "selected";
										} else {
											$check = "";
										}
								?>
									<option value="<?php echo $data_pilihan['id_dosen']; ?>" <?php echo $check; ?>><?php echo $data_pilihan['nama_dosen']; ?></option>
								<?php
									}
								?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Departemen :</label>
							<div class="col-md-7">
								<select class="chosen-select" name="departemen" data-placeholder=" ">
									<option value=""></option>
								<?php
									$query_pilihan = mysql_query("SELECT * FROM departemen ORDER BY departemen ASC");
									while ($data_pilihan = mysql_fetch_array($query_pilihan)) {
										if ($data['id_departemen'] == $data_pilihan['id_departemen']) {
											$chek = " selected";
										} else {
											$chek = ""; 
										}
								?>
									<option value="<?php echo $data_pilihan['id_departemen']; ?>" <?php echo $chek; ?>><?php echo $data_pilihan['departemen']; ?></option>
								<?php
									}
								?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Ruangan <span class="red">*</span> :</label>
							<div class="col-md-7">
								<select class="chosen-select" name="ruangan" data-placeholder=" ">
									<option value=""></option>
								<?php
									$query_pilihan = mysql_query("SELECT * FROM ruangan ORDER BY ruangan ASC");
									while ($data_pilihan = mysql_fetch_array($query_pilihan)) {
										if ($data['id_ruangan'] == $data_pilihan['id_ruangan']) {
											$chek = " selected";
										} else {
											$chek = ""; 
										}
								?>
									<option value="<?php echo $data_pilihan['id_ruangan']; ?>" <?php echo $chek; ?>><?php echo $data_pilihan['ruangan']; ?></option>
								<?php
									}
								?>
								</select>
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $ruanganEditErr; ?></span>
							</div>
						</div>
					</fieldset>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success btn-sm" name="btnEditJam">
						<i class="fa fa-save fa-fw"></i> Simpan
					</button>
					<a href="?sub=jadwal-perkelas&id=<?php echo md5($data['id_jadwal']); ?>" class="btn btn-danger btn-sm" data-dismiss="modal">
						<i class="fa fa-times fa-fw"></i> Batal
					</a>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
</form>	
<?php
						}
					}
					
					if (isset($_GET['delete'])) {
						$query = mysql_query("SELECT jadwal.id_jadwal, jadwal_hari.tanggal, jadwal_jam.*, departemen.departemen, ruangan.ruangan FROM jadwal LEFT JOIN jadwal_kelas ON jadwal.id_jadwal = jadwal_kelas.id_jadwal LEFT JOIN jadwal_hari ON jadwal_kelas.id_jKelas = jadwal_hari.id_jKelas LEFT JOIN jadwal_jam ON jadwal_hari.id_jHari = jadwal_jam.id_jHari LEFt JOIN departemen ON jadwal_jam.id_departemen = departemen.id_departemen LEFT JOIN ruangan ON jadwal_jam.id_ruangan = ruangan.id_ruangan WHERE jadwal_kelas.id_jKelas = '$id_kelas' AND MD5(jadwal.id_jadwal) = '$id' AND MD5(jadwal_jam.id_jJam) = '$id2'");
						if (mysql_num_rows($query) > 0) {
							$data = mysql_fetch_array($query);
?>
<!-- Modal Delete Jadwal Jam Perkelas -->
<form role="form" action="?sub=jadwal-perkelas&id=<?php echo md5($data['id_jadwal']); ?>&delete=<?php echo md5($data['id_jJam']); ?>" method="post" class="form-horizontal form-label-left">
	<div class="modal fade in" style="display: block; padding-right: 17px; overflow: auto;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
		<div style="height: 1000px;" class="modal-backdrop fade in"></div>
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-green">
					<a href="?sub=jadwal-perkelas&id=<?php echo md5($data['id_jadwal']); ?>" class="close">&times;</a>
					<h4 class="modal-title" id="myModalLabel">
						<i class="fa fa-trash fa-fw"></i> Delete Data Jadwal Tanggal <?php echo TanggalIndo($data['tanggal']); ?>
					</h4>
				</div>
				<div class="modal-body">
					<fieldset>	
						<input type="text" name="id" value="<?php echo $data['id_jadwal']; ?>" hidden>
						<input type="text" name="id_jJam" value="<?php echo $data['id_jJam']; ?>" hidden>
						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<p><b>Apakah anda yakin ingin menghapus data ini?</b></p>
							</div>
							<div class="col-md-2 col-md-offset-1">
								<i class="fa fa-calendar fa-5"></i>
							</div>
							<div class="col-md-7">
								<ul class="list-unstyled">									
									<li>Jam Masuk : <?php echo $data['jam_masuk']; ?></li>
									<li>Jumlah Jam : <?php echo $data['jml_jam']; ?></li>
									<li>Kegiatan : <?php echo $data['kegiatan']; ?></li>
									<li>
										Dosen : 
										<?php
											$query_dosen = mysql_query("SELECT * FROM jadwal_dosen LEFT JOIN dosen ON jadwal_dosen.id_dosen = dosen.id_dosen WHERE jadwal_dosen.id_jJam = '$data[id_jJam]'");
											if (mysql_num_rows($query_dosen) > 1) {
												echo "<ol>";
												while ($data_dosen = mysql_fetch_array($query_dosen)) {
													echo "<li>".$data_dosen['nama_dosen']."</li>";
												}
												echo "</ol>";
											} else {
												$data_dosen = mysql_fetch_array($query_dosen);
												echo $data_dosen['nama_dosen'];
											}
										?>
									</li>
									<li>Departemen : <?php echo $data['departemen']; ?></li>
									<li>Ruangan : <?php echo $data['ruangan']; ?></li>
								</ul>
							</div>
						</div>
					</fieldset>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success btn-sm" name="btnDeleteJam">
						<i class="fa fa-check fa-fw"></i> Ya
					</button>
					<a href="?sub=jadwal-perkelas&id=<?php echo md5($data['id_jadwal']); ?>" class="btn btn-danger btn-sm" data-dismiss="modal">
						<i class="fa fa-times fa-fw"></i> Batal
					</a>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
</form>	
<?php
						}
					}
					
					if (isset($_GET['delete-day'])) {
						echo "xxxx";
						$query = mysql_query("SELECT jadwal.id_jadwal, jadwal_hari.id_jHari, jadwal_hari.tanggal FROM jadwal LEFT JOIN jadwal_kelas ON jadwal.id_jadwal = jadwal_kelas.id_jadwal LEFT JOIN jadwal_hari ON jadwal_kelas.id_jKelas = jadwal_hari.id_jKelas WHERE jadwal_kelas.id_jKelas = '$id_kelas' AND MD5(jadwal.id_jadwal) = '$id' AND MD5(jadwal_hari.id_jHari) = '$id2'");
						if (mysql_num_rows($query) > 0) {
							$data = mysql_fetch_array($query);
?>
<!-- Modal Delete Jadwal Hari Perkelas -->
<form role="form" action="?sub=jadwal-perkelas&id=<?php echo md5($data['id_jadwal']); ?>&delete-day=<?php echo md5($data['id_jHari']); ?>" method="post" class="form-horizontal form-label-left">
	<div class="modal fade in" style="display: block; padding-right: 17px; overflow: auto;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
		<div style="height: 1000px;" class="modal-backdrop fade in"></div>
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-green">
					<a href="?sub=jadwal-perkelas&id=<?php echo md5($data['id_jadwal']); ?>" class="close">&times;</a>
					<h4 class="modal-title" id="myModalLabel">
						<i class="fa fa-trash fa-fw"></i> Delete Data Jadwal Kelas <?php echo $data_kelas['kelas']; ?>
					</h4>
				</div>
				<div class="modal-body">
					<fieldset>	
						<input type="text" name="id" value="<?php echo $data['id_jadwal']; ?>" hidden>
						<input type="text" name="id_jHari" value="<?php echo $data['id_jHari']; ?>" hidden>
						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<p><b>Apakah anda yakin ingin menghapus data ini?</b></p>
							</div>
							<div class="col-md-2 col-md-offset-1">
								<i class="fa fa-calendar fa-5"></i>
							</div>
							<div class="col-md-7">
								<ul class="list-unstyled">									
									<li>Hari / Tanggal : <?php echo Hari($data['tanggal']).", ".TanggalIndo($data['tanggal']); ?></li>
								</ul>
							</div>
						</div>
					</fieldset>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success btn-sm" name="btnDeleteHari">
						<i class="fa fa-check fa-fw"></i> Ya
					</button>
					<a href="?sub=jadwal-perkelas&id=<?php echo md5($data['id_jadwal']); ?>" class="btn btn-danger btn-sm" data-dismiss="modal">
						<i class="fa fa-times fa-fw"></i> Batal
					</a>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
</form>	
<?php
						}
					}
				}
			} else if ($_GET['sub'] == "mahasiswa-perkelas") {
				$query = mysql_query("SELECT * FROM jadwal_kelas WHERE MD5(id_jadwal) = '$id' AND id_jKelas = '$id_kelas'");
				if (mysql_num_rows($query) > 0) {
					$data = mysql_fetch_array($query);
?>
<!-- Modal Tambah Mahasiswa Perkelas -->
<form role="form" action="?sub=mahasiswa-perkelas&id=<?php echo md5($data['id_jadwal']); ?>" method="post" class="form-horizontal form-label-left">
	<div class="modal fade" id="modalAddMhs" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header modal-green">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">
						<i class="fa fa-plus fa-fw"></i> Tambah Data Mahasiswa
					</h4>
				</div>
				<div class="modal-body">
					<fieldset>	
						<input type="text" name="id" value="<?php echo $data['id_jadwal']; ?>" hidden>
						<input type="text" name="id_jKelas" value="<?php echo $data['id_jKelas']; ?>" hidden>	
						<div class="form-group">
							<div class="col-lg-9">
								<span class="error"><?php echo $mhsErr; ?></span>
							</div>
						</div>	
						<div class="widget-box widget-color-blue2">
							<div class="widget-header">
								<h4 class="widget-title">Data Mahasiswa</h4>

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
							</div>

							<div class="widget-body">		
								<div class="table-responsive">
									<table id="dynamic-table2" class="table table-striped table-bordered table-hover">
										<thead>
											<tr>
												<th class="center">
													<label class="pos-rel">
														<input type="checkbox" class="ace" />
															<span class="lbl"></span>
													</label>
												</th>
												<th class="center">No.</th>
												<th class="center">NIM</th>
												<th class="center">Nama</th>
												<th class="center">No. Handphone</th>
												<th class="hidden"></th>
												<th class="hidden"></th>
											</tr>
										</thead>

										<tbody>
										<?php
											$no = 0;
											$query_mhs = mysql_query("SELECT * FROM mahasiswa WHERE NOT EXISTS(SELECT id_mhs FROM jadwal_mhs WHERE mahasiswa.id_mhs = jadwal_mhs.id_mhs) ORDER BY nim DESC");
											while ($data_mhs = mysql_fetch_array($query_mhs)) {
												$no++;
										?>
											<tr>
												<td class="center">
													<label class="pos-rel">
														<input type="checkbox" class="ace" name="id_mhs[]" value="<?php echo $data_mhs['id_mhs']; ?>" />
														<span class="lbl"></span>
													</label>
												</td>
												<td class="center"><?php echo $no; ?></td>
												<td class="center"><?php echo $data_mhs['nim']; ?></td>
												<td><?php echo $data_mhs['nama_mhs']; ?></td>
												<td class="center"><?php echo $data_mhs['hp']; ?></td>
												<td class="hidden"></td>
												<td class="hidden"></td>
											</tr>
										<?php
											}
										?>
										</tbody>
									</table>
								</div>
							</div>
						</div>	
						<div class="form-group">
							<div class="col-lg-9">
								<span class="error"><?php echo $mhsErr; ?></span>
							</div>
						</div>		
					</fieldset>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success btn-sm" name="btnAddMhs">
						<i class="fa fa-save fa-fw"></i> Simpan
					</button>
					<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">
						<i class="fa fa-times fa-fw"></i> Batal
					</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
</form>

<!-- Modal Many Mahasiswa Perkelas -->
<form role="form" action="?sub=mahasiswa-perkelas&id=<?php echo md5($data['id_jadwal']); ?>" method="post" class="form-horizontal form-label-left" id="manyMhs">
	<div class="modal fade" id="modalManyMhs" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-red">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">
						<i class="fa fa-trash fa-fw"></i> Hapus Data Mahasiswa
					</h4>
				</div>
				<div class="modal-body">
					<fieldset>	
						<input type="text" name="id" value="<?php echo $data['id_jadwal']; ?>" hidden>						
						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<p><b>Apakah anda yakin ingin menghapus data-data tersebut?</b></p>
							</div>				
						</div>		
					</fieldset>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success btn-sm" name="btnManyMhs">
						<i class="fa fa-check fa-fw"></i> Ya
					</button>
					<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">
						<i class="fa fa-times fa-fw"></i> Batal
					</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
</form>
<?php
					if (isset($_GET['delete'])) {
						$query = mysql_query("SELECT * FROM jadwal_mhs LEFT JOIN mahasiswa ON jadwal_mhs.id_mhs = mahasiswa.id_mhs LEFT JOIN jadwal_kelas ON jadwal_mhs.id_jKelas = jadwal_kelas.id_jKelas LEFT JOIN jadwal ON jadwal_kelas.id_jadwal = jadwal.id_jadwal LEFT JOIN mk ON jadwal.id_mk = mk.id_mk WHERE MD5(jadwal.id_jadwal) ='$id' AND MD5(jadwal_mhs.id_jMhs) ='$id2' AND jadwal_mhs.id_jKelas = '$id_kelas'");
						if (mysql_num_rows($query) > 0) {
							$data = mysql_fetch_array($query);
?>
<!-- Modal Delete Mahasiswa -->
<form role="form" action="?sub=mahasiswa-perkelas&id=<?php echo md5($data['id_jadwal']); ?>&delete=<?php echo md5($data['id_jMhs']); ?>" method="post" class="form-horizontal form-label-left">
	<div class="modal fade in" style="display: block; padding-right: 17px;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
		<div style="height: 1000px;" class="modal-backdrop fade in"></div>
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-red">
					<a href="?sub=mahasiswa-perkelas&id=<?php echo md5($data['id_jadwal']); ?>" class="close">&times;</a>
					<h4 class="modal-title" id="myModalLabel"><i class="fa fa-trash fa-fw"></i> Hapus Data Mahasiswa</h4>
				</div>
				<div class="modal-body">
					<fieldset>							
						<input type="text" name="id" value="<?php echo $data['id_jadwal']; ?>" hidden>
						<input type="text" name="id_jMhs" value="<?php echo $data['id_jMhs']; ?>" hidden>
						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<p><b>Apakah anda yakin ingin menghapus data ini?</b></p>
							</div>
							<div class="col-md-2 col-md-offset-1">
								<i class="fa fa-calendar fa-5"></i>
							</div>
							<div class="col-md-7">
								<ul class="list-unstyled">									
									<li>Blok : <?php echo $data['mk']; ?></li>
									<li>Kelas : <?php echo $data_kelas['kelas']; ?></li>
									<li>NIM : <?php echo $data['nim']; ?></li>
									<li>Nama : <?php echo $data['nama_mhs']; ?></li>
								</ul>
							</div>
						</div>
					</fieldset>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success btn-sm" name="btnDeleteMhs">
						<i class="fa fa-check fa-fw"></i> Ya
					</button>
					<a href="?sub=mahasiswa-perkelas&id=<?php echo md5($data['id_jadwal']); ?>" class="btn btn-danger btn-sm" data-dismiss="modal">
						<i class="fa fa-times fa-fw"></i> Batal
					</a>
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
</form>
<?php
						}
					}
				}
			}
		}
		db1_close();
	} else {
		echo "<meta http-equiv='refresh' content='0; url=../../'>";
	}
?>	