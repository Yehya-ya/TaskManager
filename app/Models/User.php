<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Hidden([
    'password',
    'remember_token',
])]
final class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function memberIn(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, Member::class)->using(Member::class);
    }

    /**
     * Get a query for the projects the user owns or is a member of.
     *
     * @return Builder<Project>
     */
    public function visibleProjects(): Builder
    {
        return Project::query()->where(fn (Builder $query): Builder => $query
            ->whereBelongsTo($this, 'owner')
            ->orWhereRelation('members', 'user_id', $this->id));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
