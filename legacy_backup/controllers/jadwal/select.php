<?php				
	$error = array();			
	$select = mysql_validasi($_POST["select"]);
	
	if (empty($select)) {
		$selectErr = '<i class="fa fa-exclamation-triangle"></i> Pilih kelas.<br>';
		$error[] = "";
	} else {
		unset($_SESSION['kelas']);
		$_SESSION['kelas'] = $select;
	}
?>