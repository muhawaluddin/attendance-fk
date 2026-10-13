<?php

namespace App\Http\Controllers;

use App\Models\AttLog;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Mesin;
use App\Models\Ruangan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MesinController extends Controller
{
    public function index(Request $request)
    {
        $query = Mesin::with('ruangan');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('serial', 'like', "%{$search}%")
                  ->orWhere('nama_mesin', 'like', "%{$search}%")
                  ->orWhere('brand_mesin', 'like', "%{$search}%")
                  ->orWhere('model_tipe', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('brand')) {
            $query->where('brand_mesin', $request->brand);
        }

        $mesins = $query->orderBy('serial', 'asc')->paginate(15);
        $ruangans = Ruangan::orderBy('nama_ruangan', 'asc')->get();

        // Daftar brand populer
        $brandOptions = [
            'Deli' => 'Deli (E3960, E3961, E3962, 15152, 15153, E2180, dll)',
            'ZKTeco' => 'ZKTeco (K40, iFace, MB20, IN01, dll)',
            'Solution' => 'Solution (X100-C, X105-ID, P207, dll)',
            'Fingerspot' => 'Fingerspot (Revo Series, Neo, Duo)',
            'Hikvision' => 'Hikvision (Face & Fingerprint DS-K1T Series)',
            'BioFinger' => 'BioFinger (AF-400, AT-600 Series)',
            'Realand' => 'Realand / Tiqiaa Biometric',
            'Magic' => 'Magic Fingerprint',
            'Generic' => 'Generic / Mesin Standalone TCP/IP Lainnya',
        ];

        // Untuk simulator tap
        $mahasiswaSample = Mahasiswa::whereNotNull('id_finger')->orderBy('nama_mahasiswa')->limit(30)->get();
        $dosenSample = Dosen::whereNotNull('id_finger')->orderBy('nama_dosen')->limit(20)->get();

        return view('mesin.index', compact('mesins', 'ruangans', 'brandOptions', 'mahasiswaSample', 'dosenSample'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'serial' => ['required', 'string', 'max:100', 'unique:mesins,serial'],
            'nama_mesin' => ['nullable', 'string', 'max:255'],
            'brand_mesin' => ['required', 'string', 'max:50'],
            'model_tipe' => ['nullable', 'string', 'max:100'],
            'tipe_koneksi' => ['required', 'string', 'in:standalone_tcp,standalone_udp,cloud_adms,webhook_api'],
            'activation' => ['nullable', 'string', 'max:100'],
            'kode' => ['nullable', 'string', 'max:50'],
            'no_mesin' => ['nullable', 'string', 'max:20'],
            'ip_address' => ['nullable', 'string', 'max:50'],
            'port' => ['nullable', 'string', 'max:10'],
            'comm_key' => ['nullable', 'string', 'max:50'],
            'metode_verifikasi' => ['nullable', 'array'],
            'ruangan_id' => ['nullable', 'exists:ruangans,id'],
        ], [
            'serial.required' => 'Serial number wajib diisi.',
            'serial.unique' => 'Serial number sudah terdaftar.',
            'brand_mesin.required' => 'Pilih brand / pabrikan mesin.',
        ]);

        if (empty($validated['nama_mesin'])) {
            $validated['nama_mesin'] = $validated['brand_mesin'] . ' (' . $validated['serial'] . ')';
        }

        if (isset($validated['metode_verifikasi'])) {
            $validated['metode_verifikasi'] = implode(',', $validated['metode_verifikasi']);
        } else {
            $validated['metode_verifikasi'] = 'fingerprint,rfid,pin';
        }

        $validated['status_online'] = true;
        $validated['last_synced_at'] = now();

        Mesin::create($validated);
        return redirect()->route('mesin.index')->with('success', "Mesin {$validated['brand_mesin']} ({$validated['serial']}) berhasil didaftarkan!");
    }

    public function update(Request $request, Mesin $mesin)
    {
        $validated = $request->validate([
            'serial' => ['required', 'string', 'max:100', 'unique:mesins,serial,' . $mesin->id],
            'nama_mesin' => ['nullable', 'string', 'max:255'],
            'brand_mesin' => ['required', 'string', 'max:50'],
            'model_tipe' => ['nullable', 'string', 'max:100'],
            'tipe_koneksi' => ['required', 'string', 'in:standalone_tcp,standalone_udp,cloud_adms,webhook_api'],
            'activation' => ['nullable', 'string', 'max:100'],
            'kode' => ['nullable', 'string', 'max:50'],
            'no_mesin' => ['nullable', 'string', 'max:20'],
            'ip_address' => ['nullable', 'string', 'max:50'],
            'port' => ['nullable', 'string', 'max:10'],
            'comm_key' => ['nullable', 'string', 'max:50'],
            'metode_verifikasi' => ['nullable', 'array'],
            'ruangan_id' => ['nullable', 'exists:ruangans,id'],
        ], [
            'serial.required' => 'Serial number wajib diisi.',
        ]);

        if (isset($validated['metode_verifikasi'])) {
            $validated['metode_verifikasi'] = implode(',', $validated['metode_verifikasi']);
        }

        $mesin->update($validated);
        return redirect()->route('mesin.index')->with('success', 'Data mesin fingerprint berhasil diperbarui!');
    }

    public function destroy(Mesin $mesin)
    {
        $mesin->delete();
        return redirect()->route('mesin.index')->with('success', 'Mesin fingerprint berhasil dihapus!');
    }

    /**
     * Uji konektivitas socket / ping ke perangkat
     */
    public function testConnection(Mesin $mesin)
    {
        $isOnline = $mesin->pingSocket();

        if ($isOnline) {
            return back()->with('success', "Koneksi ke mesin {$mesin->brand_mesin} ({$mesin->ip_address}:{$mesin->port}) BERHASIL (Online)!");
        } else {
            return back()->with('error', "Koneksi ke mesin {$mesin->brand_mesin} ({$mesin->ip_address}:{$mesin->port}) GAGAL / Timeout. Pastikan perangkat menyala dan terhubung ke jaringan lokal.");
        }
    }

    public function syncAll()
    {
        $mesins = Mesin::all();
        $online = 0;
        foreach ($mesins as $m) {
            if ($m->pingSocket()) {
                $online++;
            }
        }

        return redirect()->route('mesin.index')->with('success', "Sinkronisasi selesai! {$online} dari " . count($mesins) . " mesin terdeteksi Online.");
    }

    /**
     * Simulator Scan Tap Fingerprint untuk pengujian langsung
     */
    public function simulateTap(Request $request)
    {
        $validated = $request->validate([
            'mesin_id' => ['required', 'exists:mesins,id'],
            'pin' => ['required', 'string'],
            'tipe_pengguna' => ['required', 'in:mahasiswa,dosen'],
            'scan_time' => ['nullable', 'date_format:H:i'],
        ]);

        $mesin = Mesin::findOrFail($validated['mesin_id']);
        $now = Carbon::now();

        if (!empty($validated['scan_time'])) {
            $parts = explode(':', $validated['scan_time']);
            $scanDate = $now->copy()->setTime((int)$parts[0], (int)$parts[1], rand(1, 59));
        } else {
            $scanDate = $now;
        }

        $this->recordScan(
            sn: $mesin->serial,
            pin: $validated['pin'],
            rawTime: $scanDate,
            ruanganId: $mesin->ruangan_id,
            tipePenggunaExplicit: $validated['tipe_pengguna']
        );

        return back()->with('success', "Simulasi Tap Berhasil! Log scan PIN {$validated['pin']} pada {$mesin->brand_mesin} ({$mesin->serial}) tercatat di sistem.");
    }

    /**
     * Webhook/API endpoint untuk menerima Push Log Absensi dari mesin eksternal
     * (Mendukung Deli Biometrics, ZKTeco ADMS/Push, Solution Cloud, Hikvision, Fingerspot)
     */
    public function pushAttLog(Request $request)
    {
        $savedCount = 0;

        // 1. Cek apakah ada raw ADMS cdata body (Deli / ZK ADMS POST)
        $rawContent = trim($request->getContent());
        if (!empty($rawContent) && (str_contains($rawContent, "\t") || str_contains($rawContent, "\n"))) {
            $sn = $request->input('sn') ?? $request->input('SN') ?? $request->query('SN') ?? 'DELI-ADMS';
            $lines = preg_split('/\r\n|\r|\n/', $rawContent);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || str_starts_with($line, '#')) continue;
                $cols = explode("\t", $line);
                if (count($cols) >= 2) {
                    $pin = trim($cols[0]);
                    $time = trim($cols[1]);
                    if ($this->recordScan($sn, $pin, $time)) {
                        $savedCount++;
                    }
                }
            }

            if ($request->has('SN') || $request->has('table')) {
                return response("OK\n", 200)->header('Content-Type', 'text/plain');
            }

            return response()->json([
                'status' => 'success',
                'message' => "{$savedCount} log absensi berhasil dicatat",
                'count' => $savedCount
            ], 200);
        }

        // 2. Cek apakah berupa JSON Array / Batch (Deli Cloud webhook / multi-record)
        $records = [];
        if ($request->isJson()) {
            $json = $request->json()->all();
            if (isset($json['records']) && is_array($json['records'])) {
                $records = $json['records'];
            } elseif (isset($json['data']) && is_array($json['data'])) {
                $records = $json['data'];
            } elseif (isset($json['log']) && is_array($json['log'])) {
                $records = $json['log'];
            } elseif (array_is_list($json)) {
                $records = $json;
            }
        }

        if (!empty($records)) {
            $defaultSn = $request->input('dev_sn') ?? $request->input('sn') ?? $request->input('SN') ?? 'DELI-PUSH';
            foreach ($records as $item) {
                if (!is_array($item)) continue;
                $sn = $item['dev_sn'] ?? $item['device_sn'] ?? $item['sn'] ?? $item['SN'] ?? $item['device_id'] ?? $defaultSn;
                $pin = $item['user_id'] ?? $item['pin'] ?? $item['PIN'] ?? $item['enroll_id'] ?? $item['user_no'] ?? null;
                $time = $item['verify_time'] ?? $item['scan_date'] ?? $item['time'] ?? $item['att_time'] ?? now();

                if ($pin && $this->recordScan($sn, $pin, $time)) {
                    $savedCount++;
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => "{$savedCount} log absensi dari batch berhasil dicatat",
                'count' => $savedCount
            ], 200);
        }

        // 3. Single Record (JSON / Form Data / Query String)
        $sn = $request->input('dev_sn')
            ?? $request->input('device_sn')
            ?? $request->input('sn')
            ?? $request->input('SN')
            ?? $request->input('device_id')
            ?? $request->input('machine_id')
            ?? $request->query('SN');

        $pin = $request->input('user_id')
            ?? $request->input('pin')
            ?? $request->input('PIN')
            ?? $request->input('enroll_id')
            ?? $request->input('user_no')
            ?? $request->input('id_finger')
            ?? $request->input('UserId');

        $scanTime = $request->input('verify_time')
            ?? $request->input('scan_date')
            ?? $request->input('time')
            ?? $request->input('att_time')
            ?? $request->input('datetime')
            ?? now();

        if (empty($pin)) {
            return response()->json(['status' => 'error', 'message' => 'PIN / User ID wajib disertakan'], 400);
        }

        $mesin = Mesin::where('serial', $sn)->first();
        $saved = $this->recordScan($sn ?: ($mesin ? $mesin->serial : 'API-PUSH'), $pin, $scanTime, $mesin?->ruangan_id);

        if ($request->has('SN') || $request->has('table')) {
            return response("OK\n", 200)->header('Content-Type', 'text/plain');
        }

        return response()->json([
            'status' => 'success',
            'message' => $saved ? 'Attendance log recorded' : 'Attendance log already exists / processed',
            'pin' => $pin,
        ], 201);
    }

    /**
     * Import Log Absensi Mesin Deli via File USB Flashdisk (.xlsx, .xls, .csv, .dat, .txt)
     */
    public function importDeliLog(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:15360'],
            'mesin_id' => ['nullable', 'exists:mesins,id'],
        ], [
            'file.required' => 'Pilih file log absensi dari mesin Deli atau USB Flashdisk.',
            'file.max' => 'Ukuran file maksimal 15 MB.',
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $allowed = ['xlsx', 'xls', 'csv', 'dat', 'txt'];

        if (!in_array($extension, $allowed)) {
            return back()->with('error', "Format file .{$extension} tidak didukung. Harap unggah file .xlsx, .xls, .csv, .dat, atau .txt dari mesin Deli.");
        }

        $mesin = null;
        $ruanganId = null;
        $sn = 'DELI-USB';
        if ($request->filled('mesin_id')) {
            $mesin = Mesin::find($request->mesin_id);
            if ($mesin) {
                $ruanganId = $mesin->ruangan_id;
                $sn = $mesin->serial;
            }
        }

        $importedCount = 0;
        $skippedCount = 0;

        if (in_array($extension, ['xlsx', 'xls'])) {
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray(null, true, true, false);

                $headerMap = [];
                $headerFound = false;

                foreach ($rows as $row) {
                    if (empty(array_filter($row))) continue;

                    // Deteksi baris header
                    if (!$headerFound) {
                        $pinCol = null;
                        $timeCol = null;
                        foreach ($row as $colIdx => $cellVal) {
                            $c = strtolower(trim((string)$cellVal));
                            if (in_array($c, ['pin', 'user id', 'userid', 'id', 'no id', 'no.', 'no', 'enroll id', 'user no', 'id finger', 'pin/id'])) {
                                $pinCol = $colIdx;
                            }
                            if (in_array($c, ['waktu', 'time', 'scan time', 'tanggal', 'date', 'datetime', 'scan date', 'jam', 'waktu scan', 'scan'])) {
                                $timeCol = $colIdx;
                            }
                        }

                        if ($pinCol !== null && $timeCol !== null) {
                            $headerMap = ['pin' => $pinCol, 'time' => $timeCol];
                            $headerFound = true;
                            continue;
                        }
                    }

                    $pinVal = null;
                    $timeVal = null;

                    if ($headerFound && isset($headerMap['pin']) && isset($headerMap['time'])) {
                        $pinVal = $row[$headerMap['pin']] ?? null;
                        $timeVal = $row[$headerMap['time']] ?? null;
                    } else {
                        // Heuristic cell scanner
                        foreach ($row as $val) {
                            $valStr = trim((string)$val);
                            if (empty($valStr)) continue;

                            if (!$timeVal && (preg_match('/\d{4}[-\/]\d{1,2}[-\/]\d{1,2}/', $valStr) || preg_match('/\d{1,2}[-\/]\d{1,2}[-\/]\d{4}/', $valStr))) {
                                $timeVal = $valStr;
                                continue;
                            }
                            if (!$pinVal && preg_match('/^\d{1,12}$/', $valStr)) {
                                $pinVal = $valStr;
                            }
                        }
                    }

                    if ($pinVal && $timeVal) {
                        $saved = $this->recordScan($sn, $pinVal, $timeVal, $ruanganId);
                        if ($saved) {
                            $importedCount++;
                        } else {
                            $skippedCount++;
                        }
                    }
                }
            } catch (\Throwable $e) {
                return back()->with('error', "Gagal membaca file Excel Deli: " . $e->getMessage());
            }
        } else {
            // File DAT, TXT, CSV parsing
            $content = file_get_contents($file->getRealPath());
            $lines = preg_split('/\r\n|\r|\n/', $content);

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || str_starts_with($line, '#')) continue;

                $parts = [];
                if (str_contains($line, "\t")) {
                    $parts = explode("\t", $line);
                } elseif (str_contains($line, ",")) {
                    $parts = str_getcsv($line);
                } elseif (str_contains($line, ";")) {
                    $parts = str_getcsv($line, ';');
                } else {
                    $parts = preg_split('/\s+/', $line);
                }

                $parts = array_values(array_filter(array_map('trim', $parts)));
                if (count($parts) < 2) continue;

                $pinCandidate = $parts[0];
                $timeCandidate = null;

                if (isset($parts[1]) && (preg_match('/\d{4}[-\/]\d{1,2}[-\/]\d{1,2}/', $parts[1]) || preg_match('/\d{1,2}[-\/]\d{1,2}[-\/]\d{4}/', $parts[1]))) {
                    $timeCandidate = $parts[1];
                    if (isset($parts[2]) && preg_match('/^\d{1,2}:\d{1,2}(:\d{1,2})?$/', $parts[2])) {
                        $timeCandidate .= ' ' . $parts[2];
                    }
                } elseif (isset($parts[2]) && (preg_match('/\d{4}[-\/]\d{1,2}[-\/]\d{1,2}/', $parts[2]) || preg_match('/\d{1,2}[-\/]\d{1,2}[-\/]\d{4}/', $parts[2]))) {
                    $timeCandidate = $parts[2];
                }

                if (!is_numeric($pinCandidate) && !preg_match('/^[A-Za-z0-9_-]+$/', $pinCandidate)) {
                    continue;
                }
                if (in_array(strtolower($pinCandidate), ['pin', 'user_id', 'userid', 'id', 'no'])) {
                    continue;
                }

                if ($pinCandidate && $timeCandidate) {
                    $saved = $this->recordScan($sn, $pinCandidate, $timeCandidate, $ruanganId);
                    if ($saved) {
                        $importedCount++;
                    } else {
                        $skippedCount++;
                    }
                }
            }
        }

        if ($importedCount === 0 && $skippedCount === 0) {
            return back()->with('warning', 'Tidak ada data log absensi valid yang terdeteksi dari file. Pastikan file berisi data scan presensi.');
        }

        $mesinNama = $mesin ? $mesin->brand_mesin . ' (' . $mesin->serial . ')' : 'Mesin Deli (USB Flashdisk)';
        return back()->with('success', "Import Log USB Selesai! Berhasil memasukkan {$importedCount} data log scan baru untuk {$mesinNama}. ({$skippedCount} data duplikat/dilewati).");
    }

    /**
     * Helper untuk mencatat scan absensi ke AttLog
     */
    protected function recordScan(string $sn, mixed $pin, mixed $rawTime, ?int $ruanganId = null, ?string $tipePenggunaExplicit = null): bool
    {
        $pin = trim((string)$pin);
        if (empty($pin)) {
            return false;
        }

        if (in_array(strtolower($pin), ['pin', 'user id', 'userid', 'id', 'no id', 'no'])) {
            return false;
        }

        try {
            if (is_numeric($rawTime) && $rawTime > 30000 && $rawTime < 60000) {
                $scanDate = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawTime));
            } else {
                $scanDate = Carbon::parse($rawTime);
            }
        } catch (\Throwable $e) {
            return false;
        }

        $scanTime = $scanDate->format('H:i:s');

        // Deduplikasi log scan
        $exists = AttLog::where('pin', $pin)
            ->where('scan_date', $scanDate)
            ->exists();

        if ($exists) {
            return false;
        }

        if (!$ruanganId && $sn) {
            $mesin = Mesin::where('serial', $sn)->first();
            $ruanganId = $mesin?->ruangan_id;
        }

        $tipe = $tipePenggunaExplicit;
        if (!$tipe) {
            if (Mahasiswa::where('id_finger', $pin)->exists()) {
                $tipe = 'mahasiswa';
            } elseif (Dosen::where('id_finger', $pin)->exists()) {
                $tipe = 'dosen';
            } else {
                $tipe = 'mahasiswa';
            }
        }

        AttLog::create([
            'sn' => $sn ?: 'DELI-SCAN',
            'pin' => $pin,
            'scan_date' => $scanDate,
            'scan_time' => $scanTime,
            'verified' => 1,
            'status_scan' => 0,
            'ruangan_id' => $ruanganId,
            'tipe_pengguna' => $tipe,
        ]);

        return true;
    }

    /**
     * Tampilan Halaman Log Presensi Realtime
     */
    public function logRealtime(Request $request)
    {
        $mesins = Mesin::with('ruangan')->orderBy('serial', 'asc')->get();
        $recentLogs = AttLog::with('ruangan')->orderBy('id', 'desc')->limit(25)->get();

        $initialLogs = $recentLogs->map(function ($log) {
            return $this->formatLogItem($log);
        });

        $latestId = AttLog::max('id') ?? 0;
        $totalScansToday = AttLog::whereDate('scan_date', Carbon::today())->count();
        $onlineCount = $mesins->where('status_online', true)->count();

        // Sample data untuk simulasi cepat
        $mahasiswaSample = Mahasiswa::whereNotNull('id_finger')->orderBy('nama_mahasiswa')->limit(30)->get();
        $dosenSample = Dosen::whereNotNull('id_finger')->orderBy('nama_dosen')->limit(20)->get();

        return view('mesin.realtime', compact(
            'mesins',
            'initialLogs',
            'latestId',
            'totalScansToday',
            'onlineCount',
            'mahasiswaSample',
            'dosenSample'
        ));
    }

    /**
     * API Feed JSON untuk polling streaming log realtime
     */
    public function logRealtimeFeed(Request $request)
    {
        $afterId = $request->get('after_id');
        $limit = min(50, (int)$request->get('limit', 20));

        $query = AttLog::with('ruangan')->orderBy('id', 'desc');

        if ($afterId) {
            $query->where('id', '>', (int)$afterId);
        }

        $logs = $query->limit($limit)->get();

        $formatted = $logs->map(function ($log) {
            return $this->formatLogItem($log);
        });

        $latestId = AttLog::max('id') ?? 0;
        $totalScansToday = AttLog::whereDate('scan_date', Carbon::today())->count();
        $onlineMesinCount = Mesin::where('status_online', true)->count();

        return response()->json([
            'status' => 'success',
            'latest_id' => $latestId,
            'logs' => $formatted,
            'count' => $logs->count(),
            'total_scans_today' => $totalScansToday,
            'online_mesin_count' => $onlineMesinCount,
            'server_time' => Carbon::now()->format('H:i:s'),
        ]);
    }

    /**
     * API Uji Ping Socket JSON dari halaman Log Realtime
     */
    public function pingMachineJson(Mesin $mesin)
    {
        $startTime = microtime(true);
        $isOnline = $mesin->pingSocket();
        $durationMs = round((microtime(true) - $startTime) * 1000);

        return response()->json([
            'status' => $isOnline ? 'online' : 'offline',
            'success' => $isOnline,
            'mesin_id' => $mesin->id,
            'brand' => $mesin->brand_mesin,
            'ip' => $mesin->ip_address,
            'port' => $mesin->port,
            'response_ms' => $durationMs,
            'last_synced_at' => $mesin->last_synced_at ? Carbon::parse($mesin->last_synced_at)->diffForHumans() : 'Baru saja',
            'message' => $isOnline
                ? "Koneksi ke {$mesin->brand_mesin} ({$mesin->ip_address}:{$mesin->port}) BERHASIL ({$durationMs}ms)!"
                : "Koneksi ke {$mesin->brand_mesin} ({$mesin->ip_address}:{$mesin->port}) GAGAL / Timeout.",
        ]);
    }

    /**
     * Tarik data absensi real dari memori mesin fisik via protokol ZKTeco / Fingerspot Socket (Port 4370)
     */
    public function pullFromDevice(Mesin $mesin)
    {
        if (empty($mesin->ip_address)) {
            return response()->json(['status' => 'error', 'message' => 'IP Address mesin belum diatur.'], 400);
        }

        $port = (int)($mesin->port ?: 4370);
        $ip = $mesin->ip_address;

        try {
            $zk = new \Rats\Zkteco\Lib\ZKTeco($ip, $port);
            if (!empty($zk->_zkclient)) {
                @socket_set_option($zk->_zkclient, SOL_SOCKET, SO_RCVTIMEO, ['sec' => 3, 'usec' => 0]);
                @socket_set_option($zk->_zkclient, SOL_SOCKET, SO_SNDTIMEO, ['sec' => 3, 'usec' => 0]);
            }

            if (!$zk->connect()) {
                return response()->json([
                    'status' => 'offline',
                    'success' => false,
                    'message' => "Tidak dapat terhubung ke soket mesin {$mesin->brand_mesin} ({$ip}:{$port}). Pastikan mesin menyala, kabel LAN terhubung, dan port 4370 terbuka.",
                ], 200);
            }

            $attendances = $zk->getAttendance();
            $zk->disconnect();

            $imported = 0;
            if (is_array($attendances)) {
                foreach ($attendances as $row) {
                    $pin = $row['id'] ?? null;
                    $time = $row['timestamp'] ?? null;
                    if ($pin && $time) {
                        if ($this->recordScan($mesin->serial, $pin, $time, $mesin->ruangan_id)) {
                            $imported++;
                        }
                    }
                }
            }

            $mesin->update([
                'status_online' => true,
                'last_synced_at' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'success' => true,
                'count' => $imported,
                'total_in_device' => is_array($attendances) ? count($attendances) : 0,
                'message' => "Sukses menarik {$imported} log scan baru dari mesin {$mesin->nama_mesin}!",
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'success' => false,
                'message' => "Gagal komunikasi dengan mesin: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper untuk memformat objek AttLog ke struktur representasi data lengkap
     */
    protected function formatLogItem(AttLog $log): array
    {
        $mhs = null;
        $dsn = null;
        $nama = 'Tidak Dikenal';
        $sub = '-';
        $identitas = '-';
        $tipe = $log->tipe_pengguna ?: 'mahasiswa';

        if ($tipe === 'dosen') {
            $dsn = Dosen::with('departemen')->where('id_finger', $log->pin)->first();
        } else {
            $mhs = Mahasiswa::with('kelas')->where('id_finger', $log->pin)->first();
            if (!$mhs) {
                $dsn = Dosen::with('departemen')->where('id_finger', $log->pin)->first();
                if ($dsn) {
                    $tipe = 'dosen';
                }
            }
        }

        if ($mhs) {
            $nama = $mhs->nama_mahasiswa;
            $identitas = $mhs->nim;
            $sub = $mhs->kelas?->nama_kelas ?? 'Mahasiswa FK';
            $tipe = 'mahasiswa';
        } elseif ($dsn) {
            $nama = $dsn->nama_lengkap;
            $identitas = $dsn->nip;
            $sub = $dsn->departemen?->nama_departemen ?? 'Dosen / Pengajar';
            $tipe = 'dosen';
        } else {
            $nama = 'PIN: ' . $log->pin;
            $identitas = 'ID ' . $log->pin;
            $sub = 'Belum terdaftar di Master Data';
            $tipe = 'unknown';
        }

        $scanDateTime = $log->scan_date ? Carbon::parse($log->scan_date) : now();

        return [
            'id' => $log->id,
            'pin' => $log->pin,
            'nama' => $nama,
            'identitas' => $identitas,
            'sub' => $sub,
            'tipe' => $tipe,
            'ruangan' => $log->ruangan?->nama_ruangan ?? ('Mesin ' . ($log->sn ?? '-')),
            'sn' => $log->sn ?? '-',
            'scan_date' => $scanDateTime->format('d/m/Y'),
            'scan_time' => $scanDateTime->format('H:i:s'),
            'time_ago' => $scanDateTime->diffForHumans(),
            'timestamp' => $scanDateTime->timestamp,
            'verified' => (int)($log->verified ?? 1),
        ];
    }
}
