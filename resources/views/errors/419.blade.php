@extends('errors.layout')

@section('code', '419')
@section('tone', 'warning')
@section('title', 'Session Timed Out')
@section('icon')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>@endsection
@section('heading')
Your session timed out
@endsection
@section('message')
The page was open for a long time, so it stopped being secure to send. Please refresh the page and try again. Anything you typed may need to be entered again.
@endsection
@section('primary')
    <button type="button" class="btn btn-primary" onclick="location.assign(document.referrer || location.href)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 11a8 8 0 10-2.3 5.7M20 4v7h-7"/></svg>
        Reload the Page
    </button>
@endsection
