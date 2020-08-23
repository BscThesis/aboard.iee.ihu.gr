@extends('layout.main')

@section('title', 'Server Error')

@section('content')
<error-component code="500" message="Server Error"></error-component>
@endsection