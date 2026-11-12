@extends('employee.layout.layout')
@section('title', $title)
@section('main-section')
<style>
    .select2-selection__clear{
        display: none !important;
    }
</style>
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <div class="page-wrapper">
        <div class="content">
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">{{ $title }}</h2>
                </div>
                <div class="mb-2">
                    <a href="{{ route('employee.daily.reports.view') }}" class="btn btn-primary d-flex align-items-center">
                        <i class="ti ti-list me-2"></i>View DR
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('daily-report.store') }}" id="dailyReportForm">
                        @csrf

                        <div class="d-flex mb-4 justify-content-between">
                            <div>
                                <label class="form-label mb-1">Change date for filling old DR's</label>
                                <input type="date" class="form-control mt-1" name="report_date" id="reportDate"
                                    value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d', strtotime('-2 days')) }}"
                                    max="{{ date('Y-m-d') }}">
                            </div>

                            <div class="mt-4">
                                <button type="button" class="btn btn-primary" id="addRowBtn">
                                    <i class="ti ti-plus"></i> Add Work
                                </button>
                            </div>
                        </div>

                        <script>
                            const projectList = `
                        @foreach ($projects as $p)
                            <option value="{{ $p->project_id ?? $p->id }}">{{ $p->project_name }}</option>
                        @endforeach
                        `;
                        </script>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th width="30%">Project Name</th>
                                        <th>Description</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Hours</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>

                                <tbody id="projectRows">
                                    @for ($i = 0; $i < 2; $i++)
                                        <tr>
                                            <td>
                                                <select class="form-select project-select" name="project_id[]">
                                                    <option value="">Select Project</option>
                                                    @foreach ($projects as $p)
                                                        <option value="{{ $p->project_id ?? $p->id }}">{{ $p->project_name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>

                                            <td>
                                                <textarea class="form-control description" style="resize: both !important;" name="description[]" rows="2"></textarea>
                                            </td>

                                            <td>
                                                <input type="time" class="form-control start-time" name="start_time[]"
                                                    step="300">
                                            </td>
                                            <td>
                                                <input type="time" class="form-control end-time" name="end_time[]"
                                                    step="300">
                                                <div class="text-danger time-error small mt-1" style="display:none;"></div>
                                            </td>

                                            <td><strong class="hours-text">0 Hr : 0 Mi</strong></td>

                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm delete-row">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-end">Total</th>
                                        <th><strong id="totalHours">Hr : Mi</strong></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="reset" class="btn btn-secondary me-2">
                                <i class="ti ti-x me-1"></i> Cancel
                            </button>

                            <button class="btn btn-primary" type="button" data-bs-toggle="modal"
                                data-bs-target="#save_modal">
                                <i class="ti ti-device-floppy me-1"></i> Save
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <x-footer />

        <div class="modal fade" id="save_modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-body text-center p-4">
                        <div class="mb-3">
                            <i class="ti ti-check text-primary" style="font-size: 50px;"></i>
                        </div>

                        <h5 class="fw-bold mb-2">Confirm Save</h5>
                        <p class="text-muted mb-4">Are you sure you want to save this Daily Report?</p>

                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                                data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <button type="button" id="confirmSaveBtn" class="btn btn-primary rounded-pill px-4">
                                Yes, Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="delete_modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <span class="avatar avatar-xl bg-transparent-danger text-danger mb-3">
                            <i class="ti ti-trash-x fs-36"></i>
                        </span>
                        <h4 class="mb-1">Confirm Delete</h4>
                        <p class="mb-3">You want to delete this item, this can't be undone.</p>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="javascript:void(0);" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
                            <a href="javascript:void(0);" id="confirmDeleteBtn" class="btn btn-danger">Yes, Delete</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="cannot_delete_modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-body text-center p-4">
                        <i class="ti ti-alert-triangle text-warning fs-36 mb-3"></i>
                        <h5>Cannot Delete</h5>
                        <p class="mb-0">You cannot delete the last remaining row.</p>
                        <button type="button" class="btn btn-primary mt-3" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after_scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {

            function calculateRow(row) {
                let start = $(row).find('.start-time').val();
                let end = $(row).find('.end-time').val();
                let display = $(row).find('.hours-text');
                let errorBox = $(row).find('.time-error');

                if (!start || !end) {
                    display.text('0 Hr : 0 Mi');
                    errorBox.hide().text('');
                    $(row).find('.start-time, .end-time').removeClass('is-invalid');
                    return 0;
                }

                let [sH, sM] = start.split(':').map(Number);
                let [eH, eM] = end.split(':').map(Number);

                let diff = (eH * 60 + eM) - (sH * 60 + sM);

                // ✅ Same time OR End before Start -> Error
                if (start === end) {
                    errorBox.text('Start Time and End Time cannot be same.').show();
                    $(row).find('.start-time, .end-time').addClass('is-invalid');
                    display.text('0 Hr : 0 Mi');
                    return 0;
                } else if (diff < 0) {
                    errorBox.text('End Time must be after Start Time.').show();
                    $(row).find('.start-time, .end-time').addClass('is-invalid');
                    display.text('0 Hr : 0 Mi');
                    return 0;
                } else {
                    errorBox.hide().text('');
                    $(row).find('.start-time, .end-time').removeClass('is-invalid');
                }

                let hr = Math.floor(diff / 60);
                let mn = diff % 60;

                display.text(hr + ' Hr : ' + mn + ' Mi');
                return diff;
            }

            function calculateTotal() {
                let total = 0;
                $('#projectRows tr').each(function() {
                    total += calculateRow(this);
                });
                let hr = Math.floor(total / 60);
                let mn = total % 60;
                $('#totalHours').text(hr + ' Hr : ' + mn + ' Mi');
            }

            function hasTimeErrors() {
                return $('#projectRows .time-error:visible').length > 0;
            }

            function initializeSelect() {
                $('.project-select').select2({
                    width: '100%',
                    placeholder: 'Select Project',
                    allowClear: true
                });
            }

            initializeSelect();

            $(document).on('input change', '.start-time, .end-time', function() {
                calculateTotal();
            });

            let rowToDelete = null;

            $(document).on('click', '.delete-row', function() {
                if ($('#projectRows tr').length === 1) {
                    $('#cannot_delete_modal').modal('show');
                } else {
                    rowToDelete = $(this).closest('tr');
                    $('#delete_modal').modal('show');
                }
            });

            $('#addRowBtn').on('click', function() {
                let newRow = $('<tr>' +
                    '<td><select class="form-select project-select" name="project_id[]"><option value="">Select Project</option>' +
                    projectList + '</select></td>' +
                    '<td><textarea class="form-control description" name="description[]" rows="2"></textarea></td>' +
                    '<td><input type="time" class="form-control start-time" name="start_time[]" step="300"></td>' +
                    '<td><input type="time" class="form-control end-time" name="end_time[]" step="300">' +
                    '<div class="text-danger time-error small mt-1" style="display:none;"></div></td>' +
                    '<td><strong class="hours-text">0 Hr : 0 Mi</strong></td>' +
                    '<td class="text-center"><button type="button" class="btn btn-danger btn-sm delete-row"><i class="ti ti-trash"></i></button></td>' +
                    '</tr>');
                $('#projectRows').append(newRow);
                initializeSelect();
            });

            $('#confirmSaveBtn').on('click', function() {

                calculateTotal();

                // Stop submission when any row has a time error.
                if (hasTimeErrors()) {
                    $('#save_modal').modal('hide');
                    return;
                }

                let btn = $(this);

                // Button disable
                btn.prop('disabled', true);

                // Change button text
                btn.html(`
                    <span class="spinner-border spinner-border-sm me-1"></span>
                    Saving...
                `);

                // Submit form
                $('#dailyReportForm').submit();
            });

            $('#confirmDeleteBtn').on('click', function() {
                if (rowToDelete) {
                    rowToDelete.remove();
                    calculateTotal();
                    rowToDelete = null;
                    $('#delete_modal').modal('hide');
                }
            });

            calculateTotal();
        });
        const emojiRegex = /\p{Extended_Pictographic}/gu;

        $(document).on("input paste", ".description", function(e) {

            let value = e.type === "paste" ?
                (e.originalEvent || e).clipboardData.getData("text") :
                this.value;

            if (emojiRegex.test(value)) {

                if (e.type === "paste") {
                    e.preventDefault();
                    document.execCommand(
                        "insertText",
                        false,
                        value.replace(emojiRegex, "")
                    );
                } else {
                    this.value = value.replace(emojiRegex, "");
                }

                toastr.warning(
                    "Emojis are not allowed in the Work Description."
                );
            }
        });
    </script>
@endpush
