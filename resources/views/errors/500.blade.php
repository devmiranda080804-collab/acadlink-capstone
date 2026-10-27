@extends('errors.layout')

@section('code', '500')
@section('tone', 'danger')
@section('title', 'System Error')
@section('icon')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3l9.5 17h-19z"/><path d="M12 10v4M12 17.5h.01"/></svg>@endsection
@section('heading')
Something went wrong on our side
@endsection
@section('message')
This is not your fault. The system ran into a problem while loading this page. Please try again in a moment.
@endsection
@section('primary')
    <button type="button" class="btn btn-primary" onclick="location.reload()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 11a8 8 0 10-2.3 5.7M20 4v7h-7"/></svg>
        Refresh Page
    </button>
@endsection
