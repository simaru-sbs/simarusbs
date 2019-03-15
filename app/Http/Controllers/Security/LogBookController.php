<?php
namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RegisterController;
use App\LogMasuk;
use Illuminate\Http\Request;

class LogBookController extends Controller
{
    public function indexLogBook(){
        return view('Security/indexLogBook');

    }

    public function lihatLogBook(Request $request){
        $logs = LogMasuk::where([
           'idLokasi' => auth('user')->user()->idLokasi,
            'statusLog' => 1,
        ])->get();

        $raw = array();

        foreach ($logs as $log) {
            if (LogBookController::checkTimes(substr($request->tanggal, 0, 10), substr($request->tanggal, 13, 10), $log->tanggalMasuk)) {
                array_push($raw, $log);
            };
        }

        if (!$raw) {
            return redirect()->route('get-securityIndexLogBook')
                ->with([
                    'status' => 'warning',
                    'message' => 'Tidak terdapat data log pada rentang tanggal tersebut!'
                ])->withInput();
        }

        $logs = collect($raw)->sortBy('tanggal');

        return view('Security/logBook',compact('logs'));
    }

        public function logMelebihiWaktu(Request $request){
        $logs = LogMasuk::where([
           'idLokasi' => auth('user')->user()->idLokasi,
            'statusLog' => 2,
        ])->get();

        $raw = array();

        foreach ($logs as $log) {
            if (LogBookController::checkTimes(substr($request->tanggal, 0, 10), substr($request->tanggal, 13, 10), $log->tanggalMasuk)) {
                array_push($raw, $log);
            };
        }

        return view('Security/logBelumTerselesaikan',compact('logs'));
    }


     
    public function keluarTervalidasi(Request $request){
        $logs = LogMasuk::where([
           'idLokasi' => auth('user')->user()->idLokasi,
            'statusLog' => 1,
            'tanggalKeluar' => date('Y-m-d'),
        ])->get();

        date_default_timezone_set('Asia/Jakarta');
        $raw = array();

        return view('Security/logBook',compact('logs'));
    }

    public function checkTimes($tanggalMulai, $tanggalBerakhir, $tanggalSekarang)
    {
        $mulai = strtotime($tanggalMulai);
        $berakhir = strtotime($tanggalBerakhir);
        $sekarang = strtotime($tanggalSekarang);
        return (($sekarang >= $mulai) && ($sekarang <= $berakhir));
    }
}
