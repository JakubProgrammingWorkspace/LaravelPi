<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Many-to-many: A role belongs to many users.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_role');
    }

    /**
     * Get all users with this role.
     */
    public function usersWithRole(): BelongsToMany
    {
        return $this->users();
    }
}
