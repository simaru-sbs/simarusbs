<?php

namespace App\Http\Controllers\PicTelkom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\SuratKeluarBarang;
use App\PicTelkom;
use App\PicMitra;
use App\UnitPerusahaan;
use App\Lokasi;
use App\LokasiSuratKeluar;
use App\LogSuratKeluar;
use App\BarangKeluar;
use App\LampiranSuratKeluar;
use Illuminate\Support\Facades\Storage;


class SuratKeluarBarangController extends Controller
{
    public function indexBuatSurat()
    {
        $lokasis = Lokasi::where('id', '>' ,1)->get();

        return view('picTelkom/buatSuratKeluar', compact( 'lokasis'));
    }

    public function indexLihatSuratKeluar()
    {
        $surats = SuratKeluarBarang::all();
        return view('picTelkom/lihatSuratKeluar', compact('surats'));
    }

      public function detailSuratKeluar($id)
    {
        $surat = SuratKeluarBarang::where([
            'id' => $id,
        ])->get()->first();

        if (!$surat) {
            return redirect()->route('get-indexLihatSuratKeluar')
                ->with([
                    'status' => 'warning',
                    'message' => 'Tidak terdapat surat dengan ID tersebut!'
                ]);
        }



        $arrayK1 = array();
        $arrayK2 = array();
        $arrayK3 = array();
        $arrayL1 = array();
        $arrayL2 = array();
        $arrayL3 = array();


        $max = $surat->barangKeluar->count();
        $selector = 0;
        $counter = 0;

        if ($max != 0) {
            for ($i = 0; $i < 10; $i++) {
                for ($k = 0; $k < 3; $k++) {
                    if ($selector == 0) {
                        array_push($arrayK1, $surat->barangKeluar[$counter++]->SuratKeluarBarang->namaBarang);
                        $selector++;
                    } else if ($selector == 1) {
                        array_push($arrayK2, $surat->barangKeluar[$counter++]->SuratKeluarBarang->namaBarang);
                        $selector++;
                    } else if ($selector == 2) {
                        array_push($arrayK3, $surat->barangKeluar[$counter++]->SuratKeluarBarang->namaBarang);
                        $selector = 0;
                    }
                    if ($counter == $max) {
                        break;
                    }
                }
                if ($counter == $max) {
                    break;
                }
            }
        }

        $selector = 0;

        foreach ($surat->LokasiSuratKeluar as $lokasi) {
            if ($selector == 0) {
                array_push($arrayL1, $lokasi->lokasi->lokasi);
                $selector++;
            } else if ($selector == 1) {
                array_push($arrayL2, $lokasi->lokasi->lokasi);
                $selector++;
            } else {
                array_push($arrayL3, $lokasi->lokasi->lokasi);
                $selector = 0;
            }
        }

      
        $tanggal = SuratKeluarBarangController::formatTanggalIndo(substr($surat->tanggal,0,10));
        $tahun = substr($surat->nomorSurat, -4);
        $angka = str_replace($tahun, '', $surat->nomorSurat);
        return view('PicTelkom/detailSuratKeluar', compact('surat', 'angka', 'tahun','tanggal', 'arrayL1', 'arrayL2', 'arrayL3','arrayK1', 'arrayK2', 'arrayK3'));

    }

   
    public function buatSuratKeluar(Request $request)
    {
        $lokasis = Lokasi::where('id', '>' ,1)->get();
        
        $this->validate($request, [
            'kepada' => 'required|max:255',
            'nik' => 'required|max:50',
            'jabatan' => 'required',
            'perihal' => 'required',
            'hari' => 'required',
            'tanggal' => 'required',
            'lokasi' => 'required',
            'lampiransuratkeluar' => 'bail|mimes:pdf|max:2000',
            'namaLampiran' => 'required',
        ], [
            'required' => ':Attribute tidak boleh kosong !',
            'mimes' => ':Attribute gagal diunggah, lampiran harus berupa PDF',
            'max' => 'Ukuran maksimum :attribute sebesar 2 Megabyte',

    
        ]);
        
         if (!$request->inputNamaBarang) {
                return redirect()->back()->withInput()
                    ->with([
                        'status' => 'warning',
                        'message' => 'Nama Barang tidak boleh kosong!'
                    ]);
            }
        
         if (!$request->inputMerek) {
                return redirect()->back()->withInput()
                    ->with([
                        'status' => 'warning',
                        'message' => 'Merek Barang tidak boleh kosong!'
                    ]);
            }
        
          if (!$request->inputSerialNumber) {
                return redirect()->back()->withInput()
                    ->with([
                        'status' => 'warning',
                        'message' => 'Serial Number Barang tidak boleh kosong!'
                    ]);
            }


        date_default_timezone_set('Asia/Jakarta');

        if (SuratKeluarBarang::all()->last()){
            $nomorSurat = SuratKeluarBarang::all()->last()->nomorSurat;
            $tahunSurat = substr($nomorSurat, -4);
            $jumlahSurat = str_replace($tahunSurat, '', $nomorSurat);

            if ($tahunSurat == date('Y')) {
                $surat = SuratKeluarBarang::create([
                    'nomorSurat' => ($jumlahSurat + 1) . date('Y'),
                    'kepada' => strtoupper($request->kepada),
                    'nik' => $request->nik,
                    'jabatan' => ucwords($request->jabatan),
                    'perihal' => ucwords($request->perihal),
                    'validate' => 0,
                    'statusSurat' => 0,
                    'keterangan' => ($request->keterangan ? $request->keterangan : '-' ),
                    'hari' => ucwords($request->hari),
                    'tanggal' => $request->tanggal,
                ]);
            } else {
                $surat = SuratKeluarBarang::create([
                    'nomorSurat' => '1' . date('Y'),
                    'kepada' => strtoupper($request->kepada),
                    'nik' => $request->nik,
                    'perihal' => ucwords($request->perihal),
                    'jabatan' => ucwords($request->jabatan),
                    'validate' => 0,
                    'statusSurat' => 0,
                    'keterangan' => ($request->keterangan ? $request->keterangan : '-' ),
                     'hari' => ucwords($request->hari),
                    'tanggal' => $request->tanggal,
                ]);
            }
         } else {
            $surat = SuratKeluarBarang::create([
                'nomorSurat' => '1' . date('Y'),
                'kepada' => strtoupper($request->kepada),
                'nik' => $request->nik,
                'perihal' => ucwords($request->perihal),
                'jabatan' => ucwords($request->jabatan),
                'validate' => 0,
                'statusSurat' => 0,
                'keterangan' => ($request->keterangan ? $request->keterangan : '-' ),
                 'hari' => ucwords($request->hari),
                    'tanggal' => $request->tanggal,
            ]);



        }

        $path = Storage::putFile(
            'lampiransuratkeluar', $request->file('lampiransuratkeluar')
        );

        LampiranSuratKeluar::create([
            'path' => $path,
            'pathUri' => substr($path, 9),
            'namaFile' => strtoupper($request->namaLampiran),
            'idSuratKeluar' => $surat->id
        ]);

            foreach ($request->lokasi as $loc) {
            LokasiSuratKeluar::create([
                'idLokasi' => $loc,
                'idSuratKeluar' => $surat->id,
            ]);
        }


       if ($request->inputNamaBarang) {
                foreach ($request->inputNamaBarang as $index => $namaBarang) {
                    if ($request->inputMerek[$index] && $request->SerialNumber[$index]) {
                        $BarangKeluar = BarangKeluar::create([
                            'idSuratKeluar' => $surat->id,
                            'namaBarang' => $namaBarang,
                            'merek' => $request->inputMerek[$index],
                            'serialNumber' => $request->inputSerialNumber[$index],
                     

                        ]);
                    } else {
                        $BarangKeluar = BarangKeluar::create([
                            'idSuratKeluar' => $surat->id,
                           'namaBarang' => $namaBarang,
                            'merek' => $request->inputMerek[$index],
                            'serialNumber' => $request->inputSerialNumber[$index],
                        ]);
                    }
                    }
            }

     


        $nomorSurat = $surat->nomorSurat;
        $tahunSurat = substr($nomorSurat, -4);
        $jumlahSurat = str_replace($tahunSurat, '', $nomorSurat);

        return redirect()->route('get-picTelkombuatSuratKeluar')
            ->with([
                'status' => 'success',
                'message' => 'Surat Keluar Barang  Tel. ' . $jumlahSurat . '/Surat Keluar Barang/SBS/' . $tahunSurat . ' berhasil dibuat !'
            ]);
    }

    public function checkTimes($tanggalMulai, $tanggalBerakhir, $tanggalSekarang)
    {

        $mulai = strtotime($tanggalMulai);
        $berakhir = strtotime($tanggalBerakhir);
        $sekarang = strtotime($tanggalSekarang);

        return (($sekarang >= $mulai) && ($sekarang <= $berakhir));
    }


    function formatTanggalIndo($tanggal)
    {
        $bulan = array(
            1 => 'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        );
        $split = explode('-', $tanggal);
        return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
    }
}
