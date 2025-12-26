<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Status;
use App\Services\StatusService;
use App\Repositories\StatusRepository;
use Mockery;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StatusServiceTest extends TestCase
{
    use RefreshDatabase;
    private StatusService $service;
    private StatusRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new StatusRepository();
        $this->service = new StatusService($this->repository);
    }

    /** @test */
    public function it_can_create_status_with_valid_data()
    {
        $data = [
            'status_name' => 'New Status',
            'is_check_due' => 1,
            'color' => '#d92e3b'
        ];

        $status = $this->service->create($data);

        $this->assertNotNull($status);
        $this->assertEquals('New Status', $status->status_name);
        $this->assertEquals(1, $status->is_check_due);
        $this->assertEquals('#d92e3b',$status->color);
        $this->assertDatabaseHas('statuses', [
            'status_name' => 'New Status',
        ]);
    }
    /** @test */
    public function it_creates_status_with_default_is_check_due()
    {
        $data = [
            'status_name' => 'Status Without Due',
            'color' => '#d92e3b'
        ];

        $status = $this->service->create($data);

        $this->assertEquals(0, $status->is_check_due);
    }

    /** @test */
    public function it_can_update_existing_status()
    {
        $status = Status::factory()->create([
            'status_name' => 'Old Name',
            'is_check_due' => 0,
            'color' => '#d92e3b'
        ]);

        // Update
        $updateData = [
            'status_name' => 'Updated Name',
            'is_check_due' => 1,
            'color' => '#e7460d'
        ];

        $updated = $this->service->update($status->id, $updateData);

        $this->assertTrue($updated);
        $this->assertDatabaseHas('statuses', [
            'id' => $status->id,
            'status_name' => 'Updated Name',
            'is_check_due' => 1,
            'color' => '#e7460d'
        ]);
    }

    public function it_returns_false_when_updating_non_existent_status()
    {
        $result = $this->service->update(999, ['status_name' => 'Test']);

        $this->assertFalse($result);
    }

    public function it_throws_exception_when_creating_with_invalid_data()
    {
        $this->expectException(\Exception::class);

        $this->service->create([
            'status_name' => '',
            'color' => ''
        ]);
    }

    public function it_rolls_back_transaction_on_failure()
    {
        // Mock repository để throw exception
        $mockRepo = \Mockery::mock(StatusRepository::class);
        $mockRepo->shouldReceive('create')
                ->andThrow(new \Exception('Database error'));

        $service = new StatusService($mockRepo);

        try {
            $service->create(['status_name' => 'Test','color' => '#e7460d']);
        } catch (\Exception $e) {
            $this->assertEquals('Database error', $e->getMessage());
        }

        $this->assertDatabaseMissing('statuses', [
            'status_name' => 'Test',
            'color' => '#e7460d'
        ]);
    }


 /** @test */
    public function created_status_can_be_referenced_by_id()
    {
        $status = $this->service->create([
            'status_name' => 'Pending',
            'color' => '#FFFF00',
            'is_check_due' => 1,
        ]);

        $this->assertIsInt($status->id);
        $this->assertGreaterThan(0, $status->id);
    }

    /** @test */
    public function it_returns_correct_base_url()
    {
        $baseUrl = $this->service->getBaseUrl();

        $this->assertEquals('admin.status', $baseUrl);
    }

    /** @test */
    public function it_allows_multiple_statuses_with_same_name()
    {
        $status1 = $this->service->create([
            'status_name' => 'In Progress',
            'color' => '#FF0000',
            'is_check_due' => 0,
        ]);

        $status2 = $this->service->create([
            'status_name' => 'In Progress',
            'color' => '#00FF00', // khác màu
            'is_check_due' => 1,
        ]);

        $this->assertNotEquals($status1->id, $status2->id);
        $this->assertEquals($status1->name, $status2->name);
        $this->assertDatabaseCount('statuses', 2);
    }

    /** @test */
    public function it_allows_multiple_statuses_with_same_color()
    {
        $status1 = $this->service->create([
            'status_name' => 'Done',
            'color' => '#00FF00',
            'is_check_due' => 0,
        ]);

        $status2 = $this->service->create([
            'status_name' => 'Completed',
            'color' => '#00FF00',
            'is_check_due' => 0,
        ]);

        $this->assertNotEquals($status1->id, $status2->id);
        $this->assertEquals($status1->color, $status2->color);
    }
}