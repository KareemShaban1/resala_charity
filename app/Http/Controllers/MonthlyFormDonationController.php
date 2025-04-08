<?php

namespace App\Http\Controllers;

use App\Models\MonthlyFormDonation;
use App\Http\Requests\StoreMonthlyFormDonationRequest;
use App\Http\Requests\UpdateMonthlyFormDonationRequest;
use App\Models\Donation;
use App\Models\MonthlyForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonthlyFormDonationController extends Controller
{
    public function storeMonthlyFormDonation(Request $request)
    {
        $this->authorize('create', Donation::class);

        // Extract the donation date from the request
        $donationDate = $request->input('date');
        $donationMonth = date('m', strtotime($donationDate));
        $donationYear = date('Y', strtotime($donationDate));

        // Check if a donation already exists for this monthly form in the same month
        $existingDonation = DB::table('monthly_form_donations')
            ->where('monthly_form_id', $request->monthly_form_id)
            ->whereMonth('donation_date', $donationMonth)
            ->whereYear('donation_date', $donationYear)
            ->exists();

        if ($existingDonation) {
            return response()->json([
                'success' => false,
                'message' => __('validation.donation_already_exists_for_month'),
            ], 400);
            
        }

        // Validate the incoming request data
        $validatedData = $request->validate([
            'donor_id' => 'required|exists:donors,id',
            'date' => 'required|date',
            'monthly_form_id' => 'required|exists:monthly_forms,id',
            'donation_type' => 'required|string|in:financial,inKind,both',
            'status' => 'required|in:collected,not_collected',
            'reporting_way' => 'required|string|in:call,whatsapp_chat,monthly_donation,other',
            'alternate_date' => 'sometime|date',
            'donation_category' => 'required|string|in:normal,monthly',
            'employee_id' => 'nullable|exists:employees,id',
            'collecting_date' => 'nullable|date',
            'collecting_time' => 'nullable|string',
            'collecting_way' => 'nullable|string|in:representative,location,online',
            'notes' => 'nullable|string',
            'in_kind_receipt_number' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    if (!empty($value)) {
                        $inKindExists = DB::table('donations')
                            ->join('donation_items', 'donations.id', '=', 'donation_items.donation_id')
                            ->join('donation_collectings', 'donations.id', '=', 'donation_collectings.donation_id')
                            ->where('donation_collectings.in_kind_receipt_number', $value)
                            ->where('donation_items.donation_type', 'inKind')
                            ->exists();

                        $donates = $request->get('donates', []);
                        $hasInKind = collect($donates)->contains(
                            fn($d) =>
                            isset($d['inKind_donation_type']) && ($d['inKind_donation_type'] === 'inKind' || $d['financial_donation_type'] === 'Both') &&
                                !empty($d['in_kind_item_name']) &&
                                !empty($d['in_kind_quantity'])
                        );

                        // Fail validation if in-kind donation type exists and receipt number is already used
                        if ($hasInKind && $inKindExists) {
                            $fail(__('validation.unique_receipt_number_in_kind'));
                        }
                    }
                },
            ],
            'donates' => 'required|array',
            'donates.*.financial_donation_type' => 'nullable|in:financial',
            'donates.*.financial_donation_item_type' => 'nullable|in:normal,monthly',
            'donates.*.financial_donation_categories_id' => 'nullable|exists:donation_categories,id',
            'donates.*.financial_amount' => 'nullable|numeric|min:0',
            'donates.*.financial_receipt_number' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    if (!empty($value)) {
                        // Check if the receipt number already exists in the database
                        $financialExists = DB::table('donation_items')
                            // ->join('donation_items', 'donations.id', '=', 'donation_items.donation_id')
                            ->where('donation_items.financial_receipt_number', $value)
                            ->where('donation_items.donation_type', 'financial')
                            ->exists();

                        if ($financialExists) {
                            $fail(__('validation.unique_receipt_number_financial'));
                        }

                        // Check for duplicate receipt numbers within the same request
                        $donates = $request->get('donates', []);
                        $receiptNumbers = [];

                        foreach ($donates as $donate) {
                            if (isset($donate['financial_receipt_number']) && !empty($donate['financial_receipt_number'])) {
                                if (in_array($donate['financial_receipt_number'], $receiptNumbers)) {
                                    $fail(__('validation.duplicate_receipt_number_in_request'));
                                }
                                $receiptNumbers[] = $donate['financial_receipt_number'];
                            }
                        }
                    }
                },
            ],
            'donates.*.in_kind_donation_type' => 'nullable|in:inKind',
            'donates.*.in_kind_donation_item_type' => 'nullable|in:normal,monthly',
            'donates.*.in_kind_item_name' => 'nullable|string',
            'donates.*.in_kind_quantity' => 'nullable|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            // Create the Donation
            $donation = Donation::create([
                'donor_id' => $validatedData['donor_id'],
                'status' => $validatedData['status'],
                'date' => $validatedData['date'],
                'donation_type' => $validatedData['donation_type'],
                'donation_category' => $validatedData['donation_category'],
                'collecting_time' => $validatedData['collecting_time'],
                'notes' => $validatedData['notes'],
                'alternate_date' => $validatedData['alternate_date'] ?? null,
                'reporting_way' => $validatedData['reporting_way'] ?? null,
            ]);

            $donatesAdded = false;

            // Process and save donates
            foreach ($validatedData['donates'] as $donateData) {
                if (
                    isset($donateData['financial_donation_type']) && $donateData['financial_donation_type'] === 'financial'
                    && !empty($donateData['financial_donation_categories_id'])
                    && !empty($donateData['financial_amount'])
                ) {
                    $donation->donateItems()->create([
                        'donation_type' => $donateData['financial_donation_type'],
                        'donation_category_id' => $donateData['financial_donation_categories_id'],
                        'financial_receipt_number' => $donateData["financial_receipt_number"] ?? null,
                        'amount' => $donateData['financial_amount'],
                        'donation_item_type' => $donateData['financial_donation_item_type'],
                    ]);

                    $donatesAdded = true;
                }

                if (
                    isset($donateData['in_kind_donation_type']) && $donateData['in_kind_donation_type'] === 'inKind'
                    && !empty($donateData['in_kind_item_name'])
                    && !empty($donateData['in_kind_quantity'])
                ) {
                    $donation->donateItems()->create([
                        'donation_type' => $donateData['in_kind_donation_type'],
                        'donation_category_id' => null,
                        'item_name' => $donateData['in_kind_item_name'],
                        'amount' => $donateData['in_kind_quantity'],
                        'donation_item_type' => $donateData['in_kind_donation_item_type'],
                    ]);

                    $donatesAdded = true;
                }
            }

            if ($donation->status === "collected") {
                $donation->collectingDonation()->create([
                    "employee_id" => $validatedData["employee_id"],
                    "donation_id" => $donation->id,
                    "collecting_date" => $validatedData["collecting_date"],
                    // 'financial_receipt_number' => $validatedData["financial_receipt_number"] ?? null,
                    'in_kind_receipt_number' => $validatedData["in_kind_receipt_number"] ?? null,
                    "collecting_way" => $validatedData["collecting_way"]
                ]);
            }

            // Find the monthly form and donation
            $monthlyForm = MonthlyForm::find($request->monthly_form_id);

            // Attach the donation to the monthly form with additional pivot data
            $monthlyForm->donations()->attach($donation->id, [
                'donation_date' => $donation->date,
                'month' => date('m', strtotime($donation->date)),
            ]);

            if (!$donatesAdded) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => __('validation.no_valid_donations_provided'),
                ], 400);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('messages.Donation created successfully.'),
                'data' => $donation->load('donateItems'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
