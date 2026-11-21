# Laravel model relationships example

This runnable Laravel application demonstrates:

- One-to-one: `User` has one `OneToOneProfile`
- One-to-many: `User` has many `OneToManyPost`s
- Many-to-many: `User`s have many `ManyToManyRole`s

The foreign key for the one-to-one relationship is `profiles.user_id`, which
is unique to limit each user to one profile. The one-to-many relationship uses
`posts.user_id`. The many-to-many relationship uses the `role_user` pivot
table, with one row per user/role pairing.

## Run the application

Install dependencies and prepare the SQLite database with sample relationship
records:

```sh
composer install
php artisan migrate --seed
```

Start the development server:

```sh
php artisan serve
```

Open `http://127.0.0.1:8000/register` to create an account. Registration
requires a password with at least 7 characters, including uppercase and
lowercase letters, a number, and a symbol. After registering, the app signs
you in and takes you to the protected users page. Existing accounts can sign
in at `/login`; three failed login attempts trigger a one-minute cooldown.
Refreshing after a successful form submission will not submit it again.

To display the same records in the terminal, run:

```sh
php artisan users:show
```

## Displaying the database output

The seeder uses `firstOrCreate` so it can be run again without duplicating its
sample records.

## Example usage

```php
$user = User::with(['profile', 'posts', 'roles'])->findOrFail(1);

$profile = $user->profile; // OneToOneProfile|null
$posts = $user->posts;     // Collection<OneToManyPost>
$roles = $user->roles;     // Collection<ManyToManyRole>

$user->roles()->attach($roleId);
$user->roles()->detach($roleId);
```
