<?php session_start();
	$ip_address = $_SERVER['REMOTE_ADDR'];
	
	require 'library/connector.php';
	require 'library/function.php';
	
	db1_open();
	
	//CHECK SESSION & OPEN PAGE | CLEAR SESSION
	if (isset($_SESSION['log_absensi'])) {
		$log = mysql_real_escape_string($_SESSION['log_absensi']);
		$query_logged = mysql_query("SELECT * FROM users WHERE id_user = '$log' AND status = '1'");
		if (mysql_num_rows($query_logged) > 0) {		
			$data_logged = mysql_fetch_array($query_logged);
			$_SESSION['level_absensi'] = $data_logged['level'];				
			$query_update = mysql_query("UPDATE users SET waktu = NOW(), ip_address = '$ip_address' WHERE id_user = '$log'");
			header("location:pages/beranda/"); 		
		}
	} else {
		unset($_SESSION['log_absensi']);
		unset($_SESSION['level_absensi']);
		unset($_SESSION['page']);
		$query_update = mysql_query("UPDATE users SET status = '0' WHERE DATE_ADD(waktu, INTERVAL 30 MINUTE) < NOW()");
		
		//CLEAR VARIABLE
		$nim = $nama = $username = $password = $konfirmasi = $tgl_lahir = "";
		$nimErr = $namaErr = $usernameErr = $passwordErr = $konfirmasiErr = $loginErr = $resetErr = $tgl_lahirErr = $failed = "";		
		 
		// LOGIN
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			include 'controllers/log/login.php';
		}
		
		db1_close();
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
		<link rel="icon" href="assets/img/favicon.ico">
		
		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css" />

		<!-- text fonts -->
		<link rel="stylesheet" href="assets/fonts/fonts.googleapis.com.css" />

		<!-- ace styles -->
		<link rel="stylesheet" href="assets/css/ace.min.css" />

		<!-- Madevelop Studio styles -->
		<link rel="stylesheet" href="assets/css/madevelop.css" />

		<!--[if lte IE 9]>
			<link rel="stylesheet" href="assets/css/ace-part2.min.css" />
		<![endif]-->
		<link rel="stylesheet" href="assets/css/ace-rtl.min.css" />

		<!--[if lte IE 9]>
		  <link rel="stylesheet" href="assets/css/ace-ie.min.css" />
		<![endif]-->

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->

		<!--[if lt IE 9]>
		<script src="assets/js/html5shiv.min.js"></script>
		<script src="assets/js/respond.min.js"></script>
		<![endif]-->
	</head>

	<body class="login-layout light-login">
		<div class="main-container">
			<div class="main-content">
				<div class="row">
					<div class="col-sm-10 col-sm-offset-1">
						<div class="login-container">

							<div class="space-6"></div>

							<div class="position-relative" style="margin-top:50px;">
								<div class="login-box visible widget-box no-border">
									<div class="widget-body">
										<div class="widget-main">	
											<div class="center">
												<h1>
													<img src="assets/img/unhas.png" width="25%" alt="">
													<div class="space"></div>
													<span class="red">ABSENSI</span>
													<h4 class="blue">Fakultas Kedokteran<br>Universitas Hasanuddin</h3>
												</h1>
											</div>
											<h4 class="header grey lighter bigger">
												<i class="ace-icon fa fa-lock blue"></i>
												Login
											</h4>
											<div class="space-6"></div>

											<form role="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
												<fieldset>
													<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input type="text" class="form-control" placeholder="Username" name="username" value="<?php echo $username; ?>" />
															<i class="ace-icon fa fa-user"></i>
														</span>
														<div class="error">
															<?php echo $usernameErr; ?>
														</div>
													</label>

													<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input type="password" class="form-control" placeholder="Password" name="password" value="<?php echo $password; ?>" />
															<i class="ace-icon fa fa-key"></i>
														</span>
														<div class="error">
															<?php echo $passwordErr; ?>
														</div>
													</label>

													<label class="block clearfix">
														<div class="error">
															<?php echo $loginErr; ?>
														</div>
													</label>
													<div class="clearfix">
														<button type="submit" class="btn btn-sm btn-primary btn-block" name="btnLog">
															<i class="ace-icon fa fa-sign-in"></i>
															<span class="bigger-110">Login</span>
														</button>
													</div>

													<div class="space-4"></div>
												</fieldset>
											</form>
										</div><!-- /.widget-main -->

										<div class="toolbar clearfix center">
											<small class="white" id="id-company-text">
												<small><a href ="monitoring/" target="_blank" class="white">&copy;</a> 2016. Absensi FK UNHAS. Developed by <a href ="http://med.unhas.ac.id/ict" target="_blank" class="white">ICT</a>.<small>
											</small>
										</div>
									</div><!-- /.widget-body -->
								</div><!-- /.login-box -->
							</div><!-- /.position-relative -->

							<div class="space-20"></div>
						</div>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.main-content -->
		</div><!-- /.main-container -->
		
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
						}
					?>
					</div>
				</div>
			</div>
		</nav>
		
		<!-- basic scripts -->

		<!--[if !IE]> -->
		<script src="assets/js/jquery.2.1.1.min.js"></script>

		<!-- <![endif]-->

		<!--[if IE]>
			<script src="assets/js/jquery.1.11.1.min.js"></script>
		<![endif]-->

		<!--[if !IE]> -->
		<script type="text/javascript">
			window.jQuery || document.write("<script src='assets/js/jquery.min.js'>"+"<"+"/script>");
		</script>

		<!-- <![endif]-->

		<!--[if IE]>
			<script type="text/javascript">
				window.jQuery || document.write("<script src='assets/js/jquery1x.min.js'>"+"<"+"/script>");
			</script>
		<![endif]-->
		<script type="text/javascript">
			if('ontouchstart' in document.documentElement) document.write("<script src='assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
		</script>
		
		<script src="assets/js/bootstrap.min.js"></script>
	</body>
</html>
<?php
	}
?>