<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\IssueMailService;
use App\Repositories\IssueRepository;
use App\Repositories\UserRepository;
use App\Jobs\SendMailJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class IssueMailServiceTest extends TestCase
{
    protected $issueMailService;
    protected $issueRepository;
    protected $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mocks
        $this->issueRepository = Mockery::mock(IssueRepository::class);
        $this->userRepository = Mockery::mock(UserRepository::class);

        // Initialize service with mocks
        $this->issueMailService = new IssueMailService(
            $this->issueRepository,
            $this->userRepository
        );

        Queue::fake();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_sends_created_mail_to_single_assignee()
    {
        $issueId = 1;
        
        $issue = (object)[
            'id' => 1,
            'title' => 'Test Issue',
            'project_id' => 10,
            'project_code' => 'PROJ',
        ];

        $this->issueRepository
            ->shouldReceive('find')
            ->with($issueId)
            ->once()
            ->andReturn($issue);

        $this->issueRepository
            ->shouldReceive('getUserAssign')
            ->with($issueId)
            ->once()
            ->andReturn([2]); // assignee user_id = 2

        $this->issueRepository
            ->shouldReceive('getUserReporter')
            ->with($issueId)
            ->once()
            ->andReturn([3]); // reporter user_id = 3

        $this->userRepository
            ->shouldReceive('getUserEmail')
            ->with(2)
            ->once()
            ->andReturn('assignee@test.com');

        $this->userRepository
            ->shouldReceive('getUserEmail')
            ->with(3)
            ->once()
            ->andReturn('reporter@test.com');

        // Mock loadMissing - chainable
        $issue = Mockery::mock($issue);
        $issue->shouldReceive('loadMissing')->with('project:id,project_code')->andReturnSelf();
        $issue->id = 1;
        $issue->title = 'Test Issue';
        $issue->project_id = 10;
        $issue->project_code = 'PROJ';
        
        $this->issueRepository
            ->shouldReceive('find')
            ->with($issueId)
            ->andReturn($issue);

        // Act
        $this->issueMailService->sendCreatedMail($issueId);

        // Assert
        Queue::assertPushed(SendMailJob::class, 2); // 1 assignee + 1 reporter
    }

    #[Test]
    public function it_sends_created_mail_with_developer_title_to_assignee()
    {
        $issueId = 2;
        
        $issue = Mockery::mock('stdClass');
        $issue->shouldReceive('loadMissing')->andReturnSelf();
        $issue->id = 2;
        $issue->title = 'Developer Task';
        $issue->project_id = 20;
        $issue->project_code = 'DEV';

        $this->issueRepository
            ->shouldReceive('find')
            ->with($issueId)
            ->once()
            ->andReturn($issue);

        $this->issueRepository
            ->shouldReceive('getUserAssign')
            ->with($issueId)
            ->once()
            ->andReturn([5]); // only assignee

        $this->issueRepository
            ->shouldReceive('getUserReporter')
            ->with($issueId)
            ->once()
            ->andReturn([]); // no reporter

        $this->userRepository
            ->shouldReceive('getUserEmail')
            ->with(5)
            ->once()
            ->andReturn('developer@test.com');

        // Act
        $this->issueMailService->sendCreatedMail($issueId);

        // Assert
        Queue::assertPushed(SendMailJob::class, 1);
        
        Queue::assertPushed(SendMailJob::class, function ($job) {
            $reflection = new \ReflectionClass($job);
            $contentProperty = $reflection->getProperty('content');
            $contentProperty->setAccessible(true);
            $content = $contentProperty->getValue($job);
            
            return str_contains($content, 'Developer');
        });
    }

    #[Test]
    public function it_sends_created_mail_with_reporter_title()
    {
        $issueId = 3;
        
        $issue = Mockery::mock('stdClass');
        $issue->shouldReceive('loadMissing')->andReturnSelf();
        $issue->id = 3;
        $issue->title = 'Reporter Issue';
        $issue->project_id = 30;
        $issue->project_code = 'REP';

        $this->issueRepository
            ->shouldReceive('find')
            ->with($issueId)
            ->once()
            ->andReturn($issue);

        $this->issueRepository
            ->shouldReceive('getUserAssign')
            ->with($issueId)
            ->once()
            ->andReturn([]); // no assignee

        $this->issueRepository
            ->shouldReceive('getUserReporter')
            ->with($issueId)
            ->once()
            ->andReturn([6]); // only reporter

        $this->userRepository
            ->shouldReceive('getUserEmail')
            ->with(6)
            ->once()
            ->andReturn('reporter@test.com');

        // Act
        $this->issueMailService->sendCreatedMail($issueId);

        // Assert
        Queue::assertPushed(SendMailJob::class, 1);
        
        Queue::assertPushed(SendMailJob::class, function ($job) {
            $reflection = new \ReflectionClass($job);
            $contentProperty = $reflection->getProperty('content');
            $contentProperty->setAccessible(true);
            $content = $contentProperty->getValue($job);
            
            return str_contains($content, 'Reporter');
        });
    }

    #[Test]
    public function it_sends_created_mail_with_both_reporter_and_developer_title()
    {
        $issueId = 4;
        
        $issue = Mockery::mock('stdClass');
        $issue->shouldReceive('loadMissing')->andReturnSelf();
        $issue->id = 4;
        $issue->title = 'Dual Role Issue';
        $issue->project_id = 40;
        $issue->project_code = 'DUAL';

        $this->issueRepository
            ->shouldReceive('find')
            ->with($issueId)
            ->once()
            ->andReturn($issue);

        $this->issueRepository
            ->shouldReceive('getUserAssign')
            ->with($issueId)
            ->once()
            ->andReturn([7]); // user 7 is assignee

        $this->issueRepository
            ->shouldReceive('getUserReporter')
            ->with($issueId)
            ->once()
            ->andReturn([7]); // user 7 is also reporter

        $this->userRepository
            ->shouldReceive('getUserEmail')
            ->with(7)
            ->once()
            ->andReturn('dual@test.com');

        // Act
        $this->issueMailService->sendCreatedMail($issueId);

        // Assert
        Queue::assertPushed(SendMailJob::class, 1); // Same user, only 1 email
        
        Queue::assertPushed(SendMailJob::class, function ($job) {
            $reflection = new \ReflectionClass($job);
            $contentProperty = $reflection->getProperty('content');
            $contentProperty->setAccessible(true);
            $content = $contentProperty->getValue($job);
            
            return str_contains($content, 'Reporter and Developer');
        });
    }

    #[Test]
    public function it_sends_created_mail_to_multiple_users()
    {
        $issueId = 5;
        
        $issue = Mockery::mock('stdClass');
        $issue->shouldReceive('loadMissing')->andReturnSelf();
        $issue->id = 5;
        $issue->title = 'Multi User Issue';
        $issue->project_id = 50;
        $issue->project_code = 'MULTI';

        $this->issueRepository
            ->shouldReceive('find')
            ->with($issueId)
            ->once()
            ->andReturn($issue);

        $this->issueRepository
            ->shouldReceive('getUserAssign')
            ->with($issueId)
            ->once()
            ->andReturn([10, 11, 12]); // 3 assignees

        $this->issueRepository
            ->shouldReceive('getUserReporter')
            ->with($issueId)
            ->once()
            ->andReturn([13, 14]); // 2 reporters

        // Setup email mocks for all users
        $this->userRepository
            ->shouldReceive('getUserEmail')
            ->with(10)
            ->once()
            ->andReturn('assignee1@test.com');

        $this->userRepository
            ->shouldReceive('getUserEmail')
            ->with(11)
            ->once()
            ->andReturn('assignee2@test.com');

        $this->userRepository
            ->shouldReceive('getUserEmail')
            ->with(12)
            ->once()
            ->andReturn('assignee3@test.com');

        $this->userRepository
            ->shouldReceive('getUserEmail')
            ->with(13)
            ->once()
            ->andReturn('reporter1@test.com');

        $this->userRepository
            ->shouldReceive('getUserEmail')
            ->with(14)
            ->once()
            ->andReturn('reporter2@test.com');

        // Act
        $this->issueMailService->sendCreatedMail($issueId);

        // Assert - 5 emails should be sent
        Queue::assertPushed(SendMailJob::class, 5);
    }

    #[Test]
    public function it_sends_created_mail_with_correct_subject()
    {
        $issueId = 6;
        
        $issue = Mockery::mock('stdClass');
        $issue->shouldReceive('loadMissing')->andReturnSelf();
        $issue->id = 6;
        $issue->title = 'Subject Test Issue';
        $issue->project_id = 60;
        $issue->project_code = 'SUBJ';

        $this->issueRepository
            ->shouldReceive('find')
            ->with($issueId)
            ->once()
            ->andReturn($issue);

        $this->issueRepository
            ->shouldReceive('getUserAssign')
            ->with($issueId)
            ->once()
            ->andReturn([20]);

        $this->issueRepository
            ->shouldReceive('getUserReporter')
            ->with($issueId)
            ->once()
            ->andReturn([]);

        $this->userRepository
            ->shouldReceive('getUserEmail')
            ->with(20)
            ->once()
            ->andReturn('test@test.com');


        Queue::fake();

        $this->issueMailService->sendCreatedMail($issueId);

        Queue::assertPushed(SendMailJob::class);

    }

    #[Test]
    public function it_sends_created_mail_with_issue_link()
    {
        $issueId = 7;
        
        $issue = Mockery::mock('stdClass');
        $issue->shouldReceive('loadMissing')->andReturnSelf();
        $issue->id = 7;
        $issue->title = 'Link Test Issue';
        $issue->project_id = 70;
        $issue->project_code = 'LINK';

        $this->issueRepository
            ->shouldReceive('find')
            ->with($issueId)
            ->once()
            ->andReturn($issue);

        $this->issueRepository
            ->shouldReceive('getUserAssign')
            ->with($issueId)
            ->once()
            ->andReturn([30]);

        $this->issueRepository
            ->shouldReceive('getUserReporter')
            ->with($issueId)
            ->once()
            ->andReturn([]);

        $this->userRepository
            ->shouldReceive('getUserEmail')
            ->with(30)
            ->once()
            ->andReturn('test@test.com');

        // Act
        $this->issueMailService->sendCreatedMail($issueId);

        // Assert
        Queue::assertPushed(SendMailJob::class, function ($job) use ($issue) {
            $reflection = new \ReflectionClass($job);
            $contentProperty = $reflection->getProperty('content');
            $contentProperty->setAccessible(true);
            $content = $contentProperty->getValue($job);
            
            $expectedUrl = '/projects/' . $issue->project_id . '/issues/' . $issue->id . '/view';
            
            return str_contains($content, $expectedUrl) &&
                   str_contains($content, 'Visit issue');
        });
    }

    #[Test]
    public function it_sends_updated_mail_to_assignees_and_reporters()
    {
        $issueId = 10;
        $content = '<p>Status changed from Open to Closed</p>';
        
        $issue = Mockery::mock('stdClass');
        $issue->shouldReceive('loadMissing')->andReturnSelf();
        $issue->id = 10;
        $issue->title = 'Updated Issue';
        $issue->project_id = 100;
        $issue->project_code = 'UPD';

        $this->issueRepository
            ->shouldReceive('find')
            ->with($issueId)
            ->once()
            ->andReturn($issue);

        $this->issueRepository
            ->shouldReceive('getUserAssign')
            ->with($issueId)
            ->once()
            ->andReturn([50]);

        $this->issueRepository
            ->shouldReceive('getUserReporter')
            ->with($issueId)
            ->once()
            ->andReturn([51]);

        $this->userRepository
            ->shouldReceive('getUserEmail')
            ->with(50)
            ->once()
            ->andReturn('assignee@test.com');

        $this->userRepository
            ->shouldReceive('getUserEmail')
            ->with(51)
            ->once()
            ->andReturn('reporter@test.com');

        // Act
        $this->issueMailService->sendUpdatedMail($issueId, $content);

        // Assert
        Queue::assertPushed(SendMailJob::class, 2);
    }

    #[Test]
    public function it_sends_updated_mail_with_change_content()
    {
        $issueId = 11;
        $content = '<p>Title changed from Old to New</p>';
        
        $issue = Mockery::mock('stdClass');
        $issue->shouldReceive('loadMissing')->andReturnSelf();
        $issue->id = 11;
        $issue->title = 'Content Test';
        $issue->project_id = 110;
        $issue->project_code = 'CONT';

        $this->issueRepository
            ->shouldReceive('find')
            ->with($issueId)
            ->once()
            ->andReturn($issue);

        $this->issueRepository
            ->shouldReceive('getUserAssign')
            ->with($issueId)
            ->once()
            ->andReturn([60]);

        $this->issueRepository
            ->shouldReceive('getUserReporter')
            ->with($issueId)
            ->once()
            ->andReturn([]);

        $this->userRepository
            ->shouldReceive('getUserEmail')
            ->with(60)
            ->once()
            ->andReturn('test@test.com');

        // Act
        $this->issueMailService->sendUpdatedMail($issueId, $content);

        // Assert
        Queue::assertPushed(SendMailJob::class, function ($job) use ($content) {
            $reflection = new \ReflectionClass($job);
            $contentProperty = $reflection->getProperty('content');
            $contentProperty->setAccessible(true);
            $emailContent = $contentProperty->getValue($job);
            
            return str_contains($emailContent, $content);
        });
    }

    #[Test]
    public function it_sends_updated_mail_with_correct_subject()
    {
        $issueId = 12;
        $content = '<p>Changes made</p>';
        
        $issue = Mockery::mock('stdClass');
        $issue->shouldReceive('loadMissing')->andReturnSelf();
        $issue->id = 12;
        $issue->title = 'Update Subject Test';
        $issue->project_id = 120;
        $issue->project_code = 'USUBJ';

        $this->issueRepository
            ->shouldReceive('find')
            ->with($issueId)
            ->once()
            ->andReturn($issue);

        $this->issueRepository
            ->shouldReceive('getUserAssign')
            ->with($issueId)
            ->once()
            ->andReturn([70]);

        $this->issueRepository
            ->shouldReceive('getUserReporter')
            ->with($issueId)
            ->once()
            ->andReturn([]);

        $this->userRepository
            ->shouldReceive('getUserEmail')
            ->with(70)
            ->once()
            ->andReturn('test@test.com');

        // Act
       Queue::fake();

        $this->issueMailService->sendUpdatedMail($issueId, $content);

        Queue::assertPushed(SendMailJob::class);

    }

    #[Test]
    public function it_sends_updated_mail_with_developer_title()
    {
        $issueId = 13;
        $content = '<p>Updated</p>';
        
        $issue = Mockery::mock('stdClass');
        $issue->shouldReceive('loadMissing')->andReturnSelf();
        $issue->id = 13;
        $issue->title = 'Dev Update';
        $issue->project_id = 130;
        $issue->project_code = 'UDEV';

        $this->issueRepository
            ->shouldReceive('find')
            ->with($issueId)
            ->once()
            ->andReturn($issue);

        $this->issueRepository
            ->shouldReceive('getUserAssign')
            ->with($issueId)
            ->once()
            ->andReturn([80]);

        $this->issueRepository
            ->shouldReceive('getUserReporter')
            ->with($issueId)
            ->once()
            ->andReturn([]);

        $this->userRepository
            ->shouldReceive('getUserEmail')
            ->with(80)
            ->once()
            ->andReturn('dev@test.com');

        // Act
        $this->issueMailService->sendUpdatedMail($issueId, $content);

        // Assert
        Queue::assertPushed(SendMailJob::class, function ($job) {
            $reflection = new \ReflectionClass($job);
            $contentProperty = $reflection->getProperty('content');
            $contentProperty->setAccessible(true);
            $emailContent = $contentProperty->getValue($job);
            
            return str_contains($emailContent, 'Developer');
        });
    }

    #[Test]
    public function it_sends_updated_mail_with_reporter_title()
    {
        $issueId = 14;
        $content = '<p>Updated</p>';
        
        $issue = Mockery::mock('stdClass');
        $issue->shouldReceive('loadMissing')->andReturnSelf();
        $issue->id = 14;
        $issue->title = 'Reporter Update';
        $issue->project_id = 140;
        $issue->project_code = 'UREP';

        $this->issueRepository
            ->shouldReceive('find')
            ->with($issueId)
            ->once()
            ->andReturn($issue);

        $this->issueRepository
            ->shouldReceive('getUserAssign')
            ->with($issueId)
            ->once()
            ->andReturn([]);

        $this->issueRepository
            ->shouldReceive('getUserReporter')
            ->with($issueId)
            ->once()
            ->andReturn([90]);

        $this->userRepository
            ->shouldReceive('getUserEmail')
            ->with(90)
            ->once()
            ->andReturn('reporter@test.com');

        // Act
        $this->issueMailService->sendUpdatedMail($issueId, $content);

        // Assert
        Queue::assertPushed(SendMailJob::class, function ($job) {
            $reflection = new \ReflectionClass($job);
            $contentProperty = $reflection->getProperty('content');
            $contentProperty->setAccessible(true);
            $emailContent = $contentProperty->getValue($job);
            
            return str_contains($emailContent, 'Reporter');
        });
    }

    #[Test]
    public function it_sends_updated_mail_with_both_roles_title()
    {
        $issueId = 15;
        $content = '<p>Updated</p>';
        
        $issue = Mockery::mock('stdClass');
        $issue->shouldReceive('loadMissing')->andReturnSelf();
        $issue->id = 15;
        $issue->title = 'Dual Role Update';
        $issue->project_id = 150;
        $issue->project_code = 'UDUAL';

        $this->issueRepository
            ->shouldReceive('find')
            ->with($issueId)
            ->once()
            ->andReturn($issue);

        $this->issueRepository
            ->shouldReceive('getUserAssign')
            ->with($issueId)
            ->once()
            ->andReturn([100]);

        $this->issueRepository
            ->shouldReceive('getUserReporter')
            ->with($issueId)
            ->once()
            ->andReturn([100]);

        $this->userRepository
            ->shouldReceive('getUserEmail')
            ->with(100)
            ->once()
            ->andReturn('dual@test.com');

        // Act
        $this->issueMailService->sendUpdatedMail($issueId, $content);

        // Assert
        Queue::assertPushed(SendMailJob::class, function ($job) {
            $reflection = new \ReflectionClass($job);
            $contentProperty = $reflection->getProperty('content');
            $contentProperty->setAccessible(true);
            $emailContent = $contentProperty->getValue($job);
            
            return str_contains($emailContent, 'Reporter and Developer');
        });
    }

    #[Test]
    public function it_sends_updated_mail_to_multiple_users()
    {
        $issueId = 16;
        $content = '<p>Multiple updates</p>';
        
        $issue = Mockery::mock('stdClass');
        $issue->shouldReceive('loadMissing')->andReturnSelf();
        $issue->id = 16;
        $issue->title = 'Multi User Update';
        $issue->project_id = 160;
        $issue->project_code = 'UMULT';

        $this->issueRepository
            ->shouldReceive('find')
            ->with($issueId)
            ->once()
            ->andReturn($issue);

        $this->issueRepository
            ->shouldReceive('getUserAssign')
            ->with($issueId)
            ->once()
            ->andReturn([110, 111]);

        $this->issueRepository
            ->shouldReceive('getUserReporter')
            ->with($issueId)
            ->once()
            ->andReturn([112, 113]);

        $this->userRepository
            ->shouldReceive('getUserEmail')
            ->with(110)
            ->once()
            ->andReturn('assign1@test.com');

        $this->userRepository
            ->shouldReceive('getUserEmail')
            ->with(111)
            ->once()
            ->andReturn('assign2@test.com');

        $this->userRepository
            ->shouldReceive('getUserEmail')
            ->with(112)
            ->once()
            ->andReturn('report1@test.com');

        $this->userRepository
            ->shouldReceive('getUserEmail')
            ->with(113)
            ->once()
            ->andReturn('report2@test.com');

        // Act
        $this->issueMailService->sendUpdatedMail($issueId, $content);

        // Assert
        Queue::assertPushed(SendMailJob::class, 4);
    }

    #[Test]
    public function it_handles_empty_users_list_for_created_mail()
    {
        $issueId = 18;
        
        $issue = Mockery::mock('stdClass');
        $issue->shouldReceive('loadMissing')->andReturnSelf();
        $issue->id = 18;
        $issue->title = 'No Users Issue';
        $issue->project_id = 180;
        $issue->project_code = 'NUSER';

        $this->issueRepository
            ->shouldReceive('find')
            ->with($issueId)
            ->once()
            ->andReturn($issue);

        $this->issueRepository
            ->shouldReceive('getUserAssign')
            ->with($issueId)
            ->once()
            ->andReturn([]); // no assignees

        $this->issueRepository
            ->shouldReceive('getUserReporter')
            ->with($issueId)
            ->once()
            ->andReturn([]); // no reporters

        // Act
        $this->issueMailService->sendCreatedMail($issueId);

        // Assert - no emails sent
        Queue::assertNothingPushed();
    }

    #[Test]
    public function it_handles_empty_users_list_for_updated_mail()
    {
        $issueId = 19;
        $content = '<p>No users update</p>';
        
        $issue = Mockery::mock('stdClass');
        $issue->shouldReceive('loadMissing')->andReturnSelf();
        $issue->id = 19;
        $issue->title = 'No Users Update';
        $issue->project_id = 190;
        $issue->project_code = 'NUPD';

        $this->issueRepository
            ->shouldReceive('find')
            ->with($issueId)
            ->once()
            ->andReturn($issue);

        $this->issueRepository
            ->shouldReceive('getUserAssign')
            ->with($issueId)
            ->once()
            ->andReturn([]);

        $this->issueRepository
            ->shouldReceive('getUserReporter')
            ->with($issueId)
            ->once()
            ->andReturn([]);

        // Act
        $this->issueMailService->sendUpdatedMail($issueId, $content);

        // Assert - no emails sent
        Queue::assertNothingPushed();
    }

    #[Test]
    public function it_includes_project_code_in_created_mail()
    {
        $issueId = 20;
        
        $issue = Mockery::mock('stdClass');
        $issue->shouldReceive('loadMissing')->andReturnSelf();
        $issue->id = 20;
        $issue->title = 'Project Code Test';
        $issue->project_id = 200;
        $issue->project_code = 'TESTCODE';

        $this->issueRepository
            ->shouldReceive('find')
            ->with($issueId)
            ->once()
            ->andReturn($issue);

        $this->issueRepository
            ->shouldReceive('getUserAssign')
            ->with($issueId)
            ->once()
            ->andReturn([130]);

        $this->issueRepository
            ->shouldReceive('getUserReporter')
            ->with($issueId)
            ->once()
            ->andReturn([]);

        $this->userRepository
            ->shouldReceive('getUserEmail')
            ->with(130)
            ->once()
            ->andReturn('test@test.com');

        // Act
        $this->issueMailService->sendCreatedMail($issueId);

        // Assert
        Queue::assertPushed(SendMailJob::class, function ($job) use ($issue) {
            $reflection = new \ReflectionClass($job);
            $contentProperty = $reflection->getProperty('content');
            $contentProperty->setAccessible(true);
            $content = $contentProperty->getValue($job);
            
            return str_contains($content, 'TESTCODE-20');
        });
    }
}
