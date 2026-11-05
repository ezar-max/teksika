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

            // Bobot Weighted Scoring (Total harus 1.00)
            [
                'key' => 'bobot_jarak',
                'value' => '0.25',
                'label' => 'Bobot Jarak (0 - 1)',
                'tipe' => 'number',
                'keterangan' => 'Prioritas kedekatan jarak tukang ke lokasi user.',
            ],
            [
                'key' => 'bobot_rating',
                'value' => '0.15',
                'label' => 'Bobot Rating (0 - 1)',
                'tipe' => 'number',
                'keterangan' => 'Prioritas kepuasan pelanggan / penilaian performa tukang.',
            ],
            [
                'key' => 'bobot_pengalaman',
                'value' => '0.15',
                'label' => 'Bobot Pengalaman (0 - 1)',
                'tipe' => 'number',
                'keterangan' => 'Prioritas jam terbang / masa kerja tukang dalam tahun.',
            ],
            [
                'key' => 'bobot_beban_kerja',
                'value' => '0.45',
                'label' => 'Bobot Beban Kerja (0 - 1)',
                'tipe' => 'number',
                'keterangan' => 'Prioritas ketersediaan tukang (job aktif lebih sedikit lebih diutamakan).',
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

            // Pengali Durasi Kerja berdasarkan Kompleksitas
            [
                'key' => 'pengali_durasi_rendah',
                'value' => '1.00',
                'label' => 'Pengali Durasi Kompleksitas Rendah',
                'tipe' => 'number',
                'keterangan' => 'Pengali durasi dasar untuk pekerjaan ringan.',
            ],
            [
                'key' => 'pengali_durasi_sedang',
                'value' => '1.50',
                'label' => 'Pengali Durasi Kompleksitas Sedang',
                'tipe' => 'number',
                'keterangan' => 'Pengali durasi dasar untuk pekerjaan standar (misal: 60 menit x 1.5 = 90 menit).',
            ],
            [
                'key' => 'pengali_durasi_tinggi',
                'value' => '2.00',
                'label' => 'Pengali Durasi Kompleksitas Tinggi',
                'tipe' => 'number',
                'keterangan' => 'Pengali durasi dasar untuk pekerjaan berat/rumit.',
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

