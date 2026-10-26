@extends('layouts.app')

@section('title', $product->displayName)
@section('page_title', 'Medicine Details')

@section('content')
<div class="row g-4">
    <!-- Main Info Column (8 cols) -->
    <div class="col-lg-8">
        <!-- Header Card -->
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <span class="badge bg-light text-dark border mb-2">{{ $product->category ? $product->category->name : 'General' }}</span>
                    <h3 class="fw-bold mb-1" style="color: #0f766e;">{{ $product->generic_name }}</h3>
                    <h6 class="text-muted">{{ $product->brand_name }} • {{ $product->strength }}</h6>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil me-1"></i> Edit
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>

            <div class="row g-3 text-center border-top pt-3">
                @if(auth()->user()->canAccessFinancials())
                <div class="col-sm-3 border-end">
                    <span class="text-muted small fw-bold text-uppercase">Selling Price</span>
                    <h5 class="fw-bold mb-0 text-success">Tsh {{ number_format($product->selling_price) }}</h5>
                </div>
                <div class="col-sm-3 border-end">
                    <span class="text-muted small fw-bold text-uppercase">Purchase Cost</span>
                    <h5 class="fw-bold mb-0 text-secondary">Tsh {{ number_format($product->purchase_cost) }}</h5>
                </div>
                <div class="col-sm-3 border-end">
                    <span class="text-muted small fw-bold text-uppercase">Sellable Stock</span>
                    <h5 class="fw-bold mb-0 text-emerald">{{ $product->total_stock }} {{ $product->unit ? $product->unit->code : '' }}</h5>
                </div>
                <div class="col-sm-3">
                    <span class="text-muted small fw-bold text-uppercase">Gross Profit / Unit</span>
                    <h5 class="fw-bold mb-0 text-primary">Tsh {{ number_format($product->selling_price - $product->purchase_cost) }}</h5>
                </div>
                @else
                <div class="col-sm-6 border-end">
                    <span class="text-muted small fw-bold text-uppercase">Selling Price</span>
                    <h5 class="fw-bold mb-0 text-success">Tsh {{ number_format($product->selling_price) }}</h5>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small fw-bold text-uppercase">Sellable Stock</span>
                    <h5 class="fw-bold mb-0 text-emerald">{{ $product->total_stock }} {{ $product->unit ? $product->unit->code : '' }}</h5>
                </div>
                @endif
            </div>
        </div>

        <!-- Batches Breakdown Table (FEFO) -->
        <div class="card card-custom overflow-hidden mb-4">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white">
                <h6 class="fw-bold mb-0"><i class="bi bi-calendar2-check text-emerald me-2"></i>Active Inventory Batches (FEFO Order)</h6>
                <span class="badge bg-light text-dark border">First Expiry First Out</span>
            </div>
            <div class="table-responsive">
                <table class="table table-custom align-middle">
                    <thead>
                        <tr>
                            <th>Batch #</th>
                            <th>Expiry Date</th>
                            <th>Received Qty</th>
                            <th>Remaining Qty</th>
                            @if(auth()->user()->canAccessFinancials())
                            <th>Cost</th>
                            @endif
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($product->batches as $batch)
                        @php
                            $isExpired = $batch->isExpired();
                            $days = $batch->days_until_expiry;
                        @endphp
                        <tr>
                            <td class="fw-bold text-dark">{{ $batch->batch_number }}</td>
                            <td>
                                @if($batch->expiry_date)
                                    <div>{{ $batch->expiry_date->format('d/m/Y') }}</div>
                                    @if($isExpired)
                                        <span class="badge badge-fefo-danger">Expired!</span>
                                    @elseif($days !== null && $days <= 90)
                                        <span class="badge badge-fefo-warning">{{ $days }} days left</span>
                                    @else
                                        <span class="badge badge-fefo-safe">Safe</span>
                                    @endif
                                @else
                                    <span class="text-muted">No Expiry (N/A)</span>
                                @endif
                            </td>
                            <td>{{ $batch->quantity_received }}</td>
                            <td class="fw-bold fs-6">{{ $batch->quantity_remaining }}</td>
                            @if(auth()->user()->canAccessFinancials())
                            <td>Tsh {{ number_format($batch->purchase_cost) }}</td>
                            @endif
                            <td class="text-center">
                                @if($batch->status === 'active')
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @elseif($batch->status === 'quarantined')
                                    <span class="badge bg-danger-subtle text-danger">Quarantined</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($batch->status) }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($batch->status === 'active' && $batch->quantity_remaining > 0)
                                    <form action="{{ route('batches.quarantine', $batch->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="action" value="quarantine">
                                        <input type="hidden" name="reason" value="Quarantine triggered from product view">
                                        <button type="submit" class="btn btn-sm btn-outline-warning py-0 px-2" title="Quarantine Batch">
                                            <i class="bi bi-shield-x"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->canAccessFinancials() ? 7 : 6 }}" class="text-center py-4 text-muted">Hakuna batch yoyote iliyosajiliwa bado.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Price History Table -->
        <div class="card card-custom overflow-hidden">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white">
                <h6 class="fw-bold mb-0"><i class="bi bi-clock-history text-emerald me-2"></i>Product Price Change History</h6>
                <span class="small text-muted">Auditable Pricing Ledger</span>
            </div>
            <div class="table-responsive">
                <table class="table table-custom align-middle">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Changed By</th>
                            @if(auth()->user()->canAccessFinancials())
                            <th>Purchase Cost</th>
                            @endif
                            <th>Selling Price</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($product->priceHistories as $ph)
                        <tr>
                            <td class="small">{{ $ph->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $ph->user ? $ph->user->name : 'System' }}</td>
                            @if(auth()->user()->canAccessFinancials())
                            <td>
                                Tsh {{ number_format($ph->old_purchase_cost) }} &rarr; 
                                <strong class="text-dark">Tsh {{ number_format($ph->new_purchase_cost) }}</strong>
                            </td>
                            @endif
                            <td>
                                Tsh {{ number_format($ph->old_selling_price) }} &rarr; 
                                <strong class="text-success">Tsh {{ number_format($ph->new_selling_price) }}</strong>
                            </td>
                            <td class="text-muted small">{{ $ph->reason ?? 'Price adjusted' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->canAccessFinancials() ? 5 : 4 }}" class="text-center py-4 text-muted">No price adjustments recorded yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Specifications Column (4 cols) -->
    <div class="col-lg-4">
        <div class="card card-custom p-4 mb-4">
            <h6 class="fw-bold mb-3 border-bottom pb-2">Technical Specifications</h6>

            <ul class="list-unstyled mb-0 small">
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Item Code:</span>
                    <strong class="text-dark">{{ $product->item_code }}</strong>
                </li>
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Barcode:</span>
                    <span>{{ $product->barcode ?? 'None' }}</span>
                </li>
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Dosage Form:</span>
                    <span>{{ $product->dosage_form ?? 'N/A' }}</span>
                </li>
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Unit of Measure:</span>
                    <span>{{ $product->unit ? $product->unit->name : 'N/A' }} ({{ $product->unit ? $product->unit->code : '' }})</span>
                </li>
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Prescription:</span>
                    <span>
                        @if($product->requires_prescription)
                            <span class="badge bg-danger">Rx Required</span>
                        @else
                            <span class="badge bg-success">Over-the-Counter</span>
                        @endif
                    </span>
                </li>
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Controlled / Restricted:</span>
                    <span>
                        @if($product->is_restricted)
                            <span class="badge bg-dark">Restricted</span>
                        @else
                            <span class="text-muted">Standard</span>
                        @endif
                    </span>
                </li>
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Min Stock Alert:</span>
                    <strong>{{ $product->minimum_stock_alert }} units</strong>
                </li>
                <li class="d-flex justify-content-between py-2">
                    <span class="text-muted">Storage:</span>
                    <span>{{ $product->storage_conditions ?? 'Room temperature' }}</span>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
