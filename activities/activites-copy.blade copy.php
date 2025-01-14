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
