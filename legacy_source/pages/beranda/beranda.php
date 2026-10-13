<?php			
	if (isset($_SESSION['log_absensi']) && isset($_SESSION['level_absensi'])) {
?>
<div class="page-header">
	<h1><i class="ace-icon fa fa-home"></i> Beranda</h1>
</div><!-- /.page-header -->

<div class="row">
	<div class="col-xs-12">
	<div class="center">
		<h3>SELAMAT DATANG</h3><br>
		<img src="../../assets/img/unhas.png" width="9%">
		<h1>ABSENSI</h1>
		<h4>FAKULTAS KEDOKTERAN UNIVERSITAS HASANUDDIN</h4>		
	</div>
	</div><!-- /.col -->
</div><!-- /.row -->				
<?php
	} else {
		echo "<meta http-equiv='refresh' content='0; url=../../'>";
	}
?>	