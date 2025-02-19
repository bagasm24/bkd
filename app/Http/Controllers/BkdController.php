<?php

namespace App\Http\Controllers;

use App\Models\Sdm;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BkdController extends Controller
{
    //
    public function index()
    {
        $tahunAjaran = TahunAjaran::all();
        $dataDosen = Sdm::all();
        return view('pages.dashboard.bkd.index', compact('tahunAjaran', 'dataDosen'));
    }

    public function cekBKD(Request $request)
    {
        $sdmController = new SdmController();
        $getToken = $sdmController->getToken();
        $tahunAjaran = $request->input('tahunAjaran');
        $dosen = $request->input('dosen');

        $baseUrl = env('BASE_URL');
        // $bearerToken = env('API_BEARER_TOKEN');

        if (!$baseUrl || !$getToken) {
            return response()->json([
                'success' => false,
                'message' => 'Konfigurasi API tidak ditemukan.',
            ], 500);
        }

        try {
            $response = Http::withToken($getToken)
                ->get($baseUrl . '/bkd/laporan_akhir_bkd', [
                    'id_sdm' => $dosen,
                ]);

            if ($response->successful()) {
                $responseData = $response->json();
                // dd($responseData);

                // Filter data berdasarkan tahun ajaran yang dipilih
                $filterBKD = array_filter($responseData, function ($bkd) use ($tahunAjaran) {
                    return $bkd['id_smt'] == $tahunAjaran;
                });

                $dataDosen = Sdm::where('id_sdm', $dosen)->get();
                // log($dataDosen);

                $result = [
                    'dosen' => $dataDosen,
                    'bkd' => array_values($filterBKD),
                ];

                // return response()->json($result);

                return response()->json(array_values($filterBKD));
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data dari API',
                    'error' => $response->body(),
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengakses API.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
