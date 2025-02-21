<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    public function index()
    {
        $dataUser = User::all();
        return view('pages.dashboard.user.index', compact('dataUser'));
    }

    // public function createUserBKD()
    // {
    //     $baseUrl = env('BASE_URL');
    //     $getToken = $this->getToken();
    //     if (!$baseUrl || !$getToken) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'API configuration is missing in the environment file.',
    //         ], 500);
    //     }

    //     try {
    //         set_time_limit(0);
    //         $response = Http::withToken($getToken)
    //             ->get($baseUrl . '/referensi/sdm');

    //         if ($response->successful()) {
    //             $responseData = $response->json();
    //             // dd($responseData);
    //             foreach ($responseData as $sdm) {
    //                 $password = "Ubharaj4y4Unggul";
    //                 // dd($password);
    //                 User::create(
    //                     [
    //                         'name' => $sdm['nama_sdm'],
    //                         'nidn' => $sdm['nidn'],
    //                         'password' => Hash::make($password),
    //                     ]
    //                 );
    //             }
    //             return redirect()->back()->with('success', 'Data User berhasil dibuat.');
    //         } else {
    //             return redirect()->back()->with('error', 'Failed to fetch data from API: ' . $response->body());
    //         }
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
    //     }
    // }

    public function createUserBKDAdmin()
    {
        $password = "Ubharaj4y4Unggul";
        User::create(
            [
                'name' => "Wakil Rektor 1",
                'username' => "warek1",
                'password' => Hash::make($password),
            ]
        );
    }
}
