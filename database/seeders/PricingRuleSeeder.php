<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PricingRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rules = [
            // Jarak & Tarif
            [
                'key' => 'jarak_gratis_km',
                'value' => '5.0',
                'label' => 'Batas Jarak Gratis (km)',
                'tipe' => 'number',
                'keterangan' => 'Jarak perjalanan di bawah angka ini bebas biaya tambahan.',
            ],
            [
                'key' => 'tarif_per_km',
                'value' => '2500',
                'label' => 'Tarif Per KM (Rp)',
                'tipe' => 'number',
                'keterangan' => 'Biaya tambahan per kilometer setelah batas jarak gratis.',
            ],
            [
                'key' => 'radius_maksimal_km',
                'value' => '15.0',
                'label' => 'Radius Maksimal Layanan (km)',
                'tipe' => 'number',
                'keterangan' => 'Tukang di luar radius ini tidak akan dimasukkan ke rekomendasi.',
            ],
            // Faktor Urgensi Multiplier
            [
                'key' => 'faktor_urgensi_rendah',
                'value' => '1.00',
                'label' => 'Faktor Urgensi Normal/Rendah',
                'tipe' => 'number',
                'keterangan' => 'Pengali harga untuk urgensi normal (tarif standar).',
            ],
            [
                'key' => 'faktor_urgensi_sedang',
                'value' => '1.20',
                'label' => 'Faktor Urgensi Mendesak/Sedang',
                'tipe' => 'number',
                'keterangan' => 'Pengali harga untuk urgensi mendesak (+20%).',
            ],
            [
                'key' => 'faktor_urgensi_tinggi',
                'value' => '1.40',
                'label' => 'Faktor Urgensi Darurat/Tinggi',
                'tipe' => 'number',
                'keterangan' => 'Pengali harga untuk urgensi darurat (+40%).',
            ],
            [
                'key' => 'faktor_kerusakan_ringan',
                'value' => '1.00',
                'label' => 'Faktor Kerusakan Ringan',
                'tipe' => 'number',
                'keterangan' => 'Tidak ada tambahan harga untuk kerusakan ringan.',
            ],
            [
                'key' => 'faktor_kerusakan_sedang',
                'value' => '1.05',
                'label' => 'Faktor Kerusakan Sedang',
                'tipe' => 'number',
                'keterangan' => 'Tambahan harga 5% untuk kerusakan sedang.',
            ],
            [
                'key' => 'faktor_kerusakan_berat',
                'value' => '1.10',
                'label' => 'Faktor Kerusakan Berat',
                'tipe' => 'number',
                'keterangan' => 'Tambahan harga 10% untuk kerusakan berat.',
            ],
            // Penyesuaian Rating & Pengalaman Tukang
            [
                'key' => 'persen_rating_maks',
                'value' => '15.00',
                'label' => 'Penyesuaian Rating Maksimal (%)',
                'tipe' => 'percent',
                'keterangan' => 'Persentase tambahan maksimal untuk tukang dengan rating 5.0.',
            ],
            [
                'key' => 'persen_pengalaman_per_tahun',
                'value' => '2.00',
                'label' => 'Penyesuaian Pengalaman per Tahun (%)',
                'tipe' => 'percent',
                'keterangan' => 'Persentase tambahan untuk setiap tahun pengalaman tukang.',
            ],
            [
                'key' => 'persen_pengalaman_maks',
                'value' => '20.00',
                'label' => 'Batas Penyesuaian Pengalaman Maksimal (%)',
                'tipe' => 'percent',
                'keterangan' => 'Batas maksimal persentase tambahan dari pengalaman kerja.',
            ],
            [
                'key' => 'persen_kualifikasi_min',
                'value' => '0.00',
                'label' => 'Batas Minimum Penyesuaian Kualifikasi (%)',
                'tipe' => 'percent',
                'keterangan' => 'Persentase minimum gabungan penyesuaian rating dan pengalaman.',
            ],
            [
                'key' => 'persen_kualifikasi_maks',
                'value' => '35.00',
                'label' => 'Batas Maksimum Penyesuaian Kualifikasi (%)',
                'tipe' => 'percent',
                'keterangan' => 'Persentase maksimal gabungan penyesuaian rating dan pengalaman agar harga tetap wajar.',
            ],
            // Transportasi, Batas Harga & Pembulatan
            [
                'key' => 'mode_transportasi',
                'value' => 'satu_arah',
                'label' => 'Mode Jarak Transportasi',
                'tipe' => 'text',
                'keterangan' => 'Gunakan satu_arah (1x jarak) atau pulang_pergi (2x jarak).',
            ],
            [
                'key' => 'batas_harga_jasa_min',
                'value' => '0',
                'label' => 'Batas Minimum Harga Jasa (Rp)',
                'tipe' => 'number',
                'keterangan' => 'Batas bawah subtotal jasa (0 berarti tidak dibatasi).',
            ],
            [
                'key' => 'batas_harga_jasa_max',
                'value' => '0',
                'label' => 'Batas Maksimum Harga Jasa (Rp)',
                'tipe' => 'number',
                'keterangan' => 'Batas atas subtotal jasa (0 berarti tidak dibatasi).',
            ],
            [
                'key' => 'pembulatan_harga',
                'value' => '100',
                'label' => 'Kelipatan Pembulatan Harga (Rp)',
                'tipe' => 'number',
                'keterangan' => 'Pembulatan nominal biaya (1, 100, 500, atau 1000).',
            ],

        ];

        foreach ($rules as $rule) {
            DB::table('pricing_rules')->updateOrInsert(
                ['key' => $rule['key']],
                [
                    'value' => $rule['value'],
                    'label' => $rule['label'],
                    'tipe' => $rule['tipe'],
                    'keterangan' => $rule['keterangan'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        DB::table('pricing_rules')->whereIn('key', [
            'poin_tarif_persen',
            'pengalaman_per_tahun_persen',
            'pengalaman_batas_persen',
        ])->delete();
    }
}
