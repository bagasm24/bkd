<?php

namespace App\Livewire;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SdmController;
use Livewire\Component;
use App\Models\Sdm;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SyncSdmData extends Component
{
    private ?string $lastProcessedId = null;

    protected $listeners = ['syncData', 'sync-completed'];

    // public function syncData()
    // {
    //     set_time_limit(0);

    //     try {
    //         $sdmController = app(SdmController::class);
    //         $this->lastProcessedId = Cache::get('last_processed_id', '');
    //         $query = Sdm::orderBy('id_sdm', 'asc');
    //         if (!empty($this->lastProcessedId)) {
    //             $query->where('id_sdm', '>', $this->lastProcessedId);
    //         }

    //         $query->chunk(100, function ($sdms) use ($sdmController) {
    //             foreach ($sdms as $sdm) {
    //                 try {
    //                     // Ambil detail jabatan
    //                     $detailJabatan = $sdmController->getDetailJabatanFungsional($sdm->id_sdm);

    //                     // Update data sdm dengan detail jabatan
    //                     $sdm->update([
    //                         'jabatan_fungsional' => $detailJabatan['jabatan_fungsional'] ?? null,
    //                         'angka_kredit' => $detailJabatan['angka_kredit'] ?? null,
    //                     ]);

    //                     // Jalankan rekomendasi
    //                     $rekomendasi = $sdmController->getRekomendasiTunjangan($sdm->id_sdm);

    //                     // Update data sdm dengan rekomendasi
    //                     $sdm->update([
    //                         'rekomendasi' => $rekomendasi['message'] ?? null,
    //                         'keterangan' => $rekomendasi['detail'] ?? null,
    //                     ]);

    //                     Log::info("Sinkronisasi selesai untuk ID: {$sdm->id_sdm}");
    //                     $this->lastProcessedId = $sdm->id_sdm;
    //                     Cache::put('last_processed_id', $this->lastProcessedId);
    //                 } catch (\Throwable $e) {
    //                     Log::error("Gagal sinkronisasi ID: {$sdm->id_sdm}, Error: " . $e->getMessage());
    //                 }
    //             }
    //         });
    //         Log::info("Sinkronisasi selesai");
    //         $this->dispatch('notification', ['type' => 'success', 'message' => 'Data SDM berhasil disinkronisasi.']);
    //     } catch (\Exception $e) {
    //         Log::error("Gagal sinkronisasi: " . $e->getMessage());
    //         $this->dispatch('notification', ['type' => 'error', 'message' => 'Terjadi kesalahan saat sinkronisasi.']);
    //     }

    //     $this->dispatch('sync-completed');
    //     Cache::forget('last_processed_id');
    // }

    public function getToken()
    {
        $baseUrl = env('BASE_URL');

        if (Cache::has('api_token')) {
            return Cache::get('api_token');
        }

        $response = Http::post($baseUrl . '/authorize', [
            'username' => env('API_USERNAME'),
            'password' => env('API_PASSWORD'),
            'id_pengguna' => env('API_ID_PENGGUNA'),
        ]);

        if ($response->successful()) {
            $token = $response->json()['token'];
            Cache::put('api_token', $token, now()->addMinutes(55));
            return $token;
        }

        throw new \Exception("Gagal mendapatkan token API");
    }


    public function syncData()
    {
        set_time_limit(0);

        try {
            $getToken = $this->getToken();
            $baseUrl = env('BASE_URL'); // Sesuaikan dengan URL API Anda

            $this->lastProcessedId = Cache::get('last_processed_id', '');

            // Ambil data dari API
            $response = Http::withToken($getToken)
                ->get($baseUrl . '/referensi/sdm');

            if ($response->successful()) {
                $responseData = $response->json();

                // Urutkan data berdasarkan 'id_sdm' secara ascending
                usort($responseData, function ($a, $b) {
                    return $a['id_sdm'] <=> $b['id_sdm'];
                });

                foreach ($responseData as $sdm) {
                    // Hanya proses data dengan id_sdm lebih besar dari lastProcessedId
                    if (!empty($this->lastProcessedId) && $sdm['id_sdm'] <= $this->lastProcessedId) {
                        continue;
                    }

                    try {
                        $sdmController = app(SdmController::class);

                        // Ambil detail jabatan fungsional
                        $detailJabatan = $sdmController->getDetailJabatanFungsional($sdm['id_sdm']);

                        // Sinkronisasi data SDM
                        Sdm::updateOrCreate(
                            ['id_sdm' => $sdm['id_sdm']],
                            [
                                'nama_sdm' => $sdm['nama_sdm'],
                                'nidn' => $sdm['nidn'],
                                'nip' => $sdm['nip'],
                                'nuptk' => $sdm['nuptk'],
                                'nama_status_aktif' => $sdm['nama_status_aktif'],
                                'nama_status_pegawai' => $sdm['nama_status_pegawai'],
                                'jenis_sdm' => $sdm['jenis_sdm'],
                                'jabatan_fungsional' => $detailJabatan['jabatan_fungsional'] ?? null,
                                'angka_kredit' => $detailJabatan['angka_kredit'] ?? null,
                                'sk' => $detailJabatan['sk'] ?? null,
                                'tanggal_mulai' => $detailJabatan['tanggal_mulai'] ?? null,
                            ]
                        );

                        // Ambil rekomendasi tunjangan
                        $rekomendasi = $sdmController->getRekomendasiTunjangan($sdm['id_sdm']);

                        // Update data rekomendasi
                        Sdm::updateOrCreate(
                            ['id_sdm' => $sdm['id_sdm']],
                            [
                                'rekomendasi' => $rekomendasi['message'] ?? null,
                                'keterangan' => $rekomendasi['detail'] ?? null,
                            ]
                        );

                        Log::info("Sinkronisasi selesai untuk ID: {$sdm['id_sdm']}");

                        // Perbarui lastProcessedId dan simpan ke cache
                        $this->lastProcessedId = $sdm['id_sdm'];
                        Cache::put('last_processed_id', $this->lastProcessedId);
                    } catch (\Throwable $e) {
                        Log::error("Gagal sinkronisasi ID: {$sdm['id_sdm']}, Error: " . $e->getMessage());
                    }
                }

                Log::info("Sinkronisasi selesai");
                $this->dispatch('notification', ['type' => 'success', 'message' => 'Data SDM berhasil disinkronisasi.']);
            } else {
                Log::error("Gagal mengambil data dari API: " . $response->body());
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Gagal mengambil data dari API.']);
            }
        } catch (\Exception $e) {
            Log::error("Gagal sinkronisasi: " . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Terjadi kesalahan saat sinkronisasi.']);
        }

        $this->dispatch('sync-completed');
        Cache::forget('last_processed_id');
    }

    public function render()
    {
        return view('livewire.sync-sdm-data');
    }
}
