<?php

namespace Database\Seeders;

use App\Models\Message;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $messages = [
            [
                "title" => "Selamat Datang di INVIRO",
                "text"  => "Halo {nama}, terima kasih telah mengunjungi INVIRO. Kami siap membantu kebutuhan pengolahan air dan sistem RO untuk rumah, kantor, dan industri."
            ],
            [
                "title" => "Penawaran Gratis Konsultasi",
                "text"  => "{nama}, butuh rekomendasi sistem water treatment terbaik? Hubungi kami untuk konsultasi gratis — tim INVIRO siap membantu memilih solusi sesuai kebutuhan Anda."
            ],
            [
                "title" => "Special Promo Pemasangan Depot Air",
                "text"  => "{nama}, nikmati promo khusus pemasangan depot air minum isi ulang bulan ini. Hubungi kami segera sebelum promo berakhir!"
            ],
            [
                "title" => "Layanan Rumah Tangga & Komersial",
                "text"  => "Kami siap membantu Anda, {nama}. INVIRO menyediakan layanan pengolahan air untuk rumah tangga, kantor, pabrik, sekolah, dan usaha lainnya."
            ],
            [
                "title" => "Jaminan Kualitas INVIRO",
                "text"  => "{nama}, semua instalasi dan produk INVIRO dikerjakan oleh teknisi profesional dengan standar kualitas terbaik."
            ],
            [
                "title" => "Pemesanan Mudah & Cepat",
                "text"  => "Hai {nama}, butuh alat purifikasi atau sistem RO? Pesan sekarang — proses cepat dan pengiriman ke seluruh Indonesia."
            ],
            [
                "title" => "Servis & Maintenance Berkala",
                "text"  => "{nama}, pastikan kualitas air tetap optimal. Daftarkan layanan servis berkala dari INVIRO untuk pengecekan sistem Anda."
            ],
            [
                "title" => "Dukungan Pelanggan 24/7",
                "text"  => "Kami siap membantu kapan saja, {nama}. Silakan hubungi tim support INVIRO jika mengalami kendala atau butuh informasi."
            ],
            [
                "title" => "Testimoni Pelanggan",
                "text"  => "Ingin melihat pengalaman pelanggan lain, {nama}? Banyak klien telah merasakan manfaat sistem air dari INVIRO."
            ],
            [
                "title" => "Peluang Kemitraan",
                "text"  => "{nama}, jika Anda berminat menjadi mitra atau agen INVIRO, kami siap memberikan informasi peluang bisnis terbaik."
            ],
        ];

        foreach ($messages as $message) {
            Message::factory()->create($message);
        }
        Message::whereNotNull('id')->update(['default' => false]);
        Message::first()->update(['default' => true]);
    }
}
