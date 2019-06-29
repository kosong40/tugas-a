@extends('layout.v2.desa')
@section('title','Formulir')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('desa-home')}}">Beranda</a></li>
    <li class="breadcrumb-item active" aria-current="page">Formulir</li>
@endsection
@section('content')
    <div class="card-group">
        @foreach ($pelayanan as $pelayanan)
        <div class="card">
                <div class="card-header">
                    Pelayanan
                </div>
                <div class="card-body">
                <center>
                    <h4 class="card-title">{{$pelayanan->pelayanan}}</h4>
                    <br><br>
                    <a href="#{{$pelayanan->slug}}" data-toggle="modal" class="btn btn-info btn-sm">Informasi</a>
                    <a href="{{route('formPelayanan-desa',[$pelayanan->slug])}}" class="btn btn-success btn-sm">Formulir</a>
                </center>
                </div>
            </div>
            <div id="{{$pelayanan->slug}}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="modal-title">Informasi {{$pelayanan->pelayanan}}</h4><br>
                                {!!$pelayanan->keterangan!!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection