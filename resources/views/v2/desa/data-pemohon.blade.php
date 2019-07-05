@extends('layout.v2.desa')
@section('title','Data Pemohon')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{route('desa-home')}}">Beranda</a></li>
<li class="breadcrumb-item active" aria-current="page">Data Pemohon</li>
@endsection
@section('content')
<div class="row">
    @foreach ($pelayanan as $pelayanan)
    <div class="col-sm-12 col-lg-3">
        <div class="card bg-light">
            <div class="card-body">
                <h5 class="card-title text-center">{{$pelayanan->pelayanan}}</h5>
                <table class="table no-border mini-table m-t-20">
                    <tbody>
                        <tr>
                            <td class="text-muted">Total</td>
                            <td class="font-medium">{{count($pemohon->where('pelayanan_id',$pelayanan->id))}}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Siap Dicetak</td>
                            <td class="font-medium">
                                {{count($pemohon->where('pelayanan_id',$pelayanan->id)->where('status','Setuju'))}}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Sudah ada No SK</td>
                            <td class="font-medium">
                                {{count($pemohon->where('pelayanan_id',$pelayanan->id)->where('status','Sudah ada nomor SK'))}}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Belum ada No SK</td>
                            <td class="font-medium">
                                {{count($pemohon->where('pelayanan_id',$pelayanan->id)->where('status','Belum'))}}</td>
                        </tr>
                    </tbody>
                </table>
                <p align="center"><a class="btn btn-info"
                        href="{{url('desa/v2/data-pemohon/'.$pelayanan->slug)}}">Detail <i
                            class="ti-arrow-right"></i></a></p>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection