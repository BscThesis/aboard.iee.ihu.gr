@extends('layout.main')

@section('title', 'Ανακοίνωση')

@section('content')
<announcement-component-bulma :id="{{ $id }}"></announcement-component-bulma>
@endsection