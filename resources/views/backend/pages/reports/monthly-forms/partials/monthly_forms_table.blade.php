<div class="col-xl-6 col-lg-6 order-lg-2 order-xl-1">
    <div class="card">
        <div class="card-body">
            <a href="dashboard.export_reports" class="btn btn-sm btn-link float-end">Export
                <i class="mdi mdi-download ms-1"></i>
            </a>
            <h4 class="header-title mt-2 mb-3">{{__('Monthly Forms Report')}}</h4>
            <div class="table-responsive">
                <table class="table table-centered table-nowrap table-hover mb-0">
                    <tbody>
                        <tr class="text-info">
                            <td>
                                <h5 class="font-14 my-1 fw-normal">{{__('Total Monthly Forms')}}</h5>
                            </td>
                            <td>
                                <h5 class="font-14 my-1 fw-normal" id="allMonthlyFormsCount">{{ $allMonthlyFormsCount }}</h5>
                                <span class="text-muted font-13">{{__('Donors Count')}}</span>
                            </td>
                            <td>
                                <h5 class="font-14 my-1 fw-normal" id="allMonthlyFormsAmount">${{ number_format($allMonthlyFormsAmount) }}</h5>
                                <span class="text-muted font-13">{{__('Total')}}</span>
                            </td>
                        </tr>
                        <tr class="text-success">
                            <td>
                                <h5 class="font-14 my-1 fw-normal">{{__('Collected Monthly Forms')}}</h5>
                            </td>
                           
                            <td>
                                <h5 class="font-14 my-1 fw-normal" id="monthlyFormsCollectedCount">{{ $monthlyFormsCollectedCount }}</h5>
                                <span class="text-muted font-13">{{__('Donors Count')}}</span>
                            </td>
                            <td>
                                <h5 class="font-14 my-1 fw-normal" id="monthlyFormsCollectedAmount">${{ number_format($monthlyFormsCollectedAmount) }}</h5>
                                <span class="text-muted font-13">{{__('Total')}}</span>
                            </td>
                        </tr>
                        <tr class="text-danger">
                            <td>
                                <h5 class="font-14 my-1 fw-normal" >{{__('Not Collected Monthly Forms')}}</h5>
                            </td>
                            <td>
                                <h5 class="font-14 my-1 fw-normal" id="monthlyFormsNotCollectedCount">{{ $monthlyFormsNotCollectedCount }}</h5>
                                <span class="text-muted font-13">{{__('Donors Count')}}</span>
                            </td>
                            <td>
                                <h5 class="font-14 my-1 fw-normal" id="monthlyFormsNotCollectedAmount">${{ number_format($monthlyFormsNotCollectedAmount) }}</h5>
                                <span class="text-muted font-13">{{__('Total')}}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="col-xl-6 col-lg-6 order-lg-2 order-xl-1">
    <div class="card">
        <div class="card-body">
            <a href="dashboard.export_reports" class="btn btn-sm btn-link float-end">Export
                <i class="mdi mdi-download ms-1"></i>
            </a>
            <h4 class="header-title mt-2 mb-3">{{__('Monthly Forms Report')}}</h4>
            <div class="table-responsive">
                <table class="table table-centered table-nowrap table-hover mb-0">
                    <tbody>
                        <tr class="text-info">
                            <td>
                                <h5 class="font-14 my-1 fw-normal">{{__('Total Cancelled Monthly Forms')}}</h5>
                            </td>
                            <td>
                                <h5 class="font-14 my-1 fw-normal">{{ $cancelledMonthlyFormsCount }}</h5>
                                <span class="text-muted font-13">{{__('Donors Count')}}</span>
                            </td>
                            <td>
                                <h5 class="font-14 my-1 fw-normal">${{ number_format($cancelledMonthlyFormsAmount) }}</h5>
                                <span class="text-muted font-13">{{__('Total')}}</span>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>