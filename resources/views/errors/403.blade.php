@extends('errors.layout')

@section('code', '403')
@section('tone', 'warning')
@section('title', 'No Access')
@section('icon')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/></svg>@endsection
@section('heading')
You don't have access to this page
@endsection
@section('message')
This page is for a different role, or you don't have permission to open it. If you think you should have access, please ask your Program Head or the administrator.
@endsection

