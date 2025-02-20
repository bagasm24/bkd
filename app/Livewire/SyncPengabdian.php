<?php

namespace App\Livewire;

use App\Http\Controllers\PengabdianController;
use App\Models\Pengabdian;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class SyncPengabdian extends Component
{
    public function syncDataPengabdian()
    {
        set_time_limit(0);
        try {
            $dataSdm = session()->get('sdm_data');
            $id_sdm = $dataSdm[0]['id_sdm'];
            $pengabdianController = new PengabdianController();
            $dataPengabdian = $pengabdianController->getDataPengabdianFromAPI($id_sdm);

            foreach ($dataPengabdian as $pengabdian) {
                Pengabdian::updateOrCreate(
                    ['id_sdm' => $id_sdm, 'id_pengabdian' => $pengabdian['id']],
                    [
                        'judul' => $pengabdian['judul'],
                        'lama_kegiatan' => $pengabdian['lama_kegiatan'],
                        'bidang_keilmuan' => json_encode($pengabdian['bidang_keilmuan']),
                        'tahun_pelaksanaan' => $pengabdian['tahun_pelaksanaan'],
                    ]
                );
            }
            Log::info("Sinkronisasi selesai");
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Pengabdian berhasil disinkronisasi.']);
        } catch (\Throwable $e) {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Terjadi kesalahan saat sinkronisasi.']);
        }
    }

    public function render()
    {
        return view('livewire.sync-pengabdian');
    }
}
