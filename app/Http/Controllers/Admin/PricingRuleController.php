<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\PricingService;

class PricingRuleController extends Controller
{
    protected PricingService $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    /**
     * Tampilkan halaman Pengaturan Tarif & Bobot Algoritma
     */
    public function index()
    {
        $rules = DB::table('pricing_rules')->get()->keyBy('key');
        return view('admin.pricing.index', compact('rules'));
    }

    /**
     * Simpan perubahan Pengaturan Tarif & Bobot Algoritma
     */
    public function update(Request $request)
    {
        $inputs = $request->except(['_token', '_method']);

        // 1. Validasi Total Bobot (Harus bernilai 1.00)
        $bJarak = (float) str_replace(',', '.', (string) ($inputs['bobot_jarak'] ?? 0));
        $bRating = (float) str_replace(',', '.', (string) ($inputs['bobot_rating'] ?? 0));
        $bPengalaman = (float) str_replace(',', '.', (string) ($inputs['bobot_pengalaman'] ?? 0));
        $bBeban = (float) str_replace(',', '.', (string) ($inputs['bobot_beban_kerja'] ?? 0));

        $totalBobot = round($bJarak + $bRating + $bPengalaman + $bBeban, 4);

        if (abs($totalBobot - 1.00) > 0.001) {
            return back()->withInput()->with(
                'error',
                "Total bobot rekomendasi tukang harus bernilai tepat 1,00 (100%). Saat ini total: {$totalBobot} (Jarak: {$bJarak}, Rating: {$bRating}, Pengalaman: {$bPengalaman}, Beban: {$bBeban})."
            );
        }

        // 2. Simpan setiap aturan ke tabel pricing_rules (update atau insert jika belum ada)
        $labels = [
            'jarak_gratis_km' => 'Batas Jarak Gratis (km)',
            'tarif_per_km' => 'Tarif Per KM (Rp)',
            'radius_maksimal_km' => 'Radius Maksimal Layanan (km)',
            'bobot_jarak' => 'Bobot Jarak (0 - 1)',
            'bobot_rating' => 'Bobot Rating (0 - 1)',
            'bobot_pengalaman' => 'Bobot Pengalaman (0 - 1)',
            'bobot_beban_kerja' => 'Bobot Beban Kerja (0 - 1)',
            'faktor_urgensi_rendah' => 'Faktor Urgensi Normal/Rendah',
            'faktor_urgensi_sedang' => 'Faktor Urgensi Mendesak/Sedang',
            'faktor_urgensi_tinggi' => 'Faktor Urgensi Darurat/Tinggi',
            'faktor_kerusakan_ringan' => 'Faktor Kerusakan Ringan',
            'faktor_kerusakan_sedang' => 'Faktor Kerusakan Sedang',
            'faktor_kerusakan_berat' => 'Faktor Kerusakan Berat',
            'pengali_durasi_sedang' => 'Pengali Durasi Kompleksitas Sedang',
            'pengali_durasi_tinggi' => 'Pengali Durasi Kompleksitas Tinggi',
        ];

        foreach ($inputs as $key => $value) {
            $normalizedValue = str_replace(',', '.', trim((string) $value));
            DB::table('pricing_rules')->updateOrInsert(
                ['key' => $key],
                [
                    'value' => $normalizedValue,
                    'label' => $labels[$key] ?? Str::headline($key),
                    'updated_at' => now(),
                ]
            );
        }

        // 3. Bersihkan cache aturan harga
        $this->pricingService->clearRulesCache();

        return redirect()->route('admin.pricing.index')->with('success', 'Pengaturan tarif dan parameter algoritma berhasil diperbarui!');
    }
}

