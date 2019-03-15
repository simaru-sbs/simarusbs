<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    protected $table = 'lokasi';

    protected $fillable = [
        'lokasi',
    ];

    public function lokasiKerja(){
        return $this->hasMany(LokasiKerja::class, 'idLokasi','id');
    }

    public function lokasiBarangKeluar(){
        return $this->hasMany(LokasiBarangKeluar::class, 'idLokasi','id');
    }

    public function logMasuk(){
        return $this->hasMany(LogMasuk::class,'idLokasi','id');
    }

    public function user(){
        return $this->hasMany(User::class,'idLokasi','id');
    }
}
