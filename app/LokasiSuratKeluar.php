<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LokasiBarangKeluar extends Model
{
    protected $table = 'lokasibarangkeluar';

    protected $fillable = [
        'idLokasi', 'idBarangKeluar',
    ];

    public function SuratKeluar(){
        return $this->belongsTo(SuratKeluarBarang::class, 'idSuratKeluar','id');
    }

    public function lokasi(){
        return $this->belongsTo(Lokasi::class, 'idLokasi','id');
    }
}
