<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekapitulasi Presensi Dosen - {{ $appSetting->institution_name ?? 'Universitas Hasanuddin' }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #1e293b; margin: 20px; }
        .kop { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px; }
        .kop h2 { margin: 0; font-size: 16px; text-transform: uppercase; font-weight: 800; }
        .kop h3 { margin: 3px 0 0; font-size: 13px; font-weight: 600; }
        .kop p { margin: 2px 0 0; font-size: 10px; color: #475569; }
        .title { text-align: center; margin: 15px 0 10px; font-size: 13px; font-weight: bold; text-transform: uppercase; }
        .meta { margin-bottom: 12px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; font-size: 10.5px; }
        th { background: #f8fafc; font-weight: bold; text-align: center; color: #334155; }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .green { color: #047857; }
        .red { color: #b91c1c; }
        .sign { margin-top: 30px; float: right; width: 220px; text-align: center; font-size: 11px; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 6px 16px; background: #0f172a; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 12px;">Cetak Dokumen</button>
    </div>

    <div class="kop">
        <h2>{{ $appSetting->institution_name ?? 'UNIVERSITAS HASANUDDIN' }}</h2>
        <h3>{{ $appSetting->faculty_name ?? 'FAKULTAS KEDOKTERAN' }}</h3>
        <p>{{ $appSetting->website_url ?? 'www.med.unhas.ac.id' }} &bull; {{ $appSetting->email_contact ?? '' }}</p>
    </div>

    <div class="title">REKAPITULASI PRESENSI KEHADIRAN MENGAJAR DOSEN</div>

    <div class="meta">
        <strong>Periode:</strong> Semester {{ $semester ?: 'Semua' }} &bull; Tahun Ajaran: {{ $thnAjaran ?: 'Semua' }} &bull; Dicetak: {{ now()->translatedFormat('l, d F Y H:i') }} WITA
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th>Nama Dosen & Gelar</th>
                <th width="130">NIP</th>
                <th width="150">Departemen</th>
                <th width="65">Total Sesi</th>
                <th width="55">Hadir</th>
                <th width="55">Alpa</th>
                <th width="75">% Hadir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapDosen as $index => $row)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="bold">{{ $row['nama'] }}</td>
                    <td class="center font-mono">{{ $row['nip'] }}</td>
                    <td>{{ $row['departemen'] }}</td>
                    <td class="center bold">{{ $row['total_sesi'] }}</td>
                    <td class="center bold green">{{ $row['hadir'] }}</td>
                    <td class="center bold red">{{ $row['alpa'] }}</td>
                    <td class="center bold {{ $row['persen'] >= 75 ? 'green' : ($row['persen'] >= 50 ? '' : 'red') }}">
                        {{ $row['persen'] }}%
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="center">Tidak ada data dosen atau presensi untuk filter terpilih.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background: #f8fafc; font-weight: bold;">
                <td colspan="4" class="center">RATA-RATA & TOTAL KESELURUHAN</td>
                <td class="center">{{ $rekapDosen->sum('total_sesi') }}</td>
                <td class="center green">{{ $rekapDosen->sum('hadir') }}</td>
                <td class="center red">{{ $rekapDosen->sum('alpa') }}</td>
                <td class="center">
                    {{ $rekapDosen->sum('total_sesi') > 0 ? round(($rekapDosen->sum('hadir') / $rekapDosen->sum('total_sesi')) * 100, 1) : 0 }}%
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="sign">
        Makassar, {{ now()->translatedFormat('d F Y') }}<br>
        Mengetahui,<br>
        <strong>Pengelola Presensi Terpadu</strong>
        <div style="height: 50px;"></div>
        <u>{{ auth()->user()->name ?? 'Administrator' }}</u><br>
        NIP. {{ auth()->user()->username ?? '--------------------' }}
    </div>
</body>
</html>
