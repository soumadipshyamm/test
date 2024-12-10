@extends('layout.app')
@push('style')
@endpush
@section('content')
    <section class="leadlist_sec">
        <div class="container">
            <div class="tabcon_head">
                <div class="row">
                    <div class="col-md-2">
                        <h3 class="user_title">Customer List</h3>
                    </div>

                    <div class="col-md-10">
                        <form action="{{ route('lead.list') }}" method="GET" id="searchFrm">
                            <div class="tabconh_right">
                                <div class="search_box">
                                    <input type="search" name="search" class="form-control" placeholder="Search"
                                        value="{{ Request::get('search') ?? '' }}">
                                    <button class="search_btn">
                                        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                                    </button>
                                </div>

                                <div class="tabcon_addbtn">
                                    <a href="{{ route('customer.addCustomer') }}" class="btn btn-success"> <span><i
                                                class="fa-solid fa-plus"></i></span>
                                        ADD CUSTOMER</a>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="leadls_body">
                @forelse ($datas as $key=> $data)
                    <div class="customer-list-wrap pb-3">
                        <div class="customer-list customer-{{ $key }} data-uuid={{ $data->uuid }}">
                            <div class="top-bar">
                                <p>#{{ $data?->employee_id ?? ' ' }}</p>
                                <span class="sal-tag">Sales</span>
                            </div>
                            <div class="bottom-bar">
                                <div class="cus-box name">
                                    <a href="{{ route('customer.addCustomer', $data->uuid) }}"><strong><i
                                                class="fa-regular fa-user"></i>
                                            {{ $data?->name ?? '-----' }}</strong></a>
                                </div>
                                <div class="cus-box cusphone">
                                    <a href="tel:{{ $data?->mobile_number }}"><i class="fa-thin fa-mobile"
                                            style="color: #74C0FC;"></i>
                                        {{ $data?->mobile_number ?? '-----' }}</a>
                                </div>
                                <div class="cus-box cusMail">
                                    <a href="mailto:joykarmakar@shyamfuture.com"><img src="assets/images/envelope-icon.png"
                                            alt=""> {{ $data?->email ?? '-----' }}</a>
                                </div>
                                <div class="cus-box cusAddress">
                                    <p><i class="fa-solid fa-location-dot"></i> {{ $data?->profile?->address ?? '-----' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- <table id="leadList" class="stripe row-border order-column" style="width:100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($datas as $data)
                            <tr>
                                <td>{{ $data?->name }}</td>
                                <td>{{ $data?->mobile_number ?? 'N/A' }}</td>
                                <td>{{ $data?->email ?? 'N/A' }}</td>
                                <td>{{ $data?->profile?->address ?? 'N/A' }}</td>
                                <td> <a href="#" class="btn btn-primary ms-2">
                                    <span><i class="fa-solid fa-chart-line"></i></span>
                                    SALES
                                </a></td>

                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table> --}}
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        $(function() {
            var start = moment().subtract(29, 'days');
            var end = moment();

            function cb(start, end) {
                $('#reportrange span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
            }

            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                }
            }, cb);

            cb(start, end);

            $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
                $('#searchFrm').submit();
            });
        });

        $(document).on("click", ".editModalOpen", function() {
            const detail = $(this).attr('data-detail');
            const detailObj = JSON.parse(detail);
            $('#ref_id').val(detailObj.id);
        });

        $(document).on("change", "#sort_by", function() {
            $('#searchFrm').submit();
        });
        $(document).on("change", "#status", function() {
            $('#searchFrm').submit();
        });

        $(document).on("click", ".addNote", function() {
            const detail = $(this).attr('data-detail');
            const notes = $(this).attr('data-notes');
            const notesObj = JSON.parse(notes);
            const detailObj = JSON.parse(detail);
            let appendHtml = '';
            $('.notelist_box').html(`<div class="singlenote_list">
                                    <h6>No Note Found</h6>
                                </div>`);
            notesObj.forEach((item, index) => {
                appendHtml += `<div class="singlenote_list">
                                    <p>${dateFormatCreatedAt(new Date(item.created_at))}</p>
                                    <h6>${item.note}</h6>
                                </div>`;
            });
            if (appendHtml) {
                $('.notelist_box').html(appendHtml);
            }
            $('#referral_id').val(detailObj.id);
            $('#created_date').html(dateFormat(new Date(detailObj.created_at)));
            $('#name').html(detailObj.name);
            $('#contact_number').html(detailObj.phone_number);
            $('#call_id').attr('href', `tel:+${detailObj.phone_number}`);
            $('#address').html(detailObj.address);
        });

        $(document).on("change", "#status", function() {
            console.log(this.value);
            $('.hiDFld').addClass('d-none');
            if (this.value == 2) {
                $('.hiDFld').removeClass('d-none');
            }
        });
    </script>
@endpush
