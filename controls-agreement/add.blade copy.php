<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        .section-title {
            background-color: cadetblue;
            color: white;
        }

        .office-logo {
            height: 90px;
            width: 200px;
        }

        .custom-back-button {
            background-color: #3c1ad6;
            color: #ffffff;
            /* Optional: Change text color */
        }

        .mainBody {
            border: 1px solid #000000;
        }

    </style>
</head>

<body>
    <section class="userlist_sec">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="usertab_con">
                        <div class="tabcon_head">
                            <div class="row">
                                <div class="col-md-3">
                                    <h3 class="user_title">AGREEMENTS</h3>
                                </div>
                                <div class="col-md-9">
                                    <div class="row">
                                        <div class="pb-2">
                                            <a href="{{ route('agreement.list') }}"
                                                class="btn btn-primary float-end custom-back-button">&larr; Back</a>
                                        </div>
                                        <div class="tabconh_right">
                                            <form class="search_form" id="search_form">
                                                <div class="search_sec">
                                                    <div>
                                                        <select name="select_customer" id="select_customer"
                                                            class="form-control select_customer">
                                                            <option value="">---Select Customer---</option>
                                                            {{ getCustomer(isset($customerDetails) ? $customerDetails?->uuid : '') }}
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <select name="select_office" id="select_office"
                                                            class="form-control select_office">
                                                            <option value="">---Select Office---</option>
                                                            {{ getOfficeList(isset($officeDetails) ? $officeDetails?->id : '') }}
                                                        </select>
                                                    </div>
                                                    <div class="action_btn">
                                                        <a href="javascript:void(0)"
                                                            class="btn btn-primary searchCustomerOffice">
                                                            <i class="fa-solid fa-magnifying-glass"
                                                                aria-hidden="true"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-contentUncaught TypeError: Failed to execute 'getComputedStyle' on 'Window': parameter 1 is not of type 'Element'. pdf-section "
                            id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-user" role="tabpanel"
                                aria-labelledby="pills-user-tab">
                                <form action="{{ route('agreement.add') }}"
                                    class="formSubmit fileUpload" id="addAgremmetForm" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="agreement_id" id="agreement_id"
                                        value="{{ isset($datas) ? $datas?->uuid : '' }}">
                                    <div class="modal-body mainBody">
                                        <div class="addnumodal">
                                            {{-- ***********************Header ***************************************** --}}
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <div class="col-md-4">
                                                    <div class=" d-flex flex-wrap justify-content-center ">
                                                        <a href="javascript:void(0)" class="upload-img"> <img
                                                                class="no_img"
                                                                src="{{ asset('assets/images/no_img.jpg') }}"
                                                                alt="" style="height: 100px; width:250px"></a>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="col-md-6">
                                                        <div class=" d-flex flex-wrap justify-content-center">
                                                            <h3 class="form-title head-title" id="head-title"
                                                                style="line-break: anywhere;">Enter Title</h3>
                                                            <span class="add-heading edit-title"><i class="fas fa-edit"
                                                                    style="color: #07d6f1;"></i></span>
                                                            {{-- <span class="title-save"><i class="fa-solid fa-check"
                                                                    style="color: #228505;"></i></span> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- ***********************Office Details ***************************************** --}}
                                                <div class="col-md-4">
                                                    <input type="hidden" name="office[office_id]" id="office_id"
                                                        class="office_id">
                                                    <div class="d-flex flex-wrap  ">
                                                        <div class="col-md-6 p-1">
                                                            <div class="form-group Name ">
                                                                <label for="office_name">Name:</label><span
                                                                    class="office_cancle"><i class="fas fa-times"
                                                                        style="color: #f70707;"></i></span>
                                                                <input type="text" name="office[office_name]"
                                                                    id="office_name" placeholder="Office Name"
                                                                    class="form-control office_name"
                                                                    value="{{ isset($officeDetails) ? $officeDetails?->name : '' }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 p-1">
                                                            <div class="form-group Address">
                                                                <label for="office_address">Address:</label><span
                                                                    class="office_cancle"><i class="fas fa-times"
                                                                        style="color: #f70707;"></i></span>
                                                                <input type="text" name="office[office_address]"
                                                                    id="office_address" placeholder="Address"
                                                                    class="form-control office_address"
                                                                    value="{{ isset($officeDetails) ? $officeDetails?->address : '' }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 p-1">
                                                            <div class="form-group City">
                                                                <label for="office_city">City:</label><span
                                                                    class="office_cancle"><i class="fas fa-times"
                                                                        style="color: #f70707;"></i></span>
                                                                <input type="text" name="office[office_city]"
                                                                    id="office_city" placeholder="City"
                                                                    class="form-control office_city"
                                                                    value="{{ isset($officeDetails) ? $officeDetails?->city : '' }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 p-1">
                                                            <div class="form-group Zip">
                                                                <label for="office_zip">Zip:</label><span
                                                                    class="office_cancle"><i class="fas fa-times"
                                                                        style="color: #f70707;"></i></span>
                                                                <input type="text" name="office[office_zip]"
                                                                    id="office_zip" placeholder="Zip"
                                                                    class="form-control office_zip"
                                                                    value="{{ isset($officeDetails) ? $officeDetails?->zip_code : '' }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 p-1">
                                                            <div class="form-group Phone">
                                                                <label for="office_phone">Phone:</label><span
                                                                    class="office_cancle"><i class="fas fa-times"
                                                                        style="color: #f70707;"></i></span>
                                                                <input type="text" name="office[office_phone]"
                                                                    id="office_phone" placeholder="Phone"
                                                                    class="form-control office_phone"
                                                                    value="{{ isset($officeDetails) ? $officeDetails?->phone_number : '' }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 p-1">
                                                            <div class="form-group Email">
                                                                <label for="office_mail">Email:</label><span
                                                                    class="office_cancle"><i class="fas fa-times"
                                                                        style="color: #f70707;"></i></span>
                                                                <input type="text" name="office[office_mail]"
                                                                    id="office_mail" placeholder="Email"
                                                                    class="form-control office_mail"
                                                                    value="{{ isset($officeDetails) ? $officeDetails?->email : '' }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 p-1">
                                                            <div class="form-group License">
                                                                <label for="office_license">License:</label><span
                                                                    class="office_cancle"><i class="fas fa-times"
                                                                        style="color: #f70707;"></i></span>
                                                                <input type="text" name="office[office_license]"
                                                                    id="office_license" placeholder="License"
                                                                    class="form-control office_license"
                                                                    value="{{ isset($officeDetails) ? $officeDetails?->license_number : '' }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- </div> --}}
                                            </div>
                                            {{-- **************************************************************** --}}
                                            <div class="row">
                                                {{-- ********************************Customer Address**************** --}}
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <div class="address-card">
                                                        <div class="section-title mb-3"><b>Address</b></div>
                                                        <div class="row">
                                                            <input type="hidden" name="customer[uuid]" id="uuid"
                                                                class="uuid"
                                                                value="{{ $customerDetails->uuid ?? '' }}">
                                                            <div class="col-md-6 col-sm-6">
                                                                <div class="add-form-panel mb-3">
                                                                    <label for="">Name : <span
                                                                            class="addres_bill_cancle"><i
                                                                                class="fas fa-times"
                                                                                style="color: #f70707;"></i></span></label>
                                                                    <div class="form-group">
                                                                        <input type="text" name="customer[cust_name]"
                                                                            id="cust_name"
                                                                            class="form-control cust_name"
                                                                            value="{{ isset($customerDetails) ? $customerDetails?->name : '' }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-sm-6">
                                                                <div class="add-form-panel mb-3">
                                                                    <label for="">Country : <span
                                                                            class="addres_bill_cancle"><i
                                                                                class="fas fa-times"
                                                                                style="color: #f70707;"></i></span></label>
                                                                    <div class="form-group">
                                                                        <select name="customer[cust_country]"
                                                                            id="cust_country"
                                                                            class="form-control cust_country"
                                                                            value="{{ isset($customerDetails) ? $customerDetails?->profile->country_id : '' }}">
                                                                            <option value="">---Select Country---
                                                                            </option>
                                                                            {{ getCountry('') }}
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-sm-6">
                                                                <div class="add-form-panel mb-3">
                                                                    <label for="">Zip Code : <span
                                                                            class="addres_bill_cancle"><i
                                                                                class="fas fa-times"
                                                                                style="color: #f70707;"></i></span></label>
                                                                    <div class="form-group">
                                                                        <input type="text"
                                                                            name="customer[cust_zip_code]"
                                                                            id="cust_zip_code"
                                                                            class="form-control cust_zip_code"
                                                                            value="{{ isset($customerDetails) ? $customerDetails?->profile->zip_code : '' }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-sm-6">
                                                                <div class="add-form-panel mb-3">
                                                                    <label for="">State : <span
                                                                            class="addres_bill_cancle"><i
                                                                                class="fas fa-times"
                                                                                style="color: #f70707;"></i></span></label>
                                                                    <div class="form-group">
                                                                        <select name="customer[cust_state]"
                                                                            id="cust_state"
                                                                            class="form-control cust_state"
                                                                            value="{{ isset($customerDetails) ? $customerDetails?->profile->state_id : '' }}">>
                                                                            <option value="">---Select State---
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-sm-6">
                                                                <div class="add-form-panel mb-3">
                                                                    <label for="">Address : <span
                                                                            class="addres_bill_cancle"><i
                                                                                class="fas fa-times"
                                                                                style="color: #f70707;"></i></span></label>
                                                                    <div class="form-group">
                                                                        <textarea class="form-control cust_addres"
                                                                            name="customer[cust_addres]"
                                                                            id="cust_addres" rows="1" cols="10"
                                                                            {{-- {{ isset($customerDetails?->profile?->address) ? 'readonly' : '' }}
                                                                            --}}>{{ $customerDetails?->profile?->address ?? '' }}</textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-sm-6">
                                                                <div class="add-form-panel mb-3">
                                                                    <label for="">City : <span
                                                                            class="addres_bill_cancle"><i
                                                                                class="fas fa-times"
                                                                                style="color: #f70707;"></i></span></label>
                                                                    <div class="form-group">
                                                                        <select name="customer[cust_city]"
                                                                            id="cust_city"
                                                                            class="form-control cust_city"
                                                                            value="{{ isset($customerDetails) ? $customerDetails?->profile->city_id : '' }}">>
                                                                            <option value="">---Select City---
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- ********************************Billing Address***************************************** --}}
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <div class="address-card">
                                                        <div class="section-title mb-3"><b>Billing Address</b></div>
                                                        <div class="row">
                                                            <input type="hidden" name="customer_bill[uuid]" id="uuid"
                                                                class="billing_uuid"
                                                                value="{{ $customerDetails->billingDitails?->uuid ?? '' }}">
                                                            <div class="col-md-6 col-sm-6">
                                                                <div class="add-form-panel mb-3">
                                                                    <label for="">Building No. :<span
                                                                            class="addres_bill_cancle"> <i
                                                                                class="fas fa-times"
                                                                                style="color: #f70707;"></i></span></label>
                                                                    <div class="form-group">
                                                                        <input type="text"
                                                                            class="form-control billing-address billing_building_number"
                                                                            id="billing_building_number"
                                                                            name="customer_bill[billing_building_number]"
                                                                            value="{{ $customerDetails?->billingDitails?->building_number ?? '' }}"
                                                                            {{-- {{ isset($customerDetails?->billingDitails?->building_number) ? 'readonly' : '' }}
                                                                            --}}>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-sm-6">
                                                                <div class="add-form-panel mb-3">
                                                                    <label for="">Street :<span
                                                                            class="addres_bill_cancle"><i
                                                                                class="fas fa-times"
                                                                                style="color: #f70707;"></i></span></label>
                                                                    <div class="form-group">
                                                                        <input type="text"
                                                                            class="form-control billing-address billing_street"
                                                                            id="billing_street"
                                                                            name="customer_bill[billing_street]"
                                                                            value="{{ $customerDetails?->billingDitails?->street ?? '' }}"
                                                                            {{-- {{ isset($customerDetails?->billingDitails?->street) ? 'readonly' : 'style=display:none;' }}
                                                                            --}}>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-sm-6">
                                                                <div class="add-form-panel mb-3">
                                                                    <label for="">Unit Number :<span
                                                                            class="addres_bill_cancle"><i
                                                                                class="fas fa-times"
                                                                                style="color: #f70707;"></i></span></label>
                                                                    <div class="form-group">
                                                                        <input type="text"
                                                                            class="form-control billing-address billing_unit_number"
                                                                            id="billing_unit_number"
                                                                            name="customer_bill[billing_unit_number]"
                                                                            value="{{ $customerDetails?->billingDitails?->unit_number ?? '' }}"
                                                                            {{-- {{ isset($customerDetails?->billingDitails?->unit_number) ? 'readonly' : '' }}
                                                                            --}}>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-sm-6">
                                                                <div class="add-form-panel mb-3">
                                                                    <label for="">Country :<span
                                                                            class="addres_bill_cancle"><i
                                                                                class="fas fa-times"
                                                                                style="color: #f70707;"></i></span></label>
                                                                    <div class="form-group">
                                                                        <select name="customer_bill[billing_country_id]"
                                                                            id="billing_country_id"
                                                                            class="form-control billing-address billing_select_country"
                                                                            {{-- {{ isset($customerDetails?->billingDitails?->country_id) ? 'readonly' : '' }}
                                                                            --}}>
                                                                            <option value="">---Select Country---
                                                                            </option>
                                                                            {{ getCountry('') }}
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-sm-6">
                                                                <div class="add-form-panel mb-3">
                                                                    <label for="">Zip Code :<span
                                                                            class="addres_bill_cancle"><i
                                                                                class="fas fa-times"
                                                                                style="color: #f70707;"></i></span></label>
                                                                    <div class="form-group">
                                                                        <input type="text"
                                                                            class="form-control billing-address billing_zip_code"
                                                                            name="customer_bill[billing_zip_code]"
                                                                            id="billing_zip_code"
                                                                            value="{{ $customerDetails?->billingDitails?->zip_code ?? '' }}"
                                                                            {{-- {{ isset($customerDetails?->billingDitails?->zip_code) ? 'readonly' : '' }}
                                                                            --}}>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-sm-6">
                                                                <div class="add-form-panel mb-3">
                                                                    <label for="">State :<span
                                                                            class="addres_bill_cancle"><i
                                                                                class="fas fa-times"
                                                                                style="color: #f70707;"></i></span></label>
                                                                    <div class="form-group">
                                                                        <select name="customer_bill[billing_state_id]"
                                                                            id="billing_state_id"
                                                                            class="form-control billing-address billing_select_state"
                                                                            {{-- {{ isset($customerDetails?->billingDitails?->state_id) ? 'readonly' : '' }}
                                                                            --}}>
                                                                            <option value="">---Select State---
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-sm-6">
                                                                <div class="add-form-panel mb-3">
                                                                    <label for="">Account No. :<span
                                                                            class="addres_bill_cancle"><i
                                                                                class="fas fa-times"
                                                                                style="color: #f70707;"></i></span></label>
                                                                    <div class="form-group">
                                                                        <input type="text"
                                                                            class="form-control billing-address billing_ac_no"
                                                                            name="customer_bill[billing_ac_no]"
                                                                            id="billing_ac_no"
                                                                            value="{{ $customerDetails?->billingDitails?->ac_no ?? '' }}"
                                                                            {{-- {{ isset($customerDetails?->billingDitails?->ac_no) ? 'readonly' : '' }}
                                                                            --}}>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-sm-6">
                                                                <div class="add-form-panel">
                                                                    <label for="">City :<span
                                                                            class="addres_bill_cancle"><i
                                                                                class="fas fa-times"
                                                                                style="color: #f70707;"></i></span></label>
                                                                    <div class="form-group">
                                                                        <select name="customer_bill[billing_city_id]"
                                                                            id="billing_city_id"
                                                                            class="form-control billing-address billing_select_city"
                                                                            {{-- {{ isset($customerDetails?->billingDitails?->city_id) ? 'readonly' : '' }}
                                                                            --}}>
                                                                            <option value="">---Select City---
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="save-billing-detail"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- ************************************************************ --}}
                                            </div>
                                            {{-- ******************************** Service Plans Section***************************************** --}}
                                            <div class="mb-4">
                                                <h5 class="section-title mb-3 "><b>Service Plans</b></h5>
                                                <div class="mb-3 ">
                                                    <div class="form-check form-check-inline">
                                                        <input
                                                            class="form-check-input check-service-plan drywood_and_subterranean_termite_service"
                                                            type="checkbox" id="servicePlan1"
                                                            name="service_plan[type][]"
                                                            value="drywood_and_subterranean_termite_service">
                                                        <label class="form-check-label" for="servicePlan1">Drywood and
                                                            Subterranean Termite Service</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input
                                                            class="form-check-input check-service-plan drywood_only_service"
                                                            type="checkbox" id="servicePlan2"
                                                            name="service_plan[type][]" value="drywood_only_service">
                                                        <label class="form-check-label" for="servicePlan2">Drywood
                                                            Only
                                                            Service</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input
                                                            class="form-check-input check-service-plan subterranean_only_service"
                                                            type="checkbox" id="servicePlan3"
                                                            name="service_plan[type][]"
                                                            value="subterranean_only_service">
                                                        <label class="form-check-label" for="servicePlan3">Subterranean
                                                            Only Service</label>
                                                    </div>
                                                </div>
                                                <div class="service-plan-detail">
                                                </div>
                                                <p>"Monthly payment plans Require a 24 month minimum. Unless the
                                                    structure(s) is "free of drywood termite infestations" at the time
                                                    of
                                                    inspection service plans must be coupled with an initial local
                                                    treatment
                                                    or fumigation.</p>
                                            </div>
                                            {{-- ********************************One-Time Services Section***************************************** --}}
                                            <div class="mb-4">
                                                <h5 class="section-title mb-3"><b>One-Time Services</b></h5>
                                                <div class="mb-3 d-flex flex-wrap ">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="fumigationCheck" name="services[fumigation][]"
                                                            value="fumigation">
                                                        <label class="form-check-label"
                                                            for="fumigationCheck">Fumigation</label>
                                                        <input class="form-control one-time-service-input" type="text"
                                                            id="fumigationInput"
                                                            name="services[fumigation][fumigation_cost]"
                                                            class="service-cost" value="">
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="termiteWarrantyCheck"
                                                            name="services[termite_warranty][]"
                                                            value="termite_warranty">
                                                        <label class="form-check-label"
                                                            for="termiteWarrantyCheck">Termite
                                                            Warranty</label>
                                                        <input class="form-control one-time-service-input" type="text"
                                                            id="termiteWarrantyCost"
                                                            name="services[termite_warranty][termite_warranty_cost]"
                                                            class="service-cost">
                                                        <label class="form-check-label" for="tileType">Type of
                                                            Tile</label>
                                                        <input class="form-control one-time-service-input" type="text"
                                                            id="tileType" name="services[termite_warranty][tile_type]"
                                                            class="tile-type">
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="boricAcidCheck" name="services[boric_acid][]"
                                                            value="boric_acid">
                                                        <label class="form-check-label" for="boricAcidCheck">Boric
                                                            Acid
                                                            Preventative treatment</label>
                                                        <input class="form-control one-time-service-input" type="text"
                                                            id="boricAcidCost"
                                                            name="services[boric_acid][boric_acid_cost]"
                                                            class="service-cost">
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="localTreatCheck" name="services[local_treat][]"
                                                            value="local_treat">
                                                        <label class="form-check-label" for="localTreatCheck">One-Time
                                                            or
                                                            initial Local treat</label>
                                                        <input class="form-control one-time-service-input" type="text"
                                                            id="localTreatCost"
                                                            name="services[local_treat][local_treat_cost]"
                                                            class="service-cost">
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="woodRepairsCheck" name="services[wood_repairs][]"
                                                            value="wood_repairs">
                                                        <label class="form-check-label" for="woodRepairsCheck">Wood
                                                            Repairs</label>
                                                        <input class="form-control one-time-service-input" type="text"
                                                            id="woodRepairsCost"
                                                            name="services[wood_repairs][wood_repairs_cost]"
                                                            class="service-cost">
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="wdoInspectionCheck" name="services[wdo_inspection][]"
                                                            value="wdo_inspection">
                                                        <label class="form-check-label" for="wdoInspectionCheck">WDO
                                                            Inspection Report</label>
                                                        <input class="form-control one-time-service-input" type="text"
                                                            id="wdoInspectionCost"
                                                            name="services[wdo_inspection][wdo_inspection_cost]"
                                                            class="service-cost">
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="insulationCheck" name="services[insulation][]"
                                                            value="insulation">
                                                        <label class="form-check-label"
                                                            for="insulationCheck">Insulation</label>
                                                        <input class="form-control one-time-service-input" type="text"
                                                            id="insulationCost"
                                                            name="services[insulation][insulation_cost]"
                                                            class="service-cost">
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox" id="otherCheck"
                                                            name="services[other][]" value="other">
                                                        <label class="form-check-label" for="otherCheck">Other</label>
                                                        <input class="form-control one-time-service-input" type="text"
                                                            id="otherDescription"
                                                            name="services[other][other_description]"
                                                            class="other-description">
                                                        <label class="form-check-label" for="otherCost">$</label>
                                                        <input class="form-control one-time-service-input" type="text"
                                                            id="otherCost" name="services[other][other_cost]"
                                                            class="service-cost">
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="termiteDiscountCheck"
                                                            name="services[termite_discount][]"
                                                            value="termite_discount">
                                                        <label class="form-check-label"
                                                            for="termiteDiscountCheck">Termite
                                                            bundled discount</label>
                                                        <input class="form-control one-time-service-input" type="text"
                                                            id="termiteDiscountCost"
                                                            name="services[termite_discount][termite_discount_cost]"
                                                            class="service-cost">
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- ********************************Description Section***************************************** --}}
                                            <div class="row">
                                                <div class="section-title mb-3"><b>Description of Services and Vendor
                                                        for
                                                        Repairs (if applicable)</b></div>
                                                <div class="mb-3">
                                                    <div class="form-check form-check-inline">
                                                        <label class="form-check-label"
                                                            for="servicePlan1">Description</label>
                                                        <textarea name="services[description]" id="description_services"
                                                            class="form-control" rows="3" cols="100"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- ********************************Payment and Terms Section***************************************** --}}
                                            <div class="row">
                                                <div class="section-title mb-3"><b>Payment and Terms</b></div>
                                                <div class="mb-3">
                                                    <div class="form-check form-check-inline">
                                                        <label class="form-check-label" for="totalInitial">Total
                                                            Initial</label>
                                                        <input class="form-control" type="text" id="totalInitial"
                                                            name="payment[total_initial]">
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <label class="form-check-label" for="totalYearlyRenewal">Total
                                                            Yearly
                                                            Renewal</label>
                                                        <input class="form-control" type="text" id="totalYearlyRenewal"
                                                            name="payment[total_yearly_renewal]">
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <label class="form-check-label" for="monthlyRenewal">Monthly
                                                            Renewal</label>
                                                        <input class="form-control" type="text" id="monthlyRenewal"
                                                            name="payment[monthly_renewal]">
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- ******************************** Editable Dynamic Text Box Section***************************************** --}}
                                            <div class="row">
                                                <div class="section-title mb-3"><b>Editable Dynamic</b></div>
                                                <div class="mb-3">
                                                    <div class="form-check form-check-inline">
                                                        <label class="form-check-label" for="dynamicTextBox1">Editable
                                                            Dynamic
                                                            Text Box 1</label>
                                                        <textarea name="dynamicTextBox[dynamic_text_box_one]"
                                                            id="dynamicTextBox1" class="form-control" rows="3"
                                                            cols="100"></textarea>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <label class="form-check-label" for="initialHere1">Initial
                                                            Here</label>
                                                        <input class="form-control" type="text" id="initialHere1"
                                                            name="dynamicTextBox[dynamic_text_box_one][initial_here_one]">
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="form-check form-check-inline">
                                                        <label class="form-check-label" for="dynamicTextBox2">Editable
                                                            Dynamic
                                                            Text Box 2</label>
                                                        <textarea name="dynamicTextBox[dynamic_text_box_two]"
                                                            id="dynamicTextBox2" class="form-control" rows="3"
                                                            cols="100"></textarea>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <label class="form-check-label" for="initialHere2">Initial
                                                            Here</label>
                                                        <input class="form-control" type="text" id="initialHere2"
                                                            name="dynamicTextBox[dynamic_text_box_two][initial_here_two]">
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="form-check form-check-inline">
                                                        <label class="form-check-label" for="dynamicTextBox3">Editable
                                                            Dynamic
                                                            Text Box 3</label>
                                                        <textarea name="dynamicTextBox[dynamic_text_box_three]"
                                                            id="dynamicTextBox3" class="form-control" rows="3"
                                                            cols="100"></textarea>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <h5>Customer Signature</h5>
                                                    <div class="form-check form-check-inline">
                                                        <label class="form-check-label" for="dynamicTextBox4">Editable
                                                            Dynamic
                                                            Text Box 4</label><br>
                                                        <textarea name="dynamicTextBox[dynamic_text_box_four]"
                                                            id="dynamicTextBox4" class="form-control" rows="3"
                                                            cols="100"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- ********************************************************** --}}
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger cancle-page"
                                            data-bs-dismiss="modal">CANCEL</button>
                                        <a href="javascript:void(0)" class="btn btn-info  saveAndNext"
                                            id="saveAndNext">Save & Next -></a>
                                        <a href="javascript:void(0)" class="btn btn-primary addAgremmet saveAndUpload"
                                            id="saveAndUpload">Save & PDF</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- // *********************************************************************************** --}}
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script type="text/javascript" src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.3/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ asset('assets/js/common.js') }}"></script>
    <script>
        // var APP_URL = {!! json_encode(url('/')) !!};
        // var TOAST_POSITION = 'top-right';
    </script>
    <script>
        $(document).on("click", ".cancle-page", function () {
            window.location.href = "{{ route('agreement.list') }}";
        });
        // ********************************************************
        $(document).ready(function () {
            setDropdownValues();
            setbillingDropdownValues();
            $('.one-time-service-input').prop('disabled', true);
            $('.search_form').hide();
            $('.saveAndUpload').hide();
        });
        // ********************************************************
        $(".saveAndNext").on('click', function (e) {
            $('.office_cancle').hide();
            $('.addres_bill_cancle').hide();
            $('.saveAndNext').hide();
            $('.search_form').show();
            // $('.searchCustomerOffice').prop('disabled', true);
            $('.saveAndUpload').show();
        });
        // *****************************Header**************************************************
        $(document).on("click", ".add-heading", function () {
            // let id = $(this).siblings("h3.form-title.head-title");
            $(".edit-title").hide();
            $(".title-save").show();
            $(".head-title").replaceWith(
                $('<input>').attr({
                    type: 'text',
                    id: 'fileInput',
                    name: 'title',
                }).on('input', function () {
                    // Update the h3 tag with the input value
                    const inputValue = $(this).val();
                    if (!$('.head-title').length) {
                        $(this).after('<h3 class="head-title"></h3>');
                    }
                    $('.head-title').text(inputValue);
                })
            );
        });
        $(document).on("click", ".title-save", function () {
            $("#fileInput").hide();
            $(".edit-title").show();
            $(".title-save").hide();
        });
        $(document).on("click", ".img-upload", function () {
            // let id = $(this).siblings("h3.form-title.head-title");
            $(".office-logo").replaceWith(
                $('<input>').attr({
                    type: 'file',
                    id: 'fileInput',
                    name: 'office_logo',
                    accept: 'image/*'
                }).on('change', function (event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            // Remove any existing image
                            $('.office-logo').remove();
                            // Create and insert the new image
                            const img = $('<img>').attr({
                                src: e.target.result,
                                class: 'office-logo',
                                alt: 'Uploaded Logo'
                            });
                            $('#fileInput').after(img);
                        };
                        reader.readAsDataURL(file);
                    }
                })
            );
        });
        $(document).on("click", ".office_cancle", function () {
            let id = $(this).siblings("input");
            id.closest('div').remove();
            console.log(id);
            // alert("cancel");
        });
        // $(document).on("click", ".upload-img", function() {
        // });
        // **************************Addres & Billing Delete Input Filed****************************
        $(document).on("click", ".addres_bill_cancle", function () {
            let id = $(this).closest('div').parent();
            id.remove();
        });
        // *************************Service Plans***************************************************
        $(document).on("click", ".check-service-plan", function () {
            let id = $(this).attr("id");
            let label = $("label[for='" + id + "']").html();
            let val = $("#" + id).val();
            let appData = ` <div class="mb-3 ${id }">
                            <div>
                            ${label}
                            </div>
                            <div class="form-check form-check-inline">
                                <label class="form-check-label" for="${id}">Initial</label>
                                <input class="form-control initial_${val}" type="text" id="${id}" name="service_plan[${val}][initial]" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                            <div class="form-check form-check-inline">
                                <label class="form-check-label" for="${id}">Yearly
                                    Renewal</label>
                                <input class="form-control yearly_${val}" type="text" id="${id}" name="service_plan[${val}][yearly]" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                            <div class="form-check form-check-inline">
                                <label class="form-check-label" for="${id}">Monthly
                                    Renewal</label>
                                <input class="form-control monthly_${val}" type="text" id="${id}" name="service_plan[${val}][monthly]" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                            </div>`;
            if ($(this).is(':checked')) {
                $('.service-plan-detail').append(appData);
            } else {
                $('.service-plan-detail').find(`.${id}`).remove();
            }
        });
        $(document).on("input", ".form-control", function () {
            let totalInitial = 0;
            let totalYearlyRenewal = 0;
            let monthlyRenewal = 0;
            $(".form-control").each(function () {
                let classList = $(this).attr('class').split(/\s+/);
                $.each(classList, function (index, item) {
                    if (item.startsWith('initial_')) {
                        totalInitial += parseFloat($(this).val()) || 0;
                    } else if (item.startsWith('yearly_')) {
                        totalYearlyRenewal += parseFloat($(this).val()) || 0;
                    } else if (item.startsWith('monthly_')) {
                        monthlyRenewal += parseFloat($(this).val()) || 0;
                    }
                }.bind(this));
            });
            $("#totalInitial").val(totalInitial.toFixed(2));
            $("#totalYearlyRenewal").val(totalYearlyRenewal.toFixed(2));
            $("#monthlyRenewal").val(monthlyRenewal.toFixed(2));
        });
        // *************************************************************************
        $('.form-check-input').on('change', function () {
            // Find the closest input field to the checkbox
            $(this).closest('.form-check').find('.form-control').prop('disabled', !this.checked);
        });
        // *************************Country ,state,city***************************
        $(document).on("change", ".cust_country", function () {
            let country = $(this).val();
            stateByCountry(country, ".cust_state");
        });
        $(document).on("change", ".cust_state", function () {
            let state = $(this).val();
            getCity(state, ".cust_city");
        });
        // **************************************Billing*************************
        $(document).on("change", ".billing_select_country", function () {
            let country = $(this).val();
            stateByCountry(country, ".billing_select_state");
        });
        $(document).on("change", ".billing_select_state", function () {
            let state = $(this).val();
            getCity(state, ".billing_select_city");
        });

        function setDropdownValues() {
            let uuid = $('#uuid').val();
            if (uuid) {
                var countryId = "{{ $customerDetails->profile->country_id ?? '' }}";
                var stateId = "{{ $customerDetails->profile->state_id ?? '' }}";
                var cityId = "{{ $customerDetails->profile->city_id ?? '' }}";
                // Set selected value for Country
                if (countryId) {
                    $('#cust_country').val(countryId).trigger('change'); // Trigger change to update states
                }
                // Set selected value for State
                if (stateId) {
                    $('#cust_state').val(stateId).trigger('change'); // Trigger change to update cities
                    // alert(stateId)
                    getCity(stateId, "#cust_city");
                }
                // // Set selected value for City
                setTimeout(function () {
                    if (cityId) {
                        // alert(cityId)
                        $('#cust_city').val(cityId).trigger(
                            'change'); // Trigger change to ensure any dependent logic is executed
                    }
                }, 1000);
            }
        }

        function setbillingDropdownValues() {
            let uuid = $('#uuid').val();
            if (uuid) {
                var billingCountryId =
                    "{{ $customerDetails->billingDitails->country_id ?? '' }}";
                var billingstateId =
                    "{{ $customerDetails->billingDitails->state_id ?? '' }}";
                var billingcityId =
                    "{{ $customerDetails->billingDitails->city_id ?? '' }}";
                // Set selected value for Country
                if (billingCountryId) {
                    $('#billing_country_id').val(billingCountryId).trigger(
                        'change'); // Trigger change to update states
                }
                // Set selected value for State
                if (billingstateId) {
                    $('#billing_state_id').val(billingstateId).trigger('change'); // Trigger change to update cities
                    getCity(billingstateId, "#billing_city_id");
                }
                // // Set selected value for City
                setTimeout(function () {
                    if (billingcityId) {
                        $('#billing_city_id').val(billingcityId).trigger(
                            'change'); // Trigger change to ensure any dependent logic is executed
                    }
                }, 1000);
            }
        }
        // ******************************************************************************
        function deleteImage(index) {
            let imgId = 'img' + index;
            let previewImgId = 'previewimg' + index;
            let deleteBtnId = 'delete' + index;
            document.getElementById(imgId).value = '';
            document.getElementById(previewImgId).src =
                `{{ asset('assets/images/blank${index}.png') }}`;
            document.getElementById(deleteBtnId).style.display = 'none';
        }
        // **************************Make a pdf use canva  with submit a form**************
        document.getElementById('saveAndUpload').addEventListener('click', async function () {
            if ($('#fileInput').length > 0) {
                $('#fileInput').hide();
                const {
                    jsPDF
                } = window.jspdf;
                const pdf = new jsPDF('p', 'mm', 'a4');
                const pdfWidth = pdf.internal.pageSize.getWidth();
                const pdfHeight = pdf.internal.pageSize.getHeight();
                const margin = 10;
                const contentDivs = document.querySelectorAll('.mainBody');
                // Apply temporary styles
                contentDivs.forEach(div => Object.assign(div.style, {
                    fontSize: '1.65rem',
                    border: 'none'
                }));
                for (const div of contentDivs) {
                    const canvas = await html2canvas(div, {
                        scale: 2,
                        useCORS: true,
                        backgroundColor: null,
                    });
                    const imgData = canvas.toDataURL('image/png');
                    const imgWidth = pdfWidth - margin * 2;
                    const imgHeight = (canvas.height * imgWidth) / canvas.width;
                    // alert("height" + imgHeight)
                    let y = margin;
                    let remainingHeight = imgHeight;
                    // alert("remainingHeight" + remainingHeight)
                    const renderHeight = Math.min(remainingHeight, pdfHeight - margin * 2);
                    pdf.addImage(imgData, 'PNG', margin, y, imgWidth, renderHeight);
                    remainingHeight -= renderHeight;
                }
                pdf.save("document.pdf");
                // Reset styles
                contentDivs.forEach(div => Object.assign(div.style, {
                    fontSize: '',
                    border: '1px solid #002947'
                }));
            } else {
                const headTitleElement = document.querySelector('.head-title');
                if (headTitleElement) {
                    showToast("Please Enter Title", "warning");
                }
            }
        });
        // ********************************************************************************
        $(document).on("click", '.searchCustomerOffice', function () {
            const officeId = $('.select_office').val();
            const customerId = $('.select_customer').val();
            // alert(officeId)
            if (!customerId || customerId.trim() === "") {
                showToast("Please Select a Valid Customer", "warning");
                return;
            }
            if (!officeId || officeId.trim() === "") {
                showToast("Please Select a Valid Office", "warning");
                return;
            }
            if (!customerId && !officeId) {
                showToast("Please Select a Valid  Customer & Office", "warning");
                return;
            }
            $.ajax({
                url: "{{ route('ajax.fetchOfficeDetails') }}",
                type: "GET",
                data: {
                    officeId: officeId,
                    customerId: customerId
                },
                success: function (response) {
                    console.log(response);
                    // ***********************Header ************************************
                    if (response.officeDetails && response.officeDetails.logo) {
                        $('.no_img').hide();
                        $('.office-logo').remove();
                        // Append the new image
                        const img = $('<img>').attr({
                            src: response.officeDetails.logo_path,
                            class: 'office-logo',
                            alt: 'Office Logo'
                        });
                        $('.upload-img').after(img);
                    }
                    // **********************************************************
                    if (response.officeDetails.color) {
                        $('.section-title').css('background-color', response
                            .officeDetails.color);
                    }
                    // ***********************Office Details ************************************
                    $('.office_id').val(response.officeDetails.uuid);
                    if ($('.office_name').length > 0) {
                        $('.office_name').val(response.officeDetails.name);
                    }
                    if ($('.office_address').length > 0) {
                        $('.office_address').val(response.officeDetails.address);
                    }
                    if ($('.office_city').length > 0) {
                        $('.office_city').val(response.officeDetails.city);
                    }
                    if ($('.office_zip').length > 0) {
                        $('.office_zip').val(response.officeDetails.zip_code);
                    }
                    if ($('.office_phone').length > 0) {
                        $('.office_phone').val(response.officeDetails.phone_number);
                    }
                    if ($('.office_mail').length > 0) {
                        $('.office_mail').val(response.officeDetails.email);
                    }
                    if ($('.office_license').length > 0) {
                        $('.office_license').val(response.officeDetails.license_number);
                    }
                    // ***********************Customer Details ************************************
                    if ($('.uuid').length > 0) {
                        $('.uuid').val(response.customerDetails.uuid);
                    }
                    if ($('.cust_name').length > 0) {
                        $('.cust_name').val(response.customerDetails.name);
                    }
                    if ($('.cust_country').length > 0) {
                        $('.cust_country').val(response.customerDetails.profile
                            .country_id);
                    }
                    if ($('.cust_zip_code').length > 0) {
                        $('.cust_zip_code').val(response.customerDetails.profile
                            .zip_code);
                    }
                    if ($('.cust_state').length > 0) {
                        $('.cust_state').val(response.customerDetails.profile.state_id);
                    }
                    if ($('.cust_addres').length > 0) {
                        $('.cust_addres').val(response.customerDetails.profile.address);
                    }
                    if ($('.cust_city').length > 0) {
                        $('.cust_city').val(response.customerDetails.profile.city_id);
                    }
                    // // ***********************Billing Details ************************************
                    if ($('.billing_uuid').length > 0) {
                        $('.billing_uuid').val(response.billingDetails.uuid);
                    }
                    if ($('.billing_building_number').length > 0) {
                        $('.billing_building_number').val(response.billingDetails
                            .building_number);
                    }
                    if ($('.billing_street').length > 0) {
                        $('.billing_street').val(response.billingDetails.street);
                    }
                    if ($('.billing_unit_number').length > 0) {
                        $('.billing_unit_number').val(response.billingDetails
                            .unit_number);
                    }
                    if ($('.billing_select_country').length > 0) {
                        $('.billing_select_country').val(response.billingDetails
                            .country_id);
                    }
                    if ($('.billing_zip_code').length > 0) {
                        $('.billing_zip_code').val(response.billingDetails.zip_code);
                    }
                    if ($('.billing_select_state').length > 0) {
                        $('.billing_select_state').val(response.billingDetails
                            .state_id);
                    }
                    if ($('.billing_ac_no').length > 0) {
                        $('.billing_ac_no').val(response.billingDetails.ac_no);
                    }
                }
            });
        });
        // ********************************************************************************

    </script>
</body>

</html>
