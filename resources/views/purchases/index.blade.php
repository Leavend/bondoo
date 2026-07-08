@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between my-3">
                <div>
                    <h4 class="mb-1">Purchase Orders</h4>
                    <p class="mb-0 text-muted">Manage store inventory procurements and supplier payables.</p>
                </div>
                <a href="{{ route('purchases.create') }}" class="btn btn-primary add-list"><i class="las la-plus mr-3"></i>Create PO</a>
            </div>
        </div>

        <div class="col-lg-12">
            @if(session('success'))
                <div class="alert alert-success border-left-3">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger border-left-3">
                    {{ session('error') }}
                </div>
            @endif

            <div class="card card-block">
                <div class="card-body">
                    <form action="{{ route('purchases.index') }}" method="GET" class="mb-4">
                        <div class="input-group">
                            <input type="text" class="form-control border-0 bg-white" placeholder="Search by PO Number or Supplier..." name="search" value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary border-0" type="submit">Search</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive border-0">
                        <table class="table mb-0 table-borderless text-left">
                            <thead class="text-uppercase text-muted" style="font-size: 12px; background: rgba(0,0,0,0.02)">
                                <tr>
                                    <th>PO Number</th>
                                    <th>Supplier</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Debt (Hutang)</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchases as $row)
                                    <tr style="border-bottom: 1px solid rgba(0,0,0,0.03)">
                                        <td class="font-weight-600">{{ $row->purchase_no }}</td>
                                        <td>{{ $row->supplier->name }}</td>
                                        <td>{{ $row->purchase_date->format('d/m/Y') }}</td>
                                        <td>
                                            @if($row->purchase_status === 'received')
                                                <span class="badge badge-success px-3 py-2 rounded-pill">Received</span>
                                            @elseif($row->purchase_status === 'ordered')
                                                <span class="badge badge-warning px-3 py-2 rounded-pill">Ordered</span>
                                            @else
                                                <span class="badge badge-secondary px-3 py-2 rounded-pill">Pending</span>
                                            @endif
                                        </td>
                                        <td>Rp. {{ number_format($row->total, 0, ',', '.') }}</td>
                                        <td>Rp. {{ number_format($row->pay_amount, 0, ',', '.') }}</td>
                                        <td class="{{ $row->due_amount > 0 ? 'text-danger font-weight-bold' : '' }}">
                                            Rp. {{ number_format($row->due_amount, 0, ',', '.') }}
                                        </td>
                                        <td class="text-right">
                                            <div class="d-flex justify-content-end align-items-center">
                                                <a href="{{ route('purchases.show', $row->id) }}" class="btn btn-sm btn-light mr-2 border-0" title="Details">
                                                    View
                                                </a>

                                                @if($row->purchase_status !== 'received')
                                                    <form action="{{ route('purchases.updateStatus', $row->id) }}" method="POST" class="d-inline mr-2">
                                                        @csrf
                                                        <input type="hidden" name="status" value="received">
                                                        <button type="submit" class="btn btn-sm btn-success border-0">
                                                            Mark Received
                                                        </button>
                                                    </form>
                                                @endif

                                                @if($row->due_amount > 0)
                                                    <button class="btn btn-sm btn-primary border-0" data-toggle="modal" data-target="#payModal{{ $row->id }}">
                                                        Pay Debt
                                                    </button>

                                                    <!-- Pay Debt Modal -->
                                                    <div class="modal fade" id="payModal{{ $row->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content border-0" style="border-radius: 12px">
                                                                <form action="{{ route('purchases.payDue') }}" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="id" value="{{ $row->id }}">
                                                                    <div class="modal-header border-0 bg-light">
                                                                        <h5 class="modal-title font-weight-bold">Pay Supplier Debt</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body text-left">
                                                                        <p class="text-muted">PO: <strong>{{ $row->purchase_no }}</strong></p>
                                                                        <p class="text-muted">Remaining Debt: <strong class="text-danger">Rp. {{ number_format($row->due_amount, 0, ',', '.') }}</strong></p>
                                                                        
                                                                        <div class="form-group mt-3">
                                                                            <label class="font-weight-600">Payment Amount (Rp)</label>
                                                                            <input type="number" class="form-control" name="pay_amount" max="{{ $row->due_amount }}" min="1" required placeholder="Enter amount to pay...">
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer border-0">
                                                                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                                                        <button type="submit" class="btn btn-primary">Submit Payment</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">No purchases found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        {{ $purchases->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
