<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Forbidden</title>
    <!-- Include Tailwind CSS via CDN for simplicity -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Optional custom styles */
        body {
            background: linear-gradient(to right, #e2e8f0, #f1f5f9);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="max-w-lg mx-auto text-center p-6 bg-white shadow-lg rounded-lg">
        <h1 class="text-6xl font-bold text-red-600 mb-4">403</h1>
        <h2 class="text-2xl font-semibold text-gray-800 mb-2">Access Denied</h2>
        <p class="text-gray-600 mb-6">Sorry, you don’t have permission to access this page.</p>
        
        <a href="{{ url('/home') }}" 
           class="inline-block px-6 py-3 bg-blue-600 text-white font-medium rounded-full hover:bg-blue-700 transition duration-300">
            Back to Home
        </a>
        
        <div class="mt-6 text-sm text-gray-500">
            <p>If you believe this is an error, please contact support.</p>
        </div>
    </div>
</body>
</html>