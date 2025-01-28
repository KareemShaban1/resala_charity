<?php

return [
    'required' => 'The :attribute field is required.',
    'exists' => 'The selected :attribute is invalid.',
    'in' => 'The :attribute field must be one of the following: :values.',
    'string' => 'The :attribute field must be a string.',
    'numeric' => 'The :attribute field must be a number.',
    'integer' => 'The :attribute field must be an integer.',
    'array' => 'The :attribute field must be an array.',
    'min' => [
        'numeric' => 'The :attribute must be at least :min.',
        'integer' => 'The :attribute must be at least :min.',
    ],
    'nullable' => 'The :attribute field is optional.',
    'attributes' => [
        'donor_id' => 'donor',
        'department_id' => 'department',
        'employee_id' => 'employee',
        'collecting_donation_way' => 'donation collection method',
        'status' => 'status',
        'cancellation_reason' => 'cancellation reason',
        'cancellation_date' => 'cancellation date',
        'donates' => 'donations',
        'donates.*.financial_donation_type' => 'financial donation type',
        'donates.*.financial_donation_categories_id' => 'financial donation category',
        'donates.*.financial_monthly_donation_id' => 'financial monthly form ID',
        'donates.*.financial_amount' => 'financial amount',
        'donates.*.inKind_donation_type' => 'in-kind donation type',
        'donates.*.inkind_monthly_donation_id' => 'in-kind monthly form ID',
        'donates.*.in_kind_item_name' => 'in-kind item name',
        'donates.*.in_kind_quantity' => 'in-kind quantity',
    ],
];
