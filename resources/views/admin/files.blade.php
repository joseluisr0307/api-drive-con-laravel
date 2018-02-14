@extends('layouts.default')

@section('content')
<h3>Listado de archivos</h3>
<ul id="files">
    @foreach($files as $file)
    <li>
        <div class="file">

            <div class="file-title">
                <img src="{{ $file->iconLink }}">
                {{ $file->name }}
            </div>
            <div class="file-modified">
                last modified: {{ Carbon\Carbon::parse($file->modifiedTime)->format('d-m-Y i') }}
            </div>
            <div class="file-links">
                <a href="{{ $file->webViewLink }}">Ver</a>
                @if(!empty($file->webContentLink))
                <a href="{{ $file->webContentLink }}">Descargar</a>
                @endif
                <a href="/delete/{{ $file->id }}">Eliminar</a>
            </div>
        </div>
    </li>
    @endforeach
</ul>
@stop