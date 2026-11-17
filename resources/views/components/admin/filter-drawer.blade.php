@props([
    'id' => 'adminFilterDrawer',
    'title' => 'Filter',
    'action',
    'resetUrl',
    'maxWidth' => '380px',
])

<div class="offcanvas offcanvas-end border-0" tabindex="-1" id="{{ $id }}" aria-labelledby="{{ $id }}Label" style="max-width: {{ $maxWidth }}; width: 100%;">
    <div class="d-flex align-items-center bg-primary p-3 offcanvas-header">
        <h5 class="text-white m-0 fw-semibold" id="{{ $id }}Label">
            <iconify-icon icon="solar:filter-bold-duotone" class="align-middle me-1"></iconify-icon>
            {{ $title }}
        </h5>
        <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-3">
        <form method="GET" action="{{ $action }}" class="d-flex flex-column h-100">
            {{ $slot }}

            <div class="mt-auto pt-3 border-top d-flex gap-2">
                <a href="{{ $resetUrl }}" class="btn btn-light w-50">Reset</a>
                <button type="submit" class="btn btn-primary w-50">Apply Filters</button>
            </div>
        </form>
    </div>
</div>

