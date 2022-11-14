<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ForUserRequest;

class TicketController extends Controller
{
    /**
     * Just return the unprocessed tickets.
     *
     * @return mixed
     */
    public function unprocessed()
    {
        //No need for repository pattern in something this small
        $tickets = Ticket::where('status', '=', 0)
            ->paginate(10);

        //Would normally use an API Resource because I have been doing a lot of API work
        return $tickets;
    }

    /**
     * Just return the processed tickets.
     *
     * @return mixed
     */
    public function processed()
    {
        $tickets = Ticket::where('status', '=', 1)
            ->paginate(10);

        return $tickets;
    }

    /**
     * Just return the tickets for this user.
     *
     * @return mixed
     */
    public function forUser(ForUserRequest $request)
    {
        $tickets = Ticket::where('user_email', '=', $request->email)
            ->paginate(10);

        return $tickets;
    }

    /**
     * Return the stats for all tickets.
     *
     * @return array
     */
    public function stats(): array
    {
        // It would be nice to get this in one db hit.
        // Probably wouldn't have all of this in the controller in the
        // real world. As a minimum I would use fat models/skinny controllers
        // or a service class
        // or repository pattern.

        $stats['total'] = Ticket::all()
            ->count();

        $stats['unprocessed'] = Ticket::where('status', '=', 0)
            ->count();

        //would use null safe or coalesc here if I was in php 8
        $frequentUser = DB::table('tickets')
            ->select(DB::raw("count(user_email) as frequency, user_name"))
            ->groupBy('user_email', 'user_name')
            ->orderBy('frequency', 'desc')
            ->first();

        $stats['highest-frequency-name'] = $frequentUser ? $frequentUser->user_name : null;

        $lastProcessed = Ticket::where('status', '=', 1)
            ->latest('updated_at')
            ->first();

        $stats['last-processed'] = $lastProcessed ? $lastProcessed->updated_at->toTimeString() : null;

        return $stats;
    }
}
