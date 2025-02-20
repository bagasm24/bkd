<x-app-layout>
  <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <form id="form-cek-bkd">
      @csrf
      <div class="flex gap-2">
        <div class="w-48 flex-none">
          <label for="tahunAjaran" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Tahun Ajaran</label>
          <select id="tahunAjaran" name="tahunAjaran" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            @foreach($tahunAjaran as $t)
              <option value="{{ $t->tahun }}">{{ $t->semester }}</option>
            @endforeach
          </select>
        </div>
        <div class="flex-1">
          <label for="dosen" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Dosen</label>
          <select id="dosen" name="dosen" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            @foreach($dataDosen as $d)
              <option value="{{ $d->id_sdm }}">{{ $d->nama_sdm }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-xs px-3 py-1.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 mt-2">
        Cek Laporan BKD
      </button>
    </form>

    <!-- Tempat untuk menampilkan hasil -->
    <div class="">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Hasil Laporan BKD</h3>
      <div id="hasil-container"></div>
    </div>
  </div>

  <!-- AJAX -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
      $("#form-cek-bkd").submit(function(event) {
        event.preventDefault(); // Mencegah form submit biasa

        let tahunAjaran = $("#tahunAjaran").val();
        let dosen = $("#dosen").val();
        let token = $('input[name="_token"]').val(); // CSRF Token

        function formatNumber(number) {
            // Konversi ke angka
            number = Number(number);

            // Cek apakah konversi berhasil (bukan NaN)
            if (isNaN(number)) {
                return '0'; // Atau kembalikan nilai default yang diinginkan
            }

            // Hapus desimal jika bilangan bulat
            if (Math.floor(number) === number) {
                return number.toFixed(0);
            }

            // Hapus nol di belakang desimal
            return number.toFixed(4).replace(/0+$/, '').replace(/\.$/, '');
        }

        $.ajax({
          url: "{{ route('cekBKD') }}",
          method: "POST",
          data: {
            _token: token,
            tahunAjaran: tahunAjaran,
            dosen: dosen
          },
          beforeSend: function() {
            $("#hasil-container").html('<div class="flex items-center justify-center w-24 h-24 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700"><div role="status"><svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/><path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/></svg><span class="sr-only">Loading...</span></div></div>');
          },
          success: function(response) {
            if (response.length === 0) {
              $("#hasil-container").html('<p class="text-red-500">Tidak ada data BKD untuk dosen ini.</p>');
              return;
            }
            let table = `
            <div class="">
              <table class="border-1 w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase dark:text-gray-400">
                  <tr class="border-b text-center">
                    <th class="px-4 py-2"></th>
                    <th scope="col" class="bg-gray-50 dark:bg-gray-800 px-4 py-2">Kinerja</th>
                    <th scope="col" class="px-4 py-2">Lebih</th>
                  </tr>
                </thead>
                <tbody class="text-center">`;

            response.forEach((bkd, index) => {
              table += `
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th scope="row" class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap bg-gray-50 dark:text-white dark:bg-gray-800">
                        Ajar
                    </th>
                    <td class="bg-gray-50 dark:bg-gray-800 px-4 py-2">
                      ${formatNumber(bkd.sks_kinerja_ajar)}
                    </td>
                    <td class="px-4 py-2">
                      ${formatNumber(bkd.sks_lebih_ajar)}
                    </td>
                </tr>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th scope="row" class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap bg-gray-50 dark:text-white dark:bg-gray-800">
                        Didik
                    </th>
                    <td class="bg-gray-50 dark:bg-gray-800 px-4 py-2">
                        ${formatNumber(bkd.sks_kinerja_didik)}
                    </td>
                    <td class="px-4 py-2">
                        ${formatNumber(bkd.sks_lebih_didik)}
                    </td>
                </tr>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th scope="row" class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap bg-gray-50 dark:text-white dark:bg-gray-800">
                        LIT
                    </th>
                    <td class="bg-gray-50 dark:bg-gray-800 px-4 py-2">
                        ${formatNumber(bkd.sks_kinerja_lit)}
                    </td>
                    <td class="px-4 py-2">
                        ${formatNumber(bkd.sks_lebih_lit)}
                    </td>
                </tr>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th scope="row" class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap bg-gray-50 dark:text-white dark:bg-gray-800">
                        PengMas
                    </th>
                    <td class="bg-gray-50 dark:bg-gray-800 px-4 py-2">
                        ${formatNumber(bkd.sks_kinerja_pengmas)}
                    </td>
                    <td class="px-4 py-2">
                        ${formatNumber(bkd.sks_lebih_pengmas)}
                    </td>
                </tr>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th scope="row" class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap bg-gray-50 dark:text-white dark:bg-gray-800">
                        Penunjang
                    </th>
                    <td class="bg-gray-50 dark:bg-gray-800 px-4 py-2">
                        ${formatNumber(bkd.sks_kinerja_penunjang)}
                    </td>
                    <td class="px-4 py-2">
                        ${formatNumber(bkd.sks_lebih_tunjang)}
                    </td>
                </tr>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th scope="row" class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap bg-gray-50 dark:text-white dark:bg-gray-800">
                        SKS
                    </th>
                    <td class="bg-gray-50 dark:bg-gray-800 px-4 py-2">
                        ${formatNumber(bkd.sks_kinerja)}
                    </td>
                    <td class="px-4 py-2">
                        ${formatNumber(bkd.sks_lebih)}
                    </td>
                </tr>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th scope="row" class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap bg-gray-50 dark:text-white dark:bg-gray-800">
                        Kewajiban
                    </th>
                    <td class="bg-gray-50 dark:bg-gray-800 px-4 py-2" colspan="2">
                        ${bkd.stat_kewajiban}
                    </td>
                </tr>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th scope="row" class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap bg-gray-50 dark:text-white dark:bg-gray-800">
                        Tugas
                    </th>
                    <td class="bg-gray-50 dark:bg-gray-800 px-4 py-2" colspan="2">
                        ${bkd.stat_tugas}
                    </td>
                </tr>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th scope="row" class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap bg-gray-50 dark:text-white dark:bg-gray-800">
                        Belajar
                    </th>
                    <td class="bg-gray-50 dark:bg-gray-800 px-4 py-2" colspan="2">
                        ${bkd.stat_belajar}
                    </td>
                </tr>
                <tr class="border-b border-gray-200 dark:border-gray-700 font-semibold">
                    <th scope="row" class="bg-gray-50 dark:bg-gray-800 px-4 py-2 dark:text-white dark:bg-gray-800 text-gray-900">
                        Simpulan Asesor
                    </th>
                    <td class="bg-gray-50 dark:bg-gray-800 px-4 py-2" colspan="2">
                        ${bkd.simpulan_asesor}
                    </td>
                </tr>
                `;
            });

            table += `</tbody></table></div>`;
            $("#hasil-container").html(table);
          },
          error: function(xhr) {
            $("#hasil-container").html('<p class="text-red-500">Terjadi kesalahan saat mengambil data.</p>');
          }
        });
      });
    });
  </script>
</x-app-layout>
