<?php			
	if (isset($_SESSION['log_absensi']) && isset($_SESSION['level_absensi'])) {
		$id_user = $nama = $username = $password = $password2 = $namaEdit = $usernameEdit = $passwordEdit = $password2Edit = "";
		$namaErr = $usernameErr = $passwordErr = $password2Err = $namaEditErr = $usernameEditErr = $passwordEditErr = $password2EditErr = "";
		$error = "";
		
		/* Add
		-----------------*/
		if (isset($_POST["btnAdd"])) {		
			include '../../controllers/akun/add.php';
		}		
		
		/* Edit
		-----------------*/
		if (isset($_POST["btnEdit"])) {		
			include '../../controllers/akun/edit.php';
		}	
		
		/* Delete
		-----------------*/
		if (isset($_POST["btnDelete"])) {		
			include '../../controllers/akun/delete.php';
		}
		
		/* Delete Many
		-----------------*/
		if (isset($_POST["btnMany"])) {		
			include '../../controllers/akun/many.php';
		}
		
		/* GET ID
		-----------------*/
		db1_open();
		if (isset($_GET['edit'])) {
			$id = mysql_real_escape_string($_GET['edit']);
		} else if (isset($_GET['delete'])) {
			$id = mysql_real_escape_string($_GET['delete']);
		}
		db1_close();
?>

<div class="page-header">
	<h1><i class="ace-icon fa fa-key"></i> Akun</h1>
</div><!-- /.page-header -->

<div class="row">
	<div class="col-xs-12">
		<div class="users-profile row">
			<div class="col-sm-offset- col-sm-12">
				<div class="space"></div>
				<div class="clearfix">
					<div class="pull-right tableTools-container"></div>
				</div>
				<div class="widget-box widget-color-blue">
					<div class="widget-header">
						<h4 class="widget-title">Data Akun</h4>

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
										<th class="center">Nama</th>
										<th class="center">Status</th>
										<th class="center">Terakhir Login</th>
										<th class="center">IP Address</th>
										<th class="center col-md-1">Pilihan</th>
									</tr>
								</thead>

								<tbody>
								<?php
									db1_open();									
									$no = 0;
									$query = mysql_query("SELECT * FROM users ORDER BY waktu DESC");
									while ($data = mysql_fetch_array($query)) {
										$no++;
								?>
									<tr>
										<td class="center">
											<label class="pos-rel">
												<input type="checkbox" class="ace" name="id[]" value="<?php echo $data['id_user']; ?>" form="many" />
												<span class="lbl"></span>
											</label>
										</td>
										<td class="center"><?php echo $no; ?></td>
										<td><?php echo $data['nama_user']; ?></td>
										<td class="center">
											<?php 
												if ($data['status'] == "1") {
											?>
											<span class="label label-success arrowed arrowed-right">&nbsp;&nbsp;&nbsp;On&nbsp;&nbsp;&nbsp;</span>	
											<?php 
												} else if ($data['status'] == "0") {
											?>
											<span class="label label-danger arrowed-in arrowed-in-right">&nbsp;&nbsp;&nbsp;Off&nbsp;&nbsp;&nbsp;</span>	
											<?php
												}
											?>
										</td>
										<td class="hidden-480 center"><?php echo IndonesiaTgl($data['waktu'])." ".substr($data['waktu'], 11); ?></td>
										<td class="center"><?php echo $data['ip_address']; ?></td>
										<td class="center">
											<div class="hidden-sm hidden-xs action-buttons">
												<div class="btn-group">
													<a class="btn btn-xs btn-warning" href="?edit=<?php echo md5($data['id_user']); ?>" data-rel="tooltip" title="Edit" data-toggle="tooltip">
														<i class="ace-icon fa fa-edit bigger-110"></i>
													</a>
													<a class="btn btn-xs btn-danger" href="?delete=<?php echo md5($data['id_user']); ?>" data-rel="tooltip" title="Hapus" data-toggle="tooltip">
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
															<a href="?edit=<?php echo md5($data['id_user']); ?>" class="tooltip-dark" data-rel="tooltip" title="Edit" data-toggle="tooltip">
																<span class="orange">
																	<i class="ace-icon fa fa-pencil-square-o bigger-120"></i>
																</span>
															</a>
														</li>
														<li>
															<a href="?delete=<?php echo md5($data['id_user']); ?>" class="tooltip-dark" data-rel="tooltip" title="Hapus" data-toggle="tooltip">
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
						<i class="fa fa-plus fa-fw"></i> Tambah Data Akun
					</h4>
				</div>
				<div class="modal-body">
					<fieldset>			
						<div class="form-group">
							<label class="control-label col-md-3">Nama <span class="red">*</span> :</label>
							<div class="col-md-7">
								<input type="text" class="form-control" name="nama" value="<?php echo $nama; ?>">
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $namaErr; ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Username <span class="red">*</span> :</label>
							<div class="col-md-5">
								<input type="text" class="form-control" name="username" value="<?php echo $username; ?>">
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $usernameErr; ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Password <span class="red">*</span> :</label>
							<div class="col-md-5">
								<input type="password" class="form-control" name="password" value="<?php echo $password; ?>">
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $passwordErr; ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Konfirmasi Password <span class="red">*</span> :</label>
							<div class="col-md-5">
								<input type="password" class="form-control" name="password2" value="<?php echo $password2; ?>">
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $password2Err; ?></span>
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
						<i class="fa fa-trash fa-fw"></i> Hapus Data Akun
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
				$query = mysql_query("SELECT * FROM users WHERE MD5(id_user) ='$id'");
				if (mysql_num_rows($query) > 0) {
					$data = mysql_fetch_array($query);	
?>
<!-- Modal Edit -->
<form role="form" action="?edit=<?php echo md5($data['id_user']); ?>" method="post" class="form-horizontal form-label-left">
	<div class="modal fade in" style="display: block; padding-right: 17px; overflow: auto;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
		<div style="height: 1000px;" class="modal-backdrop fade in"></div>
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header modal-orange">
					<a href="../akun/" class="close">&times;</a>
					<h4 class="modal-title" id="myModalLabel">
						<i class="fa fa-edit fa-fw"></i> Edit Data Akun
					</h4>
				</div>
				<div class="modal-body">
					<fieldset>	
						<input type="text" name="id" value="<?php echo $data['id_user']; ?>" hidden>
						<div class="form-group">
							<label class="control-label col-md-3">Nama <span class="red">*</span> :</label>
							<div class="col-md-7">
								<input type="text" class="form-control" name="nama" value="<?php echo $data['nama_user']; ?>">
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $namaEditErr; ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Username <span class="red">*</span> :</label>
							<div class="col-md-5">
								<input type="text" class="form-control" name="username" value="<?php echo $data['username']; ?>">
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $usernameEditErr; ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Password Baru <span class="red">**</span> :</label>
							<div class="col-md-5">
								<input type="password" class="form-control" name="password" value="<?php echo $passwordEdit; ?>">
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $passwordEditErr; ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Konfirmasi Password Baru <span class="red">**</span> :</label>
							<div class="col-md-5">
								<input type="password" class="form-control" name="password2" value="<?php echo $password2Edit; ?>">
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $password2EditErr; ?></span>
							</div>
						</div>
					</fieldset>
				</div>
				<div class="modal-footer">
					<p class="pull-left red">* Field tidak boleh kosong.  ** Hanya diisi jika ingin mengganti password</p>
					<button type="submit" class="btn btn-success btn-sm" name="btnEdit">
						<i class="fa fa-save fa-fw"></i> Simpan
					</button>
					<a href="../akun/" class="btn btn-danger btn-sm" data-dismiss="modal">
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
				$query = mysql_query("SELECT * FROM users WHERE MD5(id_user) = '$id'");
				if (mysql_num_rows($query) > 0) {
					$data = mysql_fetch_array($query);	
?>
<!-- Modal Delete -->
<form role="form" action="?delete=<?php echo md5($data['id_user']); ?>" method="post" class="form-horizontal form-label-left">
	<div class="modal fade in" style="display: block; padding-right: 17px; overflow: auto;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
		<div style="height: 1000px;" class="modal-backdrop fade in"></div>
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-red">
					<a href="../akun/" class="close">&times;</a>
					<h4 class="modal-title" id="myModalLabel"><i class="fa fa-trash fa-fw"></i> Hapus Data Akun</h4>
				</div>
				<div class="modal-body">
					<fieldset>							
						<input type="text" name="id" value="<?php echo $data['id_user']; ?>" hidden>
						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<p><b>Apakah anda yakin ingin menghapus data ini?</b></p>
							</div>
							<div class="col-md-2 col-md-offset-1">
								<i class="fa fa-key fa-5"></i>
							</div>
							<div class="col-md-7">
								<ul class="list-unstyled">
									<li>Nama : <?php echo $data['nama_user']; ?></li>
									<li>Status : 
										<?php 
											if ($data['status'] == "1") {
												echo "On";
											} else if ($data['status'] == "0") {
												echo "Off";
											}
										?>
									</li>
									<li>Terakhir Aktif : <?php echo IndonesiaTgl($data['waktu'])." ".substr($data['waktu'], 11, 9); ?></li>
									<li>IP Address : <?php echo $data['ip_address']; ?></li>
								</ul>
							</div>
						</div>
					</fieldset>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success btn-sm" name="btnDelete">
						<i class="fa fa-check fa-fw"></i> Ya
					</button>
					<a href="../akun/" class="btn btn-danger btn-sm" data-dismiss="modal">
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