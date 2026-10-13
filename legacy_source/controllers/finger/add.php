<?php				
	$error = array();			
	$serial = mysql_validasi($_POST["serial"]);
	$activation = mysql_validasi($_POST["activation"]);
	$kode = mysql_validasi($_POST["kode"]);
	$no_mesin = mysql_validasi($_POST["no_mesin"]);
	$ip_address = mysql_validasi($_POST["ip_address"]);
	$port = mysql_validasi($_POST["port"]);
	
	if (empty($serial)) {
		$serialErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan serial number.<br>';
		$error[] = "";
	} else {
		db1_open();
		$query_check = mysql_query("SELECT * FROM mesin WHERE serial = '$serial'");
		if (mysql_num_rows($query_check) > 0) {
			$serialErr = '<i class="fa fa-exclamation-triangle"></i> Serial telah terdaftar.<br>';
			$error[] = "";
		}
		db1_close();
		
		db2_open();
		$query_check = mysql_query("SELECT * FROM device WHERE sn = '$serial'");
		if (mysql_num_rows($query_check) > 0) {
			$serialErr = '<i class="fa fa-exclamation-triangle"></i> Serial number telah terdaftar pada aplikasi fingerspot.<br>';
			$error[] = "";
		}
		db2_close();
	}
	
	if (empty($activation)) {
		$activationErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan activation code.<br>';
		$error[] = "";
	} else {
		db1_open();
		$query_check = mysql_query("SELECT * FROM mesin WHERE activation = '$activation'");
		if (mysql_num_rows($query_check) > 0) {
			$activationErr = '<i class="fa fa-exclamation-triangle"></i> Activation code telah terdaftar.<br>';
			$error[] = "";
		}
		db1_close();
		
		db2_open();
		$query_check = mysql_query("SELECT * FROM device WHERE activation_code = '$activation'");
		if (mysql_num_rows($query_check) > 0) {
			$activationErr = '<i class="fa fa-exclamation-triangle"></i> Activation code telah terdaftar pada aplikasi fingerspot.<br>';
			$error[] = "";
		}
		db2_close();
	}
	
	if (empty($kode)) {
		$kodeErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan kode finger print.<br>';
		$error[] = "";
	} else {
		db1_open();
		$query_check = mysql_query("SELECT * FROM mesin WHERE kode = '$kode'");
		if (mysql_num_rows($query_check) > 0) {
			$kodeErr = '<i class="fa fa-exclamation-triangle"></i> Kode finger print telah digunakan.<br>';
			$error[] = "";
		}
		db1_close();
		
		db2_open();
		$query_check = mysql_query("SELECT * FROM device WHERE device_name = 'Mesin $kode'");
		if (mysql_num_rows($query_check) > 0) {
			$kodeErr = '<i class="fa fa-exclamation-triangle"></i> Kode finger print telah digunakan pada aplikasi fingerspot.<br>';
			$error[] = "";
		}
		db2_close();
	}
	
	if (empty($no_mesin)) {
		$no_mesinErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan no. mesin.<br>';
		$error[] = "";
	} else {
		db1_open();
		$query_check = mysql_query("SELECT * FROM mesin WHERE no_mesin = '$no_mesin'");
		if (mysql_num_rows($query_check) > 0) {
			$no_mesinErr = '<i class="fa fa-exclamation-triangle"></i> No. mesin telah digunakan.<br>';
			$error[] = "";
		}
		db1_close();
		
		db2_open();
		$query_check = mysql_query("SELECT * FROM device WHERE dev_id = '$no_mesin'");
		if (mysql_num_rows($query_check) > 0) {
			$no_mesinErr = '<i class="fa fa-exclamation-triangle"></i> No. mesin telah digunakan pada aplikasi fingerspot.<br>';
			$error[] = "";
		}
		db2_close();
	}
	
	if (empty($ip_address)) {
		$ip_addressErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan IP address.<br>';
		$error[] = "";
	} else {
		db1_open();
		$query_check = mysql_query("SELECT * FROM mesin WHERE ip_address = '$ip_address'");
		if (mysql_num_rows($query_check) > 0) {
			$ip_addressErr = '<i class="fa fa-exclamation-triangle"></i> IP Address telah digunakan.<br>';
			$error[] = "";
		}
		db1_close();
		
		db2_open();
		$query_check = mysql_query("SELECT * FROM device WHERE ip_address = '$ip_address'");
		if (mysql_num_rows($query_check) > 0) {
			$ip_addressErr = '<i class="fa fa-exclamation-triangle"></i> IP Address telah digunakan pada aplikasi fingerspot.<br>';
			$error[] = "";
		}
		db2_close();
	}
	
	if (empty($port)) {
		$portErr = '<i class="fa fa-exclamation-triangle"></i> Masukkan port.<br>';
		$error[] = "";
	}
			
	if (count($error) == 0) {		
		db1_open();
		$id_mesin = buatKode("mesin", "");
		$query_insert = mysql_query("INSERT INTO mesin (id_mesin, serial, activation, kode, no_mesin, ip_address, port) VALUES ('$id_mesin', '$serial', '$activation', '$kode', '$no_mesin', '$ip_address', '$port')") or die (mysql_error());
		db1_close();
		
		
		db2_open();
		$query_insert = mysql_query("INSERT INTO device (sn, activation_code, device_name, dev_id, ip_address, id_type, dev_type, ethernet_port) VALUES ('$serial', '$activation', 'Mesin $kode', '$no_mesin', '$ip_address', '12', '3', '$port')") or die (mysql_error());
		db2_close();
				
		echo "<meta http-equiv='refresh' content='1; url=?../finger-print/?success=1'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal ditambahkan!";
	}
?>