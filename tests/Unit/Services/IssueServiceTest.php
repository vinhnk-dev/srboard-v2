<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\IssueService;
use App\Repositories\IssueRepository;
use App\Repositories\UserRepository;
use App\Repositories\ProjectRepository;
use App\Services\IssueMailService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Auth\AuthManager;
use Mockery\MockInterface;

class IssueServiceTest extends TestCase
{
    protected $issueService;
    protected $issueRepository;
    protected $userRepository;
    protected $projectRepository;
    protected $issueMailService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mocks
        $this->issueRepository = Mockery::mock(IssueRepository::class);
        $this->userRepository = Mockery::mock(UserRepository::class);
        $this->projectRepository = Mockery::mock(ProjectRepository::class);
        $this->issueMailService = Mockery::mock(IssueMailService::class);

        // Initialize service with mocks
        $this->issueService = new IssueService(
            $this->issueRepository,
            $this->userRepository,
            $this->projectRepository,
            $this->issueMailService
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_returns_correct_base_url()
    {
        $result = $this->issueService->getBaseUrl();
        
        $this->assertEquals('admin.issue', $result);
    }

    #[Test]
    public function it_renders_page_left_tools_for_agile_theme()
    {
        // Mock request
        request()->merge(['parentid' => 123]);

        $result = $this->issueService->pageLeftTools('agile');

        $this->assertStringContainsString('Grid Board', $result);
        $this->assertStringContainsString('btn-danger', $result);
        $this->assertStringContainsString('disabled', $result);
        $this->assertStringContainsString('/projects/123/issues?theme=issues', $result);
    }

    #[Test]
    public function it_renders_page_left_tools_for_issues_theme()
    {
        request()->merge(['parentid' => 456]);

        $result = $this->issueService->pageLeftTools('issues');

        $this->assertStringContainsString('Agile board', $result);
        $this->assertStringContainsString('btn-danger', $result);
        $this->assertStringContainsString('/projects/456/issues?theme=agile', $result);
    }

    #[Test]
    public function it_gets_all_users_assigned()
    {
        $expectedUsers = collect([
            (object)['id' => 1, 'name' => 'User 1'],
            (object)['id' => 2, 'name' => 'User 2']
        ]);

        $this->issueRepository
            ->shouldReceive('usersAssigned')
            ->once()
            ->andReturn($expectedUsers);

        $result = $this->issueService->getAllUsersAssigned();

        $this->assertEquals($expectedUsers, $result);
    }

    #[Test]
    public function it_gets_user_assign_with_selected_marked()
    {
        $issueId = 1;
        $selectedUserIds = [2, 3];
        $users = collect([
            (object)['id' => 1, 'name' => 'User 1'],
            (object)['id' => 2, 'name' => 'User 2'],
            (object)['id' => 3, 'name' => 'User 3']
        ]);

        $this->issueRepository
            ->shouldReceive('getUserAssign')
            ->with($issueId)
            ->once()
            ->andReturn($selectedUserIds);

        $this->userRepository
            ->shouldReceive('all')
            ->once()
            ->andReturn($users);

        $result = $this->issueService->getUserAssign($issueId);

        $this->assertEquals('', $result[0]->active);
        $this->assertEquals('selected', $result[1]->active);
        $this->assertEquals('selected', $result[2]->active);
    }

    #[Test]
    public function it_gets_user_reporter_with_selected_marked()
    {
        $issueId = 1;
        $selectedUserIds = [1];
        $users = collect([
            (object)['id' => 1, 'name' => 'Reporter 1'],
            (object)['id' => 2, 'name' => 'Reporter 2']
        ]);

        $this->issueRepository
            ->shouldReceive('getUserReporter')
            ->with($issueId)
            ->once()
            ->andReturn($selectedUserIds);

        $this->userRepository
            ->shouldReceive('all')
            ->once()
            ->andReturn($users);

        $result = $this->issueService->getUserReporter($issueId);

        $this->assertEquals('selected', $result[0]->active);
        $this->assertEquals('', $result[1]->active);
    }

    #[Test]
    public function it_gets_statuses_for_project()
    {
        $projectId = 5;
        $expectedStatuses = collect([
            (object)['id' => 1, 'name' => 'Open'],
            (object)['id' => 2, 'name' => 'Closed']
        ]);

        $this->issueRepository
            ->shouldReceive('getStatuses')
            ->with($projectId)
            ->once()
            ->andReturn($expectedStatuses);

        $result = $this->issueService->getStatuses($projectId);

        $this->assertEquals($expectedStatuses, $result);
    }

    #[Test]
    public function it_gets_images()
    {
        $issueId = 10;
        $expectedImages = collect(['image1.jpg', 'image2.jpg']);

        $this->issueRepository
            ->shouldReceive('getPitures')
            ->with($issueId)
            ->once()
            ->andReturn($expectedImages);

        $result = $this->issueService->getImages($issueId);

        $this->assertEquals($expectedImages, $result);
    }

    #[Test]
    public function it_gets_issue_assigned()
    {
        $issueId = 15;
        $expectedAssigned = collect([
            (object)['id' => 1, 'name' => 'Assignee 1']
        ]);

        $this->issueRepository
            ->shouldReceive('issueAssigned')
            ->with($issueId, true)
            ->once()
            ->andReturn($expectedAssigned);

        $result = $this->issueService->getIssueAssigned($issueId);

        $this->assertEquals($expectedAssigned, $result);
    }

    #[Test]
    public function it_gets_reporter()
    {
        $issueId = 20;
        $expectedReporters = collect([
            (object)['id' => 1, 'name' => 'Reporter 1']
        ]);

        $this->issueRepository
            ->shouldReceive('getReporter')
            ->with($issueId, true)
            ->once()
            ->andReturn($expectedReporters);

        $result = $this->issueService->getReporter($issueId);

        $this->assertEquals($expectedReporters, $result);
    }

    #[Test]
    public function it_gets_issue_comments()
    {
        $issueId = 25;
        $expectedComments = collect([
            (object)['id' => 1, 'content' => 'Comment 1']
        ]);

        $this->issueRepository
            ->shouldReceive('issueComments')
            ->with($issueId)
            ->once()
            ->andReturn($expectedComments);

        $result = $this->issueService->getIssueComments($issueId);

        $this->assertEquals($expectedComments, $result);
    }

    #[Test]
    public function it_creates_issue_successfully()
    {
        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        Auth::shouldReceive('id')
            ->once()
            ->andReturn(1);

        $data = [
            'title' => 'Test Issue',
            'description' => 'Test Description',
            'user_assign' => [2, 3],
            'report_assign' => [4],
            'pic_url' => ['old1.jpg'],
            'picture_url' => ['new1.jpg']
        ];

        $createdIssue = (object)[
            'id' => 100,
            'title' => 'Test Issue',
            'description' => 'Test Description'
        ];

        $this->issueRepository
            ->shouldReceive('create')
            ->once()
            ->andReturn($createdIssue);

        $this->issueRepository
            ->shouldReceive('updateAssigned')
            ->with(100, [2, 3])
            ->once();

        $this->issueRepository
            ->shouldReceive('updateReporter')
            ->with(100, [4])
            ->once();

        $this->issueRepository
            ->shouldReceive('updatePictures')
            ->with(100, ['old1.jpg'], ['new1.jpg'])
            ->once();

        $this->issueRepository
            ->shouldReceive('load_full_display_detail')
            ->with($createdIssue)
            ->once();

        $this->issueMailService
            ->shouldReceive('sendCreatedMail')
            ->with(100)
            ->once();

        $result = $this->issueService->create($data);

        $this->assertEquals(100, $result->id);
        $this->assertEquals('Test Issue', $result->title);
    }

    #[Test]
    public function it_creates_issue_without_pictures()
    {
        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        Auth::shouldReceive('id')
            ->once()
            ->andReturn(1);

        $data = [
            'title' => 'Test Issue',
            'user_assign' => [2],
            'report_assign' => [3]
        ];

        $createdIssue = (object)[
            'id' => 101,
            'title' => 'Test Issue'
        ];

        $this->issueRepository
            ->shouldReceive('create')
            ->once()
            ->andReturn($createdIssue);

        $this->issueRepository
            ->shouldReceive('updateAssigned')
            ->with(101, [2])
            ->once();

        $this->issueRepository
            ->shouldReceive('updateReporter')
            ->with(101, [3])
            ->once();

        $this->issueRepository
            ->shouldReceive('load_full_display_detail')
            ->with($createdIssue)
            ->once();

        $this->issueMailService
            ->shouldReceive('sendCreatedMail')
            ->with(101)
            ->once();

        $result = $this->issueService->create($data);

        $this->assertEquals(101, $result->id);
    }

    #[Test]
    public function it_updates_issue_successfully()
    {
        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) use (&$contents, &$updateIssue) {
                return $callback();
            });

        Auth::shouldReceive('id')->andReturn(1);
        Auth::shouldReceive('user')->andReturn(
            Mockery::mock(User::class)->shouldIgnoreMissing()
        );


        $issueId = 50;
        $data = [
            'title' => 'Updated Title',
            'user_assign' => [5],
            'report_assign' => [6],
            'pic_url' => ['kept.jpg'],
            'picture_url' => ['new.jpg']
        ];

        $oldIssue = (object)[
            'id' => 50,
            'title' => 'Old Title',
            'url' => 'http://test.com/50',
            'status_name' => 'Open',
            'due_date' => '2024-01-01',
            'project_name' => 'Project A',
            'reporters_toString' => 'Reporter 1',
            'assignments_toString' => 'User 1',
            'issue_description' => 'Old description'
        ];

        $updatedIssue = (object)[
            'id' => 50,
            'title' => 'Updated Title',
            'url' => 'http://test.com/50',
            'status_name' => 'Open',
            'due_date' => '2024-01-01',
            'project_name' => 'Project A',
            'reporters_toString' => 'Reporter 1',
            'assignments_toString' => 'User 1',
            'issue_description' => 'Old description'
        ];

        $this->issueRepository
            ->shouldReceive('find')
            ->with($issueId)
            ->twice()
            ->andReturn($oldIssue, $updatedIssue);

        $this->issueRepository
            ->shouldReceive('update')
            ->once()
            ->andReturn(true);

        $this->issueRepository
            ->shouldReceive('updateAssigned')
            ->with(50, [5])
            ->once();

        $this->issueRepository
            ->shouldReceive('updateReporter')
            ->with(50, [6])
            ->once();

        $this->issueRepository
            ->shouldReceive('updatePictures')
            ->with(50, ['kept.jpg'], ['new.jpg'])
            ->once();

        $this->issueRepository
            ->shouldReceive('load_full_display_detail')
            ->with($updatedIssue)
            ->once();

        $this->issueRepository
            ->shouldReceive('createUpdateComment')
            ->once();

        $this->issueMailService
            ->shouldReceive('sendUpdatedMail')
            ->once();

        $result = $this->issueService->update($issueId, $data);

        $this->assertNotNull($result);
        $this->assertEquals('Updated Title', $result->title);
    }

    #[Test]
    public function it_returns_null_when_update_fails()
    {
        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $issueId = 60;
        $data = ['title' => 'Failed Update'];

        $oldIssue = (object)['id' => 60, 'title' => 'Old'];

        $this->issueRepository
            ->shouldReceive('find')
            ->with($issueId)
            ->once()
            ->andReturn($oldIssue);

        $this->issueRepository
            ->shouldReceive('update')
            ->once()
            ->andReturn(false);

        $result = $this->issueService->update($issueId, $data);

        $this->assertNull($result);
    }

    #[Test]
    public function it_updates_status_successfully()
    {
        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        Auth::shouldReceive('id')
            ->once()
            ->andReturn(1);

        $issueId = 70;
        $newStatus = ['status_id' => 2];

        $oldIssue = (object)[
            'id' => 70,
            'title' => 'Issue Title',
            'url' => 'http://test.com/70',
            'status_name' => 'Open',
            'due_date' => '2024-01-01',
            'project_name' => 'Project A',
            'reporters_toString' => 'Reporter 1',
            'assignments_toString' => 'User 1',
            'issue_description' => 'Description'
        ];

        $updatedIssue = (object)[
            'id' => 70,
            'title' => 'Issue Title',
            'url' => 'http://test.com/70',
            'status_name' => 'Closed',
            'due_date' => '2024-01-01',
            'project_name' => 'Project A',
            'reporters_toString' => 'Reporter 1',
            'assignments_toString' => 'User 1',
            'issue_description' => 'Description'
        ];

        $this->issueRepository
            ->shouldReceive('getIssueAndStatus')
            ->with($issueId)
            ->twice()
            ->andReturn($oldIssue, $updatedIssue);

        $this->issueRepository
            ->shouldReceive('load_full_display_detail')
            ->twice();

        $this->issueRepository
            ->shouldReceive('update')
            ->with($issueId, $newStatus)
            ->once()
            ->andReturn(true);

        $this->issueRepository
            ->shouldReceive('createUpdateComment')
            ->once();

        $this->issueRepository
            ->shouldReceive('enrichIssueForResponse')
            ->once();

        $this->issueMailService
            ->shouldReceive('sendUpdatedMail')
            ->once();

        $result = $this->issueService->updateStatus($issueId, $newStatus);

        $this->assertNotNull($result);
        $this->assertEquals('Closed', $result->status_name);
    }

    #[Test]
    public function it_creates_comment_successfully()
    {
        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        Auth::shouldReceive('id')
            ->once()
            ->andReturn(1);

        $data = [
            'issue_id' => 80,
            'content' => 'Test Comment'
        ];

        $createdComment = (object)[
            'id' => 1,
            'content' => 'Test Comment',
            'user_id' => 1
        ];

        $this->issueRepository
            ->shouldReceive('comment')
            ->once()
            ->andReturn($createdComment);

        $result = $this->issueService->comment($data);

        $this->assertEquals('Test Comment', $result->content);
        $this->assertEquals(1, $result->user_id);
    }

    #[Test]
    public function it_updates_sort_index()
    {
        request()->merge(['position' => [1 => 0, 2 => 1, 3 => 2]]);

        $this->issueRepository
            ->shouldReceive('updateSortIndex')
            ->with([1 => 0, 2 => 1, 3 => 2])
            ->once()
            ->andReturn(true);

        $result = $this->issueService->updateSortIndex();

        $this->assertTrue($result);
    }

    #[Test]
    public function it_force_deletes_issue()
    {
        $issueId = 90;

        $this->issueRepository
            ->shouldReceive('forceDelete')
            ->with($issueId)
            ->once()
            ->andReturn(true);

        $result = $this->issueService->forceDelete($issueId);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_handles_empty_user_assign_in_create()
    {
        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        Auth::shouldReceive('id')
            ->once()
            ->andReturn(1);

        $data = [
            'title' => 'Test Issue',
            'report_assign' => [4]
        ];

        $createdIssue = (object)[
            'id' => 100,
            'title' => 'Test Issue'
        ];

        $this->issueRepository
            ->shouldReceive('create')
            ->once()
            ->andReturn($createdIssue);

        $this->issueRepository
            ->shouldReceive('updateReporter')
            ->with(100, [4])
            ->once();

        $this->issueRepository
            ->shouldReceive('load_full_display_detail')
            ->once();

        $this->issueMailService
            ->shouldReceive('sendCreatedMail')
            ->with(100)
            ->once();

        $result = $this->issueService->create($data);

        $this->assertEquals(100, $result->id);
    }

    #[Test]
    public function it_sanitizes_data_for_admin_user()
    {
        Auth::shouldReceive('user->hasRole')
            ->with('Admin')
            ->once()
            ->andReturn(true);

        $data = [
            'title' => 'Test',
            'pic_url' => ['test.jpg'],
            'picture_url' => ['new.jpg'],
            'user_id' => 999,
            'project_id' => 5
        ];

        // Use reflection to test private method
        $reflection = new \ReflectionClass($this->issueService);
        $method = $reflection->getMethod('sanitize');
        $method->setAccessible(true);

        $result = $method->invoke($this->issueService, $data);

        $this->assertArrayNotHasKey('pic_url', $result);
        $this->assertArrayNotHasKey('picture_url', $result);
        $this->assertArrayNotHasKey('user_id', $result);
        $this->assertArrayHasKey('project_id', $result);
    }

    #[Test]
    public function it_sanitizes_data_for_non_admin_user()
    {
        Auth::shouldReceive('user->hasRole')
            ->with('Admin')
            ->once()
            ->andReturn(false);

        $data = [
            'title' => 'Test',
            'project_id' => 5,
            'pic_url' => ['test.jpg']
        ];

        // Use reflection to test private method
        $reflection = new \ReflectionClass($this->issueService);
        $method = $reflection->getMethod('sanitize');
        $method->setAccessible(true);

        $result = $method->invoke($this->issueService, $data);

        $this->assertArrayNotHasKey('project_id', $result);
        $this->assertArrayNotHasKey('pic_url', $result);
    }

    #[Test]
    public function it_compares_issues_and_generates_change_content()
    {
        $before = (object)[
            'title' => 'Old Title',
            'url' => 'http://test.com/1',
            'status_name' => 'Open',
            'due_date' => '2024-01-01',
            'project_name' => 'Project A',
            'reporters_toString' => 'Reporter 1',
            'assignments_toString' => 'User 1',
            'issue_description' => 'Old description'
        ];

        $after = (object)[
            'title' => 'New Title',
            'url' => 'http://test.com/1',
            'status_name' => 'Closed',
            'due_date' => '2024-01-01',
            'project_name' => 'Project A',
            'reporters_toString' => 'Reporter 1',
            'assignments_toString' => 'User 1',
            'issue_description' => 'Old description'
        ];

        // Use reflection to test private method
        $reflection = new \ReflectionClass($this->issueService);
        $method = $reflection->getMethod('compare');
        $method->setAccessible(true);

        $result = $method->invoke($this->issueService, $before, $after);

        $this->assertStringContainsString('Title:', $result);
        $this->assertStringContainsString('Old Title', $result);
        $this->assertStringContainsString('New Title', $result);
        $this->assertStringContainsString('Status:', $result);
        $this->assertStringContainsString('Open', $result);
        $this->assertStringContainsString('Closed', $result);
    }

    #[Test]
    public function it_compares_issues_with_description_change()
    {
        $before = (object)[
            'title' => 'Title',
            'url' => 'http://test.com/1',
            'status_name' => 'Open',
            'due_date' => '2024-01-01',
            'project_name' => 'Project A',
            'reporters_toString' => 'Reporter 1',
            'assignments_toString' => 'User 1',
            'issue_description' => 'Old description'
        ];

        $after = (object)[
            'title' => 'Title',
            'url' => 'http://test.com/1',
            'status_name' => 'Open',
            'due_date' => '2024-01-01',
            'project_name' => 'Project A',
            'reporters_toString' => 'Reporter 1',
            'assignments_toString' => 'User 1',
            'issue_description' => 'New description'
        ];

        // Use reflection to test private method
        $reflection = new \ReflectionClass($this->issueService);
        $method = $reflection->getMethod('compare');
        $method->setAccessible(true);

        $result = $method->invoke($this->issueService, $before, $after);

        $this->assertStringContainsString('Issue description was changed', $result);
    }

    #[Test]
    public function it_prepares_issue_data_with_auth_user_id()
    {
        Auth::shouldReceive('id')
            ->once()
            ->andReturn(42);

        $data = [
            'title' => 'Test',
            'user_id' => 999
        ];

        // Use reflection to test private method
        $reflection = new \ReflectionClass($this->issueService);
        $method = $reflection->getMethod('prepareIssueData');
        $method->setAccessible(true);

        $result = $method->invoke($this->issueService, $data);

        $this->assertEquals(42, $result['user_id']);
        $this->assertEquals('Test', $result['title']);
    }
}
