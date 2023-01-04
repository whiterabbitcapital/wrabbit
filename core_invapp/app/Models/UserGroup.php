<?php

namespace App\Models;

use App\Filters\Filterable;
use App\Enums\UserGroupStatus;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    use Filterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'label',
        'color',
        'desc',
        'status',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_group_term', 'group_id', 'user_id');
    }

    public function scopeWithoutHiddenGroup($query)
    {
        return $query->where('status', '<>', UserGroupStatus::HIDE);
    }
}
