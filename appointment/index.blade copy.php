@extends('layout.app')
@push('styles')
    <style>
        .user_title {
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            font-size: 24px;
            color: #3a3a3a;
        }

        .arrow {
            font-size: 24px;
            color: #3a3a3a;
            cursor: pointer;
            margin: 0 10px;
            user-select: none;
        }

        .date {
            font-weight: bold;
        }

        .arrow:hover {
            color: #007bff;
        }

        .tabcon_body_row {
            border: 1px solid #0e0d0d;
            margin: 5px;
            border-radius: 5px;
        }

        .tabcon_body_row.col-md-3,
        .tabcon_body_row.col-md-6 {
            padding: 15px;
            text-align: center;
        }

        .tabcon_body_row .col-md-3 {
            background-color: #e3f2fd;
            flex: 1;
        }

        .tabcon_body_row .col-md-6 {
            background-color: #ffffff;
            flex: 2;
        }

        .inspactor_status {
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            font-size: 24px;
            border-radius: 30%;
            padding: 6px;
            margin-top: 22px;
            color: #fff;
        }
    </style>
@endpush
@section('content')
    <section class="userlist_sec">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="usertab_con">
                        <div class="tabcon_head">
                            <div class="row">
                                <div class="col-md-3">
                                    {{-- <h3 class="user_title">
                                        <span class="arrow" onclick="prevDate()">&lt;</span>
                                        <span class="date" id="displayDate">12 November 2024</span>
                                        <span class="arrow" onclick="nextDate()">&gt;</span>
                                    </h3> --}}
                                </div>
                                <div class="col-md-9">
                                    <div class="tabconh_right">
                                        <div class="tabcon_addbtn">
                                            <button class="addRouteModalBtn btn btn-success" data-bs-toggle="modal"
                                                data-bs-target="#addRouteModal">
                                                <span><i class="fa-solid fa-plus"></i></span> ADD ROUTE
                                            </button>
                                            <button class="addInspectionModalBtn btn btn-success" data-bs-toggle="modal"
                                                data-bs-target="#addInspectionModal">
                                                <span><i class="fa-solid fa-plus"></i></span>
                                                ADD INSPECTION
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tabcon_body">
                            @foreach ($datas as $key => $data)
                                <div class="accordion" id="accordionExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingOne">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseOne_{{ $key }}" aria-expanded="true"
                                                aria-controls="collapseOne">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <img src="{{ asset('assets/images/demo-user.png') }}" alt=""
                                                            width="50px" height="50px" class="rounded-circle">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6 class="inspector_name">{{ $data?->inspector?->name }}</h6>
                                                        <label><span
                                                                class="route-type">{{ $data?->inspectionType?->name }}</span></label>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapseOne_{{ $key }}" class="accordion-collapse collapse "
                                            aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="usertab_con">
                                                    <div class="tabcon_head">
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <h4 class="user_title">Schedule List</h4>
                                                            </div>
                                                            <div class="col-md-9">
                                                                <div class="tabconh_right">
                                                                    <div class="tabcon_addbtn">
                                                                        <select class="form-control btn btn-outline-primary"
                                                                            name="route" id="routeSelect">
                                                                            <option value="">Assign Route</option>
                                                                        </select>
                                                                    </div>
                                                                    {{-- <div class="tabcon_addbtn">
                                                                        <button
                                                                            class="addInspectionModalBtn btn btn-success"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#addInspectionModal">
                                                                            <span><i class="fa-solid fa-plus"></i></span>
                                                                            ADD INSPECTION
                                                                        </button>
                                                                    </div> --}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="tabcon_body">
                                                            @foreach ($datas as $key => $apmtdata)
                                                                @php
                                                                    if ($data?->is_active !== null) {
                                                                        switch ($data->is_active) {
                                                                            case 0:
                                                                                $status = 'Pending';
                                                                                $color = 'bg-warning';
                                                                                break;
                                                                            case 1:
                                                                                $status = 'Complete';
                                                                                $color = 'bg-success';
                                                                                break;
                                                                            case 2:
                                                                                $status = 'Cancel'; // Corrected typo
                                                                                $color = 'bg-danger';
                                                                                break;
                                                                            case 3:
                                                                                $status = 'Reschedule';
                                                                                $color = 'bg-primary';
                                                                                break;
                                                                        }
                                                                    }
                                                                @endphp
                                                                {{-- @dd($apmtdata); --}}
                                                                @if ($apmtdata->user_id == $data->user_id)
                                                                    <a class="appoinementDetails" {{-- data-bs-toggle="modal" --}}
                                                                        data-uuid="{{ $apmtdata->uuid }}">

                                                                        <div class="row tabcon_body_row">
                                                                            <div class="col-md-3">
                                                                                <p class="text-secondary">Time</p>

                                                                                {{ \Carbon\Carbon::parse($data?->insp_start_datetime)->format('h:i A') .
                                                                                    '-' .
                                                                                    \Carbon\Carbon::parse($data?->insp_end_datetime)->format('h:i A') ??
                                                                                    '---' }}
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="row">
                                                                                    <div class="col-md-6">
                                                                                        <p class="text-secondary">Customer
                                                                                            Name</p>
                                                                                        {{ $data?->customers?->name ?? '---' }}
                                                                                    </div>
                                                                                    <div class="col-md-6 ">
                                                                                        <div class="tabconh_right">
                                                                                            @php
                                                                                                $currentDateTime = \Carbon\Carbon::now();
                                                                                                $startDateTime = \Carbon\Carbon::parse(
                                                                                                    $data?->insp_start_datetime,
                                                                                                );
                                                                                                $endDateTime = \Carbon\Carbon::parse(
                                                                                                    $data?->insp_end_datetime,
                                                                                                );
                                                                                                $isOngoing = $currentDateTime->between(
                                                                                                    $startDateTime,
                                                                                                    $endDateTime,
                                                                                                );
                                                                                            @endphp
                                                                                            @if ($status == 0)
                                                                                                @if ($isOngoing)
                                                                                                    <span
                                                                                                        class=" inspactor_status text-info">Ongoing
                                                                                                        Process</span>
                                                                                                    {{-- complet button  --}}
                                                                                                @endif
                                                                                            @else
                                                                                                <span
                                                                                                    class="inspactor_status {{ $color }}">{{ $status }}</span>
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <p class="text-secondary">Inspection Type
                                                                                </p>
                                                                                {{ $data?->inspectionType?->name ?? '---' }}
                                                                            </div>
                                                                        </div>
                                                                    </a>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('modal')
    <x-schedule.search-customer :date="$date" />
    <x-schedule.add-customer :date="$date" />
    {{-- <x-schedule.add-route :date="$date" /> --}}
    <x-schedule.add-appoinement :date="$date" />
    <x-schedule.appoinement-details :date="$date" :datas="$datas" />
@endsection

@push('script')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(".userAddAppoinment").on('click', function(e) {
            $("#userAddFrmAppoinment").submit();
        });

        $(".customerAddAppoinment").on('click', function(e) {
            e.preventDefault();
            let customer_id = $('#customer_id').val();
            alert(customer_id);
            if (customer_id) {
                $("#userAddFrmAppoinment").submit();
            }
        });
        $('.newCustomer').on('click', function(e) {
            e.preventDefault();
            let formData = new FormData(document.getElementById("newCustomerAddFrm"));

            $.ajax({
                url: APP_URL + '/customer/add',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log(response);
                    alert('Customer added successfully');
                    $('#addAppoinementModal').modal('show');
                    $('#customer_id').val(response.data.user.uuid);
                    $('.customer_details').empty().append(`
                            <div>
                                <label>Name: ${response.data.user.name || 'N/A'}</label><br>
                                <label>Email: ${response.data.user.email || 'N/A'}</label><br>
                                <label>Mobile Number-1: ${response.data.user.mobile_number || 'N/A'}</label>
                                <label>Mobile Number-2: ${response.data.profile.alternate_mobile_number || 'N/A'}</label>
                            </div>
                        `);
                },
                error: function(xhr) {
                    console.error(xhr);
                }
            });
        });

        $(document).on('change', ".customerName", function() {
            const customerName = $(this).val();

            $.ajax({
                url: APP_URL + `/ajax/get-customer-details`,
                type: "GET",
                data: {
                    customerName: customerName
                },
                success: function(response) {
                    if (response) {
                        console.log(response);
                        $('#addAppoinementModal').modal('show');
                        $('#customer_id').val(response.uuid);
                        $('#customerDetails').empty().append(`
                            <div>
                                <label>Name: ${response.name || 'N/A'}</label><br>
                                <label>Email: ${response.email || 'N/A'}</label><br>
                                <label>Mobile Number: ${response.mobile_number || 'N/A'}</label>
                            </div>
                        `);
                    }
                },
                error: function(xhr) {
                    console.error("Error fetching customer details:", xhr);
                }
            });
        });

        $(document).on("change", ".select_country", function() {
            let country = $(this).val();

            $.ajax({
                type: "POST",
                url: APP_URL + "/ajax/state-by-country",
                data: {
                    id: country
                },
                success: function(response) {
                    let statehtml = response.status ? response.data.map(element =>
                            `<option value="${element.id}">${element.name}</option>`).join('') :
                        `<option value="">---Select---</option>`;
                    $(".select_state").html(statehtml);
                },
                error: function() {
                    $(".select_state").html(`<option value="">---Select---</option>`);
                },
            });
        });

        $(document).on("change", ".select_state", function() {
            let state = $(this).val();

            $.ajax({
                type: "POST",
                url: APP_URL + "/ajax/city-by-state",
                data: {
                    id: state
                },
                success: function(response) {
                    let cityhtml = response.status ? response.data.map(element =>
                            `<option value="${element.id}">${element.name}</option>`).join('') :
                        `<option value="">---Select---</option>`;
                    $(".select_city").html(cityhtml);
                },
                error: function() {
                    $(".select_city").html(`<option value="">---Select---</option>`);
                },
            });
        });

        $(document).on("click", ".appoinementDetails", function(e) {
            e.preventDefault();
            let appoinementId = $(this).data('uuid');
            // alert(appoinementId);
            $.ajax({
                url: APP_URL + `/ajax/get-appoinement-details`,
                type: "GET",
                data: {
                    uuid: appoinementId
                },
                success: function(response) {
                    console.log(response.data);
                    if (response) {
                        // if (response.data.is_active !== 1 && response.data.is_active !== 4 && response
                        //     .data.is_active !== 2) {
                        //0:pending,1:complete,2:cancle,3:reschedule,4:reject
                        $('#listAppoinementDetailsModal').modal('show');
                        $('#inspectionId').val(response.data.uuid);
                        $('#inspectionType').val(response.data.inspection_type_id).change();
                        $('#insp_customer_name').text(response.data.customers.name);
                        $('#insp_customer_phone').text(response.data.customers.mobile_number);
                        $('#insp_customer_time').text(new Date(response.data.insp_start_datetime)
                            .toLocaleTimeString([], {
                                hour: '2-digit',
                                minute: '2-digit'
                            }));
                        $('#insp_customer_date').text(new Date(response.data.insp_start_datetime)
                            .toLocaleDateString());
                        $('.referral_remove').attr('data-uuid', response.data
                            .uuid); // Ensure data-uuid is set
                        // $('.referral_remove').attr(response.data.customers.address);
                        // }
                    }
                },
                // whereDate(['insp_start_datetime' => $date])
                error: function(xhr) {
                    console.error("Error fetching customer details:", xhr);
                }
            });
        });

        $(document).on('click', '.reSchedule', function() {
            let uuid = $('#inspectionId').val();

            $.ajax({
                type: "get",
                url: APP_URL + "/ajax/get-appoinement-details",
                data: {
                    uuid: uuid
                },
                success: function(response) {
                    console.log(response.data);
                    if (response) {
                        $('#listAppoinementDetailsModal').modal('hide');
                        $('#addAppoinementModal').modal('show');
                        $('.customer_id').val(response.data.customer_id);
                        $('.inp_id').val(response.data.uuid);
                        $('#customerDetails').empty().append(`
                            <div>
                                <label>Name: ${response.data.customers.name || 'N/A'}</label><br>
                                <label>Email: ${response.data.customers.email || 'N/A'}</label><br>
                                <label>Mobile Number: ${response.data.customers.mobile_number || 'N/A'}</label>
                            </div>
                        `);
                        $('.inspectionType').val(response.data.inspection_type_id).change();
                        $('.insp_inspactor_name').val(response.data.user_id).change();
                        $('.insp_start_datetime').val(response.data.insp_start_datetime);
                        $('.insp_end_datetime').val(response.data.insp_end_datetime);

                    }
                },
                error: function() {
                    console.error("Error fetching customer details:", xhr);
                },
            });
        });

        $(document).on('click', '.inspection_complete, .cancel_inspection', function() {
            let uuid = $('#inspectionId').val();
            let status = $(this).data('status');
            inspectionStatusUpadet(uuid, status)
        });

        function inspectionStatusUpadet(uuid, status) {
            $.ajax({
                type: "get",
                url: APP_URL + "/ajax/get-inspection-status-update",
                data: {
                    uuid: uuid,
                    status: status
                },
                success: function(response) {
                    if (response) {
                        $('#listAppoinementDetailsModal').modal('hide');
                        document.location.reload(true);
                    }
                },
                error: function(xhr) {
                    console.error(xhr);
                }
            });
        }
    </script>
@endpush
