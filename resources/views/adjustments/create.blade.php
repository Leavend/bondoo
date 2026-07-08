@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between my-3">
                <div>
                    <h4 class="mb-1">Record Stock Opname</h4>
                    <p class="mb-0 text-muted">Log physical count audit results to reconcile system inventory.</p>
                </div>
                <a href="{{ route('adjustments.index') }}" class="btn btn-light border-0"><i class="las la-arrow-left mr-2"></i>Back to List</a>
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
                    <form action="{{ route('adjustments.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label class="font-weight-600">Select Product *</label>
                            <select class="form-control" name="product_id" id="productId" required>
                                <option value="">Choose Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-stock="{{ $product->stock }}">
                                        {{ $product->name }} ({{ $product->code }}) - Current Stock: {{ $product->stock }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-600">System Stock</label>
                                    <input type="number" class="form-control" id="systemStock" readonly value="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-600">Physical Stock Counted *</label>
                                    <input type="number" class="form-control" id="physicalStock" min="0" required placeholder="Enter counted amount...">
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3 p-3 bg-light rounded" id="calcSection" style="display: none; border-radius: 8px">
                            <h6 class="font-weight-bold mb-2">Correction Calculation</h6>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Correction Type:</span>
                                <strong id="correctionType">-</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Adjustment Quantity:</span>
                                <strong id="adjustmentQty">-</strong>
                            </div>
                            
                            <!-- Hidden inputs for backend submission -->
                            <input type="hidden" name="type" id="hiddenType">
                            <input type="hidden" name="quantity" id="hiddenQty">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-600">Reason / Remark *</label>
                            <textarea class="form-control" name="reason" rows="3" required placeholder="Describe why this correction is made (e.g. Broken items, counting error, etc.)..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block mt-4">Save Stock Adjustment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('productId');
    const systemStockInput = document.getElementById('systemStock');
    const physicalStockInput = document.getElementById('physicalStock');
    const calcSection = document.getElementById('calcSection');
    const correctionTypeSpan = document.getElementById('correctionType');
    const adjustmentQtySpan = document.getElementById('adjustmentQty');
    
    const hiddenType = document.getElementById('hiddenType');
    const hiddenQty = document.getElementById('hiddenQty');

    function calculateAdjustment() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        if (!selectedOption || !selectedOption.value) {
            systemStockInput.value = 0;
            calcSection.style.display = 'none';
            return;
        }

        const sysStock = parseInt(selectedOption.dataset.stock) || 0;
        systemStockInput.value = sysStock;

        const physStockVal = physicalStockInput.value;
        if (physStockVal === '') {
            calcSection.style.display = 'none';
            return;
        }

        const physStock = parseInt(physStockVal) || 0;
        const diff = physStock - sysStock;

        calcSection.style.display = 'block';

        if (diff > 0) {
            correctionTypeSpan.textContent = 'Addition (Stok Masuk)';
            correctionTypeSpan.className = 'text-success';
            adjustmentQtySpan.textContent = '+' + diff;
            
            hiddenType.value = 'addition';
            hiddenQty.value = diff;
        } else if (diff < 0) {
            correctionTypeSpan.textContent = 'Subtraction (Stok Keluar)';
            correctionTypeSpan.className = 'text-danger';
            adjustmentQtySpan.textContent = diff;

            hiddenType.value = 'subtraction';
            hiddenQty.value = Math.abs(diff);
        } else {
            correctionTypeSpan.textContent = 'No Discrepancy';
            correctionTypeSpan.className = 'text-muted';
            adjustmentQtySpan.textContent = '0';

            hiddenType.value = 'addition';
            hiddenQty.value = 0;
        }
    }

    productSelect.addEventListener('change', calculateAdjustment);
    physicalStockInput.addEventListener('input', calculateAdjustment);
});
</script>
@endsection
