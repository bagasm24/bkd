<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Cards -->
        <div class="grid grid-cols-12 gap-6">
            <!-- Table (Top Channels) -->
            <div class="col-span-full xl:col-span-full bg-white dark:bg-gray-800 shadow-sm rounded-xl">
                <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                    <h2 class="font-semibold text-gray-800 dark:text-gray-100">Detail Evaluasi Pegawai</h2>
                </header>
                <div class="p-3">
                    <div class="w-full flex bg-white dark:bg-gray-800 dark:border-gray-700">
                        <div class="flex flex-col px-4 p-4">
                            <h5 class="mb-1 text-xl font-medium text-gray-900 dark:text-white">{{ $data['nama_sdm'] }}</h5>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $data['jabatan_fungsional'] }}</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $data['sk'] }}</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $data['nidn'] }}</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $data['nama_status_aktif'] }}</span>
                            <h4 class="text-xl font-semibold text-gray-500 dark:text-gray-400">{{ $data['rekomendasi'] }}</h4>
                            <span class="text-xs">{{ $data['keterangan'] }}</span>
                        </div>
                        <div class="px-4 p-4 w-3/4">
                            <h4 class="text-sm">Catatan :</h4>
                            @if ($data['jabatan_fungsional'] == 'Lektor Kepala')
                                <ul class="text-xs text-slate-500 list-disc">
                                    <li>Dalam 3 Tahun paling sedikit 3(tiga) artikel yang diterbitkan dalam 1(satu) jurnal nasional dan 2(dua) jurnal nasional terakreditasi, atau paling sedikit satu artikel yang diterbitkan dalam jurnal internasional sebagai penulis pertama atau penulis korespondensi. Dan</li>
                                    <li>Paling sedikit 1 Buku sebagai penulis pertama atau paten. Atau</li>
                                    <li>Karya seni monumental atau desain monumental</li>
                                </ul>
                            @elseif ($data['jabatan_fungsional'] == 'Profesor')
                                <ul class="text-xs text-slate-500 list-disc">
                                    <li>Dalam 3 Tahun paling sedikit 3(tiga) artikel yang diterbitkan dalam jurnal internasional sebagai penulis pertama, atau paling sedikit 1 (satu) artikel yang diterbitkan dalam Jurnal Internasional Bereputasi sebagai penulis pertama; Dan</li>
                                    <li>Paling sedikit 1 Buku sebagai peulis pertama, paten atau HAKI. Atau</li>
                                    <li>Karya seni monumental atau desain monumental yang diakui oleh peer rewview internasion dan disahkan oleh senat perguruan tinggi.</li>
                                </ul>
                            @elseif ($data['jabatan_fungsional'] == 'Lektor')
                                <ul class="text-xs text-slate-500 list-disc">
                                    <li>Dalam 3 Tahun wajib menghasilkan 3(tiga) artikel yang diterbitkan dalam jurnal nasional atau 1 (satu) artikel diterbitkan dalam jurnal nasional dan 1 (satu) artikel diterbitkan dalam jurnal nasional terakreditasi, Atau</li>
                                    <li>1 (satu) artikel yang diterbitkan dalam jurnal internasional sebagai penulis pertama atau penulis korespodensi.</li>
                                </ul>
                            @elseif ($data['jabatan_fungsional'] == 'Asisten Ahli')
                                <p class="text-xs text-slate-500">Wajib menghasilkan 2(dua) karya ilmiah dalam 2 tahun yang diterbitkan dalam jurnal nasional sebagai penulis pertama atau penulis korespondensi.</p>
                            @elseif ($data['jabatan_fungsional'] == 'Tenaga Pengajar')
                                <p class="text-xs text-slate-500">Wajib menghasilkan 2(dua) karya ilmiah dalam 2 tahun yang diterbitkan dalam jurnal nasional sebagai penulis pertama atau penulis korespondensi.</p>
                            @else
                                <p class="text-xs text-slate-500">Wajib menghasilkan 2(dua) karya ilmiah dalam 2 tahun yang diterbitkan dalam jurnal nasional sebagai penulis pertama atau penulis korespondensi.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-styled-tab" data-tabs-toggle="#default-styled-tab-content" data-tabs-active-classes="text-purple-600 hover:text-purple-600 dark:text-purple-500 dark:hover:text-purple-500 border-purple-600 dark:border-purple-500" data-tabs-inactive-classes="dark:border-transparent text-gray-500 hover:text-gray-600 dark:text-gray-400 border-gray-100 hover:border-gray-300 dark:border-gray-700 dark:hover:text-gray-300" role="tablist">
                    @if ($data['jabatan_fungsional'] != 'Profesor')
                    <li class="me-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg" id="jurnalNasional-styled-tab" data-tabs-target="#styled-jurnalNasional" type="button" role="tab" aria-controls="jurnalNasional" aria-selected="false">Jurnal Nasional</button>
                    </li>
                    <li class="me-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="jurnalNasionalTerakreditasi-styled-tab" data-tabs-target="#styled-jurnalNasionalTerakreditasi" type="button" role="tab" aria-controls="jurnalNasionalTerakreditasi" aria-selected="false">Jurnal Nasional Terakreditasi</button>
                    </li>
                    @endif
                    <li class="me-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="jurnalInternasional-styled-tab" data-tabs-target="#styled-jurnalInternasional" type="button" role="tab" aria-controls="jurnalInternasional" aria-selected="false">Jurnal Internasional</button>
                    </li>
                    @if ($data['jabatan_fungsional'] == 'Profesor')
                    <li role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="jurnalInternasionalBereputasi-styled-tab" data-tabs-target="#styled-jurnalInternasionalBereputasi" type="button" role="tab" aria-controls="jurnalInternasionalBereputasi" aria-selected="false">Jurnal Internasional Bereputasi</button>
                    </li>
                    @endif
                    @if ($data['jabatan_fungsional'] == 'Lektor Kepala' || $data['jabatan_fungsional'] == 'Profesor')
                    <li role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="buku-styled-tab" data-tabs-target="#styled-buku" type="button" role="tab" aria-controls="buku" aria-selected="false">Buku</button>
                    </li>
                    <li role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="karya-styled-tab" data-tabs-target="#styled-karya" type="button" role="tab" aria-controls="karya" aria-selected="false">Karya Seni</button>
                    </li>
                    @endif
                </ul>
            </div>
            <div id="default-styled-tab-content">
                <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="styled-jurnalNasional" role="tabpanel" aria-labelledby="jurnalNasional-tab">
                    <div class="overflow-x-auto">                
                        <table id="pagination-table">
                            <thead>
                                <tr>
                                    <th>
                                        <span class="flex items-center">
                                            No
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Penerbit
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Judul
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Jenis Publikasi
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Tahun Publikasi
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Asal Data
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jurnalNasional as $d )
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $d['penerbit'] }}</td>
                                    <td>{{ $d['judul'] }}</td>
                                    <td>{{ $d->jenisPublikasi->nama ?? 'Tidak Diketahui' }}</td>
                                    <td>
                                        @if($d['tanggal'] !== null)
                                            {{ date('d/m/Y', strtotime($d['tanggal'])) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $d['asal_data'] }}</td>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="styled-jurnalNasionalTerakreditasi" role="tabpanel" aria-labelledby="jurnalNasionalTerakreditasi-tab">
                    <div class="overflow-x-auto">                
                        <table id="pagination-table-2">
                            <thead>
                                <tr>
                                    <th>
                                        <span class="flex items-center">
                                            No
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Penerbit
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Judul
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Jenis Publikasi
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Tahun Publikasi
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Asal Data
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jurnalNasionalTerakreditasi as $d )
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $d['penerbit'] }}</td>
                                    <td>{{ $d['judul'] }}</td>
                                    <td>{{ $d->jenisPublikasi->nama ?? 'Tidak Diketahui' }}</td>
                                    <td>
                                        @if($d['tanggal'] !== null)
                                            {{ date('d/m/Y', strtotime($d['tanggal'])) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $d['asal_data'] }}</td>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="styled-jurnalInternasional" role="tabpanel" aria-labelledby="jurnalInternasional-tab">
                    <div class="overflow-x-auto">                
                        <table id="pagination-table-3">
                            <thead>
                                <tr>
                                    <th>
                                        <span class="flex items-center">
                                            No
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Penerbit
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Judul
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Jenis Publikasi
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Tahun Publikasi
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Asal Data
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jurnalInternasional as $d )
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $d['penerbit'] }}</td>
                                    <td>{{ $d['judul'] }}</td>
                                    <td>{{ $d->jenisPublikasi->nama ?? 'Tidak Diketahui' }}</td>
                                    <td>
                                        @if($d['tanggal'] !== null)
                                            {{ date('d/m/Y', strtotime($d['tanggal'])) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $d['asal_data'] }}</td>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="styled-jurnalInternasionalBereputasi" role="tabpanel" aria-labelledby="jurnalInternasionalBereputasi-tab">
                    <div class="overflow-x-auto">                
                        <table id="pagination-table-7">
                            <thead>
                                <tr>
                                    <th>
                                        <span class="flex items-center">
                                            No
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Penerbit
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Judul
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Jenis Publikasi
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Tahun Publikasi
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Asal Data
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jurnalInternasionalBereputasi as $d )
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $d['penerbit'] }}</td>
                                    <td>{{ $d['judul'] }}</td>
                                    <td>{{ $d->jenisPublikasi->nama ?? 'Tidak Diketahui' }}</td>
                                    <td>
                                        @if($d['tanggal'] !== null)
                                            {{ date('d/m/Y', strtotime($d['tanggal'])) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $d['asal_data'] }}</td>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="styled-buku" role="tabpanel" aria-labelledby="buku-tab">
                    <div class="overflow-x-auto">                
                        <table id="pagination-table-5">
                            <thead>
                                <tr>
                                    <th>
                                        <span class="flex items-center">
                                            No
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Penerbit
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Judul
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Jenis Publikasi
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Tahun Publikasi
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Asal Data
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($buku as $d )
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $d['penerbit'] }}</td>
                                    <td>{{ $d['judul'] }}</td>
                                    <td>{{ $d->jenisPublikasi->nama ?? 'Tidak Diketahui' }}</td>
                                    <td>
                                        @if($d['tanggal'] !== null)
                                            {{ date('d/m/Y', strtotime($d['tanggal'])) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $d['asal_data'] }}</td>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="styled-karya" role="tabpanel" aria-labelledby="karya-tab">
                    <div class="overflow-x-auto">                
                        <table id="pagination-table-6">
                            <thead>
                                <tr>
                                    <th>
                                        <span class="flex items-center">
                                            No
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Penerbit
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Judul
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Jenis Publikasi
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Tahun Publikasi
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">
                                            Asal Data
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($karya as $d )
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $d['penerbit'] }}</td>
                                    <td>{{ $d['judul'] }}</td>
                                    <td>{{ $d->jenisPublikasi->nama ?? 'Tidak Diketahui' }}</td>
                                    <td>
                                        @if($d['tanggal'] !== null)
                                            {{ date('d/m/Y', strtotime($d['tanggal'])) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $d['asal_data'] }}</td>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    </div>
</x-app-layout>
<script>
    
    if (document.getElementById("pagination-table") && typeof simpleDatatables.DataTable !== 'undefined') {
        const dataTable = new simpleDatatables.DataTable("#pagination-table", {
            paging: true,
            perPage: 5,
            perPageSelect: [5, 10, 15, 20, 25],
            sortable: false
        });
    }

    if (document.getElementById("pagination-table-2") && typeof simpleDatatables.DataTable !== 'undefined') {
        const dataTable = new simpleDatatables.DataTable("#pagination-table-2", {
            paging: true,
            perPage: 5,
            perPageSelect: [5, 10, 15, 20, 25],
            sortable: false
        });
    }

    if (document.getElementById("pagination-table-2") && typeof simpleDatatables.DataTable !== 'undefined') {
        const dataTable = new simpleDatatables.DataTable("#pagination-table-3", {
            paging: true,
            perPage: 5,
            perPageSelect: [5, 10, 15, 20, 25],
            sortable: false
        });
    }


    if (document.getElementById("pagination-table-2") && typeof simpleDatatables.DataTable !== 'undefined') {
        const dataTable = new simpleDatatables.DataTable("#pagination-table-5", {
            paging: true,
            perPage: 5,
            perPageSelect: [5, 10, 15, 20, 25],
            sortable: false
        });
    }

    if (document.getElementById("pagination-table-2") && typeof simpleDatatables.DataTable !== 'undefined') {
        const dataTable = new simpleDatatables.DataTable("#pagination-table-5", {
            paging: true,
            perPage: 5,
            perPageSelect: [5, 10, 15, 20, 25],
            sortable: false
        });
    }

    if (document.getElementById("pagination-table-2") && typeof simpleDatatables.DataTable !== 'undefined') {
        const dataTable = new simpleDatatables.DataTable("#pagination-table-6", {
            paging: true,
            perPage: 5,
            perPageSelect: [5, 10, 15, 20, 25],
            sortable: false
        });
    }
    if (document.getElementById("pagination-table-2") && typeof simpleDatatables.DataTable !== 'undefined') {
        const dataTable = new simpleDatatables.DataTable("#pagination-table-7", {
            paging: true,
            perPage: 5,
            perPageSelect: [5, 10, 15, 20, 25],
            sortable: false
        });
    }


</script>
