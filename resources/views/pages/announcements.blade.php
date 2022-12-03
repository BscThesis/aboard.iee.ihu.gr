@extends('layout.main')

@section('title', 'Ανακοινώσεις')

@section('content')
<announcements-component-bulma :user="{{ json_encode(Auth::user()) }}"></announcements-component-bulma>
@endsection
