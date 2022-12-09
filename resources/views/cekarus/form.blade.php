@extends('adminlte::page')

@section('title', 'Cek Arus Gangguan')

@push('css')

@section('css')
  <style>
    .sembunyi{
      display: none;
    }
  </style>
@stop

@section('content_header')
    <h1>
      Cek Arus Gangguan
      {{-- <small>Tambah</small> --}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{url('home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{url('penyulang')}}">Cek Arus Gangguan</a></li>
    </ol>
@stop

@section('content')
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div><br />
    @endif
    <div class="row">
      <!-- left column -->
      <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
          <!-- /.box-header -->
          <!-- form start -->
          <form role="form" class="form-horizontal" > {{-- class="form-horizontal" method="POST" action="{{url('cekarus')}}"> --}}
            {{csrf_field()}}
            <div class="box-body">
              <div class="row">
                <div class="col-md-3"></div>
                <!-- left column -->
                <div class="col-md-6">
                  
                  <div class="form-group">
                    <label for="penyulang" class="col-sm-4 control-label">Penyulang</label>
                    <div class="col-sm-8">
                      <select class="form-control select2" id="penyulang" name="penyulang" style="width: 100%;">
                        @foreach ($datanya["penyulang"] as $item)
                          <option value="{{ $item->id }}">{{ $item->code }} - {{ $item->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="fasa" class="col-sm-4 control-label">Fasa</label>
                    <div class="col-sm-8">
                      <select class="form-control select2" id="fasa" name="fasa" style="width: 100%;">
                          <option value="1">1</option>
                          <option value="2">2</option>
                          <option value="3">3</option>
                      </select>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="nilai_gangguan" class="col-sm-4 control-label">Nilai Gangguan</label>
                    <div class="col-sm-8">
                      <input type="number" class="form-control" name="nilai_gangguan" id="nilai_gangguan" placeholder="Masukan Nilai Gangguan" required>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="radius_deteksi" class="col-sm-4 control-label">Radius Deteksi</label>
                    <div class="col-sm-7">
                      <input type="number" class="form-control" name="radius_deteksi" id="radius_deteksi" placeholder="Dalam kilometer" value="1">
                    </div>
                    <label for="radius_deteksi" class="col-sm-1 control-label">KM</label>
                  </div>
                  
                  <h4>Output</h4>
                  <hr />
                  
                  <div class="form-group">
                    <label for="nilai_gangguan" class="col-sm-4 control-label">Jarak Gangguan</label>
                    <label for="jarak_gangguan" id="jarak_gangguan" class="col-sm-4 control-label">0</label>
                    <label for="jarak_gangguan" class="col-sm-2 control-label">KM</label>
                  </div>

                  <div class="row">
                    <div class="col-md-12 table-responsive">
                      <table class="tabel table table-bordered" id="tbl_spot_penyulang">
                        <thead>
                          <tr>
                            <th>Nama Titik</th>
                            <th>Latitude</th>
                            <th>Longitude</th>
                            <th>Jarak (KM)</th>
                          </tr>
                        </thead>
                        <tbody>                        
                        </tbody>
                      </table>
                    </div>
                    <!-- /.col -->
                  </div>

                </div>
                <div class="col-md-3"></div>
            </div>
            <!-- /.box-body -->

            <div class="box-footer">
              <button type="button" id="kalkulasi" class="btn btn-primary">Process</button>
              <button type="button" id="sharewa" class="btn btn-success sembunyi" data-toggle="modal" data-target="#modal-share"><i class="fab fa-whatsapp"></i> &nbsp;Share</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="modal fade" id="modal-share">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Share Via WhatsApp</h4>
          </div>
          <div class="modal-body">
            <form role="form" class="form-horizontal">
              <div class="box-body">
                <div class="row">
                  <!-- left column -->
                  <div class="col-md-12">
                    
                    <div class="form-group">
                      <label for="no_tujuan" class="col-sm-4 control-label">No Tujuan</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" name="no_tujuan" id="no_tujuan" placeholder="Contoh: 6282134567890">
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <label for="fasa" class="col-sm-4 control-label">Pesan</label>
                      <div class="col-sm-8">
                        <textarea class="form-control" name="pesan" id="pesan" placeholder="Isi pesan">
                        </textarea>
                      </div>
                    </div>

                  </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Tutup</button>
            <button type="button" id="kirimwa" class="btn btn-primary">Kirim</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
@stop

@section('js')

  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

  <script>

    $(function(){

      // const axios = require('axios');
      let prev_nilai_gangguan = "";
      let prev_fasa = "";
      let prev_penyulang = "";
      let hasil = [];
      let destinasi = [];
      let penyulang = [];
      let str_newgooglelink = "";
      
      $('#tbl_spot_penyulang').DataTable({
        "paging": false,
        "searching": false
      });

      $('#kalkulasi').on('click', function(){

        const curr_nilai_gangguan = $('#nilai_gangguan').val();
        const curr_fasa = $('#fasa').val();
        const curr_penyulang = $('#penyulang').val();

        axios.post('/cekgangguan', {
          nilai_gangguan: curr_nilai_gangguan,
          fasa: curr_fasa,
          penyulang_id: curr_penyulang
        })
        .then(function (response) {

          const { jarak_gangguan, data_penyulang } = response.data;

          const jarak = Math.round(jarak_gangguan * 100) / 100;

          $("#jarak_gangguan").html(jarak);

          const { gardu_induk, penyulang_spot } = data_penyulang;
          
          const list_titik_penyulang = penyulang_spot.map(function callback(value, id) {
                                      return {
                                                "latitude": value.latitude,
                                                "longitude": value.longitude,
                                                "nama": value.name
                                              };
                                    });

          const data = {
            "origins": [{
                        "latitude": gardu_induk.latitude,
                        "longitude": gardu_induk.longitude
                      }],
            "destinations": list_titik_penyulang
          };

          if(curr_nilai_gangguan != prev_nilai_gangguan || curr_fasa != prev_fasa || curr_penyulang != prev_penyulang){

            cari_titik_penyulang(data, jarak_gangguan, curr_nilai_gangguan, curr_fasa, curr_penyulang);

          }else{

            olah_titik_penyulang(jarak_gangguan);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
      });

      $('#sharewa').on('click', function(){
        
        $("#modal-share #pesan").html(str_newgooglelink);
      });

      $('#kirimwa').on('click', function(){

        const phone = $('#modal-share #no_tujuan').val();
        const text = $('#modal-share #pesan').val();

        const urllink = "https://api.whatsapp.com/send?phone=" + encodeURI(phone) + "&text=" + encodeURI(text);

        window.open(urllink,'_blank');

      });

      function cari_titik_penyulang(data_sumber, jarak_gangguan, curr_nilai_gangguan, curr_fasa, curr_penyulang){
        
        axios.post('https://dev.virtualearth.net/REST/v1/Routes/DistanceMatrix?key={{ env("BING_API_KEY") }}', {
          "origins": data_sumber.origins,
          "destinations": data_sumber.destinations,
          "travelMode": "driving",
          "distanceUnit": "km"
        })
        .then(function (response) {
          // console.log(response);

          const data = response.data;

          if(data.statusCode == 200){
            prev_nilai_gangguan = curr_nilai_gangguan;
            prev_fasa = curr_fasa;
            prev_penyulang = curr_penyulang;

            hasil = data.resourceSets[0].resources[0].results;
            destinasi = data.resourceSets[0].resources[0].destinations;
            penyulang = data_sumber.destinations;

            olah_titik_penyulang(jarak_gangguan);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
      }

      function olah_titik_penyulang(jarak_gangguan){

        const radius = parseFloat($("#radius_deteksi").val());

        const titik = $.grep(hasil, function( v, i ) {
                        
                        return (( v.travelDistance > (jarak_gangguan - radius)) && (v.travelDistance < (jarak_gangguan + radius)));
                      });

        const newtable = titik.map(function callback(value, id) {
                                    
                                  return "<tr>" +
                                            "<td>"+ penyulang[value.destinationIndex].nama +"</td>" +
                                            "<td>"+ destinasi[value.destinationIndex].latitude +"</td>" +
                                            "<td>"+ destinasi[value.destinationIndex].longitude +"</td>" +
                                            "<td>"+ value.travelDistance.toFixed(2) +"</td>" +
                                          "</tr>";
                                });

        const newgooglelink = titik.map(function callback(value, id) {
                                    
                                  return "http://www.google.com/maps/place/"+ destinasi[value.destinationIndex].latitude +","+ destinasi[value.destinationIndex].longitude;
                                });

        str_newgooglelink = newgooglelink.join("\n");

        $("#tbl_spot_penyulang tbody").html(newtable);
        $("#sharewa").removeClass("sembunyi");
        $("#modal-share #pesan").html(str_newgooglelink);
      }

    });
    
   </script>
@stop