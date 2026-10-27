@extends('errors.layout')

@section('code', '503')
@section('tone', 'info')
@section('title', 'Under Maintenance')
@section('icon')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a4 4 0 00-5.4 5.4L3 18v3h3l6.3-6.3a4 4 0 005.4-5.4l-2.5 2.5-2.4-.6-.6-2.4z"/></svg>@endsection
@section('heading')
The system is being updated
@endsection
@section('message')
The CBMA System is temporarily unavailable for maintenance. Please try again in a few minutes.
@endsection
@section('primary')
    <button type="button" class="btn btn-primary" onclick="location.reload()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 11a8 8 0 10-2.3 5.7M20 4v7h-7"/></svg>
        Refresh Page
    </button>
@endsection
