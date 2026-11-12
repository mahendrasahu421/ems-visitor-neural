<!-- Page Wrapper -->
@extends('hr.layout.layout')
@section('title', $title)

@section('main-section')
    <div class="page-wrapper">
        <div class="content">

            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">{{ $title }}</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">

                            <li class="breadcrumb-item">
                                {{ $title }}
                            </li>
                            {{-- <li class="breadcrumb-item active" aria-current="page">categories Grid</li> --}}
                        </ol>
                    </nav>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">


                    <div class="mb-2">
                        <a href="{{ route('hr.jobs.index') }}" class="btn btn-primary d-flex align-items-center"><i
                                class="ti ti-circle-plus me-2"></i>Jobs
                            List</a>
                    </div>

                </div>
            </div>
            <!-- /Breadcrumb -->
            <!-- /Add Leaves -->
            <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />


            <div class="container mt-4">
                <div class="card shadow-sm">

                    <div class="card-body">
                        <form action="{{ route('hr.jobs.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label>Job Title*</label>
                                    <input type="text" name="title" class="form-control" value="{{ old('title') }}"
                                        required>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>Functional Area</label>
                                    <select name="category_id" class="form-control select">
                                        <option value="">-- Select Category --</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>Skills</label>
                                    <select name="skills[]" class="form-control select2" id="skills" multiple>
                                        <option value="">-- Select Category --</option>

                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>No of positions*</label>
                                    <input type="number" name="positions" class="form-control"
                                        value="{{ old('positions', 1) }}" required>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>Job Type*</label>
                                    <select name="job_type" class="form-control select" required>
                                        <option value="">-- Select Job Type --</option>
                                        @foreach ($jobCategories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="col-md-4 mb-3">
                                    <label>CTC From (In Lac/Year)</label>
                                    <input type="number" step="0.01" name="ctc_from" class="form-control">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label>CTC To (In Lac/Year)</label>
                                    <input type="number" step="0.01" name="ctc_to" class="form-control">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>Min Experience*</label>
                                    <input type="number" name="min_experience" class="form-control">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>Max Experience*</label>
                                    <input type="number" name="max_experience" class="form-control">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Locations</label>
                                    <select name="locations[]" id="locations" class="form-control select2"
                                        multiple></select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Keywords</label>
                                    <input type="text" name="keywords" class="form-control">
                                </div>


                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Job Description*</label>
                                    <textarea id="job_description" name="description" class="form-control" required>
        {{ old('description') }}
    </textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Status</label>
                                    <select name="status" class="form-control select">
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-check mt-3">
                                <input type="checkbox" class="form-check-input" id="share_social" name="share_social"
                                    value="1">
                                <label class="form-check-label" for="share_social">Also share on Social Media</label>
                            </div>

                            <button type="submit" class="btn btn-primary mt-3">Save Job</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
 <x-footer />
    </div>




@endsection

@section('script')
    @parent
    <!-- <script src="{{ asset('datatables/jquery.min.js') }}"></script> -->
    <script src="https://cdn.datatables.net/2.1.5/js/dataTables.js"></script>


    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#job_description'))
            .catch(error => {
                console.error(error);
            });
    </script>
    <script>
        $(document).ready(function() {
            $('#skills').select2({
                placeholder: 'Enter Skills',
                minimumInputLength: 2,
                ajax: {
                    url: '{{ route('skills.search') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#locations').select2({
                placeholder: 'Enter City',
                minimumInputLength: 2,
                ajax: {
                    url: '{{ route('districts.search') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                }
            });
        });
    </script>
@endsection

@section('styles')
    @parent
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.5/css/dataTables.dataTables.css" />
@endsection

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var toastElList = [].slice.call(document.querySelectorAll('.toast'))
        toastElList.map(function(toastEl) {
            var toast = new bootstrap.Toast(toastEl, {
                delay: 30000
            }); // 4 sec
            toast.show();
        })
    });
</script>
