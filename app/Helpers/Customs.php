<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use App\Models\Unit;
use App\User;

class Customs
{
    public function getTest() {
        return 'hello';
    }

    public $z_fault = 0;
    public $imp_urt_posneg_r = 0.2162;
    public $imp_urt_posneg_x = 0.3305;
    public $imp_urt_nol_r = 0.3631;
    public $imp_urt_nol_x = 1.618;
    public $kap_trafo = 60;
    public $teg_tm_ps_netral = 11547.00538;
    public $rn_ngr = 40;
    public $reak_x0 = 40;

    public function get_phasa_nilai_a($phasa_no)
    {
        
        $hasil = null;

        switch ($phasa_no) {
            case 1:
                
                $hasil = pow(((2 * $this->imp_urt_posneg_r) + $this->imp_urt_nol_r), 2) + pow(((2 * $this->imp_urt_posneg_x) + $this->imp_urt_nol_x), 2);

                break;
            
            case 2:
                
                $hasil = (pow($this->imp_urt_posneg_r, 2) + pow($this->imp_urt_posneg_x, 2)) * 4;

                break;
            
            case 3:
            
                $hasil = pow($this->imp_urt_posneg_r, 2) + pow($this->imp_urt_posneg_x, 2);

                break;
        }

        return $hasil;
    }

    public function get_phasa_nilai_b($phasa_no, $zsc1, $zt, $reak_x0)
    {
        
        $hasil = null;

        switch ($phasa_no) {
            case 1:

                $hasil = (2 * ((2 * $zsc1) + (2 * $zt) + $reak_x0) * ((2 * $this->imp_urt_posneg_x) + $this->imp_urt_nol_x)) + (2 * ((2 * $this->imp_urt_posneg_r) + $this->imp_urt_nol_r) * (3 * $this->z_fault) * (3 * $this->rn_ngr));

                break;
            
            case 2:
                
                $hasil = (8 * ($zt + $zsc1) * $this->imp_urt_posneg_x) + (2 * ($this->imp_urt_posneg_r * 2) * $this->z_fault);

                break;
            
            case 3:
            
                $hasil = (2 * ( $zt + $zsc1 ) * $this->imp_urt_posneg_x) + (2 * $this->imp_urt_posneg_r * $this->z_fault);

                break;
        }

        return $hasil;
    }

    public function get_phasa_nilai_c($phasa_no, $zsc1, $zt, $nilai_gangguan, $teg_sekunder, $reak_x0)
    {

        $hasil = null;

        switch ($phasa_no) {
            case 1:

                $z_ekivalen_ps1 = (3 * $this->teg_tm_ps_netral)/$nilai_gangguan;
                
                $hasil = pow((3 * $this->rn_ngr), 2) + pow((3 * $this->z_fault), 2) + pow(((2 * $zsc1) + ($zt * 2) + ($reak_x0)), 2) - pow($z_ekivalen_ps1, 2);

                break;
            
            case 2:

                $teg_tm_ps_ps = $teg_sekunder * 1000;

                $z_ekivalen_ps2 = $teg_tm_ps_ps/$nilai_gangguan;
                
                $hasil = (pow($this->z_fault, 2) + pow(((2 * $zsc1) + (2 * $zt)), 2)) - pow($z_ekivalen_ps2, 2);

                break;
            
            case 3:
            
                $z_ekivalen_ps3 = $this->teg_tm_ps_netral/$nilai_gangguan;
            
                $hasil = pow($this->z_fault, 2) + pow(($zsc1 + $zt), 2)-pow($z_ekivalen_ps3, 2);

                break;
        }

        return $hasil;
    }

    public function cek_arus_gangguan($phasa_no, $nilai_gangguan, $arus_hs_3_phs, $imp_trafo, $teg_sekunder)
    {
        
        $arus_hs_150_kv = $arus_hs_3_phs * 150 * pow(3, (1/2));
        $zsc1 = pow($teg_sekunder, 2) / $arus_hs_150_kv;

        $zt = ($imp_trafo/100*pow($teg_sekunder, 2))/$this->kap_trafo;
        $reak_x0 = (($imp_trafo * pow($teg_sekunder, 2)) / $this->kap_trafo) / 100;
        
        $phasa_nilai_a = $this->get_phasa_nilai_a($phasa_no);
        $phasa_nilai_b = $this->get_phasa_nilai_b($phasa_no, $zsc1, $zt, $reak_x0);
        $phasa_nilai_c = $this->get_phasa_nilai_c($phasa_no, $zsc1, $zt, $nilai_gangguan, $teg_sekunder, $reak_x0);
        
        $rumus_abc_g = pow($phasa_nilai_b, 2);
        $rumus_abc_h = 4 * $phasa_nilai_a * $phasa_nilai_c;        
        $rumus_abc_i = $rumus_abc_g - $rumus_abc_h;        
        $rumus_abc_j = pow($rumus_abc_i, (1/2));        
        $rumus_abc_k = $rumus_abc_j - $phasa_nilai_b;
        $rumus_abc_l = $rumus_abc_k / (2* $phasa_nilai_a);
        
        $hasil = null;

        switch ($phasa_no) {
            case 1:
                
                if($rumus_abc_l < 0){

                    $hasil = 0;

                }else{

                    $hasil = $rumus_abc_l;
                }

                break;
            
            case 2:
                
                if($rumus_abc_k < 0){
                    
                    $hasil = 0;

                }else{

                    $hasil = $rumus_abc_l;
                }

                break;
            
            case 3:

                if($rumus_abc_k < 0){

                    $hasil = 0;

                }else if($rumus_abc_k > 0){

                    $hasil = $rumus_abc_l;

                }else{

                    $hasil = "Tepat pada arus maks";
                }

                break;
        }

        return $hasil;
    }
}