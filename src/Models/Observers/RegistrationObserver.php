<?php

namespace WalkerChiu\MorphRegistration\Models\Observers;

class RegistrationObserver
{
    /**
     * Handle the model "retrieved" event.
     *
     * @param Model  $model
     * @return void
     */
    public function retrieved($model)
    {
        //
    }

    /**
     * Handle the model "creating" event.
     *
     * @param Model  $model
     * @return void
     */
    public function creating($model)
    {
        //
    }

    /**
     * Handle the model "created" event.
     *
     * @param Model  $model
     * @return void
     */
    public function created($model)
    {
        //
    }

    /**
     * Handle the model "updating" event.
     *
     * @param Model  $model
     * @return void
     */
    public function updating($model)
    {
        //
    }

    /**
     * Handle the model "updated" event.
     *
     * @param Model  $model
     * @return void
     */
    public function updated($model)
    {
        //
    }

    /**
     * Handle the model "saving" event.
     *
     * @param Model  $model
     * @return void
     */
    public function saving($model)
    {
        if (
            config('wk-core.class.morph-registration.registration')
                ::where('id', '<>', $model->id)
                ->where('morph_type', $model->morph_type)
                ->where('morph_id', $model->morph_id)
                ->where('user_id', $model->user_id)
                ->exists()
        )
            return false;
    }

    /**
     * Handle the model "saved" event.
     *
     * @param Model  $model
     * @return void
     */
    public function saved($model)
    {
        //
    }

    /**
     * Handle the model "deleting" event.
     *
     * @param Model  $model
     * @return void
     */
    public function deleting($model)
    {
        //
    }

    /**
     * Handle the model "deleted" event.
     *
     * Its Lang will be automatically removed by database.
     *
     * @param Model  $model
     * @return void
     */
    public function deleted($model)
    {
        if (!config('wk-morph-registration.soft_delete')) {
            $model->forceDelete();
        }

        if ($model->isForceDeleting()) {
        }
    }

    /**
     * Handle the model "restoring" event.
     *
     * @param Model  $model
     * @return void
     */
    public function restoring($model)
    {
        //
    }

    /**
     * Handle the model "restored" event.
     *
     * @param Model  $model
     * @return void
     */
    public function restored($model)
    {
        //
    }
}
