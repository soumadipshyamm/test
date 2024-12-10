@extends('layout.app')
@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
@endpush
@section('content')
    <section class="userlist_sec customer-details">
        <div class="container">
            @php
                $date = date('Y-m-d');
            @endphp
            <div class="row">
                <div class="col-12">
                    <ul class="nav nav-pills" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pills-user-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-user" type="button" role="tab" aria-controls="pills-user"
                                aria-selected="true">INFO</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-subcontact-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-subcontact" type="button" role="tab"
                                aria-controls="pills-subcontact" aria-selected="false">GRAPH</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-agreement-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-agreement" type="button" role="tab"
                                aria-controls="pills-agreement" aria-selected="false">REPORT</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-pricing-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-pricing" type="button" role="tab" aria-controls="pills-pricing"
                                aria-selected="false">PRICING</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-report-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-report" type="button" role="tab" aria-controls="pills-report"
                                aria-selected="false">SCHEDULE</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-office-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-office" type="button" role="tab" aria-controls="pills-office"
                                aria-selected="false">OFFICE'S</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-textback-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-textback" type="button" role="tab"
                                aria-controls="pills-textback" aria-selected="false">DOCS</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-user" role="tabpanel"
                            aria-labelledby="pills-user-tab">
                            <form action="{{ route('customer.add') }}" class="formSubmit fileUpload" method="GET"
                                id="addNewCustomerDetailsForm" class="addNewCustomerDetailsForm">
                                @csrf
                                <input type="hidden" name="uuid" id="uuid" value="{{ $datas->uuid ?? '' }}">
                                <div class="coustomer-details-form">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3 col-sm-12">
                                            <div class="form-group">
                                                <label for="frist-name"><span><strong>Frist Name</strong></span></label>
                                                <input type="text" id="name" name="name" class="form-control"
                                                    value="{{ $datas->name ?? '' }}"
                                                    {{ isset($datas->name) ? 'readonly' : '' }}>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-12">
                                            <div class="form-group">
                                                <label for="phone-no"><span><strong>Phone No.</strong></span></label>
                                                <input type="text" id="mobile_number" name="mobile_number"
                                                    maxlength="10" class="form-control"
                                                    value="{{ $datas->mobile_number ?? '' }}"
                                                    {{ isset($datas->mobile_number) ? 'readonly' : '' }}>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-12">
                                            <div class="form-group">
                                                <label for="phoneNoTow"><span><strong>Phone No. 2</strong></span></label>
                                                <input type="text" id="alternate_mobile_number"
                                                    name="alternate_mobile_number" maxlength="10" class="form-control"
                                                    value="{{ $datas?->profile?->alternate_mobile_number ?? '' }}"
                                                    {{ isset($datas?->profile?->alternate_mobile_number) ? 'readonly' : '' }}>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-12">
                                            <div class="form-group">
                                                <label for="email"><span><strong>Email ID</strong></span></label>
                                                <input type="text" id="email" name="email" class="form-control"
                                                    value="{{ $datas?->email ?? '' }}"
                                                    {{ isset($datas?->email) ? 'readonly' : '' }}>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ***************************** Address************************************ --}}
                                <div class="address-sec">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                            <div class="address-card">
                                                <h3>Address</h3>
                                                @if (isset($datas))
                                                    <a href="#" id="editProfileAddress" class=""><i
                                                            class="fas fa-edit fa-sm editProfileAddress"
                                                            style="color: #63E6BE;"></i></a>
                                                @endif
                                                <div class="row">
                                                    <div class="col-md-6 col-sm-6">
                                                        <div class="add-form-panel mb-3">
                                                            <label for="">Building No.</label>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control profile-address"
                                                                    id="building_number" name="building_number"
                                                                    value="{{ $datas->profile->building_number ?? '' }}"
                                                                    {{ isset($datas->profile->building_number) ? 'readonly' : '' }}>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6">
                                                        <div class="add-form-panel">
                                                            <label for="">Office</label>
                                                            <div class="form-group">
                                                                <select class="form-control profile-address"
                                                                    id="office" name="office"
                                                                    value="{{ $datas->profile->office ?? '' }}"
                                                                    {{ isset($datas->profile->office) ? 'readonly' : '' }}>
                                                                    <option value="">--- Select Office ---</option>
                                                                    {{ getOfficeList(isset($datas->profile->office) ?? '') }}
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6">
                                                        <div class="add-form-panel mb-3">
                                                            <label for="">Unit Number</label>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control profile-address"
                                                                    id="unit_number" name="unit_number"
                                                                    value="{{ $datas->profile->unit_number ?? '' }}"
                                                                    {{ isset($datas->profile->unit_number) ? 'readonly' : '' }}>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6">
                                                        <div class="add-form-panel mb-3">
                                                            <label for="">Country</label>
                                                            <div class="form-group">
                                                                <select class="form-control select_country profile-address"
                                                                    id="country" name="country"
                                                                    value="{{ $datas->profile->country_id ?? '' }}"
                                                                    {{ isset($datas->profile->country_id) ? 'readonly' : '' }}>
                                                                    <option value="">---Select Country---</option>
                                                                    {{ getCountry('') }}
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6">
                                                        <div class="add-form-panel mb-3">
                                                            <label for="">Street</label>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control profile-address"
                                                                    id="street" name="street"
                                                                    value="{{ $datas->profile->street ?? '' }}"
                                                                    {{ isset($datas->profile->street) ? 'readonly' : '' }}>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6">
                                                        <div class="add-form-panel mb-3">
                                                            <label for="">State</label>
                                                            <div class="form-group">
                                                                <select class="form-control select_state profile-address"
                                                                    id="state" name="state"
                                                                    value="{{ $datas->profile->state_id ?? '' }}"
                                                                    {{ isset($datas->profile->state_id) ? 'readonly' : '' }}>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6">
                                                        <div class="add-form-panel mb-3">
                                                            <label for="">Zip Code</label>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control profile-address"
                                                                    id="zip_code" name="zip_code"
                                                                    value="{{ $datas->profile->zip_code ?? '' }}"
                                                                    {{ isset($datas->profile->zip_code) ? 'readonly' : '' }}>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6">
                                                        <div class="add-form-panel mb-3">
                                                            <label for="">City</label>
                                                            <div class="form-group">
                                                                <select name="city" id="city"
                                                                    class="form-control select_city profile-address"
                                                                    value="{{ $datas?->billingDitails?->city_id ?? '' }}"
                                                                    {{ isset($datas?->billingDitails?->city_id) ? 'readonly' : '' }}>
                                                                    <option value="">---Select City---</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6">
                                                        <div class="add-form-panel">
                                                            <label for="">Account No.</label>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control profile-address"
                                                                    id="ac_no" name="ac_no"
                                                                    value="{{ $datas->profile->ac_no ?? '' }}"
                                                                    {{ isset($datas->profile->ac_no) ? 'readonly' : '' }}>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6">
                                                        <div class="add-form-panel mb-3">
                                                            <label for="">Address</label>
                                                            <div class="form-group">
                                                                <textarea class="form-control profile-address" name="address" id="address" cols="1" rows="1"
                                                                    value="{{ $datas?->profile?->address ?? '' }}" {{ isset($datas?->profile?->address) ? 'readonly' : '' }}></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="save-profile-detail"></div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- ********************************Billing Address***************************************** --}}
                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                            <div class="address-card">
                                                <h3>Billing Address</h3>
                                                @if (isset($datas))
                                                    {{-- <div class="tabconh_right"> --}}
                                                    <a href="#" id="editBillingAddress" class=""><i
                                                            class="fas fa-edit fa-sm editBillingAddress"
                                                            style="color: #63E6BE;"></i></a>
                                                    {{-- </div> --}}
                                                @endif
                                                <div class="row">
                                                    <div class="col-md-6 col-sm-6">
                                                        <div class="add-form-panel mb-3">
                                                            <label for="">Building No.</label>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control billing-address"
                                                                    id="billing_building_number"
                                                                    name="billing_building_number"
                                                                    value="{{ $datas?->billingDitails?->building_number ?? '' }}"
                                                                    {{ isset($datas?->billingDitails?->building_number) ? 'readonly' : '' }}>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6">
                                                        <div class="add-form-panel mb-3">
                                                            <label for="">Street</label>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control billing-address"
                                                                    id="billing_street" name="billing_street"
                                                                    value="{{ $datas?->billingDitails?->street ?? '' }}"
                                                                    {{ isset($datas?->billingDitails?->street) ? 'readonly' : '' }}>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6">
                                                        <div class="add-form-panel mb-3">
                                                            <label for="">Unit Number</label>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control billing-address"
                                                                    id="billing_unit_number" name="billing_unit_number"
                                                                    value="{{ $datas?->billingDitails?->unit_number ?? '' }}"
                                                                    {{ isset($datas?->billingDitails?->unit_number) ? 'readonly' : '' }}>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6">
                                                        <div class="add-form-panel mb-3">
                                                            <label for="">Country</label>
                                                            <div class="form-group">
                                                                <select name="billing_country_id" id="billing_country_id"
                                                                    class="form-control billing-address billing_select_country"
                                                                    {{ isset($datas?->billingDitails?->country_id) ? 'readonly' : '' }}>
                                                                    <option value="">---Select Country---</option>
                                                                    {{ getCountry('') }}
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6">
                                                        <div class="add-form-panel mb-3">
                                                            <label for="">Zip Code</label>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control billing-address"
                                                                    name="billing_zip_code" id="billing_zip_code"
                                                                    value="{{ $datas?->billingDitails?->zip_code ?? '' }}"
                                                                    {{ isset($datas?->billingDitails?->zip_code) ? 'readonly' : '' }}>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6">
                                                        <div class="add-form-panel mb-3">
                                                            <label for="">State</label>
                                                            <div class="form-group">
                                                                <select name="billing_state_id" id="billing_state_id"
                                                                    class="form-control billing-address billing_select_state"
                                                                    {{ isset($datas?->billingDitails?->state_id) ? 'readonly' : '' }}>
                                                                    <option value="">---Select State---</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6">
                                                        <div class="add-form-panel mb-3">
                                                            <label for="">Account No.</label>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control billing-address"
                                                                    name="billing_ac_no" id="billing_ac_no"
                                                                    value="{{ $datas?->billingDitails?->ac_no ?? '' }}"
                                                                    {{ isset($datas?->billingDitails?->ac_no) ? 'readonly' : '' }}>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6">
                                                        <div class="add-form-panel">
                                                            <label for="">City</label>
                                                            <div class="form-group">
                                                                <select name="billing_city_id" id="billing_city_id"
                                                                    class="form-control billing-address billing_select_city"
                                                                    {{ isset($datas?->billingDitails?->city_id) ? 'readonly' : '' }}>
                                                                    <option value="">---Select City---</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-md-6 col-sm-6">
                                                        <div class="add-form-panel mb-3">
                                                            <label for="">Address</label>
                                                            <div class="form-group">
                                                                <textarea class="form-control billing-address" name="billing_address" id="billing_address" cols="1"
                                                                    rows="1" value="{{ $datas?->billingDitails?->address ?? '' }}"
                                                                    {{ isset($datas?->billingDitails) ? 'readonly' : '' }}></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6">
                                                        <div class="add-form-panel">
                                                            <label for="">Office</label>
                                                            <div class="form-group">
                                                                <select name="billing_Office" id="billing_Office"
                                                                    class="form-control billing-address"
                                                                    value="{{ $datas?->billingDitails?->office_id ?? '' }}"
                                                                    {{ isset($datas?->billingDitails) ? 'readonly' : '' }}>
                                                                    <option value="">--- Select Office ---</option>
                                                                    {{ getOfficeList($datas?->billingDitails?->office_id ?? '') }}
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="save-billing-detail"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if (!isset($datas))
                                        <div class="tabconh_right pt-3">
                                            <div class="tabcon_addbtn">
                                                <button type="button"
                                                    class="btn btn-primary addNewCustomer">SUBMIT</button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </form>
                            <div class="providedBy-sec">
                                <strong>Lead Provided by ( Brennon Moore, Door to door)</strong>
                            </div>
                            {{-- ************************************************************************************** --}}
                            <div class="inspections-sec">
                                <div class="inspections-ttl">
                                    <h3>ADD NEW INSPECTION</h3>
                                    <div class="tabcon_addbtn">
                                        <button class="addInspectionModalBtn btn btn-success customerName"
                                            data-uuid={{ $datas?->id ?? '' }}>
                                            <span><i class="fa-solid fa-plus"></i></span>
                                            ADD INSPECTION
                                        </button>
                                    </div>
                                </div>
                                @if (isset($inspectionDatas))
                                    @foreach ($inspectionDatas as $key => $inspectionData)
                                        @php
                                            // Initialize status and color variables
                                            $status = 'Unknown';
                                            $color = 'gray';
                                            if ($inspectionData?->is_active !== null) {
                                                switch ($inspectionData->is_active) {
                                                    case 0:
                                                        $status = 'Pending';
                                                        $color = 'yellow';
                                                        break;
                                                    case 1:
                                                        $status = 'Complete';
                                                        $color = 'green';
                                                        break;
                                                    case 2:
                                                        $status = 'Cancelled'; // Corrected typo
                                                        $color = 'red';
                                                        break;
                                                }
                                            }
                                        @endphp
                                        <div class="inspections-panel">
                                            <div class="text">
                                                <h3>{{ $inspectionData?->inspectionType?->name ?? '----' }} Inspection
                                                </h3>
                                                <div class="text-wrap">
                                                    <p>Assigned to:
                                                        <strong>{{ $inspectionData?->inspector?->name ?? 'N/A' }}</strong>
                                                    </p>
                                                    <p>Date:
                                                        <strong>{{ optional($inspectionData?->insp_start_datetime) ? \Carbon\Carbon::parse($inspectionData->insp_start_datetime)->format('Y-m-d') : '---' }}</strong>
                                                    </p>
                                                    <p>Time: <strong>
                                                            {{ optional($inspectionData?->insp_start_datetime) ? \Carbon\Carbon::parse($inspectionData->insp_start_datetime)->format('h:i A') . ' To ' . \Carbon\Carbon::parse($inspectionData->insp_end_datetime)->format('h:i A') : '---' }}
                                                        </strong></p>
                                                    <span class="status {{ $color }}">{{ $status }}</span>
                                                </div>
                                            </div>
                                            @if (!empty($inspectionData?->uuid))
                                                <div class="open-start">
                                                    <button class="btn appoinementDetails"
                                                        data-uuid="{{ $inspectionData->uuid }}">Open/Start</button>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('modal')
    <x-schedule.add-appoinement :date="$date" />
    <x-schedule.appoinement-details :date="$date" />
@endsection
@push('script')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // *********************************************************************************
        $(".customerAddAppoinment").on('click', function(e) {
            e.preventDefault();
            let customer_id = $('#customer_id').val();
            if (customer_id) {
                $("#userAddFrmAppoinment").submit();
            } else {
                $("#addNewCustomerDetailsForm").submit();
                $("#userAddFrmAppoinment").submit();
            }
        });

        $(".addNewCustomer").on('click', function(e) {
            $("#addNewCustomerDetailsForm").submit();
        });
        // **********************************Edit Billing Address*****************************
        $(document).on("click", ".editBillingAddress", function() {
            // Select all input and select elements within the billing address section
            const billingFields = document.querySelectorAll('.billing-address');
            // Make all fields editable
            billingFields.forEach(function(field) {
                field.removeAttribute('readonly'); // Make the field editable
                field.disable = false;
            });
            // Check if the save button already exists
            if ($('.save-billing-address').length === 0) {
                const saveButton = $('<button>', {
                    class: 'btn btn-primary save-billing-address',
                    text: 'Save',
                    type: 'button'
                });
                // Append the Save button to the billing address section
                $('.save-billing-detail').append(saveButton);
            }
        });

        // Handle the click event for saving the billing address
        $(document).on("click", ".save-billing-address", function() {
            const billingFields = document.querySelectorAll('.billing-address');
            // Collect data from the fields using their IDs
            const billingData = {
                customer_id: $('#uuid').val(),
                billing_building_number: $('#billing_building_number').val(),
                billing_street: $('#billing_street').val(),
                billing_unit_number: $('#billing_unit_number').val(),
                billing_country: $('#billing_country_id').val(),
                billing_state: $('#billing_state_id').val(),
                billing_city: $('#billing_city_id').val(),
                billing_zip_code: $('#billing_zip_code').val(),
                billing_ac_no: $('#billing_ac_no').val(),
                billing_office: $('#billing_Office').val(),
                _token: $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
            };
            $.ajax({
                url: APP_URL +
                    `/ajax/add-customer-billing-details`, // Replace with the actual URL to submit data
                type: "POST",
                data: billingData,
                success: function(response) {
                    if (response.status) {
                        // Make the fields readonly after successful save
                        billingFields.forEach(function(field) {
                            field.setAttribute('readonly',
                                true); // Set readonly attribute to true
                        });
                        $('.save-billing-address').hide(); // Hide the save button
                        $('.save-billing-detail')
                            .empty(); // Remove all child elements from the save-billing-detail
                        // Show success message in a toaster
                        toastr.success('Billing address saved successfully!', 'Success');
                    } else {
                        // Show error message in a toaster if the response indicates failure
                        toastr.error('Failed to save billing address. Please try again.', 'Error');
                    }
                },
                error: function(xhr) {
                    // Handle errors - show error message or perform other actions
                    console.log('Error saving billing address: ' + xhr.responseText);
                    toastr.error(
                        'An error occurred while saving the billing address. Please try again.',
                        'Error');
                }
            });
        });
        // **********************************************************************************
        // **********************************Edit Profile Address*****************************
        $(document).on("click", ".editProfileAddress", function() {
            const profileFields = document.querySelectorAll('.profile-address');
            // Make all fields editable
            profileFields.forEach(function(field) {
                field.removeAttribute('readonly'); // Make the field editable
                field.disable = false;
            });
            // Check if the save button already exists
            if ($('.save-profile-address').length === 0) {
                const saveButton = $('<button>', {
                    class: 'btn btn-primary save-profile-address',
                    text: 'Save',
                    type: 'button'
                });
                // Append the Save button to the billing address section
                $('.save-profile-detail').append(saveButton);
            }
        });

        // Handle the click event for saving the billing address
        $(document).on("click", ".save-profile-address", function() {
            const profileFields = document.querySelectorAll('.profile-address');
            // Collect data from the fields using their IDs
            const profileData = {
                customer_id: $('#uuid').val(),
                profile_building_number: $('#building_number').val(),
                profile_street: $('#street').val(),
                profile_unit_number: $('#unit_number').val(),
                profile_country: $('#country').val(),
                profile_state: $('#state').val(),
                profile_city: $('#city').val(),
                profile_zip_code: $('#zip_code').val(),
                profile_ac_no: $('#ac_no').val(),
                profile_office: $('#office').val(),
                profile_address: $('#address').val(),
                _token: $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
            };

            $.ajax({
                url: APP_URL +
                    `/ajax/add-customer-profile-details`, // Replace with the actual URL to submit data
                type: "POST",
                data: profileData,
                success: function(response) {
                    if (response.status) {
                        // Make the fields readonly after successful save
                        profileFields.forEach(function(field) {
                            field.setAttribute('readonly',
                                true); // Set readonly attribute to true
                        });
                        $('.save-profile-address').hide(); // Hide the save button
                        $('.save-profile-detail')
                            .empty(); // Remove all child elements from the save-billing-detail
                        // Show success message in a toaster
                        toastr.success('profile address saved successfully!', 'Success');
                    } else {
                        // Show error message in a toaster if the response indicates failure
                        toastr.error('Failed to save profile address. Please try again.', 'Error');
                    }
                },
                error: function(xhr) {
                    // Handle errors - show error message or perform other actions
                    console.log('Error saving profile address: ' + xhr.responseText);
                    toastr.error(
                        'An error occurred while saving the profile address. Please try again.',
                        'Error');
                }
            });
        });


        // **********************************************************************************
        $(document).on("change", ".select_country", function() {
            let country = $(this).val();
            stateByCountry(country, ".select_state");
        });

        $(document).on("change", ".select_state", function() {
            let state = $(this).val();
            getCity(state, ".select_city");
        });
        // ****************************Billing Address***************************************
        $(document).on("change", ".billing_select_country", function() {
            let country = $(this).val();
            stateByCountry(country, ".billing_select_state");
        });

        $(document).on("change", ".billing_select_state", function() {
            let state = $(this).val();
            getCity(state, ".billing_select_city");
        });

        // ***********************************************************************************
        // Function to get states by country
        function stateByCountry(country, stateClass) {
            $.ajax({
                type: "POST",
                url: APP_URL + "/ajax/state-by-country",
                data: {
                    id: country
                },
                success: function(response) {
                    let stateHtml = response.status ?
                        response.data.map(element =>
                            `<option value="${element.id}">${element.name}</option>`).join('') :
                        `<option value="">---Select---</option>`;
                    $(stateClass).html(stateHtml);
                },
                error: function() {
                    $(stateClass).html(`<option value="">---Select---</option>`);
                },
            });
        }

        // Function to get cities by state
        function getCity(stateId, cityClass) {
            $.ajax({
                type: "POST",
                url: APP_URL + "/ajax/city-by-state",
                data: {
                    id: stateId // Corrected variable name here
                },
                success: function(response) {
                    let cityHtml = response.status ?
                        response.data.map(element =>
                            `<option value="${element.id}">${element.name}</option>`).join('') :
                        `<option value="">---Select---</option>`;
                    $(cityClass).html(cityHtml);
                },
                error: function() {
                    $(cityClass).html(`<option value="">---Select---</option>`);
                },
            });
        }

        // ***********************************************************************************
        $(document).on('click', ".customerName", function() {
            const customerUuid = $(this).data('uuid');
            const customerName = $("#name").val();
            const customerphone = $("#mobile_number").val();
            const customerEmail = $("#email").val();
            // alert(customerName + '/' +
            //     customerphone + '/' +
            //     customerEmail);
            if (customerUuid !== '') {
                $.ajax({
                    url: APP_URL + `/ajax/get-customer-details`,
                    type: "GET",
                    data: {
                        customerName: customerUuid
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
            } else {
                $('#addAppoinementModal').modal('show');
                $('#customerDetails').empty().append(`
                            <div>
                                <label>Name: ${customerName || 'N/A'}</label><br>
                                <label>Email: ${customerphone || 'N/A'}</label><br>
                                <label>Mobile Number: ${customerEmail || 'N/A'}</label>
                            </div>
                        `);
            }
        });

        // ***********************************************************************************
        $(document).on("click", ".appoinementDetails", function(e) {
            e.preventDefault();
            let appoinementId = $(this).data('uuid');
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
        // ***********************************************************************************
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
        // ***********************************************************************************
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
        // ***********************************************************************************
    </script>
@endpush
