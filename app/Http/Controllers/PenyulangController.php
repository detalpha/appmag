<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GarduInduk;
use App\Models\Penyulang;
use App\Models\PenyulangSpot;
use Yajra\Datatables\Datatables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Coordinate\Ellipsoid;
use League\Geotools\Geotools;
use Auth;
use DB;

class PenyulangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('penyulang.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $garduinduk = GarduInduk::where("gi_type", 1)->get();
        $garduhubung = GarduInduk::where("gi_type", 2)->get();

        $datanya=[
            "garduinduk" => $garduinduk,
            "garduhubung" => $garduhubung,
        ];

        return view('penyulang.create', compact('datanya'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = array(
            // 'name'    => 'required|unique:depre_type_group',
            'code' => [
                'required',
                Rule::unique('penyulangs')->where(function ($query) use($request) {
                    return $query->where('code', $request->code)->where('deleted_at', null);
                }),
            ]
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            //return $request->all();
            return redirect('master/penyulang/create')->withErrors($validator);
        } else {

            try {
                DB::beginTransaction();

                $penyulang = Penyulang::withTrashed()->whereRaw('LOWER(code) = ?', strtolower($request->code))->first();

                $insert = null;
                $penyulang_id = null;
                
                if ($penyulang) {

                    if($penyulang->deleted_at){
                        $penyulang->restore();
                        
                        $insert = $penyulang->update([
                            'code'              => $request->code, 
                            'name'              => $request->name, 
                            'gardu_induks_id'   => $request->gardu_induks_id, 
                            'gardu_hubung_id'   => $request->gardu_hubung_id, 
                            'arus_hs_3_phs'     => $request->arus_hs_3_phs, 
                            'teg_primer'        => $request->teg_primer, 
                            'teg_skunder'       => $request->teg_skunder, 
                            'imp_trafo'         => $request->imp_trafo, 
                            'deleted_at'        => null,
                            'updated_by'        => Auth::user()->id
                        ]);

                        $penyulang_id = $penyulang->id;

                        $clean_penyulangspot = PenyulangSpot::destroy($penyulang_id);
                    }
                } else {
                    
                    $insert = Penyulang::create([
                        'code'              => $request->code, 
                        'name'              => $request->name,
                        'gardu_induks_id'   => $request->gardu_induks_id, 
                        'gardu_hubung_id'   => $request->gardu_hubung_id, 
                        'arus_hs_3_phs'     => $request->arus_hs_3_phs, 
                        'teg_primer'        => $request->teg_primer, 
                        'teg_skunder'       => $request->teg_skunder, 
                        'imp_trafo'         => $request->imp_trafo, 
                        'created_by'        => Auth::user()->id,
                        'updated_by'        => Auth::user()->id
                    ]);
                    
                    $penyulang_id = $insert->id;
                }

                if($request->has('datajson')){
                    $penyulang_spot = json_decode($request->datajson);
                    
                    foreach ($penyulang_spot as $key => $value) {

                        
                        $ddm_koordinat = $value->Position;

                        preg_match_all ('/[A-Z][0-9]{1,2}( |.)[0-9]{1,2}(.|)[0-9]{1,3}/', $ddm_koordinat, $arr_ddm_koordinat);

                        list($ddm_latitude, $ddm_longitude) = $arr_ddm_koordinat[0];

                        if(!$ddm_latitude || !$ddm_longitude){
                            return redirect('master/penyulang/create')->withErrors("format koordinat salah.");
                        }

                        $ddm_latitude = substr($ddm_latitude, 1).substr($ddm_latitude, 0, 1);
                        $ddm_longitude = substr($ddm_longitude, 1).substr($ddm_longitude, 0, 1);

                        $dd_coordinate = new Coordinate( $ddm_latitude.' '.$ddm_longitude);

                        PenyulangSpot::create([
                            'penyulangs_id' => $penyulang_id,
                            'header'        => $value->Header,
                            'code'          => $value->Name,
                            'name'          => $value->Name,
                            'type'          => $value->Type,
                            'latitude'      => $dd_coordinate->getLatitude(),
                            'longitude'     => $dd_coordinate->getLongitude()
                        ]);
                    }
                }
                
                if ($insert) {
                    DB::commit();
                    Session::flash('success', 'Data Saved');
                } else {
                    DB::rollBack();
                    Session::flash('error', 'Error');
                }
                return redirect('master/penyulang');

            } catch (Exception $e) {
                DB::rollBack();
                //throw $th;
                return redirect('master/penyulang/create')->withErrors($e->getMessage());
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Penyulang::findOrFail($id);

        $garduinduk = GarduInduk::where("gi_type", 1)->get();
        $garduhubung = GarduInduk::where("gi_type", 2)->get();

        $datanya=[
            "garduinduk" => $garduinduk,
            "garduhubung" => $garduhubung,
        ];
        
        return view('penyulang.edit', compact('data', 'datanya'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = array(
            'code' => [
                'required',
                Rule::unique('penyulangs')->where(function ($query) use($request, $id) {
                    return $query->where('code', $request->code)->where('id', '!=', $id);
                }),
            ]
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            
            return redirect('master/penyulang/edit')->withErrors($validator);
        } else {

            try {
                DB::beginTransaction();

                $penyulang = Penyulang::find($id);
                $update = $penyulang->update([
                    'code'              => $request->code, 
                    'name'              => $request->name, 
                    'gardu_induks_id'   => $request->gardu_induks_id, 
                    'gardu_hubung_id'   => $request->gardu_hubung_id, 
                    'arus_hs_3_phs'     => $request->arus_hs_3_phs, 
                    'teg_primer'        => $request->teg_primer, 
                    'teg_skunder'       => $request->teg_skunder, 
                    'imp_trafo'         => $request->imp_trafo, 
                    'deleted_at'        => null,
                    'updated_by'        => Auth::user()->id
                ]);

                if($request->has('datajson') && !empty($request->get("datajson"))){

                    PenyulangSpot::where('penyulangs_id', $id)->delete();

                    $penyulang_spot = json_decode($request->datajson);
                    
                    foreach ($penyulang_spot as $key => $value) {

                        $ddm_koordinat = $value->Position;

                        preg_match_all ('/[A-Z][0-9]{1,2}( |.)[0-9]{1,2}(.|)[0-9]{1,3}/', $ddm_koordinat, $arr_ddm_koordinat);

                        list($ddm_latitude, $ddm_longitude) = $arr_ddm_koordinat[0];

                        if(!$ddm_latitude || !$ddm_longitude){
                            return redirect('master/penyulang/edit')->withErrors("format koordinat salah.");
                        }

                        $ddm_latitude = substr($ddm_latitude, 1).substr($ddm_latitude, 0, 1);
                        $ddm_longitude = substr($ddm_longitude, 1).substr($ddm_longitude, 0, 1);

                        $dd_coordinate = new Coordinate( $ddm_latitude.' '.$ddm_longitude);

                        PenyulangSpot::create([
                            'penyulangs_id' => $insert->id,
                            'header'        => $value->Header,
                            'code'          => $value->Name,
                            'name'          => $value->Name,
                            'type'          => $value->Type,
                            'latitude'      => $dd_coordinate->getLatitude(),
                            'longitude'     => $dd_coordinate->getLongitude()
                        ]);
                    }
                }
                
                if ($update) {
                    DB::commit();
                    Session::flash('success', 'Data Saved');
                } else {
                    DB::rollBack();
                    Session::flash('error', 'Error');
                }
                return redirect('master/penyulang');

            } catch (Exception $e) {
                DB::rollBack();
                //throw $th;
                return redirect('master/penyulang/edit')->withErrors($e->getMessage());
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $olddata = Penyulang::find($id);
        $olddata->delete();

        return redirect('master/penyulang')->with('success','Penyulang telah dihapus');
    }

    public function penyulangjson(request $request)
    {
        $data = Penyulang::with(['gardu_induk'])->get();

        return DataTables::of($data)
                
                ->addColumn('gardu_induk_name', function ($data) {
                    if($data->gardu_induk){
                        
                        return $data->gardu_induk->name;

                    }else{

                        return "";
                    }
                })
                ->addColumn('gardu_hubung_name', function ($data) {
                    if($data->gardu_hubung){
                        
                        return $data->gardu_hubung->name;
                        
                    }else{

                        return "";
                    }
                })
                ->addColumn('action1', function ($data) {
                    return'
                    <div class="row">
                        <div class="col-md-6">
                            <a href="'.action('PenyulangController@edit', $data->id ).'" title="Edit" class="btn btn-xs" style="background: transparent;border:green;color: green;"><i class="fa fa-edit"></i></a>
                        </div>
                        <div class="col-md-6">
                            <form action="'.action('PenyulangController@destroy', $data->id ).'" method="post">
                                <input name="_method" type="hidden" value="DELETE">
                                <input type="hidden" name="_token" value="'.csrf_token().'"/>
                                <button style="background: transparent;border:red;color: red;" title="Delete" onclick="return confirm(\'Apakah anda ingin menghapus Penyulang '.$data->name.'.\');"><i class="fa fa-trash"></i></button>
                            </form>
                        </div>
                    </div>';
                
                })
                ->rawColumns([ 'action1','gardu_hubung_name','gardu_induk_name'])
                ->make(true);
    }
}
