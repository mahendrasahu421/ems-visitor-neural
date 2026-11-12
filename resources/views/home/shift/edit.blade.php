@extends('layout.master')
@section('title', $title)
@section('main-section')

    <div class="page-wrapper">
        <div class="content">

            <div class="row">
                <div class="col-sm-12">

                    <div class="card">
                        <div class="card-header">

                            <!-- Breadcrumb -->
                            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                                <div class="my-auto mb-2">
                                    <h2 class="mb-1">{{ $title }}</h2>
                                </div>

                                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                                    <div class="mb-2">
                                        <a href="{{ route('masters.organisation.shift') }}"
                                            class="btn btn-primary d-flex align-items-center">
                                            <i class="ti ti-list-details me-2"></i> Shift List
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!-- /Breadcrumb -->

                        </div>

                        <div class="card-body">

                            <form method="POST" action="{{ route('masters.organisation.shift.update', $shift->id) }}"
                                class="needs-validation" novalidate>

                                @csrf
                                @method('PUT')

                                <div class="row">

                                    <!-- Shift Name -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Shift Name <span class="text-danger">*</span></label>
                                        <input type="text" name="shift_name" class="form-control"
                                            placeholder="Enter Shift Name"
                                            value="{{ old('shift_name', $shift->shift_name) }}" required>

                                        @error('shift_name')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Shift Code -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Shift Code <span class="text-danger">*</span></label>
                                        <input type="text" name="shift_code" class="form-control"
                                            placeholder="Enter Shift Code"
                                            value="{{ old('shift_code', $shift->shift_code) }}" required>

                                        @error('shift_code')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Start Time -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Start Time <span class="text-danger">*</span></label>
                                        <input type="time" name="start_time" class="form-control"
                                            value="{{ old('start_time', $shift->start_time) }}" required>

                                        @error('start_time')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- End Time -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">End Time <span class="text-danger">*</span></label>
                                        <input type="time" name="end_time" class="form-control"
                                            value="{{ old('end_time', $shift->end_time) }}" required>

                                        @error('end_time')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Break Duration -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Break Duration (in minutes)</label>
                                        <input type="number" name="break_duration" class="form-control"
                                            value="{{ old('break_duration', $shift->break_duration) }}"
                                            placeholder="e.g. 60">

                                        @error('break_duration')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Grace Period -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Grace Period (in minutes)</label>
                                        <input type="number" name="grace_period" class="form-control"
                                            value="{{ old('grace_period', $shift->grace_period ?? 15) }}"
                                            placeholder="e.g. 15">
                                        <span class="text-muted fs-11">Buffer allowed after start time before marking Late In</span>

                                        @error('grace_period')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Total Working Hours -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Total Working Hours</label>
                                        <input type="number" step="0.1" name="total_working_hours" class="form-control"
                                            value="{{ old('total_working_hours', $shift->total_working_hours) }}">

                                        @error('total_working_hours')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-control" required>
                                            <option value="Active"
                                                {{ old('status', $shift->status) == 'Active' ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="Inactive"
                                                {{ old('status', $shift->status) == 'Inactive' ? 'selected' : '' }}>
                                                Inactive</option>
                                        </select>

                                        @error('status')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Week-Off Days -->
                                    <div class="col-12 mb-3">
                                        <div class="p-3 border rounded-3 bg-light-subtle">
                                            <label class="form-label fw-semibold d-flex align-items-center gap-1 mb-1">
                                                <i class="ti ti-calendar-off text-primary"></i> Weekly Off Days (Customisable)
                                            </label>
                                            <p class="text-muted fs-12 mb-2">Select which days of the week are considered week-offs for this shift. Employees under this shift will have these days marked as <strong>W</strong> (Weekly Off).</p>
                                            <div class="d-flex flex-wrap gap-2">
                                                @php
                                                    $weekDays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                                    $rawCurrent = old('week_off_days', $shift->week_off_days ?? ['Sunday']);
                                                    $selectedWeekOffs = is_array($rawCurrent) ? $rawCurrent : (json_decode($rawCurrent, true) ?: ['Sunday']);
                                                @endphp
                                                @foreach ($weekDays as $day)
                                                    <div class="form-check form-check-inline p-0 m-0">
                                                        <input type="checkbox" class="btn-check" id="weekoff_{{ $day }}" name="week_off_days[]" value="{{ $day }}"
                                                            {{ in_array($day, (array)$selectedWeekOffs) ? 'checked' : '' }} autocomplete="off">
                                                        <label class="btn btn-outline-primary btn-sm px-3 py-1.5 rounded-pill" for="weekoff_{{ $day }}">
                                                            <i class="ti ti-calendar-event me-1"></i>{{ $day }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="d-flex justify-content-end mt-3">
                                    <a href="{{ route('masters.organisation.shift') }}" class="btn btn-light me-2">
                                        <i class="ti ti-arrow-left me-1"></i> Back
                                    </a>

                                    <button type="reset" class="btn btn-secondary me-2">
                                        <i class="ti ti-x me-1"></i> Cancel
                                    </button>

                                    <button class="btn btn-primary" type="submit">
                                        <i class="ti ti-device-floppy me-1"></i> Update Shift
                                    </button>
                                </div>

                            </form>

                        </div>

                    </div>

                </div>
            </div>

        </div>

        <!-- Footer -->
        <x-footer />

    </div>

@endsection
