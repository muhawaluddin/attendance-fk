<html xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        table, th, td { border: 1px solid #000; font-family: Arial, sans-serif; font-size: 11px; }
        th { background-color: #EDF3F4; font-weight: bold; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .blue { color: #0101DF; }
        .red { color: #FF0000; }
    </style>
</head>
<body>
    <h3 align="center">
        LAPORAN PRESENSI MENGAJAR DOSEN<br>
        FAKULTAS KEDOKTERAN UNIVERSITAS HASANUDDIN
    </h3>
    <h4>
        Dosen: {{ $dosen->nama_lengkap }}<br>
        NIP: {{ $dosen->nip }} | PIN Finger: {{ $dosen->id_finger ?: '-' }}
    </h4>

    <table>
        <thead>
            <tr>
                <th class="center">No</th>
                <th class="center">Tanggal</th>
                <th class="center">Waktu</th>
                <th>Mata Kuliah / Blok</th>
                <th>Kegiatan / Materi</th>
                <th class="center">Kelas</th>
                <th class="center">Ruangan</th>
                <th class="center">Status</th>
                <th class="center">Waktu Tap</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporanData as $index => $row)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $row['hari'] }}, {{ $row['tanggal']->format('d/m/Y') }}</td>
                    <td class="center">{{ $row['jam_masuk'] }} - {{ $row['jam_keluar'] }}</td>
                    <td class="bold">{{ $row['mk'] }}</td>
                    <td>{{ $row['kegiatan'] }}</td>
                    <td class="center">{{ $row['kelas'] }}</td>
                    <td class="center">{{ $row['ruangan'] }}</td>
                    <td class="center bold {{ $row['status'] === 'H' ? 'blue' : 'red' }}">{{ $row['status'] }}</td>
                    <td class="center bold">{{ $row['checklock'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
