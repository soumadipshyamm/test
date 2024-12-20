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

*****************************only js***********************************************

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

        // Cache DOM elements to optimize performance
        const $userAddFrmAppoinment = $("#userAddFrmAppoinment");
        const $customerId = $('#customer_id');
        const $addAppoinementModal = $('#addAppoinementModal');
        const APP_URL = "{{ config('app.url') }}";

        $(".userAddAppoinment").on('click', function() {
            $userAddFrmAppoinment.submit();
        });

        $(".customerAddAppoinment").on('click', function(e) {
            e.preventDefault();
            if ($customerId.val()) {
                $userAddFrmAppoinment.submit();
            }
        });

        // Reusable function for appending customer details
        function appendCustomerDetails(data) {
            $('.customer_details').empty().append(`
                <div>
                    <label>Name: ${data.name || 'N/A'}</label><br>
                    <label>Email: ${data.email || 'N/A'}</label><br>
                    <label>Mobile Number-1: ${data.mobile_number || 'N/A'}</label><br>
                    <label>Mobile Number-2: ${data.alternate_mobile_number || 'N/A'}</label>
                </div>
            `);
        }

        $('.newCustomer').on('click', function(e) {
            e.preventDefault();
            let formData = new FormData(document.getElementById("newCustomerAddFrm"));

            $.ajax({
                url: `${APP_URL}/customer/add`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $addAppoinementModal.modal('show');
                    $customerId.val(response.data.user.uuid);
                    appendCustomerDetails(response.data.user);
                },
                error: function(xhr) {
                    console.error("Error adding customer:", xhr);
                }
            });
        });

        $(document).on('change', ".customerName", function() {
            const customerName = $(this).val();
            
            $.ajax({
                url: `${APP_URL}/ajax/get-customer-details`,
                type: "GET",
                data: { customerName: customerName },
                success: function(response) {
                    if (response) {
                        $addAppoinementModal.modal('show');
                        $customerId.val(response.uuid);
                        appendCustomerDetails(response);
                    }
                },
                error: function(xhr) {
                    console.error("Error fetching customer details:", xhr);
                }
            });
        });

        function loadSelectOptions(endpoint, target, data) {
            $.ajax({
                type: "POST",
                url: `${APP_URL}/ajax/${endpoint}`,
                data: { id: data },
                success: function(response) {
                    const options = response.status ? response.data.map(el =>
                        `<option value="${el.id}">${el.name}</option>`
                    ).join('') : `<option value="">---Select---</option>`;
                    $(target).html(options);
                },
                error: function() {
                    $(target).html(`<option value="">---Select---</option>`);
                }
            });
        }

        // Load states and cities based on country and state selection
        $(document).on("change", ".select_country", function() {
            loadSelectOptions("state-by-country", ".select_state", $(this).val());
        });

        $(document).on("change", ".select_state", function() {
            loadSelectOptions("city-by-state", ".select_city", $(this).val());
        });

        // Fetch appointment details and display modal
        $(document).on("click", ".appoinementDetails", function(e) {
            e.preventDefault();
            let appointmentId = $(this).data('uuid');

            $.ajax({
                url: `${APP_URL}/ajax/get-appoinement-details`,
                type: "GET",
                data: { uuid: appointmentId },
                success: function(response) {
                    const data = response.data;
                    if (data) {
                        const timeStart = new Date(data.insp_start_datetime).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
                        const timeEnd = new Date(data.insp_end_datetime).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
                        const date = new Date(data.insp_start_datetime).toLocaleDateString();

                        $('#listAppoinementDetailsModal').modal('show');
                        $('#inspectionId').val(data.uuid);
                        $('#insp_customer_name').text(data.customers.name);
                        $('#insp_customer_phone').text(data.customers.mobile_number);
                        $('#insp_customer_time').text(`${timeStart} - ${timeEnd}`);
                        $('#insp_customer_date').text(date);
                        
                        updateAppointmentStatus(data.is_active);
                    }
                },
                error: function(xhr) {
                    console.error("Error fetching appointment details:", xhr);
                }
            });
        });

        function updateAppointmentStatus(status) {
            const $statusChip = $('.appoyment-status');
            const statusMap = {
                0: { label: 'Pending', class: 'chip-pending' },
                1: { label: 'Complete', class: 'chip-completed' },
                2: { label: 'Cancelled', class: 'chip-cancelled' },
                3: { label: 'Rescheduled', class: 'chip-reschedule' }
            };
            $statusChip.removeClass().addClass(`chip ${statusMap[status].class}`).text(statusMap[status].label);
        }

        // Function to update inspection status
        function inspectionStatusUpdate(uuid, status) {
            $.ajax({
                type: "GET",
                url: `${APP_URL}/ajax/get-inspection-status-update`,
                data: { uuid: uuid, status: status },
                success: function() {
                    document.location.reload(true);
                },
                error: function(xhr) {
                    console.error("Error updating inspection status:", xhr);
                }
            });
        }

        $(document).on('click', '.inspectionComplete, .cancel_inspection', function() {
            inspectionStatusUpdate($('#inspectionId').val(), $(this).data('status'));
        });

        // Datepicker logic
        $(document).ready(function() {
            const $datePicker = $("#datePicker");

            $datePicker.datepicker({
                dateFormat: "dd M yy",
                onSelect: function() {
                    fetchData($datePicker.datepicker("getDate"));
                }
            });

            function fetchData(date) {
                const formattedDate = $.datepicker.formatDate("yy-mm-dd", date);
                window.location.href = `${APP_URL}/schedule/appointment-list/${formattedDate}`;
            }

            function adjustDate(days) {
                let currentDate = $datePicker.datepicker("getDate");
                if (currentDate) {
                    currentDate.setDate(currentDate.getDate() + days);
                    $datePicker.datepicker("setDate", currentDate);
                    fetchData(currentDate);
                }
            }

            $(".prev").on("click", function() {
                adjustDate(-1);
            });

            $(".next").on("click", function() {
                adjustDate(1);
            });
        });
    </script>
@endpush

****************************************Quorts************************************
     public function add(Request $request)
    {
        $authCompany = Auth::guard('company-api')->user()->company_id;
        $validator = Validator::make($request->all(), [
            // 'quotes_id' => 'required',
            // 'materials_id' => 'required',
            // 'material_requests_id' => 'required',
            // 'material_request_details_id' => 'required',
            // 'date' => 'required',
            // 'remarkes' => 'required',
            // 'img' => 'required|image',
            // 'qty' => 'required|numeric',
            // 'request_qty' => 'required|numeric',
            // 'price' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        // dd($request->all());
        DB::beginTransaction();
        try {
            $quoteDetail = [];
            $datas = $request->all();
            // dd($datas);
            if (isset($datas['img'])) {
                $remarkes = $datas['remarkes'];
                $img = $request->img ? getImgUpload($request->img, 'upload') : null;
                $existingQuoteDetail = $datas['id'] != null ? QuotesDetails::where('id', $datas['id'])->first() : null;
                if ($existingQuoteDetail?->id != null) {
                    $quoteDetail = $existingQuoteDetail->update([
                        'date' => null,
                        'remarkes' => $remarkes,
                        'img' => $img ?? null
                    ]);
                } else {
                    // dd($img);
                    $quoteDetail = QuotesDetails::create([
                        'company_id' => $authCompany,
                        'quotes_id' => $datas['quotes_id'],
                        'date' => $datas['date'],
                        'remarkes' => $remarkes,
                        'activities_id' => $datas['activities_id']->id,
                        'materials_id' => null,
                        'material_requests_id' => null,
                        'material_request_details_id' => null,
                        'img' => $img ?? null
                    ]);
                }
            } else {
                // dd($datas);
                foreach ($datas as $value) {
                    if (!empty($value['id'])) {
                        $quoteDetailItem = QuotesDetails::find($value['id']);
                        if (!$quoteDetailItem) {
                            return $this->responseJson(false, 404, 'Quote Detail not found', []);
                        }
                        $quoteDetail[] = $quoteDetailItem;
                        // Update existing quote detail
                        $quoteDetailItem->update([
                            'materials_id' => $value['materials'],
                            'material_requests_id' => $value['material_requests_id'],
                            'material_request_details_id' => $value['material_request_details_id'],
                            'date' => $value['date'],
                            'request_qty' => $value['request_qty'],
                            'price' => $value['price'],
                        ]);
                    } else {
                        // Create new quote detail
                        $quoteDetail[] = QuotesDetails::create([
                            'quotes_id' => $value['quotes_id'],
                            'materials_id' => $value['materials'],
                            'material_requests_id' => $value['material_requests_id'],
                            'material_request_details_id' => $value['material_request_details_id'],
                            'date' => $value['date'],
                            'qty' => $value['qty'],
                            'request_qty' => $value['request_qty'],
                            'price' => $value['price'],
                            'company_id' => $authCompany,
                        ]);
                    }
                }
            }
            DB::commit();
            return $this->responseJson(true, 200, 'Quote Detail Added Successfully', $quoteDetail);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to add quote detail: ' . $e->getMessage());
            return $this->responseJson(false, 500, 'Failed to add quote detail', []);
        }
    }
****************************************************************************


    public function add(Request $request)
{
    $authCompany = Auth::guard('company-api')->user()->company_id;

    // Validate the request input
    $validator = Validator::make($request->all(), [
        'data' => 'required|array',
        'data.*.quotes_id' => 'required|integer',
        'data.*.materials_id' => 'nullable|integer',
        'data.*.material_requests_id' => 'nullable|integer',
        'data.*.material_request_details_id' => 'nullable|integer',
        'data.*.date' => 'required|date',
        'data.*.remarkes' => 'nullable|string',
        'data.*.img' => 'nullable|image',
        'data.*.qty' => 'nullable|numeric',
        'data.*.request_qty' => 'nullable|numeric',
        'data.*.price' => 'nullable|numeric',
        'data.*.id' => 'nullable|integer',
    ]);

    if ($validator->fails()) {
        return $this->responseJson(false, 422, $validator->errors()->first(), []);
    }

    DB::beginTransaction();

    try {
        $quoteDetails = [];
        $entries = $request->input('data', []); // Multiple entries for batch processing

        foreach ($entries as $entry) {
            // Extract image if exists
            $img = isset($entry['img']) ? getImgUpload($entry['img'], 'upload') : null;

            // Update if ID exists
            if (!empty($entry['id'])) {
                $existingQuoteDetail = QuotesDetails::find($entry['id']);

                if (!$existingQuoteDetail) {
                    return $this->responseJson(false, 404, 'Quote Detail not found for ID ' . $entry['id'], []);
                }

                // Update existing quote detail
                $existingQuoteDetail->update([
                    'materials_id' => $entry['materials_id'] ?? $existingQuoteDetail->materials_id,
                    'material_requests_id' => $entry['material_requests_id'] ?? $existingQuoteDetail->material_requests_id,
                    'material_request_details_id' => $entry['material_request_details_id'] ?? $existingQuoteDetail->material_request_details_id,
                    'date' => $entry['date'] ?? $existingQuoteDetail->date,
                    'qty' => $entry['qty'] ?? $existingQuoteDetail->qty,
                    'request_qty' => $entry['request_qty'] ?? $existingQuoteDetail->request_qty,
                    'price' => $entry['price'] ?? $existingQuoteDetail->price,
                    'remarkes' => $entry['remarkes'] ?? $existingQuoteDetail->remarkes,
                    'img' => $img ?? $existingQuoteDetail->img,
                ]);

                $quoteDetails[] = $existingQuoteDetail;
            } else {
                // Create new quote detail
                $quoteDetails[] = QuotesDetails::create([
                    'company_id' => $authCompany,
                    'quotes_id' => $entry['quotes_id'],
                    'materials_id' => $entry['materials_id'] ?? null,
                    'material_requests_id' => $entry['material_requests_id'] ?? null,
                    'material_request_details_id' => $entry['material_request_details_id'] ?? null,
                    'date' => $entry['date'],
                    'qty' => $entry['qty'] ?? 0,
                    'request_qty' => $entry['request_qty'] ?? 0,
                    'price' => $entry['price'] ?? 0.0,
                    'remarkes' => $entry['remarkes'] ?? null,
                    'img' => $img,
                ]);
            }
        }

        DB::commit();
        return $this->responseJson(true, 200, 'Quote Details Processed Successfully', $quoteDetails);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Failed to process quote details: ' . $e->getMessage());
        return $this->responseJson(false, 500, 'Failed to process quote details', []);
    }
}

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
