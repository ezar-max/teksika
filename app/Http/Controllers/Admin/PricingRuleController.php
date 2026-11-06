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
     * Tampilkan halaman pengaturan tarif.
     */
    public function index()
    {
        $rules = DB::table('pricing_rules')->get()->keyBy('key');
        return view('admin.pricing.index', compact('rules'));
    }

    /**
     * Simpan perubahan aturan tarif.
     */
    public function update(Request $request)
    {
        $request->validate([
            'poin_tarif_persen' => 'sometimes|required|numeric|min:0|max:100',
            'pengalaman_per_tahun_persen' => 'sometimes|required|numeric|min:0|max:100',
            'pengalaman_batas_persen' => 'sometimes|required|numeric|min:0|max:100',
        ]);

        $inputs = $request->except(['_token', '_method']);

        // Simpan aturan harga yang masih dikelola di halaman ini.
        $labels = [
            'jarak_gratis_km' => 'Batas Jarak Gratis (km)',
            'tarif_per_km' => 'Tarif Per KM (Rp)',
            'radius_maksimal_km' => 'Radius Maksimal Layanan (km)',
            'poin_tarif_persen' => 'Biaya Poin (% tarif dasar)',
            'pengalaman_per_tahun_persen' => 'Tambahan Pengalaman (% per tahun)',
            'pengalaman_batas_persen' => 'Batas Tambahan Pengalaman (%)',
            'faktor_urgensi_rendah' => 'Faktor Urgensi Normal/Rendah',
            'faktor_urgensi_sedang' => 'Faktor Urgensi Mendesak/Sedang',
            'faktor_urgensi_tinggi' => 'Faktor Urgensi Darurat/Tinggi',
            'faktor_kerusakan_ringan' => 'Faktor Kerusakan Ringan',
            'faktor_kerusakan_sedang' => 'Faktor Kerusakan Sedang',
            'faktor_kerusakan_berat' => 'Faktor Kerusakan Berat',
        ];

        foreach ($inputs as $key => $value) {
            if (!array_key_exists($key, $labels)) {
                continue;
            }

            $normalizedValue = str_replace(',', '.', trim((string) $value));
            DB::table('pricing_rules')->updateOrInsert(
                ['key' => $key],
                [
                    'value' => $normalizedValue,
                    'label' => $labels[$key],
                    'updated_at' => now(),
                ]
            );
        }

        // 3. Bersihkan cache aturan harga
        $this->pricingService->clearRulesCache();

        return redirect()->route('admin.pricing.index')->with('success', 'Pengaturan tarif berhasil diperbarui!');
    }
}
