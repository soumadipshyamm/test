
To create a **BaseController** that handles **pagination, searching, filtering, sorting, and column selection** dynamically for all models in a **Laravel API**, follow this structure:

---

## **📌 1. Create the Base Controller**
This will **handle all common listing logic** and can be extended by other controllers.

📌 **Create `app/Http/Controllers/Api/BaseController.php`**
```php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class BaseController extends Controller
{
    protected $model; // Model instance

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Handles pagination, searching, sorting, and filtering dynamically.
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
        return response()->json($query->paginate($perPage));
    }
}
```
✅ **Reusable for all models**  
✅ **Supports pagination, searching, sorting, and filtering**  
✅ **Allows selecting specific columns for optimization**  

---

## **📌 2. Extend BaseController in UserController**
Now, create a `UserController` that extends `BaseController` and automatically inherits the listing logic.

📌 **Create `app/Http/Controllers/Api/UserController.php`**
```php
namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    /**
     * List Users with filters and pagination
     */
    public function index(Request $request)
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
✅ **No need to rewrite pagination logic**  
✅ **UserController inherits BaseController’s listing logic**  

---

## **📌 3. Define API Route**
📌 **Edit `routes/api.php`**
```php
use App\Http\Controllers\Api\UserController;

Route::get('/users', [UserController::class, 'index']);
```

---

## **📌 4. Test the API**
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
✔ **Single BaseController for all models**  
✔ **Dynamic, reusable, and scalable API structure**  
✔ **Handles 500,000+ records efficiently**  
✔ **Supports search, filters, sorting, and pagination**  

Now, you can extend `BaseController` for **any model** like `OrdersController`, `ProductsController`, etc., without rewriting listing logic. 🚀🔥  

Let me know if you need further enhancements! 🚀










namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class ApiHelper
{
    /**
     * Handles pagination, search, sorting, and filtering dynamically.
     */
    public static function getPaginatedData(Model $model, Request $request, array $searchableColumns = [], array $filterableColumns = [], $defaultSortColumn = 'id', $defaultSortDirection = 'desc', $perPage = 50)
    {
        $query = $model->newQuery();

        // 🔎 Apply Search Across Multiple Columns
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request, $searchableColumns) {
                foreach ($searchableColumns as $column) {
                    $q->orWhere($column, 'LIKE', '%' . $request->search . '%');
                }
            });
        }

        // 🔍 Apply Filters (like status, role, or date range)
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





