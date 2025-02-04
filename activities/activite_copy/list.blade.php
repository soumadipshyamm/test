<div class="col-md-12">
    @php
        $firstIssue = $datas->first();
        $type = $firstIssue->type ?? 'materials';
        $isMaterial = $type === 'materials';
    @endphp

    <h3>{{ $isMaterial ? 'Material' : 'Assets' }}</h3>

    <table border="1">
        <thead>
            <tr>
                <th>Sl No.</th>
                <th>Code</th>
                <th>Name</th>
                <th>Specification</th>
                <th>Units</th>
                <th>Issue Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datas as $data)
                @foreach ($data->inv_issue_details ?? [] as $key => $detail)
                    @php
                        $item = $detail->materials ?? $detail->assets;
                        $unit = $item->units->unit ?? 'N/A';
                    @endphp
                    <tr>
                        <td>{{ $loop->parent->iteration }}.{{ $loop->iteration }}</td>
                        <td class="td-line-break">{{ $item->code ?? 'N/A' }}</td>
                        <td>{{ $item->name ?? 'N/A' }}</td>
                        <td class="td-line-break">{{ $item->specification ?? 'N/A' }}</td>
                        <td>{{ $unit }}</td>
                        <td>{{ $detail->issue_qty > 0 ? $detail->issue_qty : 'N/A' }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>








array:2 [ // resources\views/common/pdf/issue.blade.php
  0 => array:18 [
    "id" => 232
    "uuid" => "6817f444-495e-46d6-909d-da726cdebd20"
    "inv_issues_id" => 93
    "materials_id" => null
    "issue_no" => "672710"
    "date" => "2025-01-31"
    "type" => "materials"
    "inv_issue_lists_id" => 2
    "img" => null
    "remarkes" => null
    "company_id" => 14
    "is_active" => 1
    "created_at" => "2025-01-31T10:27:19.000000Z"
    "updated_at" => "2025-01-31T10:27:19.000000Z"
    "deleted_at" => null
    "tag_id" => null
    "inv_issue_details" => []
    "inv_issue_list" => array:8 [
      "id" => 2
      "uuid" => "fbd15aeb-a0c6-4509-9b52-1402f3da6c91"
      "name" => "Contractor"
      "slug" => "contractor"
      "remarkes" => null
      "is_active" => 1
      "created_at" => "2024-04-10T02:16:10.000000Z"
      "updated_at" => "2024-04-10T02:16:10.000000Z"
    ]
  ]
  1 => array:18 [
    "id" => 233
    "uuid" => "83a657d2-42ff-45bf-9ca8-714cb2e3e8f1"
    "inv_issues_id" => 93
    "materials_id" => null
    "issue_no" => "735955"
    "date" => "2025-01-31"
    "type" => "materials"
    "inv_issue_lists_id" => 2
    "img" => null
    "remarkes" => null
    "company_id" => 14
    "is_active" => 1
    "created_at" => "2025-01-31T10:27:41.000000Z"
    "updated_at" => "2025-01-31T10:42:31.000000Z"
    "deleted_at" => null
    "tag_id" => null
    "inv_issue_details" => array:1 [
      0 => array:17 [
        "id" => 233
        "uuid" => "f39f2cb1-36af-48e0-8d6c-397b4c947094"
        "inv_issue_goods_id" => 233
        "materials_id" => 46
        "activities_id" => 968
        "issue_qty" => "25"
        "stock_qty" => 1673
        "remarkes" => null
        "type" => "materials"
        "company_id" => 14
        "is_active" => 1
        "created_at" => "2025-01-31T10:41:32.000000Z"
        "updated_at" => "2025-01-31T10:42:11.000000Z"
        "deleted_at" => null
        "assets_id" => null
        "assets" => null
        "materials" => array:13 [
          "id" => 46
          "uuid" => "48e08edc-79e5-4dac-9d53-a928f6d2a43c"
          "name" => "Binding wire"
          "class" => "B"
          "code" => "M016925"
          "specification" => null
          "unit_id" => 31
          "company_id" => 14
          "is_active" => 1
          "deleted_at" => null
          "created_at" => "2025-01-03T10:31:58.000000Z"
          "updated_at" => "2025-01-03T10:31:58.000000Z"
          "type" => null
        ]
      ]
    ]
    "inv_issue_list" => array:8 [
      "id" => 2
      "uuid" => "fbd15aeb-a0c6-4509-9b52-1402f3da6c91"
      "name" => "Contractor"
      "slug" => "contractor"
      "remarkes" => null
      "is_active" => 1
      "created_at" => "2024-04-10T02:16:10.000000Z"
      "updated_at" => "2024-04-10T02:16:10.000000Z"
    ]
  ]
]




<div class="col-md-12">
    @php
        $issueGoods = $datas->invIssueGoods->first();
        $type = $issueGoods->type ?? 'materials';
        $isMaterial = $type === 'materials';
    @endphp

    <h3>{{ $isMaterial ? 'Material' : 'Assets' }}</h3>

    <table border="1">
        <thead>
            <tr>
                <th>Sl No.</th>
                <th>Code</th>
                <th>Name</th>
                <th>Specification</th>
                <th>Units</th>
                <th>Issue Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($issueGoods->invIssueDetails ?? [] as $key => $val)
                @php
                    $item = $val->type === 'machines' ? $val->assets : $val->materials;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="td-line-break">{{ $item->code ?? 'N/A' }}</td>
                    <td>{{ $item->name ?? 'N/A' }}</td>
                    <td class="td-line-break">{{ $item->specification ?? 'N/A' }}</td>
                    <td>{{ $item->units->unit ?? 'N/A' }}</td>
                    <td>{{ $val->issue_qty > 0 ? $val->issue_qty : 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>







<div class="col-md-12">
        <h3>{{ $datas->invIssueGoods->first()->type == 'materials' ? 'Material' : 'Assets' }}</h3>
        <table border="1">
            <thead>
                <th>Slno.</th>
                <th>Code</th>
                <th>Name</th>
                <th>Specification</th>
                <th>Units</th>
                <th>Issue Qty</th>
                {{-- <th>Acivities</th> --}}
            </thead>
            <tbody>
                @foreach ($datas->invIssueGoods->first()->invIssueDetails as $key => $val)
                    <tr>
                        @php
                            $typess = '';
                            if ($val->type == 'machines') {
                                $typess = $val->assets;
                            } else {
                                $typess = $val->materials;
                            }
                        @endphp
                        <td>{{ $key + 1 }}</td>
                        <td class="td-line-break">{{ $typess->code ?? '' }}</td>
                        <td>{{ $typess->name ?? '' }}</td>
                        <td class="td-line-break">{{ $typess->specification ?? '' }}</td>
                        <td>{{ $typess->units->unit ?? '' }}</td>
                        <td>{{ $val->issue_qty ?? 0 }}</td>

                        {{-- <td>{{ $val->reject_qty ?? 0 }}</td>
                        <td>{{ $val->accept_qty ?? 0 }}</td>
                        <td>{{ $val->price ?? 0 }}</td>
                        <td>{{ $val->accept_qty * $val->price ?? 0 }}</td>
                        <td>{{ $val->remarkes ?? '' }}</td> --}}
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>








public function materialsList(Request $request)
{
    $authCompany = Auth::guard('company-api')->user()->company_id;
    $projectId = $request->project_id;
    $goodsType = $request->goods_type;
    $type = $request->type;

    $query = ($goodsType === 'materials') ? Materials::where('company_id', $authCompany) : Assets::where('company_id', $authCompany);

    $relation = ($type === 'issue') ? 'invIssuesDetails' : 'invInwardGoodDetails';
    $foreignKey = ($goodsType === 'materials') ? 'materials_id' : 'assets_id';

    $materialList = $query
        ->whereHas($relation, fn($q) => $q->whereNotNull($foreignKey))
        ->with('inventorys') // Eager loading for performance
        ->get()
        ->map(function ($item) {
            $totalQty = $item->inventorys->total_qty ?? 0;
            $item->total_stk_qty = ($totalQty > 0) ? $totalQty : null; // Only keep valid stock values
            return $item;
        })
        ->filter(fn($item) => !is_null($item->total_stk_qty)); // Filter out null values

    return $this->responseJson(true, 200, 'Materials List Fetched Successfully', IssueMaterialResource::collection($materialList));
}








public function materialsList(Request $request)
    {
        $authCompany = Auth::guard('company-api')->user()->company_id;

        $projectId = $request->project_id;
        if ($request->type == 'issue') {
            if ($request->goods_type == 'materials') {
                $materialList = Materials::where('company_id', $authCompany)
                    ->whereHas('invInwardGoodDetails', function ($query) {
                        $query->whereNotNull('materials_id');
                    })
                    ->get()
                    ->map(function ($material) {
                        $material->total_stk_qty = $material->inventorys->total_qty ?? 0; // Use null coalescing to handle potential null
                        return $material;
                    });
                $message =  'Fetch Materials List Successfully';
            } else {
                $materialList = Assets::where('company_id', $authCompany)
                    ->whereHas('invInwardGoodDetails', function ($quer) {
                        $quer->whereNotNull('assets_id');
                    })->get()
                    ->map(function ($material) {
                        $material->total_stk_qty = $material->inventory->total_qty ?? 0; // Use null coalescing to handle potential null
                        return $material;
                    });
                $message =  'Fetch Materials List Successfully';
            }
        } else {
            if ($request->goods_type == 'materials') {
                $materialList = Materials::where('company_id', $authCompany)
                    ->whereHas('invIssuesDetails', function ($quer) {
                        $quer->whereNotNull('materials_id');
                    })->get()->map(function ($material) {
                        $material->total_stk_qty = $material->inventorys->total_qty ?? 0; // Use null coalescing to handle potential null
                        return $material;
                    });
                $message =  'Fetch Materials List Successfully';
            } else {
                $materialList = Assets::where('company_id', $authCompany)
                    ->whereHas('invIssuesDetails', function ($quer) {
                        $quer->whereNotNull('assets_id');
                    })->get()->map(function ($material) {
                        $material->total_stk_qty = $material->inventory->total_qty ?? 0; // Use null coalescing to handle potential null
                        return $material;
                    });
                $message =  'Fetch Materials List Successfully';
            }
        }
        $message = 'Materials List Fetch Successfullsy';
        // return $this->responseJson(true, 200, $message, $materialList);
        return $this->responseJson(true, 200, $message, IssueMaterialResource::collection($materialList));
        // return $this->responseJson(true, 201, $message, InwardMaterialListResources::collection($fetchMaterial));

        // return $this->responseJson(true, 201, $message, InventoryDtailsResources::collection($materialList));
    }













function issuelistMaterials(Request $request)
{
    $authCompany = Auth::guard('company-api')->user()->company_id;

    $materials = Materials::where('company_id', $authCompany)
        ->whereHas('invIssuesDetails', fn($query) => $query->whereNotNull('materials_id'))
        ->with('inventorys') // Eager load to reduce queries
        ->get();

    $filteredMaterials = $materials->map(function ($material) {
        $totalQty = $material->inventorys->total_qty ?? 0;
        $material->total_stk_qty = $totalQty > 0 ? $totalQty : null;
        return $material;
    })->filter(fn($material) => !is_null($material->total_stk_qty));

    return $this->responseJson(true, 200, 'Fetch Materials List Successfully', $filteredMaterials);
}

function issuelistMachine(Request $request)
{
    $authCompany = Auth::guard('company-api')->user()->company_id;

    $machines = Assets::where('company_id', $authCompany)
        ->whereHas('invIssuesDetails', fn($query) => $query->whereNotNull('assets_id'))
        ->with('inventory') // Eager load to reduce queries
        ->get();

    $filteredMachines = $machines->map(function ($machine) {
        $totalQty = $machine->inventory->total_qty ?? 0;
        $machine->total_stk_qty = $totalQty > 0 ? $totalQty : null;
        return $machine;
    })->filter(fn($machine) => !is_null($machine->total_stk_qty));

    return $this->responseJson(true, 200, 'Fetch Machine List Successfully', $filteredMachines);
}









function issuelistMaterials(Request $request)
    {
        $authCompany = Auth::guard('company-api')->user()->company_id;


        $materialList = Materials::where('company_id', $authCompany)
            ->whereHas('invIssuesDetails', function ($query) {
                $query->whereNotNull('materials_id');
            })
            ->get()
            ->map(function ($material) {
                // Check if inventorys and total_qty are not null and not equal to zero
                $totalQty = $material->inventorys->total_qty ?? 0; // Use null coalescing to handle potential null
                $material->total_stk_qty = ($totalQty >= 1) ? $totalQty : null; // Set to null if total_qty is zero
                return $material;
            })
            ->filter(function ($material) {
                // Filter out materials where total_stk_qty is null
                return $material->total_stk_qty !== null;
            });
        // dd($materialList->toArray());

        $message =  'Fetch Materials List Successfully';
        return $this->responseJson(true, 200, $message, $materialList);
    }





function issuelistMachine(Request $request)
    {
        $authCompany = Auth::guard('company-api')->user()->company_id;
        $materialList = Assets::where('company_id', $authCompany)
            ->whereHas('invIssuesDetails', function ($query) {
                $query->whereNotNull('assets_id');
            })
            ->get()
            ->map(function ($material) {
                // Retrieve total_qty, default to 0 if null
                $totalQty = $material->inventory->total_qty ?? 0;

                // Set total_stk_qty to null if total_qty is 0 or less than -25
                $material->total_stk_qty = ($totalQty > 0 && $totalQty > -1) ? $totalQty : null;

                return $material;
            })
            ->filter(function ($material) {
                // Filter out materials where total_stk_qty is null
                return $material->total_stk_qty !== null;
            });

        $message =  'Fetch Materials List Successfully';

        return $this->responseJson(true, 200, $message,  $materialList);
    }
