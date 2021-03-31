@extends('layout.main')

@section('title', 'Service Unavailable')

@section('content')
<error-component code="503" message="Service Unavailable"></error-component>
@endsection