<?php

namespace WalkerChiu\MorphRegistration\Models\Entities;

trait RegistrationTrait
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function registration()
    {
        return $this->morphOne(config('wk-core.class.morph-registration.registration'), 'morph');
    }

    /**
     * @param Int  $user_id
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function registrations($user_id = null)
    {
        return $this->morphMany(config('wk-core.class.morph-registration.registration'), 'morph')
                    ->when($user_id, function ($query, $user_id) {
                                return $query->where('user_id', $user_id);
                            });
    }
}
