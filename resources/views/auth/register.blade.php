<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body { color: #1f2937; font-family: system-ui, sans-serif; margin: 0 auto; max-width: 32rem; padding: 2rem 1rem; }
        .field { margin: 1rem 0; }
        label { display: block; font-weight: 600; margin-bottom: 0.35rem; }
        input { border: 1px solid #9ca3af; border-radius: 0.25rem; box-sizing: border-box; padding: 0.65rem; width: 100%; }
        button { background: #1f2937; border: 0; border-radius: 0.25rem; color: white; cursor: pointer; padding: 0.65rem 1rem; }
        button:disabled { cursor: wait; opacity: 0.7; }
        .error { color: #b91c1c; }
        .hint { color: #6b7280; }
    </style>
</head>
<body>
    <main>
        <h1>Create an account</h1>
        <form method="POST" action="{{ route('register.store') }}" data-prevent-double-submit>
            @csrf
            <div class="field">
                <label for="name">Name</label>
                <input id="name" name="name" value="{{ old('name') }}" required maxlength="255" autocomplete="name">
                @error('name')
                    <p class="error" role="alert">{{ $message }}</p>
                @enderror
            </div>
            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email">
                @error('email')
                    <p class="error" role="alert">{{ $message }}</p>
                @enderror
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required minlength="7" autocomplete="new-password">
                <p class="hint">Use at least 7 characters with uppercase and lowercase letters, a number, and a symbol.</p>
                @error('password')
                    <p class="error" role="alert">{{ $message }}</p>
                @enderror
            </div>
            <div class="field">
                <label for="password_confirmation">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">
            </div>
            <button type="submit">Register</button>
        </form>
        <p>Already registered? <a href="{{ route('login') }}">Log in</a></p>
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
