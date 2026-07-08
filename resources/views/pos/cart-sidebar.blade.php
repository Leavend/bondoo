<!--
    POS Cart Sidebar Component
    Handles display of cart items, totals, and payment inputs.
-->

<!-- Cart Items Container -->
<div class="cart-items-wrapper position-relative" style="height: 350px; overflow-y: auto; overflow-x: hidden;">
    @if ($productItem->count() > 0)
        <div class="list-group list-group-flush">
            @foreach ($productItem as $item)
                <!-- Individual Cart Item -->
                <div class="list-group-item p-3 border-0 border-bottom">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="font-weight-bold text-dark mb-0">{{ $item->name }}</h6>
                        <span class="font-weight-bolder text-dark">Rp. {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small d-flex flex-column">
                            <span>Rp. {{ number_format($item->price, 0, ',', '.') }}</span>
                            @if (!empty($item->options->secondary_unit))
                                <select class="form-control form-control-sm border-0 bg-light rounded-pill px-2 py-0 mt-1" style="width: auto; height: 22px; font-size: 10px; cursor: pointer;" onchange="updateCart('{{ $item->rowId }}', '{{ $item->qty }}', this.value)">
                                    <option value="primary" {{ ($item->options->unit_type ?? 'primary') === 'primary' ? 'selected' : '' }}>{{ strtoupper($item->options->primary_unit) }}</option>
                                    <option value="secondary" {{ ($item->options->unit_type ?? 'primary') === 'secondary' ? 'selected' : '' }}>{{ strtoupper($item->options->secondary_unit) }}</option>
                                </select>
                            @else
                                <span class="badge badge-light px-2 py-1 rounded-pill mt-1" style="font-size: 9px; width: fit-content;">{{ strtoupper($item->options->primary_unit ?? 'PCS') }}</span>
                            @endif
                        </div>

                        <!-- Quantity Controls -->
                        <div class="d-flex align-items-center bg-light rounded-pill px-1">
                            <button type="button" class="btn btn-sm btn-circle text-danger"
                                onclick="updateCart('{{ $item->rowId }}', {{ $item->qty - 1 }}, '{{ $item->options->unit_type ?? 'primary' }}')">
                                <x-heroicon-o-minus class="w-3 h-3" />
                            </button>
                            <span class="font-weight-bold text-dark mx-2 small"
                                style="min-width: 20px; text-align: center;">
                                {{ $item->qty }}
                            </span>
                            <button type="button" class="btn btn-sm btn-circle text-success"
                                onclick="updateCart('{{ $item->rowId }}', {{ $item->qty + 1 }}, '{{ $item->options->unit_type ?? 'primary' }}')">
                                <x-heroicon-o-plus class="w-3 h-3" />
                            </button>
                        </div>

                        <!-- Remove Button -->
                        <button type="button" class="btn btn-sm text-secondary hover-text-danger p-0"
                            onclick="deleteCart('{{ $item->rowId }}')">
                            <x-heroicon-o-trash class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
            <div class="bg-light rounded-circle p-3 mb-3">
                <x-heroicon-o-shopping-bag class="w-8 h-8 text-secondary" />
            </div>
            <p class="mb-0 font-weight-medium">Cart is empty</p>
            <small>Select products to add</small>
        </div>
    @endif
</div>

<!-- Cart Summary Totals -->
<div class="p-4 bg-white border-top">
    <div class="d-flex justify-content-between mb-2">
        <span class="text-secondary small">Subtotal</span>
        <span class="font-weight-bold">{{ Cart::subtotal() }}</span>
    </div>
    <div class="d-flex justify-content-between mb-3">
        <span class="text-secondary small">Tax</span>
        <span class="font-weight-bold">{{ Cart::tax() }}</span>
    </div>
    <div class="d-flex justify-content-between align-items-center pt-3 border-top border-dashed">
        <span class="h6 font-weight-bold text-dark mb-0">Total</span>
        <span class="h4 font-weight-bolder text-primary mb-0" id="cart-total" data-total="{{ Cart::total(null, null, '') }}">{{ Cart::total() }}</span>
    </div>
</div>

<!-- Payment Section -->
<div class="p-3 bg-white border-top">
    @if (Cart::count() > 0)
        <div class="row">
            <!-- Payment Method -->
            <div class="col-6 pr-1">
                <div class="form-group mb-2">
                    <label class="small font-weight-bold text-muted mb-1">Method</label>
                    <select class="form-control form-control-sm" id="payment_type">
                        <option value="Cash" selected>Cash</option>
                        <option value="Transfer">Transfer</option>
                    </select>
                </div>
            </div>
            <!-- Amount Received Input -->
            <div class="col-6 pl-1">
                <div class="form-group mb-2">
                    <label class="small font-weight-bold text-muted mb-1">Received</label>
                    <input type="number" class="form-control form-control-sm" id="pay_amount" placeholder="0"
                        oninput="calculateChange()" min="0">
                </div>
            </div>
        </div>

        <!-- Change Display -->
        <div class="d-flex justify-content-between align-items-center mb-3 px-2 py-2 bg-light rounded">
            <span class="small font-weight-bold text-muted">Change</span>
            <span class="font-weight-bold text-success" id="change_amount">0.00</span>
        </div>

        <!-- Confirm Payment Button -->
        <button type="button"
            class="btn btn-primary btn-lg btn-block rounded-pill shadow-lg d-flex align-items-center justify-content-center"
            onclick="validateAndShowModal()">
            <span class="mr-2">Confirm Payment</span>
            <x-heroicon-o-arrow-right class="w-5 h-5" />
        </button>
    @else
        <!-- Disabled Button -->
        <button type="button" class="btn btn-light btn-lg btn-block rounded-pill text-muted" disabled>
            Start Sale
        </button>
    @endif
</div>
