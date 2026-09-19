<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'is_active' => true,
        ];
    }

    public function withRole(string $roleName): static
    {
        return $this->state(function () use ($roleName) {
            $role = Role::firstOrCreate(
                ['name' => $roleName],
                ['description' => $roleName]
            );

            return ['role_id' => $role->id];
        });
    }

    public function superAdmin(): static
    {
        return $this->withRole(User::ROLE_SUPER_ADMIN);
    }

    public function admin(): static
    {
        return $this->withRole(User::ROLE_ADMIN);
    }

    public function guru(): static
    {
        return $this->withRole(User::ROLE_GURU);
    }

    public function siswa(?StudentClass $class = null): static
    {
        return $this->withRole(User::ROLE_SISWA)->state(function () use ($class) {
            return [
                'nisn' => fake()->numerify('##########'),
                'class_id' => $class?->id ?? StudentClass::factory(),
            ];
        });
    }

    public function proktor(): static
    {
        return $this->withRole(User::ROLE_PROKTOR);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
