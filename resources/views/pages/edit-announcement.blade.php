@extends('layout.main')

@section('title', 'Επεξεργασία ανακοίνωσης')

@section('content')
<create-announcement-component-bulma :id="{{ $id }}" :user="{{ json_encode(Auth::user()) }}"></create-announcement-component-bulma>
@endsection
