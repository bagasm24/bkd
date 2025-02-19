<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataFeed;
use App\Models\Sdm;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $nidn = $user->nidn;
        $sdm = Sdm::where('nidn', $nidn)->first();
        $sdmController = new SdmController();
        $data = $sdmController->getSDMDataLogin();
        if ($sdm) {
            $dataSdm = $sdm->toArray();
        } else {
            if (isset($data[0]['id_sdm']) && !empty($data[0]['id_sdm'])) {
                $idSdm = $data[0]['id_sdm'];
                $detailJaFung = $sdmController->getDetailJabatanFungsional($idSdm);

                $sdm = new Sdm();
                $sdm->nidn = $nidn;
                $sdm->id_sdm = $data[0]['id_sdm'];
                $sdm->nama_sdm = $data[0]['nama_sdm'];
                $sdm->nuptk = $data[0]['nuptk'];
                $sdm->nip = $data[0]['nip'];
                $sdm->nama_status_aktif = $data[0]['nama_status_aktif'];
                $sdm->nama_status_pegawai = $data[0]['nama_status_pegawai'];
                $sdm->jenis_sdm = $data[0]['jenis_sdm'];
                $sdm->jabatan_fungsional = $detailJaFung['jabatan_fungsional'];
                $sdm->angka_kredit = $detailJaFung['angka_kredit'];
                $sdm->save();

                $dataSdm = $sdm->toArray();
            } else {
                return view('pages/dashboard/dashboard');
            }
        }
        return view('pages/dashboard/dashboard', compact('dataSdm'));
    }

    /**
     * Displays the analytics screen
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function analytics()
    {
        return view('pages/dashboard/analytics');
    }

    /**
     * Displays the fintech screen
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function fintech()
    {
        return view('pages/dashboard/fintech');
    }
}
