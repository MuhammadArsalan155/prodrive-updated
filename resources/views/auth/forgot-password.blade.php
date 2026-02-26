@extends('auth.layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white shadow-xl rounded-lg">
            <div class="px-4 py-8 sm:px-10">
                <div class="text-center mb-8">
                    <img class="mx-auto h-24 w-auto" src="{{ asset('admin/img/Prodrive 4.png') }}" alt="Prodrive Logo">
                    <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                        Forgot Password
                    </h2>
                    <p class="mt-2 text-center text-sm text-gray-600">
                        Enter your email and role to receive a password reset link
                    </p>
                </div>

                @if (session('status'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-6" id="forgotPasswordForm">
                    @csrf

                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">
                            Select Role
                        </label>
                        <div class="mt-1">
                            <select id="role" name="role"
                                    class="block w-full px-3 py-2 border border-black-300 rounded-md shadow-sm
                                           focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('role') border-red-500 @enderror"
                                    style="color:black"
                                    required>
                                <option value="">Select Your Role</option>
                                <!-- Roles will be dynamically populated here -->
                            </select>
                            @error('role')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email Address
                        </label>
                        <div class="mt-1">
                            <input id="email" name="email" type="email"
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400
                                          focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm
                                          @error('email') border-red-500 @enderror"
                                   style="color:black"
                                   value="{{ old('email') }}"
                                   required
                                   autocomplete="email"
                                   autofocus
                                   placeholder="you@example.com">

                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md
                                shadow-sm text-sm font-medium text-white bg-indigo-600
                                hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Send Password Reset Link
                        </button>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                            Back to Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    const emailInput = document.getElementById('email');

    // Fetch roles dynamically
    function fetchRoles() {
        fetch('{{ route("user.roles") }}', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch roles');
            }
            return response.json();
        })
        .then(roles => {
            roleSelect.innerHTML = '<option value="">Select Your Role</option>';

            roles.forEach(role => {
                const option = document.createElement('option');
                option.value = role.name;
                option.textContent = role.display_name || role.name;
                roleSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error fetching roles:', error);
            // Fallback to default roles
            const defaultRoles = [
                { name: 'student', display_name: 'Student' },
                { name: 'instructor', display_name: 'Instructor' },
                { name: 'admin', display_name: 'Admin' },
                { name: 'manager', display_name: 'Manager' },
                { name: 'parent', display_name: 'Parent' }
            ];

            defaultRoles.forEach(role => {
                const option = document.createElement('option');
                option.value = role.name;
                option.textContent = role.display_name;
                roleSelect.appendChild(option);
            });
        });
    }

    // Utility function to show error
    function showError(message) {
        const existingErrors = document.querySelectorAll('.error-message');
        existingErrors.forEach(el => el.remove());

        const errorContainer = document.createElement('div');
        errorContainer.className = 'error-message text-red-600 text-sm mt-2';
        errorContainer.textContent = message;

        const submitButton = forgotPasswordForm.querySelector('button[type="submit"]');
        submitButton.parentNode.insertBefore(errorContainer, submitButton);
    }

    // Initial roles fetch
    fetchRoles();

    // Form submission with validation
    forgotPasswordForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const email = emailInput.value;
        const role = roleSelect.value;

        if (!role) {
            showError('Please select a role');
            return;
        }

        const submitButton = forgotPasswordForm.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = 'Validating...';

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        const token = csrfToken ? csrfToken.getAttribute('content') : '';

        // AJAX request to validate user exists
        fetch('{{ route('password.validate') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                email: email,
                role: role
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(errorText => {
                    throw new Error(`HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            submitButton.disabled = false;
            submitButton.innerHTML = 'Send Password Reset Link';

            if (data.valid) {
                // If validation successful, submit the form
                forgotPasswordForm.submit();
            } else {
                const errorMessage = data.message || 'User not found';
                showError(errorMessage);
            }
        })
        .catch(error => {
            submitButton.disabled = false;
            submitButton.innerHTML = 'Send Password Reset Link';
            console.error('Error:', error);
            showError('An unexpected error occurred. Please try again.');
        });
    });
});
</script>
@endsection
