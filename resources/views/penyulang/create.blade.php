@extends('adminlte::page')

@section('title', 'Tambah Penyulang')

@push('css')

@section('content_header')
    <h1>
      Master Penyulang
      <small>Tambah</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{url('home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{url('penyulang')}}">Master Penyulang</a></li>
      <li class="active">Tambah Penyulang</li>
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
          <form role="form" class="form-horizontal" method="POST" action="{{url('master/penyulang')}}">
            {{csrf_field()}}
            <div class="box-body">
              <div class="row">
                <div class="col-md-3"></div>
                <!-- left column -->
                <div class="col-md-6">
                  
                  <div class="form-group">
                    <label for="code" class="col-sm-4 control-label">Kode Penyulang</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" name="code" id="code" placeholder="Masukan Kode Penyulang" required>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="name" class="col-sm-4 control-label">Nama Penyulang</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" name="name" id="name" placeholder="Masukan Nama Penyulang" required>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="gardu_induks_id" class="col-sm-4 control-label">Gardu Induk</label>
                    <div class="col-sm-8">
                      <select class="form-control select2" id="gardu_induks_id" name="gardu_induks_id" style="width: 100%;">
                        @foreach ($datanya["garduinduk"] as $item)
                          <option value="{{ $item->id }}">{{ $item->code }} - {{ $item->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="gardu_hubung_id" class="col-sm-4 control-label">Gardu Hubung</label>
                    <div class="col-sm-8">
                      <select class="form-control selectallowclear" id="gardu_hubung_id" name="gardu_hubung_id" style="width: 100%;">
                        @foreach ($datanya["garduhubung"] as $item)
                          <option value="{{ $item->id }}">{{ $item->code }} - {{ $item->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="arus_hs_3_phs" class="col-sm-4 control-label">Arus HS. 3 PHS (kA)</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" name="arus_hs_3_phs" id="arus_hs_3_phs" placeholder="Masukan Arus HS. 3 PHS" required>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="teg_primer" class="col-sm-4 control-label">Tegangan Primer (kV)</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" name="teg_primer" id="teg_primer" placeholder="Masukan Tegangan Primer" required>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="teg_skunder" class="col-sm-4 control-label">Tegangan Sekunder (kV)</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" name="teg_skunder" id="teg_skunder" placeholder="Masukan Tegangan Sekunder" required>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="imp_trafo" class="col-sm-4 control-label">Impendasi Trafo (%)</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" name="imp_trafo" id="imp_trafo" placeholder="Masukan Impendasi Trafo" required>
                    </div>
                  </div>

                  <hr/>
                  <div class="row">
                    <div class="col-md-6">
                      <button type="button" id="btnUpload" class="btn btn-block btn-primary pull-left" style="width: auto;">Upload Data Penyulang</button>
                    </div>
                    <div class="col-md-6">
                      <button type="button" id="btnTemplate" class="btn btn-block btn-primary pull-right" style="width: auto;">Download Template</button>
                    </div>
                    <input id='fileUpload' type='file' style="display:none;"/>
                    <input id='datajson' type='hidden' name="datajson"/>
                  </div>
                  <br>
                  <div class="row">
                    <div class="col-md-12 table-responsive">
                      <table class="tabel table table-bordered" id="tbl_spot_penyulang">
                        <thead>
                          <tr>
                            <th>Header</th>
                            <th>Nama Tiang</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Position</th>
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
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
@stop

@section('js')

  <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/jszip.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/xlsx.js"></script> -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.15.1/xlsx.full.min.js"></script>

  <script>

    $(function(){

      var X = XLSX;
      var fileUpload = document.getElementById('fileUpload');
      var btnUpload = document.getElementById('btnUpload');
      var btnTemplate = document.getElementById('btnTemplate');
      
      var wb = XLSX.utils.book_new();
      wb.SheetNames.push("Test Sheet");

      var ws_data = [['Header', 'Name', 'Description', 'Type', 'Position']];
      var ws = XLSX.utils.aoa_to_sheet(ws_data);
      wb.Sheets["Test Sheet"] = ws;

      var wbout = XLSX.write(wb, {bookType:'xlsx',  type: 'binary'});
      
      function uploadFile(e) {
        var files = e.target.files;
        var f = files[0];
        {
            var reader = new FileReader();
            var name = f.name;
            reader.onload = function (e) {
                var data = e.target.result;
                var workbook = XLSX.read(data, { type: 'binary' });
                var result = {};
                workbook.SheetNames.forEach(function (sheetName) {
                    var roa = X.utils.sheet_to_row_object_array(workbook.Sheets[sheetName]);
                    if (roa.length > 0) {
                        result[sheetName] = roa;
                    }
                });
                var output_str = JSON.stringify(result, 2, 2);
                var output_arr = JSON.parse(output_str);
                var newtable = output_arr["Test Sheet"].map(function callback(value, id) {
                                    return "<tr><td>"+ value.Header +"</td><td>"+ value.Name +"</td><td>"+ (value.Description || "") +
                                            "</td><td>"+ value.Type +"</td><td>"+ value.Position +"</td></tr>";
                                });
                $("#datajson").val(JSON.stringify(output_arr["Test Sheet"]));

                $("#tbl_spot_penyulang tbody").html(newtable);
            }
            reader.readAsBinaryString(f);
        }
      }

      function openDialog() {
        document.getElementById('fileUpload').click();
      }

      function generateTemplate(e) {
        saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), 'test.xlsx');
      }

      function s2ab(s) { 
        var buf = new ArrayBuffer(s.length); //convert s to arrayBuffer
        var view = new Uint8Array(buf);  //create uint8array as viewer
        for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF; //convert to octet
        return buf;
      }

      if (fileUpload.addEventListener)
          fileUpload.addEventListener('change', uploadFile, false);

      if (btnUpload.addEventListener)
          btnUpload.addEventListener('click', openDialog);

      if (btnTemplate.addEventListener)
          btnTemplate.addEventListener('click', generateTemplate);

      $('#tbl_spot_penyulang').DataTable({
        "paging": false,
        "searching": false
      });

    });
    
   </script>
@stop