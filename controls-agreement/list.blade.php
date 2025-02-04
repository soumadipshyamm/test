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
                                        <form action="{{ route('agreement.list') }}" method="POST" id="find_title_form">
                                            @csrf
                                            <div class="search_sec">
                                                <div class="search_box">
                                                    <button class="search_btn">
                                                        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                                                    </button>
                                                    <input type="search" name="search" id="search" class="form-control"
                                                        placeholder="Search" value="{{ Request::get('search') ?? '' }}">
                                                </div>
                                                <div class="action_btn">
                                                    <button type="submit" class="btn btn-primary find-btn">
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
                                            <th scope="col">Logo</th>
                                            <th scope="col">Office Name</th>
                                            <th scope="col">Customer Name</th>
                                            <th scope="col">phone No.</th>
                                            <th scope="col">Title</th>
                                            <th scope="col">Date</th>
                                            <th scope="col" style="width: 10%;">
                                                <a href="{{ route('agreement.add') }}"
                                                    class="btn btn-primary addModalBtn"><span><i
                                                            class="fa-solid fa-plus"></i></span>ADD NEW</a>
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
                                                <td><img src="{{ $logo }}" alt="" width="150px"
                                                        height="150px"></td>
                                                <td>{{ $data->offices->name }}</td>
                                                <td>{{ $data->customers->name }}</td>
                                                <td>{{ $data->customers->mobile_number }}</td>
                                                <td>{{ $data->title ?? 'N/A' }}</td>
                                                <td>{{ \Carbon\Carbon::parse($data->created_at)->format('Y-m-d') }}</td>
                                                <td>
                                                    <div class="action_box">
                                                        <a href="{{ route('agreement.edit', $data->uuid) }}"
                                                            data-detail='{{ json_encode($data) }}'>
                                                            <span class="text-primary"><i
                                                                    class="fa-solid fa-pen"></i></span>
                                                        </a>
                                                        <a class="deleteData" href="javascript:void(0)"
                                                            data-table="agreements" data-uuid="{{ $data->uuid }}">
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
                                <div class="pagination-wrapper">
                                    {{ $datas->appends(Request::except('page'))->links() }}
                                </div>
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
    <script>
        $(document).on('click', '.find-btn', function(e) {
            e.preventDefault(); // Prevent the default button action
            $('#find_title_form').submit(); // Submit the form
        });
    </script>

@endpush
