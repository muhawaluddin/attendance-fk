<?php
	/*--------------------------------------------------
		CONNECTOR
	--------------------------------------------------*/
	function db1_open() {
		mysql_select_db(DB1_NAMA,mysql_connect(DB1_HOST,DB1_USER,DB1_PASSWORD));
	}
	
	function db1_close() {
		mysql_close(mysql_connect(DB1_HOST,DB1_USER,DB1_PASSWORD));
	}
	
	function db2_open() {
		mysql_select_db(DB2_NAMA,mysql_connect(DB2_HOST,DB2_USER,DB2_PASSWORD));
	}
	
	function db2_close() {
		mysql_close(mysql_connect(DB2_HOST,DB2_USER,DB2_PASSWORD));
	}
?>