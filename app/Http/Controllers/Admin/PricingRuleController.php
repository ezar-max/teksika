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
            'jarak_gratis_km' => 'sometimes|required|numeric|min:0|max:100',
            'tarif_per_km' => 'sometimes|required|numeric|min:0',
            'radius_maksimal_km' => 'sometimes|required|numeric|min:1|max:100',
            'mode_transportasi' => 'sometimes|required|in:satu_arah,pulang_pergi',
            'faktor_urgensi_rendah' => 'sometimes|required|numeric|min:1|max:3',
            'faktor_urgensi_sedang' => 'sometimes|required|numeric|min:1|max:3',
            'faktor_urgensi_tinggi' => 'sometimes|required|numeric|min:1|max:3',
            'faktor_kerusakan_ringan' => 'sometimes|required|numeric|min:1|max:3',
            'faktor_kerusakan_sedang' => 'sometimes|required|numeric|min:1|max:3',
            'faktor_kerusakan_berat' => 'sometimes|required|numeric|min:1|max:3',
            'persen_rating_maks' => 'sometimes|required|numeric|min:0|max:100',
            'persen_pengalaman_per_tahun' => 'sometimes|required|numeric|min:0|max:50',
            'persen_pengalaman_maks' => 'sometimes|required|numeric|min:0|max:100',
            'persen_kualifikasi_min' => 'sometimes|required|numeric|min:0|max:100',
            'persen_kualifikasi_maks' => 'sometimes|required|numeric|min:0|max:100|gte:persen_kualifikasi_min',
            'batas_harga_jasa_min' => 'sometimes|required|numeric|min:0',
            'batas_harga_jasa_max' => 'sometimes|required|numeric|min:0',
            'pembulatan_harga' => 'sometimes|required|in:1,100,500,1000',
        ]);

        $inputs = $request->except(['_token', '_method']);

        // Simpan aturan harga yang dikelola di halaman ini.
        $labels = [
            'jarak_gratis_km' => 'Batas Jarak Gratis (km)',
            'tarif_per_km' => 'Tarif Per KM (Rp)',
            'radius_maksimal_km' => 'Radius Maksimal Layanan (km)',
            'mode_transportasi' => 'Mode Jarak Transportasi',
            'faktor_urgensi_rendah' => 'Faktor Urgensi Normal/Rendah',
            'faktor_urgensi_sedang' => 'Faktor Urgensi Mendesak/Sedang',
            'faktor_urgensi_tinggi' => 'Faktor Urgensi Darurat/Tinggi',
            'faktor_kerusakan_ringan' => 'Faktor Kerusakan Ringan',
            'faktor_kerusakan_sedang' => 'Faktor Kerusakan Sedang',
            'faktor_kerusakan_berat' => 'Faktor Kerusakan Berat',
            'persen_rating_maks' => 'Penyesuaian Rating Maksimal (%)',
            'persen_pengalaman_per_tahun' => 'Penyesuaian Pengalaman per Tahun (%)',
            'persen_pengalaman_maks' => 'Batas Penyesuaian Pengalaman Maksimal (%)',
            'persen_kualifikasi_min' => 'Batas Minimum Penyesuaian Kualifikasi (%)',
            'persen_kualifikasi_maks' => 'Batas Maksimum Penyesuaian Kualifikasi (%)',
            'batas_harga_jasa_min' => 'Batas Minimum Harga Jasa (Rp)',
            'batas_harga_jasa_max' => 'Batas Maksimum Harga Jasa (Rp)',
            'pembulatan_harga' => 'Kelipatan Pembulatan Harga (Rp)',
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
