<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Helpers\kustom;
use App\Admin;
use App\Daerah;
use App\Pelayanan;
use App\Sublayanan;
use App\Pemohon;
use App\imb;
use Datatables;

class DesaV2 extends Controller
{
    public function index()
    {
        $totalPemohon = DB::table('pemohons')->where('daerah_id', session('daerah'))->get();
        $data = [
            'totalPemohon' => count($totalPemohon)
        ];
        return view('v2/desa/index', $data);
    }
    public function formulir()
    {
        $pelayanan = Pelayanan::get();
        $data = [
            'pelayanan' => $pelayanan
        ];
        return view('v2/desa/formulir', $data);
    }
    public function formulirPelayanan($slug)
    {
        $pelayanan      = Pelayanan::where('slug', $slug)->first();
        $daerah         = Daerah::find(session('daerah'));
        $reklame        = DB::table('jenis-reklame')->get();

        $sublayanan     =   Sublayanan::get();
        $data =
            [
                'pelayanan' => $pelayanan,
                'daerah' => $daerah,
                'reklame' => $reklame,
                'sublayanan' => $sublayanan,
                'cek' => count(Sublayanan::where('id_pelayanan', $pelayanan->id)->get()),
            ];
        return view('v2/desa/formulir-pelayanan', $data);
    }
    public function formulirSublayanan($slug, $slug2)
    {
        $pelayanan      = Pelayanan::where('slug', $slug)->first();
        $daerah         = Daerah::find(session('daerah'));
        $reklame        = DB::table('jenis-reklame')->get();
        $sublayanan     = Sublayanan::where('slug', "$slug2")->first();
        $data =
            [
                'pelayanan' => $pelayanan,
                'daerah' => $daerah,
                'reklame' => $reklame,
                'sublayanan' => $sublayanan
            ];
        return view('v2/desa/formulir-sublayanan', $data);
    }


    public function formIMB(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'nama' => 'required', 'nik' => 'required|min:16|max:18', 'telepon' => 'required|numeric', 'pekerjaan' => 'required',
            'rt' => 'required', 'rw' => 'required', 'jalan' => 'required',
            'keperluan_bangunan' => 'required', 'konstruksi_bangunan' => 'required', 'letak_bangunan' => 'required', 'luas_bangunan' => 'required|numeric', 'luas_tanah' => 'required|numeric', 'tanah_milik' => 'required',
            'scan_ktp' => 'required | mimes:jpeg,jpg,png,PNG,pdf,txt | max:2048',
            'scan_persetujuan_tetangga' => 'required | mimes:jpeg,jpg,png,PNG,pdf,txt | max:2048',
            'scan_fc_kepemilikan_tanah' => 'required | mimes:jpeg,jpg,png,PNG,pdf,txt | max:2048',
            'scan_fc_sppt_pbb_terakhir' => 'required | mimes:jpeg,jpg,png,PNG,pdf,txt | max:2048',
            'scan_gambar_rencana' => 'required | mimes:jpeg,jpg,png,PNG,pdf,txt | max:2048',
            'scan_pengantar' => 'required | mimes:jpeg,jpg,png,PNG,pdf,txt | max:2048',
        ], Kustom::validasi());
        $pemohon = Pemohon::create([
            'nama'  =>  $request['nama'],
            'kode'  => Kustom::generateKode(6),
            'nik'   =>  $request['nik'],
            'telepon'   =>  $request['telepon'],
            'pekerjaan' =>  $request['pekerjaan'],
            'rt'    =>  $request['rt'],
            'rw'    =>  $request['rw'],
            'jalan' =>  $request['jalan'],
            'daerah_id'    =>  $request['daerah_id'],
            'pelayanan_id'  => $request['pelayanan_id'],
            'created_at'    =>  now(+7.00),
            'updated_at'   => null
        ]);
        $id_pemohon = $pemohon->id;

        $a  =   $request->file('scan_ktp');
        $b  =   $request->file('scan_persetujuan_tetangga');
        $c  =   $request->file('scan_fc_kepemilikan_tanah');
        $d  =   $request->file('scan_fc_sppt_pbb_terakhir');
        $e  =   $request->file('scan_gambar_rencana');
        $f  =   $request->file('scan_pengantar');
        $path_a =   "berkas/imb/a/";
        $nama_a =   $id_pemohon . "_ktp." . $a->getClientOriginalExtension();
        $request->file('scan_ktp')->move($path_a, $nama_a);
        $path_b =   "berkas/imb/b/";
        $nama_b =   $id_pemohon . "_scan_persetujuan_tetangga." . $b->getClientOriginalExtension();
        $request->file('scan_persetujuan_tetangga')->move($path_b, $nama_b);
        $path_c =   "berkas/imb/c/";
        $nama_c =   $id_pemohon . "_scan_fc_kepemilikan_tanah." . $c->getClientOriginalExtension();
        $request->file('scan_fc_kepemilikan_tanah')->move($path_c, $nama_c);
        $path_d =   "berkas/imb/d/";
        $nama_d =   $id_pemohon . "_scan_fc_sppt_pbb_terakhir." . $d->getClientOriginalExtension();
        $request->file('scan_fc_sppt_pbb_terakhir')->move($path_d, $nama_d);
        $path_e =   "berkas/imb/e/";
        $nama_e =   $id_pemohon . "_scan_gambar_rencana." . $e->getClientOriginalExtension();
        $request->file('scan_gambar_rencana')->move($path_e, $nama_e);
        $path_f =   "berkas/imb/f/";
        $nama_f =   $id_pemohon . "_scan_pengantar." . $f->getClientOriginalExtension();
        $request->file('scan_pengantar')->move($path_f, $nama_f);

        DB::table('izin-mendirikan-bangunan')->insert([
            'id_pemohon'                => $id_pemohon,
            'keperluan_bangunan'        => $request['keperluan_bangunan'],
            'konstruksi_bangunan'       => $request['konstruksi_bangunan'],
            'luas_bangunan'             => $request['luas_bangunan'],
            'luas_tanah'                => $request['luas_tanah'],
            'letak_bangunan'            => $request['letak_bangunan'],
            'tanah_milik'               => $request['tanah_milik'],
            'scan_ktp'                  => $path_a . $nama_a,
            'scan_persetujuan_tetangga' => $path_b . $nama_b,
            'scan_fc_kepemilikan_tanah' => $path_c . $nama_c,
            'scan_fc_sppt_pbb_terakhir' => $path_d . $nama_d,
            'scan_gambar_rencana'       => $path_e . $nama_e,
            'scan_pengantar'            => $path_f . $nama_f
        ]);
        return redirect()->back()->with('sukses', "Pengisian formulir berhasil, mohon untuk menunggu informasi lebih lanjut");
    }
    public function formIR(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'nama' => 'required', 'nik' => 'required|min:16|max:18', 'telepon' => 'required|numeric', 'pekerjaan' => 'required', 'rt' => 'required', 'rw' => 'required', 'jalan' => 'required',

            'id_reklame' => 'required|numeric', 'banyak' => 'required|numeric', 'pesan_produk' => 'required', 'tempat_reklame' => 'required', 'tanggal_awal' => 'required|before:tanggal_akhir', 'tanggal_akhir' => 'required|after:tanggal_awal',

            'scan_ktp' => 'required | mimes:jpeg,jpg,png,PNG,pdf,txt | max:2048',
            'scan_npwp' => 'required | mimes:jpeg,jpg,png,PNG,pdf,txt | max:2048',
            'contoh_reklame' => 'required | mimes:jpeg,jpg,png,PNG,pdf,txt | max:2048',
            'scan_persetujuan' => 'required | mimes:jpeg,jpg,png,PNG,pdf,txt | max:2048',
            'scan_izin_lama' => 'required | mimes:jpeg,jpg,png,PNG,pdf,txt | max:2048',
            'scan_pengantar' => 'required | mimes:jpeg,jpg,png,PNG,pdf,txt | max:2048',
        ], Kustom::validasi());

        $pemohon = Pemohon::create([
            'nama'  =>  $request['nama'],
            'kode'  => Kustom::generateKode(6),
            'nik'   =>  $request['nik'],
            'telepon'   =>  $request['telepon'],
            'pekerjaan' =>  $request['pekerjaan'],
            'rt'    =>  $request['rt'],
            'rw'    =>  $request['rw'],
            'jalan' =>  $request['jalan'],
            'daerah_id'    =>  $request['daerah_id'],
            'pelayanan_id'  => $request['pelayanan_id'],
            'created_at'    =>  now(+7.00),
            'updated_at'   => null
        ]);

        $id_pemohon = $pemohon->id;
        $a  =   $request->file('scan_ktp');
        $b  =   $request->file('scan_npwp');
        $c  =   $request->file('contoh_reklame');
        $d  =   $request->file('scan_persetujuan');
        $e  =   $request->file('scan_izin_lama');
        $f  =   $request->file('scan_pengantar');
        //scan ktp
        $path_a =   "berkas/reklame/a/";
        $nama_a =   $id_pemohon . "_ktp." . $a->getClientOriginalExtension();
        $request->file('scan_ktp')->move($path_a, $nama_a);
        //scan npwp
        $path_b =   "berkas/reklame/b/";
        $nama_b =   $id_pemohon . "_scan_npwp." . $b->getClientOriginalExtension();
        $request->file('scan_npwp')->move($path_b, $nama_b);
        //scan contoh reklame
        $path_c =   "berkas/reklame/c/";
        $nama_c =   $id_pemohon . "_contoh_reklame." . $c->getClientOriginalExtension();
        $request->file('contoh_reklame')->move($path_c, $nama_c);
        //  scann persetujuan
        $path_d =   "berkas/reklame/d/";
        $nama_d =   $id_pemohon . "_scan_persetujuan." . $d->getClientOriginalExtension();
        $request->file('scan_persetujuan')->move($path_d, $nama_d);
        //scan surat izin lama
        $path_e =   "berkas/reklame/e/";
        $nama_e =   $id_pemohon . "_scan_izin_lama." . $e->getClientOriginalExtension();
        $request->file('scan_izin_lama')->move($path_e, $nama_e);
        //scan pengantar
        $path_f =   "berkas/reklame/f/";
        $nama_f =   $id_pemohon . "_scan_pengantar." . $f->getClientOriginalExtension();
        $request->file('scan_pengantar')->move($path_f, $nama_f);

        DB::table('izin-reklame')->insert([
            'id_pemohon'                => $id_pemohon,
            'id_reklame'    => $request['id_reklame'],
            'banyak'        =>  $request['banyak'],
            'pesan_produk'  =>  $request['pesan_produk'],
            'tanggal_awal'  =>  $request['tanggal_awal'],
            'tanggal_akhir' =>  $request['tanggal_akhir'],
            'tempat_reklame'    => $request['tempat_reklame'],
            'scan_ktp'          => $path_a . $nama_a,
            'scan_npwp'         => $path_b . $nama_b,
            'contoh_reklame'    => $path_c . $nama_c,
            'scan_persetujuan'  => $path_d . $nama_d,
            'scan_izin_lama'    => $path_e . $nama_e,
            'scan_pengantar'    => $path_f . $nama_f,

        ]);
        return redirect()->back()->with('sukses', "Pengisian formulir berhasil, mohon untuk menunggu informasi lebih lanjut");
    }
    public function formIUMK(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'nama' => 'required', 'nik' => 'required|min:16|max:18', 'telepon' => 'required|numeric', 'pekerjaan' => 'required', 'rt' => 'required', 'rw' => 'required', 'jalan' => 'required',

            'nama_usaha' => 'required', 'alamat_usaha' => 'required', 'kodepos' => 'required|numeric', 'sektor_usaha' => 'required', 'sarana' => 'required', 'modal' => 'required|numeric', 'npwp' => 'required|numeric', 'klasifikasi' => 'required',

            'scan_ktp' => 'required | mimes:jpeg,jpg,png,PNG,pdf,txt | max:2048',
            'scan_kk' => 'required | mimes:jpeg,jpg,png,PNG,pdf,txt | max:2048',
            'foto' => 'required | mimes:jpeg,jpg,png,PNG,pdf,txt | max:2048',
            'scan_pengantar' => 'required | mimes:jpeg,jpg,png,PNG,pdf,txt | max:2048',
        ], Kustom::validasi());
        $pemohon = Pemohon::create([
            'nama'  =>  $request['nama'],
            'kode'  => Kustom::generateKode(6),
            'nik'   =>  $request['nik'],
            'telepon'   =>  $request['telepon'],
            'pekerjaan' =>  $request['pekerjaan'],
            'rt'    =>  $request['rt'],
            'rw'    =>  $request['rw'],
            'jalan' =>  $request['jalan'],
            'daerah_id'    =>  $request['daerah_id'],
            'pelayanan_id'  => $request['pelayanan_id'],
            'created_at'    =>  now(+7.00),
            'updated_at'   => null
        ]);
        $id_pemohon = $pemohon->id;
        $a  =   $request->file('scan_ktp');
        $b  =   $request->file('scan_kk');
        $c  =   $request->file('scan_pengantar');
        $d  =   $request->file('foto');
        // scan ktp
        $path_a =   "berkas/iumk/a/";
        $nama_a =   $id_pemohon . "_ktp." . $a->getClientOriginalExtension();
        $request->file('scan_ktp')->move($path_a, $nama_a);
        //scan kk
        $path_b =   "berkas/iumk/b/";
        $nama_b =   $id_pemohon . "_scan_kk." . $b->getClientOriginalExtension();
        $request->file('scan_kk')->move($path_b, $nama_b);
        //scan pengantar dari desa
        $path_c =   "berkas/iumk/c/";
        $nama_c =   $id_pemohon . "_scan_pengantar." . $c->getClientOriginalExtension();
        $request->file('scan_pengantar')->move($path_c, $nama_c);
        //  scann pas foto 4X6
        $path_d =   "berkas/iumk/d/";
        $nama_d =   $id_pemohon . "_foto." . $d->getClientOriginalExtension();
        $request->file('foto')->move($path_d, $nama_d);
        DB::table('izin-usaha-mikro-dan-kecil')->insert([
            'id_pemohon'    => $id_pemohon,
            'nama_usaha'    => $request['nama_usaha'],
            'alamat_usaha'  =>  $request['alamat_usaha'],
            'kodepos'       =>  $request['kodepos'],
            'sektor_usaha'  =>  $request['sektor_usaha'],
            'sarana'        =>  $request['sarana'],
            'modal'         =>  $request['modal'],
            'npwp'         =>  $request['npwp'],
            'klasifikasi'         =>  $request['klasifikasi'],
            'scan_ktp'          => $path_a . $nama_a,
            'scan_kk'         => $path_b . $nama_b,
            'scan_pengantar'    => $path_c . $nama_c,
            'foto'  => $path_d . $nama_d,

        ]);
        return redirect()->back()->with('sukses', "Pengisian formulir berhasil, mohon untuk menunggu informasi lebih lanjut");
    }
    public function formSK(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'nama' => 'required', 'nik' => 'required|min:16|max:18', 'telepon' => 'required', 'pekerjaan' => 'required', 'rt' => 'required', 'rw' => 'required', 'jalan' => 'required', 'jenis' => 'required',
            'nama_usaha' => 'required', 'alamat_usaha' => 'required', 'scan_ktp' => 'required | mimes:jpeg,jpg,png,PNG,pdf,txt | max:2048', 'scan_pengantar' => 'required | mimes:jpeg,jpg,png,PNG,pdf,txt | max:2048',
        ], Kustom::validasi());
        $pemohon = Pemohon::create([
            'nama'  =>  $request['nama'],
            'kode'  => Kustom::generateKode(6),
            'nik'   =>  $request['nik'],
            'telepon'   =>  $request['telepon'],
            'pekerjaan' =>  $request['pekerjaan'],
            'rt'    =>  $request['rt'],
            'rw'    =>  $request['rw'],
            'jalan' =>  $request['jalan'],
            'daerah_id'    =>  $request['daerah_id'],
            'pelayanan_id'  => $request['pelayanan_id'],
            'sublayanan_id' => $request['sublayanan_id'],
            'created_at'    =>  now(+7.00),
            'updated_at'   => null
        ]);
        $id_pemohon = $pemohon->id;
        if ($request['jenis'] == "1") {
            $jenis = "Permohonan Baru";
        } elseif ($request['jenis'] == "2") {
            $jenis = "Daftar Ulang";
        } else {
            $jenis = "Balik Nama";
        }
        $a  =   $request->file('scan_ktp');
        $b  =   $request->file('scan_pengantar');
        //scan ktp
        $path_a =   "berkas/salon-kecantikan/a/";
        $nama_a =   $id_pemohon . "_ktp." . $a->getClientOriginalExtension();
        $request->file('scan_ktp')->move($path_a, $nama_a);
        //scan kk
        $path_b =   "berkas/salon-kecantikan/b/";
        $nama_b =   $id_pemohon . "_scan_pengantar." . $b->getClientOriginalExtension();
        $request->file('scan_pengantar')->move($path_b, $nama_b);

        if ($request['jenis'] == "3") {
            DB::table('salon-kecantikan')->insert([
                'id_pemohon' => $id_pemohon,
                'jenis' => $jenis,
                'nama_usaha' => $request['nama_usaha'],
                'alamat_usaha' =>  $request['alamat_usaha'],
                'nama_usaha_baru' =>  $request['usaha_lama'],
                'scan_ktp' => $path_a . $nama_a,
                'scan_pengantar' => $path_b . $nama_b,
            ]);
        } else {
            DB::table('salon-kecantikan')->insert([
                'id_pemohon' => $id_pemohon,
                'jenis' => $jenis,
                'nama_usaha' => $request['nama_usaha'],
                'alamat_usaha' =>  $request['alamat_usaha'],
                'nama_usaha_baru' =>  "-",
                'scan_ktp' => $path_a . $nama_a,
                'scan_pengantar' => $path_b . $nama_b,
            ]);
        }
        return redirect()->back()->with('sukses', "Pengisian formulir berhasil, mohon untuk menunggu informasi lebih lanjut");
    }
    public function formRM(Request $request)
    {
        $pemohon = Pemohon::create([
            'nama'  =>  $request['nama'],
            'kode'  => Kustom::generateKode(6),
            'nik'   =>  $request['nik'],
            'telepon'   =>  $request['telepon'],
            'pekerjaan' =>  $request['pekerjaan'],
            'rt'    =>  $request['rt'],
            'rw'    =>  $request['rw'],
            'jalan' =>  $request['jalan'],
            'daerah_id'    =>  $request['daerah_id'],
            'pelayanan_id'  => $request['pelayanan_id'],
            'sublayanan_id' => $request['sublayanan_id'],
            'created_at'    =>  now(+7.00),
            'updated_at'   => null
        ]);
        $id_pemohon = $pemohon->id;
        if ($request['jenis'] == "1") {
            $jenis = "Permohonan Baru";
        } elseif ($request['jenis'] == "2") {
            $jenis = "Daftar Ulang";
        } else {
            $jenis = "Balik Nama";
        }
        $a  =   $request->file('scan_ktp');
        $b  =   $request->file('scan_pengantar');
        //scan ktp
        $path_a =   "berkas/rumah-makan/a/";
        $nama_a =   $id_pemohon . "_ktp." . $a->getClientOriginalExtension();
        $request->file('scan_ktp')->move($path_a, $nama_a);
        //scan kk
        $path_b =   "berkas/rumah-makan/b/";
        $nama_b =   $id_pemohon . "_scan_pengantar." . $b->getClientOriginalExtension();
        $request->file('scan_pengantar')->move($path_b, $nama_b);

        if ($request['jenis'] == "3") {
            DB::table('rumah-makan')->insert([
                'id_pemohon' => $id_pemohon,
                'jenis' => $jenis,
                'nama_usaha' => $request['nama_usaha'],
                'alamat_usaha' =>  $request['alamat_usaha'],
                'nama_usaha_baru' =>  $request['usaha_lama'],
                'scan_ktp' => $path_a . $nama_a,
                'scan_pengantar' => $path_b . $nama_b,
            ]);
        } else {
            DB::table('rumah-makan')->insert([
                'id_pemohon' => $id_pemohon,
                'jenis' => $jenis,
                'nama_usaha' => $request['nama_usaha'],
                'alamat_usaha' =>  $request['alamat_usaha'],
                'nama_usaha_baru' =>  "-",
                'scan_ktp' => $path_a . $nama_a,
                'scan_pengantar' => $path_b . $nama_b,
            ]);
        }
        return redirect()->back()->with('sukses', "Pengisian formulir berhasil, mohon untuk menunggu informasi lebih lanjut");
    }
    public function formGK(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'nama' => 'required', 'nik' => 'required|min:16|max:18', 'telepon' => 'required', 'pekerjaan' => 'required', 'rt' => 'required', 'rw' => 'required', 'jalan' => 'required', 'jumlah_monitor' => 'required|numeric',
            'nama_usaha' => 'required', 'alamat_usaha' => 'required', 'scan_ktp' => 'required | mimes:jpeg,jpg,png,PNG,pdf,txt | max:2048', 'scan_pengantar' => 'required | mimes:jpeg,jpg,png,PNG,pdf,txt | max:2048', 'scan_pernyataan_desa' => 'required | mimes:jpeg,jpg,png,PNG,pdf,txt | max:2048',
        ], Kustom::validasi());
        $pemohon = Pemohon::create([
            'nama'  =>  $request['nama'],
            'kode'  => Kustom::generateKode(6),
            'nik'   =>  $request['nik'],
            'telepon'   =>  $request['telepon'],
            'pekerjaan' =>  $request['pekerjaan'],
            'rt'    =>  $request['rt'],
            'rw'    =>  $request['rw'],
            'jalan' =>  $request['jalan'],
            'daerah_id'    =>  $request['daerah_id'],
            'pelayanan_id'  => $request['pelayanan_id'],
            'sublayanan_id' => $request['sublayanan_id'],
            'created_at'    =>  now(+7.00),
            'updated_at'   => null
        ]);
        $id_pemohon = $pemohon->id;
        $a  =   $request->file('scan_ktp');
        $b  =   $request->file('scan_pengantar');
        $c  =   $request->file('scan_pernyataan_desa');
        //scan ktp
        $path_a =   "berkas/gelanggang-ketangkasan/a/";
        $nama_a =   $id_pemohon . "_ktp." . $a->getClientOriginalExtension();
        $request->file('scan_ktp')->move($path_a, $nama_a);
        //scan pengantar
        $path_b =   "berkas/gelanggang-ketangkasan/b/";
        $nama_b =   $id_pemohon . "_scan_pengantar." . $b->getClientOriginalExtension();
        $request->file('scan_pengantar')->move($path_b, $nama_b);
        // scan pernyataan bermaterai
        $path_c =   "berkas/gelanggang-ketangkasan/c/";
        $nama_c =   $id_pemohon . "_scan_pernyataan_desa." . $c->getClientOriginalExtension();
        $request->file('scan_pernyataan_desa')->move($path_c, $nama_c);
        DB::table('gelanggang-ketangkasan')->insert([
            'id_pemohon'    => $id_pemohon,
            'nama_usaha'    => $request['nama_usaha'],
            'alamat_usaha'  =>  $request['alamat_usaha'],
            'jumlah_monitor'    => $request['jumlah_monitor'],
            'scan_ktp'      => $path_a . $nama_a,
            'scan_pengantar'         => $path_b . $nama_b,
            'scan_pernyataan_desa'  => $path_c . $nama_c,
        ]);
        return redirect()->back()->with('sukses', "Pengisian formulir berhasil, mohon untuk menunggu informasi lebih lanjut");
    }
    public function formAW(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'nama' => 'required', 'nik' => 'required|min:16|max:18', 'telepon' => 'required', 'pekerjaan' => 'required', 'rt' => 'required', 'rw' => 'required', 'jalan' => 'required', 'umur' => 'required|numeric',
            'nama_usaha' => 'required', 'alamat_usaha' => 'required', 'scan_ktp' => 'required | mimes:jpeg,jpg,png,PNG,pdf,txt | max:2048', 'scan_pengantar' => 'required | mimes:jpeg,jpg,png,PNG,pdf,txt | max:2048', 'scan_pernyataan_desa' => 'required | mimes:jpeg,jpg,png,PNG,pdf,txt | max:2048', 'struktur_organisasi' => 'required | mimes:jpeg,jpg,png,PNG,pdf,txt | max:2048',
        ], Kustom::validasi());
        $pemohon = Pemohon::create([
            'nama'  =>  $request['nama'],
            'kode'  => Kustom::generateKode(6),
            'nik'   =>  $request['nik'],
            'telepon'   =>  $request['telepon'],
            'pekerjaan' =>  $request['pekerjaan'],
            'rt'    =>  $request['rt'],
            'rw'    =>  $request['rw'],
            'jalan' =>  $request['jalan'],
            'daerah_id'    =>  $request['daerah_id'],
            'pelayanan_id'  => $request['pelayanan_id'],
            'sublayanan_id' => $request['sublayanan_id'],
            'created_at'    =>  now(+7.00),
            'updated_at'   => null
        ]);
        $id_pemohon = $pemohon->id;
        $a  =   $request->file('scan_ktp');
        $b  =   $request->file('scan_pengantar');
        $c  =   $request->file('scan_pernyataan_desa');
        $d  =   $request->file('struktur_organisasi');
        //scan ktp
        $path_a =   "berkas/atraksi-wisata/a/";
        $nama_a =   $id_pemohon . "_ktp." . $a->getClientOriginalExtension();
        $request->file('scan_ktp')->move($path_a, $nama_a);
        //scan pengantar
        $path_b =   "berkas/atraksi-wisata/b/";
        $nama_b =   $id_pemohon . "_scan_pengantar." . $b->getClientOriginalExtension();
        $request->file('scan_pengantar')->move($path_b, $nama_b);
        // scan pernyataan bermaterai
        $path_c =   "berkas/atraksi-wisata/c/";
        $nama_c =   $id_pemohon . "_scan_pernyataan_desa." . $c->getClientOriginalExtension();
        $request->file('scan_pernyataan_desa')->move($path_c, $nama_c);
        // scan struktur organ
        $path_d =   "berkas/atraksi-wisata/d/";
        $nama_d =   $id_pemohon . "_struktur_organisasi." . $d->getClientOriginalExtension();
        $request->file('struktur_organisasi')->move($path_d, $nama_d);
        DB::table('atraksi-wisata')->insert([
            'id_pemohon'    => $id_pemohon,

            'umur'      => $request['umur'],
            'nama_usaha'    => $request['nama_usaha'],
            'alamat_usaha'  =>  $request['alamat_usaha'],
            'jumlah_karyawan'    => $request['jumlah_karyawan'],
            'nilai_aset'    =>  $request['nilai_aset'],
            'scan_ktp'      => $path_a . $nama_a,
            'scan_pengantar'         => $path_b . $nama_b,
            'scan_pernyataan_desa'  => $path_c . $nama_c,
            'struktur_organisasi' => $path_d . $nama_d,
        ]);
        return redirect()->back()->with('sukses', "Pengisian formulir berhasil, mohon untuk menunggu informasi lebih lanjut");
    }
    public function pengaturanAkun()
    {
        $admin = Admin::where('username', session('username'))->first();
        $daerah = Daerah::where('admin_id', $admin->id)->first();
        $data = [
            'admin' => $admin,
            'daerah' => $daerah
        ];
        return view('v2/desa/pengaturan-akun', $data);
    }
    public function datalayanan()
    {
        $pelayanan = Pelayanan::get();
        $pemohon = Pemohon::where('daerah_id', session('daerah'))->get();
        $data = [
            'pelayanan' => $pelayanan,
            'pemohon' => $pemohon
        ];
        return view('v2/desa/data-pemohon', $data);
    }
    public function datapemohonDetail($slug)
    {
        $pelayanan = Pelayanan::where('slug', $slug)->first();
        $sublayanan = Sublayanan::where('id_pelayanan', $pelayanan->id)->get();
        $pemoho = Pemohon::where('daerah_id', session('daerah'))->get();
        $data = [
            'pelayanan' => $pelayanan,
            'cek' =>  count(Sublayanan::where('id_pelayanan', $pelayanan->id)->get()),
            'sublayanan' => $sublayanan,
            'pemohon' => $pemoho,
            'slug' => $slug
        ];
        return view('v2/desa/data-pemohon-detail', $data);
    }
    public function datapemohonDetailSub($slug, $slug1)
    {
        $pelayanan = Pelayanan::where('slug', $slug)->first();
        $sublayanan = Sublayanan::where('slug', $slug1)->first();
        $data = [
            'pelayanan' => $pelayanan,
            'sublayanan' => $sublayanan,
            'slug' => $slug1
        ];
        return view('v2/desa/data-pemohon-sub', $data);
    }
    public function DetailPemohon($slug, $kode)
    {
        // dd($kode);
        $dataDetail = DB::table("$slug")
            ->join('pemohons', 'pemohons.id', '=', "$slug.id_pemohon")
            ->join('daerahs', 'daerahs.id', '=', 'pemohons.daerah_id')
            ->join('pelayanans', 'pelayanans.id', '=', 'pemohons.pelayanan_id')
            ->where('pemohons.kode', "$kode")
            ->first();
        $getID = DB::table("pemohons")
            ->join("$slug", "$slug.id_pemohon", '=', 'pemohons.id')
            ->where('pemohons.kode', "$kode")
            ->first();
        $pelayanan = Pelayanan::where('slug', $slug)->first();
        if ($slug == "izin-reklame") {
            $reklame = DB::table('jenis-reklame')->find($dataDetail->id_reklame);

            $data = [
                'data' => $dataDetail,
                'id_berkas' => $getID->id,
                'layanan' => $pelayanan,
                'kode' => $kode,
                'nama_reklame' => $reklame->nama_reklame
            ];
        } else {
            $data = [
                'data' => $dataDetail,
                'id_berkas' => $getID->id,
                'layanan' => $pelayanan,
                'kode' => $kode,
            ];
        }
        return view('v2/desa/data-detail-pemohon', $data);
    }
    public function UbahDetailPemohon($slug, $kode)
    {
        $dataDetail = DB::table("$slug")
            ->join('pemohons', 'pemohons.id', '=', "$slug.id_pemohon")
            ->join('daerahs', 'daerahs.id', '=', 'pemohons.daerah_id')
            ->join('pelayanans', 'pelayanans.id', '=', 'pemohons.pelayanan_id')
            ->where('pemohons.kode', "$kode")
            ->first();
        $getID = DB::table("pemohons")
            ->join("$slug", "$slug.id_pemohon", '=', 'pemohons.id')
            ->where('pemohons.kode', "$kode")
            ->first();
        $pelayanan = Pelayanan::where('slug', $slug)->first();
        $daerah = Daerah::find(session('daerah'));
        if ($slug == "izin-reklame") {
            $reklame = DB::table('jenis-reklame')->find($dataDetail->id_reklame);

            $data = [
                'data' => $dataDetail,
                'id_berkas' => $getID->id,
                'pelayanan' => $pelayanan,
                'kode' => $kode,
                'daerah' => $daerah,
                'nama_reklame' => $reklame->nama_reklame
            ];
        } else {
            $data = [
                'data' => $dataDetail,
                'id_berkas' => $getID->id,
                'pelayanan' => $pelayanan,
                'kode' => $kode,
                'daerah' => $daerah,
            ];
        }
        return view('v2/desa/ubah-data-pemohon-1', $data);
    }
}
