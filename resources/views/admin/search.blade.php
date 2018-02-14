@extends('layouts.default')

@section('content')
<h3>Buscar Archivo</h3>
<form method="GET">
    <div class="row">
        <label for="query">Palabra clave</label>
        <input type="text" name="query" id="query" value="{{ $query }}">
    </div>
    <button class="button-primary">Buscar</button>
</form>
@if(!empty($files))
<ul id="files">
    <h4>Coincidencias: {{ $query }}</h4>
    @foreach($files as $file)
    <li>
        <div class="file">

            <div class="file-title">
                <img src="{{ $file->iconLink }}">
                {{ $file->name }}
            </div>
            <div class="file-modified">
                last modified: {{  Carbon\Carbon::parse($file->modifiedTime)->format('d-m-Y i')}}
            </div>
            <div class="file-links">
                <a href="{{ $file->webViewLink }}">Ver</a>
                @if(!empty($webContentLink))
                <a href="{{ $file->webContentLink }}">Descargar</a>
                @endif
                <a href="/delete/{{ $file->id }}">Eliminar</a>
            </div>
        </div>
    </li>
    @endforeach
</ul>
@else
No hay coincidencias para tu busqueda: {{ $query }}
@endif
@stop