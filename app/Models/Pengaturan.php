<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    use HasFactory;

    protected $table = 'pengaturans';

    protected $fillable = [
        'nama_pengaturan',
        'app_name',
        'institution_name',
        'faculty_name',
        'short_name',
        'institution_logo',
        'website_url',
        'email_contact',
        'running_text',
        'theme_color',
        'primary_color_hex',
        'secondary_color_hex',
        'menit_perjam',
        'menit_pergantian',
        'jam_kuliah',
        'jam_mulai_istirahat',
        'jam_selesai_istirahat',
        'jam_perhari',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'menit_perjam' => 'integer',
        'menit_pergantian' => 'integer',
        'jam_perhari' => 'integer',
    ];

    /**
     * Dapatkan konfigurasi warna tema saat ini
     */
    public function getThemeConfigAttribute(): array
    {
        return match ($this->theme_color) {
            'umb-blue' => [
                'primary' => '#1d4ed8', // Royal Blue
                'primary_hover' => '#1e40af',
                'primary_light' => '#eff6ff',
                'primary_border' => '#bfdbfe',
                'primary_text' => '#1e3a8a',
                'badge' => 'bg-blue-100 text-blue-800 border-blue-200',
                'gradient' => 'from-blue-950 via-slate-900 to-indigo-950',
                'sidebar' => 'bg-slate-950',
            ],
            'emerald-green' => [
                'primary' => '#059669', // Emerald
                'primary_hover' => '#047857',
                'primary_light' => '#ecfdf5',
                'primary_border' => '#a7f3d0',
                'primary_text' => '#064e3b',
                'badge' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                'gradient' => 'from-emerald-950 via-slate-900 to-teal-950',
                'sidebar' => 'bg-slate-950',
            ],
            'violet-purple' => [
                'primary' => '#7c3aed', // Purple
                'primary_hover' => '#6d28d9',
                'primary_light' => '#f5f3ff',
                'primary_border' => '#ddd6fe',
                'primary_text' => '#4c1d95',
                'badge' => 'bg-purple-100 text-purple-800 border-purple-200',
                'gradient' => 'from-purple-950 via-slate-900 to-slate-950',
                'sidebar' => 'bg-slate-950',
            ],
            'amber-orange' => [
                'primary' => '#d97706', // Amber
                'primary_hover' => '#b45309',
                'primary_light' => '#fffbeb',
                'primary_border' => '#fde68a',
                'primary_text' => '#78350f',
                'badge' => 'bg-amber-100 text-amber-800 border-amber-200',
                'gradient' => 'from-amber-950 via-slate-900 to-slate-950',
                'sidebar' => 'bg-slate-950',
            ],
            'custom' => [
                'primary' => $this->primary_color_hex ?: '#b91c1c',
                'primary_hover' => $this->primary_color_hex ?: '#991b1b',
                'primary_light' => '#f8fafc',
                'primary_border' => '#e2e8f0',
                'primary_text' => '#0f172a',
                'badge' => 'bg-slate-100 text-slate-800 border-slate-200',
                'gradient' => 'from-slate-950 via-slate-900 to-slate-950',
                'sidebar' => 'bg-slate-950',
            ],
            default => [ // unhas-red
                'primary' => '#b91c1c', // Crimson Red
                'primary_hover' => '#991b1b',
                'primary_light' => '#fef2f2',
                'primary_border' => '#fecaca',
                'primary_text' => '#7f1d1d',
                'badge' => 'bg-red-100 text-red-800 border-red-200',
                'gradient' => 'from-red-950 via-slate-900 to-slate-950',
                'sidebar' => 'bg-slate-950',
            ],
        };
    }
}
