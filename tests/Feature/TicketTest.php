<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    //testing something that runs every 1 or 5 minutes is tricky
    //which is probably because the structure is wrong
    //the commands could call a service which  could then
    //in the real world I would use the Laravel task scheduling (not sleep()!)

    /** @test */
    public function minute_ticket_is_created()
    {
        $this->artisan('tickets:create')->assertExitCode(0);

        $this->assertDatabaseCount('tickets', config('tickets.max-tickets'));
    }

    /** @test */
    public function default_ticket_not_processed()
    {
        $ticket = Ticket::factory()
            ->create();

        $this->assertDatabaseHas('tickets', [
                'id'     => $ticket->id,
                'status' => false,
            ]
        );
    }

    /** @test */
    public function tickets_can_be_processed()
    {
        $ticket = Ticket::factory()
            ->unprocessed()
            ->create();

        $this->artisan('tickets:process')
            ->assertExitCode(0);

        $this->assertDatabaseHas('tickets', [
                'id'     => $ticket->id,
                'status' => 1,
            ]
        );
    }

    /** @test */
    public function can_get_all_unprocessed()
    {
        $numberUnprocessed = rand(1, 50);
        $numberProcessed   = rand(1, 50);

        $unprocessedTickets = Ticket::factory($numberUnprocessed)
            ->unprocessed()
            ->create();

        $processedTickets = Ticket::factory($numberProcessed)
            ->processed()
            ->create();

        $response = $this->getJson(route('tickets.unprocessed'))
            ->assertSuccessful();

        $response->assertJsonMissing([
            'id' => $processedTickets->first()->id,
        ]);
        //could check detail with fluent json

    }

    /** @test */
    public function can_get_all_processed()
    {
        $numberUnprocessed = rand(1, 50);
        $numberProcessed   = rand(1, 50);

        $unprocessedTickets = Ticket::factory($numberUnprocessed)
            ->unprocessed()
            ->create();

        $processedTickets = Ticket::factory($numberProcessed)
            ->processed()
            ->create();

        $response = $this->getJson(route('tickets.processed'))
            ->assertSuccessful();

        $response->assertJsonMissing([
            'id' => $unprocessedTickets->first()->id,
        ]);
    }

    /** @test */
    public function can_get_tickets_for_user()
    {
        $unwantedTickets = Ticket::factory(rand(1, 25))
            ->create();

        $name = $this->faker->name;

        $wantedTicket = Ticket::factory()
            ->create([
                'user_name' => $name,
            ]);

        $params = ['email' => $wantedTicket->user_email];

        //could use fluent json here
        $response = $this->getJson(route('tickets.for-user', $params))
            ->assertSuccessful();

        $response->assertJsonFragment(['id' => $wantedTicket->id]);
    }

    /** @test */
    public function can_get_stats()
    {
        $numberUnprocessed = rand(1, 50);
        $numberProcessed   = rand(1, 50);

        $unprocessedTickets = Ticket::factory($numberUnprocessed)
            ->unprocessed()
            ->create();

        $processedTickets = Ticket::factory($numberProcessed)
            ->processed()
            ->create();

        $email = $this->faker->email;
        $name  = $this->faker->name;

        $frequentUserTickets = Ticket::factory(rand(5, 10))
            ->create([
                'user_email' => $email,
                'user_name'  => $name,

            ]);

        $response = $this->getJson(route('tickets.stats'))
            ->assertSuccessful();

        $time = $processedTickets->sortBy('updated_at')
            ->first()
            ->updated_at
            ->toTimeString();

        $response->assertJson(function (AssertableJson $json) use ($name, $time) {
            return $json
                ->where('total', Ticket::all()->count())
                ->where(
                    'unprocessed',
                    Ticket::where('status', '=', 0)
                        ->count()
                )
                ->where('highest-frequency-name', $name)
                ->where('last-processed', $time);
        });
    }

    //what if none created
    /** @test */
    public function check ()
    {
        $response = $this->getJson(route('tickets.stats'))
            ->assertSuccessful();

    }

}
