<?php

namespace App\Http\Controllers;

use App\Models\DonationItem;
use App\Http\Requests\StoreDonationItemRequest;
use App\Http\Requests\UpdateDonationItemRequest;

class DonationItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDonationItemRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(DonationItem $donationItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DonationItem $donationItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDonationItemRequest $request, DonationItem $donationItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DonationItem $donationItem)
    {
        //
    }
}
