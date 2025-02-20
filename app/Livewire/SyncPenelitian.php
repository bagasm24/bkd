<?php

namespace App\Livewire;

use App\Http\Controllers\PenelitianController;
use App\Models\Penelitian;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class SyncPenelitian extends Component
{
    public function syncDataPenelitian()
    {
        set_time_limit(0);
        try {
            $dataSdm = session()->get('sdm_data');
            $id_sdm = $dataSdm[0]['id_sdm'];
            $penelitianController = new PenelitianController();
            $dataPenelitian = $penelitianController->getDataPenelitianFromAPI($id_sdm);
            Log::info("Sinkronisasi proses untuk ID: {$dataSdm[0]['id_sdm']}");
            // Log::info("Data Penelitian: {$dataPenelitian}");

            foreach ($dataPenelitian as $penelitian) {
                Penelitian::updateOrCreate(
                    ['id_sdm' => $id_sdm, 'id_penelitian' => $penelitian['id']],
                    [
                        'judul' => $penelitian['judul'],
                        'lama_kegiatan' => $penelitian['lama_kegiatan'],
                        'bidang_keilmuan' => json_encode($penelitian['bidang_keilmuan']),
                        'tahun_pelaksanaan' => $penelitian['tahun_pelaksanaan'],
                    ]
                );
            }
            Log::info("Sinkronisasi selesai");
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Penelitian berhasil disinkronisasi.']);
        } catch (\Throwable $e) {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Terjadi kesalahan saat sinkronisasi.']);
        }
    }
    public function render()
    {
        return view('livewire.sync-penelitian');
    }
}
