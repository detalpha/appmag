@extends('adminlte::page')

@section('title', 'Data Penyulang')

@push('css')

@section('content_header')
    <h1>
      Penyulang
      <small>Data</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{url('home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Penyulang</li>
    </ol>
@stop

@section('content')
    @if (\Session::has('success'))
      <div class="alert alert-success">
        <p>{{ \Session::get('success') }}</p>
      </div><br />
     @endif
     
    <div class="box">
      <!-- /.box-header -->
      <div class="box-body table-responsive">
        <a type="button" href="{{ route('penyulang.create') }}" style="margin-bottom: 10px;" class="btn btn-info"><i class="fa fa-plus"></i></a>
        <table id="tabel1" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>Code</th>
              <th>Nama</th>
              <th>Gardu Induk</th>
              <th>Gardu Hubung</th>
              <th>Arus HS. 3 PHS</th>
              <th>Tegangan Primer</th>
              <th>Tegangan Sekunder</th>
              <th>Impendasi Trafo</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
      <!-- /.box-body -->
    </div>
@stop

@push('js')

@section('js')
    <script> 
      $(function () {
        //$('#tabel1').DataTable();
        var table = $('#tabel1').DataTable({
          processing: true,
          serverSide: true,
          "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
          ajax: {
            "url": "{{ route('penyulangjson') }}",
            "data": function ( d ) {
               //d.unit = $("[name='unit']").val();
            }
          },
          columns: [
            { data: 'code', name: 'code'},
            { data: 'name', name: 'name'},
            { data: 'gardu_induk_name', name: 'gardu_induk_name'},
            { data: 'gardu_hubung_name', name: 'gardu_hubung_name'},
            { data: 'arus_hs_3_phs', name: 'arus_hs_3_phs'},
            { data: 'teg_primer', name: 'teg_primer'},
            { data: 'teg_skunder', name: 'teg_skunder'},
            { data: 'imp_trafo', name: 'imp_trafo'},
            { data: 'action1', name: 'action1', orderable: false, width: '75'}
          ],
          "order": [[ 1, "ASC" ]]
        });
      })

    </script>
@stop