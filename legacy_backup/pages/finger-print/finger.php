<?php	
	if (isset($_SESSION['log_absensi']) && isset($_SESSION['level_absensi'])) {
		$serial = $serialErr = "";
		$activation = $activationErr = "";
		$kode = $kodeErr = "";
		$no_mesin = $no_mesinErr = "";
		$ip_address = $ip_addressErr = "";
		$port = $portErr = "";
		$error = "";
		
		/* Sync
		-----------------*/
		if (isset($_POST["btnSync"])) {		
			include '../../controllers/finger/sync.php';
		}	
		
		/* Add
		-----------------*/
		if (isset($_POST["btnAdd"])) {		
			include '../../controllers/finger/add.php';
		}	
		
		/* Delete
		-----------------*/
		if (isset($_POST["btnDelete"])) {		
			include '../../controllers/finger/delete.php';
		}
		
		/* Delete Many
		-----------------*/
		if (isset($_POST["btnMany"])) {		
			include '../../controllers/finger/many.php';
		}
		
		/* GET ID
		-----------------*/
		db1_open();
		if (isset($_GET['delete'])) {
			$id = mysql_real_escape_string($_GET['delete']);
		}
		db1_close();
?>

<div class="page-header">
	<h1><i class="ace-icon fa fa-calculator"></i> Finger Print</h1>
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
						<h4 class="widget-title">Data Finger Print</h4>

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
								<button class="btn btn-sm btn-default bigger" data-toggle="modal" data-target="#modalSync">
									<i class="ace-icon fa fa-refresh bigger-110"></i> Sync
								</button>
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
										<th class="center">Serial Number</th>
										<th class="center">Activation Code</th>
										<th class="center">IP Address</th>
										<th class="center">Kode</th>
										<th class="center col-md-1">Pilihan</th>
									</tr>
								</thead>

								<tbody>
								<?php
									db1_open();									
									$no = 0;
									$query = mysql_query("SELECT * FROM mesin ORDER BY ip_address ASC");
									while ($data = mysql_fetch_array($query)) {
										$no++;
								?>
									<tr>
										<td class="center">
											<label class="pos-rel">
												<input type="checkbox" class="ace" name="id[]" value="<?php echo $data['id_mesin']; ?>" form="many" />
												<span class="lbl"></span>
											</label>
										</td>
										<td class="center"><?php echo $no; ?></td>
										<td class="center"><?php echo $data['serial']; ?></td>
										<td><?php echo $data['activation']; ?></td>
										<td class="center"><?php echo $data['ip_address']; ?></td>
										<td class="center"><?php echo "Mesin ".$data['kode']; ?></td>
										<td class="center">									
											<div class="hidden-sm hidden-xs action-buttons">
												<div class="btn-group">
													<a class="btn btn-xs btn-danger" href="?delete=<?php echo md5($data['id_mesin']); ?>" data-rel="tooltip" title="Hapus" data-toggle="tooltip">
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
															<a href="#" class="tooltip-error" data-rel="tooltip" title="Hapus" data-toggle="modal" data-target="#modalDelete<?php echo $data['id_jHari']; ?>">
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
<!-- Modal Sync -->
<form role="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" class="form-horizontal form-label-left">
	<div class="modal fade" id="modalSync" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-red">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">
						<i class="fa fa-refresh fa-fw"></i> Sync Data Finger Print
					</h4>
				</div>
				<div class="modal-body">
					<fieldset>							
						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<p><b>Apakah anda yakin ingin melakukan sinkronasi data mesin dengan aplikasi Fingerspot?</b></p>
								<br>
								<p class="red">NB:</p>
								<p class="red">Sinkronasi akan menghapus data finger print yang ada pada Sistem Absensi!</p>
							</div>				
						</div>		
					</fieldset>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success btn-sm" name="btnSync">
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

<!-- Modal Tambah -->
<form role="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" class="form-horizontal form-label-left">
	<div class="modal fade" id="modalAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header modal-green">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">
						<i class="fa fa-plus fa-fw"></i> Tambah Data Finger Print
					</h4>
				</div>
				<div class="modal-body">
					<fieldset>	
						<div class="form-group">
							<label class="control-label col-md-3">Serial Number <span class="red">*</span> :</label>
							<div class="col-md-3">
								<input type="text" class="form-control input-mask-serial" name="serial" value="<?php echo $serial; ?>">
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $serialErr; ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Activation Code <span class="red">*</span> :</label>
							<div class="col-md-6">
								<input type="text" class="form-control input-mask-activation" name="activation" value="<?php echo $activation; ?>">
							</div>
							<div class="col-lg-9 col-lg-offset-3">
								<span class="error"><?php echo $activationErr; ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Kode Mesin <span class="red">*</span> :</label>
							<div class="col-md-1">
								<input type="text" class="form-control" name="kode" value="<?php echo $kode; ?>">
							</div>
							<label class="control-label col-md-4">No. Mesin <span class="red">*</span> :</label>
							<div class="col-md-1">
								<input type="text" class="form-control" name="no_mesin" value="<?php echo $no_mesin; ?>">
							</div>
							<div class="col-lg-4 col-lg-offset-3">
								<span class="error"><?php echo $kodeErr; ?></span>
							</div>
							<div class="col-lg-4 col-md-offset-1">
								<span class="error"><?php echo $no_mesinErr; ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">IP Address <span class="red">*</span> :</label>
							<div class="col-md-3">
								<input type="text" class="form-control" name="ip_address" value="<?php echo $ip_address; ?>">
							</div>
							<label class="control-label col-md-2">Port <span class="red">*</span> :</label>
							<div class="col-md-1">
								<input type="text" class="form-control input-mask-port" name="port" value="<?php echo $port; ?>">
							</div>
							<div class="col-lg-4 col-lg-offset-3">
								<span class="error"><?php echo $ip_addressErr; ?></span>
							</div>
							<div class="col-lg-3 col-md-offset-1">
								<span class="error"><?php echo $portErr; ?></span>
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
						<i class="fa fa-trash fa-fw"></i> Hapus Data Finger Print
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
			if (isset($_GET['delete'])) {
				$query = mysql_query("SELECT * FROM mesin WHERE MD5(id_mesin) = '$id'");
				if (mysql_num_rows($query) > 0) {
					$data = mysql_fetch_array($query);	
?>
<!-- Modal Delete -->
<form role="form" action="?delete=<?php echo md5($data['id_mesin']); ?>" method="post" class="form-horizontal form-label-left">
	<div class="modal fade in" style="display: block; padding-right: 17px; overflow: auto;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
		<div style="height: 1000px;" class="modal-backdrop fade in"></div>
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-red">
					<a href="../finger-print/" class="close">&times;</a>
					<h4 class="modal-title" id="myModalLabel"><i class="fa fa-trash fa-fw"></i> Hapus Data Finger Print</h4>
				</div>
				<div class="modal-body">
					<fieldset>							
						<input type="text" name="id" value="<?php echo $data['id_mesin']; ?>" hidden>
						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<p><b>Apakah anda yakin ingin menghapus data ini?</b></p>
							</div>
							<div class="col-md-2 col-md-offset-1">
								<i class="fa fa-calculator fa-5"></i>
							</div>
							<div class="col-md-9">
								<ul class="list-unstyled">
									<li>Serial Number : <?php echo $data['serial']; ?></li>
									<li>Activation Code : <?php echo $data['activation']; ?></li>
									<li>Kode Mesin : <?php echo $data['kode']; ?></li>
									<li>No. Mesin : <?php echo $data['no_mesin']; ?></li>
									<li>IP Address : <?php echo $data['ip_address']; ?></li>
									<li>Port : <?php echo $data['port']; ?></li>
								</ul>
							</div>
						</div>
					</fieldset>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success btn-sm" name="btnDelete">
						<i class="fa fa-check fa-fw"></i> Ya
					</button>
					<a href="../finger-print/" class="btn btn-danger btn-sm" data-dismiss="modal">
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