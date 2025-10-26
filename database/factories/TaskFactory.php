<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
final class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'end_at' => fake()->dateTimeBetween('now', '+30 days'),
            'project_id' => Project::factory(),
            'category_id' => Category::factory(),
            'assigned_user' => User::factory(),
        ];
    }

    public function unassigned(): self
    {
        return $this->state(fn (array $attributes): array => [
            'assigned_user' => null,
        ]);
    }

    public function dueToday(): self
    {
        return $this->state(fn (array $attributes): array => [
            'end_at' => now()->endOfDay(),
        ]);
    }

    public function overdue($days = 5): self
    {
        return $this->state(fn (array $attributes): array => [
            'end_at' => now()->subDays($days),
        ]);
    }
}
