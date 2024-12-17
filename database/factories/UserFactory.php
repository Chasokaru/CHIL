<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => $this->getDefaultPassword(),
            'remember_token' => $this->generateRememberToken(),
        ];
    }

    /**
     * Get the default hashed password.
     *
     * @return string
     */
    protected function getDefaultPassword(): string
    {
        return static::$password ??= Hash::make(config('app.default_user_password', 'password'));
    }

    /**
     * Generate a random remember token.
     *
     * @return string
     */
    protected function generateRememberToken(): string
    {
        return Str::random(random_int(8, 16));
    }

    /**
     * Modify the user's attributes based on specific conditions.
     *
     * @param array<string, mixed> $options
     * @return static
     */
    public function configureUser(array $options = []): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => $options['verified'] ?? $attributes['email_verified_at'],
            'roles' => $this->mergeRoles($attributes['roles'] ?? [], $options['roles'] ?? []),
            'is_suspended' => $options['is_suspended'] ?? $attributes['is_suspended'],
            'email' => $this->generateEmail($attributes['email'], $options['email_domain'] ?? null),
        ]);
    }

    /**
     * Merge default roles with additional roles.
     *
     * @param array $existingRoles
     * @param array $newRoles
     * @return array
     */
    protected function mergeRoles(array $existingRoles, array $newRoles): array
    {
        return array_values(array_unique(array_merge($existingRoles, $newRoles)));
    }

    /**
     * Generate a custom email if a domain is provided.
     *
     * @param string $currentEmail
     * @param string|null $domain
     * @return string
     */
    protected function generateEmail(string $currentEmail, ?string $domain): string
    {
        if ($domain) {
            $username = explode('@', $currentEmail)[0];
            return "{$username}@{$domain}";
        }
        return $currentEmail;
    }
}
