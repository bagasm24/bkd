<x-app-layout>
    @if(session('success'))
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
            {{ session('error') }}
        </div>
    @endif
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Dashboard actions -->
        <!-- Cards -->
        <div id="alert-1" class="flex items-center p-4 mb-4 text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
            <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <span class="sr-only">Info</span>
            <div class="ms-3 text-sm font-medium">
                Silahkan lakukan sikronisasi data Publikasi secara berkala.
            </div>
                <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-blue-50 text-blue-500 rounded-lg focus:ring-2 focus:ring-blue-400 p-1.5 hover:bg-blue-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-blue-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-1" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>
        <div class="grid grid-cols-12 gap-6">
            <!-- Table (Top Channels) -->
            <div class="col-span-full xl:col-span-full bg-white dark:bg-gray-800 shadow-sm rounded-xl">
                <header class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                    <div class="">
                        <h2 class="font-semibold text-gray-800 dark:text-gray-100">Publikasi <span class="text-xs text-slate-500">(Data diambil dari 3 tahun terakhir)</span></h2>
                        <p class="text-xs text-slate-500">Data update : {{ !empty($sync['updated_at']) ? date('d/m/Y', strtotime($sync['updated_at'])) : '-' }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('pblks.add') }}">
                            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                Tambah Data Publikasi
                            </button>
                        </a>
                        @livewire('sync-publikasi')
                    </div>
                </header>
                <div class="p-3">
                    <!-- Table -->
                    <div class="overflow-x-auto">                
                        <table id="pagination-table">
                            <thead>
                                <tr>
                                    <th>
                                        <span class="flex items-center">
                                            No
                                        </span>
                                    </th>
                                    {{-- <th>
                                        <span class="flex items-center">
                                            Id Kategori Kegiatan
                                        </span>
                                    </th> --}}
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
                                @foreach ($data as $d )
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    {{-- <td>{{ $d['kategori_kegiatan'] }}</td> --}}
                                    <td>{{ $d['judul'] }}</td>
                                    <td>{{ $d->jenisPublikasi->nama ?? 'Tidak Diketahui' }}</td>
                                    <td>{{ date('Y', strtotime($d['tanggal'])) }}</td>
                                    <td>{{ $d['asal_data'] }}</td>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
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

</script>