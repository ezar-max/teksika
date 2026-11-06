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
            [
                'key' => 'poin_tarif_persen',
                'value' => '2.00',
                'label' => 'Biaya Poin (% tarif dasar)',
                'tipe' => 'number',
                'keterangan' => 'Persentase tarif dasar yang dikalikan dengan poin/100.',
            ],
            [
                'key' => 'pengalaman_per_tahun_persen',
                'value' => '1.00',
                'label' => 'Tambahan Pengalaman (% per tahun)',
                'tipe' => 'number',
                'keterangan' => 'Persentase tambahan tarif dasar untuk setiap tahun pengalaman.',
            ],
            [
                'key' => 'pengalaman_batas_persen',
                'value' => '25.00',
                'label' => 'Batas Tambahan Pengalaman (%)',
                'tipe' => 'number',
                'keterangan' => 'Batas maksimum persentase tambahan tarif dasar dari pengalaman.',
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
    }
}
