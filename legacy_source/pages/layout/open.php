<?php
	if($_SESSION['page']) {
		switch ($_SESSION['page']){				
			case '' :	
				if(!file_exists ("beranda.php")) {
					include "../errors/404.php";
				} else { 
					include "beranda.php";
				}
			break;	
			
			#BERANDA	
			case 'Beranda' :	
				if(!file_exists ("beranda.php")) {
					include "../errors/404.php";
				} else { 
					include "beranda.php";
				}
			break;
			
			#FINGER PRINT	
			case 'Finger Print' :	
				if(!file_exists ("finger.php")) {
					include "../errors/404.php";
				} else { 
					include "finger.php";
				}
			break;
			
			#DEPARTEMEN	
			case 'Departemen' :	
				if(!file_exists ("departemen.php")) {
					include "../errors/404.php";
				} else { 
					include "departemen.php";
				}
			break;
			
			#Kelas	
			case 'Kelas' :	
				if(!file_exists ("kelas.php")) {
					include "../errors/404.php";
				} else { 
					include "kelas.php";
				}
			break;
			
			#DOSEN	
			case 'Dosen' :	
				if(!file_exists ("dosen.php")) {
					include "../errors/404.php";
				} else { 
					include "dosen.php";
				}
			break;
			
			#MAHASISWA	
			case 'Mahasiswa' :	
				if(!file_exists ("mahasiswa.php")) {
					include "../errors/404.php";
				} else { 
					include "mahasiswa.php";
				}
			break;
			
			#MATA KULIAH	
			case 'Mata Kuliah' :	
				if(!file_exists ("mk.php")) {
					include "../errors/404.php";
				} else { 
					include "mk.php";
				}
			break;
			
			#RUANG KULIAH	
			case 'Ruang Kuliah' :	
				if(!file_exists ("ruangan.php")) {
					include "../errors/404.php";
				} else { 
					include "ruangan.php";
				}
			break;
			
			#JADWAL KULIAH	
			case 'Jadwal Kuliah' :	
				if(!file_exists ("jadwal.php")) {
					include "../errors/404.php";
				} else { 
					include "jadwal.php";
				}
			break;
			
			#LAPORAN
			case 'Laporan Absen Dosen' :	
				if(!file_exists ("laporan_dosen.php")) {
					include "../errors/404.php";
				} else { 
					include "laporan_dosen.php";
				}
			break;
			
			case 'Laporan Absen Mahasiswa' :	
				if(!file_exists ("laporan_mhs.php")) {
					include "../errors/404.php";
				} else { 
					include "laporan_mhs.php";
				}
			break;
			
			#PENGATURAN		
			case 'Pengaturan' :	
				if(!file_exists ("pengaturan.php")) {
					include "../errors/404.php";
				} else { 
					include "pengaturan.php";
				}
			break;
			
			#AKUN	
			case 'Akun' :	
				if(!file_exists ("akun.php")) {
					include "../errors/404.php";
				} else { 
					include "akun.php";
				}
			break;
			
			#LOGOUT	
			case 'Logout' :	
				if(!file_exists ("../../controllers/log/logout.php")) {
					include "../errors/404.php";
				} else { 
					include "../../controllers/log/logout.php";
				}
			break;	
			
			default:
				include "../errors/404.php";					
			break;
		}
	} else {
		include "../errors/404.php";
	}
?>