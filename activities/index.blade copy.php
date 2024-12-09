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
                            <span><i class="fa-solid fa-plus"></i></span>Bulk Upload</a>
                        <button type="button" class="btn btn-primary copy_data" data-bs-toggle="modal"
                            data-bs-target="#myModal">Copy</button>
                        <a href="{{ route('company.activities.export') }}" class="ads-btn">
                            <span><i class="fa fa-download" aria-hidden="true"
                                    title="Download Activities Data in Excel"></i></span>
                        </a>
                    </div>
                    <div class="comp-top">
                        <div class="col-lg-12">
                            <form id="filter-form" class="d-flex">
                                <div class="col-lg-4">
                                    <label for="from_project">Project</label>
                                    <select class="form-control mySelect2" name="from_project" id="from_project">
                                        <option value="">----Select Project----</option>
                                        {{ getProject('$data->project_id') }}
                                    </select>
                                    @if ($errors->has('project'))
                                        <div class="error">{{ $errors->first('project') }}</div>
                                    @endif
                                </div>
                                <div class="col-lg-4">
                                    <label for="from_subproject">Sub Project</label>
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
                        <div class="col-lg-5">
                            <div class="position-relative form-group">
                                <label for="to_project">Project</label>
                                <select class="form-control mySelect2" name="to_project" id="to_project">
                                    <option value="">----Select Project----</option>
                                    {{ getProject('$data->project_id') }}
                                </select>
                                @if ($errors->has('to_project'))
                                    <div class="error">{{ $errors->first('to_project') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="position-relative form-group">
                                <label for="to_subproject">Sub Project</label>
                                <select class="form-control mySelect2" name="to_subproject" id="to_subproject">
                                    <option value="">----Select SubProject----</option>
                                </select>
                                @if ($errors->has('to_subproject'))
                                    <div class="error">{{ $errors->first('to_subproject') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="position-relative form-group typeAdd">
                                <label for="type">Type</label>
                                <select class="form-control activiesType" name="type" id="type">
                                    <option value="">---Select Activities---</option>
                                    <option value="heading"
                                        {{ isset($data) && $data->type == 'heading' ? 'selected' : '' }}>Heading
                                    </option>
                                    <option value="activites"
                                        {{ isset($data) && $data->type == 'activites' ? 'selected' : '' }}>Activities
                                    </option>
                                </select>
                                @if ($errors->has('type'))
                                    <div class="error">{{ $errors->first('type') }}</div>
                                @endif
                            </div>
                            <input type="hidden" value="{{ isset($data) ? $data->additional_fields : '' }}"
                                id="additional_fields" class="additional_fields">
                        </div>
                        <div class="col-md-6">
                            <div class="activites"></div>
                        </div>
                        <div class="activitesDatas"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
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
        });

        $(document).on("click", ".add_newbox", function() {
            var type = $(this).attr("data-type");
            var dataId = $(this).attr("data-id");
            var content = '';

            if (type == 'heading') {
                content = `<div class="structureb_head">
                    <a href="#">
                        <div class="strucbhelr_body">
                            <div class="strucbhe_left">
                                <div class="strucbhe_sing"></div>
                                <div class="strucbhe_sing"><p>Heading</p></div>
                                <div class="strucbhe_sing"><p>1</p></div>
                                <div class="strucbhe_sing strbhe_sgtitle"><p>Heading activities</p></div>
                            </div>
                            <div class="strucbhe_right">
                                <div class="strucbhe_sing"><i class="fa-solid fa-chevron-down"></i></div>
                            </div>
                        </div>
                    </a>
                </div>`;
                $(content).insertAfter($(this).closest(".structureb_head"));
            } else if (type == 'subheading') {
                content = `<div class="structureb_sub" style="display: block">
                    <div class="strucbhe_subbox sub_heading">
                        <div class="strucbhe_sing">
                            <a title="Save" class="submit_data text-primary"><i class="fa-regular fa-floppy-disk" style="cursor: pointer;" title="Save"></i></a>
                            <a title="Delete" class="remove-input-field text-danger"><i class="fa fa-times" aria-hidden="true"></i></a>
                        </div>
                        <div class="strucbhe_sing"><p>Sub-Heading</p></div>
                        <div class="strucbhe_sing"><p>1.2</p></div>
                        <div class="strucbhe_sing strbhe_sgtitle"><p>sub activities</p></div>
                    </div>
                </div>`;
                $(content).insertAfter($(this).closest(".structureb_sub"));
            } else {
                content = `<div class="strucbhe_subbox strucbsub_input">
                    <div class="strucbhe_sing">
                        <a title="Delete" class="remove-input-field text-danger"><i class="fa fa-trash-alt" style="cursor: pointer;" title="Remove"></i></a>
                        <a title="Save" class="submit_data text-primary"><i class="fa-regular fa-floppy-disk" style="cursor: pointer;" title="Save"></i></a>
                    </div>
                    <div class="strucbhe_sing"></div>
                    <div class="strucbhe_sing">
                        <div class="struc_inputbox"><input type="hidden" id="pid" value="${dataId}">
                        <input type="hidden" id="type" value="activities">
                        <input type="hidden" id="project_id" value="{{ $activites->project_id ?? '' }}">
                        <input type="hidden" id="subproject_id" value="{{ $activites->subproject_id ?? '' }}">
                        Activities</div>
                    </div>
                    <div class="strucbhe_sing"><div class="struc_inputbox"><input type="hidden" id="updateId" value=""><input type="text" id="slno" name="slno" placeholder="Sr.No" readonly></div></div>
                    <div class="strucbhe_sing strbhe_sgtitle"><div class="struc_inputbox"><input type="text" class="activities" id="activities" name="activities" placeholder="Activities"></div></div>
                    <div class="strucbhe_sing"><p><select class="unit_id" id="unit_id" name="unit_id"><option value="">----Select Unit----</option>{{ getUnits('') }}</select></p></div>
                    <div class="strucbhe_sing"><div class="struc_inputbox"><input type="text" class="quantity" id="quantity" name="quantity"></div></div>
                    <div class="strucbhe_sing"><div class="struc_inputbox"><input type="text" class="rate" id="rate" name="rate"></div></div>
                    <div class="strucbhe_sing"><div class="struc_inputbox"><input type="text" class="amount" id="amount" name="amount"></div></div>
                    <div class="strucbhe_sing"><div class="struc_inputbox"><input type="date" class="start_date" id="start_date" name="start_date"></div></div>
                    <div class="strucbhe_sing"><div class="struc_inputbox"><input type="date" class="end_date" id="end_date" name="end_date"></div></div>
                </div>`;
                $(content).insertAfter($(this).closest(".strucbhe_subbox"));
                $('.strucbhe_subbox .strucbhe_sing .add_newbox').hide();
            }
        });

        $(document).on("click", ".submit_data", function() {
            let data = {
                pid: $('#pid').val(),
                slno: $('#slno').val(),
                type: $('#type').val(),
                project_id: $('#project_id').val(),
                subproject_id: $('#subproject_id').val(),
                activities: $('#activities').val(),
                unit_id: $('#unit_id').val(),
                quantity: $('#quantity').val(),
                rate: $('#rate').val(),
                amount: $('#amount').val(),
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
            };
            $.ajax({
                type: "GET",
                url: "{{ route('company.activities.activitiesAdd') }}",
                data: data,
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                },
            });
        });

        $(document).on('click', '.remove-input-field', function() {
            $(this).closest('.strucbhe_subbox').remove();
        });

        $(document).on('click', '.editData', function() {
            var type = $(this).attr("data-type");
            var dataId = $(this).attr("data-uuid");
            var content = '';

            if (type == 'heading') {
                content = `<div class="structureb_head">
                    <div class="strucbhelr_body">
                        <div class="strucbhe_left">
                            <div class="strucbhe_sing"></div>
                            <div class="strucbhe_sing"><p>Heading</p></div>
                            <div class="strucbhe_sing"><div class="struc_inputbox"><input type="text" id="slno" name="slno" readonly></div></div>
                            <div class="strucbhe_sing strbhe_sgtitle"><div class="struc_inputbox"><input type="text" id="slno" name="slno" readonly></div></div>
                        </div>
                        <div class="strucbhe_right"><div class="strucbhe_sing"><i class="fa-solid fa-chevron-down"></i></div></div>
                    </div>
                </div>`;
                $(this).parent().parent().html(content);
                $('.strucbhe_subbox .strucbhe_sing .add_newbox').hide();
            } else if (type == 'subheading') {
                content = `<div class="structureb_sub" style="display: block">
                    <div class="strucbhe_subbox sub_heading">
                        <div class="strucbhe_sing">
                            <a title="update" class="update_data"><i class="fa-regular fa-pen-to-square" style="cursor: pointer;"></i></a>
                            <a title="delete" class="remove-input-field-update"><i class="fa fa-trash-alt" style="cursor: pointer;" title="Remove"></i></a>
                        </div>
                        <div class="strucbhe_sing"><p>Sub-Heading</p></div>
                        <div class="strucbhe_sing"><div class="struc_inputbox"><input type="text" id="slno" name="slno" readonly></div></div>
                        <div class="strucbhe_sing strbhe_sgtitle"><div class="struc_inputbox"><input type="text" id="slno" name="slno" readonly></div></div>
                    </div>
                </div>`;
                $(this).parent().parent().html(content);
                $('.strucbhe_subbox .strucbhe_sing .add_newbox').hide();
            } else {
                content = `<div class="strucbhe_subbox UpdateDataId">
                    <div class="strucbhe_sing">
                        <a title="update" class="update_data"><i class="fa-regular fa-floppy-disk" style="cursor: pointer;" title="Save"></i></a>
                        <a title="delete" class="remove-input-field-update"><i class="fa fa-trash-alt" style="cursor: pointer;" title="Remove"></i></a>
                    </div>
                    <div class="strucbhe_sing">
                        <div class="struc_inputbox"><input type="hidden" id="pid" value="${dataId}">
                        <input type="hidden" id="type" value="activities">
                        <input type="hidden" id="updateId" value="">
                        <input type="hidden" id="project_id" value="{{ $activites->project_id ?? '' }}">
                        <input type="hidden" id="subproject_id" value="{{ $activites->subproject_id ?? '' }}">
                        Subheading</div>
                    </div>
                    <div class="strucbhe_sing"><div class="struc_inputbox"><input type="text" id="slno" name="slno" readonly></div></div>
                    <div class="strucbhe_sing strbhe_sgtitle"><div class="struc_inputbox"><input type="text" class="activities" id="activities" name="activities"></div></div>
                    <div class="strucbhe_sing"><p><select class="unit_id" id="unit_id" name="unit_id"><option value="">----Select Unit----</option>{{ getUnits('') }}</select></p></div>
                    <div class="strucbhe_sing"><div class="struc_inputbox"><input type="text" class="quantity" id="quantity" name="quantity"></div></div>
                    <div class="strucbhe_sing"><div class="struc_inputbox"><input type="text" class="rate" id="rate" name="rate"></div></div>
                    <div class="strucbhe_sing"><div class="struc_inputbox"><input type="text" class="amount" id="amount" name="amount"></div></div>
                    <div class="strucbhe_sing"><div class="struc_inputbox"><input type="date" class="start_date" id="start_date" name="start_date"></div></div>
                    <div class="strucbhe_sing"><div class="struc_inputbox"><input type="date" class="end_date" id="end_date" name="end_date"></div></div>
                </div>`;
                $(this).parent().parent().html(content);
                $('.strucbhe_subbox .strucbhe_sing .add_newbox').hide();
            }
            $.ajax({
                type: "GET",
                url: "{{ route('company.activities.activitiesEdit') }}",
                data: {
                    dataId: dataId
                },
                success: function(response) {
                    $('.UpdateDataId').find('#updateId').val(response.id);
                    $('.UpdateDataId').find('#pid').val(response.parent_id);
                    $('.UpdateDataId').find('#type').val(response.type);
                    $('.UpdateDataId').find('#slno').val(response.sl_no);
                    $('.UpdateDataId').find('#project_id').val(response.project_id);
                    $('.UpdateDataId').find('#subproject_id').val(response.subproject_id);
                    $('.UpdateDataId').find('#unit_id').val(response.unit_id);
                    $('.UpdateDataId').find('#activities').val(response.activities);
                    $('.UpdateDataId').find('#quantity').val(response.qty);
                    $('.UpdateDataId').find('#rate').val(response.rate);
                    $('.UpdateDataId').find('#amount').val(response.amount);
                    $('.UpdateDataId').find('#start_date').val(response.start_date);
                    $('.UpdateDataId').find('#end_date').val(response.end_date);
                },
            });
        });

        $('#from_project').change(function() {
            var projectId = $(this).val();
            $.get(baseUrl + 'company/activities/subprojects/' + projectId, function(data) {
                $('#from_subproject').empty();
                $.each(data, function(key, value) {
                    $.each(value.sub_project, function(subkey, subvalue) {
                        $('#from_subproject').append('<option value="' + subvalue.id +
                            '">' + subvalue.name + '</option>');
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

        $(document).on('click', '.copy_data', function(e) {
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
                        $('.activitesDatas').append(activityHtml);
                        $('#myModal').modal('show');
                    });
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

        function copyActivity(value, key) {
            return `<div class="position-relative form-group activity-item" data-key="${key}">
                <input type="hidden" name="id[${key}]" id="id" value="${value.id}">
                <input name="name[${key}]" id="name" value="${value.activities}">
                <input name="copyType[${key}]" id="copyType" value="${value.type}">
                <button type="button" class="btn btn-danger remove-activity" data-key="${key}">Remove</button>
            </div>`;
        }

        $('#to_project').change(function() {
            var projectId = $(this).val();
            $.get(baseUrl + 'company/activities/subprojects/' + projectId, function(data) {
                $('#to_subproject').empty();
                $.each(data, function(key, value) {
                    $.each(value.sub_project, function(subkey, subvalue) {
                        $('#to_subproject').append('<option value="' + subvalue.id + '">' +
                            subvalue.name + '</option>');
                    });
                });
            });
        });

        $('.activiesType').on('change', function(e) {
            var projectId = $('#project').val();
            var subproject = $('#subproject').val();
            var type = $(this).val();
            if (type === 'heading') {
                $(".activites").html(generateTypeNameFieldHtml());
            } else {
                $(".activites").html(activiteFieldHtml(projectId, subproject));
            }
        });

        function generateTypeNameFieldHtml() {
            return `<div class="position-relative form-group">
                <label for="activities">Activities</label>
                <input name="activities" id="activities" class="form-control" value="{{ old('activities', !empty($data) ? $data->activities : '') }}" placeholder="Enter Activities">
                @if ($errors->has('activities'))
                <div class="error">{{ $errors->first('activities') }}</div>
                @endif
            </div>`;
        }

        function activiteFieldHtml(projectId, subproject) {
            return `<div class="position-relative form-group">
                <label for="activities">Heading</label>
                <select class="form-control" name="heading" id="heading">
                    <option value="">----Select Heading/Sub-Heading----</option>
                            {{ getchildActivites(`projectId`, `subproject`) }}
                </select>
            </div>`;
        }
    </script>
@endpush
