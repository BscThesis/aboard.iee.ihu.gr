@extends('layout.main')

@section('title', 'Δημιουργία ανακοίνωσης')

@section('content')
    <create-announcement-component-bulma :user="{{ json_encode(Auth::user()) }}"></create-announcement-component-bulma>
@endsection
