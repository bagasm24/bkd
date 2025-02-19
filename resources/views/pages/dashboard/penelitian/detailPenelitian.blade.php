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
                <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                    <h2 class="font-semibold text-gray-800 dark:text-gray-100">Detail Penelitian</h2>
                </header>
                <div class="p-3">
                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full dark:text-gray-300">
                            <!-- Table header -->
                            <thead class="text-xs uppercase text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-700 dark:bg-opacity-50 rounded-sm">
                                <tr>
                                    <th class="p-2">
                                        <div class="font-semibold text-left">No</div>
                                    </th>
                                    <th class="p-2">
                                        <div class="font-semibold text-left">Judul</div>
                                    </th>
                                    <th class="p-2">
                                        <div class="font-semibold text-center">Lama Kegiatan</div>
                                    </th>
                                    <th class="p-2">
                                        <div class="font-semibold text-center">Tahun Pelaksanaan</div>
                                    </th>
                                    <th class="p-2">
                                        <div class="font-semibold text-center">Aksi</div>
                                    </th>
                                    {{-- <th class="p-2">
                                        <div class="font-semibold text-center">Bidang Keilmuan</div>
                                    </th> --}}
                                </tr>
                            </thead>
                            <!-- Table body -->
                            {{-- <tbody class="text-sm font-medium divide-y divide-gray-100 dark:divide-gray-700/60">
                                <!-- Row -->
                                @foreach ($data as $d )
                                    <tr>
                                        <td class="p-2">
                                            <div class="">{{ $loop->iteration }}</div>
                                        </td>
                                        <td class="p-2">
                                            <div class="">{{ $d['judul'] }}</div>
                                        </td>
                                        <td class="p-2">
                                            <div class="text-center">{{ $d['lama_kegiatan'] }} Tahun</div>
                                        </td>
                                        <td class="p-2">
                                            <div class="text-center">{{ $d['tahun_pelaksanaan'] }}</div>
                                        </td>
                                        <td class="p-2">
                                            <div class="text-center"><a href="/dashboard/sdm/{{ $d['id'] }}">Detail</a></div>
                                        </td>
                                        <td class="p-2">
                                            @foreach ($d['bidang_keilmuan'] as $a )
                                                
                                            @endforeach
                                            <div class="text-center">{{ $a }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody> --}}
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>