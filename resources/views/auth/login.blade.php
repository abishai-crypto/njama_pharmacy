@extends('layouts.auth')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9 col-lg-7 col-xl-5">
        <div class="login-card p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-3 text-white mb-3 shadow" style="width: 58px; height: 58px; background: linear-gradient(135deg, #14b8a6, #0d9488); font-size: 1.6rem;">
                    <i class="fa-solid fa-notes-medical"></i>
                </div>
                <h3 class="fw-bold mb-1" style="color: #0f766e;">NJAMA PHARMACY</h3>
                <p class="text-muted small mb-0">Pharmacy Management System • Dar es Salaam</p>
                <div class="badge mt-2" style="background-color: #ccfbf1; color: #0f766e; font-size: 0.72rem;">
                    <i class="bi bi-shield-check me-1"></i>Secure Role-Based Authentication
                </div>
            </div>

            @if(session('info'))
                <div class="alert alert-info py-2 px-3 small">{{ session('info') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger py-2 px-3 small">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Username or Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-person-fill text-muted"></i></span>
                        <input type="text" name="login" class="form-control border-start-0 ps-0" placeholder="e.g. dr.njama or admin@njamapharmacy.co.tz" value="{{ old('login', 'dr.njama') }}" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-label fw-semibold small mb-0">Password</label>
                    </div>
                    <div class="input-group mt-1">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill text-muted"></i></span>
                        <input type="password" name="password" id="passwordInput" class="form-control border-start-0 ps-0" placeholder="••••••••" value="Admin@12345" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePass()">
                            <i class="bi bi-eye" id="passIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                        <label class="form-check-label small text-muted" for="remember">Remember this computer</label>
                    </div>
                </div>

                <button type="submit" class="btn w-100 py-2 fw-bold text-white shadow-sm" style="background: linear-gradient(135deg, #0d9488, #0f766e); border: none; border-radius: 10px;">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to NJAMA PHARMACY
                </button>
            </form>

            <!-- Quick Demo Accounts Switcher -->
            <div class="mt-4 pt-3 border-top">
                <div class="small fw-bold text-muted mb-2 text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">Quick System Accounts:</div>
                <div class="d-flex flex-wrap gap-1">
                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2" style="font-size: 0.75rem;" onclick="fillLogin('dr.njama', 'Admin@12345')">Dr. Njama (Owner)</button>
                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2" style="font-size: 0.75rem;" onclick="fillLogin('abishai', 'Admin@12345')">Abishai (Tech)</button>
                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2" style="font-size: 0.75rem;" onclick="fillLogin('cashier', 'Cash@12345')">Cashier</button>
                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2" style="font-size: 0.75rem;" onclick="fillLogin('pharmacist', 'Pharm@12345')">Pharmacist</button>
                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2" style="font-size: 0.75rem;" onclick="fillLogin('inventory', 'Store@12345')">Storekeeper</button>
                </div>
            </div>

            <div class="mt-4 text-center text-muted" style="font-size: 0.75rem;">
                Emergency Tech Support: <strong>0656392400 (Abishai Evaristo)</strong>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePass() {
        const inp = document.getElementById('passwordInput');
        const icon = document.getElementById('passIcon');
        if (inp.type === 'password') {
            inp.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            inp.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

    function fillLogin(u, p) {
        document.querySelector('input[name="login"]').value = u;
        document.querySelector('input[name="password"]').value = p;
    }
</script>
@endsection
