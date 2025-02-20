<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Dashboard actions -->
        {{-- <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Publikasi</h1>
            </div>

        </div> --}}
        
        <!-- Cards -->
        <div class="grid grid-cols-12 gap-6">
            <!-- Table (Top Channels) -->
            <div class="col-span-full xl:col-span-full bg-white dark:bg-gray-800 shadow-sm rounded-xl">
                <header class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                    <div class="">
                        <h2 class="font-semibold text-gray-800 dark:text-gray-100">Penelitian <span class="text-xs text-slate-500">(Data diambil dari 3 tahun terakhir)</span></h2>
                        {{-- <p class="text-xs text-slate-500">Data update : {{ !empty($sync['updated_at']) ? date('d/m/Y', strtotime($sync['updated_at'])) : '-' }}</p> --}}
                    </div>
                    @livewire('sync-penelitian')
                </header>
                <div class="p-3">
                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table id="pagination-table">
                            <!-- Table header -->
                            <thead>
                                <tr>
                                    {{-- <th class="p-2">
                                        <div class="font-semibold text-left">No</div>
                                    </th> --}}
                                    <th>
                                        <div class="font-semibold text-left">Judul</div>
                                    </th>
                                    <th>
                                        <div class="font-semibold text-center">Lama Kegiatan</div>
                                    </th>
                                    <th>
                                        <div class="font-semibold text-center">Tahun Pelaksanaan</div>
                                    </th>
                                    {{-- <th class="p-2">
                                        <div class="font-semibold text-center">Aksi</div>
                                    </th> --}}
                                    {{-- <th class="p-2">
                                        <div class="font-semibold text-center">Bidang Keilmuan</div>
                                    </th> --}}
                                </tr>
                            </thead>
                            <!-- Table body -->
                            <tbody>
                                <!-- Row -->
                                @foreach ($data as $d )
                                    <tr>
                                        {{-- <td class="p-2">
                                            <div class="">{{ $loop->iteration }}</div>
                                        </td> --}}
                                        <td>
                                            <div class="">{{ $d['judul'] }}</div>
                                        </td>
                                        <td>
                                            <div class="text-center">{{ $d['lama_kegiatan'] }} Tahun</div>
                                        </td>
                                        <td>
                                            <div class="text-center">{{ $d['tahun_pelaksanaan'] }}</div>
                                        </td>
                                        {{-- <td class="p-2">
                                            <div class="text-center"><a href="/dashboard/penelitian/{{ $d['id'] }}">Detail</a></div>
                                        </td> --}}
                                        {{-- <td class="p-2">
                                            @foreach ($d['bidang_keilmuan'] as $a )
                                                
                                            @endforeach
                                            <div class="text-center">{{ $a }}</div>
                                        </td> --}}
                                    </tr>
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