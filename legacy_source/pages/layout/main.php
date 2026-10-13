<?php		
	date_default_timezone_set("Asia/Makassar");	
	$ip_address = $_SERVER['REMOTE_ADDR'];
	
	if (isset($_SESSION['log_absensi']) && isset($_SESSION['level_absensi'])) {
		include '../../library/connector.php';
		include '../../library/function.php';
		include '../../library/time_function.php';
		
		db1_open();
		
		$log = mysql_real_escape_string($_SESSION['log_absensi']);
		$level = mysql_real_escape_string($_SESSION['level_absensi']);
		
		//CEK LOGIN
		$query_login = mysql_query("SELECT * FROM users WHERE id_user = '$log' AND level = '$level' AND status = '1' AND ip_address = '$ip_address'");
		$data_login = mysql_fetch_array($query_login);
		
		if (mysql_num_rows($query_login) > 0) {	
			$query_update2 = mysql_query("UPDATE users SET waktu = NOW(), ip_address = '$ip_address' WHERE id_user = '$log'");
		} else {
			$query_update2 = mysql_query("UPDATE users SET status = '0' WHERE id_user = '$log'");
			unset($_SESSION['log_absensi']);
			unset($_SESSION['level_absensi']);
			unset($_SESSION['page']);
			header("location:../../");
		}
		
		db1_close();
		
		//CLEAR VAR
		$page = $failed = "";
		$homeAct = $fingerAct = $departemenAct = $kelasAct = $dosenAct = $mahasiswaAct = $mkAct = $ruanganAct = $jadwalAct = $jadwalKelasAct = $mahasiswaKelasAct = $laporanAct = $pengaturanAct = $akunAct = $toggleAct = "";		
		
		$nama = $username = $passwordLama = $passwordBaru = $password2Baru = "";
		$error = $namaErr = $usernameErr = $passwordLamaErr = $passwordBaruErr = $password2BaruErr = $settingErr = "";
				
		if (isset($_POST["btnProfile"])) {	
			include '../../controllers/profil/edit.php';
		}	
				
		if (isset($_GET["logout"])) {	
			include '../../controllers/log/logout.php';
		}		
		
		//MENU ACTIVE
		if($_SESSION['page']) {
			switch ($_SESSION['page']){				
				case '' :	
					$homeAct = "active";
				break;
				case 'Beranda' :	
					$homeAct = "active";
				break;
				case 'Finger Print' :	
					$fingerAct = "active";
				break;
				case 'Departemen' :	
					$departemenAct = "active";
				break;
				case 'Kelas' :	
					$kelasAct = "active";
				break;
				case 'Dosen' :	
					$dosenAct = "active";
				break;
				case 'Mahasiswa' :	
					$mahasiswaAct = "active";
				break;
				case 'Mata Kuliah' :	
					$mkAct = "active";
				break;
				case 'Ruang Kuliah' :	
					$ruanganAct = "active";
				break;
				case 'Jadwal Kuliah' :	
					if (isset($_GET['sub']) && isset($_GET['id'])) {
						$jadwalAct = "active open";
						$toggleAct = "dropdown-toggle";
						if ($_GET['sub'] == "jadwal-perkelas") {
							$jadwalKelasAct = "active";
						} else if ($_GET['sub'] == "mahasiswa-perkelas") {
							$mahasiswaKelasAct = "active";
						}
					} else {
						$jadwalAct = "active";
					}
				break;
				case 'Laporan Absen Dosen' :	
					$laporanAct = "active open";
					$laporanDosenAct = "active";
				break;
				case 'Laporan Absen Mahasiswa' :	
					$laporanAct = "active open";
					$laporanMhsAct = "active";
				break;
				case 'Pengaturan' :	
					$pengaturanAct = "active";
				break;
				case 'Akun' :	
					$akunAct = "active";
				break;	
				
				default:
					$homeAct = "active";
				break;
			}
		} else {
			$homeAct = "active";
		}
?>
<!DOCTYPE html>
<html lang="id">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title>ABSENSI - Fakultas Kedokteran UNHAS</title>

		<meta name="description" content="Absensi Fakultas Kedokteran Universitas Hasanuddin" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

		<!-- Favicon -->
		<link rel="icon" href="../../assets/img/favicon.ico">

		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="../../assets/font-awesome/css/font-awesome.min.css" />

		<!-- page specific plugin styles -->
		<link rel="stylesheet" href="../../assets/css/jquery-ui.custom.min.css" />
		<link rel="stylesheet" href="../../assets/css/chosen.min.css" />
		<link rel="stylesheet" href="../../assets/css/datepicker.min.css" />
		<link rel="stylesheet" href="../../assets/css/bootstrap-timepicker.min.css" />
		<link rel="stylesheet" href="../../assets/css/daterangepicker.min.css" />
		<link rel="stylesheet" href="../../assets/css/bootstrap-datetimepicker.min.css" />
		<link rel="stylesheet" href="../../assets/css/colorpicker.min.css" />
		<link rel="stylesheet" href="../../assets/css/bootstrap-duallistbox.min.css" />
		<link rel="stylesheet" href="../../assets/css/bootstrap-multiselect.min.css" />
		<link rel="stylesheet" href="../../assets/css/select2.min.css" />

		<!-- text fonts -->
		<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" />

		<!-- ace styles -->
		<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />

		<!-- Madevelop Studio styles -->
		<link rel="stylesheet" href="../../assets/css/madevelop.css" />

		<!--[if lte IE 9]>
			<link rel="stylesheet" href="../../assets/css/ace-part2.min.css" class="ace-main-stylesheet" />
		<![endif]-->

		<!--[if lte IE 9]>
		  <link rel="stylesheet" href="../../assets/css/ace-ie.min.css" />
		<![endif]-->

		<!-- inline styles related to this page -->

		<!-- ace settings handler -->
		<script src="../../assets/js/ace-extra.min.js"></script>

		<!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

		<!--[if lte IE 8]>
		<script src="../../assets/js/html5shiv.min.js"></script>
		<script src="../../assets/js/respond.min.js"></script>
		<![endif]-->
	</head>

	<body class="no-skin">	
		<div id="navbar" class="navbar navbar-default navbar-fixed-top">
			<script type="text/javascript">
				try{ace.settings.check('navbar' , 'fixed')}catch(e){}
			</script>

			<div class="navbar-container" id="navbar-container">
				<button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
					<span class="sr-only">Toggle sidebar</span>

					<span class="icon-bar"></span>

					<span class="icon-bar"></span>

					<span class="icon-bar"></span>
				</button>

				<div class="navbar-header pull-left">
					<a href="../beranda/" class="navbar-brand">
						<small>
							<img src="../../assets/img/logo.png" alt="logo" class="logo">
							ABSENSI FK UNHAS
						</small>
					</a>
				</div>

				<div class="navbar-buttons navbar-header pull-right" role="navigation">
					<ul class="nav ace-nav">
						<li class="light-blue">
							<a data-toggle="dropdown" href="#" class="dropdown-toggle">
								<img class="nav-user-photo" src="../../assets/img/user.png" alt="Avatar" />
								<span class="user-info">
									<small>Selamat Datang,</small>
									<?php echo $data_login['nama_user']; ?>
								</span>

								<i class="ace-icon fa fa-caret-down"></i>
							</a>

							<ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
								<li>
									<a href="#" data-toggle="modal" data-target="#modalSetting">
										<i class="ace-icon fa fa-user"></i>
										Profil
									</a>
								</li>

								<li class="divider"></li>

								<li>
									<a href="../logout/">
										<i class="ace-icon fa fa-power-off"></i>
										Logout
									</a>
								</li>
							</ul>
						</li>
					</ul>
				</div>
			</div><!-- /.navbar-container -->
		</div>

		<div class="main-container" id="main-container">
			<script type="text/javascript">
				try{ace.settings.check('main-container' , 'fixed')}catch(e){}
			</script>

			<div id="sidebar" class="sidebar                  responsive sidebar-fixed sidebar-scroll">
				<script type="text/javascript">
					try{ace.settings.check('sidebar' , 'fixed')}catch(e){}
				</script>

				<ul class="nav nav-list">
					<li class="<?php echo $homeAct; ?>">
						<a href="../beranda/">
							<i class="menu-icon fa fa-home"></i>
							<span class="menu-text"> Beranda </span>
						</a>

						<b class="arrow"></b>
					</li>
					<li class="<?php echo $fingerAct; ?>">
						<a href="../finger-print/">
							<i class="menu-icon fa fa-calculator"></i>
							<span class="menu-text"> Finger Print </span>
						</a>

						<b class="arrow"></b>
					</li>
					<li class="<?php echo $departemenAct; ?>">
						<a href="../departemen/">
							<i class="menu-icon fa fa-university"></i>
							<span class="menu-text"> Departemen </span>
						</a>

						<b class="arrow"></b>
					</li>
					<li class="<?php echo $kelasAct; ?>">
						<a href="../kelas/">
							<i class="menu-icon fa fa-th-large"></i>
							<span class="menu-text"> Kelas </span>
						</a>

						<b class="arrow"></b>
					</li>
					<li class="<?php echo $dosenAct; ?>">
						<a href="../dosen/">
							<i class="menu-icon fa fa-user-md"></i>
							<span class="menu-text"> Dosen </span>
						</a>

						<b class="arrow"></b>
					</li>
					<li class="<?php echo $mahasiswaAct; ?>">
						<a href="../mahasiswa/">
							<i class="menu-icon fa fa-user"></i>
							<span class="menu-text"> Mahasiswa </span>
						</a>

						<b class="arrow"></b>
					</li>
					<li class="<?php echo $mkAct; ?>">
						<a href="../mata-kuliah/">
							<i class="menu-icon fa fa-book"></i>
							<span class="menu-text"> Mata Kuliah </span>
						</a>

						<b class="arrow"></b>
					</li>
					<li class="<?php echo $ruanganAct; ?>">
						<a href="../ruang-kuliah/">
							<i class="menu-icon fa fa-building-o"></i>
							<span class="menu-text"> Ruang Kuliah </span>
						</a>

						<b class="arrow"></b>
					</li>
					<li class="<?php echo $jadwalAct; ?>">
						<a href="../jadwal-kuliah/" class="<?php echo $toggleAct; ?>">
							<i class="menu-icon fa fa-calendar"></i>
							<span class="menu-text"> Jadwal Kuliah </span>
						</a>

						<b class="arrow"></b>
						<?php
							if (isset($_GET['sub']) && isset($_GET['id'])) {
						?>
						<ul class="submenu">
							<li class="<?php echo $jadwalKelasAct; ?>">
								<a href="../jadwal-kuliah/?sub=jadwal-perkelas&id=<?php echo $_GET['id']; ?>">
									<i class="menu-icon fa fa-table"></i>
									Jadwal Perkelas
								</a>

								<b class="arrow"></b>
							</li>
							<li class="<?php echo $mahasiswaKelasAct; ?>">
								<a href="../jadwal-kuliah/?sub=mahasiswa-perkelas&id=<?php echo $_GET['id']; ?>">
									<i class="menu-icon fa fa-users"></i>
									Mahasiswa Perkelas
								</a>

								<b class="arrow"></b>
							</li>		
						</ul>
						<?php
							}
						?>
					</li>
					<li class="<?php echo $laporanAct; ?>">
						<a href="#" class="dropdown-toggle">
							<i class="menu-icon fa fa-copy"></i>
							<span class="menu-text"> Laporan </span>
						</a>

						<b class="arrow"></b>
						
						<ul class="submenu">
							<li class="<?php echo $laporanDosenAct; ?>">
								<a href="../laporan/?sub=laporan-absen-dosen">
									<i class="menu-icon fa fa-file-text-o"></i>
									Laporan Absen Dosen
								</a>

								<b class="arrow"></b>
							</li>
							<li class="<?php echo $laporanMhsAct; ?>">
								<a href="../laporan/?sub=laporan-absen-mahasiswa">
									<i class="menu-icon fa fa-file-text-o"></i>
									Laporan Absen Mahasiswa
								</a>

								<b class="arrow"></b>
							</li>		
						</ul>
					</li>
					<li class="<?php echo $pengaturanAct; ?>">
						<a href="../pengaturan/">
							<i class="menu-icon fa fa-cogs"></i>
							<span class="menu-text"> Pengaturan </span>
						</a>

						<b class="arrow"></b>
					</li>
					<li class="<?php echo $akunAct; ?>">
						<a href="../akun/">
							<i class="menu-icon fa fa-key"></i>
							<span class="menu-text"> Akun </span>
						</a>

						<b class="arrow"></b>
					</li>
				</ul><!-- /.nav-list -->

				<div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
					<i class="ace-icon fa fa-angle-double-left" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
				</div>

				<script type="text/javascript">
					try{ace.settings.check('sidebar' , 'collapsed')}catch(e){}
				</script>
			</div>

			<div class="main-content">
				<div class="main-content-inner">
					<div class="breadcrumbs" id="breadcrumbs">
						<script type="text/javascript">
							try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
						</script>

						<ul class="breadcrumb">
							<?php
								if($_SESSION['page'] == "Beranda") {
							?>
							<li class="active"><i class="ace-icon fa fa-home home-icon"></i> Beranda</li>
							<?php
								} else if (isset($_GET['sub'])) {
							?>
							<li>
								<i class="ace-icon fa fa-home home-icon"></i>
								<a href="../beranda/">Beranda</a>
							</li>
							<li>
							<?php
									if ($_SESSION['page'] == "Jadwal Kuliah") {
							?>
								<i class="ace-icon fa fa-calendar home-icon"></i>
							<?php
									} else if ($_SESSION['page'] == "Laporan") {
							?>
								<i class="ace-icon fa fa-copy home-icon"></i>
							<?php
									}
							?>
								<a href="../<?php echo str_replace(" ", "-", strtolower($_SESSION['page'])); ?>"><?php echo $_SESSION['page']; ?></a>
							</li>
							<li class="active"><?php echo ucwords(str_replace("-", " ", $_GET['sub'])); ?></li>
							<?php
								} else {
							?>
							<li>
								<i class="ace-icon fa fa-home home-icon"></i>
								<a href="../beranda/">Beranda</a>
							</li>
							<li class="active"><?php echo $_SESSION['page']; ?></li>
							<?php
								}
							?>
						</ul><!-- /.breadcrumb -->
					</div>

					<div class="page-content">
						<div class="ace-settings-container" id="ace-settings-container">
							<div class="btn btn-app btn-xs btn-warning ace-settings-btn" id="ace-settings-btn">
								<i class="ace-icon fa fa-cog bigger-130"></i>
							</div>

							<div class="ace-settings-box clearfix" id="ace-settings-box">
								<div class="pull-left width-50">
									<div class="ace-settings-item">
										<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-navbar" checked />
										<label class="lbl" for="ace-settings-navbar"> Fixed Navbar</label>
									</div>

									<div class="ace-settings-item">
										<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-sidebar" />
										<label class="lbl" for="ace-settings-sidebar"> Fixed Sidebar</label>
									</div>

									<div class="ace-settings-item">
										<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-add-container" />
										<label class="lbl" for="ace-settings-add-container">
											Inside
											<b>.container</b>
										</label>
									</div>
								</div><!-- /.pull-left -->

								<div class="pull-left width-50">
									<div class="ace-settings-item">
										<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-hover" />
										<label class="lbl" for="ace-settings-hover"> Submenu on Hover</label>
									</div>

									<div class="ace-settings-item">
										<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-compact" />
										<label class="lbl" for="ace-settings-compact"> Compact Sidebar</label>
									</div>

									<div class="ace-settings-item">
										<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-highlight" />
										<label class="lbl" for="ace-settings-highlight"> Alt. Active Item</label>
									</div>
								</div><!-- /.pull-left -->
							</div><!-- /.ace-settings-box -->
						</div><!-- /.ace-settings-container -->

						<div class="row">
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->								
									<?php include 'open.php'; ?>
								<!-- PAGE CONTENT ENDS -->
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.page-content -->
				</div>
			</div><!-- /.main-content -->

			<!-- Modal Setting -->
			<form role="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" class="form-horizontal form-label-left">
				<div class="modal fade" id="modalSetting" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header modal-orange">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title" id="myModalLabel">
									<i class="fa fa-cogs fa-fw"></i> Pengaturan
								</h4>
							</div>
							<div class="modal-body">
								<fieldset>			
									<div class="form-group">
										<label class="control-label col-md-5">Nama :</label>
										<div class="col-md-6">
											<input type="text" class="form-control" name="nama" value="<?php echo $data_login['nama_user']; ?>">
										</div>
										<div class="col-lg-7 col-lg-offset-5">
											<span class="error"><?php echo $namaErr; ?></span>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-5">Username :</label>
										<div class="col-md-6">
											<input type="text" class="form-control" name="username" value="<?php echo $data_login['username']; ?>">
										</div>
										<div class="col-lg-7 col-lg-offset-5">
											<span class="error"><?php echo $usernameErr; ?></span>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-5">Password Lama :</label>
										<div class="col-md-6">
											<input type="password" class="form-control" name="passwordLama" value="<?php echo $passwordLama; ?>">
										</div>
										<div class="col-lg-7 col-lg-offset-5">
											<span class="error"><?php echo $passwordLamaErr; ?></span>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-5">Password Baru :</label>
										<div class="col-md-6">
											<input type="password" class="form-control" name="passwordBaru" value="<?php echo $passwordBaru; ?>">
										</div>
										<div class="col-lg-7 col-lg-offset-5">
											<span class="error"><?php echo $passwordBaruErr; ?></span>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-5">Konfirmasi Password Baru :</label>
										<div class="col-md-6">
											<input type="password" class="form-control" name="password2Baru" value="<?php echo $password2Baru; ?>">
										</div>
										<div class="col-lg-7 col-lg-offset-5">
											<span class="error"><?php echo $password2BaruErr; ?></span>
										</div>
									</div>
								</fieldset>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-success btn-sm" name="btnProfile">
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

			<div class="footer">
				<div class="footer-inner">
					<div class="footer-content">
						<span class="bigger-100">
							&copy; 2016. Absensi. Fakultas Kedokteran Universitas Hasanuddin. All Rights Reserved.
						</span>

						&nbsp; &nbsp;
						<span class="action-buttons">
							<a  href="https://twitter.com/Zhamry_Jr" target="_blank">
								<i class="ace-icon fa fa-twitter-square light-blue bigger-150"></i>
							</a>

							<a href="https://www.facebook.com/madevelop.studio" target="_blank">
								<i class="ace-icon fa fa-facebook-square text-primary bigger-150"></i>
							</a>
						</span>
					</div>
				</div>
			</div>

			<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
				<i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
			</a>
		</div><!-- /.main-container -->
		
		<?php
			if ($failed || isset($_GET['success'])) {
		?>
		<nav class="navbar navbar-inverse navbar-fixed-bottom">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-4">
					<?php
						if ($failed) {
					?>
						<div class="alert alert-danger alert-dismissable">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<?php echo $failed; ?>	
						</div>		
					<?php
						} else if (isset($_GET['success'])) {
					?>
						<div class="alert alert-success alert-dismissable">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>				
					<?php
							if ($_GET['success'] == "1") {
								echo "Data berhasil ditambahkan!";
							} else if ($_GET['success'] == "2") {
								echo "Data berhasil diperbaharui!";
							} else if ($_GET['success'] == "3") {
								echo "Data berhasil dihapus!";
							} else if ($_GET['success'] == "4") {
								echo "Data berhasil direset!";
							} else if ($_GET['success'] == "5") {
								echo "Data berhasil disinkronasi!";
							}
					?>							
						</div>	
					<?php
						}
					?>
					</div>
				</div>
			</div>
		</nav>
		<?php
			}
		?>
		
		<!-- basic scripts -->

		<!--[if !IE]> -->
		<script src="../../assets/js/jquery.2.1.1.min.js"></script>

		<!-- <![endif]-->

		<!--[if IE]>
		<script src="../../assets/js/jquery.1.11.1.min.js"></script>
		<![endif]-->

		<!--[if !IE]> -->
		<script type="text/javascript">
			window.jQuery || document.write("<script src='../../assets/js/jquery.min.js'>"+"<"+"/script>");
		</script>

		<!-- <![endif]-->

		<!--[if IE]>
		<script type="text/javascript">
			window.jQuery || document.write("<script src='../../assets/js/jquery1x.min.js'>"+"<"+"/script>");
		</script>
		<![endif]-->
		<script type="text/javascript">
			if('ontouchstart' in document.documentElement) document.write("<script src='../../assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
		</script>
		<script src="../../assets/js/bootstrap.min.js"></script>

		<!-- page specific plugin scripts -->
		<!--[if lte IE 8]>
		  <script src="../../assets/js/excanvas.min.js"></script>
		<![endif]-->
		<script src="../../assets/js/jquery-ui.custom.min.js"></script>
		<script src="../../assets/js/jquery.ui.touch-punch.min.js"></script>
		<script src="../../assets/js/chosen.jquery.min.js"></script>
		<script src="../../assets/js/fuelux.spinner.min.js"></script>
		<script src="../../assets/js/bootstrap-datepicker.min.js"></script>
		<script src="../../assets/js/bootstrap-timepicker.min.js"></script>
		<script src="../../assets/js/moment.min.js"></script>
		<script src="../../assets/js/daterangepicker.min.js"></script>
		<script src="../../assets/js/bootstrap-datetimepicker.min.js"></script>
		<script src="../../assets/js/bootstrap-colorpicker.min.js"></script>
		<script src="../../assets/js/jquery.knob.min.js"></script>
		<script src="../../assets/js/jquery.autosize.min.js"></script>
		<script src="../../assets/js/jquery.inputlimiter.1.3.1.min.js"></script>
		<script src="../../assets/js/jquery.maskedinput.min.js"></script>
		<script src="../../assets/js/jquery.inputmask.js"></script>
		<script src="../../assets/js/bootstrap-tag.min.js"></script>
		<script src="../../assets/js/jquery.dataTables.min.js"></script>
		<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>
		<script src="../../assets/js/dataTables.tableTools.min.js"></script>
		<script src="../../assets/js/dataTables.colVis.min.js"></script>
		<script src="../../assets/js/jquery.bootstrap-duallistbox.min.js"></script>
		<script src="../../assets/js/jquery.raty.min.js"></script>
		<script src="../../assets/js/bootstrap-multiselect.min.js"></script>
		<script src="../../assets/js/select2.min.js"></script>
		<script src="../../assets/js/typeahead.jquery.min.js"></script>

		<!-- ace scripts -->
		<script src="../../assets/js/ace-elements.min.js"></script>
		<script src="../../assets/js/ace.min.js"></script>

		<!-- inline scripts related to this page -->
		
		<script type="text/javascript">
			jQuery(function($) {
				$('#id-disable-check').on('click', function() {
					var inp = $('#form-input-readonly').get(0);
					if(inp.hasAttribute('disabled')) {
						inp.setAttribute('readonly' , 'true');
						inp.removeAttribute('disabled');
						inp.value="This text field is readonly!";
					}
					else {
						inp.setAttribute('disabled' , 'disabled');
						inp.removeAttribute('readonly');
						inp.value="This text field is disabled!";
					}
				});
			
			
				if(!ace.vars['touch']) {
					$('.chosen-select').chosen({allow_single_deselect:true}); 
					//resize the chosen on window resize
			
					$(window)
					.off('resize.chosen')
					.on('resize.chosen', function() {
						$('.chosen-select').each(function() {
							 var $this = $(this);
							 $this.next().css({'width': '100%'});
						})
					}).trigger('resize.chosen');
					//resize chosen on sidebar collapse/expand
					$(document).on('settings.ace.chosen', function(e, event_name, event_val) {
						if(event_name != 'sidebar_collapsed') return;
						$('.chosen-select').each(function() {
							 var $this = $(this);
							 $this.next().css({'width': $this.parent().width()});
						})
					});
			
			
					$('#chosen-multiple-style .btn').on('click', function(e){
						var target = $(this).find('input[type=radio]');
						var which = parseInt(target.val());
						$('#form-field-select-4').addClass('tag-input-style');
					});
				}
			
			
				$('[data-rel=tooltip]').tooltip({container:'body'});
				$('[data-rel=popover]').popover({container:'body'});
				
				$('textarea[class*=autosize]').autosize({append: "\n"});
				$('textarea.limited').inputlimiter({
					remText: '%n character%s remaining...',
					limitText: 'max allowed : %n.'
				});
			
				$.mask.definitions['~']='[+-]';
				$('.input-mask-serial').mask('9999999999999');
				$('.input-mask-activation').mask('*****-****-*****-****-*****-****-*****');
				$('.input-mask-port').mask('9999');
				$('.input-mask-time').mask('99:99');
				$('.input-mask-hour').mask('9');
				$('.input-mask-year').mask('2099/2099');
				$('.input-mask-nim').mask('C99999999');
				$('.input-mask-finger').mask('99999999');
			
			
			
				$( "#input-size-slider" ).css('width','200px').slider({
					value:1,
					range: "min",
					min: 1,
					max: 8,
					step: 1,
					slide: function( event, ui ) {
						var sizing = ['', 'input-sm', 'input-lg', 'input-mini', 'input-small', 'input-medium', 'input-large', 'input-xlarge', 'input-xxlarge'];
						var val = parseInt(ui.value);
						$('#form-field-4').attr('class', sizing[val]).val('.'+sizing[val]);
					}
				});
			
				$( "#input-span-slider" ).slider({
					value:1,
					range: "min",
					min: 1,
					max: 12,
					step: 1,
					slide: function( event, ui ) {
						var val = parseInt(ui.value);
						$('#form-field-5').attr('class', 'col-xs-'+val).val('.col-xs-'+val);
					}
				});
			
			
				
				//"jQuery UI Slider"
				//range slider tooltip example
				$( "#slider-range" ).css('height','200px').slider({
					orientation: "vertical",
					range: true,
					min: 0,
					max: 100,
					values: [ 17, 67 ],
					slide: function( event, ui ) {
						var val = ui.values[$(ui.handle).index()-1] + "";
			
						if( !ui.handle.firstChild ) {
							$("<div class='tooltip right in' style='display:none;left:16px;top:-6px;'><div class='tooltip-arrow'></div><div class='tooltip-inner'></div></div>")
							.prependTo(ui.handle);
						}
						$(ui.handle.firstChild).show().children().eq(1).text(val);
					}
				}).find('span.ui-slider-handle').on('blur', function(){
					$(this.firstChild).hide();
				});
				
				
				$( "#slider-range-max" ).slider({
					range: "max",
					min: 1,
					max: 10,
					value: 2
				});
				
				$( "#slider-eq > span" ).css({width:'90%', 'float':'left', margin:'15px'}).each(function() {
					// read initial values from markup and remove that
					var value = parseInt( $( this ).text(), 10 );
					$( this ).empty().slider({
						value: value,
						range: "min",
						animate: true
						
					});
				});
				
				$("#slider-eq > span.ui-slider-purple").slider('disable');//disable third item
			
				
				$('#id-input-file-1 , #id-input-file-2').ace_file_input({
					no_file:'No File ...',
					btn_choose:'Choose',
					btn_change:'Change',
					droppable:false,
					onchange:null,
					thumbnail:false //| true | large
					//whitelist:'gif|png|jpg|jpeg'
					//blacklist:'exe|php'
					//onchange:''
					//
				});
				//pre-show a file name, for example a previously selected file
				//$('#id-input-file-1').ace_file_input('show_file_list', ['myfile.txt'])
			
			
				$('#id-input-file-3').ace_file_input({
					style:'well',
					btn_choose:'Drop files here or click to choose',
					btn_change:null,
					no_icon:'ace-icon fa fa-cloud-upload',
					droppable:true,
					thumbnail:'small'//large | fit
					//,icon_remove:null//set null, to hide remove/reset button
					/**,before_change:function(files, dropped) {
						//Check an example below
						//or examples/file-upload.html
						return true;
					}*/
					/**,before_remove : function() {
						return true;
					}*/
					,
					preview_error : function(filename, error_code) {
						//name of the file that failed
						//error_code values
						//1 = 'FILE_LOAD_FAILED',
						//2 = 'IMAGE_LOAD_FAILED',
						//3 = 'THUMBNAIL_FAILED'
						//alert(error_code);
					}
			
				}).on('change', function(){
					//console.log($(this).data('ace_input_files'));
					//console.log($(this).data('ace_input_method'));
				});
				
				
				//$('#id-input-file-3')
				//.ace_file_input('show_file_list', [
					//{type: 'image', name: 'name of image', path: 'http://path/to/image/for/preview'},
					//{type: 'file', name: 'hello.txt'}
				//]);
			
				
				
			
				//dynamically change allowed formats by changing allowExt && allowMime function
				$('#id-file-format').removeAttr('checked').on('change', function() {
					var whitelist_ext, whitelist_mime;
					var btn_choose
					var no_icon
					if(this.checked) {
						btn_choose = "Drop images here or click to choose";
						no_icon = "ace-icon fa fa-picture-o";
			
						whitelist_ext = ["jpeg", "jpg", "png", "gif" , "bmp"];
						whitelist_mime = ["image/jpg", "image/jpeg", "image/png", "image/gif", "image/bmp"];
					}
					else {
						btn_choose = "Drop files here or click to choose";
						no_icon = "ace-icon fa fa-cloud-upload";
						
						whitelist_ext = null;//all extensions are acceptable
						whitelist_mime = null;//all mimes are acceptable
					}
					var file_input = $('#id-input-file-3');
					file_input
					.ace_file_input('update_settings',
					{
						'btn_choose': btn_choose,
						'no_icon': no_icon,
						'allowExt': whitelist_ext,
						'allowMime': whitelist_mime
					})
					file_input.ace_file_input('reset_input');
					
					file_input
					.off('file.error.ace')
					.on('file.error.ace', function(e, info) {
						//console.log(info.file_count);//number of selected files
						//console.log(info.invalid_count);//number of invalid files
						//console.log(info.error_list);//a list of errors in the following format
						
						//info.error_count['ext']
						//info.error_count['mime']
						//info.error_count['size']
						
						//info.error_list['ext']  = [list of file names with invalid extension]
						//info.error_list['mime'] = [list of file names with invalid mimetype]
						//info.error_list['size'] = [list of file names with invalid size]
						
						
						/**
						if( !info.dropped ) {
							//perhapse reset file field if files have been selected, and there are invalid files among them
							//when files are dropped, only valid files will be added to our file array
							e.preventDefault();//it will rest input
						}
						*/
						
						
						//if files have been selected (not dropped), you can choose to reset input
						//because browser keeps all selected files anyway and this cannot be changed
						//we can only reset file field to become empty again
						//on any case you still should check files with your server side script
						//because any arbitrary file can be uploaded by user and it's not safe to rely on browser-side measures
					});
				
				});
			
				$('#spinner1').ace_spinner({value:0,min:0,max:200,step:10, btn_up_class:'btn-info' , btn_down_class:'btn-info'})
				.closest('.ace-spinner')
				.on('changed.fu.spinbox', function(){
					//alert($('#spinner1').val())
				}); 
				$('#spinner2').ace_spinner({value:0,min:0,max:10000,step:100, touch_spinner: true, icon_up:'ace-icon fa fa-caret-up bigger-110', icon_down:'ace-icon fa fa-caret-down bigger-110'});
				$('#spinner3').ace_spinner({value:0,min:-100,max:100,step:10, on_sides: true, icon_up:'ace-icon fa fa-plus bigger-110', icon_down:'ace-icon fa fa-minus bigger-110', btn_up_class:'btn-success' , btn_down_class:'btn-danger'});
				$('#spinner4').ace_spinner({value:0,min:-100,max:100,step:10, on_sides: true, icon_up:'ace-icon fa fa-plus', icon_down:'ace-icon fa fa-minus', btn_up_class:'btn-purple' , btn_down_class:'btn-purple'});
			
				//$('#spinner1').ace_spinner('disable').ace_spinner('value', 11);
				//or
				//$('#spinner1').closest('.ace-spinner').spinner('disable').spinner('enable').spinner('value', 11);//disable, enable or change value
				//$('#spinner1').closest('.ace-spinner').spinner('value', 0);//reset to 0
			
			
				//datepicker plugin
				//link
				$('.date-picker').datepicker({
					autoclose: true,
					todayHighlight: true
				})
				//show datepicker when clicking on the icon
				.next().on(ace.click_event, function(){
					$(this).prev().focus();
				});
			
				//or change it into a date range picker
				$('.input-daterange').datepicker({autoclose:true});
			
			
				//to translate the daterange picker, please copy the "examples/daterange-fr.js" contents here before initialization
				$('input[name=date-range-picker]').daterangepicker({
					'applyClass' : 'btn-sm btn-success',
					'cancelClass' : 'btn-sm btn-default',
					locale: {
						applyLabel: 'Apply',
						cancelLabel: 'Cancel',
					}
				})
				.prev().on(ace.click_event, function(){
					$(this).next().focus();
				});
			
			
				$('#timepicker1').timepicker({
					minuteStep: 1,
					showSeconds: true,
					showMeridian: false
				}).next().on(ace.click_event, function(){
					$(this).prev().focus();
				});
				
				$('#date-timepicker1').datetimepicker().next().on(ace.click_event, function(){
					$(this).prev().focus();
				});
				
			
				$('#colorpicker1').colorpicker();
			
				$('#simple-colorpicker-1').ace_colorpicker();
				//$('#simple-colorpicker-1').ace_colorpicker('pick', 2);//select 2nd color
				//$('#simple-colorpicker-1').ace_colorpicker('pick', '#fbe983');//select #fbe983 color
				//var picker = $('#simple-colorpicker-1').data('ace_colorpicker')
				//picker.pick('red', true);//insert the color if it doesn't exist
			
			
				$(".knob").knob();
				
				
				var tag_input = $('#form-field-tags');
				try{
					tag_input.tag(
					  {
						placeholder:tag_input.attr('placeholder'),
						//enable typeahead by specifying the source array
						source: ace.vars['US_STATES'],//defined in ace.js >> ace.enable_search_ahead
						/**
						//or fetch data from database, fetch those that match "query"
						source: function(query, process) {
						  $.ajax({url: 'remote_source.php?q='+encodeURIComponent(query)})
						  .done(function(result_items){
							process(result_items);
						  });
						}
						*/
					  }
					)
			
					//programmatically add a new
					var $tag_obj = $('#form-field-tags').data('tag');
					$tag_obj.add('Programmatically Added');
				}
				catch(e) {
					//display a textarea for old IE, because it doesn't support this plugin or another one I tried!
					tag_input.after('<textarea id="'+tag_input.attr('id')+'" name="'+tag_input.attr('name')+'" rows="3">'+tag_input.val()+'</textarea>').remove();
					//$('#form-field-tags').autosize({append: "\n"});
				}
				
				
				/////////
				$('#modal-form input[type=file]').ace_file_input({
					style:'well',
					btn_choose:'Drop files here or click to choose',
					btn_change:null,
					no_icon:'ace-icon fa fa-cloud-upload',
					droppable:true,
					thumbnail:'large'
				})
				
				//chosen plugin inside a modal will have a zero width because the select element is originally hidden
				//and its width cannot be determined.
				//so we set the width after modal is show
				$('#modal-form').on('shown.bs.modal', function () {
					if(!ace.vars['touch']) {
						$(this).find('.chosen-container').each(function(){
							$(this).find('a:first-child').css('width' , '210px');
							$(this).find('.chosen-drop').css('width' , '210px');
							$(this).find('.chosen-search input').css('width' , '200px');
						});
					}
				})
				/**
				//or you can activate the chosen plugin after modal is shown
				//this way select element becomes visible with dimensions and chosen works as expected
				$('#modal-form').on('shown', function () {
					$(this).find('.modal-chosen').chosen();
				})
				*/
			
				
				
				$(document).one('ajaxloadstart.page', function(e) {
					$('textarea[class*=autosize]').trigger('autosize.destroy');
					$('.limiterBox,.autosizejs').remove();
					$('.daterangepicker.dropdown-menu,.colorpicker.dropdown-menu,.bootstrap-datetimepicker-widget.dropdown-menu').remove();
				});
				
				
			    var demo1 = $('select[name="duallistbox[]"]').bootstrapDualListbox({infoTextFiltered: '<span class="label label-purple label-lg">Filtered</span>'});
				var container1 = demo1.bootstrapDualListbox('getContainer');
				container1.find('.btn').addClass('btn-white btn-info btn-bold');
			
			});
		</script>
		<script>
	function proses_laporan() {
		alert();
    	/*if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("dataTabel").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "<?php echo base_url(); ?>rkpd/filter/"+vTahun+"/"+jns+"/"+vSkpd, true);
        xmlhttp.send();
		*/
	}
</script>
		<script type="text/javascript">
			jQuery(function($) {
				//initiate dataTables plugin
				var oTable1 = 
				$('#dynamic-table')
				//.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
				.dataTable( {
					bAutoWidth: false,
					"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
					"aoColumns": [
					  { "bSortable": false },
					  null, null,null, null, null,
					  { "bSortable": false }
					],
					"aaSorting": [],
			
					//,
					//"sScrollY": "200px",
					//"bPaginate": false,
			
					//"sScrollX": "100%",
					//"sScrollXInner": "120%",
					//"bScrollCollapse": true,
					//Note: if you are applying horizontal scrolling (sScrollX) on a ".table-bordered"
					//you may want to wrap the table inside a "div.dataTables_borderWrap" element
					
					///////Additional for page laporan to show all records
					<?php
					if ($_SESSION['page'] == "Laporan") {
					echo '"iDisplayLength": -1';
					}
					?>
			    } );
				//oTable1.fnAdjustColumnSizing();
			
			
				//TableTools settings
				TableTools.classes.container = "btn-group btn-overlap";
				TableTools.classes.print = {
					"body": "DTTT_Print",
					"info": "tableTools-alert gritter-item-wrapper gritter-info gritter-center white",
					"message": "tableTools-print-navbar"
				}
			
				//initiate TableTools extension
				var tableTools_obj = new $.fn.dataTable.TableTools( oTable1, {
					"sSwfPath": "../../assets/swf/copy_csv_xls_pdf.swf",
					
					"sRowSelector": "td:not(:last-child)",
					"sRowSelect": "multi",
					"fnRowSelected": function(row) {
						//check checkbox when row is selected
						try { $(row).find('input[type=checkbox]').get(0).checked = true }
						catch(e) {}
					},
					"fnRowDeselected": function(row) {
						//uncheck checkbox
						try { $(row).find('input[type=checkbox]').get(0).checked = false }
						catch(e) {}
					},
			
					"sSelectedClass": "success",
			        "aButtons": [
						{
							"sExtends": "copy",
							"sToolTip": "Copy to clipboard",
							"sButtonClass": "btn btn-white btn-primary btn-bold",
							"sButtonText": "<i class='fa fa-copy bigger-110 pink'></i>",
							"fnComplete": function() {
								this.fnInfo( '<h3 class="no-margin-top smaller">Table copied</h3>\
									<p>Copied '+(oTable1.fnSettings().fnRecordsTotal())+' row(s) to the clipboard.</p>',
									1500
								);
							}
						},
						
						{
							"sExtends": "csv",
							"sToolTip": "Export to CSV",
							"sButtonClass": "btn btn-white btn-primary  btn-bold",
							"sButtonText": "<i class='fa fa-file-excel-o bigger-110 green'></i>"
						},
						
						{
							"sExtends": "pdf",
							"sToolTip": "Export to PDF",
							"sButtonClass": "btn btn-white btn-primary  btn-bold",
							"sButtonText": "<i class='fa fa-file-pdf-o bigger-110 red'></i>"
						},
						
						{
							"sExtends": "print",
							"sToolTip": "Print view",
							"sButtonClass": "btn btn-white btn-primary  btn-bold",
							"sButtonText": "<i class='fa fa-print bigger-110 grey'></i>",
							
							"sMessage": "<div class='navbar navbar-default'><div class='navbar-header pull-left'><a class='navbar-brand' href='#'><small><?php echo $_SESSION['page']; ?></small></a></div></div>",
							
							"sInfo": "<h3 class='no-margin-top'>Print view</h3>\
									  <p>Please use your browser's print function to\
									  print this table.\
									  <br />Press <b>escape</b> when finished.</p>",
						}
			        ]
			    } );
				//we put a container before our table and append TableTools element to it
			    $(tableTools_obj.fnContainer()).appendTo($('.tableTools-container'));
				
				//also add tooltips to table tools buttons
				//addding tooltips directly to "A" buttons results in buttons disappearing (weired! don't know why!)
				//so we add tooltips to the "DIV" child after it becomes inserted
				//flash objects inside table tools buttons are inserted with some delay (100ms) (for some reason)
				setTimeout(function() {
					$(tableTools_obj.fnContainer()).find('a.DTTT_button').each(function() {
						var div = $(this).find('> div');
						if(div.length > 0) div.tooltip({container: 'body'});
						else $(this).tooltip({container: 'body'});
					});
				}, 200);
				
				
				
				//ColVis extension
				var colvis = new $.fn.dataTable.ColVis( oTable1, {
					"buttonText": "<i class='fa fa-search'></i>",
					"aiExclude": [0, 6],
					"bShowAll": true,
					//"bRestore": true,
					"sAlign": "right",
					"fnLabel": function(i, title, th) {
						return $(th).text();//remove icons, etc
					}
					
				}); 
				
				//style it
				$(colvis.button()).addClass('btn-group').find('button').addClass('btn btn-white btn-info btn-bold')
				
				//and append it to our table tools btn-group, also add tooltip
				$(colvis.button())
				.prependTo('.tableTools-container .btn-group')
				.attr('title', 'Show/hide columns').tooltip({container: 'body'});
				
				//and make the list, buttons and checkboxed Ace-like
				$(colvis.dom.collection)
				.addClass('dropdown-menu dropdown-light dropdown-caret dropdown-caret-right')
				.find('li').wrapInner('<a href="javascript:void(0)" />') //'A' tag is required for better styling
				.find('input[type=checkbox]').addClass('ace').next().addClass('lbl padding-8');
			
			
				
				/////////////////////////////////
				//table checkboxes
				$('th input[type=checkbox], td input[type=checkbox]').prop('checked', false);
				
				//select/deselect all rows according to table header checkbox
				$('#dynamic-table > thead > tr > th input[type=checkbox]').eq(0).on('click', function(){
					var th_checked = this.checked;//checkbox inside "TH" table header
					
					$(this).closest('table').find('tbody > tr').each(function(){
						var row = this;
						if(th_checked) tableTools_obj.fnSelect(row);
						else tableTools_obj.fnDeselect(row);
					});
				});
				
				//select/deselect a row when the checkbox is checked/unchecked
				$('#dynamic-table').on('click', 'td input[type=checkbox]' , function(){
					var row = $(this).closest('tr').get(0);
					if(!this.checked) tableTools_obj.fnSelect(row);
					else tableTools_obj.fnDeselect($(this).closest('tr').get(0));
				});
				
			
				
				
					$(document).on('click', '#dynamic-table .dropdown-toggle', function(e) {
					e.stopImmediatePropagation();
					e.stopPropagation();
					e.preventDefault();
				});
				
				
				//And for the first simple table, which doesn't have TableTools or dataTables
				//select/deselect all rows according to table header checkbox
				var active_class = 'active';
				$('#simple-table > thead > tr > th input[type=checkbox]').eq(0).on('click', function(){
					var th_checked = this.checked;//checkbox inside "TH" table header
					
					$(this).closest('table').find('tbody > tr').each(function(){
						var row = this;
						if(th_checked) $(row).addClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', true);
						else $(row).removeClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', false);
					});
				});
				
				//select/deselect a row when the checkbox is checked/unchecked
				$('#simple-table').on('click', 'td input[type=checkbox]' , function(){
					var $row = $(this).closest('tr');
					if(this.checked) $row.addClass(active_class);
					else $row.removeClass(active_class);
				});
			
				
			
				/********************************/
				//add tooltip for small view action buttons in dropdown menu
				$('[data-rel="tooltip"]').tooltip({placement: tooltip_placement});
				
				//tooltip placement on right or left
				function tooltip_placement(context, source) {
					var $source = $(source);
					var $parent = $source.closest('table')
					var off1 = $parent.offset();
					var w1 = $parent.width();
			
					var off2 = $source.offset();
					//var w2 = $source.width();
			
					if( parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2) ) return 'right';
					return 'left';
				}
			
			})	
			jQuery(function($) {
				//initiate dataTables plugin
				var oTable1 = 
				$('#dynamic-table2')
				//.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
				.dataTable( {
					bAutoWidth: false,
					"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
					"aoColumns": [
					  { "bSortable": false },
					  null, null,null, null, null,
					  { "bSortable": false }
					],
					"aaSorting": [],
			
					//,
					//"sScrollY": "200px",
					//"bPaginate": false,
			
					//"sScrollX": "100%",
					//"sScrollXInner": "120%",
					//"bScrollCollapse": true,
					//Note: if you are applying horizontal scrolling (sScrollX) on a ".table-bordered"
					//you may want to wrap the table inside a "div.dataTables_borderWrap" element
			
					"iDisplayLength": -1
			    } );
				//oTable1.fnAdjustColumnSizing();
			
			
				//initiate TableTools extension
				var tableTools_obj = new $.fn.dataTable.TableTools( oTable1, {
					"sRowSelector": "td:not(:last-child)",
					"sRowSelect": "multi",
					"fnRowSelected": function(row) {
						//check checkbox when row is selected
						try { $(row).find('input[type=checkbox]').get(0).checked = true }
						catch(e) {}
					},
					"fnRowDeselected": function(row) {
						//uncheck checkbox
						try { $(row).find('input[type=checkbox]').get(0).checked = false }
						catch(e) {}
					},
			
					"sSelectedClass": "success"
			    } );
				//we put a container before our table and append TableTools element to it
			    $(tableTools_obj.fnContainer()).appendTo($('.tableTools-container'));
				
				//also add tooltips to table tools buttons
				//addding tooltips directly to "A" buttons results in buttons disappearing (weired! don't know why!)
				//so we add tooltips to the "DIV" child after it becomes inserted
				//flash objects inside table tools buttons are inserted with some delay (100ms) (for some reason)
				setTimeout(function() {
					$(tableTools_obj.fnContainer()).find('a.DTTT_button').each(function() {
						var div = $(this).find('> div');
						if(div.length > 0) div.tooltip({container: 'body'});
						else $(this).tooltip({container: 'body'});
					});
				}, 200);
				
				
				
				//ColVis extension
				var colvis = new $.fn.dataTable.ColVis( oTable1, {
					"buttonText": "<i class='fa fa-search'></i>",
					"aiExclude": [0, 6],
					"bShowAll": true,
					//"bRestore": true,
					"sAlign": "right",
					"fnLabel": function(i, title, th) {
						return $(th).text();//remove icons, etc
					}
					
				}); 
				
				//style it
				$(colvis.button()).addClass('btn-group').find('button').addClass('btn btn-white btn-info btn-bold')
				
				//and append it to our table tools btn-group, also add tooltip
				$(colvis.button())
				.prependTo('.tableTools-container .btn-group')
				.attr('title', 'Show/hide columns').tooltip({container: 'body'});
				
				//and make the list, buttons and checkboxed Ace-like
				$(colvis.dom.collection)
				.addClass('dropdown-menu dropdown-light dropdown-caret dropdown-caret-right')
				.find('li').wrapInner('<a href="javascript:void(0)" />') //'A' tag is required for better styling
				.find('input[type=checkbox]').addClass('ace').next().addClass('lbl padding-8');
			
			
				
				/////////////////////////////////
				//table checkboxes
				$('th input[type=checkbox], td input[type=checkbox]').prop('checked', false);
				
				//select/deselect all rows according to table header checkbox
				$('#dynamic-table2 > thead > tr > th input[type=checkbox]').eq(0).on('click', function(){
					var th_checked = this.checked;//checkbox inside "TH" table header
					
					$(this).closest('table').find('tbody > tr').each(function(){
						var row = this;
						if(th_checked) tableTools_obj.fnSelect(row);
						else tableTools_obj.fnDeselect(row);
					});
				});
				
				//select/deselect a row when the checkbox is checked/unchecked
				$('#dynamic-table2').on('click', 'td input[type=checkbox]' , function(){
					var row = $(this).closest('tr').get(0);
					if(!this.checked) tableTools_obj.fnSelect(row);
					else tableTools_obj.fnDeselect($(this).closest('tr').get(0));
				});
				
					$(document).on('click', '#dynamic-table2 .dropdown-toggle', function(e) {
					e.stopImmediatePropagation();
					e.stopPropagation();
					e.preventDefault();
				});			
			})		
		</script>
	</body>
</html>
<?php
	} else {
		header("location:../../");
	}
?>