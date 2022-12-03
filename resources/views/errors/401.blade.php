@extends('layout.main')

@section('title', 'Unauthorized')

@section('content')
<error-component code="401" message="Unauthorized"></error-component>
@endsection