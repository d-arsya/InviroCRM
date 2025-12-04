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
                "title" => "Terima Kasih, {nama}",
                "text"  => "Halo {nama}, terima kasih telah berbelanja. Jika persediaan Anda mulai menipis, kami siap mengantarkan pesanan berikutnya kapan saja."
            ],
            [
                "title" => "Siap Isi Ulang Kapan Pun",
                "text"  => "{nama}, jika Anda membutuhkan isi ulang atau ingin memesan produk tambahan, cukup balas pesan ini. Tim kami siap melayani."
            ],
            [
                "title" => "Butuh Pesanan Tambahan?",
                "text"  => "Halo {nama}, terima kasih atas pembelian Anda kemarin. Jika stok sudah mulai berkurang, kami siap mengirimkan pesanan baru."
            ],
            [
                "title" => "Kami Siap Antar Lagi",
                "text"  => "{nama}, jika Anda membutuhkan pembelian berikutnya, kami siap membantu dengan cepat dan mudah. Pesan kapan saja melalui pesan ini."
            ],
            [
                "title" => "Jangan Ragu untuk Reorder",
                "text"  => "Halo {nama}, jika kebutuhan air minum atau gas Anda sudah hampir habis, Anda bisa melakukan repeat order kapan saja. Kami siap melayani."
            ],
            [
                "title" => "Pesan Lagi Lebih Mudah",
                "text"  => "{nama}, terima kasih telah memilih layanan kami. Untuk pemesanan ulang, cukup kirimkan pesan 'Order' dan tim kami akan memprosesnya."
            ],
            [
                "title" => "Ingat, Kami Selalu Tersedia",
                "text"  => "Halo {nama}, jika Anda membutuhkan refill, galon tambahan, atau produk lain, cukup hubungi kami. Kami siap antar dengan cepat."
            ],
            [
                "title" => "Siap Antar Seperti Biasa",
                "text"  => "{nama}, kami selalu siap mengantarkan kebutuhan harian Anda. Jika sudah mendekati habis, tinggal pesan kembali saja."
            ],
            [
                "title" => "Kapan Pun Anda Butuh",
                "text"  => "Halo {nama}, kami berharap produk kami membantu kebutuhan Anda. Jika butuh isi ulang atau produk tambahan, tinggal balas pesan ini."
            ],
            [
                "title" => "Terima Kasih & Yuk Order Lagi",
                "text"  => "{nama}, terima kasih atas pembelian Anda. Jika ingin melakukan pembelian ulang, kami siap memproses pesanan kapan pun Anda butuh."
            ],
        ];


        foreach ($messages as $message) {
            Message::factory()->create($message);
        }
        Message::whereNotNull('id')->update(['default' => false]);
        Message::first()->update(['default' => true]);
    }
}
