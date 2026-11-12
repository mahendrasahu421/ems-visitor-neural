@extends('layout.master')
@section('title', $title ?? 'Add Project')

@section('main-section')
    <div class="page-wrapper">
        <div class="content">
            <div class="alert alert-info">
                To add a project, please use the invoice project form.
                <a href="{{ route('invoice.projects.create') }}" class="alert-link">Open Invoice Project Form</a>
            </div>
        </div>
        <x-footer />
    </div>
@endsection
