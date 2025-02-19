<?php

namespace Database\Seeders;

use App\Models\TahunAjaran;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tahunAjarans = [
            [
                'id' => 1,
                'tahun' => '20151',
                'semester' => '2015/2016 - Ganjil',
            ],
            [
                'id' => 2,
                'tahun' => '20152',
                'semester' => '2015/2016 - Genap',
            ],
            [
                'id' => 3,
                'tahun' => '20161',
                'semester' => '2016/2017 - Ganjil',
            ],
            [
                'id' => 4,
                'tahun' => '20162',
                'semester' => '2016/2017 - Genap',
            ],
            [
                'id' => 5,
                'tahun' => '20171',
                'semester' => '2017/2018 - Ganjil',
            ],
            [
                'id' => 6,
                'tahun' => '20172',
                'semester' => '2017/2018 - Genap',
            ],
            [
                'id' => 7,
                'tahun' => '20181',
                'semester' => '2018/2019 - Ganjil',
            ],
            [
                'id' => 8,
                'tahun' => '20182',
                'semester' => '2018/2019 - Genap',
            ],
            [
                'id' => 9,
                'tahun' => '20191',
                'semester' => '2019/2020 - Ganjil',
            ],
            [
                'id' => 10,
                'tahun' => '20192',
                'semester' => '2019/2020 - Genap',
            ],
            [
                'id' => 11,
                'tahun' => '20201',
                'semester' => '2020/2021 - Ganjil',
            ],
            [
                'id' => 12,
                'tahun' => '20202',
                'semester' => '2020/2021 - Genap',
            ],
            [
                'id' => 14,
                'tahun' => '20203',
                'semester' => '2020/2021 - Antara',
            ],
            [
                'id' => 15,
                'tahun' => '20211',
                'semester' => '2021/2022 - Ganjil',
            ],
            [
                'id' => 16,
                'tahun' => '20212',
                'semester' => '2021/2022 - Genap',
            ],
            [
                'id' => 20,
                'tahun' => '20221',
                'semester' => '2022/2023 - Ganjil',
            ],
            [
                'id' => 21,
                'tahun' => '20222',
                'semester' => '2022/2023 - Genap',
            ],
            [
                'id' => 22,
                'tahun' => '20223',
                'semester' => '2022/2023 - Antara',
            ],
            [
                'id' => 23,
                'tahun' => '20231',
                'semester' => '2023/2024 - Ganjil',
            ],
            [
                'id' => 24,
                'tahun' => '20232',
                'semester' => '2023/2024 - Genap',
            ],
            [
                'id' => 25,
                'tahun' => '20241',
                'semester' => '2024/2025 - Ganjil',
            ],
            [
                'id' => 26,
                'tahun' => '20242',
                'semester' => '2024/2025 - Genap',
            ],
        ];

        foreach ($tahunAjarans as $tahunAjaran) {
            TahunAjaran::create($tahunAjaran);
        }
    }
}
