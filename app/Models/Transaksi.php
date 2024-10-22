<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DetilTransaksi;

class Transaksi extends Model
{
    use HasFactory;
    protected $fillable =['kode','total','status'];
    public function detilTransaksi(){
        return $this->hasMany(DetilTransaksi::class);
    }
}
