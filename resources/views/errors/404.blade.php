@extends('layout.main')

@section('title', 'Not found')

@section('content')
<error-component code="404" message="Not found"></error-component>
@endsection