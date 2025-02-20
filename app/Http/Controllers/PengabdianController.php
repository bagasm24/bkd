<?php

namespace App\Http\Controllers;

use App\Models\Pengabdian;
use App\Models\Sdm;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PengabdianController extends Controller
{
    public function index()
    {
        if (session()->has('sdm_data')) {
            $dataSdm = session()->get('sdm_data');
            $id_sdm = $dataSdm[0]['id_sdm'];
            $dataPengabdian = Pengabdian::where('id_sdm', $id_sdm)->get();
            return view('pages.dashboard.pengabdian.index', [
                'data' => $dataPengabdian,
                'id_sdm' => $id_sdm,
            ]);
        } else {
            // Session sudah habis, arahkan pengguna ke halaman login
            return redirect()->route('login');
        }
    }

    public function detailPengabdian($id)
    {
        $baseUrl = env('BASE_URL');
        $bearerToken = env('API_BEARER_TOKEN');
        if (!$baseUrl || !$bearerToken) {
            return response()->json([
                'success' => false,
                'message' => 'API configuration is missing in the environment file.',
            ], 500);
        }

        try {
            $response = Http::withToken($bearerToken)
                ->get($baseUrl . '/pengabdian/' . $id);
            if ($response->successful()) {
                $responseData = $response->json();
                // dd($responseData);
                return view('pages.dashboard.pengabdian.detailPengabdian', [
                    'data' => $responseData,
                ]);
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

    public function dataPengabdianPerSemester()
    {
        $sdmController = new SdmController();
        $data = $sdmController->getSDMData();
        $id_sdm = $data[0]['id_sdm'];
        $semester = '20232';
        // Ambil URL dan Token dari .env
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
                ->get($baseUrl . '/bkd/pengmas', [
                    'id_sdm' => $id_sdm,
                    'id_smt' => $semester,
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

    public function getDataPengabdianFromAPI($id_sdm)
    {
        set_time_limit(0);
        $baseUrl = env('BASE_URL');
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
                ->get($baseUrl . '/pengabdian', [
                    'id_sdm' => $id_sdm,
                ]);

            if ($response->successful()) {
                $responseData = $response->json();
                $tahunSekarang = Carbon::now()->year;

                $filteredData = array_filter($responseData, function ($item) use ($tahunSekarang) {
                    if (isset($item['tahun_pelaksanaan']) && is_numeric($item['tahun_pelaksanaan'])) {
                        return $item['tahun_pelaksanaan'] >= ($tahunSekarang - 3);
                    }
                    return false;
                });
                // dd($responseData);
                // foreach ($filteredData as $pengabdian) {
                //     Pengabdian::create([
                //         'id_sdm' => $id_sdm,
                //         'id_pengabdian' => $pengabdian['id'],
                //         'judul' => $pengabdian['judul'],
                //         'lama_kegiatan' => $pengabdian['lama_kegiatan'],
                //         'bidang_keilmuan' => json_encode($pengabdian['bidang_keilmuan']),
                //         'tahun_pelaksanaan' => $pengabdian['tahun_pelaksanaan'],
                //     ]);
                // }

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
}
