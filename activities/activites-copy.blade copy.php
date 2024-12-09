@extends('Company.layouts.app')
@section('activities-active', 'active')
@section('title', __('Activities'))
@push('styles')
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner card">
            <!-- dashboard body -->
            <div class="dashboard_body">
                <div class="activites-datas">
                    <div class="comp-top">
                        <a href="{{ route('company.activities.add') }}" class="ads-btn">
                            <span><i class="fa-solid fa-plus"></i></span>Create New
                        </a>
                        <a href="{{ route('company.activities.bulkbulkupload') }}" class="btn btn-secondary">
                            <span><i class="fa-solid fa-plus"></i></span>Bulk Upload</a>
                        <a href="{{ route('company.activities.copyActivites') }}" class="btn btn-warning">
                            <span><i class="fa-solid fa-copy"></i></span> Copy</a>
                    </div>
                    <form action="{{ route('company.activities.addCopyActivites') }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="comp-top">
                            <div id="form-project-filter" class="d-flex">
                                <div class="col-lg-6 ">
                                    <select class="form-control mySelect2" value="{{ old('from_project') }}"
                                        name="from_project" id="from_project">
                                        <option value="">----Select Project----</option>
                                        {{ getProject('$data->project_id') }}
                                    </select>
                                    @if ($errors->has('from_project'))
                                        <div class="error">{{ $errors->first('from_project') }}</div>
                                    @endif
                                </div>
                                <div class="col-lg-6">
                                    <select class="form-control mySelect2" value="{{ old('from_subproject') }}"
                                        name="from_subproject" id="from_subproject">
                                        <option value="">----Select SubProject----</option>
                                    </select>
                                    @if ($errors->has('from_subproject'))
                                        <div class="error">{{ $errors->first('from_subproject') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex">
                                <div class="col-lg-6 ">
                                    <select class="form-control mySelect2" value="{{ old('to_project') }}" name="to_project"
                                        id="to_project">
                                        <option value="">----Select Project----</option>
                                        {{ getProject('$data->project_id') }}
                                    </select>
                                    @if ($errors->has('to_project'))
                                        <div class="error">{{ $errors->first('to_project') }}</div>
                                    @endif
                                </div>
                                <div class="col-lg-6">
                                    <select class="form-control mySelect2" value="{{ old('to_subproject') }}"
                                        name="to_subproject" id="to_subproject">
                                        <option value="">----Select SubProject----</option>
                                    </select>
                                    @if ($errors->has('to_subproject'))
                                        <div class="error">{{ $errors->first('to_subproject') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="comp-body">
                            <div class="structuretable">
                                <div class="structure_head">
                                    <div class="structureh_sing">
                                        <p>#</p>
                                    </div>

                                    <div class="structureh_sing activity_strbox">
                                        <p>
                                            Activities
                                        </p>
                                    </div>
                                    <div class="structureh_sing">
                                        <p>
                                            Unit
                                        </p>
                                    </div>
                                    <div class="structureh_sing">
                                        <p>
                                            Qty
                                        </p>
                                    </div>
                                    <div class="structureh_sing">
                                        <p>
                                            Rate
                                        </p>
                                    </div>
                                    <div class="structureh_sing">
                                        <p>
                                            Amount
                                        </p>
                                    </div>
                                    <div class="structureh_sing">
                                        <p>
                                            Start Date
                                        </p>
                                    </div>
                                    <div class="structureh_sing">
                                        <p>
                                            End Date
                                        </p>
                                    </div>
                                    <div class="structureh_sing">
                                        <p>
                                            Action
                                        </p>
                                    </div>
                                </div>
                                <div class="accordion companyig_box" id="constGroup">
                                </div>
                            </div>
                        </div>
                        <div>
                            <button class="mt-2 btn btn-primary">Submit</button>
                            <a href="{{ route('company.activities.list') }}" class="mt-2 btn btn-secondary">&lt;Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('.comp-body').hide();
        })
        $(document).on("click", ".structureb_head > a ", function() {
            if ($(this).closest("a").hasClass("active")) {
                $(this).closest("a").removeClass("active");
                $(this).closest("a")
                    .siblings(".structureb_sub")
                    .slideUp(200);
                $(".structureb_head > a i.fa-solid")
                    .removeClass("fa-chevron-up")
                    .addClass("fa-chevron-down");
            } else {
                $(".structureb_head > a i.fa-solid")
                    .removeClass("fa-chevron-up")
                    .addClass("fa-chevron-down");
                $(this).closest("a")
                    .find("i.fa-solid")
                    .removeClass("fa-chevron-down")
                    .addClass("fa-chevron-up");
                $(".structureb_head > a").removeClass("active");
                $(this).closest("a").addClass("active");
                $(".structureb_sub").slideUp(200);
                $(this).closest("a")
                    .siblings(".structureb_sub")
                    .slideDown(200);
            }
        });
        // $(document).on("click", ".add_newbox", function() {
        //     var type = $(this).attr("data-type")
        //     var dataId = $(this).attr("data-id")
        //     // alert(dataId);
        //     var content = ''
        //     if (type == 'heading') {
        //         content = `<div class="structureb_head">
    //                 <a href="#">
    //                     <div class="strucbhelr_body">
    //                     <div class="strucbhe_left">
    //                         <div class="strucbhe_sing">
    //                         // <span class="add_newbox" data-type="heading">
    //                         //     <i class="fa fa-plus" aria-hidden="true"></i>
    //                         // </span>
    //                         </div>
    //                         <div class="strucbhe_sing">
    //                         <p>Heading</p>
    //                         </div>
    //                         <div class="strucbhe_sing">
    //                         <p>1</p>
    //                         </div>
    //                         <div class="strucbhe_sing strbhe_sgtitle">
    //                         <p>Heading activites</p>
    //                         </div>
    //                     </div>
    //                     <div class="strucbhe_right">
    //                         <div class="strucbhe_sing">
    //                         <i class="fa-solid fa-chevron-down"></i>
    //                         </div>
    //                     </div>
    //                     </div>
    //                 </a>
    //             </div>`
        //         $(content).insertAfter($(this).closest(".structureb_head"))
        //     } else if (type == 'subheading') {
        //         content = `<div class="structureb_sub" style="display: block">
    //         <div class="strucbhe_subbox sub_heading">
    //         <div class="strucbhe_sing">
    //         <a title="Save"  class="submit_data text-primary"><i class="fa-regular fa-floppy-disk"  style="cursor: pointer;" title="Save"></i></a>
    //          <a title="Delete"  class="remove-input-field text-danger"><i class="fa fa-times" aria-hidden="true"></i></a>
    //         </div>
    //         <div class="strucbhe_sing">
    //             <p>Sub-Heading</p>
    //         </div>
    //         <div class="strucbhe_sing">
    //             <p>1.2</p>
    //         </div>
    //         <div class="strucbhe_sing strbhe_sgtitle">
    //             <p>sub activites</p>
    //         </div>
    //         </div>
    //     </div>`
        //         $(content).insertAfter($(this).closest(".structureb_sub"))
        //     } else {
        //         content = `
    //             <div class="strucbhe_subbox  strucbsub_input">
    //                 <div class="strucbhe_sing">
    //                     <a title="Delete"  class="remove-input-field text-danger"><i class="fa fa-trash-alt"
    //                                         style="cursor: pointer;" title="Remove">
    //                                     </i></a>
    //             <a title="Save"  class="submit_data text-primary"><i class="fa-regular fa-floppy-disk"
    //                                         style="cursor: pointer;" title="Save">
    //                                     </i></a>
    //                 </div>
    //                 <div class="strucbhe_sing">
    //                 </div>
    //                 <div class="strucbhe_sing">
    //                     <div class="struc_inputbox"><input type="hidden"  id="pid" value="${dataId}">
    //                     <input type="hidden"  id="type" value="activites">
    //                     <input type="hidden"  id="project_id" value="{{ $activites->project_id ?? '' }}">
    //                     <input type="hidden"  id="subproject_id" value="{{ $activites->subproject_id ?? '' }}">
    //                     Activites</div>
    //                 </div>
    //                 <div class="strucbhe_sing">
    //                     <div class="struc_inputbox"><input type="hidden"  id="updateId" value=""><input type="text"  id="slno" name="slno" placeholder="Sr.No" readonly></div>
    //                 </div>
    //                 <div class="strucbhe_sing strbhe_sgtitle">
    //                 <div class="struc_inputbox">
    //                 <input type="text" class="activities" id="activities" name="activities" placeholder="Activities"></div>
    //                 </div>
    //                 <div class="strucbhe_sing">
    //                 <p>
    //                 <select class="unit_id" id="unit_id" name="unit_id">
    //                 <option value="">----Select Unit----</option>
    //                     {{ getUnits('') }}
    //                 </select>
    //                 </p>
    //                 </div>
    //                 <div class="strucbhe_sing">
    //                 <div class="struc_inputbox"><input type="text" class="quantity" id="quantity" name="quantity"></div>
    //                 </div>
    //                 <div class="strucbhe_sing">
    //                 <div class="struc_inputbox"><input type="text" class="rate" id="rate" name="rate"></div>
    //                 </div>
    //                 <div class="strucbhe_sing">
    //                 <div class="struc_inputbox"><input type="text" class="amount" id="amount" name="amount"></div>
    //                 </div>
    //                 <div class="strucbhe_sing">
    //                 <div class="struc_inputbox"><input type="date" class="start_date" id="start_date" name="start_date"></div>
    //                 </div>
    //                 <div class="strucbhe_sing">
    //                 <div class="struc_inputbox"><input type="date" class="end_date" id="end_date" name="end_date"></div>
    //                 </div>
    //             </div>`
        //         $(content).insertAfter($(this).closest(".strucbhe_subbox"))

        //         $('.strucbhe_subbox .strucbhe_sing .add_newbox').hide();
        //     }
        // })
        // $(document).on("click", ".submit_data", function() {
        //     // alert($('#slno').val());
        //     let pid = $('#pid').val();
        //     let type = $('#type').val();
        //     let slno = $('#slno').val();
        //     let activities = $('#activities').val();
        //     let unit_id = $('#unit_id').val();
        //     let quantity = $('#quantity').val();
        //     let rate = $('#rate').val();
        //     let amount = $('#amount').val();
        //     let project_id = $('#project_id').val();
        //     let subproject_id = $('#subproject_id').val();
        //     let start_date = $('#start_date').val();
        //     let end_date = $('#end_date').val();
        //     $.ajax({
        //         type: "GET",
        //         url: "{{ route('company.activities.activitiesAdd') }}",
        //         data: {
        //             pid: pid,
        //             slno: slno,
        //             type: type,
        //             project_id: project_id,
        //             subproject_id: subproject_id,
        //             activities: activities,
        //             unit_id: unit_id,
        //             quantity: quantity,
        //             rate: rate,
        //             amount: amount,
        //             start_date: start_date,
        //             end_date: end_date,
        //         },
        //         success: function(response) {
        //             if (response.success) {
        //                 location.reload();
        //             }

        //         },
        //     });
        // });
        // $(document).on("click", ".update_data", function() {
        //     let updateId = $('#updateId').val();
        //     let pid = $('#pid').val();
        //     let slno = $('#slno').val();
        //     let type = $('#type').val();
        //     let activities = $('#activities').val();
        //     let unit_id = $('#unit_id').val();
        //     let quantity = $('#quantity').val();
        //     let rate = $('#rate').val();
        //     let amount = $('#amount').val();
        //     let project_id = $('#project_id').val();
        //     let subproject_id = $('#subproject_id').val();
        //     let start_date = $('#start_date').val();
        //     let end_date = $('#end_date').val();
        //     $.ajax({
        //         type: "GET",
        //         url: "{{ route('company.activities.activitiesUpdate') }}",
        //         data: {
        //             updateId: updateId,
        //             slno: slno,
        //             pid: pid,
        //             type: type,
        //             project_id: project_id,
        //             subproject_id: subproject_id,
        //             activities: activities,
        //             unit_id: unit_id,
        //             quantity: quantity,
        //             rate: rate,
        //             amount: amount,
        //             start_date: start_date,
        //             end_date: end_date,
        //         },
        //         success: function(response) {
        //             if (response.success) {
        //                 location.reload();
        //             }

        //         },
        //     });
        // });
        // $(document).on('click', '.remove-input-field', function() {

        //     $('.strucbhe_subbox .strucbhe_sing .add_newbox').show()
        //     $(this).closest('.strucbhe_subbox').remove();
        // });
        // $(document).on('click', '.remove-input-field-update', function() {
        //     $('.strucbhe_subbox .strucbhe_sing .add_newbox').show()
        //     $(this).closest('.strucbhe_subbox').remove();
        //     location.reload();
        // });
        // $(document).on('click', '.editData', function() {
        //     var type = $(this).attr("data-type")
        //     var dataId = $(this).attr("data-uuid")
        //     if (type == 'heading') {
        //         content = `<div class="structureb_head">
    //                 // <a href="#">
    //                     <div class="strucbhelr_body">
    //                     <div class="strucbhe_left">
    //                         <div class="strucbhe_sing">
    //                         </div>
    //                         <div class="strucbhe_sing">
    //                         <p>Heading</p>
    //                         </div>
    //                         <div class="strucbhe_sing">
    //                         <div class="struc_inputbox"><input type="text"  id="slno" name="slno" readonly></div>
    //                         </div>
    //                         <div class="strucbhe_sing strbhe_sgtitle">
    //                         <div class="struc_inputbox"><input type="text"  id="slno" name="slno" readonly></div>
    //                         </div>
    //                     </div>
    //                     <div class="strucbhe_right">
    //                         <div class="strucbhe_sing">
    //                         <i class="fa-solid fa-chevron-down"></i>
    //                         </div>
    //                     </div>
    //                     </div>
    //                 // </a>
    //             </div>`
        //         $(this).parent().parent().html(content);
        //         $('.strucbhe_subbox .strucbhe_sing .add_newbox').hide();
        //     } else if (type == 'subheading') {
        //         content = `<div class="structureb_sub" style="display: block">
    //         <div class="strucbhe_subbox sub_heading">
    //         <div class="strucbhe_sing ">
    //         <a title="update" class="update_data"><i class="fa-regular fa-pen-to-square"  style="cursor: pointer;"></i></a>
    //         <a title="delete" class="remove-input-field-update"><i class="fa fa-trash-alt"
    //                                         style="cursor: pointer;" title="Remove">
    //                                     </i></a>
    //         </div>
    //         <div class="strucbhe_sing">
    //             <p>Sub-Heading</p>
    //         </div>
    //         <div class="strucbhe_sing">
    //         <div class="struc_inputbox"><input type="text"  id="slno" name="slno" readonly></div>
    //         </div>
    //         <div class="strucbhe_sing strbhe_sgtitle">
    //         <div class="struc_inputbox"><input type="text"  id="slno" name="slno" readonly></div>
    //         </div>
    //         </div>
    //     </div>`
        //         $(this).parent().parent().html(content);
        //         $('.strucbhe_subbox .strucbhe_sing .add_newbox').hide();
        //     } else {
        //         content = `
    //         <div class="strucbhe_subbox UpdateDataId">
    //                 <div class="strucbhe_sing">
    //                 <a title="update" class="update_data"><i class="fa-regular fa-floppy-disk"  style="cursor: pointer;" title="Save"></i></a>
    //                 <a title="delete" class="remove-input-field-update"><i class="fa fa-trash-alt"
    //                                         style="cursor: pointer;" title="Remove">
    //                                     </i></a>
    //                 </div>
    //                 <div class="strucbhe_sing">
    //                     <div class="struc_inputbox"><input type="hidden"  id="pid" value="${dataId}">
    //                     <input type="hidden"  id="type" value="activites">
    //                     <input type="hidden"  id="updateId" value="">
    //                     <input type="hidden"  id="project_id" value="{{ $activites->project_id ?? '' }}">
    //                     <input type="hidden"  id="subproject_id" value="{{ $activites->subproject_id ?? '' }}">
    //                     Subheading</div>
    //                 </div>
    //                 <div class="strucbhe_sing">
    //                     <div class="struc_inputbox"><input type="text"  id="slno" name="slno" readonly></div>
    //                 </div>
    //                 <div class="strucbhe_sing strbhe_sgtitle">
    //                 <div class="struc_inputbox"><input type="text" class="activities" id="activities" name="activities"></div>
    //                 </div>
    //                 <div class="strucbhe_sing">
    //                 <p>
    //                 <select class="unit_id" id="unit_id" name="unit_id">
    //                 <option value="">----Select Unit----</option>
    //                     {{ getUnits('') }}
    //                 </select>
    //                 </p>
    //                 </div>
    //                 <div class="strucbhe_sing">
    //                 <div class="struc_inputbox"><input type="text" class="quantity" id="quantity" name="quantity"></div>
    //                 </div>
    //                 <div class="strucbhe_sing">
    //                 <div class="struc_inputbox"><input type="text" class="rate" id="rate" name="rate"></div>
    //                 </div>
    //                 <div class="strucbhe_sing">
    //                 <div class="struc_inputbox"><input type="text" class="amount" id="amount" name="amount"></div>
    //                 </div>
    //                 <div class="strucbhe_sing">
    //                 <div class="struc_inputbox"><input type="date" class="start_date" id="start_date" name="start_date"></div>
    //                 </div>
    //                 <div class="strucbhe_sing">
    //                 <div class="struc_inputbox"><input type="date" class="end_date" id="end_date" name="end_date"></div>
    //                 </div>
    //             </div>`;
        //         $(this).parent().parent().html(content);
        //         $('.strucbhe_subbox .strucbhe_sing .add_newbox').hide();
        //     }
        //     $.ajax({
        //         type: "GET",
        //         url: "{{ route('company.activities.activitiesEdit') }}",
        //         data: {
        //             dataId: dataId,
        //         },
        //         success: function(response) {
        //             $('.UpdateDataId').find('#updateId').val(response.id);
        //             $('.UpdateDataId').find('#pid').val(response.parent_id);
        //             $('.UpdateDataId').find('#type').val(response.type);
        //             $('.UpdateDataId').find('#slno').val(response.sl_no);
        //             $('.UpdateDataId').find('#project_id').val(response.project_id);
        //             $('.UpdateDataId').find('#subproject_id').val(response.subproject_id);
        //             $('.UpdateDataId').find('#unit_id').val(response.unit_id);
        //             $('.UpdateDataId').find('#activities').val(response.activities);
        //             $('.UpdateDataId').find('#quantity').val(response.qty);
        //             $('.UpdateDataId').find('#rate').val(response.rate);
        //             $('.UpdateDataId').find('#amount').val(response.amount);
        //             $('.UpdateDataId').find('#start_date').val(response.start_date);
        //             $('.UpdateDataId').find('#end_date').val(response.end_date);
        //             // console.log(response);
        //         },
        //     });
        // });
        // $(document).ready(function() {
        //     $(".structureb_head").on("keyup", function() {
        //         // alert('asdfghj');
        //         var quantity = $("#quantity").val();
        //         var rate = $("#rate").val();
        //         var totalAmount = quantity * rate;
        //         $("#amount").attr({
        //             value: totalAmount
        //         })
        //     });
        // });
        $('#from_project').change(function() {
            var projectId = $(this).val();
            $.get(baseUrl + 'company/activities/subprojects/' + projectId, function(data) {
                $('#from_subproject').empty();
                $.each(data, function(key, value) {
                    console.log(value.sub_project);
                    $.each(value.sub_project, function(subkey, subvalue) {
                        console.log(subvalue);
                        $('#from_subproject').append('<option value="' + subvalue.id +
                            '">' +
                            subvalue.name +
                            '</option>');
                    });
                });
            });
        });
        $('#to_project').change(function() {
            var projectId = $(this).val();
            $.get(baseUrl + 'company/activities/subprojects/' + projectId, function(data) {
                $('#to_subproject').empty();
                $.each(data, function(key, value) {
                    console.log(value.sub_project);
                    $.each(value.sub_project, function(subkey, subvalue) {
                        console.log(subvalue);
                        $('#to_subproject').append('<option value="' + subvalue.id + '">' +
                            subvalue.name +
                            '</option>');
                    });
                });
            });
        });

        $('#form-project-filter').on('change', function(e) {
            e.preventDefault();
            var project = $('#from_project').val();
            var subproject = $('#from_subproject').val();
            // alert(project + '/' + subproject)
            $.ajax({
                url: "{{ route('company.activities.copyActivites') }}",
                type: "GET",
                data: {
                    project: project,
                    subproject: subproject
                },
                success: function(response) {
                    $('.comp-body').show()
                    $("#constGroup").html(response);
                },
                error: function(error) {
                    aler(error);
                }
            });
        });
    </script>
@endpush
