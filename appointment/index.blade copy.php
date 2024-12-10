$(document).on("click", ".appoinementDetails", function(e) {
    e.preventDefault();

    let appoinementId = $(this).data('uuid');
    
    $.ajax({
        url: APP_URL + `/ajax/get-appoinement-details`,
        type: "GET",
        data: { uuid: appoinementId },
        success: function(response) {
            if (!response || !response.data) {
                console.error("No appointment data received.");
                return;
            }
            
            let appointmentData = response.data;
            let startDatetime = new Date(appointmentData.insp_start_datetime);
            let endDatetime = new Date(appointmentData.insp_end_datetime);
            let appointmentStatus = '';
            
            // Display appointment details in the modal
            $('#listAppoinementDetailsModal').modal('show');
            $('#inspectionId').val(appointmentData.uuid);
            $('#inspectionType').val(appointmentData.inspection_type_id).change();
            $('#insp_customer_name').text(appointmentData.customers.name);
            $('#insp_customer_phone').text(appointmentData.customers.mobile_number);
            $('#insp_customer_time').text(
                startDatetime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true }) + 
                ' - ' + 
                endDatetime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true })
            );
            $('#insp_customer_date').text(startDatetime.toLocaleDateString());

            // Set referral remove UUID
            $('.referral_remove').attr('data-uuid', appointmentData.uuid);

            // Determine appointment status and apply corresponding styles
            switch (appointmentData.is_active) {
                case 0:
                    appointmentStatus = 'Pending';
                    $('.appoyment-status').addClass('chip-pending').text(appointmentStatus);
                    break;
                case 1:
                    appointmentStatus = 'Complete';
                    $('.appoyment-status').addClass('chip-completed').text(appointmentStatus);
                    break;
                case 2:
                    appointmentStatus = 'Cancelled';
                    $('.appoyment-status').addClass('chip-cancelled').text(appointmentStatus);
                    break;
                case 3:
                    appointmentStatus = 'Rescheduled';
                    $('.appoyment-status').addClass('chip-reschedule').text(appointmentStatus);
                    break;
            }

            // Hide irrelevant actions based on appointment status
            if (appointmentData.is_active === 0) {
                alert('Appointment is pending');
            } else {
                alert('Appointment status updated');
            }

            // Toggle visibility of certain elements based on appointment status
            $('.inspection-complete').hide();
            $('.inspection-complete-checkbox').hide();
            $('#referral-remove').hide();
            $('.re-schedule').hide();
            $('.cancel_inspection').hide();
        },
        error: function(xhr) {
            console.error("Error fetching appointment details:", xhr);
        }
    });
});

****************************************************************************

@section('content')
<section class="createref_sec">
    <div class="container">
        <!-- Date and Action Bar -->
        <div class="date-route-bar">
            <div class="date-nav">
                <button class="ch-btn prev"><i class="fa-solid fa-chevron-left"></i></button>
                <input type="text" id="datePicker" class="form-control" value="{{ \Carbon\Carbon::parse($date)->format('d M Y') }}" disabled>
                <button class="ch-btn next"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
            <div class="add-route">
                <button class="add-btn" data-bs-toggle="modal" data-bs-target="#addRouteModal"><i class="fa-solid fa-plus"></i> Add Route</button>
            </div>
            <div class="add-insp">
                <button class="add-insp-btn" data-bs-toggle="modal" data-bs-target="#addInspectionModal"><i class="fa-solid fa-plus"></i> Add Inspection</button>
            </div>
        </div>

        <!-- Inspection Details -->
        @foreach ($datas as $data)
        <div class="mb-4 inspaction-details">
            <div class="user-scheduled-info">
                <div class="info">
                    <div class="icon">
                        <img src="{{ $data['user']->imagePath }}" alt="User Image">
                    </div>
                    <div class="text">
                        <h4>{{ $data['user']?->name }}</h4>
                        <span>{{ $data['user']->roles->first()->name }}</span>
                    </div>
                </div>
                <button class="dw-ang-btn"><img src="assets/images/angle-down.png" alt=""></button>
            </div>

            <div class="scheduled-top-head">
                <h3 class="user_title">Scheduled List</h3>
                <div class="tabconh_right">
                    <div class="tabcon_selectbox">
                        <select class="form-control">
                            <option value="">Assign Route</option>
                            <option value="">Assign Route 1</option>
                            <option value="">Assign Route 2</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Scheduled List -->
            <div class="scheduled-list-wrap">
                @foreach ($data['inspections'] as $apmtdata)
                @php
                    $status = match($apmtdata->is_active) {
                        0 => ['Pending', 'chip-pending'],
                        1 => ['Complete', 'chip-completed'],
                        2 => ['Cancelled', 'chip-cancelled'],
                        3 => ['Rescheduled', 'chip-reschedule'],
                        default => ['Unknown', 'chip-default']
                    };
                @endphp
                <div class="scheduled-list appoinementDetails" data-uuid="{{ $apmtdata->uuid }}">
                    <div class="time-box sc-box">
                        <i>Time</i>
                        <div class="d-flex justify-content-between align-items-center">
                            <p>{{ \Carbon\Carbon::parse($apmtdata->insp_start_datetime)->format('h:i A') }} - {{ \Carbon\Carbon::parse($apmtdata->insp_end_datetime)->format('h:i A') }}</p>
                            <img src="assets/images/angle-right.png" alt="">
                        </div>
                    </div>
                    <div class="name-box sc-box">
                        <div class="time-info">
                            <i>Customer Name</i>
                            <p>{{ $apmtdata->customers?->name ?? '---' }}</p>
                        </div>
                        <div class="time-status">
                            @php
                                $isOngoing = $apmtdata->is_active === 0 && now() >= \Carbon\Carbon::parse($apmtdata->insp_start_datetime);
                            @endphp
                            @if ($isOngoing)
                            <span class="scd-chip chip-Ongoing">Ongoing Process</span><br>
                            <a href="#"><span class="inspectionComplete" data-status="1" data-uuid="{{ $apmtdata->uuid }}">Mark as Complete >></span></a>
                            @else
                            <span class="scd-chip {{ $status[1] }}">{{ $status[0] }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="type-box sc-box">
                        <i>Inspection Type</i>
                        <p>{{ $apmtdata->inspectionType?->name ?? '---' }}</p>
                    </div>
                </div>
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
<x-schedule.add-appoinement :date="$date" />
<x-schedule.appoinement-details :date="$date" :datas="$datas" />
@endsection

@push('script')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    $(document).ready(function () {
        const APP_URL = "{{ url('/') }}";

        // Date Picker Initialization
        $("#datePicker").datepicker({
            dateFormat: "dd M yy",
            onSelect: fetchAppointments
        });

        function fetchAppointments(dateText) {
            const formattedDate = $.datepicker.formatDate("yy-mm-dd", $("#datePicker").datepicker("getDate"));
            window.location.href = `${APP_URL}/schedule/appointment-list/${formattedDate}`;
        }

        function adjustDate(days) {
            const currentDate = $("#datePicker").datepicker("getDate");
            currentDate.setDate(currentDate.getDate() + days);
            $("#datePicker").datepicker("setDate", currentDate);
            fetchAppointments();
        }

        $(".prev").on("click", function () {
            adjustDate(-1);
        });

        $(".next").on("click", function () {
            adjustDate(1);
        });

        // Appointment Details Fetcher
        $(document).on("click", ".appoinementDetails", function () {
            const uuid = $(this).data("uuid");
            $.ajax({
                url: `${APP_URL}/ajax/get-appoinement-details`,
                type: "GET",
                data: { uuid },
                success: function (response) {
                    if (response) {
                        // Populate Modal with Data
                        const { customers, insp_start_datetime, insp_end_datetime } = response.data;
                        $("#listAppoinementDetailsModal").modal("show");
                        $("#insp_customer_name").text(customers.name || "N/A");
                        $("#insp_customer_phone").text(customers.mobile_number || "N/A");
                        $("#insp_customer_time").text(
                            `${new Date(insp_start_datetime).toLocaleTimeString()} - ${new Date(insp_end_datetime).toLocaleTimeString()}`
                        );
                    }
                },
                error: function (xhr) {
                    console.error(xhr);
                }
            });
        });

        // Update Inspection Status
        function updateInspectionStatus(uuid, status) {
            $.ajax({
                url: `${APP_URL}/ajax/get-inspection-status-update`,
                type: "GET",
                data: { uuid, status },
                success: function () {
                    location.reload();
                },
                error: function (xhr) {
                    console.error(xhr);
                }
            });
        }

        $(document).on("click", ".inspectionComplete", function () {
            const uuid = $(this).data("uuid");
            const status = $(this).data("status");
            updateInspectionStatus(uuid, status);
        });
    });
</script>
@endpush

****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
