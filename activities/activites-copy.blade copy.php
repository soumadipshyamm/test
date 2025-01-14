public function add(Request $request)
{
    // Start the session and load the company
    Session::put('navbar', 'show');
    $authConpany = Auth::guard('company')->user()->id;
    $companyId = searchCompanyId($authConpany);
    
    if ($request->isMethod('post')) {
        // Validate request data
        $validatedData = $request->validate([
            'project' => 'required',
            'type' => 'required|in:heading,activites',
            'heading' => 'required_if:type,activites',
            'activities' => 'required',
        ]);

        // Fetch subscription info and activity data
        $checkAdditionalFeatures = fetchDataActivities($companyId, $request->project);
        $isSubscription = checkSubscriptionPermission($companyId, 'activities');
        
        // Assign subproject or null
        $subproject = $request->subproject ?? null;

        // Determine sl_no based on type (heading or activity)
        $slNo = $this->generateSlNo($request->type, $request->heading, $companyId);

        // Perform update or create based on whether UUID is provided
        try {
            DB::beginTransaction();

            // If UUID exists, update existing activity
            if ($request->uuid) {
                $id = uuidtoid($request->uuid, 'activities');
                Activities::where('id', $id)->update([
                    'project_id' => $request->project,
                    'subproject_id' => $subproject,
                    'type' => $request->type,
                    'parent_id' => $request->heading,
                    'activities' => $request->activities,
                    'unit_id' => $request->unit_id ?? null,
                    'qty' => $request->quantity,
                    'rate' => $request->rate,
                    'amount' => $request->amount,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                ]);
            } else {
                // Create new activity
                if (count($checkAdditionalFeatures) < $isSubscription->is_subscription) {
                    Activities::create([
                        'uuid' => Str::uuid(),
                        'project_id' => $request->project,
                        'subproject_id' => $subproject,
                        'type' => $request->type,
                        'sl_no' => $slNo,
                        'parent_id' => $request->heading,
                        'activities' => $request->activities,
                        'unit_id' => $request->unit_id ?? null,
                        'qty' => $request->quantity,
                        'rate' => $request->rate,
                        'amount' => $request->amount,
                        'start_date' => $request->start_date,
                        'end_date' => $request->end_date,
                        'company_id' => $companyId,
                    ]);
                } else {
                    return redirect()->back()->with('expired', true);
                }
            }

            DB::commit();
            return redirect()->route('company.activities.list')->with('success', 'Activities Saved Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e->getMessage() . '--' . $e->getFile() . '--' . $e->getLine());
            return redirect()->route('company.activities.list')->with('false', $e->getMessage());
        }
    }

    return view('Company.activities.add-edit');
}

/**
 * Generate the sl_no for new activities based on their type and parent.
 */
private function generateSlNo($type, $parentId, $companyId)
{
    // If activity is a heading (no parent), generate sl_no for headings (top-level)
    if ($type === 'heading') {
        return $this->generateHeadingSlNo($companyId);
    }

    // If it's a child activity, find the parent's sl_no and increment appropriately
    if ($parentId) {
        return $this->generateChildSlNo($parentId);
    }

    return '1'; // Default to '1' if neither case is met
}

/**
 * Generate sl_no for headings (top-level activities).
 */
private function generateHeadingSlNo($companyId)
{
    $lastSlNo = Activities::whereNull('parent_id')
        ->where('company_id', $companyId)
        ->max('sl_no');

    return $lastSlNo ? $lastSlNo + 1 : 1;
}

/**
 * Generate sl_no for child activities (under a parent).
 */
private function generateChildSlNo($parentId)
{
    $parent = Activities::find($parentId);

    // Get the parent's sl_no and increment the child number
    $parentSlNo = $parent->sl_no;
    $lastChildSlNo = Activities::where('parent_id', $parentId)->max('sl_no');

    if (strpos($parentSlNo, '.') === false) {
        return $parentSlNo . '.1'; // First child of a heading
    }

    // Increment the last child number
    $lastChildNumber = substr(strrchr($lastChildSlNo, '.'), 1); // Get last child number
    return $parentSlNo . '.' . ($lastChildNumber + 1);
}


***************************************************************************************88

public function add(Request $request)
{
    Session::put('navbar', 'show');
    $authConpany = Auth::guard('company')->user()->id;
    $companyId = searchCompanyId($authConpany);
    
    if ($request->isMethod('post')) {
        $checkAdditionalFeatures = fetchDataActivities($companyId, $request->project);
        $isSubscription = checkSubscriptionPermission($companyId, 'activities');
        
        $validatedData = $request->validate([
            'project' => 'required',
            'type' => 'required|in:heading,activites',
            'heading' => 'required_if:type,activites',
            'activities' => 'required',
        ]);
        
        $subproject = $request->subproject ?? null;

        // Determine the sl_no
        if ($request->type == 'heading') {
            // For headings, sl_no starts from 1 and increments for each new heading
            $slNo = Activities::whereNull('parent_id')->where('company_id', $companyId)->max('sl_no') + 1 ?? 1;
        } else {
            // For activities under a parent (heading), sl_no follows parent.child format
            $parent = Activities::where('id', $request->heading)->first();
            $parentSlNo = $parent->sl_no; // Get the parent's sl_no
            $lastChildSlNo = Activities::where('parent_id', $request->heading)->max('sl_no');

            if (strpos($parentSlNo, '.') === false) {
                // If the parent has no child, we start with "parentSlNo.1"
                $slNo = $parentSlNo . '.1';
            } else {
                // If parent has children, increment the child number
                $lastChildNumber = substr(strrchr($lastChildSlNo, '.'), 1); // Get last child's last number
                $slNo = $parentSlNo . '.' . ($lastChildNumber + 1);
            }

            // For grandchildren or nested children
            $lastChildSlNo = Activities::where('parent_id', $request->heading)->max('sl_no');
            $parentParts = explode('.', $parentSlNo);
            $parentSlNoLast = end($parentParts); // This will be the "1" in "1.1" (the first part before the dot)
            
            $siblingCount = Activities::where('parent_id', $request->heading)->count(); // Count how many siblings exist
            $newSlNo = $parentSlNoLast . '.' . ($siblingCount + 1);
        }

        // If UUID is provided, update the activity
        if ($request->uuid) {
            try {
                $id = uuidtoid($request->uuid, 'activities');
                Activities::where('id', $id)->update([
                    'project_id' => $request->project,
                    'subproject_id' => $subproject,
                    'type' => $request->type,
                    'parent_id' => $request->heading,
                    'activities' => $request->activities,
                    'unit_id' => $request->unit_id ?? null,
                    'qty' => $request->quantity,
                    'rate' => $request->rate,
                    'amount' => $request->amount,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                ]);
                DB::commit();
                return redirect()->route('company.activities.list')->with('success', 'Activities Updated Successfully');
            } catch (\Exception $e) {
                DB::rollBack();
                logger($e->getMessage() . '--' . $e->getFile() . '--' . $e->getLine());
                return redirect()->route('company.activities.list')->with('false', $e->getMessage());
            }
        } else {
            // Ensure subscription limits are not exceeded
            if (count($checkAdditionalFeatures) < $isSubscription->is_subscription) {
                try {
                    Activities::create([
                        'uuid' => Str::uuid(),
                        'project_id' => $request->project,
                        'subproject_id' => $subproject,
                        'type' => $request->type,
                        'sl_no' => $slNo,
                        'parent_id' => $request->heading,
                        'activities' => $request->activities,
                        'unit_id' => $request->unit_id ?? null,
                        'qty' => $request->quantity,
                        'rate' => $request->rate,
                        'amount' => $request->amount,
                        'start_date' => $request->start_date,
                        'end_date' => $request->end_date,
                        'company_id' => $companyId,
                    ]);
                    DB::commit();
                    return redirect()->route('company.activities.list')->with('success', 'Activities Created Successfully');
                } catch (\Exception $e) {
                    DB::rollBack();
                    logger($e->getMessage() . '--' . $e->getFile() . '--' . $e->getLine());
                    return redirect()->route('company.activities.list')->with('false', $e->getMessage());
                }
            } else {
                return redirect()->back()->with('expired', true);
            }
        }
    }
    
    return view('Company.activities.add-edit');
}
***************************************************************************************88
















use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Activities extends Model
{
    protected $fillable = ['uuid', 'parent_id', 'type', 'activities', 'sl_no'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // If there's no parent_id (i.e., top-level entry)
            if (is_null($model->parent_id)) {
                // Get the last sl_no for top-level entries and increment it
                $lastSlNo = Activities::whereNull('parent_id')->max('sl_no');
                $model->sl_no = $lastSlNo ? $lastSlNo + 1 : 1; // Start with 1 if no records exist
            } else {
                // If there is a parent_id, generate sl_no in parent.child format
                $parentSlNo = Activities::where('id', $model->parent_id)->value('sl_no');
                
                // If there's a parent_sl_no, get the last child sl_no for this parent and increment it
                $lastChildSlNo = Activities::where('parent_id', $model->parent_id)->max('sl_no');
                $model->sl_no = $lastChildSlNo ? $parentSlNo . '.' . ($lastChildSlNo + 1) : $parentSlNo . '.1';
                
                // If it's a child with its own children, it should use the child sl_no as parent
                if ($parentSlNo) {
                    $lastSiblingSlNo = Activities::where('parent_id', $model->parent_id)->max('sl_no');
                    $model->sl_no .= '.' . ($lastSiblingSlNo + 1);
                }
            }

            // Optionally, set UUID if not provided
            if (!$model->uuid) {
                $model->uuid = Str::uuid();
            }
        });
    }
}
