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
                                            <th scope="col">Office Details</th>
                                            <th scope="col">Customer Details</th>
                                            <th scope="col">Billing Details</th>
                                            <th scope="col">Licence Number</th>
                                            <th scope="col">Address</th>
                                            <th scope="col">Color</th>
                                            <th scope="col">Office Name</th>
                                            <th scope="col">Office No</th>
                                            <th scope="col">Inspection Date</th>
                                            <th scope="col">Logo</th>
                                            <th scope="col" style="width: 10%;">
                                                <a href="{{ route('agreement.add') }}"
                                                    class="btn btn-primary addModalBtn"><span><i
                                                            class="fa-solid fa-plus"></i></span>ADD</a>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($datas as $key=> $data)
                                            @php
                                                $customers = json_decode($data->customer);
                                                $offices = json_decode($data->office);
                                                $customer_billings = json_decode($data->customer_billing);
                                                $service_plans = json_decode($data->service_plan);
                                                $one_time_services = json_decode($data->one_time_service);
                                                $logo =
                                                    $data->offices->logo_path != null
                                                        ? $data->offices->logo_path
                                                        : asset('assets/images/no_img.jpg');
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div>
                                                        <p><img src="{{ $logo }}" alt="" width="100px"
                                                                height="100px"></p>
                                                        @foreach ($offices as $key => $office)
                                                            @if ($key !== 'office_id')
                                                                <p>{{ $key }}:{{ $office }}</p>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        @foreach ($customers as $key => $customer)
                                                            @if ($key !== 'uuid')
                                                                <p>{{ $key }}:{{ $customer }}</p>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        @foreach ($customer_billings as $key => $customer_billing)
                                                            @if ($key !== 'uuid')
                                                                <p>{{ $key }}:{{ $customer_billing }}</p>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        @foreach ($service_plans as $key => $service_plan)
                                                            @if ($key !== 'type')
                                                                <p>{{ $key }}</p>
                                                                @foreach ($service_plan as $k => $value)
                                                                    <p>{{ $k }}:{{ $value }}</p>
                                                                @endforeach
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>
                                                    <div class="action_box">
                                                        <a class="editModalOpen" href="javascript:void(0)"
                                                            data-detail='{{ json_encode($data->offices) }}'>
                                                            <span class="text-primary"><i
                                                                    class="fa-solid fa-pen"></i></span>
                                                        </a>
                                                        <a class="deleteData" href="javascript:void(0)"
                                                            data-table="agreements" data-uuid="{{ $data->offices->id }}">
                                                            <span class="text-danger">
                                                                <i class="fa-solid fa-trash-can"></i>
                                                            </span>
                                                        </a>
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

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    {{-- <script>
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
    </script> --}}
@endpush
