<?php			
	if (isset($_POST["id"])) {	
		$id = array();
		$id = $_POST['id'];
		$n = count($id);
				
		db1_open();		
		for ($i = 0; $i < $n; $i++) {							
			$query_delete = mysql_query("DELETE FROM ruangan WHERE id_ruangan = '$id[$i]'") or die (mysql_error());
			$query_update = mysql_query("UPDATE mesin SET id_ruangan = '' WHERE id_ruangan = '$id[$i]'") or die (mysql_error());
		}
		db1_close();
				
		echo "<meta http-equiv='refresh' content='1; url=../ruang-kuliah/?success=3'>";
	}
?>