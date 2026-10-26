@extends('layouts.app')

@section('title', __('app.suppliers') . ' — NJAMA PHARMACY')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-truck text-teal me-2"></i>{{ __('app.suppliers') }}</h3>
            <p class="text-muted small mb-0">Usimamizi wa wasambazaji wa dawa, ankara, na malipo ya jumla</p>
        </div>
        @if(in_array(auth()->user()->role, ['admin', 'pharmacist']))
        <button class="btn btn-teal shadow-sm" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
            <i class="bi bi-plus-circle me-1"></i> Sajili Msambazaji Mpya
        </button>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card card-custom border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">Jina la Kampuni</th>
                            <th>Mtu wa Mawasiliano</th>
                            <th>Simu & Barua Pepe</th>
                            <th>TIN / VRN</th>
                            <th>Masharti ya Malipo</th>
                            <th class="text-center">Jumla ya Ankara</th>
                            <th>Hali</th>
                            <th class="text-end pe-4">Vitendo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark fs-6">{{ $supplier->name }}</div>
                                <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $supplier->address ?? 'Haikuwekwa' }}</small>
                            </td>
                            <td>{{ $supplier->contact_person ?? '—' }}</td>
                            <td>
                                <div><i class="bi bi-telephone text-teal me-1"></i>{{ $supplier->phone ?? '—' }}</div>
                                @if($supplier->email)
                                <small class="text-muted"><i class="bi bi-envelope me-1"></i>{{ $supplier->email }}</small>
                                @endif
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $supplier->tin_number ?? 'Bila TIN' }}</span></td>
                            <td><span class="badge bg-info-subtle text-info border border-info-subtle">{{ $supplier->payment_terms ?? 'Cash / Makubaliano' }}</span></td>
                            <td class="text-center">
                                <span class="badge bg-teal rounded-pill px-3">{{ $supplier->purchases_count }}</span>
                            </td>
                            <td>
                                @if($supplier->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Hai (Active)</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border">Imesitishwa</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-sm btn-outline-teal me-1" title="Tazama Taarifa na Ankara">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(in_array(auth()->user()->role, ['admin', 'pharmacist']))
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editSupplierModal{{ $supplier->id }}" title="Hariri">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @endif
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editSupplierModal{{ $supplier->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-teal text-white">
                                            <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Hariri Msambazaji: {{ $supplier->name }}</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Jina la Kampuni / Msambazaji <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" value="{{ $supplier->name }}" required>
                                            </div>
                                            <div class="row g-2 mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Mtu wa Mawasiliano</label>
                                                    <input type="text" name="contact_person" class="form-control" value="{{ $supplier->contact_person }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Namba ya Simu</label>
                                                    <input type="text" name="phone" class="form-control" value="{{ $supplier->phone }}">
                                                </div>
                                            </div>
                                            <div class="row g-2 mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Barua Pepe</label>
                                                    <input type="email" name="email" class="form-control" value="{{ $supplier->email }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Namba ya TIN</label>
                                                    <input type="text" name="tin_number" class="form-control" value="{{ $supplier->tin_number }}">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Masharti ya Malipo (Payment Terms)</label>
                                                <input type="text" name="payment_terms" class="form-control" value="{{ $supplier->payment_terms }}" placeholder="Mfano: Net 30, Cash on Delivery">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Anwani / Mahali Ilipo</label>
                                                <textarea name="address" class="form-control" rows="2">{{ $supplier->address }}</textarea>
                                            </div>
                                            <div class="form-check form-switch mt-2">
                                                <input class="form-check-input" type="checkbox" name="is_active" id="active{{ $supplier->id }}" {{ $supplier->is_active ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold" for="active{{ $supplier->id }}">Msambazaji Yupo Hai (Active)</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ghairi</button>
                                            <button type="submit" class="btn btn-teal"><i class="bi bi-save me-1"></i>Hifadhi Mabadiliko</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-truck fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Hakuna msambazaji aliyepatikana.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Supplier Modal -->
<div class="modal fade" id="addSupplierModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-teal text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-truck me-2"></i>Sajili Msambazaji Mpya</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jina la Kampuni / Msambazaji <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Mfano: TONIC Pharmaceuticals" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mtu wa Mawasiliano</label>
                            <input type="text" name="contact_person" class="form-control" placeholder="Mfano: Juma Ali">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Namba ya Simu</label>
                            <input type="text" name="phone" class="form-control" placeholder="07XXXXXXXX">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Barua Pepe</label>
                            <input type="email" name="email" class="form-control" placeholder="info@supplier.co.tz">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Namba ya TIN</label>
                            <input type="text" name="tin_number" class="form-control" placeholder="123-456-789">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Masharti ya Malipo</label>
                        <input type="text" name="payment_terms" class="form-control" placeholder="Mfano: Cash, Net 14 Days, Cheque">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Anwani / Mahali Ilipo</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Kariakoo, Dar es Salaam..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ghairi</button>
                    <button type="submit" class="btn btn-teal"><i class="bi bi-check-circle me-1"></i>Hifadhi Msambazaji</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
