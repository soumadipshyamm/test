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
    <section class="createref_sec">
        <div class="container">
            <div class="date-route-bar">
                <div class="date-nav">

                    <button class="ch-btn prev"><i class="fa-solid fa-chevron-left"></i></button>
                    <input type="text" id="datePicker" class="form-control"
                        value="{{ \Carbon\Carbon::parse($date)->format('d M Y') }}" disabled>
                    <button class="ch-btn next"><i class="fa-solid fa-chevron-right"></i></button>
                </div>
                <div class="add-route">
                    <button class="add-btn" data-bs-toggle="modal" data-bs-target="#addRouteModal"><i
                            class="fa-solid fa-plus"></i> Add Route</button>
                </div>
                <div class="add-insp">
                    <button class="add-insp-btn" data-bs-toggle="modal" data-bs-target="#addInspectionModal"><i
                            class="fa-solid fa-plus"></i> ADD INSPECTION</button>
                </div>
            </div>

            @foreach ($datas as $key => $data)
                <div class="mb-4 inspaction-details">
                    <div class="user-scheduled-info">
                        <div class="info">
                            <div class="icon">
                                <img src="{{ $data['user']->imagePath }}" alt="">
                            </div>
                            <div class="text">
                                <h4>{{ $data['user']?->name }}</h4>
                                <span class="">{{ $data['user']->roles->first()->name }}</span>
                            </div>
                        </div>
                        <button class="dw-ang-btn"><img src="assets/images/angle-down.png" alt=""></i></button>
                    </div>

                    <div class="scheduled-top-head">
                        <h3 class="user_title">Scheduled List</h3>
                        <div class="tabconh_right mb-0">
                            <div class="tabcon_selectbox">
                                <select name="" class="form-control" id="">
                                    <option value="">Assign Route</option>
                                    <option value="">Assign Route 1</option>
                                    <option value="">Assign Route 2</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="scheduled-list-wrap">
                        @foreach ($data['inspections'] as $key => $apmtdata)
                            @php
                                if ($apmtdata?->is_active !== null) {
                                    switch ($apmtdata->is_active) {
                                        case 0:
                                            $status = 'Pending';
                                            $color = 'chip-pending';
                                            break;
                                        case 1:
                                            $status = 'Complete';
                                            $color = 'chip-completed';
                                            break;
                                        case 2:
                                            $status = 'Cancel'; // Corrected typo
                                            $color = 'chip-cancelled';
                                            break;
                                        case 3:
                                            $status = 'Reschedule';
                                            $color = 'chip-reschedule';
                                            break;
                                    }
                                }
                            @endphp
                            @if ($apmtdata->user_id == $apmtdata->user_id)
                                <div class="scheduled-list appoinementDetails" data-uuid="{{ $apmtdata->uuid }}">
                                    <div class="time-box sc-box">
                                        <i>Time</i>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p>{{ \Carbon\Carbon::parse($apmtdata?->insp_start_datetime)->format('h:i A') .
                                                '-' .
                                                \Carbon\Carbon::parse($apmtdata?->insp_end_datetime)->format('h:i A') ??
                                                '---' }}
                                            </p>
                                            <img src="assets/images/angle-right.png" alt="">
                                        </div>
                                    </div>
                                    <div class="name-box sc-box">
                                        <div class="time-info">
                                            <i>Customer Name</i>
                                            <p> {{ $apmtdata?->customers?->name ?? '---' }}</p>
                                        </div>
                                        <div class="time-status">
                                            @php
                                                $currentDateTime = \Carbon\Carbon::now();
                                                $startDateTime = \Carbon\Carbon::parse($apmtdata?->insp_start_datetime);
                                                $endDateTime = \Carbon\Carbon::parse($apmtdata?->insp_end_datetime);
                                            @endphp
                                            @if ($apmtdata?->is_active == 0 && $currentDateTime >= $startDateTime)
                                                <span class="scd-chip chip-Ongoing">Ongoing Process</span><br>
                                                <a href="#"><span class="inspectionComplete" data-status="1"
                                                        data-uuid="{{ $apmtdata?->uuid }}">Mark as
                                                        Complete >> </span></a>
                                            @else
                                                <span class="scd-chip {{ $color }}">{{ $status }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="type-box sc-box">
                                        <i>Inspection Type</i>
                                        <p>{{ $apmtdata?->inspectionType?->name ?? '---' }}</p>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
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
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script> <!-- Include jQuery UI for datepicker -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

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
            // alert(customer_id);
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
                    // alert('Customer added successfully');
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
                        //0:pending,1:complete,2:cancle,3:reschedule,4:reject
                        if (response.data.is_active == 0) {
                            alert('Appoinement is pending');
                            $('#listAppoinementDetailsModal').modal('show');
                            $('#inspectionId').val(response.data.uuid);
                            $('#inspectionType').val(response.data.inspection_type_id).change();
                            $('#insp_customer_name').text(response.data.customers.name);
                            $('#insp_customer_phone').text(response.data.customers.mobile_number);
                            $('#insp_customer_time').text(new Date(response.data.insp_start_datetime)
                                .toLocaleTimeString([], {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: true // Change to 24-hour format
                                }) + ' - ' + new Date(response.data.insp_end_datetime)
                                .toLocaleTimeString([], {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: true // Change to 24-hour format
                                }));

                            $('#insp_customer_date').text(new Date(response.data.insp_start_datetime)
                                .toLocaleDateString());
                            $('.referral_remove').attr('data-uuid', response.data.uuid);
                        } else {
                            alert('Appoinement');

                            $('#listAppoinementDetailsModal').modal('show');
                            $('#inspectionId').val(response.data.uuid);
                            $('#inspectionType').val(response.data.inspection_type_id).change();
                            $('#insp_customer_name').text(response.data.customers.name);
                            $('#insp_customer_phone').text(response.data.customers.mobile_number);
                            $('#insp_customer_time').text(new Date(response.data.insp_start_datetime)
                                .toLocaleTimeString([], {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: true // Change to 24-hour format
                                }) + ' - ' + new Date(response.data.insp_end_datetime)
                                .toLocaleTimeString([], {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: true // Change to 24-hour format
                                }));
                            $('#insp_customer_date').text(new Date(response.data.insp_start_datetime)
                                .toLocaleDateString());

                            let appointmentStatus = '';
                            switch (response.data.is_active) {
                                case 0:
                                    appointmentStatus = 'Pending';
                                    $('.appoyment-status').addClass('chip-pending').text(
                                        appointmentStatus);
                                    break;
                                case 1:
                                    appointmentStatus = 'Complete';
                                    $('.appoyment-status').addClass('chip-completed').text(
                                        appointmentStatus);
                                    break;
                                case 2:
                                    appointmentStatus = 'Cancelled';
                                    $('.appoyment-status').addClass('chip-cancelled').text(
                                        appointmentStatus);
                                    break;
                                case 3:
                                    appointmentStatus = 'Rescheduled';
                                    $('.appoyment-status').addClass('chip-reschedule').text(
                                        appointmentStatus);
                                    break;
                            }
                            $('.inspection-complete').hide();
                            $('.inspection-complete-checkbox').hide();
                            $('#referral-remove').hide();
                            $('.re-schedule').hide();
                            $('.cancel_inspection').hide();
                        }

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
            // alert(uuid + '/' + status)
            inspectionStatusUpadet(uuid, status)
        });
        $(document).on('click', '.inspectionComplete', function() {
            let uuid = $(this).data('uuid');
            let status = $(this).data('status');
            // alert(uuid + '/' + status);
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
                        // $('#listAppoinementDetailsModal').modal('hide');
                        document.location.reload(true);
                    }
                },
                error: function(xhr) {
                    console.error(xhr);
                }
            });
        }

        $(document).ready(function() {
            // Initialize date picker

            $("#datePicker").datepicker({
                dateFormat: "dd M yy", // Format for the displayed date
                onSelect: function(dateText) {
                    // Uncomment this if you want to fetch data when a date is selected
                    fetchData(dateText);
                }
            });

            // Function to fetch data for a specific date
            function fetchData(date) {
                // Change the date format to 'yyyy-mm-dd' for the URL
                let formattedDateForUrl = $.datepicker.formatDate("yy-mm-dd", $("#datePicker").datepicker(
                    "getDate"));
                window.location.href = APP_URL + "/schedule/appointment-list/" + formattedDateForUrl;
                // APP_URL + "/ajax/get-appoinement-details"
            }

            // Adjust date when buttons are clicked
            function adjustDate(days) {
                $('#ui-datepicker-div').hide();
                let currentDate = $("#datePicker").datepicker("getDate");
                if (currentDate) {
                    currentDate.setDate(currentDate.getDate() + days);
                    let formattedDate = $.datepicker.formatDate("dd M yy", currentDate);
                    $("#datePicker").datepicker("setDate", formattedDate); // Update the picker
                    // Uncomment this if you want to fetch data for the new date
                    fetchData(formattedDate);
                } else {
                    console.error("Current date is not valid.");
                }
            }

            // Previous date button
            $(".prev").on("click", function() {
                adjustDate(-1);
            });

            // Next date button
            $(".next").on("click", function() {
                adjustDate(1);
            });

        });
    </script>
@endpush
