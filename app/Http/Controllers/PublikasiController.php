<?php

namespace App\Http\Controllers;

use App\Models\JenisPublikasi;
use App\Models\Kategori;
use App\Models\KategoriKegiatan;
use App\Models\Publikasi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use function PHPSTORM_META\map;

class PublikasiController extends Controller
{
    //
    public function index(Request $request)
    {
        $dataSdm = session()->get('sdm_data');
        $id_sdm = $dataSdm[0]['id_sdm'];
        $tahunSekarang = Carbon::now()->year;

        if (session()->has('sdm_data')) {
            $dataPublikasi = Publikasi::with('jenisPublikasi') // Load relasi jenisPublikasi
                ->where('id_sdm', $id_sdm)
                ->get() // Jangan diubah ke array
                ->filter(function ($item) use ($tahunSekarang) {
                    if (isset($item->tanggal)) {
                        $tahunPelaksanaan = date('Y', strtotime($item->tanggal));
                        return $tahunPelaksanaan >= ($tahunSekarang - 2); // Ambil 3 tahun terakhir
                    }
                    return false;
                });
            $syncTerbaru = Publikasi::latest('updated_at')
                ->where('id_sdm', $id_sdm)
                ->first();
            // dd($syncTerbaru);
            return view('pages.dashboard.publikasi.index', [
                'data' => $dataPublikasi,
                'sync' => $syncTerbaru,
                'id_sdm' => $id_sdm,
            ]);
        } else {
            // Session sudah habis, arahkan pengguna ke halaman login
            return redirect()->route('login');
        }

        // $baseUrl = env('BASE_URL');
        // // dd($baseUrl);
        // $bearerToken = env('API_BEARER_TOKEN');
        // // dd($bearerToken);
        // if (!$baseUrl || !$bearerToken) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'API configuration is missing in the environment file.',
        //     ], 500);
        // }

        // try {
        //     set_time_limit(0);
        //     $response = Http::withToken($bearerToken)
        //         ->get($baseUrl . '/publikasi', [
        //             'id_sdm' => $id_sdm,
        //         ]);

        //     if ($response->successful()) {
        //         $responseData = $response->json();
        //         // $getJenisPublikasi = $this->getJenisPublikasi();
        //         $tahunSekarang = Carbon::now()->year;

        //         $filteredData = array_filter($responseData, function ($item) use ($tahunSekarang) {
        //             if (isset($item['tanggal'])) {
        //                 $tahunPelaksanaan = date('Y', strtotime($item['tanggal']));
        //                 return $tahunPelaksanaan >= ($tahunSekarang - 2); // Ambil 3 tahun terakhir
        //             }
        //             return false;
        //         });
        //         return view('pages.dashboard.publikasi.index', [
        //             'data' => $filteredData,
        //             'id_sdm' => $id_sdm,
        //         ]);
        //     } else {
        //         return response()->json([
        //             'success' => false,
        //             'message' => 'Failed to fetch data from API',
        //             'error' => $response->body(),
        //         ], $response->status());
        //     }
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Something went wrong while making the API request.',
        //         'error' => $e->getMessage(),
        //     ], 500);
        // }
    }

    public function getDataPublikasi($id_sdm)
    {
        $baseUrl = env('BASE_URL');
        // dd($baseUrl);
        // $bearerToken = env('API_BEARER_TOKEN');
        $getToken = $this->getToken();
        // dd($bearerToken);
        if (!$baseUrl || !$getToken) {
            return response()->json([
                'success' => false,
                'message' => 'API configuration is missing in the environment file.',
            ], 500);
        }

        try {
            $response = Http::withToken($getToken)
                ->get($baseUrl . '/publikasi', [
                    'id_sdm' => $id_sdm,
                ]);

            if ($response->successful()) {
                $responseData = $response->json();
                // $getJenisPublikasi = $this->getJenisPublikasi();
                $tahunSekarang = Carbon::now()->year;

                $filteredData = array_filter($responseData, function ($item) use ($tahunSekarang) {
                    if (isset($item['tanggal'])) {
                        $tahunPelaksanaan = date('Y', strtotime($item['tanggal']));
                        return $tahunPelaksanaan >= ($tahunSekarang - 2); // Ambil 3 tahun terakhir
                    }
                    return false;
                });
                // dd($filteredData);
                return $filteredData;
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

    public function getAllDataPublikasiByIdSdm($id_sdm)
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
            $response = Http::withToken($getToken)
                ->get($baseUrl . '/publikasi', [
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

    public function getDetailPublikasi($id_sdm)
    {
        $dataPublikasi = $this->getAllDataPublikasiByIdSdm($id_sdm);
        // dd($dataPublikasi);
        $responseDataArray = [];
        foreach ($dataPublikasi as $p) {
            $baseUrl = env('BASE_URL');
            $getToken = $this->getToken();
            // dd($baseUrl);
            $bearerToken = env('API_BEARER_TOKEN');
            // dd($bearerToken);
            if (!$baseUrl || !$getToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'API configuration is missing in the environment file.',
                ], 500);
            }

            try {
                $response = Http::withToken($getToken)
                    ->get($baseUrl . '/publikasi/' . $p['id']);

                if ($response->successful()) {
                    $responseData = $response->json();
                    $responseDataArray[] = $responseData;
                } else {
                    Log::error('Failed to fetch data from API: ' . $response->body());
                    continue; // Abaikan data yang gagal
                }
            } catch (\Exception $e) {
                Log::error('Error fetching data from API: ' . $e->getMessage());
                continue; // Abaikan data yang menyebabkan error
            }
        }

        return $responseDataArray;
    }

    public function detailPublikasiSDM($id_sdm)
    {

        return view('pages.dashboard.sdm.detailPublikasiSDM');
    }

    public function getJenisPublikasi()
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
                ->get($baseUrl . '/referensi/jenis_publikasi');

            if ($response->successful()) {
                $responseData = $response->json();
                foreach ($responseData as &$j) {
                    JenisPublikasi::updateOrCreate(
                        ['id' => $j['id']], // Kondisi untuk update jika sudah ada
                        [
                            'nama' => $j['nama']
                        ]
                    );
                }
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

    public function addPublikasi()
    {
        $jenisPublikasi = JenisPublikasi::all();
        $parentKategori = KategoriKegiatan::whereNull('parent_id')->get();
        // dd($parentKategori);
        // dd($jenisPublikasi);
        return view('pages.dashboard.publikasi.add', [
            'jenisPublikasi' => $jenisPublikasi,
            'kategori' => $parentKategori,
        ]);
    }

    public function savePublikasi(Request $request)
    {
        $dataSdm = session()->get('sdm_data');
        // dd($request->all());
        $request->validate([
            'judul' => 'required|string|max:255',
            'nama_jurnal' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'jenis_publikasi' => 'required|exists:jenis_publikasis,id',
            'urutan_penulis' => 'required|integer|min:1',
            'kategori_publikasi' => 'required|exists:kategori_kegiatans,id',
            'penerbit' => 'required',
        ]);

        // Simpan ke database
        Publikasi::create([
            'id_sdm' => $dataSdm[0]['id_sdm'],
            'judul' => $request->judul,
            'nama_jurnal' => $request->nama_jurnal,
            'tanggal' => $request->tanggal,
            'jenis_publikasi' => $request->jenis_publikasi,
            'urutan_penulis' => $request->urutan_penulis,
            'kategori_kegiatan' => $request->sub2publikasi ?? $request->subpublikasi ?? $request->kategori_publikasi,
            'asal_data' => 'MANUAL',
            'penerbit' => $request->penerbit,
        ]);

        return redirect()->route('publikasi')->with('success', 'Publikasi berhasil disimpan!');
    }

    public function getAllDataPublikasi()
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
                        $dataPublikasi = $this->getDetailPublikasi($sdm['id_sdm']);
                        // dd($sdm['id_sdm']);
                    } catch (\Exception $e) {
                        $dataPublikasi = null; // Jika gagal, berikan nilai null
                    }

                    foreach ($dataPublikasi as $publikasi) {
                        $penulis = $publikasi['penulis'];
                        $urutanPenulis = null;

                        foreach ($penulis as $penulisItem) {
                            if ($penulisItem['id_sdm'] == $sdm['id_sdm']) {
                                $urutanPenulis = $penulisItem['urutan'];
                                break;
                            }
                        }
                        Publikasi::updateOrCreate(
                            ['id_sdm' => $sdm['id_sdm'], 'id_publikasi' => $publikasi['id']],
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
                    Log::info("Sinkronisasi Data Publikasi selesai untuk ID: {$sdm['id_sdm']}");
                }
                return redirect()->back()->with('success', 'Data SDM berhasil disinkronisasi.');
            } else {
                return redirect()->back()->with('error', 'Failed to fetch data from API: ' . $response->body());
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}
