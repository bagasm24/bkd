<?php

namespace App\Http\Controllers;

use App\Models\JenisPublikasi;
use App\Models\Sdm;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use PhpParser\Node\Stmt\Return_;
use Termwind\Components\Dd;
use App\Http\Livewire\JurnalNasional;
use App\Models\Penelitian;
use App\Models\Pengabdian;
use App\Models\Publikasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;


class SdmController extends Controller
{

    public function index()
    {
        try {
            // Mengambil semua data dari tabel sdms
            set_time_limit(0); // Menghapus batas waktu eksekusi
            $dataSdm = Sdm::all();
            // Kirim data ke view
            return view('pages.dashboard.sdm.index', ['data' => $dataSdm]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data SDM.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAllDataSDM()
    {
        $baseUrl = env('BASE_URL');
        $getToken = $this->getToken();
        if (!$baseUrl || !$getToken) {
            return response()->json([
                'success' => false,
                'message' => 'API configuration is missing in the environment file.',
            ], 500);
        }

        try {
            set_time_limit(0);
            $response = Http::withToken($getToken)
                ->get($baseUrl . '/referensi/sdm');

            if ($response->successful()) {
                $responseData = $response->json();
                // dd($responseData);
                foreach ($responseData as $sdm) {
                    // Ambil detail jabatan fungsional untuk setiap SDM
                    try {
                        $detailJabatan = $this->getDetailJabatanFungsional($sdm['id_sdm']);
                        // dd($sdm['id_sdm']);
                    } catch (\Exception $e) {
                        $detailJabatan = null; // Jika gagal, berikan nilai null
                    }
                    // Tambahkan detail jabatan ke data SDM
                    Sdm::updateOrCreate(
                        ['id_sdm' => $sdm['id_sdm']], // Kondisi untuk update jika sudah ada
                        [
                            'nama_sdm' => $sdm['nama_sdm'],
                            'nidn' => $sdm['nidn'],
                            'nip' => $sdm['nip'],
                            'nuptk' => $sdm['nuptk'],
                            'nama_status_aktif' => $sdm['nama_status_aktif'],
                            'nama_status_pegawai' => $sdm['nama_status_pegawai'],
                            'jenis_sdm' => $sdm['jenis_sdm'],
                            'jabatan_fungsional' => $detailJabatan['jabatan_fungsional'] ?? null, // Gunakan data dari detail jabatan
                            'angka_kredit' => $detailJabatan['angka_kredit'] ?? null, // Gunakan angka kredit jika tersedia
                            'sk' => $detailJabatan['sk'] ?? null,
                            'tanggal_mulai' => $detailJabatan['tanggal_mulai'] ?? null,
                        ]
                    );
                    try {
                        $rekomendasi = $this->getRekomendasiTunjangan($sdm['id_sdm']);
                        // dd($rekomendasi);
                    } catch (\Exception $e) {
                        $rekomendasi = null;
                    }
                    Sdm::updateOrCreate(
                        ['id_sdm' => $sdm['id_sdm']], // Kondisi untuk update jika sudah ada
                        [
                            'rekomendasi' => $rekomendasi['message'],
                            'keterangan' => $rekomendasi['detail'] ?? null,
                        ]
                    );
                }
                return redirect()->back()->with('success', 'Data SDM berhasil disinkronisasi.');
            } else {
                return redirect()->back()->with('error', 'Failed to fetch data from API: ' . $response->body());
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function getAllDataSDMChuck()
    {
        $baseUrl = env('BASE_URL');
        $bearerToken = env('API_BEARER_TOKEN');

        if (!$baseUrl || !$bearerToken) {
            return redirect()->back()->with('error', 'API configuration is missing.');
        }

        try {
            $response = Http::withToken($bearerToken)
                ->get("$baseUrl/referensi/sdm");

            if (!$response->successful()) {
                return redirect()->back()->with('error', 'Failed to fetch data from API: ' . $response->body());
            }

            $responseData = $response->json();
            if (!is_array($responseData)) {
                return redirect()->back()->with('error', 'Invalid API response format.');
            }

            // Proses data dalam batch (Chunk)
            collect($responseData)->chunk(200)->each(function ($chunk) {
                $dataToInsert = [];

                foreach ($chunk as $sdm) {
                    try {
                        // Ambil detail jabatan
                        $detailJabatan = $this->getDetailJabatanFungsional($sdm['id_sdm']) ?? [];

                        // Ambil rekomendasi tunjangan
                        $rekomendasi = $this->getRekomendasiTunjangan($sdm['id_sdm']) ?? [];

                        $dataToInsert[] = [
                            'id_sdm' => $sdm['id_sdm'],
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
                            'rekomendasi' => $rekomendasi['message'] ?? null,
                            'keterangan' => $rekomendasi['detail'] ?? null,
                        ];
                    } catch (\Exception $e) {
                        Log::error("Error processing SDM {$sdm['id_sdm']}: " . $e->getMessage());
                    }
                }

                // Mass Insert (Upsert)
                DB::table('sdms')->upsert($dataToInsert, ['id_sdm'], [
                    'nama_sdm', 'nidn', 'nip', 'nuptk', 'nama_status_aktif',
                    'nama_status_pegawai', 'jenis_sdm', 'jabatan_fungsional',
                    'angka_kredit', 'sk', 'tanggal_mulai', 'rekomendasi', 'keterangan'
                ]);
            });

            return redirect()->back()->with('success', 'Data SDM berhasil disinkronisasi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function getSDMDataLogin()
    {
        $user = Auth::user();
        $nidn = $user->nidn;

        if (session()->has('sdm_data')) {
            return session()->get('sdm_data');
        }

        $getToken = $this->getToken();
        $baseUrl = env('BASE_URL');
        if (!$baseUrl || !$getToken) {
            return response()->json([
                'success' => false,
                'message' => 'API configuration is missing in the environment file.',
            ], 500);
        }

        try {
            $response = Http::withToken($getToken)
                ->get($baseUrl . '/referensi/sdm', [
                    'nidn' => $nidn,
                ]);

            session()->put('sdm_data', $response->json());

            if ($response->successful()) {
                $responseData = $response->json();
                return $responseData;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch data from API',
                    'error' => $response->body(),
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while making the API request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getJabatanFungsional($id_sdm)
    {
        // dd($id);
        // Ambil URL dan Token dari .env
        $getToken = $this->getToken();
        // dd($getToken);
        $baseUrl = env('BASE_URL');
        // dd($baseUrl);
        // $bearerToken = env('API_BEARER_TOKEN');
        if (!$baseUrl || !$getToken) {
            return response()->json([
                'success' => false,
                'message' => 'API configuration is missing in the environment file.',
            ], 500);
        }

        try {
            $response = Http::withToken($getToken)
                ->get($baseUrl . '/jabatan_fungsional', [
                    'id_sdm' => $id_sdm,
                ]);

            if ($response->successful()) {
                $responseData = $response->json();

                if (empty($responseData)) {
                    return null;
                }

                usort($responseData, function ($a, $b) {
                    return $b['tanggal_mulai'] <=> $a['tanggal_mulai'];
                });
                return $responseData[0]['id'];
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch data from API',
                    'error' => $response->body(),
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while making the API request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getDetailJabatanFungsional($id_sdm)
    {
        $getToken = $this->getToken();
        // dd($getToken);
        $dataJabatan = $this->getJabatanFungsional($id_sdm);
        // Ambil URL dan Token dari .env
        $baseUrl = env('BASE_URL');
        // dd($baseUrl);
        // $bearerToken = env('API_BEARER_TOKEN');
        // dd($bearerToken);
        if (!$baseUrl || !$getToken) {
            return response()->json([
                'success' => false,
                'message' => 'API configuration is missing in the environment file.',
            ], 500);
        }

        try {
            $response = Http::withToken($getToken)
                ->get($baseUrl . '/jabatan_fungsional/' . $dataJabatan);

            if ($response->successful()) {
                $responseData = $response->json();
                return $responseData;
            } else {
                return null;
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getFotoSDM()
    {
        $data = $this->getSDMDataLogin();
        $id_sdm = $data[0]['id_sdm'];
        // dd($id_sdm);


        $baseUrl = env('BASE_URL');
        // dd($baseUrl);
        $bearerToken = env('API_BEARER_TOKEN');
        // dd($bearerToken);
        if (!$baseUrl || !$bearerToken) {
            return response()->json([
                'success' => false,
                'message' => 'API configuration is missing in the environment file.',
            ], 500);
        }

        try {
            $response = Http::withToken($bearerToken)
                ->get($baseUrl . '/data_pribadi/foto/' . $id_sdm);

            if ($response->successful()) {
                $responseData = $response->json();
                return $responseData;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch data from API',
                    'error' => $response->body(),
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while making the API request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getDataAjuanJaFung($id_sdm, $tanggalSK)
    {
        $baseUrl = env('BASE_URL');
        $bearerToken = env('API_BEARER_TOKEN');
        $getToken = $this->getToken();
        if (!$baseUrl || !$getToken) {
            return response()->json([
                'success' => false,
                'message' => 'API configuration is missing in the environment file.',
            ], 500);
        }

        try {
            $response = Http::withToken($getToken)
                ->get($baseUrl . '/jabatan_fungsional/ajuan', [
                    'id_sdm' => $id_sdm,
                ]);

            if ($response->successful()) {
                $responseData = $response->json();

                if (empty($responseData)) {
                    return null;
                }
                usort($responseData, function ($a, $b) {
                    return $b['tanggal_ajuan'] <=> $a['tanggal_ajuan'];
                });
                return $responseData[0]['tanggal_ajuan'];
            } else {
                return null;
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getRekomendasiTunjangan($id_sdm)
    {
        $sdm = Sdm::where('id_sdm', $id_sdm)->firstOrFail();
        $publikasiController = new PublikasiController();
        $jafung = $sdm->jabatan_fungsional;
        // dd($jafung);
        // $jafung = $dataJafung['jabatan_fungsional'];
        // Logika rekomendasi berdasarkan jabatan
        $dataPublikasiManual = Publikasi::where('id_sdm', $id_sdm)
            ->where('asal_data', 'MANUAL')
            ->get();
        $dataPublikasi = $publikasiController->getDetailPublikasi($id_sdm);
        $dataAjuanJaFung = $this->getDataAjuanJaFung($id_sdm, $sdm->tanggal_mulai);

        $judulManual = $dataPublikasiManual->pluck('judul')->map(fn ($judul) => trim(strtolower($judul)))->toArray();
        $judulApi = collect($dataPublikasi)->pluck('judul')->map(fn ($judul) => trim(strtolower($judul)))->toArray();

        // Cari judul yang sama
        $judulSama = array_intersect($judulManual, $judulApi);

        // Ambil judul yang tidak ada di daftar judul yang sama
        $judulUnikManual = array_diff($judulManual, $judulSama);

        // Filter data dari objek asli berdasarkan judul yang unik
        $dataManualUnik = $dataPublikasiManual->filter(fn ($item) => in_array(trim(strtolower($item->judul)), $judulUnikManual));

        // **Convert Collection to Array When Needed**
        $dataManualUnik = $dataManualUnik->toArray();

        switch ($jafung) {
            case null:
                $filteredPublikasi = collect($dataPublikasi)->filter(function ($publikasi) use ($sdm) {
                    $tanggalPublikasi = Carbon::parse($publikasi['tanggal']);
                    $duaTahunTerakhir = Carbon::now()->subYears(2);
                    $isPenulisUtama = collect($publikasi['penulis'])->contains(function ($penulis) {
                        return $penulis['urutan'] === 1 || $penulis['corresponding_author'] === 1;
                    });
                    return $tanggalPublikasi->betweenIncluded($duaTahunTerakhir, Carbon::now()) && $isPenulisUtama;
                });

                //Filter Publikasi Manual
                $filteredPublikasiManual = collect($dataManualUnik)->filter(function ($publikasi) use ($sdm) {
                    $tanggalPublikasi = Carbon::parse($publikasi['tanggal']);
                    $duaTahunTerakhir = Carbon::now()->subYears(2);
                    return $tanggalPublikasi->betweenIncluded($duaTahunTerakhir, Carbon::now());
                });

                // dd($filteredPublikasi);

                // Jurnal Nasional API
                $jurnalNasional = $filteredPublikasi->filter(function ($publikasi) {
                    $jenisJurnalNasional = JenisPublikasi::where('nama', 'Jurnal nasional')->first();
                    return $jenisJurnalNasional && $publikasi['id_jenis_publikasi'] === $jenisJurnalNasional->id;
                });

                // Jurnal Nasional Manual
                $jurnalNasionalManual = $filteredPublikasiManual->filter(function ($publikasi) {
                    $jenisJurnalNasional = JenisPublikasi::where('nama', 'Jurnal nasional')->first();
                    return $jenisJurnalNasional && $publikasi['jenis_publikasi'] === $jenisJurnalNasional->id && $publikasi['urutan_penulis'] === 1;
                });

                // Gabungan Jurnal Nasional
                $mergedJurnalNasional = $jurnalNasional->merge($jurnalNasionalManual);

                // Jurnal Nasional Terakreditasi API
                $jurnalNasionalTerakreditasi = collect($filteredPublikasi)->filter(function ($publikasi) {
                    return $publikasi['id_jenis_publikasi'] === JenisPublikasi::where('nama', 'Jurnal nasional terakreditasi')->first()->id;
                });

                // Jurnal Nasional Terakreditasi Manual
                $jurnalNasionalTerakreditasiManual = collect($filteredPublikasiManual)->filter(function ($publikasi) {
                    return $publikasi['jenis_publikasi'] === JenisPublikasi::where('nama', 'Jurnal nasional terakreditasi')->first()->id
                        && $publikasi['urutan_penulis'] === 1;
                });

                // Gabungan Jurnal Nasional Terakreditasi
                $mergedJurnalNasionalTerakreditasi = $jurnalNasionalTerakreditasi->merge($jurnalNasionalTerakreditasiManual);

                // Jurnal Internasional API
                $jurnalInternasional = collect($filteredPublikasi)->filter(function ($publikasi) {
                    return $publikasi['id_jenis_publikasi'] === JenisPublikasi::where('nama', 'Jurnal internasional')->first()->id
                        && collect($publikasi['penulis'])->contains(function ($penulis) {
                            return $penulis['urutan'] === 1 || $penulis['corresponding_author'] === 1;
                        });
                });

                // Jurnal Internasional Manual
                $jurnalInternasionalManual = collect($filteredPublikasiManual)->filter(function ($publikasi) {
                    return $publikasi['jenis_publikasi'] === JenisPublikasi::where('nama', 'Jurnal internasional')->first()->id
                        && $publikasi['urutan_penulis'] === 1;
                });

                //Gabungan Jurnal Internasional
                $mergedJurnalInternasional = $jurnalInternasional->merge($jurnalInternasionalManual);

                // Hitung jumlah publikasi yang memenuhi syarat
                $jumlahPublikasiNasional = $mergedJurnalNasional->count();
                $jumlahPublikasiNasionalTerakreditasi = $mergedJurnalNasionalTerakreditasi->count();
                $jumlahPublikasiInternasional = $mergedJurnalInternasional->count();

                // dd($jurnalNasionalTerakreditasi);

                // Cek apakah jumlah publikasi cukup (minimal 2)
                if ($jumlahPublikasiNasional < 2 && $jumlahPublikasiNasionalTerakreditasi < 2 && $jumlahPublikasiInternasional < 2) {
                    return [
                        'success' => false,
                        'message' => 'Tidak Lolos Rekomendasi',
                        'detail' => 'Kurang dari 2 publikasi dalam Jurnal Nasional selama 2 tahun setelah SK terbit'
                    ];
                }

                // Ambil data ajuan kenaikan jabatan akademik
                // $dataAjuanJaFung = $this->getDataAjuanJaFung($id_sdm, $sdm->tanggal_mulai);
                // // dd($dataAjuanJaFung);
                // if (empty($dataAjuanJaFung)) {
                //     return [
                //         'success' => false,
                //         'message' => 'Tidak Lolos Rekomendasi',
                //         'detail' => 'Tidak ada data ajuan kenaikan jabatan akademik'
                //     ];
                // }

                // // dd($dataAjuanJaFung);
                // // Validasi waktu pengajuan kenaikan jabatan (harus antara 2-3 tahun setelah SK)
                // $tanggalKenaikanJabatan = Carbon::parse($dataAjuanJaFung);
                // $tigaTahunSetelahSK = Carbon::parse($sdm->tanggal_mulai)->addYears(3);
                // if ($tanggalKenaikanJabatan->between($tanggalKenaikanJabatan, $tigaTahunSetelahSK)) {
                //     return [
                //         'success' => true,
                //         'message' => 'Lolos Rekomendasi',
                //     ];
                // } else {
                //     return [
                //         'success' => false,
                //         'message' => 'Tidak Lolos Rekomendasi',
                //         'detail' => 'Pengajuan kenaikan jabatan tidak dalam rentang 2-3 tahun setelah SK',
                //     ];
                // }

                return [
                    'success' => true,
                    'message' => 'Lolos Rekomendasi',
                ];
                break;
            case 'Tenaga Pengajar':
                $tigaTahunSetelahSK = Carbon::parse($sdm->tanggal_mulai)->addYears(3);

                // Filter publikasi API
                $filteredPublikasi = collect($dataPublikasi)->filter(function ($publikasi) use ($sdm) {
                    $tahunPublikasi = Carbon::parse($publikasi['tanggal'])->format('Y');
                    $tahunMulai = Carbon::parse($sdm->tanggal_mulai)->format('Y');
                    $duaTahunSetelahSK = Carbon::parse($sdm->tanggal_mulai)->addYears(2)->format('Y');
                    $isPenulisUtama = collect($publikasi['penulis'])->contains(function ($penulis) {
                        return $penulis['urutan'] === 1 || $penulis['corresponding_author'] === 1;
                    });
                    return $tahunPublikasi >= $tahunMulai && $tahunPublikasi <= $duaTahunSetelahSK && $isPenulisUtama;
                });

                //Filter Publikasi Manual
                $filteredPublikasiManual = collect($dataManualUnik)->filter(function ($publikasi) use ($sdm) {
                    $tahunPublikasi = Carbon::parse($publikasi['tanggal'])->format('Y');
                    $tahunMulai = Carbon::parse($sdm->tanggal_mulai)->format('Y');
                    $duaTahunSetelahSK = Carbon::parse($sdm->tanggal_mulai)->addYears(2)->format('Y');
                    return $tahunPublikasi >= $tahunMulai && $tahunPublikasi <= $duaTahunSetelahSK;
                });

                // Jurnal Nasional API
                $jurnalNasional = $filteredPublikasi->filter(function ($publikasi) {
                    $jenisJurnalNasional = JenisPublikasi::where('nama', 'Jurnal nasional')->first();
                    return $jenisJurnalNasional && $publikasi['id_jenis_publikasi'] === $jenisJurnalNasional->id;
                });

                // Jurnal Nasional Manual
                $jurnalNasionalManual = $filteredPublikasiManual->filter(function ($publikasi) {
                    $jenisJurnalNasional = JenisPublikasi::where('nama', 'Jurnal nasional')->first();
                    return $jenisJurnalNasional && $publikasi['jenis_publikasi'] === $jenisJurnalNasional->id && $publikasi['urutan_penulis'] === 1;
                });

                // Gabungan Jurnal Nasional
                $mergedJurnalNasional = $jurnalNasional->merge($jurnalNasionalManual);

                // Jurnal Nasional Terakreditasi API
                $jurnalNasionalTerakreditasi = collect($filteredPublikasi)->filter(function ($publikasi) {
                    return $publikasi['id_jenis_publikasi'] === JenisPublikasi::where('nama', 'Jurnal nasional terakreditasi')->first()->id;
                });

                // Jurnal Nasional Terakreditasi Manual
                $jurnalNasionalTerakreditasiManual = collect($filteredPublikasiManual)->filter(function ($publikasi) {
                    return $publikasi['jenis_publikasi'] === JenisPublikasi::where('nama', 'Jurnal nasional terakreditasi')->first()->id
                        && $publikasi['urutan_penulis'] === 1;
                });

                // Gabungan Jurnal Nasional Terakreditasi
                $mergedJurnalNasionalTerakreditasi = $jurnalNasionalTerakreditasi->merge($jurnalNasionalTerakreditasiManual);

                // Jurnal Internasional API
                $jurnalInternasional = collect($filteredPublikasi)->filter(function ($publikasi) {
                    return $publikasi['id_jenis_publikasi'] === JenisPublikasi::where('nama', 'Jurnal internasional')->first()->id
                        && collect($publikasi['penulis'])->contains(function ($penulis) {
                            return $penulis['urutan'] === 1 || $penulis['corresponding_author'] === 1;
                        });
                });

                // Jurnal Internasional Manual
                $jurnalInternasionalManual = collect($filteredPublikasiManual)->filter(function ($publikasi) {
                    return $publikasi['jenis_publikasi'] === JenisPublikasi::where('nama', 'Jurnal internasional')->first()->id
                        && $publikasi['urutan_penulis'] === 1;
                });

                //Gabungan Jurnal Internasional
                $mergedJurnalInternasional = $jurnalInternasional->merge($jurnalInternasionalManual);

                // Hitung jumlah publikasi yang memenuhi syarat
                $jumlahPublikasiNasional = $mergedJurnalNasional->count();
                $jumlahPublikasiNasionalTerakreditasi = $mergedJurnalNasionalTerakreditasi->count();
                $jumlahPublikasiInternasional = $mergedJurnalInternasional->count();

                // dd($jurnalNasionalTerakreditasi);
                // Cek apakah tanggal 3 tahun setelah SK kurang dari tanggal sekarang


                $sekarang = Carbon::now();
                $tahunSK = Carbon::parse($sdm->tanggal_mulai)->year;
                $tahunMaksimal = $tahunSK + 3;

                if ($sekarang->year > $tahunMaksimal) {
                    return [
                        'success' => false,
                        'message' => 'Tidak Lolos Rekomendasi',
                        'detail' => 'Pengajuan kenaikan jabatan tidak dalam rentang maksimal 3 tahun setelah SK'
                    ];
                }

                // Cek publikasi
                if ($jumlahPublikasiNasional < 2 && $jumlahPublikasiNasionalTerakreditasi < 2 && $jumlahPublikasiInternasional < 2) {
                    return [
                        'success' => false,
                        'message' => 'Tidak Lolos Rekomendasi',
                        'detail' => 'Kurang dari 2 publikasi dalam Jurnal Nasional selama 2 tahun setelah SK terbit'
                    ];
                }

                return [
                    'success' => true,
                    'message' => 'Lolos Rekomendasi',
                ];


                // $sekarang = Carbon::now();
                // if ($tigaTahunSetelahSK->lt($sekarang)) {
                //     // Cek apakah ada data ajuan jafung yang waktu pengajuannya 2-3 tahun setelah tanggal SK
                //     $dataAjuanJaFung = $this->getDataAjuanJaFung($id_sdm, $sdm->tanggal_mulai);
                //     if (empty($dataAjuanJaFung)) {
                //         return [
                //             'success' => false,
                //             'message' => 'Tidak Lolos Rekomendasi',
                //             'detail' => 'Tidak ada data ajuan kenaikan jabatan akademik'
                //         ];
                //     }

                //     $tanggalKenaikanJabatan = Carbon::parse($dataAjuanJaFung);
                //     $tahunSK = Carbon::parse($sdm->tanggal_mulai)->year;
                //     $tahunMinimal = $tahunSK + 2;
                //     $tahunMaksimal = $tahunSK + 3;
                //     if ($tanggalKenaikanJabatan->year < $tahunMinimal || $tanggalKenaikanJabatan->year > $tahunMaksimal) {
                //         return [
                //             'success' => false,
                //             'message' => 'Tidak Lolos Rekomendasi',
                //             'detail' => 'Pengajuan kenaikan jabatan tidak dalam rentang 2-3 tahun setelah SK'
                //         ];
                //     }

                //     // Cek publikasi
                //     if ($jumlahPublikasiNasional < 2 && $jumlahPublikasiNasionalTerakreditasi < 2 && $jumlahPublikasiInternasional < 2) {
                //         return [
                //             'success' => false,
                //             'message' => 'Tidak Lolos Rekomendasi',
                //             'detail' => 'Kurang dari 2 publikasi dalam Jurnal Nasional selama 2 tahun setelah SK terbit'
                //         ];
                //     }

                //     return [
                //         'success' => true,
                //         'message' => 'Lolos Rekomendasi',
                //     ];
                // } else {
                //     // Cek publikasi tanpa cek data ajuan jafung
                //     if ($jumlahPublikasiNasional < 2 && $jumlahPublikasiNasionalTerakreditasi < 2 && $jumlahPublikasiInternasional < 2) {
                //         return [
                //             'success' => false,
                //             'message' => 'Tidak Lolos Rekomendasi',
                //             'detail' => 'Kurang dari 2 publikasi dalam Jurnal Nasional selama 2 tahun setelah SK terbit'
                //         ];
                //     }

                //     return [
                //         'success' => true,
                //         'message' => 'Lolos Rekomendasi',
                //     ];
                // }

                break;
            case 'Asisten Ahli':
                $tigaTahunSetelahSK = Carbon::parse($sdm->tanggal_mulai)->addYears(3);

                // Filter publikasi API
                $filteredPublikasi = collect($dataPublikasi)->filter(function ($publikasi) use ($sdm) {
                    $tahunPublikasi = Carbon::parse($publikasi['tanggal'])->format('Y');
                    $tahunMulai = Carbon::parse($sdm->tanggal_mulai)->format('Y');
                    $duaTahunSetelahSK = Carbon::parse($sdm->tanggal_mulai)->addYears(2)->format('Y');
                    $isPenulisUtama = collect($publikasi['penulis'])->contains(function ($penulis) {
                        return $penulis['urutan'] === 1 || $penulis['corresponding_author'] === 1;
                    });
                    return $tahunPublikasi >= $tahunMulai && $tahunPublikasi <= $duaTahunSetelahSK && $isPenulisUtama;
                });

                //Filter Publikasi Manual
                $filteredPublikasiManual = collect($dataManualUnik)->filter(function ($publikasi) use ($sdm) {
                    $tahunPublikasi = Carbon::parse($publikasi['tanggal'])->format('Y');
                    $tahunMulai = Carbon::parse($sdm->tanggal_mulai)->format('Y');
                    $duaTahunSetelahSK = Carbon::parse($sdm->tanggal_mulai)->addYears(2)->format('Y');
                    return $tahunPublikasi >= $tahunMulai && $tahunPublikasi <= $duaTahunSetelahSK;
                });

                // Jurnal Nasional API
                $jurnalNasional = $filteredPublikasi->filter(function ($publikasi) {
                    $jenisJurnalNasional = JenisPublikasi::where('nama', 'Jurnal nasional')->first();
                    return $jenisJurnalNasional && $publikasi['id_jenis_publikasi'] === $jenisJurnalNasional->id;
                });

                // Jurnal Nasional Manual
                $jurnalNasionalManual = $filteredPublikasiManual->filter(function ($publikasi) {
                    $jenisJurnalNasional = JenisPublikasi::where('nama', 'Jurnal nasional')->first();
                    return $jenisJurnalNasional && $publikasi['jenis_publikasi'] === $jenisJurnalNasional->id && $publikasi['urutan_penulis'] === 1;
                });

                // Gabungan Jurnal Nasional
                $mergedJurnalNasional = $jurnalNasional->merge($jurnalNasionalManual);

                // Jurnal Nasional Terakreditasi API
                $jurnalNasionalTerakreditasi = collect($filteredPublikasi)->filter(function ($publikasi) {
                    return $publikasi['id_jenis_publikasi'] === JenisPublikasi::where('nama', 'Jurnal nasional terakreditasi')->first()->id;
                });

                // Jurnal Nasional Terakreditasi Manual
                $jurnalNasionalTerakreditasiManual = collect($filteredPublikasiManual)->filter(function ($publikasi) {
                    return $publikasi['jenis_publikasi'] === JenisPublikasi::where('nama', 'Jurnal nasional terakreditasi')->first()->id
                        && $publikasi['urutan_penulis'] === 1;
                });

                // Gabungan Jurnal Nasional Terakreditasi
                $mergedJurnalNasionalTerakreditasi = $jurnalNasionalTerakreditasi->merge($jurnalNasionalTerakreditasiManual);

                // Jurnal Internasional API
                $jurnalInternasional = collect($filteredPublikasi)->filter(function ($publikasi) {
                    return $publikasi['id_jenis_publikasi'] === JenisPublikasi::where('nama', 'Jurnal internasional')->first()->id
                        && collect($publikasi['penulis'])->contains(function ($penulis) {
                            return $penulis['urutan'] === 1 || $penulis['corresponding_author'] === 1;
                        });
                });

                // Jurnal Internasional Manual
                $jurnalInternasionalManual = collect($filteredPublikasiManual)->filter(function ($publikasi) {
                    return $publikasi['jenis_publikasi'] === JenisPublikasi::where('nama', 'Jurnal internasional')->first()->id
                        && $publikasi['urutan_penulis'] === 1;
                });

                //Gabungan Jurnal Internasional
                $mergedJurnalInternasional = $jurnalInternasional->merge($jurnalInternasionalManual);

                // Hitung jumlah publikasi yang memenuhi syarat
                $jumlahPublikasiNasional = $mergedJurnalNasional->count();
                $jumlahPublikasiNasionalTerakreditasi = $mergedJurnalNasionalTerakreditasi->count();
                $jumlahPublikasiInternasional = $mergedJurnalInternasional->count();

                // Cek apakah tanggal 3 tahun setelah SK kurang dari tanggal sekarang
                $sekarang = Carbon::now();
                $tahunSK = Carbon::parse($sdm->tanggal_mulai)->year;
                $tahunMaksimal = $tahunSK + 3;

                if ($sekarang->year > $tahunMaksimal) {
                    return [
                        'success' => false,
                        'message' => 'Tidak Lolos Rekomendasi',
                        'detail' => 'Pengajuan kenaikan jabatan tidak dalam rentang maksimal 3 tahun setelah SK'
                    ];
                }

                // Cek publikasi
                if ($jumlahPublikasiNasional < 2 && $jumlahPublikasiNasionalTerakreditasi < 2 && $jumlahPublikasiInternasional < 2) {
                    return [
                        'success' => false,
                        'message' => 'Tidak Lolos Rekomendasi',
                        'detail' => 'Kurang dari 2 publikasi dalam Jurnal Nasional selama 2 tahun setelah SK terbit'
                    ];
                }

                return [
                    'success' => true,
                    'message' => 'Lolos Rekomendasi',
                ];
                break;


            case 'Lektor':
                $tigaTahunSetelahSK = Carbon::parse($sdm->tanggal_mulai)->addYears(3);
                // Filter Data API
                $filteredPublikasi = array_filter($dataPublikasi, function ($publikasi) use ($sdm) {
                    $tahunPublikasi = Carbon::parse($publikasi['tanggal'])->format('Y');
                    $tahunMulai = Carbon::parse($sdm->tanggal_mulai)->format('Y');
                    $tigaTahunSetelahSK = Carbon::parse($sdm->tanggal_mulai)->addYears(3)->format('Y');
                    // $tahunAkhir = $tahunMulaiSk + 3; // 3 tahun setelah SK terbit

                    return $tahunPublikasi >= $tahunMulai && $tahunPublikasi <= $tigaTahunSetelahSK;
                });

                // dd($filteredPublikasi);

                // Filter Data Manual
                $filteredPublikasiManual = array_filter($dataManualUnik, function ($publikasi) use ($sdm) {
                    $tahunMulaiSk = Carbon::parse($sdm->tanggal_mulai)->format('Y');
                    $tahunPublikasi = Carbon::parse($publikasi['tanggal'])->format('Y');
                    $tahunAkhir = $tahunMulaiSk + 3; // 3 tahun setelah SK terbit

                    return $tahunPublikasi >= $tahunMulaiSk && $tahunPublikasi <= $tahunAkhir;
                });

                // Jurnal Nasional API
                $jurnalNasional = collect($filteredPublikasi)->filter(function ($publikasi) {
                    return $publikasi['id_jenis_publikasi'] === JenisPublikasi::where('nama', 'Jurnal nasional')->first()->id
                        && collect($publikasi['penulis'])->contains(function ($penulis) {
                            return $penulis['urutan'] === 1 || $penulis['corresponding_author'] === 1;
                        });
                });

                // Jurnal Nasional Manual
                $jurnalNasionalManual = collect($filteredPublikasiManual)->filter(function ($publikasi) {
                    return $publikasi['jenis_publikasi'] === JenisPublikasi::where('nama', 'Jurnal nasional')->first()->id
                        && $publikasi['urutan_penulis'] === 1;
                });

                // Gabungan Jurnal Nasional
                $mergedJurnalNasional = $jurnalNasional->merge($jurnalNasionalManual);

                // Jurnal Nasional Terakreditasi API
                $jurnalNasionalTerakreditasi = collect($filteredPublikasi)->filter(function ($publikasi) {
                    return $publikasi['id_jenis_publikasi'] === JenisPublikasi::where('nama', 'Jurnal nasional terakreditasi')->first()->id
                        && collect($publikasi['penulis'])->contains(function ($penulis) {
                            return $penulis['urutan'] === 1 || $penulis['corresponding_author'] === 1;
                        });
                });

                // Jurnal Nasional Terakreditasi Manual
                $jurnalNasionalTerakreditasiManual = collect($filteredPublikasiManual)->filter(function ($publikasi) {
                    return $publikasi['jenis_publikasi'] === JenisPublikasi::where('nama', 'Jurnal nasional terakreditasi')->first()->id
                        && $publikasi['urutan_penulis'] === 1;
                });

                // Gabungan Jurnal Nasional TerAkreditasi
                $mergedJurnalNasionalTerakreditasi = $jurnalNasionalTerakreditasi->merge($jurnalNasionalTerakreditasiManual);

                // Jurnal Internasional API
                $jurnalInternasional = collect($filteredPublikasi)->filter(function ($publikasi) {
                    return $publikasi['id_jenis_publikasi'] === JenisPublikasi::where('nama', 'Jurnal internasional')->first()->id
                        && collect($publikasi['penulis'])->contains(function ($penulis) {
                            return $penulis['urutan'] === 1 || $penulis['corresponding_author'] === 1;
                        });
                });

                // Jurnal Internasional Manual
                $jurnalInternasionalManual = collect($filteredPublikasiManual)->filter(function ($publikasi) {
                    return $publikasi['jenis_publikasi'] === JenisPublikasi::where('nama', 'Jurnal internasional')->first()->id && $publikasi['urutan_penulis'] === 1;
                });

                // Gabungan Jurnal Internasional
                $mergedJurnalInternasional = $jurnalInternasional->merge($jurnalInternasionalManual);

                $sekarang = Carbon::now();
                $tahunSK = Carbon::parse($sdm->tanggal_mulai)->year;
                $tahunMaksimal = $tahunSK + 4;

                if ($sekarang->year > $tahunMaksimal) {
                    return [
                        'success' => false,
                        'message' => 'Tidak Lolos Rekomendasi',
                        'detail' => 'Pengajuan kenaikan jabatan tidak dalam rentang maksimal 4 tahun setelah SK'
                    ];
                }

                if (
                    ($mergedJurnalNasional->count() >= 3) || ($mergedJurnalNasionalTerakreditasi->count() >= 3) ||
                    ($mergedJurnalNasional->count() >= 1 && $mergedJurnalNasionalTerakreditasi->count() >= 1) ||
                    ($mergedJurnalInternasional->count() >= 1)
                ) {
                    return [
                        'success' => true,
                        'message' => 'Lolos Rekomendasi',
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Tidak Lolos Rekomendasi',
                    'detail' => 'Tidak memenuhi kewajiban Publikasi selama 3 Tahun setelah SK terbit',
                ];

                break;

            case 'Lektor Kepala':
                $tigaTahunSetelahSK = Carbon::parse($sdm->tanggal_mulai)->addYears(3);
                $empatTahunSetelahSK = Carbon::parse($sdm->tanggal_mulai)->addYears(4);

                //Filter Data API
                $filteredPublikasi = array_filter($dataPublikasi, function ($publikasi) use ($sdm) {
                    $tahunPublikasi = Carbon::parse($publikasi['tanggal'])->format('Y');
                    $tahunTigaTahunTerakhir = Carbon::now()->subYears(3)->format('Y');
                    return $tahunPublikasi >= $tahunTigaTahunTerakhir;
                });

                //Filter Data Manual
                $filteredPublikasiManual = array_filter($dataManualUnik, function ($publikasi) use ($sdm) {
                    $tahunPublikasi = Carbon::parse($publikasi['tanggal'])->format('Y');
                    $tahunTigaTahunTerakhir = Carbon::now()->subYears(3)->format('Y');
                    return $tahunPublikasi >= $tahunTigaTahunTerakhir;
                });

                // Jurnal Nasional API
                $jurnalNasional = collect($filteredPublikasi)->filter(function ($publikasi) {
                    return $publikasi['id_jenis_publikasi'] === JenisPublikasi::where('nama', 'Jurnal nasional')->first()->id
                        && collect($publikasi['penulis'])->contains(function ($penulis) {
                            return $penulis['urutan'] === 1 || $penulis['corresponding_author'] === 1;
                        });
                });

                // Jurnal Nasional Manual
                $jurnalNasionalManual = collect($filteredPublikasiManual)->filter(function ($publikasi) {
                    return $publikasi['jenis_publikasi'] === JenisPublikasi::where('nama', 'Jurnal nasional')->first()->id
                        && $publikasi['urutan_penulis'] === 1;
                });

                //Gabungan Jurnal Nasional
                $mergedJurnalNasional = $jurnalNasional->merge($jurnalNasionalManual);

                // Jurnal Nasional Terakreditasi API
                $jurnalNasionalTerakreditasi = collect($filteredPublikasi)->filter(function ($publikasi) {
                    return $publikasi['id_jenis_publikasi'] === JenisPublikasi::where('nama', 'Jurnal nasional terakreditasi')->first()->id
                        && collect($publikasi['penulis'])->contains(function ($penulis) {
                            return $penulis['urutan'] === 1 || $penulis['corresponding_author'] === 1;
                        });
                });

                // Jurnal Nasional Terakreditasi Manual
                $jurnalNasionalTerakreditasiManual = collect($filteredPublikasiManual)->filter(function ($publikasi) {
                    return $publikasi['jenis_publikasi'] === JenisPublikasi::where('nama', 'Jurnal nasional terakreditasi')->first()->id
                        && $publikasi['urutan_penulis'] === 1;
                });

                //Gabungan Jurnal Nasional Terakreditasi
                $mergedJurnalNasionalTerakreditasi = $jurnalNasionalTerakreditasi->merge($jurnalNasionalTerakreditasiManual);

                // Jurnal Internasional API
                $jurnalInternasional = collect($filteredPublikasi)->filter(function ($publikasi) {
                    return $publikasi['id_jenis_publikasi'] === JenisPublikasi::where('nama', 'jurnal internasional')->first()->id
                        && collect($publikasi['penulis'])->contains(function ($penulis) {
                            return $penulis['urutan'] === 1 || $penulis['corresponding_author'] === 1;
                        });
                });

                // Jurnal Internasional Manual
                $jurnalInternasionalManual = collect($filteredPublikasiManual)->filter(function ($publikasi) {
                    return $publikasi['jenis_publikasi'] === JenisPublikasi::where('nama', 'jurnal internasional')->first()->id && $publikasi['urutan_penulis'] === 1;
                });

                //Gabungan Jurnal Internasional
                $mergedJurnalInternasional = $jurnalInternasional->merge($jurnalInternasionalManual);


                $jenisBuku = JenisPublikasi::whereIn('nama', ['Buku referensi', 'Buku lainnya', 'Book chapter nasional', 'Book chapter internasional', 'Monograf'])->pluck('id');

                // Semua Buku API
                $buku = collect($filteredPublikasi)->filter(function ($publikasi) use ($jenisBuku) {
                    return $jenisBuku->contains($publikasi['id_jenis_publikasi'])
                        && collect($publikasi['penulis'])->contains(function ($penulis) {
                            return $penulis['urutan'] === 1;
                        });
                });

                // Semua Buku Manual
                $bukuManual =
                    collect($filteredPublikasiManual)->filter(function ($publikasi) use ($jenisBuku) {
                        return $jenisBuku->contains($publikasi['jenis_publikasi'])
                            &&
                            $publikasi['urutan_penulis'] === 1;
                    });

                // Gabungan Semua Buku
                $mergedBuku = $buku->merge($bukuManual);

                // Karya API
                $karya = collect($filteredPublikasi)->filter(function ($publikasi) {
                    return $publikasi['id_jenis_publikasi'] === JenisPublikasi::where('nama', 'Rancangan dan karya seni monumental')->first()->id;
                });

                //Karya Monual
                $karyaManual = collect($filteredPublikasiManual)->filter(function ($publikasi) {
                    return $publikasi['jenis_publikasi'] === JenisPublikasi::where('nama', 'Rancangan dan karya seni monumental')->first()->id;
                });

                //Gabungan Karya
                $mergedKarya = $karya->merge($karyaManual);

                // dd($jurnalNasional->count());
                // dd($jurnalNasional->count() >= 1 && $jurnalNasionalTerakreditasi >= 2);

                if (
                    (($mergedJurnalNasional->count() >= 1 && $mergedJurnalNasionalTerakreditasi->count() >= 2) ||
                        ($mergedJurnalInternasional->count() >= 1)) &&
                    ($mergedBuku->count() >= 1) ||
                    ($mergedKarya->count() >= 1)
                ) {
                    return [
                        'success' => true,
                        'message' => 'Lolos Rekomendasi',
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Tidak Lolos Rekomendasi',
                    'detail' => 'Tidak memenuhi kewajiban Publikasi selama 3 Tahun terakhir',
                ];

                break;

            case 'Profesor':
                $tigaTahunSetelahSK = Carbon::parse($sdm->tanggal_mulai)->addYears(3);

                // Filter Data Publikasi API
                $filteredPublikasi = array_filter($dataPublikasi, function ($publikasi) use ($sdm) {
                    $tahunPublikasi = Carbon::parse($publikasi['tanggal'])->format('Y');
                    $tahunTigaTahunTerakhir = Carbon::now()->subYears(3)->format('Y');
                    return $tahunPublikasi >= $tahunTigaTahunTerakhir;
                });

                // Filter Data Publikasi Manual
                $filteredPublikasiManual = array_filter($dataManualUnik, function ($publikasi) use ($sdm) {
                    $tahunPublikasi = Carbon::parse($publikasi['tanggal'])->format('Y');
                    $tahunTigaTahunTerakhir = Carbon::now()->subYears(3)->format('Y');
                    return $tahunPublikasi >= $tahunTigaTahunTerakhir;
                });

                // Ambil ID jenis publikasi untuk jurnal internasional terakreditasi dan bereputasi
                $jurnalInternasionalBereputasiId = JenisPublikasi::where('nama', 'Jurnal internasional bereputasi')->value('id');
                $bukuReferensiId = JenisPublikasi::where('nama', 'Buku referensi')->value('id');

                // Jurnal Internasional API
                $jurnalInternasional = collect($filteredPublikasi)->filter(function ($publikasi) {
                    return $publikasi['id_jenis_publikasi'] === JenisPublikasi::where('nama', 'jurnal internasional')->first()->id
                        && collect($publikasi['penulis'])->contains(function ($penulis) {
                            return $penulis['urutan'] === 1 || $penulis['corresponding_author'] === 1;
                        });
                });

                // Jurnal Internasional Manual
                $jurnalInternasionalManual = collect($filteredPublikasiManual)->filter(function ($publikasi) {
                    return $publikasi['jenis_publikasi'] === JenisPublikasi::where('nama', 'jurnal internasional')->first()->id && $publikasi['urutan_penulis'] === 1;;
                });

                // Gabungan Jurnal InterNasional
                $mergedJurnalInternasional = $jurnalInternasional->merge($jurnalInternasionalManual);

                // Jurnal Bereputasi API
                $jurnalInternasionalBereputasi = collect($filteredPublikasi)->filter(function ($publikasi) use ($jurnalInternasionalBereputasiId) {
                    return $publikasi['id_jenis_publikasi'] === $jurnalInternasionalBereputasiId
                        && collect($publikasi['penulis'])->contains(function ($penulis) {
                            return $penulis['urutan'] === 1 || $penulis['corresponding_author'] === 1;
                        });
                });

                // Jurnal Bereputasi Manual
                $jurnalInternasionalBereputasiManual = collect($filteredPublikasiManual)->filter(function ($publikasi) use ($jurnalInternasionalBereputasiId) {
                    return $publikasi['jenis_publikasi'] === $jurnalInternasionalBereputasiId
                        && $publikasi['urutan_penulis'] === 1;
                });

                // Gabungan Jurnal InterNasional Bereputasi
                $mergedJurnalInternasionalBereputasi = $jurnalInternasionalBereputasi->merge($jurnalInternasionalBereputasiManual);

                $jenisBuku = JenisPublikasi::whereIn('nama', ['Buku referensi', 'Buku lainnya', 'Book chapter nasional', 'Book chapter internasional', 'Monograf'])->pluck('id');

                $buku = collect($filteredPublikasi)->filter(function ($publikasi) use ($jenisBuku) {
                    return $jenisBuku->contains($publikasi['id_jenis_publikasi'])
                        && collect($publikasi['penulis'])->contains(function ($penulis) {
                            return $penulis['urutan'] === 1;
                        });
                });

                // Semua Buku Manual
                $bukuManual =
                    collect($filteredPublikasiManual)->filter(function ($publikasi) use ($jenisBuku) {
                        return $jenisBuku->contains($publikasi['jenis_publikasi'])
                            &&
                            $publikasi['urutan_penulis'] === 1;
                    });

                // Gabungan Buku
                $mergedBuku = $buku->merge($bukuManual);

                $jenisPaten = JenisPublikasi::whereIn('nama', ['Paten nasional', 'Paten internasional'])->pluck('id');

                // Paten API
                $paten = collect($filteredPublikasi)->filter(
                    function ($publikasi) use ($jenisPaten) {
                        return $jenisPaten->contains($publikasi['id_jenis_publikasi']);
                    }
                );

                // Paten Manual
                $patenManual = collect($filteredPublikasiManual)->filter(
                    function ($publikasi) use ($jenisPaten) {
                        return $jenisPaten->contains($publikasi['jenis_publikasi']);
                    }
                );

                // Gabungan Paten
                $mergedPaten = $paten->merge($patenManual);

                $jenisHaki = JenisPublikasi::whereIn('nama', ['Hak cipta nasional', 'Hak cipta internasional'])->pluck('id');

                // Haki API
                $haki = collect($filteredPublikasi)->filter(
                    function ($publikasi) use ($jenisHaki) {
                        return $jenisHaki->contains($publikasi['id_jenis_publikasi']);
                    }
                );

                // Haki Manual
                $hakiManual = collect($filteredPublikasiManual)->filter(
                    function ($publikasi) use ($jenisHaki) {
                        return $jenisHaki->contains($publikasi['jenis_publikasi']);
                    }
                );

                $mergedHaki = $haki->merge($hakiManual);

                // Karya API
                $karyaSeni =
                    collect($filteredPublikasi)->filter(function ($publikasi) {
                        return $publikasi['id_jenis_publikasi'] === JenisPublikasi::where('nama', 'Rancangan dan karya seni monumental')->first()->id;
                    });

                // Karya Manual
                $karyaSeniManual =
                    collect($filteredPublikasiManual)->filter(function ($publikasi) {
                        return $publikasi['jenis_publikasi'] === JenisPublikasi::where('nama', 'Rancangan dan karya seni monumental')->first()->id;
                    });

                //Gabungan Karya
                $mergedKarya = $karyaSeni->merge($karyaSeniManual);

                if (
                    (($mergedJurnalInternasional->count() >= 3 || $mergedJurnalInternasionalBereputasi->count() >= 1) &&
                        ($mergedBuku->count() >= 1 || $mergedPaten->count() >= 1 || $mergedHaki->count() >= 1)) ||
                    $mergedKarya->count() >= 1
                ) {
                    return [
                        'success' => true,
                        'message' => 'Lolos Rekomendasi',
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Tidak Lolos Rekomendasi',
                    'detail' => 'Tidak memenuhi kewajiban Publikasi selama 3 Tahun setelah SK terbit',
                ];
                break;

            default:
                return [
                    'success' => false,
                    'message' => 'Tidak Ada Jabatan',
                ];
                break;
        }
    }

    public function getDetailRekomendasi($id_sdm)
    {
        // dd($id_sdm);
        set_time_limit(0);
        $sdm = Sdm::find($id_sdm);
        $dataPublikasi = Publikasi::with('jenisPublikasi')->where('id_sdm', $id_sdm)->get();
        $filteredPublikasi = collect($dataPublikasi)->filter(function ($publikasi) use ($sdm) {
            $tanggalPublikasi = Carbon::parse($publikasi['tanggal']);
            $tigaTahunTerakhir = Carbon::now()->subYears(3);
            return $tanggalPublikasi->betweenIncluded($tigaTahunTerakhir, Carbon::now());
        });

        // dd($filteredPublikasi);

        // Klasifikasi publikasi berdasarkan jenis
        $jurnalNasional = collect($filteredPublikasi)->filter(function ($publikasi) {
            return $publikasi['jenis_publikasi'] === JenisPublikasi::where('nama', 'Jurnal nasional')->first()->id;
        });

        // dd($jurnalNasional);

        // $jurnalNasionalTerakreditasi = collect($filteredPublikasi)->filter(function ($publikasi) {
        //     return $publikasi['jenis_publikasi'] === JenisPublikasi::where('nama', 'Jurnal nasional terakreditasi')->first()->id;
        // });

        $jurnalInternasional = collect($filteredPublikasi)->filter(function ($publikasi) {
            return $publikasi['jenis_publikasi'] === 23;
        });

        $jurnalInternasionalBereputasi = collect($filteredPublikasi)->filter(function ($publikasi) {
            return $publikasi['jenis_publikasi'] === 24;
        });

        $jurnalNasionalTerakreditasi = collect($filteredPublikasi)->filter(function ($publikasi) {
            return $publikasi['jenis_publikasi'] === 22;
        });

        $bukuReferensi = collect($filteredPublikasi)->filter(function ($publikasi) {
            return $publikasi['jenis_publikasi'] === 12;
        });

        $bukuLainnya = collect($filteredPublikasi)->filter(function ($publikasi) {
            return $publikasi['jenis_publikasi'] === 13;
        });

        $bookChapterNasional = collect($filteredPublikasi)->filter(function ($publikasi) {
            return $publikasi['jenis_publikasi'] === 14;
        });

        $bookChapterInternasional = collect($filteredPublikasi)->filter(function ($publikasi) {
            return $publikasi['jenis_publikasi'] === 15;
        });

        $bukuMonograf = collect($filteredPublikasi)->filter(function ($publikasi) {
            return $publikasi['jenis_publikasi'] === 11;
        });

        $buku = $bukuReferensi
            ->merge($bukuLainnya)
            ->merge($bookChapterNasional)
            ->merge($bookChapterInternasional)
            ->merge($bukuMonograf);

        $karya = collect($filteredPublikasi)->filter(function ($publikasi) {
            return $publikasi['jenis_publikasi'] === JenisPublikasi::where('nama', 'Rancangan dan karya seni monumental')->first()->id;
        });
        return view('pages.dashboard.sdm.detailPublikasiSDM', [
            'data' => $sdm,
            'jurnalNasional' => $jurnalNasional,
            'jurnalNasionalTerakreditasi' => $jurnalNasionalTerakreditasi,
            'jurnalInternasional' => $jurnalInternasional,
            'jurnalInternasionalBereputasi' => $jurnalInternasionalBereputasi,
            'buku' => $buku,
            'karya' => $karya,
        ]);
    }

    public function getDetailRekomendasiFromAPI($id_sdm)
    {
        // dd($id_sdm);
        set_time_limit(0);
        $sdm = Sdm::find($id_sdm);
        $publikasiController = new PublikasiController();
        $dataPublikasi = $publikasiController->getDetailPublikasi($id_sdm);
        foreach ($dataPublikasi as $publikasi) {
            $penulis = $publikasi['penulis'];
            $urutanPenulis = null;

            foreach ($penulis as $penulisItem) {
                if ($penulisItem['id_sdm'] == $id_sdm) {
                    $urutanPenulis = $penulisItem['urutan'];
                    break;
                }
            }
            Publikasi::updateOrInsert(
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

        $filteredPublikasi = collect($dataPublikasi)->filter(function ($publikasi) use ($sdm) {
            $tanggalPublikasi = Carbon::parse($publikasi['tanggal']);
            $tigaTahunTerakhir = Carbon::now()->subYears(3);
            return $tanggalPublikasi->betweenIncluded($tigaTahunTerakhir, Carbon::now());
        });

        // dd($filteredPublikasi);

        // Klasifikasi publikasi berdasarkan jenis
        $jurnalNasional = collect($filteredPublikasi)->filter(function ($publikasi) {
            return $publikasi['jenis_publikasi'] === JenisPublikasi::where('nama', 'Jurnal nasional')->first()->id;
        });

        // dd($jurnalNasional);

        $jurnalNasionalTerakreditasi = collect($filteredPublikasi)->filter(function ($publikasi) {
            return $publikasi['jenis_publikasi'] === JenisPublikasi::where('nama', 'Jurnal nasional terakreditasi')->first()->id;
        });

        $jurnalInternasional = collect($filteredPublikasi)->filter(function ($publikasi) {
            return $publikasi['jenis_publikasi'] === 24 && $publikasi['urutan_penulis'] === 1;
        });

        $jurnalInternasionalBereputasi = collect($filteredPublikasi)->filter(function ($publikasi) {
            return $publikasi['jenis_publikasi'] === 24 && $publikasi['urutan_penulis'] === 1;
        });

        $bukuReferensi = collect($filteredPublikasi)->filter(function ($publikasi) {
            return $publikasi['jenis_publikasi'] === 12 && $publikasi['urutan_penulis'] === 1;
        });

        $bukuLainnya = collect($filteredPublikasi)->filter(function ($publikasi) {
            return $publikasi['jenis_publikasi'] === 13 && $publikasi['urutan_penulis'] === 1;
        });

        $bookChapterNasional = collect($filteredPublikasi)->filter(function ($publikasi) {
            return $publikasi['jenis_publikasi'] === 14 && $publikasi['urutan_penulis'] === 1;
        });

        $bookChapterInternasional = collect($filteredPublikasi)->filter(function ($publikasi) {
            return $publikasi['jenis_publikasi'] === 15 && $publikasi['urutan_penulis'] === 1;
        });

        $buku = $bukuReferensi
            ->merge($bukuLainnya)
            ->merge($bookChapterNasional)
            ->merge($bookChapterInternasional);

        $karya = collect($filteredPublikasi)->filter(function ($publikasi) {
            return $publikasi['jenis_publikasi'] === JenisPublikasi::where('nama', 'Rancangan dan karya seni monumental')->first()->id;
        });
        return view('pages.dashboard.sdm.detailPublikasiSDM', [
            'data' => $sdm,
            'jurnalNasional' => $jurnalNasional,
            'jurnalNasionalTerakreditasi' => $jurnalNasionalTerakreditasi,
            'jurnalInternasional' => $jurnalInternasional,
            'jurnalInternasionalBereputasi' => $jurnalInternasionalBereputasi,
            'buku' => $buku,
            'karya' => $karya,
        ]);
    }

    public function getSertifikatPendidik($id_sdm)
    {
        $baseUrl = env('BASE_URL');
        // dd($baseUrl);
        $bearerToken = env('API_BEARER_TOKEN');
        // dd($bearerToken);
        if (!$baseUrl || !$bearerToken) {
            return response()->json([
                'success' => false,
                'message' => 'API configuration is missing in the environment file.',
            ], 500);
        }

        try {
            $response = Http::withToken($bearerToken)
                ->get($baseUrl . '/sertifikasi_dosen', [
                    'id_sdm' => $id_sdm,
                ]);

            if ($response->successful()) {
                $responseData = $response->json();
                return $responseData;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch data from API',
                    'error' => $response->body(),
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while making the API request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAllDataSDMByNIDN()
    {
        $getToken = $this->getToken();
        $baseUrl = env('BASE_URL');
        // $bearerToken = env('API_BEARER_TOKEN');

        if (!$baseUrl || !$getToken) {
            return response()->json([
                'success' => false,
                'message' => 'API configuration is missing in the environment file.',
            ], 500);
        }

        try {
            set_time_limit(0);

            // List NIDN yang ingin diambil
            $nidnList = [
                '0302066905',
                '0324047407',
            ];

            $allData = []; // Array untuk menyimpan semua data SDM

            foreach ($nidnList as $nidn) {
                $response = Http::withToken($getToken)
                    ->get($baseUrl . '/referensi/sdm', ['nidn' => $nidn]);

                if ($response->successful()) {
                    $responseData = $response->json();
                    $allData = array_merge($allData, $responseData); // Gabungkan semua data
                    // dd($allData);
                } else {
                    return redirect()->back()->with('error', 'Failed to fetch data for NIDN ' . $nidn);
                }
            }

            // Proses semua data yang telah dikumpulkan
            foreach ($allData as $sdm) {
                try {
                    $detailJabatan = $this->getDetailJabatanFungsional($sdm['id_sdm']);
                } catch (\Exception $e) {
                    $detailJabatan = null;
                }

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

                try {
                    $rekomendasi = $this->getRekomendasiTunjangan($sdm['id_sdm']);
                } catch (\Exception $e) {
                    $rekomendasi = null;
                }

                Sdm::updateOrCreate(
                    ['id_sdm' => $sdm['id_sdm']],
                    [
                        'rekomendasi' => $rekomendasi['message'],
                        'keterangan' => $rekomendasi['detail'] ?? null,
                    ]
                );
            }
            return redirect()->back()->with('success', 'Data SDM berhasil disinkronisasi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function getDetailRekomendasiLivewire($id_sdm)
    {
        set_time_limit(0);
        $sdm = Sdm::find($id_sdm);
        // dd($sdm);
        return view('pages.dashboard.sdm.detailPublikasiSDM', [
            'data' => $sdm,
        ]);
    }

    public function syncData(Request $request)
    {
        set_time_limit(0);

        $isCancelled = $request->input('isCancelled', false);
        if ($isCancelled) {
            return response()->json(['message' => 'Sinkronisasi dibatalkan.', 'progress' => 0]);
        }

        $totalSdm = Sdm::count();
        $processed = 0;

        try {
            $sdmController = app(SdmController::class);
            $lastProcessedId = Cache::get('last_processed_id', '');
            $query = Sdm::orderBy('id_sdm', 'asc');
            if (!empty($lastProcessedId)) {
                $query->where('id_sdm', '>', $lastProcessedId);
            }

            Log::info("Status pembatalan diterima: " . ($isCancelled ? "true" : "false"));
            $query->chunk(100, function ($sdms) use ($sdmController, &$processed, $totalSdm, $request) {
                foreach ($sdms as $sdm) {
                    if ($request->input('isCancelled', false)) {
                        throw new \Exception("Sinkronisasi dibatalkan.");
                    }

                    try {
                        $detailJabatan = $sdmController->getDetailJabatanFungsional($sdm->id_sdm);
                        $rekomendasi = $sdmController->getRekomendasiTunjangan($sdm->id_sdm);

                        $sdm->update([
                            'jabatan_fungsional' => $detailJabatan['jabatan_fungsional'] ?? null,
                            'angka_kredit' => $detailJabatan['angka_kredit'] ?? null,
                            'rekomendasi' => $rekomendasi['message'] ?? null,
                            'keterangan' => $rekomendasi['detail'] ?? null,
                        ]);

                        Log::info("Sinkronisasi selesai untuk ID: {$sdm->id_sdm}");
                        Cache::put('last_processed_id', $sdm->id_sdm);
                    } catch (\Throwable $e) {
                        Log::error("Gagal sinkronisasi ID: {$sdm->id_sdm}, Error: " . $e->getMessage());
                    }

                    $processed++;
                }
            });

            $progress = round(($processed / $totalSdm) * 100);
            Cache::forget('last_processed_id');
            return response()->json(['progress' => $progress, 'message' => 'Sinkronisasi selesai.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'progress' => 0]);
        }
    }
}
