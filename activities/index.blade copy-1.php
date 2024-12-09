@extends('Company.layouts.app')
@section('activities-active', 'active')
@section('title', __('Activities'))
@push('styles')
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner card">
            <div class="dashboard_body">
                <div class="activites-datas">
                    <div class="comp-top">
                        <a href="{{ route('company.activities.add') }}" class="ads-btn">
                            <span><i class="fa-solid fa-plus"></i></span>Create New
                        </a>
                        <a href="{{ route('company.activities.bulkbulkupload') }}" class="btn btn-secondary">
                            <span><i class="fa-solid fa-plus"></i></span>Bulk Upload
                        </a>
                        <button type="button" class="btn btn-primary copy_data" data-bs-toggle="modal"
                            data-bs-target="#myModal">Copy</button>
                        <a href="{{ route('company.activities.export') }}" class="ads-btn">
                            <span><i class="fa fa-download" aria-hidden="true"
                                    title="Download Activites Data in Excel"></i></span>
                        </a>
                    </div>
                    <div class="comp-top">
                        <div class="col-lg-12">
                            <form id="filter-form" class="d-flex">
                                <div class="col-lg-4">
                                    <label for="project">Project</label>
                                    <select class="form-control mySelect2" name="from_project" id="from_project">
                                        <option value="">----Select Project----</option>
                                        {{ getProject('$data->project_id') }}
                                    </select>
                                    @if ($errors->has('project'))
                                        <div class="error">{{ $errors->first('project') }}</div>
                                    @endif
                                </div>
                                <div class="col-lg-4">
                                    <label for="subproject">Sub Project</label>
                                    <select class="form-control mySelect2" name="from_subproject" id="from_subproject">
                                        <option value="">----Select SubProject----</option>
                                    </select>
                                    @if ($errors->has('subproject'))
                                        <div class="error">{{ $errors->first('subproject') }}</div>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="comp-body">
                        <div class="structuretable">
                            <div class="structure_head">
                                <div class="structureh_sing">
                                    <p>#</p>
                                </div>
                                <div class="structureh_sing activity_strbox">
                                    <p>Activities</p>
                                </div>
                                <div class="structureh_sing">
                                    <p>Unit</p>
                                </div>
                                <div class="structureh_sing">
                                    <p>Qty</p>
                                </div>
                                <div class="structureh_sing">
                                    <p>Rate</p>
                                </div>
                                <div class="structureh_sing">
                                    <p>Amount</p>
                                </div>
                                <div class="structureh_sing">
                                    <p>Start Date</p>
                                </div>
                                <div class="structureh_sing">
                                    <p>End Date</p>
                                </div>
                                <div class="structureh_sing">
                                    <p>Action</p>
                                </div>
                            </div>
                            <div class="accordion companyig_box" id="constGroup">
                                {{-- @include('Company.activities.include.list') --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Copy Activities</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('company.activities.addCopyActivites') }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="to_project" class="form-label">Project</label>
                                <select class="form-control mySelect2" name="to_project" id="to_project">
                                    <option value="">----Select Project----</option>
                                    {{ getProject('$data->project_id') }}
                                </select>
                                @if ($errors->has('to_project'))
                                    <div class="text-danger">{{ $errors->first('to_project') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="to_subproject" class="form-label">Sub Project</label>
                                <select class="form-control mySelect2" name="to_subproject" id="to_subproject">
                                    <option value="">----Select SubProject----</option>
                                </select>
                                @if ($errors->has('to_subproject'))
                                    <div class="text-danger">{{ $errors->first('to_subproject') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select class="form-control activiesType" name="type" id="type">
                                    <option value="">---Select Activities---</option>
                                    <option value="heading"
                                        {{ isset($data) && $data->type == 'heading' ? 'selected' : '' }}>Heading
                                    </option>
                                    <option value="activities"
                                        {{ isset($data) && $data->type == 'activities' ? 'selected' : '' }}>Activities
                                    </option>
                                </select>
                                @if ($errors->has('type'))
                                    <div class="text-danger">{{ $errors->first('type') }}</div>
                                @endif
                            </div>
                            <input type="hidden" value="{{ isset($data) ? $data->additional_fields : '' }}"
                                id="additional_fields" class="additional_fields">
                        </div>
                        <div class="col-md-6">
                            <div class="activities"></div>
                        </div>
                        <div class="activitiesDatas"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.comp-body').hide();

            $('#from_project').change(function() {
                var projectId = $(this).val();
                $.get(baseUrl + 'company/activities/subprojects/' + projectId, function(data) {
                    $('#from_subproject').empty();
                    $.each(data, function(key, value) {
                        $.each(value.sub_project, function(subkey, subvalue) {
                            $('#from_subproject').append('<option value="' +
                                subvalue.id + '">' + subvalue.name + '</option>'
                            );
                        });
                    });
                });
            });

            $('#filter-form').on('change', function() {
                var project = $('#from_project').val();
                var subproject = $('#from_subproject').val();
                $.ajax({
                    url: "{{ route('company.activities.copyActivites') }}",
                    type: "GET",
                    data: {
                        project: project,
                        subproject: subproject
                    },
                    success: function(response) {
                        $('.comp-body').show();
                        $("#constGroup").html(response);
                    },
                    error: function(error) {
                        alert(error);
                    }
                });
            });

            $('#to_project').change(function() {
                var projectId = $(this).val();
                $.get(baseUrl + 'company/activities/subprojects/' + projectId, function(data) {
                    $('#to_subproject').empty();
                    $.each(data, function(key, value) {
                        $.each(value.sub_project, function(subkey, subvalue) {
                            $('#to_subproject').append('<option value="' + subvalue
                                .id + '">' + subvalue.name + '</option>');
                        });
                    });
                });
            });

            $('.activiesType').on('change', function() {
                var type = $(this).val();
                if (type === 'heading') {
                    $(".activities").html(generateTypeNameFieldHtml());
                } else {
                    $(".activities").html(activiteFieldHtml());
                }
            });

            function generateTypeNameFieldHtml() {
                return `<div class="position-relative form-group">
                            <label for="activities">Activities</label>
                            <input name="activities" id="activities" class="form-control" placeholder="Enter Activities">
                            @if ($errors->has('activities'))
                                <div class="error">{{ $errors->first('activities') }}</div>
                            @endif
                        </div>`;
            }

            function activiteFieldHtml() {
                return `<div class="position-relative form-group">
                            <label for="activities">Heading</label>
                            <select class="form-control" name="heading" id="heading">
                                <option value="">----Select Heading/Sub-Heading----</option>
                                {{ getchildActivites('projectId', 'subproject') }}
                            </select>
                        </div>`;
            }

            $(document).on("click", ".copy_data", function() {
                var selectedItems = [];
                $('input[type="checkbox"]:checked').each(function() {
                    selectedItems.push($(this).val());
                });

                $.ajax({
                    type: "post",
                    url: "{{ route('company.activities.findId') }}",
                    data: {
                        selectedItems: selectedItems
                    },
                    success: function(response) {
                        $.each(response, function(key, value) {
                            var activityHtml = copyActivity(value, key);
                            $('.activitiesDatas').append(activityHtml);
                            $('#myModal').modal('show');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });

            function copyActivity(value, key) {
                return `<div class="position-relative form-group">
                            <input type="hidden" name="id[${key}]" value="${value.id}" readonly>
                            <input name="name[${key}]" value="${value.activities}" readonly>
                            <input name="copyType[${key}]" value="${value.type}" readonly>
                        </div>`;
            }
        });
    </script>
@endpush
