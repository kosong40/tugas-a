<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pemohon extends Model
{
    protected $guarded = ['id'];

    public static function getPemohonToday(){
        return DB::table("pemohons")->whereDate('created_at', DB::raw('CURDATE()'))->get();
    }
    public static function getDetailPemohon1($slug,$kode){
        return DB::table("$slug")
            ->join('pemohons', 'pemohons.id', '=', "$slug.id_pemohon")
            ->join('daerahs', 'daerahs.id', '=', 'pemohons.daerah_id')
            ->join('pelayanans', 'pelayanans.id', '=', 'pemohons.pelayanan_id')
            ->where('pemohons.kode', "$kode")
            ->first();
    }
    public static function getIDPemohon($slug,$kode){
        return DB::table("pemohons")
        ->join("$slug", "$slug.id_pemohon", '=', 'pemohons.id')
        ->where('pemohons.kode', "$kode")
        ->first();
    }
    public static function getDetailPemohon2($slug,$kode){
        return DB::table("$slug")
        ->join('pemohons', 'pemohons.id', '=', "$slug.id_pemohon")
        ->join('daerahs', 'daerahs.id', '=', 'pemohons.daerah_id')
        ->join('pelayanans', 'pelayanans.id', '=', 'pemohons.pelayanan_id')
        ->join('sublayanans', 'sublayanans.id', '=', 'pemohons.sublayanan_id')
        ->where('pemohons.kode', "$kode")
        ->first();
    }
    public static function addPemohon1($data = array())
    {
        return Pemohon::create($data);
    }
}
