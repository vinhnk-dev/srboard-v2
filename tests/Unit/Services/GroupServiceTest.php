<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\GroupService;
use App\Repositories\GroupRepository;
use Mockery;
use Illuminate\Support\Facades\DB;

class GroupServiceTest extends TestCase
{
    protected $groupRepository;
    protected $groupService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->groupRepository = Mockery::mock(GroupRepository::class);

        // bind mock into container
        $this->app->instance(GroupRepository::class, $this->groupRepository);

        $this->groupService = app(GroupService::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_group_calls_attach_and_assign()
    {
        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(fn ($cb) => $cb());

        $data = [
            'group_name' => 'Group A',
            'user_group_id' => [1, 2],
            'group_assign_id' => [10, 20],
        ];

        $group = (object) ['id' => 1];

        $this->groupRepository
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($group);

        $this->groupRepository
            ->shouldReceive('attachUsersToGroup')
            ->once()
            ->with(1, [1, 2]);

        $this->groupRepository
            ->shouldReceive('assignProjectsToGroup')
            ->once()
            ->with(1, [10, 20]);

        $result = $this->groupService->create($data);

        $this->assertSame($group, $result);
    }

    public function test_create_group_does_not_attach_when_no_data()
    {
         DB::shouldReceive('transaction')
        ->once()
        ->andReturnUsing(fn ($cb) => $cb());

        $data = ['group_name' => 'Group B'];
        $group = (object) ['id' => 2];

        $this->groupRepository
        ->shouldReceive('create')
        ->once()
        ->andReturn($group);
        // if (!empty($data['user_group_id'])) 
        $this->groupRepository->shouldNotReceive('attachUsersToGroup');
        $this->groupRepository->shouldNotReceive('assignProjectsToGroup');

        $result = $this->groupService->create($data);

        $this->assertSame($group, $result);
    }

    public function test_update_returns_null_when_group_not_found()
    {
        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(fn ($cb) => $cb());

        $this->groupRepository
            ->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn(null);

        $this->groupRepository->shouldNotReceive('update');
        $this->groupRepository->shouldNotReceive('updateUserGroupLinks');
        $this->groupRepository->shouldNotReceive('updateGroupAssignmentLinks');

        $result = $this->groupService->update(1, []);

        $this->assertNull($result);
    }

    public function test_update_group_calls_replace_links()
    {
        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(fn ($cb) => $cb());

        $group = (object) ['id' => 1];

        $data = [
            'group_name' => 'Updated',
            'user_group_id' => [1, 2],
            'group_assign_id' => [3],
        ];

        $this->groupRepository
            ->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($group);

        $this->groupRepository
            ->shouldReceive('update')
            ->once()
            ->with(1, $data)
            ->andReturn($group);

        // CHỈ cần đảm bảo service GỌI method này
        $this->groupRepository
            ->shouldReceive('updateUserGroupLinks')
            ->once()
            ->with(1, [1, 2]);

        $this->groupRepository
            ->shouldReceive('updateGroupAssignmentLinks')
            ->once()
            ->with(1, [3]);

        $result = $this->groupService->update(1, $data);

        $this->assertSame($group, $result);
    }


}