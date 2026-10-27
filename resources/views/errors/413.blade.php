@extends('errors.layout')

@section('code', '413')
@section('tone', 'warning')
@section('title', 'File Too Large')
@section('icon')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 3H6a2 2 0 00-2 2v14a2 2 0 002 2h12a2 2 0 002-2V9z"/><path d="M14 3v6h6M12 12v5M12 19.5h.01"/></svg>@endsection
@section('heading')
The file is too large
@endsection
@section('message')
The file you tried to upload is bigger than the system allows. Please upload a smaller file, for example by saving it as a PDF or compressing its images.
@endsection

