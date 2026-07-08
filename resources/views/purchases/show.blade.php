@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between my-3">
                <div>
                    <h4 class="mb-1">Purchase Order Details</h4>
                    <p class="mb-0 text-muted">PO: <strong>{{ $purchase->purchase_no }}</strong></p>
                </div>
                <a href="{{ route('purchases.index') }}" class="btn btn-light border-0"><i class="las la-arrow-left mr-2"></i>Back to List</a>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-block">
                <div class="card-body">
                    <h5 class="mb-3 font-weight-bold">PO Information</h5>
                    
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-0 bg-transparent">
                            <span class="text-muted">PO Number:</span>
                            <span class="font-weight-bold">{{ $purchase->purchase_no }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-0 bg-transparent">
                            <span class="text-muted">Date:</span>
                            <span class="font-weight-bold">{{ $purchase->purchase_date->format('d/m/Y') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-0 bg-transparent">
                            <span class="text-muted">Supplier:</span>
                            <span class="font-weight-bold">{{ $purchase->supplier->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-0 bg-transparent">
                            <span class="text-muted">Status:</span>
                            <span>
                                @if($purchase->purchase_status === 'received')
                                    <span class="badge badge-success px-3 py-2 rounded-pill">Received</span>
                                @elseif($purchase->purchase_status === 'ordered')
                                    <span class="badge badge-warning px-3 py-2 rounded-pill">Ordered</span>
                                @else
                                    <span class="badge badge-secondary px-3 py-2 rounded-pill">Pending</span>
                                @endif
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card card-block mt-4">
                <div class="card-body">
                    <h5 class="mb-3 font-weight-bold">Financial Summary</h5>
                    
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-0 bg-transparent">
                            <span class="text-muted">Sub Total:</span>
                            <span class="font-weight-bold">Rp. {{ number_format($purchase->sub_total, 0, ',', '.') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-0 bg-transparent">
                            <span class="text-muted">Tax:</span>
                            <span class="font-weight-bold">Rp. {{ number_format($purchase->tax, 0, ',', '.') }}</span>
                        </li>
                        <hr class="w-100">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-0 bg-transparent">
                            <span class="font-weight-bold">Grand Total:</span>
                            <span class="font-weight-bold text-primary" style="font-size: 16px">Rp. {{ number_format($purchase->total, 0, ',', '.') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-0 bg-transparent">
                            <span class="text-muted">Paid:</span>
                            <span class="font-weight-bold text-success">Rp. {{ number_format($purchase->pay_amount, 0, ',', '.') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-0 bg-transparent">
                            <span class="text-muted">Remaining Debt:</span>
                            <span class="font-weight-bold text-danger">Rp. {{ number_format($purchase->due_amount, 0, ',', '.') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card card-block">
                <div class="card-body">
                    <h5 class="mb-3 font-weight-bold">Items List</h5>
                    
                    <div class="table-responsive border-0">
                        <table class="table mb-0 table-borderless text-left">
                            <thead class="text-uppercase text-muted" style="font-size: 11px; background: rgba(0,0,0,0.02)">
                                <tr>
                                    <th>Product Code</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Unit Cost</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchase->details as $detail)
                                    <tr style="border-bottom: 1px solid rgba(0,0,0,0.03)">
                                        <td>{{ $detail->product->code }}</td>
                                        <td class="font-weight-600">{{ $detail->product->name }}</td>
                                        <td>{{ $detail->quantity }}</td>
                                        <td>Rp. {{ number_format($detail->unit_cost, 0, ',', '.') }}</td>
                                        <td class="text-right font-weight-bold">Rp. {{ number_format($detail->total, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
