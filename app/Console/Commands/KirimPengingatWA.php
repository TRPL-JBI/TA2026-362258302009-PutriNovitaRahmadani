<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\Penyewaan;

class KirimPengingatWA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kirim:pengingat-wa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim pengingat WhatsApp untuk penyewaan yang akan berakhir';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
{
    $this->info("=== Command Kirim WA dijalankan ===");

    $data = Penyewaan::where('status_penyewaan', 'sedang_disewa')
        ->where('sudah_diingatkan', 0)
        ->get();

    $this->info("Jumlah penyewaan yang perlu diingatkan: " . $data->count());

    if ($data->isEmpty()) {
        $this->info("Tidak ada penyewaan yang perlu diingatkan.");
        return;
    }

    foreach ($data as $item) {

        if (!$item->penyewa || !$item->penyewa->user || !$item->penyewa->user->no_telepon) {
            $this->info("Penyewa atau nomor telepon kosong, ID: {$item->idpenyewaan}, dilewati.");
            continue;
        }

        $now = Carbon::now();
        $waktu_selesai = Carbon::parse($item->tanggal_selesai)
            ->setHour(16)->setMinute(0)->setSecond(0);

        $waktu_pengingat = $waktu_selesai->copy()->subHours(3);

        if ($now->lt($waktu_pengingat)) {
            $this->info("Belum waktunya mengingatkan, ID: {$item->idpenyewaan}");
            continue;
        }

        if ($now->gt($waktu_selesai)) {
            $this->info("Sudah lewat jam 18:00, ID: {$item->idpenyewaan}");
            continue;
        }

        $nomor = preg_replace('/[^0-9]/', '', $item->penyewa->user->no_telepon);
        if (substr($nomor, 0, 1) == '0') {
            $nomor = '62' . substr($nomor, 1);
        }

        $pesan = "Assalamu'alaikum wr.wb.\n\n"
    ."Salam Lestari,"
    ."{$item->penyewa->user->nama}\n\n"
    ."Selamat siang. Kami ingin menginformasikan kepada seluruh customer OutdoorKriss Rental Alat Camp terkait waktu pengembalian barang:\n\n"
    ."1. Maksimal pengembalian pukul 16.00 WIB atau lebih awal.\n"
    ."2. Pastikan seluruh barang lengkap dan dalam kondisi aman.\n"
    ."3. Segera konfirmasi kepada admin apabila terdapat kendala.\n\n"
    ."NB: Atas kerja samanya kami ucapkan terima kasih 🙏";

        try {
            $response = Http::withHeaders([
                'Authorization' => env('FONTE_TOKEN')
            ])->asForm()->post(env('FONTE_URL'), [
                'target' => $nomor,
                'message' => $pesan,
            ]);

            $responseData = $response->json();

            if (!empty($responseData['status']) && $responseData['status'] === true) {
                $this->info("✅ WA berhasil ke: $nomor");
            } else {
                $this->error("❌ Gagal ke: $nomor");
                $this->info("Response: " . json_encode($responseData));
            }

        } catch (\Exception $e) {
            $this->error("❌ Exception: " . $e->getMessage());
        }

        // 🔥 tandai sudah diproses (anti spam)
        $item->update(['sudah_diingatkan' => 1]);
    }

    $this->info("=== Proses kirim pengingat selesai ===");
}
}