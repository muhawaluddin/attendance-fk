<?php			
	if (isset($_POST["id_jMhs"])) {	
		$id_jMhs = array();
		$id = mysql_validasi($_POST["id"]);
		$id_jMhs = $_POST['id_jMhs'];
		$n = count($id_jMhs);
				
		for ($i = 0; $i < $n; $i++) {		
			db1_open();
			$query_delete = mysql_query("DELETE FROM jadwal_mhs WHERE id_jMhs = '$id_jMhs[$i]'") or die (mysql_error());
			db1_close();
		}
				
		echo "<meta http-equiv='refresh' content='1; url=?sub=mahasiswa-perkelas&id=".md5($id)."&success=3'>";
	}
?>