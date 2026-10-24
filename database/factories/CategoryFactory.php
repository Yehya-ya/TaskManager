<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Override;

/**
 * @extends Factory<Category>
 */
final class CategoryFactory extends Factory
{
    #[Override]
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'title' => fake()->word(),
            'description' => fake()->sentence(),
            'project_id' => Project::factory(),
        ];
    }
}
