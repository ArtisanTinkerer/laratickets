<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use Illuminate\Console\Command;
use Database\Factories\TicketFactory;

class ProcessTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process a ticket every five minutes.';

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
     * Process a ticket every 5 minutes.
     *
     * @return int
     */
    public function handle()
    {
        $maxTickets = config('tickets.max-tickets');

        for ($i=0; $i < $maxTickets; $i++) {
            $nextTicket = Ticket::where('status','=',false)
                ->orderByDesc('created_at')
                ->first();

            if ($nextTicket){
                $nextTicket->status = true;
                $nextTicket->save();
                $this->info('Ticket Processed');
            }

            sleep(60*5);
        }

        return 0;
    }
}
