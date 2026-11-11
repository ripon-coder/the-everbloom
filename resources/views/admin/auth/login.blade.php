@extends('layouts.admin-auth')

@section('title', 'Admin Sign In')

@section('content')
<h2 class="fw-bold fs-24">Admin Sign In</h2>
<p class="text-muted mt-1 mb-4">Enter your credentials to access the Everbloom management console.</p>

<div class="mb-4">
    <form action="{{ route('admin.dashboard') }}" method="GET" class="authentication-form">
        <div class="mb-3">
            <label class="form-label" for="admin-email">Email Address</label>
            <input type="email" id="admin-email" name="email" class="form-control" placeholder="admin@the-everbloom.com" value="admin@the-everbloom.com" required>
        </div>
        <div class="mb-3">
            <a href="javascript:void(0);" class="float-end text-muted text-decoration-underline ms-1">Forgot password?</a>
            <label class="form-label" for="admin-password">Password</label>
            <input type="password" id="admin-password" name="password" class="form-control" placeholder="Enter password" value="••••••••••••" required>
        </div>
        <div class="mb-3">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="checkbox-signin" checked>
                <label class="form-check-label" for="checkbox-signin">Remember this browser</label>
            </div>
        </div>

        <div class="mb-1 text-center d-grid">
            <button class="btn btn-primary" type="submit">Sign In to Dashboard</button>
        </div>
    </form>
</div>

<p class="text-muted text-center fs-13">
    Botanical Storefront: <a href="{{ route('home') }}" class="text-primary fw-semibold ms-1" target="_blank">View Live Store &rarr;</a>
</p>
@endsection
