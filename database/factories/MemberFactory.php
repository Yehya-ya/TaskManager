<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Member;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
final class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'project_id' => Project::factory(),
            'user_id' => null,
        ];
    }

    public function withUser(): self
    {
        return $this->state(function (array $attributes): array {
            $user = User::factory()->create();

            return [
                'email' => $user->email,
                'user_id' => $user->id,
            ];
        });
    }

    public function forEmail(string $email): self
    {
        return $this->state(fn (array $attributes): array => [
            'email' => $email,
            'user_id' => User::query()->where('email', $email)->value('id'),
        ]);
    }
}
