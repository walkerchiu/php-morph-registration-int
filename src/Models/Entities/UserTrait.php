<?php

namespace WalkerChiu\MorphRegistration\Models\Entities;

trait UserTrait
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function registrations($morph_type = null, $morph_id = null)
    {
        return $this->hasMany(config('wk-core.class.morph-registration.registration'), 'user_id', 'id')
                    ->when($morph_type, function ($query, $morph_type) {
                                return $query->where('morph_type', $morph_type);
                            })
                    ->when($morph_id, function ($query, $morph_id) {
                                return $query->where('morph_id', $morph_id);
                            });
    }
}
