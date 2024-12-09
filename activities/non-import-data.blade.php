@extends('Company.layouts.app')
@section('activities-active', 'active')
@section('title', __('Activities'))
@push('styles')
@endpush
@section('content')
    {{-- <div class="app-main__outer">
    <div class="app-main__inner card">
        <div class="page-title-actions">
            <a href="{{ route('company.activities.list') }}" class="mt-2 btn btn-secondary">&lt; Back</a>
</div>
<!-- dashboard body -->
<h1>NOn Import Data Export</h1>
{{$dataCount}}
<div>
    <h3>Export</h3>
    <a href="{{route('company.activities.NonImportDataExport')}}">Download</a>
</div>
</div>
</div> --}}
    <div class="app-main__outer">
        <div class="app-main__inner card">
            <!-- dashboard body -->
            <div class="dashboard_body">
                <div class="comp-top">
                    <a href="{{ route('company.activities.list') }}" class="mt-2 btn btn-secondary">&lt; Back</a>
                </div>
                <div class="comp-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="main-card mb-3 card">
                                <div class="card-body">
                                    <h5 class="card-title">Data Not Import</h5>
                                    <div class="table-responsive">
                                        <p>How Many Data Do not import = <span>{{ $dataCount }}</span></p>
                                    </div>
                                    <div class="table-responsive">
                                        <h3>Export Data
                                            <a href="{{ route('company.activities.NonImportDataExport') }}"
                                                class="mt-2 btn btn-secondary">Download</a>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
@endpush
