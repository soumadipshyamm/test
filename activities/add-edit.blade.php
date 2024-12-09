@extends('Company.layouts.app')
@section('activities-active', 'active')
@section('title', __('Activities'))
@push('styles')
    <style>
        .error {
            color: red;
        }
    </style>
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner card">
            <!-- dashboard body -->
            <div class="dashboard_body">
                <!-- company details -->
                <div class="company-details">
                    <h5>Add Activities Details</h5>
                </div>
                <form method="POST" action="{{ route('company.activities.add') }}"
                    data-url="{{ route('company.activities.add') }}" class="formSubmit fileUpload"
                    enctype="multipart/form-data" id="UserForm">
                    @csrf
                    <input type="hidden" name="uuid" id="uuid" value="{{ $data->uuid ?? '' }}">
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="position-relative form-group">
                                <label for="project" class="">Project</label>
                                <select class="form-control" value="{{ old('project') }}" name="project" id="project">
                                    <option value="">----Select Project----</option>
                                    {{ getProject('$data->project_id') }}
                                </select>
                                @if ($errors->has('project'))
                                    <div class="error">{{ $errors->first('project') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="position-relative form-group">
                                <label for="subproject" class="">Sub Project</label>
                                <select class="form-control" value="{{ old('subproject') }}" name="subproject"
                                    id="subproject">
                                    <option value="">----Select SubProject----</option>
                                </select>
                                @if ($errors->has('subproject'))
                                    <div class="error">{{ $errors->first('subproject') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-row">
                        <div class="col-md-4 ">
                            <div class="position-relative form-group typeAdd">
                                <label for="type" class="">Type </label>
                                <select class="form-control activiesType" value="{{ old('type', $data->type ?? '') }}"
                                    name="type" id="type">
                                    <option value="">---Select Activites---</option>
                                    <option value="heading"
                                        {{ isset($data) && $data->type == 'heading' ? 'selected' : '' }}>Heading</option>

                                    <option value="activites"
                                        {{ isset($data) && $data->type == 'activites' ? 'selected' : '' }}>Activites
                                    </option>
                                </select>
                                @if ($errors->has('type'))
                                    <div class="error">{{ $errors->first('type') }}</div>
                                @endif
                            </div>
                            <input type="hidden" value="{{ isset($data) ? $data->additional_fields : '' }}"
                                id="additional_fields" class="additional_fields">
                        </div>
                    </div>
                    <div class="activites">
                    </div>
                    <button class="mt-2 btn btn-primary">Submit</button>
                    <a href="{{ route('company.activities.list') }}" class="mt-2 btn btn-secondary">&lt;
                        Back</a>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            var i = 1;

            $('#project').change(function() {
                var projectId = $(this).val();
                $.get(baseUrl + 'company/activities/subprojects/' + projectId, function(data) {
                    $('#subproject').empty();
                    $.each(data, function(key, value) {
                        $.each(value.sub_project, function(subkey, subvalue) {
                            $('#subproject').append('<option value="' + subvalue
                                .id + '">' + subvalue.name + '</option>');
                        });
                    });

                    updateActivitiesType();
                    $(".activites").hide();
                    $(".activiesType").val("");
                    // alert("project")

                });
            });

            $('#subproject').on('change', function() {
                updateActivitiesType();
                $(".activites").hide();
                $(".activiesType").val("");
                // alert("subproject")
            });

            var data = $('.additional_fields').val();
            $('.activiesType').on('change', function(e) {
                e.preventDefault();
                updateActivitiesType();
                // alert("kjhgtfre")
                // $(".activiesType").val("");
            });

            function updateActivitiesType() {

                var projectId = $('#project').val();
                var subproject = $('#subproject').val();
                var type = $('.activiesType').val();

                if (type === 'activites') {
                    $(".activites").show();
                    $(".activites").html(activiteFieldHtml(projectId, subproject));
                }
                if (type === 'heading') {
                    $(".activites").show();
                    $(".activites").html(generateTypeNameFieldHtml());
                }
            }

            function generateTypeNameFieldHtml() {
                return `<div class="form-row">
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            <label for="activities" class="">Activities</label>
                            <input name="activities" id="activities" class="form-control"
                                value="{{ old('activities', !empty($data) ? $data->activities : '') }}"
                                placeholder=" Enter Activities">
                            @if ($errors->has('activities'))
                            <div class="error">{{ $errors->first('activities') }}</div>
                            @endif
                        </div>
                    </div>
                </div>`;
            }

            function selectHeadingFieldHtml() {
                return `<hr> <div class="form-row">
                    <div class="col-md-4">
                        <div class="position-relative form-group">
                            <label for="activities" class="">Heading</label>
                            <select class="form-control" value="{{ old('heading') }}" name="heading" id="heading">
                                <option value="">----Select Heading/Sub-Heading----</option>
                                {{ getHeading('') }}
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="position-relative form-group">
                            <label for="activities" class="">Activities</label>
                            <input name="activities" id="activities" class="form-control"
                                value="{{ old('activities', !empty($data) ? $data->activities : '') }}"
                                placeholder=" Enter Activities">
                            @if ($errors->has('activities'))
                            <div class="error">{{ $errors->first('activities') }}</div>
                            @endif
                        </div>
                    </div>
                </div>`;
            }

            function activiteFieldHtml(projectId, subproject) {

                $.get(baseUrl + 'company/activities/activiteFieldHtml/' + projectId + '/' + subproject, function(
                    data) {
                    var html = `
                        <hr>
                        <hr>
                        <div class="form-row">
                            <div class="col-md-4">
                                <div class="position-relative form-group">
                                    <label for="activities" class="">Heading</label>
                                    <select class="form-control" value="{{ old('heading') }}" name="heading" id="heading">
                                        <option value="">----Select Heading/Sub-Heading----</option>
                                        ${data}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label for="activities" class="">Activities</label>
                                    <input name="activities" id="activities" class="form-control"
                                        value="{{ old('activities', !empty($data) ? $data->activities : '') }}"
                                        placeholder=" Enter Activities">
                                    @if ($errors->has('activities'))
                                    <div class="error">{{ $errors->first('activities') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label for="unit_id" class="">Unit Type</label>
                                    <select class="form-control" value="{{ old('unit_id') }}" name="unit_id" id="unit_id">
                                        <option value="">----Select Unit----</option>
                                        {{ getUnits($data->unit_id ?? '') }}
                                    </select>
                                    @if ($errors->has('unit_id'))
                                    <div class="error">{{ $errors->first('unit_id') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-4">
                                <div class="position-relative form-group">
                                    <label for="quantity" class="">Quantity</label>
                                    <input type="number" class="form-control quantity" name="quantity"
                                        value="{{ old('quantity', $data->qty ?? '') }}" id="quantity" placeholder="Enter Quantity">
                                    @if ($errors->has('quantity'))
                                    <div class="error">{{ $errors->first('quantity') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="position-relative form-group">
                                    <label for="rate" class="">Rate</label>
                                    <input type="number" class="form-control" name="rate"
                                        value="{{ old('rate', $data->rate ?? '') }}" id="rate" placeholder="Enter rate">
                                    @if ($errors->has('rate'))
                                    <div class="error">{{ $errors->first('rate') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="position-relative form-group totalAmount">
                                    <label for="amount" class="">Amount</label>
                                    <input type="number" class="form-control totalamount" name="amount"
                                        value="{{ old('amount', $data->amount ?? '') }}" id="amount" placeholder="Total Amount" readonly>
                                    @if ($errors->has('amount'))
                                    <div class="error">{{ $errors->first('amount') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label for="start_date" class="">Start Date</label>
                                    <input type="date" class="form-control" name="start_date"
                                        value="{{ old('start_date', $data->start_date ?? '') }}" id="start_date"
                                        placeholder="Enter start_date">
                                    @if ($errors->has('start_date'))
                                    <div class="error">{{ $errors->first('start_date') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label for="end_date" class="">End Date</label>
                                    <input type="date" class="form-control" name="end_date"
                                        value="{{ old('end_date', $data->end_date ?? '') }}" id="end_date"
                                        placeholder="Enter end_date">
                                    @if ($errors->has('end_date'))
                                    <div class="error">{{ $errors->first('end_date') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>`;
                    $(".activites").html(html);
                });

            }
        });
    </script>
    <script>
        // Example using jQuery
        // $('#project').change(function() {
        //     var projectId = $(this).val();
        //     // console.log(projectId);
        //     $.get(baseUrl + 'company/activities/subprojects/' + projectId, function(data) {
        //         $('#subproject').empty();
        //         $.each(data, function(key, value) {
        //             console.log(value.sub_project);
        //             $.each(value.sub_project, function(subkey, subvalue) {
        //                 console.log(subvalue);
        //                 $('#subproject').append('<option value="' + subvalue.id + '">' +
        //                     subvalue.name +
        //                     '</option>');
        //             });
        //         });
        //     });
        // });


        // var data = $('.additional_fields').val();
        // alert(data)
        // $('.activiesType').on('change', function(e) {
        //     var projectId = $('#project').val();
        //     var subproject = $('#subproject').val();
        //     var type = $(this).val();
        //     // alert(projectId);
        //     if (type === 'heading') {

        //         $(".activites").html(generateTypeNameFieldHtml());
        //         // } else if (type === 'subheading') {
        //         //     $(".activites").html(selectHeadingFieldHtml());
        //     } else {
        //         // alert(projectId + '/' + subproject)

        //         $(".activites").html(activiteFieldHtml(projectId, subproject));
        //     }
        // });
    </script>
    {{-- <script>
        $(document).ready(function() {
            var i = 1;

            $('#project').change(function() {
                var projectId = $(this).val();
                $.get(baseUrl + 'company/activities/subprojects/' + projectId, function(data) {
                    $('#subproject').empty();
                    $.each(data, function(key, value) {
                        $.each(value.sub_project, function(subkey, subvalue) {
                            $('#subproject').append('<option value="' + subvalue
                                .id + '">' +
                                subvalue.name +
                                '</option>');
                        });
                    });
                    updateActivitiesType();
                });

            }
        });
    </script>

    <script>
        $(document).ready(function() {
            $("#UserForm").on("keyup", function() {
                var quantity = $("#quantity").val();
                var rate = $("#rate").val();
                var totalAmount = quantity * rate;
                $(".totalamount").attr({
                    value: totalAmount
                })
            });
        });
    </script>
    <script src="{{ asset('assets\js\ajax\test.js') }}"></script>
    --}}
@endpush
