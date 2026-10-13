<?php	
	if (isset($_SESSION['log_absensi']) && isset($_SESSION['level_absensi'])) {
		$nim = $nama = $hp = $finger = "";
		$nimEdit = $namaEdit = $hpEdit = $fingerEdit = "";		
		$nimErr = $namaErr = $hpErr = $fingerErr = "";
		$nimEditErr = $namaEditErr = $hpEditErr = $fingerEditErr = "";
		$error = "";
		
		/* Add
		-----------------*/
		if (isset($_POST["btnAdd"])) {		
			include '../../controllers/mahasiswa/add.php';
		}	
		
		/* Reset
		-----------------*/
		if (isset($_POST["btnReset"])) {		
			include '../../controllers/mahasiswa/reset.php';
		}	
		
		/* Edit
		-----------------*/
		if (isset($_POST["btnEdit"])) {		
			include '../../controllers/mahasiswa/edit.php';
		}	
		
		/* Delete
		-----------------*/
		if (isset($_POST["btnDelete"])) {		
			include '../../controllers/mahasiswa/delete.php';
		}
		
		/* Delete Many
		-----------------*/
		if (isset($_POST["btnMany"])) {		
			include '../../controllers/mahasiswa/many.php';
		}
		
		/* GET ID
		-----------------*/
		db1_open();
		if (isset($_GET['edit'])) {
			$id = mysql_real_escape_string($_GET['edit']);
		} else if (isset($_GET['delete'])) {
			$id = mysql_real_escape_string($_GET['delete']);
		} else if (isset($_GET['reset'])) {
			$id = mysql_real_escape_string($_GET['reset']);
		}
		db1_close();
?>

<div class="page-header">
	<h1><i class="ace-icon fa fa-user"></i> Mahasiswa</h1>
</div><!-- /.page-header -->

<div class="row">
	<div class="col-xs-12">
		<div class="users-profile row">
			<div class="col-sm-offset- col-sm-12">
				<div class="space"></div>
				<div class="clearfix">
					<div class="pull-right tableTools-container"></div>
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
										<th class="center">Pilihan</th>
									</tr>
								</thead>

								<tbody>
								<?php
									db1_open();
									
									$no = 0;
									$query = mysql_query("SELECT * FROM mahasiswa ORDER BY nim ASC");
									while ($data = mysql_fetch_array($query)) {
										$no++;
								?>
									<tr>
										<td class="center">
											<label class="pos-rel">
												<input type="checkbox" class="ace" name="id[]" value="<?php echo $data['id_mhs']; ?>" form="many" />
												<span class="lbl"></span>
											</label>
										</td>
										<td class="center"><?php echo $no; ?></td>
										<td class="center"><?php echo $data['nim']; ?></td>
										<td><?php echo $data['nama_mhs']; ?></td>
										<td class="center"><?php echo $data['hp']; ?></td>
										<td class="hidden"></td>
										<td class="center">
											<div class="hidden-sm hidden-xs action-buttons">
												<div class="btn-group">
													<a class="btn btn-xs btn-purple" href="?reset=<?php echo md5($data['id_mhs']); ?>" data-rel="tooltip" title="Reset Password" data-toggle="tooltip">
														<i class="ace-icon fa fa-recycle bigger-110"></i>
													</a>
													<a class="btn btn-xs btn-warning" href="?edit=<?php echo md5($data['id_mhs']); ?>" data-rel="tooltip" title="Edit" data-toggle="tooltip">
														<i class="ace-icon fa fa-edit bigger-110"></i>
													</a>
													<a class="btn btn-xs btn-danger" href="?delete=<?php echo md5($data['id_mhs']); ?>" data-rel="tooltip" title="Hapus" data-toggle="tooltip">
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
															<a href="?reset=<?php echo md5($data['id_mhs']); ?>" class="tooltip-dark" data-rel="tooltip" title="Reset Password" data-toggle="tooltip">
																<span class="purple">
																	<i class="ace-icon fa fa-recycle bigger-120"></i>
																</span>
															</a>
														</li>
														<li>
															<a href="?edit=<?php echo md5($data['id_mhs']); ?>" class="tooltip-dark" data-rel="tooltip" title="Edit" data-toggle="tooltip">
																<span class="orange">
																	<i class="ace-icon fa fa-pencil-square-o bigger-120"></i>
																</span>
															</a>
														</li>
														<li>
															<a href="?delete=<?php echo md5($data['id_mhs']); ?>" class="tooltip-dark" data-rel="tooltip" title="Hapus" data-toggle="tooltip">
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
									
									db1_close();
								?>
								</tbody>
							</table>
						</div>
						<?php
							}
						?>
					</div>
				</div>
			</div><!-- /.span -->
		</div><!-- /.users-profile -->
	</div><!-- /.col -->
</div><!-- /.row -->

<?php	
		db1_open();
?>
<!-- Modal Tambah -->
<form role="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" class="form-horizontal form-label-left">
	<div class="modal fade" id="modalAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
						<div class="form-group">
							<label class="control-label col-md-3">ID Finger <span class="red">*</span> :</label>							
							<div class="col-md-2">
								<input type="text" class="form-control input-mask-finger" name="finger" value="<?php echo $finger; ?>">
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $fingerErr; ?></span>
							</div>
						</div>					
						<div class="form-group">
							<label class="control-label col-md-3">NIM <span class="red">*</span> :</label>
							<div class="col-md-3">
								<input type="text" class="form-control input-mask-nim" name="nim" value="<?php echo $nim; ?>">
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $nimErr; ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3" for="last-name">Nama <span class="red">*</span> :</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="nama" value="<?php echo $nama; ?>">
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $namaErr; ?></span>
							</div>
						</div>		
						<div class="form-group">
							<label class="control-label col-md-3">Handphone :</label>
							<div class="col-md-2">
								<input type="text" class="form-control" name="hp" value="<?php echo $hp; ?>">
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $hpErr; ?></span>
							</div>
						</div>
					</fieldset>
				</div>
				<div class="modal-footer">
					<p class="pull-left red">* Field tidak boleh kosong.</p>
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
						<i class="fa fa-trash fa-fw"></i> Hapus Data Mahasiswa
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
			if (isset($_GET['reset'])) {
				$query = mysql_query("SELECT * FROM mahasiswa LEFT JOIN users ON mahasiswa.id_mhs = users.id_mhs WHERE MD5(mahasiswa.id_mhs) = '$id'");
				if (mysql_num_rows($query) > 0) {
					$data = mysql_fetch_array($query);
?>
<!-- Modal Reset -->
<form role="form" action="?reset=<?php echo md5($data['id_mhs']); ?>" method="post" class="form-horizontal form-label-left">
	<div class="modal fade in" style="display: block; padding-right: 17px; overflow: auto;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
		<div style="height: 1000px;" class="modal-backdrop fade in"></div>
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-purple">
					<a href="../mahasiswa/" class="close">&times;</a>
					<h4 class="modal-title" id="myModalLabel">
						<i class="fa fa-recycle fa-fw"></i> Reset Password Mahasiswa
					</h4>
				</div>
				<div class="modal-body">
					<fieldset>						
						<input type="text" name="id" value="<?php echo $data['id_mhs']; ?>" hidden>	
						<input type="text" name="nim" value="<?php echo $data['nim']; ?>" hidden>	
						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<p><b>Apakah anda yakin ingin melakukan reset password data ini?</b></p>
							</div>
							<div class="col-md-2 col-md-offset-1">
								<i class="fa fa-user-md fa-5"></i>
							</div>
							<div class="col-md-7">
								<ul class="list-unstyled">
									<li>NIM : <?php echo $data['nim']; ?></li>
									<li>Nama : <?php echo $data['nama_mhs']; ?></li>
									<li>Terakhir Aktif : <?php echo IndonesiaTgl($data['waktu'])." ".substr($data['waktu'], 11, 9); ?></li>
								</ul>
							</div>
						</div>	
					</fieldset>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success btn-sm" name="btnReset">
						<i class="fa fa-check fa-fw"></i> Ya
					</button>
					<a href="../mahasiswa/" class="btn btn-danger btn-sm" data-dismiss="modal">
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
				$query = mysql_query("SELECT * FROM mahasiswa WHERE MD5(id_mhs) = '$id'");
				if (mysql_num_rows($query) > 0) {
					$data = mysql_fetch_array($query);
?>
<!-- Modal Edit -->
<form role="form" action="?edit=<?php echo md5($data['id_mhs']); ?>" method="post" class="form-horizontal form-label-left">
	<div class="modal fade in" style="display: block; padding-right: 17px; overflow: auto;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
		<div style="height: 1000px;" class="modal-backdrop fade in"></div>
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header modal-orange">
					<a href="../mahasiswa/" class="close">&times;</a>
					<h4 class="modal-title" id="myModalLabel">
						<i class="fa fa-edit fa-fw"></i> Edit Data Mahasiswa
					</h4>
				</div>
				<div class="modal-body">
					<fieldset>		
						<input type="text" name="id" value="<?php echo $data['id_mhs']; ?>" hidden>
						<div class="form-group">
							<label class="control-label col-md-3">ID Finger <span class="red">*</span> :</label>
							<div class="col-md-2">
								<input type="text" class="form-control input-mask-finger" name="finger" value="<?php echo $data['id_finger']; ?>">
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $fingerEditErr; ?></span>
							</div>
						</div>			
						<div class="form-group">
							<label class="control-label col-md-3">NIM <span class="red">*</span> :</label>
							<div class="col-md-3">
								<input type="text" class="form-control input-mask-nim" name="nim" value="<?php echo $data['nim']; ?>">
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $nimEditErr; ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Nama <span class="red">*</span> :</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="nama" value="<?php echo $data['nama_mhs']; ?>">
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $namaEditErr; ?></span>
							</div>
						</div>		
						<div class="form-group">
							<label class="control-label col-md-3">Handphone :</label>
							<div class="col-md-2">
								<input type="text" class="form-control" name="hp" value="<?php echo $data['hp']; ?>">
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $hpEditErr; ?></span>
							</div>
						</div>
					</fieldset>
				</div>
				<div class="modal-footer">
					<p class="pull-left red">* Field tidak boleh kosong.</p>
					<button type="submit" class="btn btn-success btn-sm" name="btnEdit">
						<i class="fa fa-save fa-fw"></i> Simpan
					</button>
					<a href="../mahasiswa/" class="btn btn-danger btn-sm" data-dismiss="modal">
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
				$query = mysql_query("SELECT * FROM mahasiswa WHERE MD5(id_mhs) = '$id'") or die (mysql_error());
				if (mysql_num_rows($query) > 0) {
					$data = mysql_fetch_array($query);	
?>
<!-- Modal Delete -->
<form role="form" action="?delete=<?php echo md5($data['id_mhs']); ?>" method="post" class="form-horizontal form-label-left">
	<div class="modal fade in" style="display: block; padding-right: 17px; overflow: auto;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
		<div style="height: 1000px;" class="modal-backdrop fade in"></div>
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-red">
					<a href="../mahasiswa/" class="close">&times;</a>
					<h4 class="modal-title" id="myModalLabel"><i class="fa fa-trash fa-fw"></i> Hapus Data Mahasiswa</h4>
				</div>
				<div class="modal-body">
					<fieldset>							
						<input type="text" name="id" value="<?php echo $data['id_mhs']; ?>" hidden>	
						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<p><b>Apakah anda yakin ingin menghapus data ini?</b></p>
							</div>
							<div class="col-md-2 col-md-offset-1">
								<i class="fa fa-user fa-5"></i>
							</div>
							<div class="col-md-7">
								<ul class="list-unstyled">
									<li>ID Finger : <?php echo $data['id_finger']; ?></li>
									<li>NIM : <?php echo $data['nim']; ?></li>
									<li>Nama : <?php echo $data['nama_mhs']; ?></li>
									<li>No. Handphone : <?php echo $data['hp']; ?></li>
								</ul>
							</div>
						</div>
					</fieldset>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success btn-sm" name="btnDelete">
						<i class="fa fa-check fa-fw"></i> Ya
					</button>
					<a href="../mahasiswa/" class="btn btn-danger btn-sm" data-dismiss="modal">
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
		
		db1_close();
	} else {
		echo "<meta http-equiv='refresh' content='0; url=../../'>";
	}
?>					