@extends('layouts.app')

@section('content')
<div class="min-vh-100 py-4">
    <div class="container" style="max-width: 28rem;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-dark fs-2 fw-bold mb-0">Notifications</h2>
        </div>
        
        <div class="bg-white rounded-4 shadow-lg overflow-hidden border">
            <!-- Confirmation Notification -->
            <a href="#" class="d-flex align-items-center p-4 text-decoration-none text-dark border-bottom notification-item">
                <div class="flex-shrink-0">
                    <div class="d-flex align-items-center justify-content-center rounded-circle border" style="width: 2.5rem; height: 2.5rem; background-color: #f8f9fa;">
                        <i class="fas fa-check text-dark fs-5"></i>
                    </div>
                </div>
                <div class="ms-3 flex-grow-1">
                    <div class="fw-semibold text-dark">Confirmation</div>
                    <div class="text-muted small">Please confirm your email address</div>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
            </a>

            <!-- Error Notification -->
            <a href="#" class="d-flex align-items-center p-4 text-decoration-none text-dark border-bottom notification-item">
                <div class="flex-shrink-0">
                    <div class="d-flex align-items-center justify-content-center rounded-circle border" style="width: 2.5rem; height: 2.5rem; background-color: #343a40;">
                        <i class="fas fa-times text-white fs-5"></i>
                    </div>
                </div>
                <div class="ms-3 flex-grow-1">
                    <div class="fw-semibold text-dark">Error</div>
                    <div class="text-muted small">Please confirm your email address</div>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
            </a>

            <!-- Warning Notification 1 -->
            <a href="#" class="d-flex align-items-center p-4 text-decoration-none text-dark border-bottom notification-item">
                <div class="flex-shrink-0">
                    <div class="d-flex align-items-center justify-content-center rounded-circle border" style="width: 2.5rem; height: 2.5rem; background-color: #6c757d;">
                        <i class="fas fa-exclamation-triangle text-white fs-5"></i>
                    </div>
                </div>
                <div class="ms-3 flex-grow-1">
                    <div class="fw-semibold text-dark">Warning</div>
                    <div class="text-muted small">Can't reach your current location</div>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
            </a>

            <!-- Warning Notification 2 -->
            <a href="#" class="d-flex align-items-center p-4 text-decoration-none text-dark border-bottom notification-item">
                <div class="flex-shrink-0">
                    <div class="d-flex align-items-center justify-content-center rounded-circle border" style="width: 2.5rem; height: 2.5rem; background-color: #6c757d;">
                        <i class="fas fa-exclamation-triangle text-white fs-5"></i>
                    </div>
                </div>
                <div class="ms-3 flex-grow-1">
                    <div class="fw-semibold text-dark">Warning</div>
                    <div class="text-muted small">Please contact support center</div>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
            </a>

            <!-- Blue Notification -->
            <a href="#" class="d-flex align-items-center p-4 text-decoration-none text-dark border-bottom notification-item">
                <div class="flex-shrink-0">
                    <div class="d-flex align-items-center justify-content-center rounded-circle border" style="width: 2.5rem; height: 2.5rem; background-color: #343a40;">
                        <i class="fas fa-bell text-white fs-5"></i>
                    </div>
                </div>
                <div class="ms-3 flex-grow-1">
                    <div class="fw-semibold text-dark">Notification</div>
                    <div class="text-muted small">You have new message</div>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
            </a>

            <!-- Success Notification -->
            <a href="#" class="d-flex align-items-center p-4 text-decoration-none text-dark notification-item">
                <div class="flex-shrink-0">
                    <div class="d-flex align-items-center justify-content-center rounded-circle border" style="width: 2.5rem; height: 2.5rem; background-color: #f8f9fa;">
                        <i class="fas fa-check text-dark fs-5"></i>
                    </div>
                </div>
                <div class="ms-3 flex-grow-1">
                    <div class="fw-semibold text-dark">Success</div>
                    <div class="text-muted small">You have a new follower</div>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
            </a>
        </div>
    </div>
</div>

<style>
.notification-item:hover {
    background-color: #f8f9fa !important;
    transition: background-color 0.15s ease-in-out;
}

.rounded-4 {
    border-radius: 1rem !important;
}
</style>
@endsection