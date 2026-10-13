<?php
	if(isset($_SESSION['log_absensi'])) {
		db1_open();
		$query_update = mysql_query("UPDATE users SET status = '0' WHERE id_user = '$log'");
		db1_close();
		
		session_destroy();
		echo "<meta http-equiv='refresh' content='0; url=../../'>";
	}
?>