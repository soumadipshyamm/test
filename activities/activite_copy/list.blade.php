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
