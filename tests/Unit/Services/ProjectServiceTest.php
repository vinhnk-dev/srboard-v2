<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\ProjectService;
use App\Repositories\ProjectRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mockery;


class ProjectServiceTest extends TestCase
{

    protected ProjectRepository $projectRepository;
    protected ProjectService $projectService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->projectRepository = Mockery::mock(ProjectRepository::class);
        $this->projectService = new ProjectService($this->projectRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_project_status_maps_active_and_check_correctly()
    {
        $projectId = 1;

        $projectStatuses = collect([
            (object)[
                'status_id' => 2,
                'show' => true,
            ],
        ]);

        $statuses = collect([
            (object)['id' => 1],
            (object)['id' => 2],
        ]);

        $this->projectRepository
            ->shouldReceive('getProjectStatusesByProjectId')
            ->once()
            ->with($projectId)
            ->andReturn($projectStatuses);

        $this->projectRepository
            ->shouldReceive('getAllStatuses')
            ->once()
            ->andReturn($statuses);

        $result = $this->projectService->getProjectStatus($projectId);

        $this->assertEquals('', $result[0]->active);
        $this->assertEquals('', $result[0]->check);

        $this->assertEquals('selected', $result[1]->active);
        $this->assertEquals('checked', $result[1]->check);
    }

    public function test_create_project_with_groups_and_statuses()
    {
        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(fn ($cb) => $cb());

        Auth::shouldReceive('id')
            ->once()
            ->andReturn(10);

        $data = [
            'name' => 'Project A',
            'group_assignment_id' => [1, 2],
            'status_id' => [3, 4],
        ];

        $project = (object)['id' => 99];

        $this->projectRepository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($arg) {
                return $arg['user_id'] === 10;
            }))
            ->andReturn($project);

        $this->projectRepository
            ->shouldReceive('assignGroups')
            ->once()
            ->with(99, [1, 2]);

        $this->projectRepository
            ->shouldReceive('addStatuses')
            ->once()
            ->with(99, [3, 4]);

        $result = $this->projectService->create($data);

        $this->assertSame($project, $result);
    }

    public function test_create_project_without_optional_relationships()
    {
        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(fn ($cb) => $cb());

        Auth::shouldReceive('id')->once()->andReturn(1);

        $data = ['name' => 'Project B'];
        $project = (object)['id' => 1];

        $this->projectRepository
            ->shouldReceive('create')
            ->once()
            ->andReturn($project);

        $this->projectRepository->shouldReceive('assignGroups')->never();
        $this->projectRepository->shouldReceive('addStatuses')->never();

        $result = $this->projectService->create($data);

        $this->assertSame($project, $result);
    }

    public function test_update_project_with_groups_and_statuses()
    {
        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(fn ($cb) => $cb());

        $project = (object)['id' => 5];

        $data = [
            'name' => 'Updated',
            'group_assignment_id' => [1],
            'status_id' => [2],
            'show' => [2 => true],
        ];

        $this->projectRepository
            ->shouldReceive('update')
            ->once()
            ->with(5, $data)
            ->andReturn($project);

        $this->projectRepository
            ->shouldReceive('updateGroupAssignments')
            ->once()
            ->with(5, [1]);

        $this->projectRepository
            ->shouldReceive('updateProjectStatuses')
            ->once()
            ->with(5, [2], [2 => true]);

        $result = $this->projectService->update(5, $data);

        $this->assertSame($project, $result);
    }

    public function test_update_returns_null_when_project_not_found()
    {
        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(fn ($cb) => $cb());

        $this->projectRepository
            ->shouldReceive('update')
            ->once()
            ->andReturn(null);

        $this->projectRepository->shouldReceive('updateGroupAssignments')->never();
        $this->projectRepository->shouldReceive('updateProjectStatuses')->never();

        $result = $this->projectService->update(1, []);

        $this->assertNull($result);
    }

    public function test_force_delete_relationship()
    {
        $this->projectRepository
            ->shouldReceive('forcesDeleteRelationship')
            ->once()
            ->with(10)
            ->andReturn(true);

        $result = $this->projectService->forcesDeleteRelationship(10);

        $this->assertTrue($result);
    }
}
