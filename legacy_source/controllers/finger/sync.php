<?php	
	$data_sync = array();	
	
	db2_open();
	$query_db2 = mysql_query("SELECT * FROM device ORDER BY ip_address ASC") or die (mysql_error());
	$jml_db2 = mysql_num_rows($query_db2);
	while ($data_db2 = mysql_fetch_array($query_db2)) {
		$data_sync[] = $data_db2['sn'].",".$data_db2['activation_code'].",".substr($data_db2['device_name'], 6).",".$data_db2['dev_id'].",".$data_db2['ip_address'].",".$data_db2['ethernet_port'];
	}
	db2_close();	
	
	db1_open();
	$query_delete = mysql_query("TRUNCATE mesin") or die (mysql_error());
	for ($i = 0; $i < $jml_db2; $i++) {		
		$data = explode(",", $data_sync[$i]);
		$id_mesin = buatKode("mesin", "");
		$query_insert = mysql_query("INSERT INTO mesin (id_mesin, serial, activation, kode, no_mesin, ip_address, port) VALUES ('$id_mesin', '$data[0]', '$data[1]', '$data[2]', '$data[3]', '$data[4]', '$data[5]')") or die (mysql_error());
	}
	db1_close();
				
	echo "<meta http-equiv='refresh' content='1; url=../finger-print/?success=5'>";
?>