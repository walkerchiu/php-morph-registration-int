<?php

namespace WalkerChiu\MorphRegistration\Models\Forms;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use WalkerChiu\Core\Models\Forms\FormRequest;

class RegistrationFormRequest extends FormRequest
{
    /**
     * @Override Illuminate\Foundation\Http\FormRequest::getValidatorInstance
     */
    protected function getValidatorInstance()
    {
        $request = Request::instance();
        $data = $this->all();
        if (
            $request->isMethod('put')
            && empty($data['id'])
            && isset($request->id)
        ) {
            $data['id'] = (int) $request->id;
            $this->getInputSource()->replace($data);
        }

        return parent::getValidatorInstance();
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return Array
     */
    public function attributes()
    {
        return [
            'morph_type'            => trans('php-registration::registration.morph_type'),
            'morph_id'              => trans('php-registration::registration.morph_id'),
            'user_id'               => trans('php-registration::registration.user_id'),
            'signup_note'           => trans('php-registration::registration.signup_note'),
            'signup_code'           => trans('php-registration::registration.signup_code'),
            'signup_rule_version'   => trans('php-registration::registration.signup_rule_version'),
            'signup_policy_version' => trans('php-registration::registration.signup_policy_version'),
            'state'                 => trans('php-registration::registration.state'),
            'state_info'            => trans('php-registration::registration.state_info'),
            'rule_version'          => trans('php-registration::registration.rule_version'),
            'policy_version'        => trans('php-registration::registration.policy_version')
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return Array
     */
    public function rules()
    {
        $rules = [
            'morph_type'            => 'required_with:morph_id|string',
            'morph_id'              => 'required_with:morph_type|integer|min:1',
            'user_id'               => ['required','integer','min:1','exists:'.config('wk-core.table.user').',id'],
            'signup_note'           => 'nullable|string',
            'signup_code'           => '',
            'signup_rule_version'   => '',
            'signup_policy_version' => '',
            'state'                 => 'required|integer|min:0',
            'state_info'            => 'nullable|string',
            'rule_version'          => '',
            'policy_version'        => ''
        ];

        $request = Request::instance();
        if (
            $request->isMethod('put')
            && isset($request->id)
        ) {
            $rules = array_merge($rules, ['id' => ['required','integer','min:1','exists:'.config('wk-core.table.morph-registration.registrations').',id']]);
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return Array
     */
    public function messages()
    {
        return [
            'id.required'              => trans('php-core::validation.required'),
            'id.integer'               => trans('php-core::validation.integer'),
            'id.min'                   => trans('php-core::validation.min'),
            'id.exists'                => trans('php-core::validation.exists'),
            'morph_type.required_with' => trans('php-core::validation.required_with'),
            'morph_type.string'        => trans('php-core::validation.string'),
            'morph_id.required_with'   => trans('php-core::validation.required_with'),
            'morph_id.integer'         => trans('php-core::validation.integer'),
            'morph_id.min'             => trans('php-core::validation.min'),
            'user_id.required'         => trans('php-core::validation.required'),
            'user_id.integer'          => trans('php-core::validation.integer'),
            'user_id.min'              => trans('php-core::validation.min'),
            'user_id.exists'           => trans('php-core::validation.exists'),
            'signup_note.string'       => trans('php-core::validation.string'),
            'state.required'           => trans('php-core::validation.required'),
            'state.integer'            => trans('php-core::validation.integer'),
            'state.min'                => trans('php-core::validation.min'),
            'state_info.string'        => trans('php-core::validation.string')
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after( function ($validator) {
            $data = $validator->getData();
            if (
                isset($data['morph_type'])
                && isset($data['morph_id'])
            ) {
                if (
                    config('wk-registration.onoff.site-cms')
                    && !empty(config('wk-core.class.site-cms.site'))
                    && $data['morph_type'] == config('wk-core.class.site-cms.site')
                ) {
                    $result = DB::table(config('wk-core.table.site-cms.sites'))
                                ->where('id', $data['morph_id'])
                                ->exists();
                    if (!$result)
                        $validator->errors()->add('morph_id', trans('php-core::validation.exists'));
                } elseif (
                    config('wk-registration.onoff.site-mall')
                    && !empty(config('wk-core.class.site-mall.site'))
                    && $data['morph_type'] == config('wk-core.class.site-mall.site')
                ) {
                    $result = DB::table(config('wk-core.table.site-mall.sites'))
                                ->where('id', $data['morph_id'])
                                ->exists();
                    if (!$result)
                        $validator->errors()->add('morph_id', trans('php-core::validation.exists'));
                } elseif (
                    config('wk-registration.onoff.group')
                    && !empty(config('wk-core.class.group.group'))
                    && $data['morph_type'] == config('wk-core.class.group.group')
                ) {
                    $result = DB::table(config('wk-core.table.group.groups'))
                                ->where('id', $data['morph_id'])
                                ->exists();
                    if (!$result)
                        $validator->errors()->add('morph_id', trans('php-core::validation.exists'));
                }
            }
        });
    }
}
