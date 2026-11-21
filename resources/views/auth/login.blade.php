<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
    <style>
        .login-card {
            width: 100%;
            max-width: 22rem;
            box-sizing: border-box;
            padding: 2rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            background: white;
            box-shadow: 0 1.25rem 2.5rem rgba(15, 23, 42, 0.12);
        }
        .login-card h1 { margin-top: 0; }
        .field { margin: 1rem 0; }
        label { display: block; font-weight: 600; margin-bottom: 0.35rem; }
        input { border: 1px solid #9ca3af; border-radius: 0.25rem; box-sizing: border-box; padding: 0.65rem; width: 100%; }
        button { background: #1f2937; border: 0; border-radius: 0.25rem; color: white; cursor: pointer; padding: 0.65rem 1rem; }
        button:disabled { cursor: wait; opacity: 0.7; }
        .error { color: #b91c1c; }
      
        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            font-family: sans-serif;
            color: #1f2937;
            background:
                radial-gradient(circle at 16% 21%, rgba(129, 143, 248, 0.35), transparent 25rem),
                radial-gradient(circle at 85% 80%, rgba(56, 189, 248, 0.28), transparent 25rem),
                linear-gradient(135deg, #eef2ff 0%, #f8fafc 50%, #e0f2fe 100%);
            padding: 1.5rem;
        }
    </style>
</head>
<body>
    <main class="login-card">
        <h1>Log in</h1>
        <form method="POST" action="{{ route('login.store') }}" data-prevent-double-submit>
            @csrf
            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                    <p class="error" role="alert">{{ $message }}</p>
                @enderror
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required autocomplete="current-password">
                @error('password')
                    <p class="error" role="alert">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit">Log in</button>
        </form>
        <p>Need an account? <a href="{{ route('register') }}">Register</a></p>
    </main>
    <script>
        document.querySelectorAll('form[data-prevent-double-submit]').forEach((form) => {
            form.addEventListener('submit', (event) => {
                if (event.submitter instanceof HTMLButtonElement) {
                    event.submitter.disabled = true;
                    event.submitter.textContent = 'Please wait...';
                }
            });
        });
    </script>
</body>
</html>
