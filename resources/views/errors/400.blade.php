@extends('layout.main')

@section('title', 'Bad Request')

@section('content')
<error-component code="400" message="Bad Request"></error-component>
@endsection