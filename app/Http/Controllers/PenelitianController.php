<?php

namespace App\Http\Controllers;

use App\Models\Penelitian;
use App\Models\Sdm;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use function PHPSTORM_META\map;

class PenelitianController extends Controller
{
    public function index()
    {
        $dataSdm = session()->get('sdm_data');
        $id_sdm = $dataSdm[0]['id_sdm'];
        $dataPenelitian = Penelitian::where('id_sdm', $id_sdm)->get();

        if (session()->has('sdm_data')) {
            return view('pages.dashboard.penelitian.index', [
                'data' => $dataPenelitian,
                'id_sdm' => $id_sdm,
            ]);
        } else {
            // Session sudah habis, arahkan pengguna ke halaman login
            return redirect()->route('login');
        }
        // if (!$baseUrl || !$getToken) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'API configuration is missing in the environment file.',
        //     ], 500);
        // }

        // try {
        //     $response = Http::withToken($getToken)
        //         ->get($baseUrl . '/penelitian', [
        //             'id_sdm' => $id_sdm,
        //         ]);

        //     if ($response->successful()) {
        //         $responseData = $response->json();
        //         $tahunSekarang = Carbon::now()->year;

        //         $filteredData = array_filter($responseData, function ($item) use ($tahunSekarang) {
        //             if (isset($item['tahun_pelaksanaan']) && is_numeric($item['tahun_pelaksanaan'])) {
        //                 return $item['tahun_pelaksanaan'] >= ($tahunSekarang - 3);
        //             }
        //             return false;
        //         });

        //         return view('pages.dashboard.penelitian.index', [
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

    public function detailPenelitian($id)
    {
        $sdmController = new SdmController();
        $getToken = $sdmController->getToken();
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
                ->get($baseUrl . '/penelitian/' . $id);

            if ($response->successful()) {
                $responseData = $response->json();
                // dd($responseData);
                return view('pages.dashboard.penelitian.detailPenelitian', [
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

    public function getDataPenelitianFromAPI($id_sdm)
    {
        set_time_limit(0);
        // $sdm = Sdm::find($id_sdm);
        $baseUrl = env('BASE_URL');
        // dd($id_sdm);
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
                ->get($baseUrl . '/penelitian', [
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
                // foreach ($filteredData as $penelitian) {
                //     Penelitian::create([
                //         'id_sdm' => $id_sdm,
                //         'id_penelitian' => $penelitian['id'],
                //         'judul' => $penelitian['judul'],
                //         'lama_kegiatan' => $penelitian['lama_kegiatan'],
                //         'bidang_keilmuan' => json_encode($penelitian['bidang_keilmuan']),
                //         'tahun_pelaksanaan' => $penelitian['tahun_pelaksanaan'],
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
