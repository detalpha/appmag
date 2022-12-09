@extends('adminlte::page')

@section('title', 'Tambah Gardu')

@push('css')

@section('content_header')
    <h1>
      Master Gardu
      <small>Edit</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{url('home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{url('gardu')}}">Master Gardu</a></li>
      <li class="active">Edit Gardu</li>
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
          <form role="form" class="form-horizontal" method="POST" action="{{ url('master/gardu/'.$data->id) }}">
            {{csrf_field()}}
            <input type="hidden" name="_method" value="PUT" class="form-control">
            <div class="box-body">
              <div class="row">
                <div class="col-md-3"></div>
                <!-- left column -->
                <div class="col-md-6">
                  
                  <div class="form-group">
                    <label for="code" class="col-sm-3 control-label">Kode Gardu</label>
                    <div class="col-sm-9">
                    <input type="text" class="form-control" name="code" id="code" placeholder="Masukan Kode Gardu" value="{{ $data->code }}" required>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="name" class="col-sm-3 control-label">Nama Gardu</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" name="name" id="name" placeholder="Masukan Nama Gardu" value="{{ $data->name }}" required>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="type" class="col-sm-3 control-label">Tipe</label>
                    <div class="col-sm-9">
                      <select class="form-control select2" name="type" style="width: 100%;">
                      <option {{ ($data->gi_type == 1 ? "selected":"") }} value="1">Gardu Induk</option>
                      <option {{ ($data->gi_type == 2 ? "selected":"") }} value="2">Gardu Hubung</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="name" class="col-sm-3 control-label">Koordinat</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" name="coordinate" id="us2-lon" placeholder="E.g. N1.37797 E99.27430 or 1.37797 99.27430" value="{{ $data->latitude }} {{ $data->longitude }}" required>
                    </div>
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