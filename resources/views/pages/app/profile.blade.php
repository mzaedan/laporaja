@extends('layouts.app')

@section('title', 'Profile Saya')

@section('content')

@php
    $avatarPath = Auth::user()->resident->avatar ?? null;
@endphp


 <div class="d-flex flex-column justify-content-center align-items-center gap-2">
    @if ($avatarPath)
        <img src="{{ asset('storage/' . $avatarPath) }}" alt="avatar" class="avatar">
    @endif
    <h5>{{ Auth::user()->name }}</h5>
</div>

    <div class="row mt-4">
        <div class="col-6">
            <div class="card profile-stats">
                <div class="card-body">
                    <h5 class="card-title">{{ $activeReportsCount ?? 0 }}</h5>
                    <p class="card-text">Laporan Aktif</p>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card profile-stats">
                <div class="card-body">
                    <h5 class="card-title">{{ $completedReportsCount ?? 0 }}</h5>
                    <p class="card-text">Laporan Selesai</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <div class="list-group list-group-flush">
            <a href="#"
                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <i class="fa-solid fa-user"></i>
                    <p class="fw-light">Pengaturan Akun</p>
                </div>
                <i class="fa-solid fa-chevron-right"></i>
            </a>
            <a href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal"
                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <i class="fa-solid fa-lock"></i>
                    <p class="fw-light"> Kata sandi</p>
                </div>
                <i class="fa-solid fa-chevron-right"></i>
            </a>
            <a href="{{ route('help') }}"
                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <i class="fa-solid fa-question-circle"></i>
                    <p class="fw-light">Bantuanan dukungan</p>
                </div>
                <i class="fa-solid fa-chevron-right"></i>
            </a>
            <a href="{{ route('public.dashboard') }}"
                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <i class="fa-solid fa-chart-bar"></i>
                    <p class="fw-light">Dashboard Statistik</p>
                </div>
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        </div>

        <div class="mt-4">
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
            <button class="btn btn-outline-danger w-100 rounded-pill" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Keluar
            </button>
        </div>
    </div>



<!-- Modal Ganti Kata Sandi -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">Ganti Kata Sandi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm">
                    @csrf
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label">Kata Sandi Saat Ini</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">Kata Sandi Baru</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="newPassword" name="new_password" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        <div class="form-text">Minimal 8 karakter</div>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Konfirmasi Kata Sandi Baru</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirmPassword" name="new_password_confirmation" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div id="passwordError" class="alert alert-danger" style="display: none;"></div>
                    <div id="passwordSuccess" class="alert alert-success" style="display: none;"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnChangePassword">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    // Toggle password visibility
    $('#toggleCurrentPassword').click(function() {
        togglePassword('currentPassword', this);
    });
    
    $('#toggleNewPassword').click(function() {
        togglePassword('newPassword', this);
    });
    
    $('#toggleConfirmPassword').click(function() {
        togglePassword('confirmPassword', this);
    });
    
    function togglePassword(inputId, button) {
        const input = $('#' + inputId);
        const icon = $(button).find('i');
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    }
    
    // Handle password change
    $('#btnChangePassword').click(function() {
        changePassword();
    });
    
    // Handle form submit on Enter
    $('#changePasswordForm').on('keypress', function(e) {
        if (e.which === 13) {
            changePassword();
        }
    });
    
    function changePassword() {
        const form = $('#changePasswordForm')[0];
        const formData = new FormData(form);
        
        // Reset messages
        $('#passwordError').hide().text('');
        $('#passwordSuccess').hide().text('');
        
        // Validate passwords match
        const newPassword = $('#newPassword').val();
        const confirmPassword = $('#confirmPassword').val();
        
        if (newPassword !== confirmPassword) {
            $('#passwordError').text('Kata sandi baru dan konfirmasi tidak cocok.').show();
            return;
        }
        
        if (newPassword.length < 8) {
            $('#passwordError').text('Kata sandi baru minimal 8 karakter.').show();
            return;
        }
        
        // Disable button and show loading
        $('#btnChangePassword').prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...');
        
        $.ajax({
            url: '{{ route("profile.change-password") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#passwordSuccess').text(response.message).show();
                $('#changePasswordForm')[0].reset();
                
                // Close modal after 2 seconds
                setTimeout(function() {
                    $('#changePasswordModal').modal('hide');
                    $('#passwordSuccess').hide();
                }, 2000);
            },
            error: function(xhr) {
                const errorMessage = xhr.responseJSON?.message || 'Terjadi kesalahan. Silakan coba lagi.';
                $('#passwordError').text(errorMessage).show();
            },
            complete: function() {
                $('#btnChangePassword').prop('disabled', false).text('Simpan Perubahan');
            }
        });
    }
    
    // Reset form when modal is closed
    $('#changePasswordModal').on('hidden.bs.modal', function() {
        $('#changePasswordForm')[0].reset();
        $('#passwordError').hide().text('');
        $('#passwordSuccess').hide().text('');
    });
});
</script>
@endsection

@endsection




