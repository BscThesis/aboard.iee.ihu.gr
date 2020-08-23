@extends('layout.main')

@section('title', 'Δε βρέθηκε')

@section('content')
<error-component code="403" message="Forbidden"></error-component>
@endsection