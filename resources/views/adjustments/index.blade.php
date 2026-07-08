@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between my-3">
                <div>
                    <h4 class="mb-1">Stock Opname Logs</h4>
                    <p class="mb-0 text-muted">Audits and corrections for physical vs system stock discrepancies.</p>
                </div>
                <a href="{{ route('adjustments.create') }}" class="btn btn-primary add-list"><i class="las la-plus mr-3"></i>Record Opname</a>
            </div>
        </div>

        <div class="col-lg-12">
            @if(session('success'))
                <div class="alert alert-success border-left-3">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card card-block">
                <div class="card-body">
                    <form action="{{ route('adjustments.index') }}" method="GET" class="mb-4">
                        <div class="input-group">
                            <input type="text" class="form-control border-0 bg-white" placeholder="Search by Product Name or Code..." name="search" value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary border-0" type="submit">Search</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive border-0">
                        <table class="table mb-0 table-borderless text-left">
                            <thead class="text-uppercase text-muted" style="font-size: 12px; background: rgba(0,0,0,0.02)">
                                <tr>
                                    <th>Date</th>
                                    <th>Product Code</th>
                                    <th>Product Name</th>
                                    <th>Correction Type</th>
                                    <th>Discrepancy Qty</th>
                                    <th>Reason / Notes</th>
                                    <th class="text-right">Auditor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($adjustments as $row)
                                    <tr style="border-bottom: 1px solid rgba(0,0,0,0.03)">
                                        <td>{{ $row->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $row->product->code }}</td>
                                        <td class="font-weight-600">{{ $row->product->name }}</td>
                                        <td>
                                            @if($row->type === 'addition')
                                                <span class="badge badge-success px-3 py-2 rounded-pill">+ Stock In</span>
                                            @else
                                                <span class="badge badge-danger px-3 py-2 rounded-pill">- Stock Out</span>
                                            @endif
                                        </td>
                                        <td class="font-weight-bold">{{ $row->quantity }}</td>
                                        <td>{{ $row->reason }}</td>
                                        <td class="text-right font-weight-500">{{ $row->user->name ?? 'System' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No adjustments logged yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        {{ $adjustments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
