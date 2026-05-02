@extends('layouts.app')

@section('title', 'Profile')

@slot('slot')
    <x-page-header title="Profile Settings" description="Manage your account information and security." :breadcrumbs="['Dashboard' => route('dashboard'), 'Profile' => null]" />

    <div class="space-y-6 max-w-3xl">
        <x-card title="Update Profile" icon="user-circle">
            @include('profile.partials.update-profile-information-form')
        </x-card>

        <x-card title="Change Password" icon="key">
            @include('profile.partials.update-password-form')
        </x-card>

        <x-card title="Delete Account" icon="trash" class="border-red-100">
            <div class="p-4 bg-red-50 border border-red-100 rounded-xl mb-6">
                <p class="text-xs text-red-700 leading-relaxed font-medium">
                    Once your account is deleted, all of its resources and data will be permanently deleted.
                </p>
            </div>
            @include('profile.partials.delete-user-form')
        </x-card>
    </div>
@endslot
