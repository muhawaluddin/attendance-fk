<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Cetak Laporan Absen Mahasiswa - FK UNHAS</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #111; margin: 20px; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .header { margin-bottom: 20px; }
        .header h2, .header h3, .header h4 { margin: 2px 0; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #444; padding: 4px 6px; }
        th { background-color: #f1f5f9; }
        .status-h { color: #0000cc; font-weight: bold; }
        .status-a { color: #cc0000; font-weight: bold; }
        .print-btn { padding: 8px 16px; background: #b91c1c; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; margin-bottom: 15px; }
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print text-right">
        <button onclick="window.print()" class="print-btn">Cetak Dokumen (Ctrl + P)</button>
    </div>

    <div class="header text-center">
        <h3>LAPORAN ABSENSI MAHASISWA SEMESTER {{ strtoupper($selectedJadwal->semester) }} {{ $selectedJadwal->thn_ajaran }}</h3>
        <h2>FAKULTAS KEDOKTERAN UNIVERSITAS HASANUDDIN</h2>
        <h4>BLOK: {{ strtoupper($selectedJadwal->mataKuliah->nama_mk) }} &bull; KELAS: {{ strtoupper($selectedJadwalKelas->kelas->nama_kelas) }}</h4>
    </div>

    <div style="margin-bottom: 10px; font-size: 11px;">
        Koordinator: <strong>{{ $selectedJadwal->koordinator->nama_lengkap ?? '-' }}</strong> | 
        Sekretaris: <strong>{{ $selectedJadwal->sekretaris->nama_lengkap ?? '-' }}</strong> |
        Dicetak pada: {{ now()->format('d/m/Y H:i') }} WITA
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" width="3%">No</th>
                <th rowspan="2" width="10%">NIM</th>
                <th rowspan="2">Nama Mahasiswa</th>
                @foreach($sesiList as $s)
                    <th colspan="2" class="text-center">
                        {{ $s->jadwalHari->tanggal->format('d/m/y') }}<br>
                        <small>{{ $s->jam_masuk }}-{{ $s->jam_keluar ?: $s->hitungJamKeluar() }}</small>
                    </th>
                @endforeach
                <th colspan="2">Total</th>
                <th rowspan="2" width="6%">%</th>
            </tr>
            <tr>
                @foreach($sesiList as $s)
                    <th width="3%">Sts</th>
                    <th width="5%">Tap</th>
                @endforeach
                <th width="3%">H</th>
                <th width="3%">A</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporanData as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center font-bold">{{ $row['mahasiswa']->nim }}</td>
                    <td>{{ $row['mahasiswa']->nama_mahasiswa }}</td>
                    @foreach($row['sesi_status'] as $st)
                        <td class="text-center {{ $st['status'] === 'H' ? 'status-h' : 'status-a' }}">
                            {{ $st['status'] }}
                        </td>
                        <td class="text-center" style="font-size: 9px;">
                            {{ $st['waktu'] }}
                        </td>
                    @endforeach
                    <td class="text-center status-h">{{ $row['hadir'] }}</td>
                    <td class="text-center status-a">{{ $row['alpa'] }}</td>
                    <td class="text-center font-bold" style="color: {{ $row['persen'] >= 70 ? 'blue' : 'red' }};">
                        {{ $row['persen'] }}%
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 40px; display: flex; justify-content: space-between;">
        <div style="width: 250px;" class="text-center">
            <p>Mengetahui,<br>Wakil Dekan Bidang Akademik,</p>
            <br><br><br>
            <p><strong>Dr. dr. Irfan Idris, M.Kes</strong><br>NIP. 196706241998021001</p>
        </div>
        <div style="width: 250px;" class="text-center">
            <p>Makassar, {{ now()->locale('id')->isoFormat('D MMMM Y') }}<br>Koordinator Blok,</p>
            <br><br><br>
            <p><strong>{{ $selectedJadwal->koordinator->nama_lengkap ?? '...............................' }}</strong><br>NIP. {{ $selectedJadwal->koordinator->nip ?? '...............................' }}</p>
        </div>
    </div>

</body>
</html>
