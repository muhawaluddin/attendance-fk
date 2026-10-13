<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use App\Services\LogoProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PengaturanController extends Controller
{
    public function index()
    {
        $pengaturans = Pengaturan::orderBy('id', 'desc')->get();
        $activeSetting = Pengaturan::where('is_active', true)->first() ?? $pengaturans->first();

        // Opsi preset tema
        $themePresets = [
            'unhas-red' => [
                'name' => 'UNHAS Crimson Red & Navy',
                'primary' => '#b91c1c',
                'secondary' => '#0f172a',
                'desc' => 'Identitas resmi Universitas Hasanuddin (Merah & Navy Gelap)',
            ],
            'umb-blue' => [
                'name' => 'UMB Palopo Royal Blue & Navy',
                'primary' => '#1d4ed8',
                'secondary' => '#0f172a',
                'desc' => 'Identitas Universitas Mega Buana Palopo (Biru Elegan & Navy Modern)',
            ],
            'emerald-green' => [
                'name' => 'Medical Emerald Green',
                'primary' => '#059669',
                'secondary' => '#0f172a',
                'desc' => 'Nuansa Hijau Medis & Pelayanan Kesehatan',
            ],
            'violet-purple' => [
                'name' => 'Academic Royal Violet',
                'primary' => '#7c3aed',
                'secondary' => '#0f172a',
                'desc' => 'Nuansa Ungu Akademik Modern & Futuristik',
            ],
            'amber-orange' => [
                'name' => 'Warm Amber Gold',
                'primary' => '#d97706',
                'secondary' => '#0f172a',
                'desc' => 'Nuansa Emas Kehangatan & Prestasi',
            ],
            'custom' => [
                'name' => 'Warna Custom (Bebas Tentukan Sendiri)',
                'primary' => $activeSetting->primary_color_hex ?? '#b91c1c',
                'secondary' => $activeSetting->secondary_color_hex ?? '#0f172a',
                'desc' => 'Pilih kode warna HEX primer dan sekunder sesuai kebutuhan',
            ],
        ];

        return view('pengaturan.index', compact('pengaturans', 'activeSetting', 'themePresets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pengaturan' => ['required', 'string', 'max:255'],
            'menit_perjam' => ['required', 'integer', 'min:10', 'max:180'],
            'menit_pergantian' => ['required', 'integer', 'min:0', 'max:60'],
            'jam_kuliah' => ['required', 'string'],
            'jam_mulai_istirahat' => ['required', 'string'],
            'jam_selesai_istirahat' => ['required', 'string'],
            'jam_perhari' => ['required', 'integer', 'min:1', 'max:16'],
        ], [
            'nama_pengaturan.required' => 'Nama pengaturan wajib diisi.',
            'menit_perjam.required' => 'Jumlah menit perjam wajib diisi.',
            'menit_pergantian.required' => 'Jumlah menit pergantian wajib diisi.',
        ]);

        if ($request->boolean('is_active')) {
            Pengaturan::query()->update(['is_active' => false]);
            $validated['is_active'] = true;
        }

        Pengaturan::create($validated);
        return redirect()->route('pengaturan.index')->with('success', 'Pengaturan perkuliahan berhasil ditambahkan!');
    }

    public function update(Request $request, Pengaturan $pengaturan)
    {
        $validated = $request->validate([
            'nama_pengaturan' => ['required', 'string', 'max:255'],
            'menit_perjam' => ['required', 'integer', 'min:10', 'max:180'],
            'menit_pergantian' => ['required', 'integer', 'min:0', 'max:60'],
            'jam_kuliah' => ['required', 'string'],
            'jam_mulai_istirahat' => ['required', 'string'],
            'jam_selesai_istirahat' => ['required', 'string'],
            'jam_perhari' => ['required', 'integer', 'min:1', 'max:16'],
            'is_active' => ['nullable'],
        ]);

        if ($request->boolean('is_active')) {
            Pengaturan::where('id', '!=', $pengaturan->id)->update(['is_active' => false]);
            $validated['is_active'] = true;
        }

        $pengaturan->update($validated);
        return redirect()->route('pengaturan.index')->with('success', 'Pengaturan berhasil diperbarui!');
    }

    /**
     * Update Branding Institusi dan Tema Warna Global
     */
    public function updateBranding(Request $request)
    {
        $validated = $request->validate([
            'app_name' => ['required', 'string', 'max:100'],
            'institution_name' => ['required', 'string', 'max:255'],
            'faculty_name' => ['nullable', 'string', 'max:255'],
            'short_name' => ['required', 'string', 'max:50'],
            'website_url' => ['nullable', 'string', 'max:255'],
            'email_contact' => ['nullable', 'string', 'max:255'],
            'running_text' => ['nullable', 'string'],
            'theme_color' => ['required', 'string', 'in:unhas-red,umb-blue,emerald-green,violet-purple,amber-orange,custom'],
            'primary_color_hex' => ['nullable', 'string', 'max:20'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,ico', 'max:2048'],
        ], [
            'institution_name.required' => 'Nama institusi/kampus wajib diisi (misal: UMB Palopo).',
            'short_name.required' => 'Singkatan nama kampus wajib diisi.',
        ]);

        $activeSetting = Pengaturan::where('is_active', true)->first() ?? Pengaturan::first();

        if (!$activeSetting) {
            $activeSetting = Pengaturan::create(['nama_pengaturan' => 'Default']);
        }

        // Handle upload logo with automatic background removal
        if ($request->hasFile('logo')) {
            $logoFile = $request->file('logo');
            $filename = 'logo_' . time() . '.png';
            $destinationPath = public_path('assets/img/branding');
            
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $targetPath = $destinationPath . '/' . $filename;

            // Otomatis hilangkan latar belakang dan simpan sebagai PNG transparan
            LogoProcessor::removeBackground($logoFile->getRealPath(), $targetPath);

            $validated['institution_logo'] = 'assets/img/branding/' . $filename;
        }

        // Tentukan warna hex otomatis berdasarkan pilihan tema
        if ($validated['theme_color'] === 'umb-blue') {
            $validated['primary_color_hex'] = '#1d4ed8';
        } elseif ($validated['theme_color'] === 'unhas-red') {
            $validated['primary_color_hex'] = '#b91c1c';
        } elseif ($validated['theme_color'] === 'emerald-green') {
            $validated['primary_color_hex'] = '#059669';
        } elseif ($validated['theme_color'] === 'violet-purple') {
            $validated['primary_color_hex'] = '#7c3aed';
        } elseif ($validated['theme_color'] === 'amber-orange') {
            $validated['primary_color_hex'] = '#d97706';
        }

        // Update semua record pengaturan agar sinkron
        Pengaturan::query()->update([
            'app_name' => $validated['app_name'],
            'institution_name' => $validated['institution_name'],
            'faculty_name' => $validated['faculty_name'] ?? '',
            'short_name' => $validated['short_name'],
            'website_url' => $validated['website_url'] ?? '',
            'email_contact' => $validated['email_contact'] ?? '',
            'running_text' => $validated['running_text'] ?? '',
            'theme_color' => $validated['theme_color'],
            'primary_color_hex' => $validated['primary_color_hex'] ?? '#1d4ed8',
            'institution_logo' => $validated['institution_logo'] ?? $activeSetting->institution_logo,
        ]);

        return redirect()->route('pengaturan.index')->with('success', "Branding institusi ({$validated['institution_name']}) dan tema warna berhasil disimpan secara global!");
    }

    public function setActive(Pengaturan $pengaturan)
    {
        Pengaturan::where('id', '!=', $pengaturan->id)->update(['is_active' => false]);
        $pengaturan->update(['is_active' => true]);

        return redirect()->route('pengaturan.index')->with('success', "Pengaturan '{$pengaturan->nama_pengaturan}' sekarang berstatus aktif.");
    }
}
