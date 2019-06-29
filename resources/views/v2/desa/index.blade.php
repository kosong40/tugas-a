@extends('layout.v2.desa')
@section('title','Dashboard '.session('username'))
@section('content')
@php
switch(date("m")){
case '01':$bulan = "Januari";break;
case '02':$bulan = "Februari";break;
case '03':$bulan = "Maret";break;
case '04':$bulan = "April";break;
case '05':$bulan = "Mei";break;
case '06':$bulan = "Juni";break;
case '07':$bulan = "Juli";break;
case '08':$bulan = "Agustus";break;
case '09':$bulan = "Spetember";break;
case '10':$bulan = "Oktober";break;
case '11':$bulan = "November";break;
case '12':$bulan = "Desember";break;
}
switch (date("l")) {
case 'Wednesday':$hari = "Rabu";break;
case 'Monday':$hari = "Senin";break;
case 'Tuesday':$hari = "Selasa";break;
case 'Thursday':$hari = "Kamis";break;
case 'Friday':$hari = "Jumat";break;
case 'Saturday':$hari = "Sabtu";break;
case 'Sunday':$hari = "Minggu";break;
default:$hari = date("l");break;
}
@endphp
<div class="card-group">
    <!-- card group -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="m-r-10">
                    <span class="btn btn-circle btn-lg bg-danger">
                        <i class="icon-Calendar text-white"></i>
                    </span>
                </div>
                <div>
                    {{$hari .", ".date('d')." ". $bulan ." ".date('Y')." "}}<br><span class=" small-box-footer"
                        id="txt"></span>
                </div>
                <div class="ml-auto">
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="m-r-10">
                    <span class="btn btn-circle btn-lg bg-info">
                        <i class="icon-File text-white"></i>
                    </span>
                </div>
                <div>
                    Total Permohonan
                </div>
                <div class="ml-auto">
                    <h2 class="m-b-0 font-light">{{$totalPemohon}}</h2>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
    function startTime() {
            var today = new Date();
            var h = today.getHours();
            var m = today.getMinutes();
            var s = today.getSeconds();
            m = checkTime(m);
            s = checkTime(s);
            document.getElementById('txt').innerHTML =
            h + ":" + m + ":" + s;
            var t = setTimeout(startTime, 500);
        }
        function checkTime(i) {
            if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
            return i;
        }
</script>
@endsection
@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">Beranda</li>
@endsection