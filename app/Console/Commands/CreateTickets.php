<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use Illuminate\Console\Command;
use Database\Factories\TicketFactory;

class CreateTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a dummy ticket every minute.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create a random ticket every 1 minute.
     *
     * @return int
     */
    public function handle()
    {
        $maxTickets = config('tickets.max-tickets');

        //This would be better with a scheduled command which I can stop/start
        for ($i=0; $i < $maxTickets; $i++) {
            $ticket = Ticket::factory()
                ->create([
                    'status' => false
                ]);
            $this->info('Ticket Created');
            sleep(60);

        }

        return 0;
    }
}
