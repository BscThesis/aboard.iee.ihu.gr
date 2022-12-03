@extends('layout.main')

@section('title', 'Too Many Requests')

@section('content')
<error-component code="429" message="Too Many Requests"></error-component>
@endsection