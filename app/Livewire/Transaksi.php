<?php

namespace App\Livewire;

use App\Models\DetilTransaksi;
use Livewire\Component;
use App\Models\Transaksi as ModelTransaksi;
use App\Models\Produk;

class Transaksi extends Component
{
    public $kode, $total, $bayar =0, $kembalian, $totalSemuaBelanja;
    public $transaksiAktif;

    public function transaksiSelesai(){
        $this->transaksiAktif->total = $this->totalSemuaBelanja;
        $this->transaksiAktif->status = 'selesai';
        $this->transaksiAktif->save();
        $this->reset();
    }

    public function hapusProduk($id){
        $detil = DetilTransaksi::find($id);
        $detil->delete();
    }

    public function transaksiBaru(){
        $this->reset();
        $this->transaksiAktif = new ModelTransaksi();
        $this->transaksiAktif->kode = 'INV/'.date('YmdHis');
        $this->transaksiAktif->total = 0;
        $this->transaksiAktif->status = 'pending';
        $this->transaksiAktif->save();
    }

    public function batalTransaksi(){
        if($this->transaksiAktif){
            $detilTransaksi = DetilTransaksi::where('transaksi_id', $this->transaksiAktif->id)->get();
            foreach ($detilTransaksi as $detil){
                $detil->delete;
            }
            $this->transaksiAktif->delete();
        }
        $this->reset();
    }


    public function updatedKode(){
        $produk = Produk::where('kode',$this->kode)->first();
        // dd($this->kode);
        if($produk && $produk->stok > 0){
            $detil = DetilTransaksi::firstOrNew([
                'transaksi_id' => $this->transaksiAktif->id,
                'produk_id' => $produk->id
            ],[
                'jumlah' => 0
            ]);
            $detil->jumlah += 1;
            $detil->save();
            $this->reset('kode');
        }
    }

    public function updatedBayar(){
        if($this->bayar > 0)
        $this->kembalian = $this->bayar - $this->totalSemuaBelanja;
    }

    public function render()
    {
        if($this->transaksiAktif){
            $semuaProduk = DetilTransaksi::where('transaksi_id', $this->transaksiAktif->id)->get();
            $this->totalSemuaBelanja = $semuaProduk->sum(function ($detil){
                return $detil->produk->harga * $detil->jumlah;
            });
        } else {
            $semuaProduk = [];
        }
            return view('livewire.transaksi')->with([
                'semuaProduk' => $semuaProduk
            ]);
    }
}
