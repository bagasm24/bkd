<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Dashboard actions -->
        {{-- <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Publikasi</h1>
            </div>

            <!-- Right: Actions -->
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">

                <!-- Filter button -->
                <x-dropdown-filter align="right" />

                <!-- Datepicker built with flatpickr -->
                <x-datepicker />

                <!-- Add view button -->
                <button class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">
                    <svg class="fill-current shrink-0 xs:hidden" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                  </svg>
                  <span class="max-xs:sr-only">Add View</span>
                </button>
                
            </div>

        </div> --}}
        
        <!-- Cards -->
        <div class="grid grid-cols-12 gap-6">
            <!-- Table (Top Channels) -->
            <div class="col-span-full xl:col-span-full bg-white dark:bg-gray-800 shadow-sm rounded-xl">
                <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                    <h2 class="font-semibold text-gray-800 dark:text-gray-100">Pendidikan</h2>
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
                                        <div class="font-semibold text-left">Id Kategori Kegiatan</div>
                                    </th>
                                    <th class="p-2">
                                        <div class="font-semibold text-center">Klaim BKD</div>
                                    </th>
                                    <th class="p-2">
                                        <div class="font-semibold text-center">Judul</div>
                                    </th>
                                    <th class="p-2">
                                        <div class="font-semibold text-center">Jenis Publikasi</div>
                                    </th>
                                    <th class="p-2">
                                        <div class="font-semibold text-center">Kategori Kegiatan</div>
                                    </th>
                                    <th class="p-2">
                                        <div class="font-semibold text-center">Asal Data</div>
                                    </th>
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
                                            <div class="">{{ $d['id_kategori_kegiatan'] }}</div>
                                        </td>
                                        <td class="p-2">
                                            <div class="text-center">{{ $d['a_klaim_bkd'] }}</div>
                                        </td>
                                        <td class="p-2">
                                            <div class="">{{ $d['judul'] }}</div>
                                        </td>
                                        <td class="p-2">
                                            <div class="">{{ $d['jenis_publikasi'] }}</div>
                                        </td>
                                        <td class="p-2">
                                            <div class="">{{ $d['kategori_kegiatan'] }}</div>
                                        </td>
                                        <td class="p-2">
                                            <div class="">{{ $d['asal_data'] }}</div>
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