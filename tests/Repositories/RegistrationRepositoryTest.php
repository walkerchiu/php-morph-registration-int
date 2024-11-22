<?php

namespace WalkerChiu\MorphRegistration;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use WalkerChiu\MorphRegistration\Models\Entities\Registration;
use WalkerChiu\MorphRegistration\Models\Repositories\RegistrationRepository;

class RegistrationRepositoryTest extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    protected $repository;

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

        $this->repository = $this->app->make(RegistrationRepository::class);
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
     * A basic functional test on RegistrationRepository.
     *
     * For WalkerChiu\Core\Models\Repositories\Repository
     *
     * @return void
     */
    public function testRegistrationRepository()
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
            'serial'     => $faker->username,
            'identifier' => $faker->slug,
            'is_enabled' => 1
        ]);

        // Give
        $id_list = [];
        for ($i=1; $i<=3; $i++) {
            $record = $this->repository->save([
                'morph_type' => config('wk-core.class.group.group'),
                'morph_id'   => $group_id,
                'user_id'    => [$user_id_1, $user_id_2, $user_id_3][$i-1],
                'state'      => 0
            ]);
            array_push($id_list, $record->id);
        }

        // Get and Count records after creation
            // When
            $records = $this->repository->get();
            $count   = $this->repository->count();
            // Then
            $this->assertCount(3, $records);
            $this->assertEquals(3, $count);

        // Find someone
            // When
            $record = $this->repository->first();
            // Then
            $this->assertNotNull($record);

            // When
            $record = $this->repository->find($faker->uuid());
            // Then
            $this->assertNull($record);

        // Delete someone
            // When
            $this->repository->deleteByIds([$id_list[0]]);
            $count = $this->repository->count();
            // Then
            $this->assertEquals(2, $count);

            // When
            $this->repository->deleteByExceptIds([$id_list[2]]);
            $count = $this->repository->count();
            $record = $this->repository->find($id_list[2]);
            // Then
            $this->assertEquals(1, $count);
            $this->assertNotNull($record);

            // When
            $count = $this->repository->where('id', '>', 0)->count();
            // Then
            $this->assertEquals(1, $count);

            // When
            $count = $this->repository->whereWithTrashed('id', '>', 0)->count();
            // Then
            $this->assertEquals(3, $count);

            // When
            $count = $this->repository->whereOnlyTrashed('id', '>', 0)->count();
            // Then
            $this->assertEquals(2, $count);

        // Force delete someone
            // When
            $this->repository->forcedeleteByIds([$id_list[2]]);
            $records = $this->repository->get();
            // Then
            $this->assertCount(0, $records);

        // Restore records
            // When
            $this->repository->restoreByIds([$id_list[0], $id_list[1]]);
            $count = $this->repository->count();
            // Then
            $this->assertEquals(2, $count);
    }

    /**
     * Unit test about Query List on RegistrationRepository.
     *
     * For WalkerChiu\Core\Models\Repositories\RepositoryTrait
     *     WalkerChiu\MorphRegistration\Models\Repositories\RegistrationRepository
     *
     * @return void
     */
    public function testQueryList()
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
        $user_id_4 = 4;
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
            ],[
                'id'       => $user_id_4,
                'name'     => $faker->username,
                'email'    => $faker->email,
                'password' => $faker->password
            ]
        ]);

        $group_id = 1;
        DB::table(config('wk-core.table.group.groups'))->insert([
            'serial'     => $faker->username,
            'identifier' => $faker->slug,
            'is_enabled' => 1
        ]);

        // Give
        $id_list = [];
        for ($i=1; $i<=4; $i++) {
            $record = $this->repository->save([
                'morph_type' => config('wk-core.class.group.group'),
                'morph_id'   => $group_id,
                'user_id'    => [$user_id_1, $user_id_2, $user_id_3, $user_id_4][$i-1],
                'state'      => 0
            ]);
            array_push($id_list, $record->id);
        }

        // Get query
            // When
            sleep(1);
            $this->repository->find($id_list[2])->touch();
            $records = $this->repository->ofNormal()->get();
            // Then
            $this->assertCount(4, $records);

            // When
            $record = $records->first();
            // Then
            $this->assertArrayNotHasKey('deleted_at', $record->toArray());
            $this->assertEquals($id_list[2], $record->id);

        // Get query of trashed records
            // When
            $this->repository->deleteByIds([$id_list[3]]);
            $this->repository->deleteByIds([$id_list[0]]);
            $records = $this->repository->ofTrash()->get();
            // Then
            $this->assertCount(2, $records);

            // When
            $record = $records->first();
            // Then
            $this->assertArrayHasKey('deleted_at', $record);
            $this->assertEquals($id_list[0], $record->id);
    }
}
