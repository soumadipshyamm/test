To build a **more advanced, scalable, and dynamic API structure** for **handling large data listings, searching, filtering, and sorting** in Laravel, we will follow **clean architecture principles** using:

✅ **BaseRepository** (Handles reusable query logic)  
✅ **Model-Specific Repository** (Extends BaseRepository per model)  
✅ **Service Layer** (Handles business logic)  
✅ **Controller** (Handles HTTP requests)  
✅ **Request Validation** (Validates user input for security)  
✅ **API Routes** (Defines endpoints)  

---

# **📌 1. Create the Base Repository**
We will create a **BaseRepository** that all repositories can extend.  

📌 **Create `app/Repositories/BaseRepository.php`**
```php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class BaseRepository
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Handles pagination, search, sorting, and filtering dynamically.
     */
    public function getPaginatedData(Request $request, array $searchableColumns = [], array $filterableColumns = [], $defaultSortColumn = 'id', $defaultSortDirection = 'desc', $perPage = 50)
    {
        $query = $this->model->query();

        // 🔎 Apply Search Across Multiple Columns
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request, $searchableColumns) {
                foreach ($searchableColumns as $column) {
                    $q->orWhere($column, 'LIKE', '%' . $request->search . '%');
                }
            });
        }

        // 🔍 Apply Filters (e.g., status, date range)
        foreach ($filterableColumns as $column) {
            if ($request->has($column)) {
                $query->where($column, $request->input($column));
            }
        }
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        // 🔄 Sorting
        $sortColumn = $request->get('sort_by', $defaultSortColumn);
        $sortDirection = $request->get('sort_order', $defaultSortDirection);
        $query->orderBy($sortColumn, $sortDirection);

        // 📌 Select Required Columns for Optimization
        $columns = $request->get('columns', '*'); // Default: Select all
        if ($columns !== '*') {
            $query->select(explode(',', $columns));
        }

        // 🚀 Return Paginated Results
        return $query->paginate($perPage);
    }
}
```
✅ **Reusable for any model**  
✅ **Supports pagination, searching, sorting, and filtering**  
✅ **Allows selecting specific columns for performance optimization**  

---

# **📌 2. Create a Model-Specific Repository**
Now, create a **UserRepository** that extends `BaseRepository` for managing user-specific queries.

📌 **Create `app/Repositories/UserRepository.php`**
```php
namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    /**
     * Define searchable and filterable columns for users
     */
    public function getUsers($request)
    {
        return $this->getPaginatedData(
            $request,
            ['name', 'email'], // Searchable columns
            ['status', 'role'], // Filterable columns
            'name', // Default sort column
            'asc'  // Default sort order
        );
    }
}
```
✅ **Allows different models to have different search and filter columns**  
✅ **Clean separation of concerns**  

---

# **📌 3. Create a Service Layer**
This layer **manages business logic** between the repository and controller.

📌 **Create `app/Services/UserService.php`**
```php
namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function listUsers(Request $request)
    {
        return $this->userRepository->getUsers($request);
    }
}
```
✅ **Keeps controllers thin and focused on HTTP handling**  
✅ **Business logic is separated for better maintainability**  

---

# **📌 4. Create a Controller**
Now, we will use the service layer inside the controller.

📌 **Create `app/Http/Controllers/Api/UserController.php`**
```php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserService;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Handle listing users with filters
     */
    public function index(Request $request)
    {
        return response()->json($this->userService->listUsers($request));
    }
}
```
✅ **Clean and focused on handling HTTP requests**  
✅ **Calls the service layer instead of repository directly**  

---

# **📌 5. Create API Routes**
📌 **Edit `routes/api.php`**
```php
use App\Http\Controllers\Api\UserController;

Route::get('/users', [UserController::class, 'index']);
```

---

# **📌 6. Use Request Validation (Optional)**
To validate API parameters, create a request class.

📌 **Run the command:**
```sh
php artisan make:request UserRequest
```

📌 **Modify `app/Http/Requests/UserRequest.php`**
```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function rules()
    {
        return [
            'search' => 'nullable|string|max:100',
            'sort_by' => 'nullable|string|in:name,email,created_at',
            'sort_order' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|min:10|max:100',
            'status' => 'nullable|in:active,inactive',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }
}
```
📌 **Modify Controller to Use Validation**
```php
public function index(UserRequest $request)
{
    return response()->json($this->userService->listUsers($request));
}
```
✅ **Ensures valid inputs**  
✅ **Prevents invalid queries from slowing down the API**  

---

# **📌 7. Test the API**
Now, test the API with different filters:

➡ **Get paginated users**  
```
GET /api/users
```

➡ **Search by name/email**  
```
GET /api/users?search=john
```

➡ **Filter by status**  
```
GET /api/users?status=active
```

➡ **Sort by email descending**  
```
GET /api/users?sort_by=email&sort_order=desc
```

➡ **Filter by date range**  
```
GET /api/users?start_date=2024-01-01&end_date=2024-01-31
```

➡ **Select specific columns**  
```
GET /api/users?columns=id,name,email
```

---

# **🎯 Final Thoughts**
✔ **Reusable across multiple models** (Just create a new repository & service)  
✔ **Scalable for large datasets (500,000+ records)**  
✔ **Supports search, filtering, sorting, and column selection dynamically**  
✔ **Uses pagination to prevent memory overload**  
✔ **Easy to extend with more business logic**  

This is a **fully optimized, dynamic, and scalable** Laravel API structure for handling large data listings efficiently. 🚀🔥  

Let me know if you need further improvements! 🚀














To make a **common function** for data listing and searching in a Laravel API, we will:  

✅ **Create a reusable function** for listing and searching data  
✅ **Use dynamic filters** for flexible queries  
✅ **Apply pagination, sorting, and column selection**  

---

## **1. Create a Base Repository for Reusability**
Instead of writing the same logic in every controller, we will create a **BaseRepository** that can be used in multiple models.  

📌 **Create a new file:** `app/Repositories/BaseRepository.php`
```php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class BaseRepository
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get paginated data with optional search and filters
     */
    public function getPaginatedData($request, $defaultSortColumn = 'id', $defaultSortDirection = 'desc', $perPage = 50)
    {
        $query = $this->model->query();

        // Searchable columns (Modify based on your model)
        $searchableColumns = ['name', 'email', 'status']; 

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request, $searchableColumns) {
                foreach ($searchableColumns as $column) {
                    $q->orWhere($column, 'LIKE', '%' . $request->search . '%');
                }
            });
        }

        // Apply additional filters (like status, date range, etc.)
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        // Sorting
        $sortColumn = $request->get('sort_by', $defaultSortColumn);
        $sortDirection = $request->get('sort_order', $defaultSortDirection);
        $query->orderBy($sortColumn, $sortDirection);

        // Select only required columns (Modify based on your model)
        $query->select(['id', 'name', 'email', 'status', 'created_at']);

        // Return paginated results
        return $query->paginate($perPage);
    }
}
```

---

## **2. Use the Repository in a Model-Specific Repository**
Now, create a repository that extends `BaseRepository` and use it in a specific model.

📌 **Create:** `app/Repositories/UserRepository.php`
```php
namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }
}
```

---

## **3. Inject Repository in Controller**
📌 **Create API Controller:** `app/Http/Controllers/Api/UserController.php`
```php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * List Users with search, filters, and pagination
     */
    public function index(Request $request)
    {
        return response()->json($this->userRepository->getPaginatedData($request));
    }
}
```

---

## **4. Define API Route**
📌 **Edit `routes/api.php`**
```php
use App\Http\Controllers\Api\UserController;

Route::get('/users', [UserController::class, 'index']);
```

---

## **5. Test the API**
Now, test the API with different filters:  

➡ **Basic Pagination:**  
```
GET /api/users
```

➡ **Search Users:**  
```
GET /api/users?search=john
```

➡ **Filter by Status:**  
```
GET /api/users?status=active
```

➡ **Sort by Name (Ascending):**  
```
GET /api/users?sort_by=name&sort_order=asc
```

➡ **Filter by Date Range:**  
```
GET /api/users?start_date=2024-01-01&end_date=2024-01-31
```

---

## **🎯 Key Benefits**
✅ **Reusable for any model** (Just create a new repository)  
✅ **Supports pagination, search, sorting, and filtering**  
✅ **Faster API response with selected columns**  
✅ **Easy to maintain and extend**  

This is now a **scalable solution** for listing APIs with searching and filtering. 🚀🔥  

Let me know if you need improvements! 🚀








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
