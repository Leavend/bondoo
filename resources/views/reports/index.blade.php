@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between my-3">
                <div>
                    <h4 class="mb-1">Periodic Reports Generator</h4>
                    <p class="mb-0 text-muted">Generate audit logs and spreadsheets for business sales, procurement, and stock levels.</p>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mx-auto">
            @if($errors->any())
                <div class="alert alert-danger border-left-3">
                    <ul class="mb-0 pl-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card card-block">
                <div class="card-body">
                    <form action="{{ route('reports.generate') }}" method="POST" target="_blank">
                        @csrf
                        
                        <div class="form-group">
                            <label class="font-weight-600">Report Category *</label>
                            <select class="form-control" name="type" required>
                                <option value="sales" selected>Sales Report (Laporan Penjualan)</option>
                                <option value="purchases">Purchase & Supplier Debt Report (Laporan Pengadaan & Hutang)</option>
                                <option value="adjustments">Stock adjustment/Opname Report (Laporan Opname/Koreksi)</option>
                                <option value="returns">Product Returns Report (Laporan Retur)</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-600">Start Date *</label>
                                    <input type="date" class="form-control" name="start_date" value="{{ date('Y-m-01') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-600">End Date *</label>
                                    <input type="date" class="form-control" name="end_date" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-600">Export Format *</label>
                            <div class="mt-2">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="formatHtml" name="format" value="html" class="custom-control-input" checked>
                                    <label class="custom-control-label" for="formatHtml">HTML / Print to PDF</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline ml-3">
                                    <input type="radio" id="formatExcel" name="format" value="excel" class="custom-control-input">
                                    <label class="custom-control-label" for="formatExcel">Excel Spreadsheet (.xlsx)</label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block mt-4">Generate Report</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
