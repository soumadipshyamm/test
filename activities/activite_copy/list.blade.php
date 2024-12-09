<div class="structure_body">
    @forelse($activities as $hkey=>$activity)
        <div class="structureb_head" id="structureb_head">
            <a href="#" class="">
                <div class="strucbhelr_body">
                    <div class="strucbhe_left">
                        <div class="strucbhe_sing">
                            <fieldset class="parrentcheckArray">
                            <input type="checkbox" name="headactivity[{{ $activity->id }}]" value="{{ $activity->id }}"
                                id="headactivity" class="headactivity parent{{$hkey}} mainparent" data-key="{{$hkey}}" data-checked="0">
                            </fieldset>
                        </div>
                        <div class="strucbhe_sing strbhe_sgtitle">
                            <p>{{ $activity->parent_id == null ? $activity->activities : '' }}</p>
                            {{-- @dd($activity->toArray()) --}}
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

            <div class="structureb_sub">
                @if (count($activity->children) > 0)
                    @forelse ($activity->children as $skey=> $subactivity)
                        <div class="strucbhe_subbox sub_heading">
                            <div class="strucbhe_sing">
                                <input name="subactivite[{{ $subactivity->id }}]" type="checkbox"
                                    value="{{ $subactivity->id }}" id="subactivite" class="mainchild child{{$hkey}}" data-key="{{$skey}}" data-checked="0">
                            </div>
                            <div class="strucbhe_sing strbhe_sgtitle">
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
                            @forelse ($subactivity->children as $akey=> $childActivity)
                                <div class="strucbhe_subbox">
                                    <div class="strucbhe_sing">
                                        <input type="checkbox"
                                            name="activites[{{ $childActivity->id }}]"
                                            value="{{ $childActivity->id }}" id="activites" class="subchild{{$hkey}} childsubchild{{$skey}} mainsubchild" data-key="{{$akey}}" data-checked="0">
                                        {{-- @if ($count == $i)
                                            <span class="add_newbox" data-type="activities"
                                                data-id="{{ $childActivity->parent_id }}">
                                                <i class="fa fa-plus"></i>
                                            </span>
                                        @endif --}}
                                    </div>
                                    <div class="strucbhe_sing strbhe_sgtitle">
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
