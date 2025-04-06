@extends('layouts.admin')

@section('title', 'Detail Data Masyarakat')

@section('content')

<a href="{{ route('admin.resident.index') }}" class="btn btn-danger mb-3">Kembali</a>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Detail Masyarakat</h6>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <td>Nama</td>
                <td>{{ $resident->user->name }}</td>
            </tr>
            <tr>
                <td>Email</td>
                <td>{{ $resident->user->email }}</td>
            </tr>
             <tr>
                <td>Poto Profile</td>
                <td>
                    <img src="{{ asset('storage/' . $resident->avatar) }}" alt="profile-picture" width="200">
                </td>
            </tr>
        </table>
    </div>
</div>

@endsection