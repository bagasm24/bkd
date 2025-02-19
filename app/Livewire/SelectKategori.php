<?php

namespace App\Livewire;

use App\Models\KategoriKegiatan;
use Livewire\Component;

class SelectKategori extends Component
{
    public $selectedParent = null;
    public $selectedSub = null;
    public $parents = [];
    public $subKategori = [];

    public function mount()
    {
        $this->parents = KategoriKegiatan::whereNull('parent_id')->get();
    }

    public function updatedSelectedParent($value)
    {
        $this->subKategori = KategoriKegiatan::where('parent_id', $value)->get();
        $this->selectedSub = null;
    }

    public function render()
    {
        return view('livewire.select-kategori');
    }
}
