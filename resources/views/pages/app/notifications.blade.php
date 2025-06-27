@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-purple-600 to-indigo-500 py-6">
    <div class="max-w-md mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-white text-2xl font-bold">Notifications</h2>
            <span class="bg-white/20 text-white text-xs px-2 py-1 rounded-lg font-semibold">RTL</span>
        </div>
        <div class="bg-white rounded-2xl shadow-lg divide-y overflow-hidden">
            <a href="#" class="flex items-center px-5 py-4 hover:bg-gray-50 transition">
                <span class="flex-shrink-0"><span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-green-100"><i class="fas fa-check text-green-600 text-lg"></i></span></span>
                <div class="ml-4 flex-1">
                    <div class="font-semibold text-gray-900">Confirmation</div>
                    <div class="text-sm text-gray-500">Please confirm your email address</div>
                </div>
                <i class="fas fa-chevron-right text-gray-300"></i>
            </a>
            <a href="#" class="flex items-center px-5 py-4 hover:bg-gray-50 transition">
                <span class="flex-shrink-0"><span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-red-100"><i class="fas fa-times text-red-600 text-lg"></i></span></span>
                <div class="ml-4 flex-1">
                    <div class="font-semibold text-gray-900">Error</div>
                    <div class="text-sm text-gray-500">Please confirm your email address</div>
                </div>
                <i class="fas fa-chevron-right text-gray-300"></i>
            </a>
            <a href="#" class="flex items-center px-5 py-4 hover:bg-gray-50 transition">
                <span class="flex-shrink-0"><span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-yellow-100"><i class="fas fa-exclamation-triangle text-yellow-600 text-lg"></i></span></span>
                <div class="ml-4 flex-1">
                    <div class="font-semibold text-gray-900">Warning</div>
                    <div class="text-sm text-gray-500">Can't reach your current location</div>
                </div>
                <i class="fas fa-chevron-right text-gray-300"></i>
            </a>
            <a href="#" class="flex items-center px-5 py-4 hover:bg-gray-50 transition">
                <span class="flex-shrink-0"><span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-yellow-100"><i class="fas fa-exclamation-triangle text-yellow-600 text-lg"></i></span></span>
                <div class="ml-4 flex-1">
                    <div class="font-semibold text-gray-900">Warning</div>
                    <div class="text-sm text-gray-500">Please contact support center</div>
                </div>
                <i class="fas fa-chevron-right text-gray-300"></i>
            </a>
            <a href="#" class="flex items-center px-5 py-4 hover:bg-gray-50 transition">
                <span class="flex-shrink-0"><span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-100"><i class="fas fa-bell text-blue-600 text-lg"></i></span></span>
                <div class="ml-4 flex-1">
                    <div class="font-semibold text-gray-900">Notification</div>
                    <div class="text-sm text-gray-500">You have new message</div>
                </div>
                <i class="fas fa-chevron-right text-gray-300"></i>
            </a>
            <a href="#" class="flex items-center px-5 py-4 hover:bg-gray-50 transition">
                <span class="flex-shrink-0"><span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-green-100"><i class="fas fa-check text-green-600 text-lg"></i></span></span>
                <div class="ml-4 flex-1">
                    <div class="font-semibold text-gray-900">Success</div>
                    <div class="text-sm text-gray-500">You have a new follower</div>
                </div>
                <i class="fas fa-chevron-right text-gray-300"></i>
            </a>
        </div>
    </div>
</div>
@endsection
