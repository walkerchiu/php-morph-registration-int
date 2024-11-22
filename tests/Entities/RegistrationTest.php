<?php

namespace WalkerChiu\MorphRegistration;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use WalkerChiu\MorphRegistration\Models\Entities\Registration;

class RegistrationTest extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ .'/../migrations');
        $this->withFactories(__DIR__ .'/../../src/database/factories');
    }

    /**
     * To load your package service provider, override the getPackageProviders.
     *
     * @param \Illuminate\Foundation\Application  $app
     * @return Array
     */
    protected function getPackageProviders($app)
    {
        return [\WalkerChiu\Core\CoreServiceProvider::class,
                \WalkerChiu\MorphRegistration\MorphRegistrationServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
    }

    /**
     * A basic functional test on Registration.
     *
     * For WalkerChiu\MorphRegistration\Models\Entities\Registration
     * 
     * @return void
     */
    public function testRegistration()
    {
        // Config
        Config::set('wk-core.onoff.core-lang_core', 0);
        Config::set('wk-morph-registration.onoff.core-lang_core', 0);
        Config::set('wk-core.lang_log', 1);
        Config::set('wk-morph-registration.lang_log', 1);
        Config::set('wk-core.soft_delete', 1);
        Config::set('wk-morph-registration.soft_delete', 1);

        $faker = \Faker\Factory::create();

        $user_id_1 = 1;
        $user_id_2 = 2;
        $user_id_3 = 3;
        DB::table(config('wk-core.table.user'))->insert([
            [
                'id'       => $user_id_1,
                'name'     => $faker->username,
                'email'    => $faker->email,
                'password' => $faker->password
            ],[
                'id'       => $user_id_2,
                'name'     => $faker->username,
                'email'    => $faker->email,
                'password' => $faker->password
            ],[
                'id'       => $user_id_3,
                'name'     => $faker->username,
                'email'    => $faker->email,
                'password' => $faker->password
            ]
        ]);

        $group_id = 1;
        DB::table(config('wk-core.table.group.groups'))->insert([
            'id'         => $group_id,
            'serial'     => $faker->username,
            'identifier' => $faker->slug,
            'is_enabled' => 1
        ]);

        // Give
        $db_morph_1 = factory(Registration::class)->create(['user_id' => $user_id_1, 'morph_id' => $group_id, 'morph_type' => config('wk-core.class.group.group')]);
        $db_morph_2 = factory(Registration::class)->create(['user_id' => $user_id_2, 'morph_id' => $group_id, 'morph_type' => config('wk-core.class.group.group')]);
        $db_morph_3 = factory(Registration::class)->create(['user_id' => $user_id_3, 'morph_id' => $group_id, 'morph_type' => config('wk-core.class.group.group')]);

        // Get records after creation
            // When
            $records = Registration::all();
            // Then
            $this->assertCount(3, $records);

        // Delete someone
            // When
            $db_morph_2->delete();
            $records = Registration::all();
            // Then
            $this->assertCount(2, $records);

        // Resotre someone
            // When
            Registration::withTrashed()
                        ->find($db_morph_2->id)
                        ->restore();
            $record_2 = Registration::find($db_morph_2->id);
            $records = Registration::all();
            // Then
            $this->assertNotNull($record_2);
            $this->assertCount(3, $records);
    }
}
