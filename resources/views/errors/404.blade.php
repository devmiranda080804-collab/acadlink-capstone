@extends('errors.layout')

@section('code', '404')
@section('tone', 'info')
@section('title', 'Page Not Found')
@section('icon')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3M8.5 11h5"/></svg>@endsection
@section('heading')
We couldn't find that page
@endsection
@section('message')
The link may be wrong, or the page or file may have been moved or removed.
@endsection

