@extends('layout.main')

@section('title', 'Αποτελέσματα αναζήτησης')

@section('content')
<custom-search-component :params="{{ $params }}"></custom-search-component>
@endsection