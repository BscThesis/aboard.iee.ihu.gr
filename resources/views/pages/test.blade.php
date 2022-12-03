@extends('layout.main')

@section('title', 'Test')

@section('content')
<editor-component :value='{!! json_encode("<p>Foobar</p>") !!}'></editor-component>
@endsection