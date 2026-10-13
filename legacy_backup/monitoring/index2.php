<?php		
	date_default_timezone_set("Asia/Makassar");	
	include '../library/connector.php';
	include '../library/function.php';
	include '../library/time_function.php';
	
	$tanggal = date("Y-m-d");
	echo strtotime($jam = date("H:i"));
	
	echo "<meta http-equiv='refresh' content='600; url='>";
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title>ABSENSI - Fakultas Kedokteran UNHAS</title>

		<!-- Favicon -->
		<link rel="icon" href="../assets/img/favicon.ico">

		<meta name="description" content="top menu &amp; navigation" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="../assets/font-awesome/css/font-awesome.min.css" />

		<!-- page specific plugin styles -->

		<!-- text fonts -->
		<link rel="stylesheet" href="../assets/fonts/fonts.googleapis.com.css" />

		<!-- ace styles -->
		<link rel="stylesheet" href="../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />

		<!-- Madevelop Studio styles -->
		<link rel="stylesheet" href="../assets/css/madevelop.css" />

		<!--[if lte IE 9]>
			<link rel="stylesheet" href="../assets/css/ace-part2.min.css" class="ace-main-stylesheet" />
		<![endif]-->

		<!--[if lte IE 9]>
		  <link rel="stylesheet" href="../assets/css/ace-ie.min.css" />
		<![endif]-->

		<!-- inline styles related to this page -->

		<!-- ace settings handler -->
		<script src="../assets/js/ace-extra.min.js"></script>

		<!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

		<!--[if lte IE 8]>
		<script src="../assets/js/html5shiv.min.js"></script>
		<script src="../assets/js/respond.min.js"></script>
		<![endif]-->
	</head>

	<body class="no-skin">
		<div id="navbar" class="navbar navbar-default    navbar-collapse       h-navbar navbar-fixed-top">
			<script type="text/javascript">
				try{ace.settings.check('navbar' , 'fixed')}catch(e){}
			</script>

			<div class="navbar-container" id="navbar-container">
				<div class="navbar-header pull-left">
					<a href="./" class="navbar-brand">
						<small>
							<img src="../assets/img/logo.png" alt="logo" class="logo">
							ABSENSI FK UNHAS
						</small>
					</a>
				</div>

				<nav role="navigation" class="navbar-menu pull-right">
					<ul class="nav navbar-nav">
						<li>
							<a href="#" class="center">
								<i class="ace-icon fa fa-calendar-o bigger-130 icon-animated-vertical"></i>
								<big><?php echo TanggalIndo($tanggal); ?></big>
							</a>
						</li>
					</ul>
				</nav>
			</div><!-- /.navbar-container -->
		</div>

		<div class="main-container" id="main-container">
			<script type="text/javascript">
				try{ace.settings.check('main-container' , 'fixed')}catch(e){}
			</script>

			<div id="sidebar" class="sidebar      h-sidebar                navbar-collapse collapse">
				<script type="text/javascript">
					try{ace.settings.check('sidebar' , 'fixed')}catch(e){}
				</script>

				<script type="text/javascript">
					try{ace.settings.check('sidebar' , 'collapsed')}catch(e){}
				</script>
			</div>

			<div class="main-content">
				<div class="main-content-inner">
					<div class="page-content">
						<div class="page-header">
							<h1>
								<p class="center">JADWAL KEGIATAN AKADEMIK</p>
								<small class="pull-left"><i class="ace-icon fa fa-link"></i> www.med.unhas.ac.id</small>
								<small class="pull-right"><i class="ace-icon fa fa-envelope"></i> prodis1_fkuh@gmail.com</small>
								<div class="space-4"></div>
							</h1>
						</div><!-- /.page-header -->

						<div class="row">
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->
								<div class="widget-box widget-color-blue">
									<div class="widget-body">	
										<div class="table-responsive">
											<table id="dynamic-table" class="table table-striped table-bordered table-hover">
												<thead>
													<tr>
														<th class="center">Waktu</th>
														<th class="center">Blok</th>
														<th class="center">Kelas</th>
														<th class="center">Ruangan</th>
														<th class="center">Kegiatan</th>
														<th class="center">Dosen</th>
														<th class="center col-md-1">Status</th>
													</tr>
												</thead>

												<tbody>
												<?php
													//AND DATE_ADD(CAST(jadwal_jam.jam_masuk AS TIME), INTERVAL -(pengaturan.menit_perjam) MINUTE) < NOW()  AND DATE_ADD(CAST(jadwal_jam.jam_masuk AS TIME), INTERVAL ((jadwal_jam.jml_jam * pengaturan.menit_perjam) + ((jadwal_jam.jml_jam - 1) * pengaturan.menit_pergantian)) MINUTE) > NOW()
													db1_open();									
													$no = $keg = $jam_keluar = 0; 
													$query = mysql_query("SELECT jadwal_jam.*, pengaturan.*, DATE_ADD(CAST(jadwal_jam.jam_masuk AS TIME), INTERVAL ((jadwal_jam.jml_jam * pengaturan.menit_perjam) + ((jadwal_jam.jml_jam - 1) * pengaturan.menit_pergantian)) MINUTE) AS jam_keluar, mk.mk, kelas.kelas, ruangan.ruangan, jadwal_jam.kegiatan FROM jadwal_jam LEFT JOIN jadwal_hari ON jadwal_jam.id_jHari = jadwal_hari.id_jHari LEFT JOIN jadwal_kelas ON jadwal_hari.id_jKelas = jadwal_kelas.id_jKelas LEFT JOIN jadwal ON jadwal_kelas.id_jadwal = jadwal.id_jadwal LEFT JOIN pengaturan ON jadwal.id_pengaturan = pengaturan.id_pengaturan LEFT JOIN mk ON jadwal.id_mk = mk.id_mk LEFT JOIN kelas ON jadwal_kelas.id_kelas = kelas.id_kelas LEFT JOIN ruangan ON jadwal_jam.id_ruangan = ruangan.id_ruangan WHERE jadwal_hari.tanggal = '$tanggal' ORDER BY jadwal_jam.jam_masuk ASC");
													while ($data = mysql_fetch_array($query)) {
														$no++;
														if ($data['jml_jam'] < 1) {
															$n = 1;
														} else {
															$n = $data['jml_jam'];
														}
														
														for ($i = 0; $i < $n; $i++) {
															if ($keg != $data['id_jJam']) {
																$jam_masuk = $data['jam_masuk'];
																$keg = $data['id_jJam'];
															} else {
																$jam_masuk = timeCounter($jam_keluar, $data['menit_pergantian']);
															}
															$jam_keluar = timeCounter($jam_masuk, $data['menit_perjam']);
														}
														
														if (strtotime($jam_keluar) > strtotime($jam)) {
												?>
													<tr>
														<td class="center"><?php echo $data['jam_masuk']."-".$jam_keluar; ?></td>
														<td class="center"><?php echo $data['mk']; ?></td>
														<td class="center"><?php echo $data['kelas']; ?></td>
														<td class="center"><?php echo $data['ruangan']; ?></td>
														<td><?php echo $data['kegiatan']; ?></td>
														<td>
														<?php
															$query_dosen = mysql_query("SELECT * FROM jadwal_dosen LEFT JOIN dosen ON jadwal_dosen.id_dosen = dosen.id_dosen WHERE jadwal_dosen.id_jJam = '$data[id_jJam]'");
															while ($data_dosen = mysql_fetch_array($query_dosen)) {
																echo $data_dosen['nama_dosen']."<br>";
															}
														?>
														</td>
														<td class="center">
														
														</td>
													</tr>
												<?php
														}
													}
													
													db1_close();
												?>
												</tbody>
											</table>										
										</div>
									</div>
								</div>

								<!-- PAGE CONTENT ENDS -->
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.page-content -->
				</div>
			</div><!-- /.main-content -->

			<div class="footer" >
				<div class="footer-inner">
					<div class="footer-content">
						<span class="bigger-50">
						<h4>
							<MARQUEE direction="left" scrollamount="6"  style="color: #ffffff; background-color: #3c8dbc"> <b>Pengumuman</b>: Disampaikan kepada mahasiswa FK-UNHAS Angkatan 2016 silahkan mengisi data diri di : <font color=#090BCF>http://med.unhas.ac.id/mahasiswa </font>
							dengan cara klik registrasi, setelah melakukan registrasi silahkan login kembali untuk mengisi data diri 
							<font color="red">* file foto harus bertype jpeg </font>						
							</MARQUEE>
						</h4>
						</span>
						<span class="bigger-100">
							&copy; 2016. Absensi. Fakultas Kedokteran Universitas Hasanuddin. Developed by <a href ="http://med.unhas.ac.id/ict" target="_blank" class="red bolder">ICT</a>.
						</span>

						<!--&nbsp; &nbsp;
						<span class="action-buttons">
							<a  href="https://twitter.com/Zhamry_Jr" target="_blank">
								<i class="ace-icon fa fa-twitter-square light-blue bigger-150"></i>
							</a>

							<a href="https://www.facebook.com/madevelop.studio" target="_blank">
								<i class="ace-icon fa fa-facebook-square text-primary bigger-150"></i>
							</a>
						</span>
						-->
					</div>
				</div>
			</div>

			<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
				<i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
			</a>
		</div><!-- /.main-container -->

		<!-- basic scripts -->

		<!--[if !IE]> -->
		<script src="../assets/js/jquery.2.1.1.min.js"></script>

		<!-- <![endif]-->

		<!--[if IE]>
<script src="../assets/js/jquery.1.11.1.min.js"></script>
<![endif]-->

		<!--[if !IE]> -->
		<script type="text/javascript">
			window.jQuery || document.write("<script src='../assets/js/jquery.min.js'>"+"<"+"/script>");
		</script>

		<!-- <![endif]-->

		<!--[if IE]>
<script type="text/javascript">
 window.jQuery || document.write("<script src='../assets/js/jquery1x.min.js'>"+"<"+"/script>");
</script>
<![endif]-->
		<script type="text/javascript">
			if('ontouchstart' in document.documentElement) document.write("<script src='../assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
		</script>
		<script src="../assets/js/bootstrap.min.js"></script>

		<!-- page specific plugin scripts -->

		<!-- ace scripts -->
		<script src="../assets/js/ace-elements.min.js"></script>
		<script src="../assets/js/ace.min.js"></script>

		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			jQuery(function($) {
			 var $sidebar = $('.sidebar').eq(0);
			 if( !$sidebar.hasClass('h-sidebar') ) return;
			
			 $(document).on('settings.ace.top_menu' , function(ev, event_name, fixed) {
				if( event_name !== 'sidebar_fixed' ) return;
			
				var sidebar = $sidebar.get(0);
				var $window = $(window);
			
				//return if sidebar is not fixed or in mobile view mode
				var sidebar_vars = $sidebar.ace_sidebar('vars');
				if( !fixed || ( sidebar_vars['mobile_view'] || sidebar_vars['collapsible'] ) ) {
					$sidebar.removeClass('lower-highlight');
					//restore original, default marginTop
					sidebar.style.marginTop = '';
			
					$window.off('scroll.ace.top_menu')
					return;
				}
			
			
				 var done = false;
				 $window.on('scroll.ace.top_menu', function(e) {
			
					var scroll = $window.scrollTop();
					scroll = parseInt(scroll / 4);//move the menu up 1px for every 4px of document scrolling
					if (scroll > 17) scroll = 17;
			
			
					if (scroll > 16) {			
						if(!done) {
							$sidebar.addClass('lower-highlight');
							done = true;
						}
					}
					else {
						if(done) {
							$sidebar.removeClass('lower-highlight');
							done = false;
						}
					}
			
					sidebar.style['marginTop'] = (17-scroll)+'px';
				 }).triggerHandler('scroll.ace.top_menu');
			
			 }).triggerHandler('settings.ace.top_menu', ['sidebar_fixed' , $sidebar.hasClass('sidebar-fixed')]);
			
			 $(window).on('resize.ace.top_menu', function() {
				$(document).triggerHandler('settings.ace.top_menu', ['sidebar_fixed' , $sidebar.hasClass('sidebar-fixed')]);
			 });
			
			
			});
		</script>
	</body>
</html>