<?php			
	if (isset($_POST["id"])) {	
		$id = array();
		$id = $_POST['id'];
		$n = count($id);
				
			
		for ($i = 0; $i < $n; $i++) {	
			db1_open();
			$query = mysql_query("SELECT * FROM mesin WHERE id_mesin = '$id[$i]'");
			$data = mysql_fetch_array($query);
			$sn = $data['serial'];
			$query_delete = mysql_query("DELETE FROM mesin WHERE id_mesin = '$id[$i]'") or die (mysql_error());
			db1_close();
		
			db2_open();
			$query_delete = mysql_query("DELETE FROM device WHERE sn = '$sn'") or die (mysql_error());				
			db2_close();
		}
				
		echo "<meta http-equiv='refresh' content='1; url=../finger-print/?success=3'>";
	}
?>