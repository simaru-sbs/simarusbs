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

   
    public function buatSuratKeluar(Request $request)
    {
        $lokasis = Lokasi::where('id', '>' ,1)->get();
        
        $this->validate($request, [
            'kepada' => 'required|max:255',
            'nik' => 'required|max:50',
            'jabatan' => 'required',
            'perihal' => 'required',
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
