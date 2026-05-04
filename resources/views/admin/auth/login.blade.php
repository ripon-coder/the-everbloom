<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login | Dashboard</title>
  @vite('resources/css/app.css') <!-- Tailwind + Flowbite included via Vite -->
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">

  <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-8">
    <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">Sign in to Dashboard</h2>
    
    @if ($errors->any())
      <div class="mb-6 p-4 text-sm text-red-700 bg-red-100 rounded-lg border border-red-200" role="alert">
        <div class="flex items-center">
          <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
          </svg>
          <span class="font-medium">Error!</span>
        </div>
        <ul class="mt-2 list-disc list-inside">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-6">
      @csrf
      <!-- Email -->
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input 
          type="email" 
          name="email" 
          id="email" 
          placeholder="you@example.com"
          value="{{ old('email') }}"
          required
          class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
        />
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
        <input 
          type="password" 
          name="password" 
          id="password" 
          placeholder="••••••••"
          required
          class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
        />
      </div>

      <!-- Remember Me -->
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
          <label for="remember" class="ml-2 block text-sm text-gray-900"> Remember me </label>
        </div>
      </div>

      <!-- Submit -->
      <button 
        type="submit"
        class="w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        Sign In
      </button>
    </form>


  </div>

</body>
</html>
