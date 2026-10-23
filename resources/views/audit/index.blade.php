@extends('layouts.app')

@section('title', __('app.audit_logs') . ' — NJAMA PHARMACY')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-shield-check text-teal me-2"></i>{{ __('app.audit_logs') }}</h3>
            <p class="text-muted small mb-0">Rejesta isiyofutika ya matukio yote ya mfumo, mauzo, mabadiliko ya bei, na usimamizi wa stoo</p>
        </div>
        <button onclick="window.print()" class="btn btn-outline-secondary">
            <i class="bi bi-printer me-1"></i> Chapisha Kumbukumbu
        </button>
    </div>

    <!-- Filter Card -->
    <div class="card card-custom border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('audit.index') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <select name="user_id" class="form-select">
                        <option value="">Wafanyakazi Wote (All Users)</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} (@ {{ $user->username }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="action" class="form-control" placeholder="Tafuta kwa kitendo (Mfano: sale_completed, price_updated)..." value="{{ request('action') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-teal w-100"><i class="bi bi-funnel me-1"></i> Chuja</button>
                    <a href="{{ route('audit.index') }}" class="btn btn-outline-secondary">Weka Upya</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card card-custom border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">Tarehe & Saa</th>
                            <th>Mtumiaji (Staff)</th>
                            <th>Kitendo (Action)</th>
                            <th>Maelezo Kamili</th>
                            <th>Aina ya Rekodi</th>
                            <th class="pe-4">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="ps-4 text-muted small" style="white-space: nowrap;">
                                <i class="bi bi-clock me-1 text-teal"></i>{{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td>
                                @if($log->user)
                                    <div class="fw-bold text-dark">{{ $log->user->name }}</div>
                                    <small class="text-muted"><span class="badge bg-light text-dark border">{{ $log->user->role }}</span></small>
                                @else
                                    <span class="text-muted">Mfumo (System)</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-teal-subtle text-teal border border-teal-subtle font-monospace">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td>
                                <div class="text-dark small">{{ $log->description }}</div>
                            </td>
                            <td>
                                @if($log->entity_type)
                                    <span class="badge bg-secondary-subtle text-secondary border">
                                        {{ class_basename($log->entity_type) }} #{{ $log->entity_id }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="pe-4 text-muted small font-monospace">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-shield-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Hakuna kumbukumbu za matukio (audit logs) zilizopatikana.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
