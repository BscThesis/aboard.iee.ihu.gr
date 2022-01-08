@extends('layout.main')

@section('title', 'Ανακοίνωση')

@section('content')
<announcement-component-bulma :id="{{ $id }}" :user="{{ json_encode(Auth::user()) }}"></announcement-component-bulma>
@endsection
