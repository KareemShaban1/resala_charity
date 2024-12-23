<?php

namespace App\Http\Controllers;

use App\Models\MonthlyDonation;
use App\Http\Requests\StoreMonthlyDonationRequest;
use App\Http\Requests\UpdateMonthlyDonationRequest;
use App\Models\DonationCategory;
use App\Models\Donor;
use Yajra\DataTables\Facades\DataTables;

class MonthlyDonationController extends BaseController
{
    public function __construct()
    {
        $this->model = MonthlyDonation::class;
        $this->viewPath = 'backend.pages.monthly_donations';
        $this->routePrefix = 'monthly_donations';
        $this->validationRules = [
            'donor_id' => 'required|exists:donors,id',
            'created_by' => 'required|exists:users,id',
            'date' => 'required|string',
            'notes' => 'nullable|string',
            'collecting_donation_way' => 'required|string',
            // 'collected_by' => 'nullable|exists:users,id'
        ];
    }

    public function index()
    {
        $donors = Donor::all();
        $donationCategories = DonationCategory::all();
        return view($this->viewPath . '.index', compact('donors','donationCategories'));
    }

    public function data()
    {
        $query = $this->model::query();

        return DataTables::of($query)
            ->addColumn('action', function ($item) {
                return '
                    <div class="d-flex gap-2">
                        <a href="javascript:void(0);" onclick="editMonthlyDonation(' . $item->id .')"
                        class="btn btn-sm btn-info">
                            <i class="mdi mdi-square-edit-outline"></i>
                        </a>
                        <a href="javascript:void(0);" onclick="deleteRecord(' . $item->id . ', \'monthly_donations\')"
                        class="btn btn-sm btn-danger">
                            <i class="mdi mdi-delete"></i>
                        </a>
                    </div>
                ';
            })
            ->editColumn('created_at', function ($item) {
                return $item->created_at->format('Y-m-d H:i:s');
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    protected function getUpdateValidationRules($id)
    {
        return [
            'donor_id' => 'required|exists:donors,id',
            'created_by' => 'required|exists:users,id',
            'date' => 'required|string',
            'notes' => 'nullable|string',
            'collecting_donation_way' => 'required|string',
            'collected_by' => 'nullable|exists:users,id'
        ];
    }
}
