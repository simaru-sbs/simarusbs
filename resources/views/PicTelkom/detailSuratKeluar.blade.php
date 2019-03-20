@extends('PicTelkom.Layouts.rootPage')

@section('extraStyleSheet')
    <style>
        .identintasSurat {
            width: 80px;
        }

        .badanSuratNumber {
            display: inline-block;
            width: 15px;
        }

        .listDetailPekerjaan {
            padding: 0% 0% 0% 6%;
            width: 180px;
        }

        .listDetailPeraturan {
            padding: 0% 0% 0% 10%;
            margin: 0px;
        }

        .tabListDetail {
            display: inline-block;
            width: 150px;
        }

        .tabListPetugas {
            padding-left: 24%;
        }
    </style>
@endsection


@section('content-header')
    <h1>
        Surat
        <small><b>Tel. {{$angka}}/SIMARU/SBS/{{$tahun}}</b></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{route('picTelkom-home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
    </ol>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li><strong> {{ $error }} </strong></li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(session('status'))
                <div class="alert alert-{{session('status')}}">
                    {{session('message')}}
                </div>
            @endif
        </div>
    </div>
    <section class="invoice">
        <div class="row">
            <div class="col-md-12 table-responsive">
                <h4>
                    <div class="pull-right">
                        <img src="{{url('img/TelkomAsset/logoSurat.png')}}" alt="TelkomLogo">
                    </div>
 
          <h1 style="text-align: center;">
                    SURAT KELUAR BARANG
                    </h1>
                 </h4>
                <hr style="margin: 5% 0px 5% 0px;border: 1px solid #000;">
            </div>
        </div>
        <div class="row invoice-info">
            <div class="col-md-12 table-responsive">
        
                    <tbody>
                     <tr>
                        <td style="vertical-align: top;" class="identintasSurat"> Kepada Yth.</td>
                     
                    
                    </tr>
                      <tr>
                        <p> Bagian Keamanan</p>
              
                    
                    </tr>
                      <tr>
                        <td style="vertical-align: top;" class="identintasSurat"> Di Tempat</td>
              
                    
                    </tr>

                     <tr>
                        <td style="vertical-align: top;" class="identintasSurat"> Surat ini adalah bukti resmi yang diberikan kepada</td>
                        <td style="vertical-align: top"> :</td>
                    
                    </tr>

                    <table>
                    <tr>
                        <td style="vertical-align: top;" class="identintasSurat"> Nama</td>
                        <td style="vertical-align: top"> :</td>
                        <td style="padding-left: 5px"> Sdr. {{strtoupper($surat->kepada)}}</td>
                    </tr>
                   
                    <tr>
                        <td style="vertical-align: top;" class="identintasSurat"> Jabatan</td>
                        <td style="vertical-align: top"> :</td>
                        <td style="padding-left: 5px"> {{$surat->jabatan}}</td>
                    </tr>

                    </table>

                      <tr>
                        <td style="vertical-align: top;" class="identintasSurat"> <br>Yang akan membawa KELUAR barang berupa</td>
                        <td style="vertical-align: top"> :</td>
                    
                    </tr>
                    </tbody>
            
            </div>
        </div>
       
           
        <div class="row no-print">
            <div class="col-xs-12">
                <a href="{{url()->previous()}}">
                    <button type="button" class="btn btn-success pull-right" style="margin-right: 5px;">
                        Kembali
                    </button>
                </a>
            </div>
        </div>
    </section>
@endsection