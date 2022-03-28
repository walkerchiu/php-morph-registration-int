<?php

namespace WalkerChiu\MorphRegistration\Models\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use WalkerChiu\Core\Models\Entities\DateTrait;

class Registration extends Model
{
    use DateTrait;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var Array
     */
    protected $fillable = [
        'morph_type', 'morph_id',
        'user_id',
        'signup_note', 'signup_code', 'signup_rule_version', 'signup_policy_version',
        'state', 'state_info',
        'rule_version', 'policy_version'
    ];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var Array
	 */
    protected $hidden = [
        'deleted_at'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var Array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];



    /**
     * Create a new instance.
     *
     * @param Array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->table = config('wk-core.table.morph-registration.registrations');

        parent::__construct($attributes);
    }

    /**
     * Get the owning commentable model.
     */
    public function morph()
    {
        return $this->morphTo();
    }

    /**
     * @return Bool
     */
    public function isInitial(): bool
    {
        return $this->state == 0;
    }

    /**
     * @return Bool
     */
    public function isVerifying(): bool
    {
        return $this->state == 1;
    }

    /**
     * @return Bool
     */
    public function isConfirming(): bool
    {
        return $this->state == 2;
    }

    /**
     * @return Bool
     */
    public function isConfirmed(): bool
    {
        return $this->state == 3;
    }

    /**
     * @return Bool
     */
    public function isRejected(): bool
    {
        return $this->state == 4;
    }

    /**
     * @return Bool
     */
    public function isBanned(): bool
    {
        return $this->state == 5;
    }

    /**
     * @return Bool
     */
    public function canModify(): bool
    {
        return in_array($this->state, config('wk-morph-registration.states_can_modify'));
    }
}
