@extends('layout.app')
@section('content')
    <section class="userlist_sec">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    @include('layout.partials.headbar')
                    <div class="usertab_con">
                        <div class="tabcon_head">
                            <div class="row">
                                <div class="col-md-3">
                                    <h3 class="user_title">Agreements</h3>
                                </div>
                                <div class="col-md-9">
                                    <div class="tabconh_right">
                                        <form action="{{ route('agreement.list') }}" method="POST">
                                            <div class="search_sec">
                                                <div class="search_box">
                                                    <button class="search_btn">
                                                        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                                                    </button>
                                                    <input type="search" name="search" id="search" class="form-control"
                                                        placeholder="Search" value="{{ Request::get('search') ?? '' }}">
                                                </div>
                                                <div class="action_btn">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                                <div class="btn btn-primary" class="action_btn">
                                                    <a href="{{ route('agreement.list') }}"><i
                                                            class="fa-solid fa-rotate text-white"></i></a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tabcon_body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">Name</th>
                                            <th scope="col">Phone Number</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Licence Number</th>
                                            <th scope="col">Address</th>
                                            <th scope="col">Color</th>
                                            <th scope="col">Office Name</th>
                                            <th scope="col">Office No</th>
                                            <th scope="col">Inspection Date</th>
                                            <th scope="col">Logo</th>
                                            <th scope="col" style="width: 10%;">
                                                <button class="btn btn-primary addModalBtn" data-bs-toggle="modal"
                                                    data-bs-target="#addModal">
                                                    <span><i class="fa-solid fa-plus"></i></span>ADD NEW
                                                </button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($details as $key=> $detail)
                                            @php
                                                $logo =
                                                    $detail->logo != null
                                                        ? asset('storage/profile/' . $detail->logo)
                                                        : asset('assets/images/no_img.jpg');
                                            @endphp
                                            <tr>
                                                <td>{{ $detail->name ?? 'N/A' }}</td>
                                                <td>{{ $detail->phone_number ?? 'N/A' }}</td>
                                                <td>{{ $detail->email ?? 'N/A' }}</td>
                                                <td>{{ $detail->license_number ?? 'N/A' }}</td>
                                                <td>{{ $detail->address ?? 'N/A' }}</td>
                                                <td>{{ $detail->color ?? 'N/A' }}</td>
                                                <td>{{ $detail->office_name ?? 'N/A' }}</td>
                                                <td>{{ $detail->office_number ?? 'N/A' }}</td>
                                                <td>{{ $detail->inspection_date ?? 'N/A' }}</td>
                                                <td><img src="{{ $logo }}" alt="" width="200px"
                                                        height="200px"></td>
                                                <td>
                                                    <div class="action_box">
                                                        <x-button.action :detail="$detail" :key="$key" />
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8">No Data Found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('modal')
    <div class="modal addModal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel"><span id="modalName">ADD</span> AGREEMENT</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <form action="{{ route('agreement.add') }}" class="formSubmit fileUpload" id="addFrm"
                    enctype="multipart/form-data">
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="addnumodal">
                            <div class="editmodal_head">
                                <div class="toggle_icon switch_toggle">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input checkValChange" type="checkbox" role="switch"
                                            id="is_active" name="is_active" value="1" checked>
                                    </div>
                                </div>
                                <div class="status_box">
                                    <p class="active_status">
                                        <span><i class="fa-solid fa-circle" aria-hidden="true"></i></span><em
                                            id="activeStatus">Active</em>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group single_form col-md-12">
                                <label for="name">Customer Name</label>
                                <input type="text" class="form-control" placeholder="Customer Name" id="name"
                                    name="name">
                            </div>
                            <div class="row">
                                <div class="form-group single_form col-md-6">
                                    <label for="phone_number">Customer Number</label>
                                    <input type="text" class="form-control number-only" placeholder="Customer Number"
                                        id="phone_number" name="phone_number" maxlength="10">
                                </div>

                                <div class="form-group single_form col-md-6">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" placeholder="Email" id="email"
                                        name="email">
                                </div>
                            </div>

                            <div class="form-group single_form col-md-12">
                                <label for="license_number">Address</label>
                                <textarea class="form-control" name="address" id="address" cols="30" rows="3"></textarea>
                            </div>
                            <div class="row">
                                <div class="form-group single_form col-md-6">
                                    <label for="color">Color</label>
                                    <input type="color" class="form-control number-only" placeholder="color"
                                        id="color" name="color">
                                </div>

                            </div>
                            <div class="form-group single_form col-md-12">
                                <label for="office_name">Office Name></label>
                                <input type="text" class="form-control" placeholder="Office Name" id="office_name"
                                    name="office_name">
                            </div>
                            <div class="row">
                                <div class="form-group single_form col-md-6">
                                    <label for="license_number">Office Licence Number</label>
                                    <input type="text" class="form-control" placeholder="Office License Number"
                                        id="license_number" name="license_number">
                                </div>
                                <div class="form-group single_form col-md-6">
                                    <label for="office_number">Phone Number</label>
                                    <input type="text" class="form-control number-only"
                                        placeholder="Office Phone Number" id="office_number" name="office_number"
                                        maxlength="10">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group single_form col-md-6">
                                    <label for="inspection_date">Inspection Date</label>
                                    <input type="Date" class="form-control number-only" placeholder="Inspection Date"
                                        id="inspection_date" name="inspection_date">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group single_form col-md-8">
                                    <label for="logo">Logo</label>
                                    <input type="file" class="form-control showOnUpload" placeholder="Logo"
                                        id="logo" name="logo" data-location="showPhoto" accept="image/*">
                                </div>
                                <div class="form-group single_form mt-1 col-md-4">
                                    <img src="{{ asset('assets/img/avatars/blank-picture.jpg') }}" class="img-fluid"
                                        id="showPhoto" alt="">
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CANCLE</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).on("click", ".editModalOpen", function() {
            $('#modalName').html('EDIT');
            const detail = $(this).attr('data-detail');
            const detailObj = JSON.parse(detail);
            const logo = detailObj.logo;
            console.log(APP_URL + '/storage/profile/' + logo);

            $('#id').val(detailObj.id);
            if (detailObj.is_active == 1) {
                $('#is_active').attr('checked', true);
            } else {
                $('#is_active').attr('checked', false);
            }
            $('#name').val(detailObj.name);
            $('#license_number').val(detailObj.license_number);
            $('#phone_number').val(detailObj.phone_number);
            $('#email').val(detailObj.email);
            $('#address').val(detailObj.address);
            $('#color').val(detailObj.color);
            $('#office_name').val(detailObj.office_name);
            $('#license_number').val(detailObj.license_number);
            $('#office_number').val(detailObj.office_number);
            $('#inspection_date').val(detailObj.inspection_date);
            $('#showPhoto').val(detailObj.logo);

            // $('#maxie').val(detailObj.maxie);
        });
    </script>
@endpush
