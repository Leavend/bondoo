@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between my-3">
                <div>
                    <h4 class="mb-1">Log Product Return</h4>
                    <p class="mb-0 text-muted">Record sales returns or purchase returns to reconcile inventory and funds.</p>
                </div>
                <a href="{{ route('returns.index') }}" class="btn btn-light border-0"><i class="las la-arrow-left mr-2"></i>Back to List</a>
            </div>
        </div>

        <div class="col-lg-6 mx-auto">
            @if(session('error'))
                <div class="alert alert-danger border-left-3">
                    {{ session('error') }}
                </div>
            @endif

            <div class="card card-block">
                <div class="card-body">
                    <form action="{{ route('returns.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label class="font-weight-600">Return Type *</label>
                            <select class="form-control" name="type" required>
                                <option value="sales" selected>Sales Return (From Customer - Stock In)</option>
                                <option value="purchase">Purchase Return (To Supplier - Stock Out)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-600">Reference Document Number *</label>
                            <input type="text" class="form-control" name="reference_no" required placeholder="Enter Invoice Number or PO Number (e.g. INV-00001)...">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-600">Product *</label>
                            <select class="form-control" name="product_id" required>
                                <option value="">Choose Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">
                                        {{ $product->name }} ({{ $product->code }}) - Stock: {{ $product->stock }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-600">Quantity to Return *</label>
                                    <input type="number" class="form-control" name="quantity" min="1" value="1" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-600">Refund Amount (Rp) *</label>
                                    <input type="number" class="form-control" name="refund_amount" min="0" value="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-600">Reason for Return *</label>
                            <textarea class="form-control" name="reason" rows="3" required placeholder="Reason (e.g., damaged on arrival, wrong item variant, customer refund)..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block mt-4">Record Return</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
