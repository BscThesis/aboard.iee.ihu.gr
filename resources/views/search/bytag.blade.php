@extends('layout.main')

@section('title', 'Αναζήτηση')

@section('content')
<search-bytag-component-bulma :id="{{ $id }}"></search-bytag-component-bulma>
@endsection