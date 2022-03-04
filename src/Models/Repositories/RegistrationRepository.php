<?php

namespace WalkerChiu\MorphRegistration\Models\Repositories;

use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Forms\FormTrait;
use WalkerChiu\Core\Models\Repositories\Repository;
use WalkerChiu\Core\Models\Repositories\RepositoryTrait;
use WalkerChiu\Core\Models\Services\PackagingFactory;

class RegistrationRepository extends Repository
{
    use FormTrait;
    use RepositoryTrait;

    protected $instance;



    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->instance = App::make(config('wk-core.class.morph-registration.registration'));
    }

    /**
     * @param Array  $data
     * @param Bool   $auto_packing
     * @return Array|Collection|Eloquent
     */
    public function list(array $data, $auto_packing = false)
    {
        $instance = $this->instance;

        $data = array_map('trim', $data);
        $repository = $instance->when($data, function ($query, $data) {
                                    return $query->unless(empty($data['id']), function ($query) use ($data) {
                                                return $query->where('id', $data['id']);
                                            })
                                            ->unless(empty($data['morph_type']), function ($query) use ($data) {
                                                return $query->where('morph_type', $data['morph_type']);
                                            })
                                            ->unless(empty($data['morph_id']), function ($query) use ($data) {
                                                return $query->where('morph_id', $data['morph_id']);
                                            })
                                            ->unless(empty($data['user_id']), function ($query) use ($data) {
                                                return $query->where('user_id', $data['user_id']);
                                            })
                                            ->unless(empty($data['signup_note']), function ($query) use ($data) {
                                                return $query->where('signup_note', 'LIKE', "%".$data['signup_note']."%");
                                            })
                                            ->unless(empty($data['signup_code']), function ($query) use ($data) {
                                                return $query->where('signup_code', 'LIKE', "%".$data['signup_code']."%");
                                            })
                                            ->unless(empty($data['signup_rule_version']), function ($query) use ($data) {
                                                return $query->where('signup_rule_version', 'LIKE', "%".$data['signup_rule_version']."%");
                                            })
                                            ->unless(empty($data['signup_policy_version']), function ($query) use ($data) {
                                                return $query->where('signup_policy_version', 'LIKE', "%".$data['signup_policy_version']."%");
                                            })
                                            ->unless(empty($data['state']), function ($query) use ($data) {
                                                return $query->where('state', $data['state']);
                                            })
                                            ->unless(empty($data['state_info']), function ($query) use ($data) {
                                                return $query->where('state_info', 'LIKE', "%".$data['state_info']."%");
                                            })
                                            ->unless(empty($data['rule_version']), function ($query) use ($data) {
                                                return $query->where('rule_version', 'LIKE', "%".$data['rule_version']."%");
                                            })
                                            ->unless(empty($data['policy_version']), function ($query) use ($data) {
                                                return $query->where('policy_version', 'LIKE', "%".$data['policy_version']."%");
                                            });
                                })
                                ->orderBy('updated_at', 'DESC');

        if ($auto_packing) {
            $factory = new PackagingFactory(config('wk-morph-registration.output_format'), config('wk-morph-registration.pagination.pageName'), config('wk-morph-registration.pagination.perPage'));
            return $factory->output($repository);
        }

        return $repository;
    }

    /**
     * @param Registration  $instance
     * @return Array
     */
    public function show($instance): array
    {
        if (empty($instance))
            return [
                'id'                    => '',
                'morph_type'            => '',
                'morph_id'              => '',
                'user_id'               => '',
                'signup_note'           => '',
                'signup_code'           => '',
                'signup_rule_version'   => '',
                'signup_policy_version' => '',
                'state'                 => '',
                'state_info'            => '',
                'rule_version'          => '',
                'policy_version'        => ''
            ];

        $this->setmodel($instance);

        return [
              'id'                    => $instance->id,
              'morph_type'            => $instance->morph_type,
              'morph_id'              => $instance->morph_id,
              'user_id'               => $instance->user_id,
              'signup_note'           => $instance->signup_note,
              'signup_code'           => $instance->signup_code,
              'signup_rule_version'   => $instance->signup_rule_version,
              'signup_policy_version' => $instance->signup_policy_version,
              'state'                 => $instance->state,
              'state_info'            => $instance->state_info,
              'rule_version'          => $instance->rule_version,
              'policy_version'        => $instance->policy_version
        ];
    }
}
