<?php

namespace App\Http\Controllers;

use App\Models\DonationCategory;
use App\Http\Requests\StoreDonationCategoryRequest;
use App\Http\Requests\UpdateDonationCategoryRequest;
use Yajra\DataTables\Facades\DataTables;

class DonationCategoryController extends BaseController
{
    public function __construct()
    {
        $this->model = DonationCategory::class;
        $this->viewPath = 'backend.pages.donation-categories';
        $this->routePrefix = 'donation-categories';
        $this->validationRules = [
            'name' => 'required|string|max:255|unique:donation_categories,name',
            'active' => 'nullable|boolean',
            'description' => 'nullable|string'
        ];
    }

    public function data()
    {
        $query = $this->model::query();
        
        return DataTables::of($query)
            ->addColumn('action', function ($item) {
                $itemJson = htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8');
                return '
                    <div class="d-flex gap-2">
                        <a href="javascript:void(0);" onclick="editDonationCategory(' . $itemJson . ')"
                        class="btn btn-sm btn-info">
                            <i class="mdi mdi-square-edit-outline"></i>
                        </a>
                        <a href="javascript:void(0);" onclick="deleteDonationCategory(' . $item->id . ', \'donation_categories\')"
                        class="btn btn-sm btn-danger">
                            <i class="mdi mdi-delete"></i>
                        </a>
                    </div>
                ';
            })
            ->editColumn('created_at', function($item) {
                return $item->created_at->format('Y-m-d H:i:s');
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    protected function getUpdateValidationRules($id)
    {
        return [
            'name' => 'required|string|max:255|unique:donation_categories,name,' . $id,
            'active' => 'nullable|boolean',
            'description' => 'nullable|string'
        ];
    }
}
