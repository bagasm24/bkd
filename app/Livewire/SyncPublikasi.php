<?php

namespace App\Livewire;

use App\Http\Controllers\PublikasiController;
use App\Http\Controllers\SdmController;
use App\Models\Publikasi;
use Exception;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class SyncPublikasi extends Component
{
    public function syncDataPublikasi()
    {
        set_time_limit(0);
        try {
            $dataSdm = session()->get('sdm_data');
            $id_sdm = $dataSdm[0]['id_sdm'];
            $dataPublikasiManual = Publikasi::where('id_sdm', $id_sdm)
                ->where('asal_data', 'MANUAL')
                ->get();
            $publikasiController = new PublikasiController();
            $dataPublikasi = $publikasiController->getDetailPublikasi($id_sdm);

            $judulManual = $dataPublikasiManual->pluck('judul')->map(fn ($judul) => trim(strtolower($judul)))->toArray();
            $judulApi = collect($dataPublikasi)->pluck('judul')->map(fn ($judul) => trim(strtolower($judul)))->toArray();

            // Cari judul yang sama
            $judulSama = array_intersect($judulManual, $judulApi);

            $dataManualSama = $dataPublikasiManual->filter(fn ($item) => in_array(trim(strtolower($item->judul)), $judulSama));

            $dataManualSama->each(function ($item) {
                $item->delete();
            });
            foreach ($dataPublikasi as $publikasi) {
                $penulis = $publikasi['penulis'];
                $urutanPenulis = null;

                foreach ($penulis as $penulisItem) {
                    if ($penulisItem['id_sdm'] == $id_sdm) {
                        $urutanPenulis = $penulisItem['urutan'];
                        break;
                    }
                }
                Publikasi::updateOrCreate(
                    ['id_sdm' => $id_sdm, 'id_publikasi' => $publikasi['id']],
                    [
                        'judul' => $publikasi['judul'],
                        'nama_jurnal' => $publikasi['nama_jurnal'],
                        'tanggal' => $publikasi['tanggal'],
                        'jenis_publikasi' => $publikasi['id_jenis_publikasi'],
                        'kategori_kegiatan' => $publikasi['id_kategori_kegiatan'],
                        'urutan_penulis' => $urutanPenulis,
                        'asal_data' => $publikasi['asal_data'],
                        'penerbit' => $publikasi['penerbit'],
                    ]
                );
            }
            Log::info("Sinkronisasi selesai");
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Publikasi berhasil disinkronisasi.']);
        } catch (Exception $e) {
            Log::error("Gagal sinkronisasi: " . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Terjadi kesalahan saat sinkronisasi.']);
        }
    }
    public function render()
    {
        return view('livewire.sync-publikasi');
    }
}
