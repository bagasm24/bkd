<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="grid grid-cols-12 gap-6">
            <!-- Table (Top Channels) -->
            <div class="col-span-full xl:col-span-full bg-white dark:bg-gray-800 shadow-sm rounded-xl">
                <header class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                    <div class="">
                        <h2 class="font-semibold text-gray-800 dark:text-gray-100">Data User BKD</h2>
                    </div>
                    {{-- <form method="POST" action="{{ route('user.create') }}">
                        @csrf
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                            Buat User Baru
                        </button>
                    </form> --}}
                </header>
                <div class="p-3">
                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table id="pagination-table">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="font-semibold text-left">Nama</div>
                                    </th>
                                    <th>
                                        <div class="font-semibold text-center">Role</div>
                                    </th>
                                </tr>
                            </thead>
                            <!-- Table body -->
                            <tbody>
                                <!-- Row -->
                                @foreach ($dataUser as $d )
                                    <tr>
                                        <td>
                                            <div class="">{{ $d['name'] }}</div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                @if($d['role'] == 3)
                                                    Dosen
                                                @elseif($d['role'] == 2)
                                                    Wakil Rektor 1
                                                @elseif($d['role'] == 1)
                                                    Admin
                                                @else
                                                    Role tidak dikenal
                                                @endif
                                            </div>
                                        </td>
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