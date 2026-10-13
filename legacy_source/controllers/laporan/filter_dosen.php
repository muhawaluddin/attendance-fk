<?php
    $error = array();
	$smsThn = explode(" ", mysql_validasi($_POST["semester"]));
	$semester = $smsThn[0];
    $thn_ajaran = $smsThn[1];

    $disabledDosen = '';
    $id_dosen = isset($_POST['id_dosen']) ? mysql_validasi($_POST["id_dosen"]) : '';

    if ($id_dosen) {
        $disabledBlok = '';
        $blok = isset($_POST['blok']) ? mysql_validasi($_POST["blok"]) : '';
    }

    if ($blok) {
        $disabledKelas = '';
        $kelas = isset($_POST['kelas']) ? mysql_validasi($_POST["kelas"]) : '';
    }