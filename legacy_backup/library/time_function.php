<?php
	/*---------------------------------------------------------
		MENGHITUNG SELISIH TANGGAL
	-----------------------------------------------------------*/
	function intervalDate($value, $value2) {
		$startDate = new DateTime($value);
		$endDate = new DateTime($value2);
		$interval = $endDate->diff($startDate);
		return $interval->format('%a');
	}
	
	/*---------------------------------------------------------
		MENAMPILKAN RANGE DARI 2 TANGGAL
	-----------------------------------------------------------*/
	function datePeriod($value, $value2) {
		$startDate = new DateTime($value);
		$endDate = new DateTime($value2);
		$endDate = $endDate->modify('+1 day');

		$interval = new DateInterval('P1D');
		return $daterange = new DatePeriod($startDate, $interval ,$endDate);
	}
	
	/*---------------------------------------------------------
		MENGHITUNG JAM SELESAI BERDASARKAN JAM MASUK KULIAH
	-----------------------------------------------------------*/
	function timeCounter($value, $value2) {
		$startTime = strtotime($value);
		$endTime = date("H:i", strtotime("+$value2 minutes", $startTime));
		return $endTime;
	}
?>