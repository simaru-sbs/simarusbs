<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SuratKeluarBarang extends Model
{
    protected $table = 'suratkeluarbarang';

    protected $fillable = [
        'nomorSurat',
        'kepada',
        'nik',
        'jabatan',
        'perihal',
        'validate',
        'statusSurat',
        'keterangan',
        'hari',
        'tanggal',
    ];

    public function lokasiSuratKeluar()
    {
        return $this->hasMany(LokasiSuratKeluar::class, 'idSuratkeluar', 'id');
    }

     public function barangKeluar()
    {
        return $this->hasMany(BarangKeluar::class, 'idSuratkeluar', 'id');
    }

    public function lampiranSuratKeluar(){
        return $this->hasOne(LampiranSuratKeluar::class,'idSuratkeluar', 'id');
    }

    public function logMasuk()
    {
        return $this->hasMany(LogMasuk::class, 'idSuratkeluar', 'id');
    }

    public function petugas()
    {
        return $this->hasMany(Petugas::class, 'idSuratkeluar', 'id');
    }

    public function waspang(){
        return $this->hasMany(Waspang::class, 'idSuratkeluar', 'id');
    }

    public function forward(){
        return $this->hasMany(Forwarding::class, 'idSuratkeluar', 'id');
    }
}
