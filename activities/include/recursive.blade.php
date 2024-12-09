@foreach ($nodes as $activity)
    {{-- <ul> --}}
    @if ($activity->children && count($activity->children) > 0)
        {{-- !-{{ $activity->activities }} --}}
        <div class="strucbhe_subbox sub_heading">
            <div class="strucbhe_sing">
            </div>
            <div class="strucbhe_sing">
                {{-- <p>{{ $subheading->sl_no ?? '' }}</p> --}}
            </div>
            <div class="strucbhe_sing strbhe_sgtitle">
                {{-- <p>{{ $subheading->type == 'subheading' ? $subheading->activities : '' }} --}}
                --{{ $activity->activities }}
                </p>
            </div>
            <div class="strucbhe_sing">
                {{-- <p>{{ $subheading->units->unit ?? '' }}</p> --}}
            </div>
            <div class="strucbhe_sing">
                {{-- <p>{{ $subheading->qty ?? '' }}</p> --}}
            </div>
            <div class="strucbhe_sing">
                {{-- <p>{{ $subheading->rate ?? '' }}</p> --}}
            </div>
            <div class="strucbhe_sing">
                {{-- <p>{{ $subheading->amount ?? '' }}</p> --}}
            </div>
            <div class="strucbhe_sing">
                {{-- <p>{{ $subheading->start_date }}</p> --}}
            </div>
            <div class="strucbhe_sing">
                {{-- <p>{{ $subheading->end_date }}</p> --}}
            </div>
            <div class="strucbhe_sing">
                <a class="editData" {{-- data-uuid="{{ $subheading->id }}" data-model="company" --}} data-type="activities"><i class=" fa fa-edit"
                        style="cursor: pointer;" title="Edit">
                    </i></a>
                <a class="deleteData text-danger" {{-- data-uuid="{{ $subheading->id }}" --}} data-model="company" data-type="activities"
                    data-table="activities" href="javascript:void(0)"><i class="fa fa-trash-alt"
                        style="cursor: pointer;" title="Remove">
                    </i></a>
            </div>
        </div>
        @if (count($activity->children) > 0)
            @include('Company.activities.include.recursive', [
                'nodes' => $activity->children,
            ])
        @endif
    @else
        <div class="strucbhe_subbox">
            <div class="strucbhe_sing">
                {{-- @if ($count == $i)
                        <span class="add_newbox" data-type="activities"
                            data-id="{{ $activites->parent_id }}">
                            <i class="fa fa-plus"></i>
                        </span>
                    @endif --}}
            </div>
            <div class="strucbhe_sing">
                {{-- <p>{{ $activites->sl_no ?? '' }}</p> --}}
            </div>
            <div class="strucbhe_sing strbhe_sgtitle">
                {{-- <p>{{ $activites->type == 'activites' ? $activites->activities : '' }} --}}
                {{ $activity->activities }}
                </p>
            </div>
            <div class="strucbhe_sing">
                {{-- <p>{{ $activites->units->unit ?? '' }}</p> --}}
            </div>
            <div class="strucbhe_sing">
                {{-- <p>{{ $activites->qty ?? '' }}</p> --}}
            </div>
            <div class="strucbhe_sing">
                {{-- <p>{{ $activites->rate ?? '' }}</p> --}}
            </div>
            <div class="strucbhe_sing">
                {{-- <p>{{ $activites->amount ?? '' }}</p> --}}
            </div>
            <div class="strucbhe_sing">
                {{-- <p>{{ $activites->start_date }}</p> --}}
            </div>
            <div class="strucbhe_sing">
                {{-- <p>{{ $activites->end_date }}</p> --}}
            </div>
            <div class="strucbhe_sing">
                <a class="editData" {{-- data-uuid="{{ $activites->id }}" data-model="company" --}} data-type="activities"><i class=" fa fa-edit"
                        style="cursor: pointer;" title="Edit">
                    </i></a>
                <a class="deleteData text-danger" {{-- data-uuid="{{ $activites->id }}" --}} data-model="company" data-type="activities"
                    data-table="activities" href="javascript:void(0)"><i class="fa fa-trash-alt"
                        style="cursor: pointer;" title="Remove">
                    </i></a>
            </div>
        </div>
    @endif

    {{-- </ul> --}}
@endforeach
