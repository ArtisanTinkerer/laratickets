
## About Laraticket

* Laraticket creates and processes random tickets.
* Built using Laravel 8, PHP 7.3.
* Developed on Laravel Sail.


## Configuration 
Ensure that you set the MAX_TICKETS environment variable, this will stop the commands looping forever.

## Commands
### To create tickets ```php artisan tickets:create```
### To process tickets ```php artisan tickets:process```

## Endpoints

|                  |                                                                                    |
|------------------|------------------------------------------------------------------------------------|
| GET /unprocessed | Returns a paginated list of all unprocessed tickets.                               |
| GET /processed       | Returns a paginated list of all processed tickets.                                 |
| GET /for-user        | Returns a paginated list of all tickets for a user with the supplied email address |
| GET /stats           | Returns the stats for the system                                                   |

## In the real world.

I would probably use cron/Laravel Scheduler to create/process the tickets and the commands would just
switch flags off and on.

Would refactor some of the code from the controller out to a service or repository.


