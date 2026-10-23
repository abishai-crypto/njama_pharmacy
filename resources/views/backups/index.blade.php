@extends('layouts.app')

@section('title', __('app.backups') . ' — NJAMA PHARMACY')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-database-check text-teal me-2"></i>{{ __('app.backups') }}</h3>
            <p class="text-muted small mb-0">Hifadhi salama ya data ya mfumo (Database SQL Backups) na ulinzi dhidi ya upotevu wa taarifa</p>
        </div>
        <form action="{{ route('backups.create') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-teal shadow-sm" onclick="return confirm('Je, ungependa kuanzisha hifadhi mpya (backup) ya database sasa?')">
                <i class="bi bi-cloud-arrow-up-fill me-1"></i> Tengeneza Backup Mpya Sasa
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <!-- Backups Table -->
            <div class="card card-custom border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-archive text-teal me-2"></i>Faili za Hifadhi (Backup Archives)</h5>
                    <span class="badge bg-teal rounded-pill">{{ $backups->total() }} Nakala</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4">Jina la Faili (Filename)</th>
                                    <th>Ukubwa</th>
                                    <th>Tarehe & Saa</th>
                                    <th>Iliyotengenezwa Na</th>
                                    <th>Hali</th>
                                    <th class="text-end pe-4">Pakua</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($backups as $b)
                                <tr>
                                    <td class="ps-4 fw-bold font-monospace text-teal">
                                        <i class="bi bi-file-earmark-code me-1"></i>{{ $b->filename }}
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ round($b->size_bytes / 1024, 1) }} KB
                                        </span>
                                    </td>
                                    <td class="small text-muted">{{ $b->created_at->format('d/m/Y H:i:s') }}</td>
                                    <td><small class="text-dark fw-semibold">{{ $b->creator ? $b->creator->name : 'Mfumo' }}</small></td>
                                    <td>
                                        @if($b->status === 'completed')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                <i class="bi bi-check-circle me-1"></i> Imekamilika
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger">Imeshindikana</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('backups.download', $b) }}" class="btn btn-sm btn-outline-teal">
                                            <i class="bi bi-download me-1"></i> Pakua (Download)
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-database-slash fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                        Hakuna faili za backup zilizorekodiwa bado. Bofya "Tengeneza Backup Mpya Sasa" hapo juu.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($backups->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $backups->links() }}
                </div>
                @endif
            </div>
        </div>

        <!-- Right: Security & Recovery Tips -->
        <div class="col-md-4">
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-shield-lock-fill text-teal me-2"></i>Ulinzi na Usalama wa Data</h5>
                </div>
                <div class="card-body pt-0 small">
                    <p class="text-muted mb-3">
                        Hifadhi hizi zinajumuisha jedwali zote za dawa 180, ankara za TONIC, miamala ya mauzo, rekodi za wagonjwa na taarifa za watumiaji.
                    </p>
                    <div class="alert alert-info-subtle border border-info-subtle mb-3">
                        <i class="bi bi-info-circle-fill text-info me-2"></i>
                        <strong>Ushauri kwa Abishai na Dr. Njama:</strong>
                        Inashauriwa kupakua nakala ya SQL mara kwa mara na kuihifadhi kwenye External Flash Drive au Google Drive ya pharmacy kwa usalama zaidi.
                    </div>
                    <ul class="list-unstyled d-flex flex-column gap-2 text-dark mb-0">
                        <li><i class="bi bi-check2 text-teal me-2"></i><strong>Mahali Zinapohifadhiwa:</strong> <code>storage/app/backups/</code></li>
                        <li><i class="bi bi-check2 text-teal me-2"></i><strong>Muundo wa Faili:</strong> MySQL/MariaDB SQL Dump</li>
                        <li><i class="bi bi-check2 text-teal me-2"></i><strong>Urejesho (Restore):</strong> Inaweza kurejeshwa kwa amri moja ya <code>mysql -u root njama_pharmacy &lt; faili.sql</code></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
