<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Cetak Laporan Presensi Dosen - FK UNHAS</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #111; margin: 20px; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .header { margin-bottom: 20px; }
        .header h2, .header h3, .header h4 { margin: 2px 0; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #444; padding: 5px 8px; }
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
        <h3>LAPORAN PRESENSI MENGAJAR DOSEN</h3>
        <h2>FAKULTAS KEDOKTERAN UNIVERSITAS HASANUDDIN</h2>
        <h4>DOSEN: {{ strtoupper($selectedDosen->nama_lengkap) }}</h4>
    </div>

    <div style="margin-bottom: 10px; font-size: 11px;">
        NIP: <strong>{{ $selectedDosen->nip }}</strong> | 
        PIN Finger: <strong>{{ $selectedDosen->id_finger ?: '-' }}</strong> |
        Departemen: <strong>{{ $selectedDosen->departemen->nama_departemen ?? '-' }}</strong> |
        Dicetak pada: {{ now()->format('d/m/Y H:i') }} WITA
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%" class="text-center">No</th>
                <th width="15%">Tanggal</th>
                <th width="15%" class="text-center">Waktu</th>
                <th>Mata Kuliah / Blok</th>
                <th>Kegiatan / Materi</th>
                <th width="10%" class="text-center">Kelas</th>
                <th width="8%" class="text-center">Status</th>
                <th width="10%" class="text-center">Waktu Tap</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporanDosenData as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row['hari'] }}, {{ $row['tanggal']->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $row['jam_masuk'] }} - {{ $row['jam_keluar'] }}</td>
                    <td class="font-bold">{{ $row['mk'] }}</td>
                    <td>{{ $row['kegiatan'] }}</td>
                    <td class="text-center">{{ $row['kelas'] }}</td>
                    <td class="text-center {{ $row['status'] === 'H' ? 'status-h' : 'status-a' }}">
                        {{ $row['status'] }}
                    </td>
                    <td class="text-center font-bold">{{ $row['checklock'] }}</td>
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
            <p>Makassar, {{ now()->locale('id')->isoFormat('D MMMM Y') }}<br>Dosen Yang Bersangkutan,</p>
            <br><br><br>
            <p><strong>{{ $selectedDosen->nama_lengkap }}</strong><br>NIP. {{ $selectedDosen->nip }}</p>
        </div>
    </div>

</body>
</html>
