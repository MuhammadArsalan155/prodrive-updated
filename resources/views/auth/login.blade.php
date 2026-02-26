@extends('auth.layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white shadow-xl rounded-lg">
            <div class="px-4 py-8 sm:px-10">
                <div class="text-center mb-8">
                    <img class="mx-auto h-24 w-auto" src="{{ asset('admin/img/Prodrive 4.png') }}" alt="Prodrive Logo">
                    <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                        Welcome Back!
                    </h2>
                </div>

                <form method="POST" action="{{ route('user.login') }}" class="space-y-6" id="loginForm">
                    @csrf
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">
                            Select Role
                        </label>
                        <div class="mt-1">
                            <select id="role" name="role"
                                    class="block w-full px-3 py-2 border border-black-300 rounded-md shadow-sm
                                           focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" style="color:black"
                                    required>
                                <option value="">Select Your Role</option>
                                <!-- Roles will be dynamically populated here -->
                            </select>
                        </div>
                    </div>

                    <!-- Rest of the form remains the same -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email Address
                        </label>
                        <div class="mt-1">
                            <input id="email" name="email" type="email"
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400
                                          focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm
                                          @error('email') border-red-500 @enderror" style="color:black"
                                   value="{{ old('email') }}"
                                   required
                                   autocomplete="email"
                                   autofocus
                                   placeholder="you@example.com">

                            @error('email')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Password
                        </label>
                        <div class="mt-1">
                            <input id="password" name="password" type="password"
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400
                                          focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm
                                          @error('password') border-red-500 @enderror" style="color:black"
                                   required
                                   autocomplete="current-password"
                                   placeholder="Your password">

                            @error('password')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Rest of the form remains the same -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="customCheck" name="remember" type="checkbox"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                   {{ old('remember') ? 'checked' : '' }}>
                            <label for="customCheck" class="ml-2 block text-sm text-gray-900">
                                Remember me
                            </label>
                        </div>

                        <div class="text-sm">
                            <a href="{{route('password.request')}}" class="font-medium text-indigo-600 hover:text-indigo-500">
                                Forgot your password?
                            </a>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md
                                shadow-sm text-sm font-medium text-white bg-indigo-600
                                hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Login
                        </button>
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
    const loginForm = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');


function fetchRoles() {
    // Use the ID from your HTML (role instead of roleSelect)
    const roleSelect = document.getElementById('role');

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

        // Populate roles
        roles.forEach(role => {
            const option = document.createElement('option');
            option.value = role.name;
            option.textContent = role.display_name || role.name;
            roleSelect.appendChild(option);
        });
    })
    .catch(error => {
        console.error('Error fetching roles:', error);
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

    function showError(message) {
        const existingErrors = document.querySelectorAll('.error-message');
        existingErrors.forEach(el => el.remove());


        const errorContainer = document.createElement('div');
        errorContainer.className = 'error-message text-red-600 text-sm mt-2';
        errorContainer.textContent = message;

        // Insert error message before submit button
        const submitButton = loginForm.querySelector('button[type="submit"]');
        submitButton.parentNode.insertBefore(errorContainer, submitButton);
    }

    // Initial roles fetch
    fetchRoles();

    loginForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const email = emailInput.value;
        const password = passwordInput.value;
        const role = roleSelect.value;

        // Validate role is selected
        if (!role) {
            showError('Please select a role');
            return;
        }

        // Disable submit button to prevent multiple submissions
        const submitButton = loginForm.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = 'Validating...';

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        const token = csrfToken ? csrfToken.getAttribute('content') : '';

        // AJAX request to validate user credentials and role
        fetch('{{ route('user.login.validate') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                email: email,
                password: password,
                role: role
            })
        })
        .then(response => {
            if (!response.ok) {
                // If not OK, try to get error message
                return response.text().then(errorText => {
                    console.error('Error Response Text:', errorText);
                    throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
                });
            }

            // Try to parse JSON
            return response.json();
        })
        .then(data => {
            // Re-enable submit button
            submitButton.disabled = false;
            submitButton.innerHTML = 'Login';

            // Log parsed data
            console.log('Parsed data:', data);

            if (data.valid) {
                // If validation successful, submit the form
                loginForm.submit();
            } else {
                // Show error message
                const errorMessage = data.message || 'Invalid credentials';
                showError(errorMessage);
            }
        })
        .catch(error => {
            // Re-enable submit button
            submitButton.disabled = false;
            submitButton.innerHTML = 'Login';

            // Log full error details
            console.error('Full error:', error);

            // Show user-friendly error
            showError('An unexpected error occurred. Please try again.');
        });
    });
});
</script>
@endsection
