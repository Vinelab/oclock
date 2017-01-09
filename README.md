# OClock
Explore the details of scheduled events in a Laravel application.

### What is this?
There are times when you have too many schedules registered to run at different times,
be it in the same application or multiple applications, i.e. in a micro-services architecture
where multiple services have their work scheduled to perform different events at different times,
it becomes crucial to have one central place that can navigate these scheduled events from
across the different applications (services, projects, etc.). The way OClock achieves this is as follows:

#### Definitions
- **Source**: The application/project where the events have been defined and scheduled.
- **Event**: Is a scheduled event that includes the command and Cron expression that defines its running schedule.
- **Session**: A single run of a scheduled Event. i.e. if an Event is scheduled to run every five minutes
each of these runs throughout the day is a Session.
- **Schedule**: A collection of Events with their Source information.

1- Send the session output to a file where we can read them later
2- Set the `before` hook on each of the events to store their session information in the database every time they start.
3- Set the `after` hook on each of the events to:
    - update their session information and mark the session as done, meanwhile the running status flag `is_running` will be marked as `true`.
    - Read the session output from the file and add it to the Session record in the database
    - Remove the file

## Requirements

- [MongoDB Extension](http://php.net/manual/en/mongodb.installation.pecl.php)

## Installation
```
composer require vinelab/oclock
```

## Configuration
- Add the service provider `OClock\OClockServiceProvider::class,` to the `providers` array in `config/app.php`
- Add the mongodb configuration to `config/database.php` as follows:

```php
'default' => env('DB_CONNECTION', 'mongodb'),
```

```php
'connections' => [

    'mongodb' => [
        'driver'   => 'mongodb',
        'host'     => env('DB_HOST', 'localhost'),
        'port'     => env('DB_PORT', 27017),
        'database' => env('DB_DATABASE', 'logs'),
        'username' => env('DB_USERNAME'),
        'password' => env('DB_PASSWORD'),
        'options' => [
            'database' => 'admin' // sets the authentication database required by mongo 3
        ]
    ],
]
```

- Update `.env` file with the correct parameters.

## Usage

### Setup
- In the `schedule` method of `app/Console/Kernel.php` add the following to the bottom (after all the schedules have been defined):
```php
protected function schedule(Schedule $schedule)
{
    // register schedules here

    // ...

    OClock::register($schedule);
}
```
Make sure you import the facade by adding `use OClock;` to the top of the file.

### Methods
- `OClock::sessions`: Get all the stored sessions
- `OClock::sessionsByDay`: Get all the sessions grouped by day
- `OClock::sessionsForEvent($eventId)`: Get the sessions for the given event
