@extends('adminlte::page')

@section('title', 'Data Gardu')

@push('css')

@section('content_header')
    <h1>
      Gardu
      <small>Data</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{url('home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Gardu</li>
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
      <div class="box-body">
        <a type="button" href="{{ route('gardu.create') }}" style="margin-bottom: 10px;" class="btn btn-info"><i class="fa fa-plus"></i></a>
        <table id="tabel1" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>Nama Gardu</th>
              <th>Tipe Gardu</th>
              <th>Posisi</th>
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
            "url": "{{ route('mstgijson') }}",
            "data": function ( d ) {
               //d.unit = $("[name='unit']").val();
            }
          },
          columns: [
            { data: 'name', name: 'name'},
            { data: 'gi_type', name: 'gi_type'},
            { data: 'koordinat', name: 'koordinat'},
            { data: 'action1', name: 'action1', orderable: false, width: '75'}
          ],
          "order": [[ 1, "ASC" ]]
        });
      })

    </script>
@stop