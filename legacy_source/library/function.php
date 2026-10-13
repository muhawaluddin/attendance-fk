<?php
	/*--------------------------------------------------
		VALIDATION
	--------------------------------------------------*/
	function mysql_validasi($value) {
		if(get_magic_quotes_gpc()){
			$value = stripslashes($value);
		} else {
			$value = addslashes($value);
		}
		return $value;
	}

	/*--------------------------------------------------
		DAY
	--------------------------------------------------*/
	function Hari($date){
		if ($date != "0000-00-00" || $date != "0000-00-00 00:00:00") {
		$days = array("Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu");

		$hari = date("N", strtotime($date));

		$result = $days[(int)$hari-1];
		return($result);
		}
	}

	/*--------------------------------------------------
		CALENDAR
	--------------------------------------------------*/
	function TanggalIndo($date){
		if ($date != "0000-00-00" || $date != "0000-00-00 00:00:00") {
		$BulanIndo = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

		$tahun = substr($date, 0, 4);
		$bulan = substr($date, 5, 2);
		$tgl   = substr($date, 8, 2);

		$result = $tgl . " " . $BulanIndo[(int)$bulan-1] . " ". $tahun;
		return($result);
		}
	}

	function TanggalEng($date){
		$result = date("F jS, Y", strtotime($date));
		return($result);
	}

	function TglSortIndo($date){
		if ($date != "0000-00-00" || $date != "0000-00-00 00:00:00") {
		$BulanIndo = array("Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des");

		$tahun = substr($date, 0, 4);
		$bulan = substr($date, 5, 2);
		$tgl   = substr($date, 8, 2);

		$result = $tgl . " " . $BulanIndo[(int)$bulan-1] . " ". $tahun;
		return($result);
		}
	}

	function TglSortEng($date){
		$result = date("M jS, Y", strtotime($date));
		return($result);
	}

	function DoubleTglid($date, $date2){
		$BulanIndo = array("Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des");

		$thn = substr($date, 0, 4);
		$bln = substr($date, 5, 2);
		$tgl   = substr($date, 8, 2);

		$thn2 = substr($date2, 0, 4);
		$bln2 = substr($date2, 5, 2);
		$tgl2   = substr($date2, 8, 2);

		if ($date2 != "0000-00-00") {
			if ($thn == $thn2) {
				if ($bln == $bln2) {
					if ($tgl == $tgl2) {
						$result = $tgl." ".$BulanIndo[(int)$bln-1]." ".$thn;
					} else {
						$result = $tgl." - ".$tgl2." ".$BulanIndo[(int)$bln-1]." ".$thn;
					}
				} else {
					$result = $tgl." ".$BulanIndo[(int)$bln-1]." - ".$tgl2." ".$BulanIndo[(int)$bln2-1]." ".$thn;
				}
			} else {
				$result = $tgl." ".$BulanIndo[(int)$bln-1]." ".$thn." - ".$tgl2." ".$BulanIndo[(int)$bln2-1]." ".$thn2;
			}
		} else {
			$result = $tgl." ".$BulanIndo[(int)$bln-1]." ".$thn;
		}

		return($result);
	}

	function DoubleTglEn($date, $date2){
		$BulanIndo = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");

		$thn = substr($date, 0, 4);
		$bln = substr($date, 5, 2);
		$tgl   = substr($date, 8, 2);

		$thn2 = substr($date2, 0, 4);
		$bln2 = substr($date2, 5, 2);
		$tgl2   = substr($date2, 8, 2);

		if ($date2 != "0000-00-00") {
			if ($thn == $thn2) {
				if ($bln == $bln2) {
					if ($tgl == $tgl2) {
						$result = $BulanIndo[(int)$bln-1].", ".$tgl." ".$thn;
					} else {
						$result = $BulanIndo[(int)$bln-1].", ".$tgl." - ".$tgl2." ".$thn;
					}
				} else {
					$result = $BulanIndo[(int)$bln-1].", ".$tgl." - ".$BulanIndo[(int)$bln2-1].", ".$tgl2." ".$thn;
				}
			} else {
				$result = $BulanIndo[(int)$bln-1].", ".$tgl." ".$thn." - ".$BulanIndo[(int)$bln2-1].", ".$tgl2." ".$thn2;
			}
		} else {
			$result = $BulanIndo[(int)$bln-1].", ".$tgl." ".$thn;
		}

		return($result);
	}

	function IndonesiaTgl($tanggal){
		$tgl=substr($tanggal,8,2);
		$bln=substr($tanggal,5,2);
		$thn=substr($tanggal,0,4);
		$tanggal="$tgl/$bln/$thn";
		return $tanggal;
	}

	function InggrisTgl($tanggal){
		$tgl=substr($tanggal,8,2);
		$bln=substr($tanggal,5,2);
		$thn=substr($tanggal,0,4);
		$tanggal="$bln/$tgl/$thn";
		return $tanggal;
	}

	function tglMulai($tanggal){
		$tgl=substr($tanggal,3,2);
		$bln=substr($tanggal,0,2);
		$thn=substr($tanggal,6,4);
		$tanggal="$thn-$bln-$tgl";
		return $tanggal;
	}

	function tglSelesai($tanggal){
		$tgl=substr($tanggal,16,2);
		$bln=substr($tanggal,13,2);
		$thn=substr($tanggal,19,4);
		$tanggal="$thn-$bln-$tgl";
		return $tanggal;
	}

	/*--------------------------------------------------
		CODE
	--------------------------------------------------*/
	function buatKode($tabel, $inisial){
		$struktur	= mysql_query("SELECT * FROM $tabel");
		$field		= mysql_field_name($struktur,0);
		$panjang	= mysql_field_len($struktur,0);

		$qry	= mysql_query("SELECT MAX(".$field.") FROM ".$tabel);
		$row	= mysql_fetch_array($qry);
		if ($row[0]=="") {
			$angka=0;
		}
		else {
			$angka		= substr($row[0], strlen($inisial));
		}

		$angka++;
		$angka	=strval($angka);
		$tmp	="";
		for($i=1; $i<=($panjang-strlen($inisial)-strlen($angka)); $i++) {
			$tmp=$tmp."0";
		}
		return $inisial.$tmp.$angka;
	}

	/*--------------------------------------------------
		NUMBER
	--------------------------------------------------*/
	function format_angka($angka) {
		$hasil =  number_format($angka,0, ",",".");
		return $hasil;
	}

	/*--------------------------------------------------
		TIME
	--------------------------------------------------*/
	function Waktu($date){
		$jam = substr($date, 0, 2);
		$menit = substr($date, 3, 2);
		$detik   = substr($date, 6, 2);

		$result = $jam . ":" . $menit;
		return($result);
	}

	/*--------------------------------------------------
		CASE
	--------------------------------------------------*/
	function depanUp($text){
		$new = ucwords(strtolower($text));
		$text = $new;
		return $text;
	}

	function group_by($key, $data) {
		$result = array();

		foreach($data as $val) {
			if(array_key_exists($key, $val)){
				$result[$val[$key]][] = $val;
			}else{
				$result[""][] = $val;
			}
		}

		return $result;
	}
?>