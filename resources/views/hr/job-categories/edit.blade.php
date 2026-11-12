<!-- Page Wrapper -->
@extends('hr.layout.layout')
@section('title', $title)
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1100;">


    @if ($errors->any())
        <div class="toast align-items-center text-white bg-danger border-0 show" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif

</div>
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

            </div>
            <!-- /Breadcrumb -->
            <div class="card p-5">
                <div class="row">
                    <form action="{{ route('hr.categories.update', $jobCategory->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Category Name *</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $jobCategory->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <input type="text" name="type" class="form-control"
                                value="{{ old('type', $jobCategory->type) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Parent Category</label>
                            <select name="parent_id" class="form-control">
                                <option value="">-- None --</option>
                                @foreach ($parents as $parent)
                                    <option value="{{ $parent->id }}"
                                        {{ old('parent_id', $jobCategory->parent_id) == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="Active"
                                    {{ old('status', $jobCategory->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Inactive"
                                    {{ old('status', $jobCategory->status) == 'Inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>



                </div>
            </div>


        </div>
 <x-footer />
    </div>
    <!-- /Page Wrapper -->

    <!-- Add categories -->

    <!-- /Add categories -->



@endsection

@section('script')
    @parent
    <!-- <script src="{{ asset('datatables/jquery.min.js') }}"></script> -->
    <script src="https://cdn.datatables.net/2.1.5/js/dataTables.js"></script>

    <script>
        $(document).ready(function(e) {
            new DataTable('#jobs-list', {


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
                delay: 4000
            }); // 4 sec
            toast.show();
        })
    });
</script>
