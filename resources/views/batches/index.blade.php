@extends('layouts.app')

@section('title', 'Batches & Expiry (FEFO)')
@section('page_title', __('app.batches'))

@section('content')
<div class="card card-custom p-3 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-calendar2-check text-emerald me-2"></i>Usimamizi wa Mabatchi & Tarehe za Kuisha (FEFO)</h5>
            <small class="text-muted">Mfumo unatumia First Expiry, First Out (FEFO) ili kuzuia dawa zisikae hadi zikaisha muda.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reports.expiry') }}" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> Ripoti Kamili ya Expiry
            </a>
            <a href="{{ route('batches.movements') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-journal-text me-1"></i> Stock Ledger
            </a>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card card-custom p-3 mb-4">
    <form action="{{ route('batches.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-4">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Tafuta namba ya batch au jina la dawa..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="expiry" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">-- Tarehe za Kuisha (Zote) --</option>
                <option value="expired" {{ request('expiry') == 'expired' ? 'selected' : '' }}>🔴 Zilizokwisha Muda (Expired)</option>
                <option value="30" {{ request('expiry') == '30' ? 'selected' : '' }}>🟠 Zinaisha Ndani ya Siku 30</option>
                <option value="60" {{ request('expiry') == '60' ? 'selected' : '' }}>🟡 Zinaisha Ndani ya Siku 60</option>
                <option value="90" {{ request('expiry') == '90' ? 'selected' : '' }}>🔵 Zinaisha Ndani ya Siku 90</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="all">-- Hali Zote (All Statuses) --</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active (Inatumika)</option>
                <option value="quarantined" {{ request('status') == 'quarantined' ? 'selected' : '' }}>Quarantined (Zilizotengwa)</option>
                <option value="depleted" {{ request('status') == 'depleted' ? 'selected' : '' }}>Depleted (Zilizokwisha)</option>
            </select>
        </div>
        <div class="col-md-2 text-end">
            <button type="submit" class="btn btn-sm btn-dark w-100">Filter</button>
        </div>
    </form>
</div>

<!-- Batches Table -->
<div class="card card-custom overflow-hidden">
    <div class="table-responsive">
        <table class="table table-custom align-middle">
            <thead>
                <tr>
                    <th>Batch #</th>
                    <th>Jina la Dawa</th>
                    <th>Tarehe ya Kuisha (Expiry)</th>
                    <th>Hali ya Expiry (FEFO Alert)</th>
                    <th class="text-center">Zilizoingia</th>
                    <th class="text-center">Zilizobaki</th>
                    <th class="text-end">Bei ya Ununuzi</th>
                    <th class="text-center">Hali</th>
                    <th class="text-center" style="width: 140px;">Vitendo</th>
                </tr>
            </thead>
            <tbody>
                @forelse($batches as $batch)
                @php
                    $isExpired = $batch->isExpired();
                    $days = $batch->days_until_expiry;
                @endphp
                <tr>
                    <td class="fw-bold text-dark">{{ $batch->batch_number }}</td>
                    <td>
                        <div class="fw-bold text-dark">{{ $batch->product ? $batch->product->generic_name : 'N/A' }}</div>
                        <small class="text-muted">{{ $batch->product ? $batch->product->brand_name : '' }}</small>
                    </td>
                    <td>
                        {{ $batch->expiry_date ? $batch->expiry_date->format('d/m/Y') : 'N/A (Haina Expiry)' }}
                    </td>
                    <td>
                        @if($batch->expiry_date)
                            @if($isExpired)
                                <span class="badge badge-fefo-danger">Imeisha Muda! (Quarantine)</span>
                            @elseif($days !== null && $days <= 30)
                                <span class="badge badge-fefo-warning">Haraka! Zimebaki siku {{ $days }}</span>
                            @elseif($days !== null && $days <= 90)
                                <span class="badge badge-fefo-warning">Zimebaki siku {{ $days }}</span>
                            @else
                                <span class="badge badge-fefo-safe">Salama ({{ $days }} days)</span>
                            @endif
                        @else
                            <span class="badge bg-light text-muted border">Vifaa Tiba / N/A</span>
                        @endif
                    </td>
                    <td class="text-center text-muted">{{ $batch->quantity_received }}</td>
                    <td class="text-center">
                        <strong class="fs-6 {{ $batch->quantity_remaining <= 0 ? 'text-muted' : ($isExpired ? 'text-danger' : 'text-dark') }}">
                            {{ $batch->quantity_remaining }}
                        </strong>
                    </td>
                    <td class="text-end text-secondary">Tsh {{ number_format($batch->purchase_cost) }}</td>
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
                        <div class="btn-group btn-group-sm">
                            @if($batch->status === 'active' && $batch->quantity_remaining > 0)
                                <form action="{{ route('batches.quarantine', $batch->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Je, una uhakika unataka kuitenga batch hii kwenye karantini?');">
                                    @csrf
                                    <input type="hidden" name="action" value="quarantine">
                                    <input type="hidden" name="reason" value="Quarantine by pharmacy officer">
                                    <button type="submit" class="btn btn-outline-warning py-1" title="Quarantine">
                                        <i class="bi bi-shield-x"></i>
                                    </button>
                                </form>
                            @elseif($batch->status === 'quarantined')
                                <form action="{{ route('batches.quarantine', $batch->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Je, una uhakika unataka kuirejesha stoo batch hii?');">
                                    @csrf
                                    <input type="hidden" name="action" value="release">
                                    <input type="hidden" name="reason" value="Released back to active inventory">
                                    <button type="submit" class="btn btn-outline-success py-1" title="Release to Active">
                                        <i class="bi bi-shield-check"></i>
                                    </button>
                                </form>
                            @endif

                            @if($batch->quantity_remaining > 0)
                                <button type="button" class="btn btn-outline-danger py-1" title="Write Off Stock" data-bs-toggle="modal" data-bs-target="#writeOffModal{{ $batch->id }}">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            @endif
                        </div>

                        <!-- Write Off Modal -->
                        <div class="modal fade" id="writeOffModal{{ $batch->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered text-start">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h6 class="modal-title fw-bold text-white"><i class="bi bi-exclamation-triangle me-2"></i>Write-Off Stock: Batch #{{ $batch->batch_number }}</h6>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('batches.write-off', $batch->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <p class="small text-muted mb-3">Dawa hii itaondolewa kwenye stoo na hasara ya thamani yake itarekodiwa kwenye ripoti ya fedha.</p>

                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Idadi ya Kuondoa (Remaining: {{ $batch->quantity_remaining }})</label>
                                                <input type="number" name="quantity" class="form-control" max="{{ $batch->quantity_remaining }}" min="1" value="{{ $batch->quantity_remaining }}" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Sababu ya Kuondoa (Reason) <span class="text-danger">*</span></label>
                                                <select name="reason" class="form-select" required>
                                                    <option value="Dawa imeisha muda (Expired)">Dawa imeisha muda (Expired)</option>
                                                    <option value="Imeharibika / Imepasuka (Damaged)">Imeharibika / Imepasuka (Damaged)</option>
                                                    <option value="Imetengwa na TMDA/Pharmacy Council (Recalled)">Imetengwa na TMDA/Pharmacy Council (Recalled)</option>
                                                    <option value="Upotevu / Hesabu ya Stoo (Physical Stock Loss)">Upotevu / Hesabu ya Stoo (Physical Stock Loss)</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Maelezo ya Ziada (Notes)</label>
                                                <textarea name="notes" class="form-control" rows="2" placeholder="Maelezo zaidi..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Ghairi</button>
                                            <button type="submit" class="btn btn-danger btn-sm fw-bold">Thibitisha Write-Off</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-5 text-muted">Hakuna batch iliyopatikana kwa vigezo hivyo.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-3 border-top bg-white d-flex justify-content-between align-items-center">
        <small class="text-muted">Inaonyesha {{ $batches->firstItem() ?? 0 }} hadi {{ $batches->lastItem() ?? 0 }} kati ya {{ $batches->total() }}</small>
        {{ $batches->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
