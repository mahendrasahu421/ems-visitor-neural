@extends('layout.master')
@section('title', 'Email Preview')

@section('main-section')
    <div class="page-wrapper">
        <div class="content">
            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">{{ $title }}</h2>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                    <div class="mb-2">
                        <a href="{{ route('email-templates.create') }}"
                            class="btn btn-primary d-flex align-items-center">
                            <i class="ti ti-circle-plus me-2"></i> Add Email Template
                        </a>
                    </div>
                </div>
            </div>
            <!-- /Breadcrumb -->
            <div class="row">
                <div class="col-sm-12">
                    <div id="emailPreview"
                        style="border:1px solid #ddd; max-width:600px; margin:auto; background:#fff; font-family:Arial,sans-serif;">
                        <div
                            style="background:#332C6D; color:#fff; padding:15px; display:flex; justify-content:space-between; align-items:center;">

                            <!-- LEFT : Heading Text -->
                            <div style="text-align:left;">
                                <h3 id="previewSubjectHeader" style="margin:0; font-size:18px;" class="text-white">
                                    {{ $template->template_key }}
                                </h3>
                            </div>

                            <!-- RIGHT : Company Logo -->
                            <div style="text-align:right;">
                                @foreach ($compneyDetails as $company)
                                    <img src="{{ asset('uploads/company/' . $company->company_logo) }}" alt="Logo"
                                        width="100" style="margin-left:10px;">
                                @endforeach
                            </div>
                        </div>
                        <div style="padding:20px; color:#333; line-height:1.5; font-size:14px;">
                            {!! $template->body !!}
                        </div>
                        <div style="background:#f1f1f1; padding:15px; font-size:12px; text-align:center; color:#555;">
                             {{ $company->address }}
                        </div>
                    </div>

                    {{-- <div class="mt-3 text-center">
                        <a href="{{ url('settings.email-template.preview.pdf', $template->id) }}"
                            class="btn btn-success">Download
                            PDF</a>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
@endsection
