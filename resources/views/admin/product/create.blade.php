@extends('admin.partials.app')
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">
            <div class="app-card app-card-settings shadow-sm p-4">
                <div class="app-card-body">
                    <form class="settings-form">
                        <div class="mb-3">
                            <label for="setting-input-2" class="form-label">Contact Name</label>
                            <input type="text" class="form-control" id="setting-input-2" value="Steve Doe" required>
                        </div>
                        <div class="mb-3">
                            <label for="setting-input-3" class="form-label">Business Email Address</label>
                            <input type="email" class="form-control" id="setting-input-3"
                                value="hello@companywebsite.com">
                        </div>
                        <button type="submit" class="btn app-btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
