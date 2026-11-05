<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $bRating = (float) str_replace(',', '.', (string) ($inputs['bobot_rating'] ?? 0));
        $bPengalaman = (float) str_replace(',', '.', (string) ($inputs['bobot_pengalaman'] ?? 0));

        $totalBobot = round($bRating + $bPengalaman, 4);

        if (abs($totalBobot - 1.00) > 0.001) {
            return back()->withInput()->with(
                'error',
                "Total bobot rekomendasi tukang harus bernilai tepat 1,00 (100%). Saat ini total: {$totalBobot} (Rating: {$bRating}, Pengalaman: {$bPengalaman})."
            );
        }

        // Hapus parameter kecepatan_kmh, bobot_jarak, dan bobot_beban_kerja jika ada
        unset($inputs['kecepatan_kmh'], $inputs['bobot_jarak'], $inputs['bobot_beban_kerja']);
        DB::table('pricing_rules')->whereIn('key', ['kecepatan_kmh', 'bobot_jarak', 'bobot_beban_kerja'])->delete();

        // 2. Simpan setiap aturan ke tabel pricing_rules (update atau insert jika belum ada)
        foreach ($inputs as $key => $value) {
            $normalizedValue = str_replace(',', '.', trim((string) $value));
            DB::table('pricing_rules')->updateOrInsert(
                ['key' => $key],
                [
                    'value' => $normalizedValue,
                    'updated_at' => now(),
                ]
            );
        }

        // 3. Bersihkan cache aturan harga
        $this->pricingService->clearRulesCache();

        return redirect()->route('admin.pricing.index')->with('success', 'Pengaturan tarif dan parameter algoritma berhasil diperbarui!');
    }
}

