<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Models\Agreement;
use App\Models\BillingDetails;
use App\Models\ControlPricing;
use App\Models\Office;
use App\Models\PricingGuide;
use App\Models\User;
use App\Models\WoodRepair;
use App\Traits\CommonFunction;
use App\Traits\UploadAble;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgreementController extends BaseController
{
    use CommonFunction;
    use UploadAble;

    public function index(Request $request)
    {

        if ($request->isMethod('post')) {
            // Change to paginate instead of get
            $datas = Agreement::where('title', $request->search)->paginate(10); // Adjust the number as needed
            return view('pages.controls-agreement.list', compact('datas'));
        }
        // Change to paginate instead of all
        $datas = Agreement::paginate(10); // Adjust the number as needed
        return view('pages.controls-agreement.list', compact('datas'));
    }

    public function add(Request $request)
    {
        if ($request->isMethod('post')) {
            // dd($request->all());
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'agreement_id' => 'required|uuid',
                'office.office_id' => 'required|uuid',
                'customer.uuid' => 'required|uuid',
                'customer_bill.uuid' => 'required|uuid',
                'service_plan' => 'nullable|array',
                'services' => 'nullable|array',
                'services.description' => 'nullable|string',
                'payment.total_initial' => 'nullable|numeric',
                'payment.total_yearly_renewal' => 'nullable|numeric',
                'payment.monthly_renewal' => 'nullable|numeric',
                'dynamicTextBox' => 'nullable|array',
            ]);

            $responseData = $request->all();

            // dd( $responseData);
            $agreement = Agreement::updateOrCreate(
                ['uuid' => $responseData['agreement_id']],
                [
                    'title' => $responseData['title'],
                    'office_id' => uuidtoid($responseData['office']['office_id'], 'offices'),
                    'customer_id' => uuidtoid($responseData['customer']['uuid'], 'users'),
                    'customer_billing_id' => uuidtoid($responseData['customer_bill']['uuid'], 'billing_details'),
                    'office' => $responseData['office'] ? json_encode($responseData['office']) : null,
                    'customer' => $responseData['customer'] ? json_encode($responseData['customer']) : null,
                    'customer_billing' => $responseData['customer_bill'] ? json_encode($responseData['customer_bill']) : null,
                    'service_plan' => isset($responseData['service_plan']) ? json_encode($responseData['service_plan']) : null,
                    'one_time_service' => isset($responseData['services']) ? json_encode($responseData['services']) : null,
                    'description' => $responseData['services']['description'] ?? null,
                    'total_initial' => $responseData['payment']['total_initial'] ?? null,
                    'total_yearly_renewal' => $responseData['payment']['total_yearly_renewal'] ?? null,
                    'total_monthly_renewal' => $responseData['payment']['monthly_renewal'] ?? null,
                    'dynamic_text_box' => $responseData['dynamicTextBox'] ? json_encode($responseData['dynamicTextBox']) : null,
                ]
            );
            return $this->responseRedirect('agreement.list', 'Agreement data saved successfully', 'success');
        }
        return view('pages.controls-agreement.add');
    }

    public function edit(Request $request, $uuid)
    {
        $datas = Agreement::where('uuid', $uuid)->first();
        return view('pages.controls-agreement.update', compact('datas'));
    }
}


