@extends('layout.main')

@section('title', 'Αναζήτηση')

@section('content')
<search-byauthor-component-bulma :id="{{ $id }}"></search-bytag-component-bulma>
@endsection