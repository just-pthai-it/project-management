<?php

namespace Tests\Feature;

use App\Events\SystemObjectEvent;
use App\Events\UsersAssignedEvent;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create ()
    {
        Event::fake();

        $currentUser = User::factory()->create();

        $input = [
            'name'          => 'name',
            'customer_name' => 'customer_name',
            'code'          => 'code',
            'summary'       => 'summary',
            'starts_at'     => '2023/05/01',
            'ends_at'       => '2023/07/01',
            'duration'      => 3,
            'status_id'     => ProjectStatus::STATUS_NOT_START,
            'user_ids'      => [1, 2, 3],
        ];

        $response = $this->actingAs($currentUser, 'web')
                         ->withHeaders(['Accept' => 'application/json',])
                         ->post('api/projects', $input);

        Event::assertDispatched(SystemObjectEvent::class);
        Event::assertDispatched(UsersAssignedEvent::class);

        $response->assertStatus(201);
    }

    public function test_update ()
    {
        Event::fake();

        $currentUser = User::factory()->create();

        $input = [
            'name'          => 'name',
            'customer_name' => 'customer_name',
            'code'          => 'code',
            'summary'       => 'summary',
            'starts_at'     => '2023/05/01',
            'ends_at'       => '2023/07/01',
            'duration'      => 3,
            'status_id'     => ProjectStatus::STATUS_NOT_START,
            'user_ids'      => [1, 2, 3],
        ];

        $response = $this->actingAs($currentUser, 'web')
                         ->withHeaders(['Accept' => 'application/json',])
                         ->patch('api/projects/1', $input);

        Event::assertDispatched(SystemObjectEvent::class);
        Event::assertDispatched(UsersAssignedEvent::class);

        $response->assertStatus(200);
    }

    public function test_delete ()
    {
        Event::fake();

        $project = Project::query()->find(1);

        $currentUser = User::factory()->create();
        $response    = $this->actingAs($currentUser, 'web')
                            ->withHeaders(['Accept' => 'application/json',])
                            ->delete('api/projects/1');


        $project->refresh();
        $this->assertSoftDeleted($project);
        $response->assertStatus(200);
    }
}
