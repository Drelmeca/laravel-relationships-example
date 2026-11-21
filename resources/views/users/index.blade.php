<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users and Relationships</title>
    <style>
        body {
            color: #1f2937;
            font-family: system-ui, sans-serif;
            margin: 0 auto;
            max-width: 900px;
            padding: 2rem 1rem;
        }

        h1, h2, h3 {
            margin-top: 0;
        }

        .user {
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            margin: 1rem 0;
            padding: 1.25rem;
        }

        .create-user {
            background: #f9fafb;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            margin: 1rem 0 2rem;
            padding: 1.25rem;
        }

        .field {
            margin: 1rem 0;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.35rem;
        }

        input {
            border: 1px solid #9ca3af;
            border-radius: 0.25rem;
            box-sizing: border-box;
            max-width: 100%;
            padding: 0.55rem;
            width: 24rem;
        }

        button {
            background: #1f2937;
            border: 0;
            border-radius: 0.25rem;
            color: white;
            cursor: pointer;
            padding: 0.65rem 1rem;
        }

        .relationship {
            margin-top: 1.25rem;
        }

        .muted {
            color: #6b7280;
        }

        .status {
            color: #166534;
        }

        .error {
            color: #b91c1c;
        }

        button:disabled {
            cursor: wait;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <main>
        <h1>Users and Relationships</h1>

        @if (session('status'))
            <p class="status" role="status">{{ session('status') }}</p>
        @endif

        <section class="create-user">
            <h2>Add a user</h2>
            <form method="POST" action="{{ route('users.store') }}" data-prevent-double-submit>
                @csrf
                <div class="field">
                    <label for="name">Name</label>
                    <input id="name" name="name" value="{{ old('name') }}" required maxlength="255">
                    @error('name')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required maxlength="255">
                    @error('email')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit">Create user</button>
            </form>
        </section>

        <h2>Saved users</h2>
        @forelse ($users as $user)
            <article class="user">
                <h2>{{ $user->name }}</h2>
                <p>{{ $user->email }}</p>

                <section class="relationship">
                    <h3>Profile</h3>
                    @if ($user->profile)
                        <p>{{ $user->profile->bio ?: 'No bio provided.' }}</p>
                        @if ($user->profile->website)
                            <p>Website: {{ $user->profile->website }}</p>
                        @endif
                    @else
                        <p class="muted">No profile.</p>
                    @endif
                </section>

                <section class="relationship">
                    <h3>Posts</h3>
                    @forelse ($user->posts as $post)
                        <article>
                            <h4>{{ $post->title }}</h4>
                            <p>{{ $post->body }}</p>
                        </article>
                    @empty
                        <p class="muted">No posts.</p>
                    @endforelse
                </section>

                <section class="relationship">
                    <h3>Roles</h3>
                    @forelse ($user->roles as $role)
                        <span>{{ $role->name }}</span>@if (!$loop->last), @endif
                    @empty
                        <p class="muted">No roles.</p>
                    @endforelse
                </section>
            </article>
        @empty
            <p class="muted">No users found. Add users to the database to see their relationships.</p>
        @endforelse

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Log out</button>
        </form>
    </main>
    <script>
        document.querySelectorAll('form[data-prevent-double-submit]').forEach((form) => {
            form.addEventListener('submit', (event) => {
                if (event.submitter instanceof HTMLButtonElement) {
                    event.submitter.disabled = true;
                    event.submitter.textContent = 'Saving...';
                }
            });
        });
    </script>
</body>
</html>
