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
use League\Geotools\Geotools;
use Auth;

class GarduIndukController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('garduinduk.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('garduinduk.create');
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
                Rule::unique('gardu_induks')->where(function ($query) use($request) {
                    return $query->where('code', $request->code)->whereNull('deleted_at');
                }),
            ]
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            //return $request->all();
            return redirect('master/gardu/create')->withErrors($validator);
        } else {
            
            $ddm_koordinat = $request->coordinate;

            preg_match_all ('/[A-Z][0-9]{1,2}( |.)[0-9]{1,2}(.|)[0-9]{1,3}/', $ddm_koordinat, $arr_ddm_koordinat);

            list($ddm_latitude, $ddm_longitude) = $arr_ddm_koordinat[0];

            if(!$ddm_latitude || !$ddm_longitude){
                return redirect('master/gardu/create')->withErrors("format koordinat salah.");
            }

            $ddm_latitude = substr($ddm_latitude, 1).substr($ddm_latitude, 0, 1);
            $ddm_longitude = substr($ddm_longitude, 1).substr($ddm_longitude, 0, 1);

            $dd_coordinate = new Coordinate( $ddm_latitude.' '.$ddm_longitude);

            $insert = null;
            
            $garduinduk = GarduInduk::withTrashed()->whereRaw('LOWER(code) = ?', strtolower($request->code))->first();

            if ($garduinduk) {

                if($garduinduk->deleted_at){
                    
                    $garduinduk->restore();
                    $insert = $garduinduk->update([
                        'code'          => $request->code, 
                        'name'          => $request->name, 
                        'gi_type'       => $request->type,
                        'latitude'      => $dd_coordinate->getLatitude(),
                        'longitude'     => $dd_coordinate->getLongitude(),
                        'updated_by'    => Auth::user()->id
                    ]);
                }
            } else {
                $insert = GarduInduk::create([
                    'code'          => $request->code, 
                    'name'          => $request->name, 
                    'gi_type'       => $request->type,
                    'latitude'      => $dd_coordinate->getLatitude(),
                    'longitude'     => $dd_coordinate->getLongitude(),
                    'created_by'    => Auth::user()->id,
                    'updated_by'    => Auth::user()->id
                ]);
            }
            
            if ($insert) {
                Session::flash('success', 'Data Saved');
            } else {
                Session::flash('error', 'Error');
            }
            return redirect('master/gardu');
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
        $data = GarduInduk::findOrFail($id);
        
        return view('garduinduk.edit', compact('data'));
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
            // 'name'    => 'required|unique:depre_type_group',
            'code' => [
                'required',
                // Rule::unique('gardu_induks')->where(function ($query) use($request) {
                //     return $query->where('code', $request->code);
                // }),
                Rule::unique('gardu_induks')->ignore($id),
            ]
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            //return $request->all();
            return redirect('master/gardu/'.$id.'/edit')->withErrors($validator);
        } else {

            $ddm_koordinat = $request->coordinate;

            preg_match_all ('/[A-Z][0-9]{1,2}( |.)[0-9]{1,2}(.|)[0-9]{1,3}/', $ddm_koordinat, $arr_ddm_koordinat);

            list($ddm_latitude, $ddm_longitude) = $arr_ddm_koordinat[0];
            return json_encode($arr_ddm_koordinat);

            if(!$ddm_latitude || !$ddm_longitude){
                return redirect('master/gardu/create')->withErrors("format koordinat salah.");
            }

            $ddm_latitude = substr($ddm_latitude, 1).substr($ddm_latitude, 0, 1);
            $ddm_longitude = substr($ddm_longitude, 1).substr($ddm_longitude, 0, 1);

            $dd_coordinate = new Coordinate( $ddm_latitude.' '.$ddm_longitude);

            $insert = null;
            
            $garduinduk = GarduInduk::find($id);
            $update = $garduinduk->update([
                'code'          => $request->code, 
                'name'          => $request->name, 
                'gi_type'       => $request->type,
                'latitude'      => $dd_coordinate->getLatitude(),
                'longitude'     => $dd_coordinate->getLongitude(),
                'updated_by'    => Auth::user()->id
            ]);
            
            if ($update) {
                Session::flash('success', 'Data Saved');
            } else {
                Session::flash('error', 'Error');
            }
            return redirect('master/gardu');
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
        $olddata = GarduInduk::find($id);
        $olddata->delete();

        return redirect('master/gardu')->with('success','Gardu telah dihapus');
    }

    public function mstgijson(request $request)
    {
        $data = GarduInduk::get();

        return DataTables::of($data)
                ->editColumn('gi_type', function ($data) {
                    if($data->gi_type == 1){

                        return "Gardu Induk";

                    }else{

                        return "Gardu Hubung";
                    }
                })
                ->addColumn('koordinat', function ($data) {
                    
                    return $data->latitude.", ".$data->longitude;

                })
                ->addColumn('action1', function ($data) {
                    return'
                    <div class="row">
                        <div class="col-md-6">
                            <a href="'.action('GarduIndukController@edit', $data->id ).'" title="Edit" class="btn btn-xs" style="background: transparent;border:green;color: green;"><i class="fa fa-edit"></i></a>
                        </div>
                        <div class="col-md-6">
                            <form action="'.action('GarduIndukController@destroy', $data->id ).'" method="post">
                                <input name="_method" type="hidden" value="DELETE">
                                <input type="hidden" name="_token" value="'.csrf_token().'"/>
                                <button style="background: transparent;border:red;color: red;" title="Delete" onclick="return confirm(\'Apakah anda ingin menghapus Gardu '.$data->name.'.\');"><i class="fa fa-trash"></i></button>
                            </form>
                        </div>
                    </div>';
                
                })
                ->rawColumns(['koordinat','action1'])
                ->make(true);
    }
}
