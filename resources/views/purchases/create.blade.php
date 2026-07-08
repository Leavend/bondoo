@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between my-3">
                <div>
                    <h4 class="mb-1">Create Purchase Order</h4>
                    <p class="mb-0 text-muted">Register new inventory arrivals and log purchase transactions.</p>
                </div>
                <a href="{{ route('purchases.index') }}" class="btn btn-light border-0"><i class="las la-arrow-left mr-2"></i>Back to List</a>
            </div>
        </div>

        <div class="col-lg-12">
            @if(session('error'))
                <div class="alert alert-danger border-left-3">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm">
                @csrf
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card card-block">
                            <div class="card-body">
                                <h5 class="mb-3 font-weight-bold">PO Settings</h5>

                                <div class="form-group">
                                    <label class="font-weight-600">Supplier *</label>
                                    <select class="form-control" name="supplier_id" required>
                                        <option value="">Select Supplier</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-600">Purchase Date *</label>
                                    <input type="date" class="form-control" name="purchase_date" value="{{ date('Y-m-d') }}" required>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-600">Status *</label>
                                    <select class="form-control" name="purchase_status" required>
                                        <option value="received">Received (Increments Stock)</option>
                                        <option value="ordered">Ordered</option>
                                        <option value="pending" selected>Pending</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-600">Initial Pay Amount (Rp) *</label>
                                    <input type="number" class="form-control" name="pay_amount" id="payAmount" value="0" min="0" required>
                                </div>

                                <div class="mt-4 p-3 bg-light rounded" style="border-radius: 8px">
                                    <h6 class="font-weight-bold mb-2">Summary</h6>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">Sub Total:</span>
                                        <strong id="summarySubTotal">Rp. 0</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">Tax:</span>
                                        <strong>Rp. 0</strong>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <span class="font-weight-bold">Grand Total:</span>
                                        <strong class="text-primary" id="summaryGrandTotal" style="font-size: 18px">Rp. 0</strong>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block mt-4">Save Purchase Order</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card card-block">
                            <div class="card-body">
                                <h5 class="mb-3 font-weight-bold">Order Items</h5>

                                <table class="table table-borderless" id="itemsTable">
                                    <thead class="text-uppercase text-muted" style="font-size: 11px">
                                        <tr>
                                            <th style="width: 40%">Product</th>
                                            <th style="width: 20%">Quantity</th>
                                            <th style="width: 15%">Unit Type</th>
                                            <th style="width: 20%">Unit Cost</th>
                                            <th class="text-right" style="width: 5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsContainer">
                                        <!-- Row item will be added here -->
                                    </tbody>
                                </table>

                                <button type="button" class="btn btn-sm btn-light border-0 mt-3" id="addItemButton">
                                    <i class="las la-plus mr-1"></i>Add Item Row
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Template Row for dynamic items -->
<template id="itemRowTemplate">
    <tr class="item-row">
        <td>
            <select class="form-control select-product" name="items[__INDEX__][product_id]" required>
                <option value="">Select Product</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" 
                            data-buying-price="{{ $product->buying_price }}"
                            data-primary-unit="{{ $product->primary_unit }}"
                            data-secondary-unit="{{ $product->secondary_unit }}">
                        {{ $product->name }} ({{ $product->code }})
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" class="form-control input-qty" name="items[__INDEX__][quantity]" min="1" value="1" required>
        </td>
        <td>
            <select class="form-control select-unit-type" name="items[__INDEX__][unit_type]" required>
                <option value="primary">Base Unit</option>
                <option value="secondary">Secondary Unit</option>
            </select>
        </td>
        <td>
            <input type="number" class="form-control input-cost" name="items[__INDEX__][unit_cost]" min="0" value="0" required>
        </td>
        <td class="text-right">
            <button type="button" class="btn btn-sm btn-danger border-0 remove-row-button">
                &times;
            </button>
        </td>
    </tr>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const itemsContainer = document.getElementById('itemsContainer');
    const addItemButton = document.getElementById('addItemButton');
    const template = document.getElementById('itemRowTemplate').innerHTML;
    let rowIndex = 0;

    function addRow() {
        const rowHtml = template.replace(/__INDEX__/g, rowIndex);
        const tempDiv = document.createElement('tbody');
        tempDiv.innerHTML = rowHtml;
        const rowElement = tempDiv.querySelector('tr');
        itemsContainer.appendChild(rowElement);

        // Bind events
        const selectProduct = rowElement.querySelector('.select-product');
        const selectUnitType = rowElement.querySelector('.select-unit-type');
        const inputCost = rowElement.querySelector('.input-cost');
        const inputQty = rowElement.querySelector('.input-qty');
        const removeBtn = rowElement.querySelector('.remove-row-button');

        selectProduct.addEventListener('change', function() {
            const selectedOption = selectProduct.options[selectProduct.selectedIndex];
            if (selectedOption && selectedOption.value) {
                inputCost.value = selectedOption.dataset.buyingPrice || 0;
                
                // Update unit selection labels
                const primaryUnit = selectedOption.dataset.primaryUnit || 'pcs';
                const secondaryUnit = selectedOption.dataset.secondaryUnit || '';

                selectUnitType.options[0].textContent = primaryUnit.toUpperCase();
                if (secondaryUnit) {
                    selectUnitType.options[1].style.display = 'block';
                    selectUnitType.options[1].textContent = secondaryUnit.toUpperCase();
                } else {
                    selectUnitType.options[1].style.display = 'none';
                    selectUnitType.value = 'primary';
                }
            }
            calculateTotals();
        });

        selectUnitType.addEventListener('change', calculateTotals);
        inputCost.addEventListener('input', calculateTotals);
        inputQty.addEventListener('input', calculateTotals);

        removeBtn.addEventListener('click', function() {
            rowElement.remove();
            calculateTotals();
        });

        rowIndex++;
        calculateTotals();
    }

    function calculateTotals() {
        let subtotal = 0;
        const rows = itemsContainer.querySelectorAll('.item-row');
        
        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.input-qty').value) || 0;
            const cost = parseFloat(row.querySelector('.input-cost').value) || 0;
            subtotal += qty * cost;
        });

        document.getElementById('summarySubTotal').textContent = 'Rp. ' + subtotal.toLocaleString('id-ID');
        document.getElementById('summaryGrandTotal').textContent = 'Rp. ' + subtotal.toLocaleString('id-ID');
    }

    addItemButton.addEventListener('click', addRow);

    // Add first row automatically
    addRow();
});
</script>
@endsection
