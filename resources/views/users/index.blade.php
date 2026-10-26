@extends('layouts.app')

@section('title', __('app.users') . ' — NJAMA PHARMACY')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-people text-teal me-2"></i>{{ __('app.users') }}</h3>
            <p class="text-muted small mb-0">Usimamizi wa akaunti za wafanyakazi na mamlaka za mfumo (Role-Based Access Control)</p>
        </div>
        <button class="btn btn-teal shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-person-plus-fill me-1"></i> Sajili Mfanyakazi Mpya
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <h6 class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Hitilafu:</h6>
            <ul class="mb-0 small ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Staff List Table -->
    <div class="card card-custom border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">Jina la Mfanyakazi</th>
                            <th>Jina la Kuingilia (Username)</th>
                            <th>Mawasiliano</th>
                            <th>Nafasi / Wajibu (Role)</th>
                            <th>Kuingia Mwisho</th>
                            <th>Hali</th>
                            <th class="text-end pe-4">Kitendo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-teal text-white d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; font-size: 1.1rem;">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $u->name }}</div>
                                        <small class="text-muted">{{ $u->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="fw-semibold text-dark font-monospace">{{ $u->username }}</td>
                            <td>
                                <div><i class="bi bi-telephone text-teal me-1"></i>{{ $u->phone ?? '—' }}</div>
                            </td>
                            <td>
                                @if($u->role === 'admin')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1">
                                        <i class="bi bi-shield-shaded me-1"></i> Msimamizi Mkuu (Admin)
                                    </span>
                                @elseif($u->role === 'pharmacist')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1">
                                        <i class="bi bi-heart-pulse me-1"></i> Mfamasia (Pharmacist)
                                    </span>
                                @elseif($u->role === 'cashier')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1">
                                        <i class="bi bi-cash-stack me-1"></i> Muuzaji (Cashier)
                                    </span>
                                @elseif($u->role === 'inventory_officer')
                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-3 py-1">
                                        <i class="bi bi-box me-1"></i> Afisa Stoo (Inventory)
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border px-3 py-1">
                                        <i class="bi bi-file-earmark-check me-1"></i> Mkaguzi (Auditor)
                                    </span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $u->last_login_at ? \Carbon\Carbon::parse($u->last_login_at)->format('d/m/Y H:i') : 'Hajawahi kuingia' }}
                                </small>
                            </td>
                            <td>
                                @if($u->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Hai (Active)</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border">Imesitishwa</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-1">
                                    <button class="btn btn-sm btn-outline-teal" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $u->id }}">
                                        <i class="bi bi-pencil me-1"></i> Hariri
                                    </button>
                                    @if($u->id !== auth()->id())
                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $u->id }}" title="Futa Mfanyakazi">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Delete User Modal -->
                        @if($u->id !== auth()->id())
                        <div class="modal fade" id="deleteUserModal{{ $u->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <form action="{{ route('users.destroy', $u) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-body p-4 text-center">
                                            <div class="text-danger mb-3" style="font-size: 2.8rem;">
                                                <i class="bi bi-exclamation-triangle-fill"></i>
                                            </div>
                                            <h5 class="fw-bold text-dark mb-2">Thibitisha Kufuta Mfanyakazi</h5>
                                            <p class="text-muted small mb-0">Je, una uhakika unataka kumfuta kabisa <strong>{{ $u->name }}</strong> (<code>{{ $u->username }}</code>) kutoka kwenye mfumo?</p>
                                        </div>
                                        <div class="modal-footer bg-light justify-content-center border-0 pt-0 pb-3">
                                            <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Ghairi</button>
                                            <button type="submit" class="btn btn-danger px-4"><i class="bi bi-trash me-1"></i> Ndiyo, Futa</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Edit User Modal -->
                        <div class="modal fade" id="editUserModal{{ $u->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <form action="{{ route('users.update', $u) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-teal text-white">
                                            <h5 class="modal-title fw-bold"><i class="bi bi-person-gear me-2"></i>Hariri Mfanyakazi: {{ $u->name }}</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Jina Kamili <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" value="{{ $u->name }}" required>
                                            </div>
                                            <div class="row g-2 mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Jina la Kuingilia (Username) <span class="text-danger">*</span></label>
                                                    <input type="text" name="username" class="form-control" value="{{ $u->username }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Namba ya Simu</label>
                                                    <input type="text" name="phone" class="form-control" value="{{ $u->phone }}">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Barua Pepe (Email) <span class="text-danger">*</span></label>
                                                <input type="email" name="email" class="form-control" value="{{ $u->email }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Wajibu / Nafasi (Role) <span class="text-danger">*</span></label>
                                                <select name="role" class="form-select" required>
                                                    <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Msimamizi Mkuu (Admin)</option>
                                                    <option value="pharmacist" {{ $u->role === 'pharmacist' ? 'selected' : '' }}>Mfamasia (Pharmacist)</option>
                                                    <option value="cashier" {{ $u->role === 'cashier' ? 'selected' : '' }}>Muuzaji (Cashier)</option>
                                                    <option value="auditor" {{ $u->role === 'auditor' ? 'selected' : '' }}>Mkaguzi wa Ndani (Auditor)</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Neno la Siri Jipya (Acha tupu kama hubadili)</label>
                                                <input type="password" name="password" class="form-control" placeholder="Weka jipya tu kama unataka kubadili">
                                            </div>
                                            <div class="form-check form-switch mt-3">
                                                <input class="form-check-input" type="checkbox" name="is_active" id="active{{ $u->id }}" {{ $u->is_active ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold" for="active{{ $u->id }}">Akaunti Ipo Hai (Active)</label>
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
                            <td colspan="7" class="text-center py-5 text-muted">Hakuna watumiaji waliopatikana.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-teal text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-plus me-2"></i>Sajili Mfanyakazi Mpya</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jina Kamili <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Mfano: Neema Emmanuel" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jina la Kuingilia (Username) <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" placeholder="neema" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Namba ya Simu</label>
                            <input type="text" name="phone" class="form-control" placeholder="07XXXXXXXX">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Barua Pepe (Email) <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="neema@njamapharmacy.co.tz" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nafasi / Wajibu (Role) <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <option value="cashier" selected>Muuzaji (Cashier)</option>
                            <option value="pharmacist">Mfamasia (Pharmacist)</option>
                            <option value="auditor">Mkaguzi wa Hesabu (Auditor)</option>
                            <option value="admin">Msimamizi Mkuu (Admin)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Neno la Siri (Password) <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Angalau tarakimu 6" minlength="6" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ghairi</button>
                    <button type="submit" class="btn btn-teal"><i class="bi bi-check-circle me-1"></i>Sajili Mfanyakazi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
