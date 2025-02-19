<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-full xl:col-span-full bg-white dark:bg-gray-800 shadow-sm rounded-xl">
                <header class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                    <h2 class="font-semibold text-gray-800 dark:text-gray-100">Tambah Publikasi Manual</h2>
                </header>
                <form method="POST"action="{{ route('savePublikasi') }}" class="mx-auto p-4">
                    @csrf
                    <div class="relative z-0 w-full mb-5 group">
                        <input type="text" name="judul" id="judul" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required />
                        <label for="judul" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Judul</label>
                    </div>
                    <div class="relative z-0 w-full mb-5 group">
                        <input type="text" name="nama_jurnal" id="nama_jurnal" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required />
                        <label for="nama_jurnal" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Nama Jurnal</label>
                    </div>
                    <div class="relative z-0 w-full mb-5 group">
                        <input type="text" name="penerbit" id="penerbit" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required />
                        <label for="penerbit" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Penerbit</label>
                    </div>
                    <div class="grid md:grid-cols-3 md:gap-6">
                        <div class="relative z-0 w-full mb-5 group">
                            <input type="date" name="tanggal" id="tanggal" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required />
                            <label for="tanggal" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Tanggal Publikasi</label>
                        </div>
                        <div class="relative z-0 w-full mb-5 group">
                            <label for="jenis_publikasi" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6 px-1">Jenis Publikasi</label>
                            <select name="jenis_publikasi" id="jenis_publikasi" class="block py-2.5 px-1 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required>
                                @foreach ($jenisPublikasi as $jp)
                                    <option value="{{ $jp['id'] }}">{{ $jp['nama'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="relative z-0 w-full mb-5 group">
                            <label for="urutan_penulis" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6 px-1">Urutan Penulis</label>
                            <select name="urutan_penulis" id="urutan_penulis" class="block py-2.5 px-1 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required>
                                    <option value=1>1</option>
                                    <option value=2>2</option>
                                    <option value=3>3</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-2 md:gap-6">
                        <div class="relative z-0 w-full group">
                            <label for="kategori_publikasi" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6 px-1">Pilih Kategori Kegiatan</label>
                            <select name="kategori_publikasi" id="kategori_publikasi" class="block py-2.5 px-1 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategori  as $kat)
                                    <option value="{{ $kat['id'] }}">{{ $kat['nama'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="subkategori" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6 px-1">Pilih Sub Kategori</label>
                            <select name="subpublikasi" id="subkategori" class="block py-2.5 px-1 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required>
                                    <option value="">-- Pilih Sub Kategori --</option>
                            </select>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="sub2kategori" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6 px-1">Pilih Sub-Sub Kategori</label>
                            <select name="sub2publikasi" id="sub2kategori" class="block mb-2 py-2.5 px-1 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required>
                                    <option value="">-- Pilih Sub Sub Kategori --</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-4 py-1.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit</button>
                </form>

            </div>    
        </div>
    </div>
</x-app-layout>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
        $(document).ready(function () {
        $('#subkategori').hide();
        $('label[for="subkategori"]').hide();
        $('#sub2kategori').hide();
        $('label[for="sub2kategori"]').hide();
    $('#kategori_publikasi').change(function () {
        var kategoriId = $(this).val();

        if (kategoriId) {
            $.ajax({
                url: '/get-subkategori/' + kategoriId,
                type: 'GET',
                success: function (data) {
                    $('#subkategori').empty();
                    $('#subkategori').append('<option value="">-- Pilih Sub Kategori --</option>');
                    $.each(data, function (key, value) {
                        $('#subkategori').append('<option value="' + value.id + '">' + value.nama + '</option>');
                    });
                    $('#subkategori').show();
                    $('label[for="subkategori"]').show();
                },
                error: function (xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        } else {
            $('#subkategori').empty();
            $('#subkategori').append('<option value="">-- Pilih Subkategori --</option>');
            $('#subkategori').hide();
            $('label[for="subkategori"]').hide();
        }
    });
    $('#subkategori').change(function () {
        var kategoriId = $(this).val();

        if (kategoriId) {
            $.ajax({
                url: '/get-sub2kategori/' + kategoriId,
                type: 'GET',
                success: function (data) {
                    if (data.length > 0) {
                    $('#sub2kategori').empty();
                    $('#sub2kategori').append('<option value="">-- Pilih Sub Sub Kategori --</option>');
                    $.each(data, function (key, value) {
                        $('#sub2kategori').append('<option value="' + value.id + '">' + value.nama + '</option>');
                    });
                    $('#sub2kategori').show().attr('required', true);
                    $('label[for="sub2kategori"]').show();
                } else {
                    $('#sub2kategori').empty();
                    $('#sub2kategori').append('<option value="">-- Pilih Sub Sub Kategori --</option>');
                    $('#sub2kategori').hide().removeAttr('required');
                    $('label[for="sub2kategori"]').hide();
                }
                },
                error: function (xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        } else {
            $('#sub2kategori').empty();
            $('#sub2kategori').append('<option value="">-- Pilih Sub Sub Kategori --</option>');
            $('#sub2kategori').hide();
            $('label[for="sub2kategori"]').hide();
        }
    });
});
</script>
