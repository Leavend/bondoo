@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between my-3">
                <div>
                    <h4 class="mb-1">Product Returns</h4>
                    <p class="mb-0 text-muted">Track returns from customer sales or supplier purchases.</p>
                </div>
                <a href="{{ route('returns.create') }}" class="btn btn-primary add-list"><i class="las la-plus mr-3"></i>Log Return</a>
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
                    <form action="{{ route('returns.index') }}" method="GET" class="mb-4">
                        <div class="input-group">
                            <input type="text" class="form-control border-0 bg-white" placeholder="Search by Reference Invoice or PO..." name="search" value="{{ request('search') }}">
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
                                    <th>Return Type</th>
                                    <th>Ref Number</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Reason</th>
                                    <th class="text-right">Refund Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($returns as $row)
                                    <tr style="border-bottom: 1px solid rgba(0,0,0,0.03)">
                                        <td>{{ $row->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($row->type === 'sales')
                                                <span class="badge badge-success px-3 py-2 rounded-pill">Sales Return</span>
                                            @else
                                                <span class="badge badge-warning px-3 py-2 rounded-pill">Purchase Return</span>
                                            @endif
                                        </td>
                                        <td class="font-weight-600">{{ $row->reference_no }}</td>
                                        <td>{{ $row->product->name }}</td>
                                        <td>{{ $row->quantity }}</td>
                                        <td>{{ $row->reason }}</td>
                                        <td class="text-right font-weight-bold">
                                            Rp. {{ number_format($row->refund_amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No return records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        {{ $returns->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
