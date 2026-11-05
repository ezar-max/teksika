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

