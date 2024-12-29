<!DOCTYPE html>
<html lang="en" xmlns="" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>Traceability Report</title>
    <style>
        table,
        td,
        div,
        h1,
        p {
            font-family: Arial, sans-serif;
        }

        .optionhead {
            background: #95C1E8 !important;
        }

        .optionhead-bg {
            background: #3F8CD0 !important;
        }

        #preloader {
            position: fixed;
            height: 100%;
            width: 100%;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #ffffff;
            z-index: 9999;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes bouncing {
            0% {
                transform: translate3d(0, 10px, 0) scale(1.2, 0.85);
            }

            100% {
                transform: translate3d(0, -20px, 0) scale(0.9, 1.1);
            }
        }

        .d-none {
            display: none;
        }

        .page-break {
            page-break-after: always;
            break-after: always;
        }
    </style>
</head>
@php
    $companyRespectClass = '';
    $companyRespectHeadingClass = '';
    $companyRespectBorderClass = '#bc2226';
    $dummyText =
        'This report is for supplementary purposes only. For full description please refer to an official WDO report or call us at (858) 345-9990. (Offers are honored for a limited time after inspections date.)';
    if ($pricingGuide?->office?->name == 'Moxie Pest Control') {
        $companyRespectClass = 'optionhead-bg';
        $companyRespectHeadingClass = 'optionhead';
        $companyRespectBorderClass = '#3F8CD0';
        $dummyText =
            'This report is for supplementary purposes only. For full description please refer to an official WDO report or call us at (949) 377-0347. (Offers are honored for a limited time after inspections date.)';
    }

    $B2 = $pricingGuide->ms ?? 0;
    $B3 = $pricingGuide->infestations ?? 0;
    $B4 = $pricingGuide->roof_type ?? null;
    $B5 = $pricingGuide->attic_area ?? 0;
    $B6 = $pricingGuide->number_of_attics_or_crawl_spaces ?? 0;
    $B7 = $pricingGuide->number_of_structures_being_fumigated ?? 0;
    $B8 = $pricingGuide->title_warranty ?? 0;
    $P6 = $controlPricing->where('control', 4)->first()->cost;
    $P7 = $controlPricing->where('control', 5)->first()->cost;
    $Q6 = $controlPricing->where('control', 4)->first()->minimum;
    $P18 = $controlPricing->where('control', 15)->first()->cost;
    $Q7 = $controlPricing->where('control', 5)->first()->minimum;
    $Q8 = $controlPricing->where('control', 6)->first()->minimum;
    $Q9 = $controlPricing->where('control', 7)->first()->minimum;
    $P8 = $controlPricing->where('control', 6)->first()->cost;
    $P9 = $controlPricing->where('control', 7)->first()->cost;
    $P17 = $controlPricing->where('control', 14)->first()->cost;
    $P20 = $controlPricing->where('control', 17)->first()->cost;

    $tileWarranty = 0;
    if ($B4 == 1) {
        $tileWarranty = $controlPricing->where('control', 19)->first()->cost;
    } elseif ($B4 == 2) {
        $tileWarranty = $controlPricing->where('control', 20)->first()->cost;
    } elseif ($B4 == 3) {
        $tileWarranty = $controlPricing->where('control', 21)->first()->cost;
    } elseif ($B4 == 4) {
        $tileWarranty = $controlPricing->where('control', 22)->first()->cost;
    } elseif ($B4 == 5) {
        $tileWarranty = $controlPricing->where('control', 23)->first()->cost;
    } elseif ($B4 == 6) {
        $tileWarranty = $controlPricing->where('control', 24)->first()->cost;
    }

    $D43 = $fumigationCal = ($B2 * $P6 < $Q6 ? $Q6 : $B2 * $P6) + ($B7 > 1 ? $B7 * $P18 - $P18 : 0);
    $D44 = $tileWarrantyCal = $tileWarranty;
    // if ($pricingGuide->title_warranty == 1) {
    //     $D44 = $tileWarrantyCal = $tileWarranty;
    // } else {
    //     $D44 = $tileWarrantyCal = 0;
    // }
    $D45 = $fumeTileCal = (float) $fumigationCal + (float) $tileWarrantyCal;
    $D46 = $preventiveTreatmentCal = $B6 <= 1 ? $Q8 : $Q8 + $P8 * ($B6 - 1);
    $D47 = $oneTimeLocaltreatCal = $B3 <= 2 ? $Q9 : $Q9 + ($B3 - 2) * $P9;
    $D48 = $woodRepairsCal = 0;
    $D49 = $WDOInspectionCal = $P17;
    $D50 = $insulationCal = $P7 * $B5 < $Q7 ? $Q7 : $P7 * $B5;
    $D51 = $bundleDiscount = -($D43 * 0.05 > $P20 ? $D43 * 0.05 : $P20);

    // dd($D51);

    $totalPrice =
        $fumigationCal +
        $tileWarrantyCal +
        $fumeTileCal +
        $preventiveTreatmentCal +
        $oneTimeLocaltreatCal +
        $woodRepairsCal +
        $WDOInspectionCal +
        $insulationCal +
        $bundleDiscount;

    //new calculation start
    $initialCostBoth = 10.0;
    $initialCostBothMin = 599;
    $initialCostBoth2 = 8.0;
    $initialCostBoth2Min = 499;
    $monthlyCost = 1.0;
    $monthlyCostMin = 59.0;
    $annualCost = 5.0;
    $annualCostMin = 449.0;
    $fumeCost = 89.0;
    $fumeCostMin = 1799.0;
    $insulationCost = 4.6;
    $insulationCostMin = 2400.0;
    $preventiveTreatment = 150.0;
    $preventiveTreatmentMin = 1099.0;
    $localPerInfestation = 50.0;
    $localPerInfestationMin = 599.0;
    $hwc = 15.0;
    $hwcMin = 699.0;
    $lwc = 35.0;
    $lwcMin = 699.0;
    $onePieceClay = 35.0;
    $onePieceClayMin = 699.0;
    $twoPieceClay = 40.0;
    $twoPieceClayMin = 699.0;
    $twoPieceCement = 45.0;
    $twoPieceCementMin = 699.0;
    $maxi = 40.0;
    $maxiMin = 699.0;
    $wdoInspection = 150.0;
    $additnialStructureFee = 150.0;
    $fumigationBundleMin = 1700.0;
    // $D51 = $bundleDiscount = -400.0;

    $D35 = $drywood_and_subterranean_initial_pif = 1956;
    $E35 = $drywood_and_subterranean_yearly = 449;
    $F35 = $drywood_and_subterranean_initial_contract = 599;
    $G35 = $drywood_and_subterranean_monthly = 59;

    $D36 = $drywood_only_initial_pif = 1626;
    $E36 = $drywood_only_yearly = 399;
    $F36 = $drywood_only_initial_contract = 499;
    $G36 = $drywood_only_monthly = 49;

    $D37 = $subterranean_only_initial_pif = 1726;
    $E37 = $subterranean_only_yearly = 399;
    $F37 = $subterranean_only_initial_contract = 499;
    $G37 = $subterranean_only_monthly = 49;

    //main start
    $drywood_and_subterranean_subscription_fumigation_monthly_f = $D45 + $F35 + $D51;
    $drywood_and_subterranean_subscription_localtreat_monthly_f = $B3 < 3 ? $F35 : $F35 + $D47 + $D51;
    $drywood_and_subterranean_subscription_renewal_monthly_f = 59;

    $drywood_only_subscription_fumigation_monthly_f = $F36 + $D45 + $D51;
    $drywood_only_subscription_localtreat_monthly_f = $B3 < 3 ? $F36 : $F36 + $D47 + $D51;
    $drywood_only_subscription_renewal_monthly_f = 49;

    $subterranean_only_monthly_f = $F37;
    $subterranean_only_renewal_monthly_f = 49;
    $drywood_and_subterranean_subscription_fumigation_pif_f = $D12 = $B8 == 1 ? $D35 + $D45 + $D51 : $D43 + $D35 + $D51;
    $drywood_and_subterranean_subscription_localtreat_pif_f = $F12 = $B3 < 3 ? $D35 : $D35 + $D47 + $D51;
    $drywood_and_subterranean_subscription_renewal_pif_f = 449;

    $drywood_only_subscription_fumigation_pif_f = $B8 == 1 ? $D45 + $D36 + $D51 : $D43 + $D51 + $D36;
    $drywood_only_subscription_localtreat_pif_f = $B3 < 3 ? $D36 : $D36 + $D47 + $D51;
    $drywood_only_subscription_renewal_pif_f = 399;

    $subterranean_only_pif_f = $D37;
    $subterranean_only_renewal_pif_f = 399;

    $initialFumigationWithSubscriptionDiscount = $F12 + $D45 - $D12;
    //main end
    //new calculation end
@endphp


<body style="margin:0;padding:0;">
    <table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;background:#ffffff;"
        class="">
        <tr>
            <td colspan="6" align="center" style="padding:0;">
                <table role="presentation" cellspacing="0" cellpadding="0"
                    style="width:95%;  border-collapse:collapse; border:0px solid #eee;border-spacing:0;text-align:left;">
                    <tr>
                        <td colspan="2" style="padding:16px 0px 20px 0px;">
                            <table role="presentation"
                                style="width:100%; border-collapse:collapse;border:0;border-spacing:0;">
                                <tr class="pdf-section"
                                    style="background:#ffffff; border: hsl(0, 0%, 22%)  solid; border-bottom: 0px;">
                                    <td colspan="2"
                                        style="width: 60%; font-size: 18px; font-weight: 600; padding: 8px 8px;">
                                        <div
                                            style="display: flex; align-items: center; justify-self: start; gap: 5px; padding: 6px 6px;">
                                            <div style="cursor: pointer;"><img
                                                    src="{{ $pricingGuide->office?->logo_path ?? asset('assets/images/moxie-logo.png') }}"
                                                    style="width: 180px;" class="img-fluid" alt=""></div>
                                            <div
                                                style="display: flex; align-items: center;justify-content: end; gap: 4px;">
                                                <a class="downloadPdf" href="javascript:void(0)"
                                                    style="background: #090993; font-size: 12px; font-weight: 600;text-decoration: none; color: #fff;
                                                padding: 5px 6px; border-radius: 4px; text-align: center;">Agreement
                                                    Pricing</a>
                                                <a class="saveAndUpload" href="javascript:void(0)"
                                                    style="background: #090993; font-size: 12px; font-weight: 600;text-decoration: none; color: #fff;
                                                padding: 5px 6px; border-radius: 4px; text-align: center;">Save
                                                    and Upload</a>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="width: 40%;">
                                        <div
                                            style="display: flex; align-items: center; justify-self: start; gap: 5px; padding: 6px 6px;">
                                            <label for="datemin" style="font-size: 14px; width: 166px;">Inspection
                                                Date:</label>
                                            <input type="text" id="datemin" name="datemin" min=""
                                                style="padding: 3px 4px; font-size: 17px; width: 100%;"
                                                value="{{ !empty($pricingGuide->created_at) ? $pricingGuide->created_at->format('m-d-Y') : \Carbon\Carbon::now()->format('m-d-Y') }}"
                                                {{ !empty($pricingGuide->created_at) ? '' : '' }}>
                                        </div>
                                        <div
                                            style="display: flex; align-items: center; justify-self: start; gap: 5px; padding: 6px 6px;">
                                            <label for="datemin" style="font-size: 14px; width: 166px;">Address
                                                :</label>
                                            <textarea id="w3review" name="w3review" rows="2" style="padding: 3px 4px; font-size: 17px; width: 100%;">{{ $pricingGuide->property_address ?? '' }}</textarea>
                                        </div>
                                    </td>
                                </tr>

                                <tr class="showInPdf"
                                    style="background:#ffffff; border: hsl(0, 0%, 22%)  solid; border-bottom: 0px; border-top:2px solid rgb(56, 56, 56); display: none;">
                                    <td colspan="2"
                                        style="width: 55%; font-size: 18px; font-weight: 600; padding: 8px 8px;">
                                        <div
                                            style="display: flex; align-items: center; justify-self: start; gap: 5px; padding: 6px 6px;">
                                            <div style="cursor: pointer;">
                                                <img src="{{ $pricingGuide->office->logo_path ?? asset('assets/images/moxie-logo.png') }}"
                                                    style="width: 180px;" class="img-fluid" alt="">
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding-right: 10px;">
                                        <h3
                                            style="width: 100%; line-height:8px; text-align: left;font-size: 12px; font-weight: 600;padding: 2px 0px;">
                                            Inspection Date : <p id="date"></p>
                                        </h3>
                                        <h3
                                            style="text-align: left;font-size: 12px;  line-height:8px; font-weight: 600;padding: 2px 0px; width: 100%; float: left;">
                                            Address : </h3>
                                        <h3
                                            style="text-align: left;font-size: 12px;  line-height:12px; width: 100%; font-weight: 400;padding: 8px 0px;">
                                            <p id="address"
                                                style="width:200px word-break: break-all; white-space: break-spaces;word-wrap: break-word;">
                                            </p>
                                        </h3>
                                    </td>
                                </tr>

                                <tr style="background:#ffffff; border: #373737  solid; border-top:0px;">
                                    <td colspan="3"
                                        style="text-align: center;font-size: 22px; font-weight: 600;padding: 8px 8px;">
                                        TREATMENT OPTIONS
                                    </td>
                                </tr>

                                <tr class="{{ $companyRespectHeadingClass ?? '' }}"
                                    style="width: 100%; background: #c85b5d; border: #373737  solid;">
                                    <td style="margin: 0px; padding: 0px; border-right: #000  solid; width: 33.3%;">
                                        <h2
                                            style="background: ; padding: 0px 0px; color: #fff;font-size: 17px; font-weight: bold; text-align: center;">
                                            OPTION 1</h2>
                                    </td>
                                    <td style="margin: 0px; padding: 0px; border-right: #000  solid; width: 33.3%;">
                                        <h2
                                            style="background: ; padding: 0px 0px; color: #fff; font-size: 17px; font-weight: bold; text-align: center;">
                                            OPTION 2</h2>
                                    </td>

                                    <td style="margin: 0px; padding: 0px;">
                                        <h2
                                            style="background: ; padding: 0px 0px; color: #fff; font-size: 17px; font-weight: bold; text-align: center;">
                                            OPTION 3</h2>
                                    </td>
                                </tr>

                                <tr style="width: 100%; background: #fff; border: #373737  solid;">
                                    <td class="{{ $companyRespectClass ?? '' }}"
                                        style="vertical-align: top; margin: 0px; padding: 4px 0 16px; border-right: #000  solid; background: #bc2226; width: 33.3%;">
                                        <h3
                                            style="display: flex; align-items: center; justify-content: center; height: 50px; margin: 0px; padding: 0px; font-size: 15px; color: #fff; font-weight: 600; text-align: center;">
                                            Subterranean and<br> Drywood </h3>


                                        <ul
                                            style=" margin: 0px 11px; padding: 0px 0px 0 20px; font-size: 12px; color: #fff; line-height: 18px;">
                                            {{-- <li>Initial Fumigation (Tenting)</li> --}}
                                            <li>
                                                Yearly Inspections
                                            </li>

                                            <li>
                                                Unlimited Localized Treatments
                                            </li>

                                            <li>
                                                Subterranean Bait System
                                            </li>

                                            <li>
                                                Preventive Treatment
                                            </li>

                                            <li>
                                                Mapping/Monitoring of New & Past Infestations
                                            </li>

                                            <li>
                                                Starting at 5% off Future Fumigations, increasing by
                                                <span style="font-size: 11px">
                                                    5% per year up to 50%
                                                </span>
                                            </li>
                                        </ul>
                                    </td>
                                    <td class="{{ $companyRespectClass ?? '' }}"
                                        style="vertical-align: top;margin: 0px; padding: 4px 0 16px; border-right: #000  solid; background: #bc2226; width: 33.3%;">
                                        <h3
                                            style="display: flex; align-items: center; justify-content: center; height: 50px; margin: 0px; padding: 0px; font-size: 15px; color: #fff; font-weight: 600; text-align: center;">
                                            Drywood Only </h3>
                                        <ul
                                            style=" margin: 0px 11px; padding: 0px 0px 0 20px; font-size: 12px; color: #fff; line-height: 18px;">

                                            <li>
                                                Yearly Inspections
                                            </li>

                                            <li>
                                                Unlimited Localized Treatments
                                            </li>

                                            <li>
                                                Preventive Treatment
                                            </li>

                                            <li>
                                                Mapping/Monitoring of New & Past Infestations
                                            </li>

                                            <li>
                                                Starting at 5% off Future Fumigations, increasing by
                                                <span style="font-size: 11px">
                                                    5% per year up to 50%
                                                </span>
                                            </li>
                                        </ul>
                                    </td>

                                    <td class="{{ $companyRespectClass ?? '' }}"
                                        style="vertical-align: top;margin: 0px; padding:4px 0 16px; background: #bc2226; width: 33.3%;">
                                        <h3
                                            style="display: flex; align-items: center; justify-content: center; height: 50px; margin: 0px; padding: 0px; font-size: 15px; color: #fff; font-weight: 600; text-align: center;">
                                            Subterranean Only </h3>
                                        <ul
                                            style=" margin: 0px 11px; padding: 0px 0px 0 20px; font-size: 12px; color: #fff; line-height: 18px;">
                                            {{-- <li>Initial Fumigation (Tenting)</li> --}}
                                            <li>
                                                Yearly Inspections
                                            </li>


                                            <li>
                                                Subterranean Bait System
                                            </li>

                                            <li>
                                                Trenching and sub slab injections as needed
                                            </li>


                                        </ul>
                                    </td>
                                </tr>

                                <tr style="background: #ffffff; border: #373737  solid;">
                                    <td style="padding: 6px; height: 100px; border-right:#373737  solid;">
                                        <select name="subterranean_and_drywood" style="margin-bottom:12px"
                                            id="subterranean_and_drywood"
                                            onchange="changeFnValue(this,'dassfpf','dassfmf')" class="mt-2">
                                            <option value="dassfpf">Paid in full</option>
                                            <option value="dassfmf">Monthly</option>
                                        </select>
                                        <div
                                            style="padding: 0px 0px; margin: 0px 0px; display: flex; align-items: center; justify-content: center;">
                                            <div
                                                style="text-align: center; padding: 5px 0px; font-weight: 500; font-size: 13px; line-height: 17px; color: #000000; border-bottom:1px solid #333; width:50%">
                                                Fumigation :<br>
                                                <input class="dassfpf" id="dassfpf"
                                                    style="margin: 0 4px 0 0;width:85px;"
                                                    value="${{ $drywood_and_subterranean_subscription_fumigation_pif_f ?? '' }}">

                                                <input class="dassfmf d-none" id="dassfmf"
                                                    style="margin:0 4px 0 0;width:85px;"
                                                    value="${{ $drywood_and_subterranean_subscription_fumigation_monthly_f ?? '' }}">
                                            </div>
                                            {{-- <div
                                                style="text-align: center; padding: 5px 0px; font-weight: 500; font-size: 13px; line-height: 17px; color: #000000; border-bottom:1px solid #333; width:50%">
                                                Fumigation :<br>
                                                <p class="dassfpf" id="dassfpf" style="margin: 0 4px 0 0">
                                                    ${{ $drywood_and_subterranean_subscription_fumigation_pif_f ?? '' }}
                                                </p>
                                                <p class="dassfmf d-none" id="dassfmf" style="margin:0 4px 0 0">
                                                    ${{ $drywood_and_subterranean_subscription_fumigation_monthly_f ?? '' }}
                                                </p>

                                            </div> --}}
                                            <div
                                                style="text-align: left;
                                                        padding: 5px 8px;
                                                        font-weight: 500;
                                                        font-size: 13px;
                                                        line-height: 17px;
                                                        color: #000000;
                                                        border: 1px solid #333;
                                                        border-right: 0;
                                                        border-top: 0;width:50%">
                                                Local Treat :<br>
                                                <input class="dassfpf" id="dasslpf"
                                                    style="margin: 0 4px 0 0;width:85px;"
                                                    value="${{ $drywood_and_subterranean_subscription_localtreat_pif_f ?? '' }}">

                                                <input class="dassfmf d-none" id="dasslmf"
                                                    style="margin: 0 4px 0 0;width:85px;"
                                                    value="${{ $drywood_and_subterranean_subscription_localtreat_monthly_f ?? '' }}">
                                                {{-- <p class="dassfpf" id="dasslpf" style="margin: 0 4px 0 0">
                                                    ${{ $drywood_and_subterranean_subscription_localtreat_pif_f ?? '' }}
                                                </p>
                                                <p class="dassfmf d-none" id="dasslmf" style="margin: 0 4px 0 0">
                                                    ${{ $drywood_and_subterranean_subscription_localtreat_monthly_f ?? '' }}
                                                </p> --}}
                                            </div>
                                        </div>
                                        <div
                                            style="text-align: center;margin: 0px;padding: 5px 0px;font-weight: 500;font-size: 13px;color: #000000;">
                                            Renewal :
                                            {{-- <p class="dassfpf" style="margin: 0 4px 0 0">
                                                ${{ $drywood_and_subterranean_subscription_renewal_pif_f ?? '' }}/year
                                            </p>
                                            <p class="dassfmf d-none" style="margin: 0 4px 0 0">
                                                ${{ $drywood_and_subterranean_subscription_renewal_monthly_f ?? '' }}/month
                                            </p> --}}
                                            <input class="dassfpf" style="margin: 0 4px 0 0;width:85px;"
                                                value="${{ $drywood_and_subterranean_subscription_renewal_pif_f ?? '' }}/year">

                                            <input class="dassfmf d-none" style="margin: 0 4px 0 0;width:85px;"
                                                value="${{ $drywood_and_subterranean_subscription_renewal_monthly_f ?? '' }}/month">

                                        </div>
                                        {{-- <h6
                                            style="text-align: left; margin: 0px; padding: 4px 0px; font-weight: 500; font-size: 12px; color: #000000;">
                                            Fact that a reader</h6> --}}
                                    </td>

                                    <td style="padding: 0px 6px; height: 100px; border-right:#373737  solid;">
                                        <select style="margin-bottom:12px" name="drywood_only" id="drywood_only"
                                            onchange="changeFnValue(this,'dosfpf','dosfmf')" class="mt-2">
                                            <option value="dosfpf">Paid in full</option>
                                            <option value="dosfmf">Monthly</option>
                                        </select>
                                        <div
                                            style="padding: 0px 0px; margin: 0px 0px; display: flex; align-items: center; justify-content: center;     width: 100%;">
                                            <div
                                                style="text-align: center; padding: 5px 0px; font-weight: 500; font-size: 13px; line-height: 17px; color: #000000; border-bottom:1px solid #333; width:50%">
                                                Fumigation :<br>
                                                <input class="dosfpf" id="dosfpf"
                                                    style="margin: 0 4px 0 0;width:85px;"
                                                    value="${{ $drywood_only_subscription_fumigation_pif_f ?? '' }}">
                                                <input class="dosfmf d-none" id="dosfmf"
                                                    style="margin: 0 4px 0 0;width:85px;"
                                                    value="${{ $drywood_only_subscription_fumigation_monthly_f ?? '' }}">

                                                {{-- <p class="dosfpf" id="dosfpf" style="margin: 0 4px 0 0">
                                                    ${{ $drywood_only_subscription_fumigation_pif_f ?? '' }}
                                                </p>
                                                <p class="dosfmf d-none" id="dosfmf" style="margin: 0 4px 0 0">
                                                    ${{ $drywood_only_subscription_fumigation_monthly_f ?? '' }}
                                                </p> --}}
                                            </div>
                                            <div
                                                style="text-align: left;
                                                    padding: 5px 8px;
                                                    font-weight: 500;
                                                    font-size: 13px;
                                                    line-height: 17px;
                                                    color: #000000;
                                                    border: 1px solid #333;
                                                    border-right: 0;
                                                    border-top: 0;width:50%">
                                                Local Treat :<br>
                                                <input class="dosfpf" id="doslpf"
                                                    style="margin: 0 4px 0 0;width:85px;"
                                                    value="${{ $drywood_only_subscription_localtreat_pif_f ?? '' }}">

                                                <input class="dosfmf d-none" id="doslmf"
                                                    style="margin: 0 4px 0 0;width:85px;"
                                                    value="${{ $drywood_only_subscription_localtreat_monthly_f ?? '' }}">
                                                {{-- <p class="dosfpf" id="doslpf" style="margin: 0 4px 0 0">
                                                    ${{ $drywood_only_subscription_localtreat_pif_f ?? '' }}
                                                </p>
                                                <p class="dosfmf d-none" id="doslmf" style="margin: 0 4px 0 0">
                                                    ${{ $drywood_only_subscription_localtreat_monthly_f ?? '' }}
                                                </p> --}}
                                            </div>
                                        </div>
                                        <div
                                            style="text-align: center;margin: 0px;padding: 5px 0px;font-weight: 500;font-size: 13px;color: #000000;">
                                            Renewal :
                                            <input class="dosfpf" style="margin: 0 4px 0 0;width:85px;"
                                                value="${{ $drywood_only_subscription_renewal_pif_f ?? '' }}/year">
                                            <input class="dosfmf d-none" style="margin: 0 4px 0 0;width:85px;"
                                                value="${{ $drywood_only_subscription_renewal_monthly_f ?? '' }}/month">

                                            {{-- <p class="dosfpf" style="margin: 0 4px 0 0">
                                                ${{ $drywood_only_subscription_renewal_pif_f ?? '' }}/year
                                            </p>
                                            <p class="dosfmf d-none" style="margin: 0 4px 0 0">
                                                ${{ $drywood_only_subscription_renewal_monthly_f ?? '' }}/month
                                            </p> --}}

                                        </div>
                                        {{-- <h6
                                            style="text-align: left; margin: 0px; padding: 4px 0px; font-weight: 500; font-size: 12px; color: #000000;">
                                            Fact that a reader</h6> --}}
                                    </td>

                                    <td style="padding: 0px 6px; height: 100px;">
                                        <select style="margin-bottom:12px" name="subterranean_only"
                                            id="subterranean_only" onchange="changeFnValue(this,'sopf','somf')"
                                            class="mt-2">
                                            <option value="sopf">Paid in full</option>
                                            <option value="somf">Monthly</option>
                                        </select>
                                        <div
                                            style="padding: 0px 0px; margin: 0px 0px; display: flex; align-items: center; justify-content: center;">
                                            <div
                                                style="text-align: center; padding: 5px 0px; font-weight: 500; font-size: 13px; line-height: 17px; width:50%; color: #000000; border-bottom:1px solid #333">
                                                Initial Price :<br>
                                                <input class="sopf" style="margin: 0 4px 0 0;width:85px;"
                                                    value="${{ $subterranean_only_pif_f ?? '' }}">
                                                <input class="somf d-none" style="margin: 0 4px 0 0;width:85px;"
                                                    value="${{ $subterranean_only_monthly_f ?? '' }}">
                                                {{-- <p class="sopf" style="margin: 0 4px 0 0">
                                                    ${{ $subterranean_only_pif_f ?? '' }}
                                                </p>
                                                <p class="somf d-none" style="margin: 0 4px 0 0">
                                                    ${{ $subterranean_only_monthly_f ?? '' }}
                                                </p> --}}
                                            </div>
                                        </div>
                                        {{-- <div style="display: flex;"> --}}
                                        <div
                                            style="text-align: center; padding: 5px 0px; font-weight: 500; font-size: 13px; line-height: 17px; color: #000000; ">
                                            Renewal :
                                            <input class="sopf" style="margin: 0 4px 0 0;width:85px;"
                                                value="${{ $subterranean_only_renewal_pif_f ?? '' }}/year">
                                            <input class="somf d-none" style="margin: 0 4px 0 0;width:85px;"
                                                value="${{ $subterranean_only_renewal_monthly_f ?? '' }}/month">
                                            {{-- <p class="sopf" style="margin: 0 4px 0 0">
                                                ${{ $subterranean_only_renewal_pif_f ?? '' }}/year
                                            </p>
                                            <p class="somf d-none" style="margin: 0 4px 0 0">
                                                ${{ $subterranean_only_renewal_monthly_f ?? '' }}/month
                                            </p> --}}

                                        </div>
                                        {{-- </div> --}}
                                        {{-- <h6
                                            style="text-align: left; margin: 0px; padding: 4px 0px; font-weight: 500; font-size: 12px; color: #000000;">
                                            Fact that a reader</h6> --}}
                                    </td>
                                </tr>
                                <tr style="background: #bc2226; border: #373737  solid;">
                                    <td class={{ $companyRespectClass ?? '' }}
                                        style="background: #bc2226; padding: 0px 6px; border-right: #373737  solid;"
                                        colspan="2">
                                        <h6
                                            style="text-align: left; margin: 0px; padding: 4px 0px; font-weight: 500; font-size: 13px; color: #fff;">
                                            Fumigation is always the primary recomendation and only sure kill for
                                            Drywood Termite activity</h6>
                                    </td>
                                    <td class="{{ $companyRespectClass ?? '' }}"
                                        style="background: #bc2226; padding: 0px 6px;">
                                        <h6
                                            style="text-align: left; margin: 0px; padding: 4px 0px; font-weight: 500; font-size: 13px; color: #fff;">
                                            Two year minimum on all service plan</h6>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="padding: 0px 6px; border:#373737  solid; height: 20px;">
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="page-break"></div>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3" style="padding: 0px 0px; border:#373737  solid;">
                                        <table class="{{ $companyRespectClass ?? '' }}" role="presentation"
                                            style="background: #bc2226; width:100%; border-collapse:collapse;border:0;border-spacing:0; padding: 0px 0px; margin: 0px 0px; ">
                                            <tr>
                                                <td class="{{ $companyRespectHeadingClass ?? '' }}"
                                                    style="background: #c85b5d; border-right:  solid #000; padding: 0px 0px;">
                                                    <h2
                                                        style="color: #fff;font-size: 17px; font-weight: bold; text-align: center;">
                                                        SERVICE</h2>
                                                </td>
                                                <td class="{{ $companyRespectHeadingClass ?? '' }}"
                                                    style="background: #c85b5d; border-right:  solid #000; padding: 0px 0px; ">
                                                    <h2
                                                        style="color: #fff;font-size: 17px; font-weight: bold; text-align: center;">
                                                        DESCRIPTION</h2>
                                                </td>
                                                <td class="{{ $companyRespectHeadingClass ?? '' }}"
                                                    style="background: #c85b5d; padding: 0px 0px;">
                                                    <h2
                                                        style="color: #fff;font-size: 17px; font-weight: bold; text-align: center;">
                                                        PRICE</h2>
                                                </td>
                                            </tr>
                                            <tr style="border-bottom:  solid #000;">
                                                <td style=" border-right:  solid #000;">
                                                    <p style="padding: 0px 6px; font-size: 12px; color: #fff; ">
                                                        <input type="checkbox" class="allCheck fmgsn"
                                                            name="preventive_treatment" id="preventive_treatment"
                                                            checked="">
                                                        Fumigation
                                                    </p>
                                                </td>
                                                <td style="border-right:  solid #000;">
                                                    <p
                                                        style="padding: 0px 6px; font-size: 12px; color: #fff;     width: 85%; ">
                                                        Fumigation with 2 Year Guarantee </p>
                                                </td>
                                                <td
                                                    style=" background: #fff; text-align: center;border: {{ $companyRespectBorderClass ?? '' }} 2px solid;">
                                                    <input style="padding: 3px 3px; font-size: 13px;" type="text"
                                                        class="form-control" id="fumigationCal"
                                                        placeholder="Example input"
                                                        value="{{ $fumigationCal ?? 0 }}">
                                                </td>
                                            </tr>
                                            <tr style="border-bottom:  solid #000;">
                                                <td style="border-right:  solid #000;">
                                                    <p style="padding: 0px 6px; font-size: 12px; color: #fff; ">
                                                        <input type="checkbox" class="allCheck"
                                                            name="preventive_treatment" id="preventive_treatment"
                                                            checked>
                                                        Tile Warranty
                                                    </p>
                                                </td>
                                                <td style="border-right:  solid #000;">
                                                    <p
                                                        style="padding: 0px 6px; font-size: 12px; color: #fff;     width: 85%;">
                                                        Repair
                                                        or Replace Roof tiles that were damaged during fumigation </p>
                                                </td>
                                                <td
                                                    style="background: #fff; text-align: center; border: {{ $companyRespectBorderClass ?? '' }} 2px solid;">
                                                    <input style="padding: 3px 3px; font-size: 13px; margin: 0px 4px;"
                                                        type="text" class="form-control" id="fumigationCal"
                                                        placeholder="Example input"
                                                        value="{{ $tileWarrantyCal ?? 0 }}">
                                                </td>
                                            </tr>
                                            <tr style="border-bottom:  solid #000;">
                                                <td style="border-right:  solid #000;">
                                                    <p style="padding: 0px 6px; font-size: 12px; color: #fff; ">
                                                        <input type="checkbox" class="allCheck"
                                                            name="preventive_treatment" id="preventive_treatment"
                                                            checked="">
                                                        Fume + tile
                                                    </p>
                                                </td>
                                                <td style="border-right:  solid #000;">
                                                    <p
                                                        style="padding: 0px 6px; font-size: 12px; color: #fff;    width: 85%; ">
                                                        Fumigation and Repair or Replace Roof tiles that were damaged
                                                        during fumigation </p>
                                                </td>
                                                <td
                                                    style="background: #fff; text-align: center; border: {{ $companyRespectBorderClass ?? '' }} 2px solid;">
                                                    <input style="padding: 3px 3px; font-size: 13px; margin: 0px 4px;"
                                                        type="text" class="form-control" id="fumigationCal"
                                                        placeholder="Example input" value="{{ $fumeTileCal ?? 0 }}">
                                                </td>
                                            </tr>
                                            <tr style="border-bottom:  solid #000;">
                                                <td style="border-right:  solid #000;">
                                                    <p style="padding: 0px 6px; font-size: 12px; color: #fff; ">
                                                        <input type="checkbox" class="allCheck"
                                                            name="preventive_treatment" id="preventive_treatment"
                                                            checked>
                                                        Preventive Treatment
                                                    </p>
                                                </td>
                                                <td style="border-right:  solid #000;">
                                                    <p
                                                        style="padding: 0px 6px; font-size: 12px; color: #fff;    width: 85%; ">
                                                        Protective Coating Spreyed on all accessible wood in areas like
                                                        attic and crawl spaces</p>
                                                </td>
                                                <td
                                                    style="background: #fff; text-align: center; border: {{ $companyRespectBorderClass ?? '' }} 2px solid;">
                                                    <input style="padding: 3px 3px; font-size: 13px; margin: 0px 4px;"
                                                        type="text" class="form-control" id="fumigationCal"
                                                        placeholder="Example input"
                                                        value="{{ $preventiveTreatmentCal ?? 0 }}">
                                                </td>
                                            </tr>
                                            <tr style="border-bottom:  solid #000;" class="">
                                                <td style="border-right:  solid #000;">
                                                    <p style="padding: 0px 6px; font-size: 12px; color: #fff; ">
                                                        <input type="checkbox" class="allCheck ontmlctrt"
                                                            name="preventive_treatment" id="preventive_treatment"
                                                            value="1099.00" checked="">
                                                        One time local treat
                                                    </p>
                                                </td>
                                                <td style="border-right:  solid #000;">
                                                    <p
                                                        style="padding: 0px 6px; font-size: 12px; color: #fff; width: 85%;">
                                                        Locally treat to known and accessible infestations - No Warranty
                                                    </p>
                                                </td>
                                                <td
                                                    style="background: #fff; text-align: center; border: {{ $companyRespectBorderClass ?? '' }} 2px solid;">
                                                    <input style="padding: 3px 3px; font-size: 13px; margin: 0px 4px;"
                                                        type="text" class="form-control" id="fumigationCal"
                                                        placeholder="Example input"
                                                        value="{{ $oneTimeLocaltreatCal ?? 0 }}">
                                                </td>
                                            </tr>
                                            <tr style="border-bottom:  solid #000;" class="hideInPdf">
                                                <td style="border-right:  solid #000;">
                                                    <p style="padding: 0px 6px; font-size: 12px; color: #fff; ">
                                                        <input type="checkbox" class="allCheck" name="wood_repairs"
                                                            id="wood_repairs"
                                                            onchange="document.getElementById('woodRepirs').value = !(this.checked)? 'Ask for bid' : '{{ $woodRepairsCal ?? 'Ask for bid' }}';"
                                                            onclick="showHideWoodRepairCalculator(this)">
                                                        Wood Repairs
                                                    </p>
                                                </td>
                                                <td style="border-right:  solid #000;">
                                                    <p
                                                        style="padding: 0px 6px; font-size: 12px; color: #fff;    width: 85%; ">
                                                        Repair or replace damaged wood as identified in WDO report</p>
                                                </td>
                                                <td
                                                    style="background: #fff; text-align: center; border: {{ $companyRespectBorderClass ?? '' }} 2px solid;">
                                                    <input style="padding: 3px 3px; font-size: 13px; margin: 0px 4px;"
                                                        type="text" class="form-control" id="woodRepirs"
                                                        placeholder="Example input"
                                                        value="{{ $woodRepairsCal > 0 ? $woodRepairsCal : 'Ask for bid' }}">
                                                </td>
                                            </tr>
                                            <tr style="border-bottom:  solid #000;" class="hideInPdf">
                                                <td style="border-right:  solid #000;">
                                                    <p style="padding: 0px 6px; font-size: 12px; color: #fff; ">
                                                        <input type="checkbox" class="allCheck"
                                                            name="preventive_treatment" id="preventive_treatment">
                                                        WDO Inspections
                                                    </p>
                                                </td>
                                                <td style="border-right:  solid #000;">
                                                    <p
                                                        style="padding: 0px 6px; font-size: 12px; color: #fff;    width: 85%; ">
                                                        State certified wood destroying organism report</p>
                                                </td>
                                                <td
                                                    style="background: #fff; text-align: center; border: {{ $companyRespectBorderClass ?? '' }} 2px solid;">
                                                    <input style="padding: 3px 3px; font-size: 13px; margin: 0px 4px;"
                                                        type="text" class="form-control" id="fumigationCal"
                                                        placeholder="Example input"
                                                        value="{{ $WDOInspectionCal ?? 0 }}">
                                                </td>
                                            </tr>
                                            <tr style="border-bottom:  solid #000;" class="hideInPdf">
                                                <td style="border-right:  solid #000;">
                                                    <p style="padding: 0px 6px; font-size: 12px; color: #fff; ">
                                                        <input type="checkbox" class="allCheck"
                                                            name="preventive_treatment" id="preventive_treatment">
                                                        Insulations
                                                    </p>
                                                </td>
                                                <td style="border-right:  solid #000;">
                                                    <p
                                                        style="padding: 0px 6px; font-size: 12px; color: #fff;    width: 85%; ">
                                                        Remove, sanitize and replace attic insulation</p>
                                                </td>
                                                <td
                                                    style="background: #fff; text-align: center;border: {{ $companyRespectBorderClass ?? '' }} 2px solid;">
                                                    <input style="padding: 3px 3px; font-size: 13px; margin: 0px 4px;"
                                                        type="text" class="form-control" id="fumigationCal"
                                                        placeholder="Example input"
                                                        value="{{ empty($B5) ? 'Ask for bid' : $insulationCal ?? 0 }}">
                                                </td>
                                            </tr>
                                            <tr style="border-bottom:  solid #000;" class="hideInPdf">
                                                <td style="border-right:  solid #000;">
                                                    <p style="padding: 0px 6px; font-size: 12px; color: #fff; ">
                                                        <input type="checkbox" class="allCheck"
                                                            name="preventive_treatment" id="preventive_treatment">
                                                        Bundle discount
                                                    </p>
                                                </td>
                                                <td style="border-right:  solid #000;">
                                                    <p
                                                        style="padding: 0px 6px; font-size: 12px; color: #fff;    width: 85%; ">
                                                        Discount given with Subscription plans</p>
                                                </td>
                                                <td
                                                    style="background: #fff; text-align: center;border: {{ $companyRespectBorderClass ?? '' }} 2px solid;">
                                                    <input style="padding: 3px 3px; font-size: 13px; margin: 0px 4px;"
                                                        type="text" class="form-control" id="fumigationCal"
                                                        placeholder="Example input"
                                                        value="{{ $bundleDiscount ?? 0 }}">
                                                </td>
                                            </tr>
                                            <tr style="border-bottom:  solid #000;">
                                                <td style="border-right:  solid #000;">
                                                    <p style="padding: 0px 6px; font-size: 12px; color: #fff; ">
                                                        <input type="text" name="manual_first" id="manual_first"
                                                            style="width: 95%;">
                                                    </p>
                                                </td>
                                                <td style="border-right:  solid #000;">
                                                    <p
                                                        style="padding: 0px 6px; font-size: 12px; color: #fff;    width: 85%; ">
                                                        <input type="text" name="manual_first" id="manual_first"
                                                            style="width: 112%;">
                                                    </p>
                                                </td>
                                                <td
                                                    style="background: #fff; text-align: center;border: {{ $companyRespectBorderClass ?? '' }} 2px solid;">
                                                    <input style="padding: 3px 3px; font-size: 13px; margin: 0px 4px;"
                                                        type="text" class="form-control" id="fumigationCal"
                                                        placeholder="" value="">
                                                </td>
                                            </tr>
                                            <tr style="border-bottom:  solid #000;">
                                                <td style="border-right:  solid #000;">
                                                    <p style="padding: 0px 6px; font-size: 12px; color: #fff; ">
                                                        <input type="text" name="" id=""
                                                            style="width: 95%;">
                                                    </p>
                                                </td>
                                                <td style="border-right:  solid #000;">
                                                    <p
                                                        style="padding: 0px 6px; font-size: 12px; color: #fff;    width: 100%; ">
                                                        <input type="text" name="" id=""
                                                            style="width: 95%;">
                                                    </p>
                                                </td>
                                                <td
                                                    style="background: #fff; text-align: center;border: {{ $companyRespectBorderClass ?? '' }} 2px solid;">
                                                    <input style="padding: 3px 3px; font-size: 13px; margin: 0px 4px;"
                                                        type="text" class="form-control" id="fumigationCal"
                                                        placeholder="" value="">
                                                </td>
                                            </tr>
                                            {{-- <tr style="border-bottom: 0px solid #000;">
                                                <td style="border-right:  solid #000;">
                                                    <p
                                                        style="padding: 0px 6px; font-size: 16px; color: #fff; font-weight: bold; ">
                                                        Total :: </p>
                                                </td>
                                                <td style="border-right:  solid #000;">
                                                    <p
                                                        style="padding: 0px 6px; font-size: 16px; color: #fff; font-weight: bold; ">
                                                        Total :: </p>
                                                </td>
                                                <td
                                                    style="background: #fff; text-align: center;border: #bc2226 2px solid;">
                                                    <input style="padding: 3px 3px; font-size: 13px; margin: 0px 4px;"
                                                        type="text" class="form-control" id="fumigationCal"
                                                        placeholder="Example input" value="7797">
                                                </td>
                                            </tr> --}}
                                        </table>
                                    </td>
                                </tr>

                                <tr style="background: #fff; border:  solid #000;">
                                    <td colspan="3" style="color: #000;">
                                        <p style="font-size: 12px; color: #000; text-align: center;">
                                            {{ $dummyText ?? '' }}</p>
                                    </td>
                                </tr>

                                <tr style="background: #fff; border:  solid #000;">
                                    <td colspan="3" style="color: #000;">
                                        <div class="row"
                                            style="width: 100%; display: flex; align-items: center;  justify-content: space-evenly; position: relative; gap:4px">
                                            @for ($i = 0; $i < 5; $i++)
                                                <div style="position: relative;">
                                                    <button type="button" class="btn btn-danger"
                                                        onclick="deleteImage({{ $i + 1 }})"
                                                        style="display: none;color:#fff; border:0;     position: absolute;
                                                                        top: 0;
                                                                        right: 0; background:#bc2226"
                                                        id="delete{{ $i + 1 }}">X</button>
                                                    <div style="width: 120px;  border:#000 solid 1px; margin-bottom: 12px; cursor: pointer;"
                                                        onclick="document.getElementById('img{{ $i + 1 }}').click()">
                                                        <input type="file" accept="image/*" style="display: none;"
                                                            id="img{{ $i + 1 }}"
                                                            onchange="readURL(this,'previewimg{{ $i + 1 }}'); document.getElementById('delete{{ $i + 1 }}').style.display = 'block';">
                                                        <label for="img{{ $i + 1 }}"
                                                            class="btn btn-outline-secondary" style="width: 100%;"><i
                                                                class="fa-solid fa-image"></i></label>
                                                        {{-- <br><br> --}}
                                                        <img src="{{ dynamicNoImage($i + 1) }}"
                                                            style="width: 100%; height: 120px; object-fit: cover;"
                                                            class="img-fluid previewimg{{ $i + 1 }}"
                                                            alt="" id="previewimg{{ $i + 1 }}">
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="page-break"></div>
                                    </td>
                                </tr>

                                <tr style="background: #fff; border:  solid #000;">
                                    <td colspan="3" style="padding: 0px 6px;">
                                        <p class="{{ $companyRespectClass ?? '' }}"
                                            style="padding: 13px; background: #fff; text-align: center;border: #000 2px solid; ">
                                            Initial Fumigation with Subscription discount :
                                            <input type="number" name="fume_discount" id="fume_discount"
                                                style="width: 30%; height: 30px"
                                                value="{{ $initialFumigationWithSubscriptionDiscount ?? 0 }}">
                                        </p>
                                        <p class="{{ $companyRespectClass ?? '' }}"
                                            style="padding: 13px; background: #fff; text-align: center;border: #000 2px solid;">
                                            Wood Repairs calculator :
                                            <input class="wood_repire_calculator" type="checkbox"
                                                id="wood_repire_calculator" name="wood_repire_calculator"
                                                onclick="showHideWoodRepairCalculator(this)"
                                                {{ $pricingGuide->wood_repire_calculator == 1 ? 'checked' : '' }}>
                                        </p>
                                    </td>
                                </tr>
                                {{-- <hr> --}}
                                <tr style="background: #fff; border:  solid #000;">
                                    <td colspan="3">
                                        <div
                                            style="display: flex; padding:0 10px; flex-wrap:wrap; justify-content:space-between">
                                            <p
                                                style="padding: 0px 0px; font-size: 13px; color: #000; font-weight: 500; width:33%">
                                                Address : {{ $pricingGuide->property_address ?? 'N/A' }}</p>
                                            <p
                                                style="padding: 0px 0px; font-size: 13px; color: #000; font-weight: 500; width:33%">
                                                Inspector : {{ $pricingGuide->who_the_inspector_is ?? 'N/A' }}</p>
                                            <p
                                                style="padding: 0px 0px; font-size: 13px; color: #000; font-weight: 500; width:33%">
                                                Inspection type : @switch($pricingGuide->inspection_tag ?? 0)
                                                    @case(1)
                                                        Attic
                                                    @break

                                                    @case(2)
                                                        Garage
                                                    @break

                                                    @case(3)
                                                        Crawlspace
                                                    @break

                                                    @case(4)
                                                        Other
                                                    @break

                                                    @default
                                                        N/A
                                                @endswitch
                                            </p>
                                            <p
                                                style="padding: 0px 0px; font-size: 13px; color: #000; font-weight: 500; width:33%">
                                                M size : {{ $pricingGuide->ms ?? 'N/A' }}</p>
                                            <p
                                                style="padding: 0px 0px; font-size: 13px; color: #000; font-weight: 500; width:33%">
                                                Number of structures :
                                                {{ $pricingGuide->number_of_structures_being_fumigated ?? 'N/A' }}</p>
                                            <p
                                                style="padding: 0px 0px; font-size: 13px; color: #000; font-weight: 500; width:33%">
                                                Number of attics:
                                                {{ $pricingGuide->number_of_attics_or_crawl_spaces ?? 'N/A' }}</p>

                                            {{-- <p
                                                style="padding: 0px 0px; font-size: 13px; color: #000; font-weight: 500; width:33%">
                                                Inspector:
                                                {{ $pricingGuide->who_the_inspector_is ?? 'N/A' }}</p> --}}
                                            <p
                                                style="padding: 0px 0px; font-size: 13px; color: #000; font-weight: 500; width:33%">
                                                Report Type:
                                                {{ getReportType()[$pricingGuide->report_type] ?? 'N/A' }}<br>
                                                {{ !empty($pricingGuide->specify_area) ? 'Details :' . $pricingGuide->specify_area : '' }}
                                            </p>
                                            <p
                                                style="padding: 0px 0px; font-size: 13px; color: #000; font-weight: 500; width:33%">
                                                Building Type:
                                                {{ getBuildingType()[$pricingGuide->building_type] ?? 'N/A' }}</p>
                                            <p
                                                style="padding: 0px 0px; font-size: 13px; color: #000; font-weight: 500; width:33%">
                                                Occupancy :
                                                {{ getOccupancy()[$pricingGuide->occupancy] ?? 'N/A' }}</p>
                                            <p
                                                style="padding: 0px 0px; font-size: 13px; color: #000; font-weight: 500; width:33%">
                                                Garage/Carport :
                                                {{ getGarageCarport()[$pricingGuide->garage_carport] ?? 'N/A' }}</p>
                                            <p
                                                style="padding: 0px 0px; font-size: 13px; color: #000; font-weight: 500; width:33%">
                                                Substructure :
                                                {{ getSubstructure()[$pricingGuide->substructure] ?? 'N/A' }}</p>
                                            <p
                                                style="padding: 0px 0px; font-size: 13px; color: #000; font-weight: 500; width:33%">
                                                Other Structures :
                                                {{ getOtherSubstructure()[$pricingGuide->other_structures] ?? 'N/A' }}
                                            </p>
                                            <p
                                                style="padding: 0px 0px; font-size: 13px; color: #000; font-weight: 500; width:33%">
                                                # of structures :
                                                {{ getInspectionTag()[$pricingGuide->number_of_structures] ?? 'N/A' }}
                                            </p>
                                            <p
                                                style="padding: 0px 0px; font-size: 13px; color: #000; font-weight: 500; width:33%">
                                                # of stories :
                                                {{ $pricingGuide->number_of_stories ?? 'N/A' }}</p>
                                            <p
                                                style="padding: 0px 0px; font-size: 13px; color: #000; font-weight: 500; width:33%">
                                                Number of Gas Meters :
                                                {{ getServicePlan()[$pricingGuide->number_of_gas_meters] ?? 'N/A' }}
                                            </p>
                                            <p
                                                style="padding: 0px 0px; font-size: 13px; color: #000; font-weight: 500; width:33%">
                                                Inspection Tag :
                                                {{ getInspectionTag()[$pricingGuide->inspection_tag] ?? 'N/A' }}</p>
                                            <p
                                                style="padding: 0px 0px; font-size: 13px; color: #000; font-weight: 500; width:33%">
                                                Tile warranty included :
                                                {{ getTileWarranty()[$pricingGuide->title_warranty] ?? 'N/A' }}</p>
                                            <p
                                                style="padding: 0px 0px; font-size: 13px; color: #000; font-weight: 500; width:33%">
                                                Other :
                                                {{ $pricingGuide->other ?? 'N/A' }}</p>
                                            <p
                                                style="padding: 0px 0px; font-size: 13px; color: #000; font-weight: 500; width:33%">
                                                Attic Area :
                                                {{ $pricingGuide->attic_area ?? 'N/A' }}</p>
                                            <p
                                                style="padding: 0px 0px; font-size: 13px; color: #000; font-weight: 500; width:33%">
                                                Roof type :
                                                {{ getRoofType()[$pricingGuide->roof_type] ?? 'N/A' }}</p>
                                            <p
                                                style="padding: 0px 0px; font-size: 13px; color: #000; font-weight: 500; width:33%">
                                                <span style="display: inline-block;">Number of structures being
                                                    fumigated :</span>
                                                <span
                                                    style="display: inline-block;">{{ $pricingGuide->number_of_structures_being_fumigated ?? 'N/A' }}</span>
                                            </p>
                                            <p
                                                style="padding: 0px 0px; font-size: 13px; color: #000; font-weight: 500; width:33%">
                                                Number of attics / Crawl Spaces :
                                                {{ $pricingGuide->number_of_attics_or_crawl_spaces ?? 'N/A' }}</p>
                                            {{-- <p
                                                style="padding: 0px 0px; font-size: 13px; color: #000; font-weight: 500; width:33%">
                                                M's :
                                                {{ $pricingGuide->ms ?? 'N/A' }}</p> --}}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" id="noteinput">
                                        <textarea id="noteinputval"
                                            style="margin: 8px auto 23px auto; padding: 10px; width: 99%;word-wrap: break-word; word-break: break-all;white-space: break-spaces; min-height:100px;resize: none;"
                                            class="form-control2">{{ $pricingGuide->findings_and_notes ?? '' }}</textarea>
                                    </td>

                                    <td colspan="3" id="noteview" class="d-none"
                                        style="padding: 0;margin:0;border:none;">
                                        <p style="font-size:12px; margin: 8px auto 23px auto; padding: 10px; word-wrap: break-word; word-break: break-all; white-space: break-spaces; min-height:80px;resize: none; border:2px solid #000;"
                                            class="form-control2"> <span id="noteviewval"></span></p>
                                    </td>
                                </tr>
                                {{-- <tr class="page-break">
                                    <td colspan="3" style="height: 30px;"></td>
                                </tr> --}}

                                <tr>
                                    <td colspan="3" style="height: 70px;"></td>
                                </tr>

                                <tr class="woodRepireCalculator {{ $pricingGuide->wood_repire_calculator == 1 ? '' : 'd-none' }}"
                                    style="background: #fff; border:  solid #000;">
                                    <td colspan="3"
                                        style="margin: 0px 0px; padding: 12px 0px; text-align: center; font-size: 22px; font-weight: bold; color: #000;">
                                        Termite Wood Repairs Rate Card</td>
                                </tr>

                                <tr class="woodRepireCalculator {{ $pricingGuide->wood_repire_calculator == 1 ? '' : 'd-none' }}"
                                    style="background: #fff; border:  solid #000;">
                                    <td colspan="3" style="">
                                        <table role="presentation"
                                            style="background: #fff; width:100%; border-collapse:collapse;border:0;border-spacing:0; padding: 0px 0px; margin: 0px 0px; ">
                                            <tr style=" border-bottom:  solid #000;">
                                                <td
                                                    style="background: #fff; border-right:  solid #000; font-size: 14px; font-weight: 600; padding: 6px 6px;">
                                                    ITEM</td>
                                                <td
                                                    style="background: #fff; border-right:  solid #000; font-size: 14px; font-weight: 600; padding: 6px 6px;">
                                                    DESCRIPTION</td>
                                                <td
                                                    style="background: #fff; border-right:  solid #000; font-size: 14px; font-weight: 600; padding: 6px 6px;">
                                                    UOM</td>
                                                <td
                                                    style="background: #fff; border-right:  solid #000; font-size: 14px; font-weight: 600; padding: 6px 6px;">
                                                    UNIT PRICE</td>
                                                <td
                                                    style="background: #fff; border-right:  solid #000; font-size: 14px; font-weight: 600; padding: 6px 6px;">
                                                    COUNT</td>
                                                <td
                                                    style="background: #fff; font-size: 14px; font-weight: 600; padding: 6px 6px;">
                                                    UNIT PRICE </td>
                                            </tr>

                                            @php
                                                $qtyArr = json_decode($pricingGuide->qty) ?? null;
                                                $totalPriceArr = json_decode($pricingGuide->total_price) ?? null;
                                            @endphp
                                            @forelse ($woodRepair as $key => $item)
                                                <tr style="">
                                                    <td
                                                        style="background: #fff; border-right:  solid #000; font-size: 14px; font-weight: 600; padding: 6px 6px;">
                                                        {{ $loop->iteration }}.
                                                    </td>
                                                    <td
                                                        style="background: #fff; border-right:  solid #000; font-size: 14px; font-weight: 600; padding: 6px 6px;">
                                                        {{ $item->item ?? '' }}
                                                    </td>
                                                    <td
                                                        style="background: #fff; border-right:  solid #000; font-size: 14px; font-weight: 600; padding: 6px 6px;">
                                                        {{ $item->uom?->title ?? 'N/A' }}
                                                    </td>
                                                    <td
                                                        style="background: #fff; border-right:  solid #000; font-size: 14px; font-weight: 600; padding: 6px 6px;">
                                                        {{ $item->price ?? 0 }}
                                                    </td>
                                                    <td
                                                        style="background: #fff; border-right:  solid #000; font-size: 14px; font-weight: 600; padding: 6px 6px;">
                                                        <input
                                                            style="padding: 3px 3px; font-size: 13px; margin: 0px 4px;"
                                                            type="number" class="form-control qty allCount"
                                                            id="qty" name="qty[]" placeholder="Example input"
                                                            value="{{ $qtyArr[$key] ?? 0 }}"
                                                            onkeyup="calculateTotalPrice('{{ $item->price }}',this.value,'total-price-{{ $item->id }}')">
                                                    </td>
                                                    <td
                                                        style="background: #fff; font-size: 14px; font-weight: 600; padding: 6px 6px;">
                                                        <input
                                                            style="padding: 3px 3px; font-size: 13px; margin: 0px 4px;"
                                                            type="text"
                                                            class="form-control totalPrice allCount total-price-{{ $item->id }}"
                                                            id="total_price" name="total_price[]"
                                                            placeholder="Example input"
                                                            value="{{ $totalPriceArr[$key] ?? 0 }}" readonly>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6">No Data Found</td>
                                                </tr>
                                            @endforelse
                                            {{-- <tr>
                                                @php
                                                    $tripCharge = $woodRepair->where('item', 'Trip charge')->first();
                                                @endphp
                                                <td class="table-txet">
                                                    <p></p>
                                                </td>
                                                <td class="table-txet" style="text-align: left;">
                                                    <p></p>
                                                </td>
                                                <td class="table-txet">
                                                    <p></p>
                                                </td>
                                                <td class="table-txet" style="text-align: right;">
                                                    <p></p>
                                                </td>
                                                <td class="table-txet" style="text-align: right;">
                                                    <p>Total</p>
                                                </td>
                                                <td class="table-txet" style="text-align: right;">
                                                    <p><input type="text" class="form-control actTotalPrice"
                                                            placeholder="Total Price"
                                                            value="{{ $tripCharge->price ?? 600 }}">
                                                    </p>
                                                </td>
                                            </tr> --}}
                                        </table>
                                    </td>
                                </tr>

                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        const woodRepairTotal = "{{ $woodRepairTotal ?? 0 }}";
        // $('#woodRepairsCal').val(woodRepairTotal);
        // $('#wood_repairs').val(woodRepairTotal);
        calTotPri();
    });

    $(".fmgsn").click(function() {
        const dassfpf = "{{ $drywood_and_subterranean_subscription_fumigation_pif_f }}";
        const dassfmf = "{{ $drywood_and_subterranean_subscription_fumigation_monthly_f }}";

        const dosfpf = "{{ $drywood_only_subscription_fumigation_pif_f }}";
        const dosfmf = "{{ $drywood_only_subscription_fumigation_monthly_f }}";
        if ($(this).prop('checked')) {
            $('#dassfpf').html(`$${dassfpf}`);
            $('#dassfmf').html(`$${dassfmf}`);

            $('#dosfpf').html(`$${dosfpf}`);
            $('#dosfmf').html(`$${dosfmf}`);
        } else {
            $('#dassfpf').html('Ask For Bid');
            $('#dassfmf').html('Ask For Bid');

            $('#dosfpf').html('Ask For Bid');
            $('#dosfmf').html('Ask For Bid');
        }
    });

    $(".ontmlctrt").click(function() {
        const dasslpf = "{{ $drywood_and_subterranean_subscription_localtreat_pif_f }}";
        const dasslmf = "{{ $drywood_and_subterranean_subscription_localtreat_monthly_f }}";

        const doslpf = "{{ $drywood_only_subscription_localtreat_pif_f }}";
        const doslmf = "{{ $drywood_only_subscription_localtreat_monthly_f }}";
        if ($(this).prop('checked')) {
            $('#dasslpf').html(`$${dasslpf}`);
            $('#dasslmf').html(`$${dasslmf}`);

            $('#doslpf').html(`$${doslpf}`);
            $('#doslmf').html(`$${doslmf}`);
        } else {
            $('#dasslpf').html('Ask For Bid');
            $('#dasslmf').html('Ask For Bid');

            $('#doslpf').html('Ask For Bid');
            $('#doslmf').html('Ask For Bid');
        }
    });

    // var qty = [];
    // var totalPrice = [];
    // $(document).on("keyup", ".qty", function() {
    //     var qty = [];
    //     var totalPrice = [];
    //     $(".qty").each(function(index) {
    //         qty.push($(this).val());
    //     });
    //     $(".totalPrice").each(function(index) {
    //         totalPrice.push($(this).val());
    //     });
    // });

    function changeFnValue(th, val1, val2) {
        if (th.value == val1) {
            var val1QuerySelector = document.getElementsByClassName(val1);
            for (var i = 0; i < val1QuerySelector.length; i++) {
                val1QuerySelector[i].classList.toggle('d-none');
            }

            var val2QuerySelector = document.getElementsByClassName(val2);
            for (var i = 0; i < val2QuerySelector.length; i++) {
                val2QuerySelector[i].classList.toggle('d-none');
            }

        } else if (th.value == val2) {
            var val2QuerySelector = document.getElementsByClassName(val2);
            for (var i = 0; i < val2QuerySelector.length; i++) {
                val2QuerySelector[i].classList.toggle('d-none');
            }

            var val1QuerySelector = document.getElementsByClassName(val1);
            for (var i = 0; i < val1QuerySelector.length; i++) {
                val1QuerySelector[i].classList.toggle('d-none');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const pdfSection = document.querySelector('.pdf-section');
        const pdfSectionStyle = window.getComputedStyle(pdfSection);
        const pdfSectionDisplay = pdfSectionStyle.display;
        const pdfSectionVisibility = pdfSectionStyle.visibility;

        // Function to generate PDF and save to database
        function generateAndSavePDF() {
            var qty = [];
            var totalPrice = [];
            $(".qty").each(function(index) {
                qty.push($(this).val());
            });
            $(".totalPrice").each(function(index) {
                totalPrice.push($(this).val());
            });
            // Hide the section
            pdfSection.style.display = 'none';
            pdfSection.style.visibility = 'hidden';

            const datemin = document.getElementById('datemin').value;
            const w3review = document.getElementById('w3review').value;
            const noteinput = document.getElementById('noteinputval').value;

            document.getElementById('address').innerHTML = w3review ?? 'N/A';
            document.getElementById('date').innerHTML = datemin ?? 'N/A';
            document.getElementById('noteviewval').innerHTML = noteinput ?? 'N/A';

            $('.showInPdf').show();
            $('#noteinput').hide();
            $('#noteview').show();
            // Options for PDF generation
            var opt = {
                margin: 1,
                filename: 'pricing-guide-calculate.pdf',
                image: {
                    type: 'jpeg',
                    quality: 0.95
                },
                html2canvas: {
                    scale: 2,
                    useCORS: true,
                    // Fix for right side not showing up properly
                    windowWidth: document.body.scrollWidth -
                        15, // Add extra width to ensure right side is not cut off
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait'
                }
            };

            // Get the content of the entire page
            var element = document.body;

            // Generate PDF
            html2pdf().from(element).set(opt).outputPdf('datauristring').then(function(pdfAsString) {
                // Send PDF data and HTML content to server using AJAX
                $.ajax({
                    url: "{{ route('pricing.guide.calculate', $pricingGuide->uuid) }}", // Replace with your server endpoint
                    method: 'POST',
                    data: {
                        pdf: pdfAsString,
                        qty: qty,
                        total_price: totalPrice,
                        w3review: w3review,
                        html: element.outerHTML, // Save the full HTML content
                        _token: '{{ csrf_token() }}' // CSRF token for Laravel
                    },
                    success: function(response) {
                        // Show the section
                        pdfSection.style.display = pdfSectionDisplay;
                        pdfSection.style.visibility = pdfSectionVisibility;
                        $('.showInPdf').hide();
                        $('#noteinput').show();
                        $('#noteview').hide();

                        $(location).attr("href", "{{ route('pricing.guide') }}");
                    },
                    error: function(xhr, status, error) {
                        console.error('Error saving PDF and HTML:', error);
                        alert('Error saving PDF and HTML. Please try again.');

                        $('.showInPdf').hide();
                        $('#noteinput').show();
                        $('#noteview').hide();
                    }
                });
            });
        }

        // Function to generate PDF and download
        function generateAndDownload() {
            // Hide the section
            pdfSection.style.display = 'none';
            pdfSection.style.visibility = 'hidden';

            const datemin = document.getElementById('datemin').value;
            const w3review = document.getElementById('w3review').value;
            const noteinput = document.getElementById('noteinputval').value;

            document.getElementById('address').innerHTML = w3review ?? 'N/A';
            document.getElementById('date').innerHTML = datemin ?? 'N/A';
            document.getElementById('noteviewval').innerHTML = noteinput ?? 'N/A';
            $('.showInPdf').show();
            // $('.hideInPdf').hide();
            $('#noteinput').hide();
            $('#noteview').show();
            // Options for PDF generation
            var opt = {
                margin: 1,
                filename: 'pricing-guide-calculate.pdf',
                image: {
                    type: 'jpeg',
                    quality: 0.95
                },
                html2canvas: {
                    scale: 2,
                    useCORS: true,
                    // Fix for right side not showing up properly
                    windowWidth: document.body.scrollWidth -
                        15, // Add extra width to ensure right side is not cut off
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait'
                }
            };

            // Get the content of the entire page
            var element = document.body;

            // Generate PDF and download
            html2pdf().from(element).set(opt).save().then(function() {
                // Show the section
                pdfSection.style.display = pdfSectionDisplay;
                pdfSection.style.visibility = pdfSectionVisibility;
                $('.showInPdf').hide();
                // $('.hideInPdf').show();
                $('#noteinput').show();
                $('#noteview').hide();
            });
        }

        // Add click event listener to elements with class 'saveAndUpload'
        var saveAndUploadButtons = document.getElementsByClassName('saveAndUpload');
        for (var i = 0; i < saveAndUploadButtons.length; i++) {
            saveAndUploadButtons[i].addEventListener('click', generateAndSavePDF);
        }

        var downloadPdfButtons = document.getElementsByClassName('downloadPdf');
        for (var i = 0; i < downloadPdfButtons.length; i++) {
            downloadPdfButtons[i].addEventListener('click', generateAndDownload);
        }
    });

    function calculateTotalPrice(price, count, className) {
        $('.' + className).val(price * count);
        calTotPri();
    }

    function calTotPri() {
        const actTotalPrice = "{{ $tripCharge->price ?? 600 }}";
        // let sum = parseFloat(actTotalPrice);
        let sum = 0;
        $('.totalPrice').each(function() {
            sum += parseFloat($(this).val()) || 0;
        });
        $('.actTotalPrice').val(sum.toFixed(2));
        $('#woodRepairsCal').val(sum.toFixed(2));
        if (sum > 0) {
            $('#woodRepirs').val(sum.toFixed(2));
        } else {
            $('#woodRepirs').val('Ask for bid');
        }
    }

    $(document).on("click", ".allCheck", function() {
        let sum = 0;
        $(".allCheck").each(function(index) {
            if (this.checked) {
                sum += parseFloat(this.value ?? 0) ?? 0;
                $(this).parent().parent().parent('tr').removeClass('hideInPdf');
            } else {
                $(this).parent().parent().parent().addClass('hideInPdf');
            }
            $('#totalPrice').val(sum);
        });
    });

    $(document).on("click", ".allCheck", function() {
        // this.value = 0;
        $(this).attr('checked', false);
        if (this.checked) {
            // this.value = 1;
            $(this).attr('checked', true);
        }
    });

    function readURL(input, id) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#' + id).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function deleteImage(index) {
        let imgId = 'img' + index;
        let previewImgId = 'previewimg' + index;
        let deleteBtnId = 'delete' + index;

        document.getElementById(imgId).value = '';
        document.getElementById(previewImgId).src = `{{ asset('assets/images/blank${index}.png') }}`;
        document.getElementById(deleteBtnId).style.display = 'none';
    }

    function showHideWoodRepairCalculator(sss) {
        var woodRepairElements = document.getElementsByClassName("woodRepireCalculator");
        // console.log(woodRepairElements);
        if (woodRepairElements) {
            for (var i = 0; i < woodRepairElements.length; i++) {
                woodRepairElements[i].classList.toggle('d-none');
            }
        }

        if (sss.checked) {
            setTimeout(() => {
                document.getElementById("wood_repire_calculator").checked = true;
                document.getElementById("wood_repairs").checked = true;
            }, 500);
        } else {
            setTimeout(() => {
                document.getElementById("wood_repairs").checked = false;
                document.getElementById("wood_repire_calculator").checked = false;
                document.getElementById("woodRepirs").value = 'Ask for bid';
                let allCounts = document.getElementsByClassName("allCount");
                for (let i = 0; i < allCounts.length; i++) {
                    allCounts[i].value = 0;
                }
            }, 500);
        }
    }
</script>

</html>
