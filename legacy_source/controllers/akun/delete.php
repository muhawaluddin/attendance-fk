<?php			
	$error = array();	
	$id = mysql_validasi($_POST["id"]);		
	
	if (md5($id) != $_GET['delete']) {
		$error[] = "";
	}
	
	if (count($error) == 0) {		
		db1_open();
		$query_delete = mysql_query("DELETE FROM users WHERE id_user = '$id'") or die (mysql_error());
		db1_close();
					
		echo "<meta http-equiv='refresh' content='1; url=../akun/?success=3'>"; 
	} else {
		$failed = "Terjadi kesalahan. Data gagal dihapus!";
	}
?>