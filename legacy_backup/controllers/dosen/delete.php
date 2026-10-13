<?php			
	$error = array();
	$id = mysql_validasi($_POST["id"]);	
	
	if (md5($id) != $_GET['delete']) {
		$error[] = "";
	}
				
	if (count($error) == 0) {
		db1_open();
		$query_delete = mysql_query("DELETE dosen, users FROM dosen LEFT JOIN users ON dosen.id_dosen = users.id_dosen WHERE dosen.id_dosen = '$id'") or die (mysql_error());				
		db1_close();
		
		echo "<meta http-equiv='refresh' content='1; url=../dosen/?success=3'>";
	} else {
		$failed = "Terjadi kesalahan. Data gagal diperbaharui!";
	}
?>