@extends('layouts.app')

@section('title', 'My Profile')
@section('page_title', 'User Profile & Security')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-custom p-4">
            <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold fs-3" style="width: 64px; height: 64px; background: linear-gradient(135deg, #0d9488, #0f766e);">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h4 class="mb-1 fw-bold">{{ $user->name }}</h4>
                    <span class="badge bg-light text-dark border">{{ strtoupper(str_replace('_', ' ', $user->role)) }}</span>
                    <span class="text-muted ms-2 small">Username: <strong>{{ $user->username }}</strong></span>
                </div>
            </div>

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Full Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Email Address</label>
                        <input type="email" class="form-control bg-light" value="{{ $user->email }}" disabled>
                        <small class="text-muted">Email is managed by system administrator.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Assigned Role</label>
                        <input type="text" class="form-control bg-light" value="{{ ucfirst(str_replace('_', ' ', $user->role)) }}" disabled>
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-shield-lock me-2 text-emerald"></i>Change Password</h5>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Current Password</label>
                    <input type="password" name="current_password" class="form-control" placeholder="Enter current password to make changes">
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">New Password</label>
                        <input type="password" name="new_password" class="form-control" placeholder="Minimum 6 characters">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" class="form-control" placeholder="Re-enter new password">
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-emerald">
                        <i class="bi bi-check2-circle me-1"></i> Save Profile Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
