<div>
    {{-- Dropdown Parent --}}
    <label>Pilih Kategori:</label>
    <select wire:model="selectedParent" class="form-control">
        <option value="">-- Pilih Kategori --</option>
        @foreach ($parents as $parent)
            <option value="{{ $parent->id }}">{{ $parent->nama }}</option>
        @endforeach
    </select>

    {{-- Dropdown Sub Kategori --}}
    @if (!empty($subKategori))
        <label>Pilih Sub Kategori:</label>
        <select wire:model="selectedSub" class="form-control">
            <option value="">-- Pilih Sub Kategori --</option>
            @foreach ($subKategori as $sub)
                <option value="{{ $sub->id }}">{{ $sub->nama }}</option>
            @endforeach
        </select>
    @endif

    {{-- <pre>{{ var_export($subKategori, true) }}</pre> --}}
</div>
