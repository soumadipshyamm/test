// app/Http/Controllers/DataTableController.php

namespace App\Http\Controllers;

use App\Models\YourModel;
use Illuminate\Http\Request;
use DataTables;

class DataTableController extends Controller
{
    public function getData(Request $request)
    {
        $query = YourModel::query();

        // Apply filters based on request parameters
        if ($request->has('filter1')) {
            $query->where('column1', $request->input('filter1'));
        }

        if ($request->has('filter2')) {
            $query->where('column2', $request->input('filter2'));
        }

        // Add more filters as needed...

        return DataTables::of($query)->toJson();
    }
}


// routes/web.php

use App\Http\Controllers\DataTableController;

Route::get('/data', [DataTableController::class, 'getData']);


<!-- Your Blade view file -->

<table id="dataTable" class="display">
    <thead>
        <!-- Table headers -->
    </thead>
</table>

<script>
    $(document).ready(function () {
        $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/data',
                type: 'GET',
                data: function (d) {
                    // Add additional parameters as needed
                    d.filter1 = $('#filter1').val();
                    d.filter2 = $('#filter2').val();
                    // Add more filters...
                }
            },
            columns: [
                // Define your table columns
            ]
        });
    });
</script>




<!-- Include DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.6/css/jquery.dataTables.css">

<!-- Include DataTables JS -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.6/js/jquery.dataTables.js"></script>


<!-- Include DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.6/css/jquery.dataTables.css">

<!-- Include DataTables JS -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.6/js/jquery.dataTables.js"></script>
