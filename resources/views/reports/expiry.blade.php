@extends('layouts.app')

@section('title', __('app.expiry_report') . ' — NJAMA PHARMACY')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-calendar-x text-teal me-2"></i>{{ __('app.expiry_report') }}</h3>
            <p class="text-muted small mb-0">Ufuatiliaji wa tarehe za mwisho wa matumizi ya dawa (TMDA Expiry Compliance & Early Alert)</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class="bi bi-printer me-1"></i> Chapisha Orodha
            </button>
        </div>
    </div>

    <!-- Alert KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card card-custom border-0 shadow-sm p-3 border-start border-danger border-4">
                <span class="text-danger small text-uppercase fw-bold"><i class="bi bi-exclamation-octagon-fill me-1"></i>Zilizopitwa na Wakati</span>
                <h3 class="fw-bold text-danger mt-2 mb-0">{{ $expired->count() }}</h3>
                <small class="text-muted mt-1">Marufuku kuuzwa kabisa</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom border-0 shadow-sm p-3 border-start border-warning border-4">
                <span class="text-warning small text-uppercase fw-bold"><i class="bi bi-exclamation-triangle-fill me-1"></i>Siku 30 Zilizobaki</span>
                <h3 class="fw-bold text-warning mt-2 mb-0">{{ $near30->count() }}</h3>
                <small class="text-muted mt-1">Hatua za haraka zinahitajika</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom border-0 shadow-sm p-3 border-start border-info border-4">
                <span class="text-info small text-uppercase fw-bold"><i class="bi bi-clock-history me-1"></i>Siku 60 Zilizobaki</span>
                <h3 class="fw-bold text-info mt-2 mb-0">{{ $near60->count() }}</h3>
                <small class="text-muted mt-1">Angalizo la kati</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom border-0 shadow-sm p-3 border-start border-teal border-4">
                <span class="text-teal small text-uppercase fw-bold"><i class="bi bi-calendar3 me-1"></i>Siku 90 Zilizobaki</span>
                <h3 class="fw-bold text-teal mt-2 mb-0">{{ $near90->count() }}</h3>
                <small class="text-muted mt-1">Taarifa ya mapema</small>
            </div>
        </div>
    </div>

    <!-- Nav Tabs for Categories -->
    <div class="card card-custom border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-3">
            <ul class="nav nav-pills" id="expiryTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active fw-bold text-danger" id="expired-tab" data-bs-toggle="pill" data-bs-target="#tab-expired" type="button">
                        <i class="bi bi-x-circle me-1"></i> Zilizopitwa na Wakati ({{ $expired->count() }})
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold text-warning" id="near30-tab" data-bs-toggle="pill" data-bs-target="#tab-near30" type="button">
                        <i class="bi bi-exclamation-circle me-1"></i> Siku 1 - 30 ({{ $near30->count() }})
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold text-info" id="near60-tab" data-bs-toggle="pill" data-bs-target="#tab-near60" type="button">
                        <i class="bi bi-info-circle me-1"></i> Siku 31 - 60 ({{ $near60->count() }})
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold text-teal" id="near90-tab" data-bs-toggle="pill" data-bs-target="#tab-near90" type="button">
                        <i class="bi bi-calendar-event me-1"></i> Siku 61 - 90 ({{ $near90->count() }})
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body p-0">
            <div class="tab-content" id="expiryTabsContent">
                <!-- Tab: Expired -->
                <div class="tab-pane fade show active" id="tab-expired" role="tabpanel">
                    @include('reports.partials.expiry_table', ['batches' => $expired, 'badgeClass' => 'bg-danger text-white', 'isExpired' => true])
                </div>

                <!-- Tab: Near 30 -->
                <div class="tab-pane fade" id="tab-near30" role="tabpanel">
                    @include('reports.partials.expiry_table', ['batches' => $near30, 'badgeClass' => 'bg-warning text-dark', 'isExpired' => false])
                </div>

                <!-- Tab: Near 60 -->
                <div class="tab-pane fade" id="tab-near60" role="tabpanel">
                    @include('reports.partials.expiry_table', ['batches' => $near60, 'badgeClass' => 'bg-info text-dark', 'isExpired' => false])
                </div>

                <!-- Tab: Near 90 -->
                <div class="tab-pane fade" id="tab-near90" role="tabpanel">
                    @include('reports.partials.expiry_table', ['batches' => $near90, 'badgeClass' => 'bg-teal text-white', 'isExpired' => false])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
