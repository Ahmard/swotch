<?php
declare(strict_types=1);

namespace Swotch;


use Swoole\Event;

class Watcher extends AbstractEvent
{
    protected mixed $inotifyFD;
    protected array $watchAnyEvents = [
        2, # IN_MODIFY
        192, # IN_MOVED
        256, # IN_CREATE
        512, # IN_DELETE
    ];


    public static function watch(string|array $path): Watcher
    {
        return new Watcher($path);
    }

    public function getInotifyFD(): mixed
    {
        if (!isset($this->inotifyFD)) {
            $this->inotifyFD = inotify_init();
        }

        return $this->inotifyFD;
    }

    public function __construct(string|array $path)
    {
        parent::__construct();

        if (is_array($path)) {
            foreach ($path as $aPath) {
                $this->addPath($aPath, Watcher::ON_ALL_EVENTS);
            }
        }

        // Setup a new event listener for inotify read events
        $previousMask = [0, 1, time()];
        Event::add($this->getInotifyFD(), function () use (&$previousMask) {
            $events = inotify_read($this->getInotifyFD());

            // IF WE ARE LISTENING TO 'ON_ALL_EVENTS'
            if (
                $this->willWatchAny
                && (
                    in_array($events[0]['mask'], $this->watchAnyEvents)
                    || $events[0]['mask'] > Watcher::ON_ISDIR
                )
                && time() > $previousMask[2] + 1
            ) {
                $this->eventEmitter->emit('ON_ALL_EVENTS', [$events]);
                $previousMask = [$events[0]['mask'], $events[0]['wd'], time()];
            }
        });

        // Set to monitor and listen for read events for the given $fd
        Event::set(
            $this->getInotifyFD(),
            null,
            null,
            SWOOLE_EVENT_READ
        );
    }

    public function addPath(string $path, ?int $mask = null): Watcher
    {
        inotify_add_watch($this->getInotifyFD(), $path, $mask ?? Watcher::ON_ALL_EVENTS);
        return $this;
    }
}