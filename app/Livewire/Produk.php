<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Produk as ModelProduk;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\Produk as ImportProduk;

class Produk extends Component
{
    use WithFileUploads;
    public $pilihanMenu = 'lihat';
    public $kode;
    public $nama;
    public $harga;
    public $stok;
    public $produkTerpilih;
    public $fileExcel;

    public function importExcel(){
        Excel::import(new ImportProduk, $this->fileExcel);
        $this->reset();
    }

    public function simpanEdit(){
        $this->validate([
            'nama' => 'required',
            'kode' => ['required','unique:produks,kode,'.$this->produkTerpilih->id],
            'harga' => 'required',
            'stok' => 'required',
    ],[
        'nama.required' => 'Nama harus diisi',
        'kode.required' => 'Kode harus diisi',
        'kode.unique' => 'Kode telah digunakan',
        'harga.unique' => 'Harga sudah digunakan',
        'stok.required' => 'Stok harus diisi',
    ]);
    $simpan = $this->produkTerpilih;
    $simpan->kode = $this->kode;
    $simpan->nama = $this->nama;
    $simpan->harga = $this->harga;
    $simpan->stok = $this->stok;
    $simpan->save();

    $this->reset(['kode','nama','harga','stok','produkTerpilih']);
    $this->pilihanMenu = 'lihat';

    }
    public function pilihEdit($id){
        $this->produkTerpilih = ModelProduk::findOrFail($id);
        $this->kode = $this->produkTerpilih->kode;
        $this->nama = $this->produkTerpilih->nama;
        $this->harga = $this->produkTerpilih->harga;
        $this->stok = $this->produkTerpilih->stok;
        $this->pilihanMenu = 'edit';
    }

    public function pilihHapus($id){
        $this->produkTerpilih = ModelProduk::findOrFail($id);
        $this->pilihanMenu = 'hapus';
    }
    public function batal(){
        $this->reset();
    }
    public function hapus(){
        $this->produkTerpilih->delete();
        $this->reset();
    }
    public function simpan(){
        $this->validate([
            'nama' => 'required',
            'kode' => ['required','unique:produks,kode'],
            'harga' => 'required',
            'stok' => 'required',
    ],[
        'nama.required' => 'Nama harus diisi',
        'kode.required' => 'Kode harus diisi',
        'kode.unique' => 'Kode telah digunakan',
        'harga.unique' => 'Harga sudah digunakan',
        'stok.required' => 'Stok harus diisi',
    ]);
    $simpan = new ModelProduk();
    $simpan->kode = $this->kode;
    $simpan->nama = $this->nama;
    $simpan->harga = $this->harga;
    $simpan->stok = $this->stok;
    $simpan->save();

    $this->reset(['kode','nama','harga','stok']);
    $this->pilihanMenu = 'lihat';

    }
    public function pilihMenu($menu){
        $this->pilihanMenu = $menu;
    }

    public function render()
    {
        return view('livewire.produk')->with([
            'semuaProduk' => ModelProduk::all()
        ]);
    }
}
