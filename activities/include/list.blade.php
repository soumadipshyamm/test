<div class="structure_body">
    {{-- @dd($activities->toArray()) --}}
    @forelse($activities as $activity)
        <!-- full activity box -->
        <div class="structureb_head">
            <a href="#" class="">
                <div class="strucbhelr_body">
                    <div class="strucbhe_left">
                        <div class="strucbhe_sing">
                            <input type="checkbox" name="headactivity[{{ $activity->id }}]" value="{{ $activity->id }}"
                                class="checkactivity" data-id={{ $activity->id }}>
                        </div>
                        <div class="strucbhe_sing strbhe_sgtitle">
                            <p>{{ $activity->parent_id == null ? $activity->activities : '' }}</p>
                        </div>
                    </div>
                    <div class="strucbhe_right">
                        <div class="strucbhe_sing">
                            <div class="strcsusing_action">
                                <button class="deleteData text-danger" data-uuid="{{ $activity->id }}"
                                    data-model="company" data-type="heading" data-table="activities"
                                    href="javascript:void(0)"><i class="fa fa-trash-alt" style="cursor: pointer;"
                                        title="Remove"></i></button>
                            </div>
                        </div>
                        <div class="strucbhe_sing">
                            <i class="fa-solid fa-chevron-down"></i>
                        </div>
                    </div>
                </div>
            </a>

            <div class="structureb_sub ">
                <!-- subheading box -->
                @if (count($activity->children) > 0)
                    @forelse ($activity->children as $subactivity)
                        <!-- under subheading -->
                        <!-- single activities -->
                        <div class="strucbhe_subbox sub_heading">
                            <div class="strucbhe_sing">
                                <input type="checkbox" name="subactivite[{{ $subactivity->id }}]"
                                    value="{{ $subactivity->id }}" class="checkactivity" data-id={{ $subactivity->id }}>
                            </div>
                            {{-- <div class="strucbhe_sing">
                                <p>{{ $subactivity->sl_no ?? '' }}</p>
                            </div> --}}
                            <div class="strucbhe_sing strbhe_sgtitle">
                                {{-- <p>{{ $subactivity->type == 'activites' ? $subactivity->activities : '' }} --}}
                                <p>{{ $subactivity->activities ?? '' }}
                                </p>
                            </div>
                            <div class="strucbhe_sing">
                                <p>{{ $subactivity->units->unit ?? '' }}</p>
                            </div>
                            <div class="strucbhe_sing">
                                <p>{{ $subactivity->qty ?? '' }}</p>
                            </div>
                            <div class="strucbhe_sing">
                                <p>{{ $subactivity->rate ?? '' }}</p>
                            </div>
                            <div class="strucbhe_sing">
                                <p>{{ $subactivity->amount ?? '' }}</p>
                            </div>
                            <div class="strucbhe_sing">
                                <p>{{ $subactivity->start_date }}</p>
                            </div>
                            <div class="strucbhe_sing">
                                <p>{{ $subactivity->end_date }}</p>
                            </div>
                            <div class="strucbhe_sing">
                                <a class="editData" data-uuid="{{ $subactivity->id }}" data-model="company"
                                    data-type="activities"><i class=" fa fa-edit" style="cursor: pointer;"
                                        title="Edit">
                                    </i></a>
                                <a class="deleteData text-danger" data-uuid="{{ $subactivity->id }}"
                                    data-model="company" data-type="activities" data-table="activities"
                                    href="javascript:void(0)"><i class="fa fa-trash-alt" style="cursor: pointer;"
                                        title="Remove">
                                    </i></a>
                            </div>
                        </div>
                        @if (count($subactivity->children) > 0)
                            @php
                                $count = count($subactivity->children);
                                $i = 1;
                            @endphp
                            @forelse ($subactivity->children as $childActivity)
                                <!-- under subheading -->
                                <!-- single activities -->
                                <div class="strucbhe_subbox">
                                    <div class="strucbhe_sing">
                                        <input type="checkbox" name="activites[{{ $childActivity->id }}]"
                                            value="{{ $childActivity->id }}" class="checkactivity"
                                            data-id={{ $childActivity->id }}>
                                        {{-- @if ($count == $i)
                                            <span class="add_newbox" data-type="activities"
                                                data-id="{{ $childActivity->parent_id }}">
                                                <i class="fa fa-plus"></i>
                                            </span>
                                        @endif --}}
                                    </div>
                                    {{-- <div class="strucbhe_sing">
                                        <p>{{ $childActivity->sl_no ?? '' }}</p>
                                    </div> --}}
                                    <div class="strucbhe_sing strbhe_sgtitle">
                                        {{-- <!-- <p>{{ $childActivity->type == 'activites' ? $childActivity->activities : '' }} --> --}}
                                        <p>{{ $childActivity->activities ?? '' }}
                                        </p>
                                    </div>
                                    <div class="strucbhe_sing">
                                        <p>{{ $childActivity->units->unit ?? '' }}</p>
                                    </div>
                                    <div class="strucbhe_sing">
                                        <p>{{ $childActivity->qty ?? '' }}</p>
                                    </div>
                                    <div class="strucbhe_sing">
                                        <p>{{ $childActivity->rate ?? '' }}</p>
                                    </div>
                                    <div class="strucbhe_sing">
                                        <p>{{ $childActivity->amount ?? '' }}</p>
                                    </div>
                                    <div class="strucbhe_sing">
                                        <p>{{ $childActivity->start_date }}</p>
                                    </div>
                                    <div class="strucbhe_sing">
                                        <p>{{ $childActivity->end_date }}</p>
                                    </div>
                                    <div class="strucbhe_sing">
                                        <a class="editData" data-uuid="{{ $childActivity->id }}" data-model="company"
                                            data-type="activities"><i class=" fa fa-edit" style="cursor: pointer;"
                                                title="Edit">
                                            </i></a>
                                        <a class="deleteData text-danger" data-uuid="{{ $childActivity->id }}"
                                            data-model="company" data-type="activities" data-table="activities"
                                            href="javascript:void(0)"><i class="fa fa-trash-alt"
                                                style="cursor: pointer;" title="Remove">
                                            </i></a>
                                    </div>
                                </div>
                                @php
                                    $i++;
                                @endphp
                            @empty
                                <p>!No Found Data</p>
                            @endforelse
                        @endif
                    @empty
                        <p>!No Found Data</p>
                    @endforelse
                @endif
            </div>
        </div>
    @empty
        <p>!No Found Data</p>
    @endforelse
</div>

