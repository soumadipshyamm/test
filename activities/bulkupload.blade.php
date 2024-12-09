@extends('Company.layouts.app')
@section('activities-active', 'active')
@section('title', __('Activities'))
@push('styles')
@endpush
@section('content')
<div class="app-main__outer">
    <div class="app-main__inner card">
        <!-- dashboard body -->

        <div class="dashboard_body">
            <form action="{{ route('company.activities.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="page-title-actions">
                    <a href="{{ route('company.activities.list') }}" class="mt-2 btn btn-secondary">&lt; Back</a>
                </div>
                <div class="tablesec-head blukup_head">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="excel_btnbox">
                                <a href="{{ route('company.activities.export') }}" class="excelbtn"><span><img src="{{ asset('company_assets/images/excel.png') }}" class="img-fluid" alt="excel"></span>Export Activities Data</a>
                            </div>
                            <div class="excel_btnbox">
                                <a class="excelbtn" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><span><img src="{{ asset('company_assets/images/excel.png') }}" class="img-fluid" alt="excel"></span>Import Activities Data</a>
                            </div>
                            <!-- ******************************************************************************************************** -->
                            <div class="collapse" id="collapseExample">
                                <div class="form-row">
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="class" class="">Project</label>
                                            <select class="form-control" value="{{ old('project') }}" name="project" id="project">
                                                <option value="">----Select Project----</option>
                                                {{ getProject('') }}
                                            </select>
                                            @if($errors->has('project'))
                                            <div class="error">{{ $errors->first('project') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="warehouses" class="">Sub-Project</label>
                                            <select class="form-control" value="{{ old('subproject') }}" name="subproject" id="subproject">
                                                <option value="">----Select Sub-Project----</option>
                                            </select>
                                            @if($errors->has('subproject'))
                                            <div class="error">{{ $errors->first('subproject') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="card card-body">
                                    <input type="file" name="file" class="form-control" required>
                                    <br>
                                    <div>
                                        <button class="btn btn-success">Import Data</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js">
</script>
<script>
    // Example using jQuery
    $('#project').change(function() {
        var projectId = $(this).val();
        // console.log(projectId);
        $.get(baseUrl + 'company/activities/subprojects/' + projectId, function(data) {
            $('#subproject').empty();
            $.each(data, function(key, value) {
                console.log(value.sub_project);
                $.each(value.sub_project, function(subkey, subvalue) {
                    console.log(subvalue);
                    $('#subproject').append('<option value="' + subvalue.id + '">' +
                        subvalue.name +
                        '</option>');
                });
            });
        });
    });
</script>
@endpush
