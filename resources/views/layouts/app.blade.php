<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — NJAMA PHARMACY</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --brand-primary: #0d9488;
            --brand-dark: #0f766e;
            --brand-deep: #134e4a;
            --brand-light: #ccfbf1;
            --sidebar-bg: #092c28;
            --sidebar-hover: #115e59;
            --body-bg: #f8fafc;
            --card-border: #e2e8f0;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--body-bg);
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        #sidebar-wrapper {
            width: 270px;
            min-height: 100vh;
            background: linear-gradient(180deg, #092c28 0%, #061e1b 100%);
            color: #ffffff;
            transition: all 0.3s ease;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.08);
            z-index: 1000;
        }

        .sidebar-brand {
            padding: 22px 20px;
            font-size: 1.15rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-icon-box {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.35);
        }

        .sidebar-section-title {
            padding: 18px 22px 6px;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #5eead4;
            font-weight: 700;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 22px;
            color: #cbd5e1;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            border-radius: 8px;
            margin: 2px 12px;
            transition: all 0.2s ease;
        }

        .sidebar-link i {
            font-size: 1.15rem;
            width: 22px;
            text-align: center;
            color: #94a3b8;
            transition: all 0.2s ease;
        }

        .sidebar-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.07);
        }

        .sidebar-link:hover i {
            color: #2dd4bf;
        }

        .sidebar-link.active {
            background: linear-gradient(90deg, #0d9488, #14b8a6);
            color: #ffffff;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.3);
        }

        .sidebar-link.active i {
            color: #ffffff;
        }

        /* Top Header */
        .main-header {
            background: #ffffff;
            border-bottom: 1px solid var(--card-border);
            padding: 12px 28px;
            position: sticky;
            top: 0;
            z-index: 990;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        }

        /* Cards & Metrics */
        .card-custom {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 14px;
            box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.04);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-custom:hover {
            box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.07);
        }

        .metric-icon-bubble {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }

        .badge-fefo-safe {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            font-weight: 600;
        }

        .badge-fefo-warning {
            background-color: #fffbeb;
            color: #92400e;
            border: 1px solid #fde68a;
            font-weight: 600;
        }

        .badge-fefo-danger {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
            font-weight: 600;
        }

        /* Buttons */
        .btn-emerald {
            background: linear-gradient(135deg, #0d9488, #0f766e);
            color: white;
            border: none;
            font-weight: 600;
            padding: 9px 18px;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(13, 148, 136, 0.25);
            transition: all 0.2s;
        }

        .btn-emerald:hover {
            background: linear-gradient(135deg, #0f766e, #115e59);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 5px 14px rgba(13, 148, 136, 0.35);
        }

        /* Table Aesthetics */
        .table-custom {
            margin-bottom: 0;
        }

        .table-custom th {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 700;
            color: #64748b;
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 16px;
        }

        .table-custom td {
            padding: 14px 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.88rem;
        }

        .table-custom tbody tr:hover {
            background-color: #f8fafc;
        }

        /* POS Keyboard helper badge */
        .kbd-key {
            background-color: #e2e8f0;
            color: #334155;
            border: 1px solid #cbd5e1;
            padding: 2px 7px;
            border-radius: 5px;
            font-size: 0.75rem;
            font-weight: 700;
            font-family: monospace;
        }
    </style>
    @yield('styles')
</head>
<body>
<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <div class="sidebar-brand">
            <div class="brand-icon-box">
                <i class="fa-solid fa-notes-medical"></i>
            </div>
            <div>
                <div style="line-height: 1.2;">NJAMA PHARMACY</div>
                <small style="font-size: 0.65rem; color: #5eead4; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Dar es Salaam • TZS</small>
            </div>
        </div>

        <div class="py-2" style="max-height: calc(100vh - 80px); overflow-y: auto;">
            <div class="sidebar-section-title">Core Operations</div>
            
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->is('dashboard') || request()->is('/') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>{{ __('app.dashboard') }}</span>
            </a>

            @if(auth()->user()->isCashier() || auth()->user()->isPharmacist() || auth()->user()->isAdmin())
            <a href="{{ route('pos.index') }}" class="sidebar-link {{ request()->is('pos*') ? 'active' : '' }}">
                <i class="bi bi-cart-check-fill"></i>
                <span>{{ __('app.pos') }}</span>
            </a>
            <a href="{{ route('shifts.index') }}" class="sidebar-link {{ request()->is('shifts*') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i>
                <span>{{ __('app.cashier_shifts') }}</span>
            </a>
            @endif

            <div class="sidebar-section-title">Pharmacy Inventory</div>

            <a href="{{ route('products.index') }}" class="sidebar-link {{ request()->is('products') || request()->is('products/show*') || request()->is('products/create') || request()->is('products/*/edit') ? 'active' : '' }}">
                <i class="bi bi-capsule"></i>
                <span>{{ __('app.medicines') }}</span>
            </a>

            @if(auth()->user()->isAdmin())
            <a href="{{ route('products.bulk-prices') }}" class="sidebar-link {{ request()->is('products-bulk-prices*') ? 'active' : '' }}">
                <i class="bi bi-tags-fill"></i>
                <span>Bulk Selling Prices</span>
            </a>
            @endif

            <a href="{{ route('batches.index') }}" class="sidebar-link {{ request()->is('batches*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check-fill"></i>
                <span>{{ __('app.batches') }}</span>
            </a>

            <a href="{{ route('batches.movements') }}" class="sidebar-link {{ request()->is('inventory/movements*') ? 'active' : '' }}">
                <i class="bi bi-arrow-left-right"></i>
                <span>Stock Movement Ledger</span>
            </a>

            <div class="sidebar-section-title">Clinical & Procurement</div>

            @if(auth()->user()->isPharmacist() || auth()->user()->isAdmin())
            <a href="{{ route('prescriptions.index') }}" class="sidebar-link {{ request()->is('prescriptions*') && !request()->is('dispensing-records*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-medical-fill"></i>
                <span>{{ __('app.prescriptions') }}</span>
            </a>
            <a href="{{ route('prescriptions.dispensing') }}" class="sidebar-link {{ request()->is('dispensing-records*') ? 'active' : '' }}">
                <i class="bi bi-journal-check"></i>
                <span>Dispensing Records</span>
            </a>
            @endif

            <a href="{{ route('purchases.index') }}" class="sidebar-link {{ request()->is('purchases*') || request()->is('purchase/new') ? 'active' : '' }}">
                <i class="bi bi-truck"></i>
                <span>{{ __('app.purchases') }}</span>
            </a>

            <a href="{{ route('suppliers.index') }}" class="sidebar-link {{ request()->is('suppliers*') ? 'active' : '' }}">
                <i class="bi bi-building"></i>
                <span>Suppliers & Balances</span>
            </a>

            <div class="sidebar-section-title">Finance & Reports</div>

            <a href="{{ route('expenses.index') }}" class="sidebar-link {{ request()->is('expenses*') ? 'active' : '' }}">
                <i class="bi bi-wallet2"></i>
                <span>{{ __('app.expenses') }}</span>
            </a>

            @if(auth()->user()->isAdmin() || auth()->user()->isAuditor())
            <a href="{{ route('reports.sales') }}" class="sidebar-link {{ request()->is('reports/sales*') ? 'active' : '' }}">
                <i class="bi bi-graph-up-arrow"></i>
                <span>Sales Reports</span>
            </a>
            <a href="{{ route('reports.profit-loss') }}" class="sidebar-link {{ request()->is('reports/profit-loss*') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i>
                <span>Profit & Loss (P&L)</span>
            </a>
            <a href="{{ route('reports.inventory-valuation') }}" class="sidebar-link {{ request()->is('reports/inventory-valuation*') ? 'active' : '' }}">
                <i class="bi bi-pie-chart-fill"></i>
                <span>Stock Valuation</span>
            </a>
            <a href="{{ route('reports.expiry') }}" class="sidebar-link {{ request()->is('reports/expiry*') ? 'active' : '' }}">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>Expiry Analysis</span>
            </a>
            @endif

            @if(auth()->user()->isAdmin())
            <div class="sidebar-section-title">Administration</div>
            <a href="{{ route('settings.index') }}" class="sidebar-link {{ request()->is('settings*') ? 'active' : '' }}">
                <i class="bi bi-gear-fill"></i>
                <span>{{ __('app.settings') }}</span>
            </a>
            <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->is('users*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i>
                <span>{{ __('app.users') }}</span>
            </a>
            <a href="{{ route('audit.index') }}" class="sidebar-link {{ request()->is('audit-logs*') ? 'active' : '' }}">
                <i class="bi bi-shield-lock-fill"></i>
                <span>{{ __('app.audit_trail') }}</span>
            </a>
            <a href="{{ route('backups.index') }}" class="sidebar-link {{ request()->is('backups*') ? 'active' : '' }}">
                <i class="bi bi-database-fill-down"></i>
                <span>{{ __('app.backups') }}</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="flex-grow-1 d-flex flex-column" style="min-width: 0;">
        <!-- Header -->
        <header class="main-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-light border d-lg-none" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <div class="d-none d-md-block">
                    <h5 class="mb-0 fw-bold">@yield('page_title', 'Overview')</h5>
                    <small class="text-muted">Dar es Salaam (EAT) • {{ date('d/m/Y') }}</small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Language Toggle -->
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center gap-1" data-bs-toggle="dropdown">
                        <i class="bi bi-translate"></i>
                        <span>{{ app()->getLocale() == 'sw' ? 'Kiswahili' : 'English' }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}" href="{{ route('lang.switch', 'en') }}">🇬🇧 English</a></li>
                        <li><a class="dropdown-item {{ app()->getLocale() == 'sw' ? 'active' : '' }}" href="{{ route('lang.switch', 'sw') }}">🇹🇿 Kiswahili</a></li>
                    </ul>
                </div>

                <!-- User Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-light border d-flex align-items-center gap-2 rounded-pill px-3 py-1" data-bs-toggle="dropdown">
                        <div class="rounded-circle bg-emerald text-white fw-bold d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background-color: var(--brand-primary); font-size: 0.8rem;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="text-start d-none d-sm-block" style="line-height: 1.1;">
                            <div class="fw-bold" style="font-size: 0.82rem;">{{ auth()->user()->name }}</div>
                            <small class="text-muted" style="font-size: 0.7rem; text-transform: capitalize;">{{ str_replace('_', ' ', auth()->user()->role) }}</small>
                        </div>
                        <i class="bi bi-chevron-down text-muted" style="font-size: 0.75rem;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width: 200px;">
                        <li><h6 class="dropdown-header">NJAMA PHARMACY</h6></li>
                        <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="bi bi-person me-2"></i>Profile & Password</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>{{ __('app.logout') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Body Content -->
        <main class="p-3 p-md-4 flex-grow-1">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm border-0" role="alert" style="background-color: #ecfdf5; color: #065f46; border-left: 4px solid #10b981 !important;">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm border-0" role="alert" style="background-color: #fef2f2; color: #991b1b; border-left: 4px solid #ef4444 !important;">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="text-center py-3 text-muted border-top bg-white mt-auto" style="font-size: 0.8rem;">
            <div>&copy; {{ date('Y') }} <strong>NJAMA PHARMACY</strong> — All Rights Reserved.</div>
            <div>Doctor In-Charge: <strong>Dr. Njama (0715362428)</strong> | Systems Engineer: <strong>Abishai Evaristo (0656392400)</strong></div>
        </footer>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('sidebarToggle')?.addEventListener('click', function() {
        document.getElementById('sidebar-wrapper').classList.toggle('d-none');
    });
</script>
@yield('scripts')
</body>
</html>
