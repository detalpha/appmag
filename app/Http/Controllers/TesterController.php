<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GarduInduk;
use Yajra\Datatables\Datatables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Coordinate\Ellipsoid;
use Auth;

class TesterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $coordinatea = new Coordinate('1 11.940N 99 22.756E'); 
        $coordinateb = new Coordinate('1.34391N 99.29913E');
        printf("Latitude A: %F\n", $coordinatea->getLatitude());
        printf("Longitude A: %F\n", $coordinatea->getLongitude());
        printf("Latitude B: %F\n", $coordinateb->getLatitude());
        printf("Longitude B: %F\n", $coordinateb->getLongitude());
    }
}
