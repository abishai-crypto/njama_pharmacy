@extends('layouts.app')

@section('title', 'Medicine Catalogue')
@section('page_title', __('app.medicines'))

@section('content')
<div class="card card-custom p-3 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-capsule text-emerald me-2"></i>Orodha ya Dawa & Vifaa Tiba (Catalogue)</h5>
            <small class="text-muted">Jumla ya aina za dawa zilizosajiliwa: <strong>{{ $products->total() }}</strong></small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('products.bulk-prices') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-tags-fill text-warning"></i> Bulk Selling Prices
            </a>
            <a href="{{ route('products.create') }}" class="btn btn-emerald btn-sm d-flex align-items-center gap-1 shadow-sm">
                <i class="bi bi-plus-circle-fill"></i> Sajili Dawa Mpya
            </a>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card card-custom p-3 mb-4">
    <form action="{{ route('products.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Tafuta kwa jina la dawa, brand, au barcode..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">-- Makundi Yote (Categories) --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <div class="form-check form-check-inline small">
                <input class="form-check-input" type="checkbox" name="prescription_only" id="rxCheck" value="1" {{ request('prescription_only') ? 'checked' : '' }} onchange="this.form.submit()">
                <label class="form-check-label" for="rxCheck">Rx Only</label>
            </div>
        </div>
        <div class="col-md-2 text-end">
            <button type="submit" class="btn btn-sm btn-dark w-100">Filter</button>
        </div>
    </form>
</div>

<!-- Products Table -->
<div class="card card-custom overflow-hidden">
    <div class="table-responsive">
        <table class="table table-custom align-middle">
            <thead>
                <tr>
                    <th style="width: 70px;">Code</th>
                    <th>Jina la Dawa (Generic & Brand)</th>
                    <th>Kundi (Category)</th>
                    <th class="text-center">Kipimo</th>
                    @if(auth()->user()->canAccessFinancials())
                    <th class="text-end">Bei ya Kununua</th>
                    @endif
                    <th class="text-end">Bei ya Kuuzia</th>
                    <th class="text-center">Stoo Iliyopo</th>
                    <th class="text-center">Hali</th>
                    <th class="text-center" style="width: 120px;">Kitendo</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td class="fw-bold text-muted small">{{ $product->item_code }}</td>
                    <td>
                        <div class="fw-bold text-dark">
                            <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none text-dark">
                                {{ $product->generic_name }}
                            </a>
                            @if($product->requires_prescription)
                                <span class="badge bg-danger-subtle text-danger ms-1" style="font-size: 0.65rem;">Rx</span>
                            @endif
                            @if($product->is_restricted)
                                <span class="badge bg-dark text-white ms-1" style="font-size: 0.65rem;">Restricted</span>
                            @endif
                        </div>
                        <small class="text-muted">{{ $product->brand_name }} {{ $product->strength ? '• ' . $product->strength : '' }}</small>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $product->category ? $product->category->name : 'N/A' }}</span>
                    </td>
                    <td class="text-center">{{ $product->unit ? $product->unit->code : 'PCS' }}</td>
                    @if(auth()->user()->canAccessFinancials())
                    <td class="text-end text-secondary">Tsh {{ number_format($product->purchase_cost) }}</td>
                    @endif
                    <td class="text-end fw-bold text-emerald">Tsh {{ number_format($product->selling_price) }}</td>
                    <td class="text-center">
                        @php $stock = $product->total_stock; @endphp
                        @if($stock <= 0)
                            <span class="badge bg-danger">0 Out of Stock</span>
                        @elseif($stock <= $product->minimum_stock_alert)
                            <span class="badge bg-warning text-dark">{{ $stock }} (Low)</span>
                        @else
                            <span class="badge bg-success-subtle text-success border border-success-subtle">{{ $stock }} In Stock</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge bg-success-subtle text-success">Active</span>
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-secondary py-1" title="View Details">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-outline-primary py-1" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ auth()->user()->canAccessFinancials() ? 9 : 8 }}" class="text-center py-5 text-muted">
                        Hakuna dawa iliyopatikana kwa vigezo hivyo.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-3 border-top bg-white d-flex justify-content-between align-items-center">
        <small class="text-muted">Inaonyesha {{ $products->firstItem() ?? 0 }} hadi {{ $products->lastItem() ?? 0 }} kati ya {{ $products->total() }}</small>
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
