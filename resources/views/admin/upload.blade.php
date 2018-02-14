@extends('layouts.default')

@section('content')
<h3>Subir Archivo</h3>
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="row">
        <label for="file">Archivo</label>
        <input type="file" name="file" id="file">
    </div>
    <div class="row">
        <label for="description">Descripcion</label>
        <input type="text" name="description" id="description">
    </div>
    <button class="button-primary">Guardar en Drive</button>
</form>
@stop