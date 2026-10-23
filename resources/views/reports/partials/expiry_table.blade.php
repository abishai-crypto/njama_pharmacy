<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light text-muted small text-uppercase">
            <tr>
                <th class="ps-4">Namba ya Batch</th>
                <th>Jina la Dawa</th>
                <th class="text-center">Kiasi Kilichopo</th>
                <th>Tarehe ya Mwisho (Expiry)</th>
                <th>Muda Uliobaki</th>
                <th class="text-end pe-4">Hatua ya Usalama</th>
            </tr>
        </thead>
        <tbody>
            @forelse($batches as $batch)
            @php
                $expiry = \Carbon\Carbon::parse($batch->expiry_date);
                $daysDiff = (int) now()->startOfDay()->diffInDays($expiry, false);
            @endphp
            <tr>
                <td class="ps-4 fw-bold font-monospace text-dark">{{ $batch->batch_number }}</td>
                <td>
                    <div class="fw-bold text-dark">{{ $batch->product ? $batch->product->generic_name : 'Dawa Haijulikani' }}</div>
                    <small class="text-muted">{{ $batch->product ? $batch->product->brand_name : '' }}</small>
                </td>
                <td class="text-center">
                    <span class="badge bg-secondary rounded-pill px-3">
                        {{ $batch->quantity_remaining }} {{ $batch->product && $batch->product->unit ? $batch->product->unit->symbol : '' }}
                    </span>
                </td>
                <td>
                    <span class="badge {{ $badgeClass }} px-2 py-1">
                        {{ $expiry->format('d/m/Y') }}
                    </span>
                </td>
                <td>
                    @if($daysDiff < 0)
                        <span class="text-danger fw-bold"><i class="bi bi-x-circle me-1"></i>Imepitwa na siku {{ abs($daysDiff) }}</span>
                    @elseif($daysDiff == 0)
                        <span class="text-danger fw-bold"><i class="bi bi-clock me-1"></i>Inamalizika Leo!</span>
                    @else
                        <span class="text-dark fw-semibold"><i class="bi bi-hourglass-split me-1"></i>Zimebaki siku {{ $daysDiff }}</span>
                    @endif
                </td>
                <td class="text-end pe-4">
                    @if(in_array(auth()->user()->role, ['admin', 'inventory_officer', 'pharmacist']))
                        <div class="btn-group">
                            @if($batch->status === 'active')
                            <form action="{{ route('batches.quarantine', $batch) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Weka Karantini (Zuia Kuuzwa)" onclick="return confirm('Je, una uhakika unataka kusitisha uuzaji wa batch hii?')">
                                    <i class="bi bi-shield-slash me-1"></i> Karantini
                                </button>
                            </form>
                            @endif

                            <form action="{{ route('batches.write-off', $batch) }}" method="POST" class="d-inline ms-1">
                                @csrf
                                <input type="hidden" name="reason" value="Expired or spoiled medicine written off safely">
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Ondoa kwenye stoo (Write-off)" onclick="return confirm('Je, una uhakika unataka kuiondoa batch hii kwenye hesabu ya stoo?')">
                                    <i class="bi bi-trash3 me-1"></i> Write-Off
                                </button>
                            </form>
                        </div>
                    @else
                        <span class="text-muted small">Tazama tu</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-5 text-muted">
                    <i class="bi bi-check-circle-fill text-success fs-2 d-block mb-2"></i>
                    Hakuna batch yoyote ya dawa iliyo kwenye kundi hili kwa sasa. Stoo iko salama!
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
