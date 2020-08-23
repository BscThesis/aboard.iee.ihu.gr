@extends('layout.main')

@section('title', 'Επεξεργασία ανακοίνωσης')

@section('content')
<create-announcement-component-bulma :id="{{ $id }}"></create-announcement-component-bulma>
@endsection