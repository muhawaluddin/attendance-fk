<?php				
	$error = array();			
	$smsThn = explode(" ", mysql_validasi($_POST["semester"]));
	$semester = $smsThn[0];
	$thn_ajaran = $smsThn[1];
	$dosen = mysql_validasi($_POST["dosen"]);
?>