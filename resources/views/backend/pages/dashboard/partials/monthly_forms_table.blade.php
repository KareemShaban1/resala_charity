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
                                <h5 class="font-14 my-1 fw-normal">{{ $allMonthlyFormsCount }}</h5>
                                <span class="text-muted font-13">{{__('Donors Count')}}</span>
                            </td>
                            <td>
                                <h5 class="font-14 my-1 fw-normal">${{ number_format($allMonthlyFormsAmount) }}</h5>
                                <span class="text-muted font-13">{{__('Total')}}</span>
                            </td>
                        </tr>
                        <tr class="text-success">
                            <td>
                                <h5 class="font-14 my-1 fw-normal">{{__('Collected Monthly Forms')}}</h5>
                            </td>
                            <td>
                                <h5 class="font-14 my-1 fw-normal">{{ $monthlyFormsCollectedCount }}</h5>
                                <span class="text-muted font-13">{{__('Donors Count')}}</span>
                            </td>
                            <td>
                                <h5 class="font-14 my-1 fw-normal">${{ number_format($monthlyFormsCollectedAmount) }}</h5>
                                <span class="text-muted font-13">{{__('Total')}}</span>
                            </td>
                        </tr>
                        <tr class="text-danger">
                            <td>
                                <h5 class="font-14 my-1 fw-normal">{{__('Not Collected Monthly Forms')}}</h5>
                            </td>
                            <td>
                                <h5 class="font-14 my-1 fw-normal">{{ $monthlyFormsNotCollectedCount }}</h5>
                                <span class="text-muted font-13">{{__('Donors Count')}}</span>
                            </td>
                            <td>
                                <h5 class="font-14 my-1 fw-normal">${{ number_format($monthlyFormsNotCollectedAmount) }}</h5>
                                <span class="text-muted font-13">{{__('Total')}}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


