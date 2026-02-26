@extends('auth.layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white shadow-xl rounded-lg">
            <div class="px-4 py-8 sm:px-10">
                <div class="text-center mb-8">
                    <img class="mx-auto h-24 w-auto" src="{{ asset('admin/img/Prodrive 4.png') }}" alt="Prodrive Logo">
                    <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                        Reset Password
                    </h2>
                </div>

                <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">
                    <input type="hidden" name="role" value="{{ $role }}">

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Email Address: <strong>{{ $email }}</strong>
                        </label>
                        <label class="block text-sm font-medium text-gray-700 mt-2">
                            Role: <strong>{{ ucfirst($role) }}</strong>
                        </label>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            New Password
                        </label>
                        <div class="mt-1">
                            <input id="password" name="password" type="password"
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400
                                          focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm
                                          @error('password') border-red-500 @enderror"
                                   style="color:black"
                                   required
                                   autocomplete="new-password"
                                   placeholder="Enter new password">

                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                            Confirm New Password
                        </label>
                        <div class="mt-1">
                            <input id="password_confirmation" name="password_confirmation" type="password"
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400
                                          focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   style="color:black"
                                   required
                                   autocomplete="new-password"
                                   placeholder="Confirm new password">
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md
                                shadow-sm text-sm font-medium text-white bg-indigo-600
                                hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Reset Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
