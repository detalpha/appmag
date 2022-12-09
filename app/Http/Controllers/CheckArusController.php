<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Helpers\Customs;
use App\Models\GarduInduk;
use App\Models\Penyulang;
use App\Models\PenyulangSpot;
use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Coordinate\Ellipsoid;
use League\Geotools\Geotools;
use Auth;
use DB;
use Exception;

class CheckArusController extends Controller
{
    public $customs;
    public $list_titik_penyulang;
    public $list_titik_perkiraan_gangguan = [];

    public function __construct()
    {
        $this->customs = new Customs();
        $this->middleware('auth');
    }
    
    public function form_gangguan()
    {
        
        $penyulang = Penyulang::get();

        $datanya=[
            "penyulang" => $penyulang
        ];

        return view('cekarus.form', compact('datanya'));
    }
    
    public function perhitungan(Request $request)
    {
        
        $rules = array(
            'nilai_gangguan'    => 'required|numeric',
            'fasa'              => 'required|numeric',
            'penyulang_id'      => 'required|exists:penyulangs,id',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            
            return redirect('proses/cekgangguan')->withErrors($validator);

        } else {

            $data_penyulang = Penyulang::with(['gardu_induk','penyulang_spot'])->findOrFail($request->penyulang_id);
            $data_gardu_induk = $data_penyulang->gardu_induk;
            // $this->list_titik_penyulang = $data_penyulang->penyulang_spot;
            $list_titik_penyulang = $data_penyulang->penyulang_spot;
            
            $jarak_gangguan = $this->customs->cek_arus_gangguan($request->fasa, $request->nilai_gangguan, $data_penyulang->arus_hs_3_phs, $data_penyulang->imp_trafo, $data_penyulang->teg_skunder);

            // $titik_gangguan = $this->cari_titik_gangguan($data_gardu_induk, $list_titik_penyulang, $jarak_gangguan, 0, 0);
            // $titik_gangguan = $this->cari_titik_gangguan_radius($data_gardu_induk, $list_titik_penyulang, $jarak_gangguan);
            // $titik_gangguan = $this->cari_titik_terdekat($data_gardu_induk);

            if(is_nan($jarak_gangguan)){
                $jarak_gangguan = 0;
            }

            $data = [
                'jarak_gangguan' => $jarak_gangguan,
                'data_penyulang' => $data_penyulang
            ];

            return $data;
        }
    }
    
    public function cari_titik_gangguan($dari, $list_titik_penyulang, $jarak_gangguan, $sum_jarak, $prev_jarak)
    {
        $masih_cari = true;
        $last_titik = null;
        $next_titik = null;
        $list_titik_perkiraan_gangguan = [];

        // return $jarak_gangguan;

        // while ($masih_cari) {

        $next_wp = $this->cari_titik_terdekat($dari, $list_titik_penyulang);
        
        foreach($next_wp as $value){
            
            $new_jarak = $sum_jarak + $value["jarak"];
            $new_dari = $value["data_titik"];

            echo "id:".$dari["id"]."->".$value["data_titik"]["id"].", jarak:".$value["jarak"].", total jarak:".$new_jarak."||";
            
            if($new_jarak < $jarak_gangguan){

                // $list_titik_penyulang = $this->list_titik_penyulang;

                if(!is_array($list_titik_penyulang)){
                    $list_titik_penyulang = $list_titik_penyulang->all();
                }

                $exclude_id = $new_dari->id;
                
                $new_list_spot = array_filter($list_titik_penyulang, function($v) use ($exclude_id){ 
                    return $v["id"] != $exclude_id; 
                });

                // $this->list_titik_penyulang = $new_list_spot;

                if(count($new_list_spot) > 0){
                    echo "list: ". count($new_list_spot);
                    $hasil_pencarian_titik = $this->cari_titik_gangguan($new_dari, $new_list_spot, $jarak_gangguan, $new_jarak, $value["jarak"]);

                    if(count($hasil_pencarian_titik) > 0){
                        echo "hasil: ". count($hasil_pencarian_titik);
                        $list_titik_perkiraan_gangguan[] = $hasil_pencarian_titik;
                    }else{
                        echo "hasil: tidak ada";
                    }

                    continue;
                } else {
                    echo "list: tidak ada";
                    $prev_wp = array(
                        'wp_data' => $dari,
                        'jarak' => $sum_jarak
                    );
    
                    $next_wp = array(
                        'wp_data' => $new_dari,
                            'jarak' => $new_jarak,
                    );
    
                    $list_titik_perkiraan_gangguan[] = array(
                        'prev_wp' => $prev_wp,
                        'next_wp' => $next_wp
                    );

                    continue;
                }
            } else {
                echo "jarak: lewat";
                $prev_wp = array(
                    'wp_data' => $dari,
                    'jarak' => $sum_jarak
                );

                $next_wp = array(
                    'wp_data' => $new_dari,
                        'jarak' => $new_jarak,
                );

                $list_titik_perkiraan_gangguan[] = array(
                    'prev_wp' => $prev_wp,
                    'next_wp' => $next_wp
                );

                continue;
            }

            echo "<br />\r\n";
        }

        return $list_titik_perkiraan_gangguan;
    }

    public function cari_titik_terdekat($dari, $list_titik_penyulang)
    {
        
        $geotools = new \League\Geotools\Geotools();
        $coordA   = new \League\Geotools\Coordinate\Coordinate([$dari->latitude, $dari->longitude]);

        $arr_titik_terdekat = [];
        // $list_titik_penyulang = $this->list_titik_penyulang;

        if(!is_array($list_titik_penyulang)){
            $list_titik_penyulang = $list_titik_penyulang->all();
        }
        
        // try {
            $distances = array_map(function($titik) use($geotools, $coordA) {

                $coordB   = new \League\Geotools\Coordinate\Coordinate([$titik->latitude, $titik->longitude]);
                $distance = $geotools->distance()->setFrom($coordA)->setTo($coordB);
    
                return $distance->in('km')->haversine();
    
            }, $list_titik_penyulang);
            
            $next_tiang = array_filter($distances, function($v){
                return $v < 0.1;
            });
    
            foreach($next_tiang as $key => $value){
                
                $obj_titik = [
                    'data_titik'    => $list_titik_penyulang[$key],
                    'jarak'         => $value
                ];
                
                $arr_titik_terdekat[] = $obj_titik;
            }
            
            return $arr_titik_terdekat;

        // } catch (Exception $e) {
        //     return $e->getMessage();
        // }
        
    }

    

    public function cari_titik_gangguan_radius($dari, $list_titik_penyulang, $jarak_gangguan)
    {
        
        $geotools = new \League\Geotools\Geotools();
        $coordA   = new \League\Geotools\Coordinate\Coordinate([$dari->latitude, $dari->longitude]);

        $arr_titik_terdekat = [];
        // $list_titik_penyulang = $this->list_titik_penyulang;

        if(!is_array($list_titik_penyulang)){
            $list_titik_penyulang = $list_titik_penyulang->all();
        }
        
        // try {
            $distances = array_map(function($titik) use($geotools, $coordA) {

                $coordB   = new \League\Geotools\Coordinate\Coordinate([$titik->latitude, $titik->longitude]);
                $distance = $geotools->distance()->setFrom($coordA)->setTo($coordB);
    
                return $distance->in('km')->haversine();
    
            }, $list_titik_penyulang);
            
            $next_tiang = array_filter($distances, function($v) use ($jarak_gangguan){
                return (($v < $jarak_gangguan) && ($jarak_gangguan - $v <= 0.1)) || (($v > $jarak_gangguan) && ($v - $jarak_gangguan <= 0.1));
            });
    
            foreach($next_tiang as $key => $value){
                
                $obj_titik = [
                    'data_titik'    => $list_titik_penyulang[$key],
                    'jarak'         => $value
                ];
                
                $arr_titik_terdekat[] = $obj_titik;
            }
            
            return $arr_titik_terdekat;

        // } catch (Exception $e) {
        //     return $e->getMessage();
        // }
        
    }
}
