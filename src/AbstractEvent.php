<?php


namespace Swotch;


use Evenement\EventEmitter;
use JetBrains\PhpStorm\Pure;

abstract class AbstractEvent
{
    public const ON_ACCESS = 1;
    public const ON_MODIFY = 2;
    public const ON_ATTRIB = 4;
    public const ON_CLOSE_WRITE = 8;
    public const ON_CLOSE_NOWRITE = 16;
    public const ON_OPEN = 32;
    public const ON_MOVED_FROM = 64;
    public const ON_MOVED_TO = 128;
    public const ON_CREATE = 256;
    public const ON_DELETE = 512;
    public const ON_DELETE_SELF = 1024;
    public const ON_MOVE_SELF = 2048;
    public const ON_UNMOUNT = 8192;
    public const ON_Q_OVERFLOW = 16384;
    public const ON_IGNORED = 32768;
    public const ON_CLOSE = 24;
    public const ON_MOVE = 192;
    public const ON_ALL_EVENTS = 4095;
    public const ON_ONLYDIR = 16777216;
    public const ON_DONT_FOLLOW = 33554432;
    public const ON_MASK_ADD = 536870912;
    public const ON_ISDIR = 1073741824;
    public const ON_ONESHOT = 2147483648;

    public const ON_CLOSE_NOWRITE_HIGH = 1073741840;
    public const ON_OPEN_HIGH = 1073741856;
    public const ON_CREATE_HIGH = 1073742080;
    public const ON_DELETE_HIGH = 1073742336;

    public const UNKNOWN = 0;

    public static array $constants = [
        0 => ['UNKNOWN', 'Unknown code.'],
        1 => ['ON_ACCESS', 'File was accessed (read)'],
        2 => ['ON_MODIFY', 'File was modified'],
        4 => ['ON_ATTRIB', 'Metadata changed (e.g. permissions, mtime, etc.)'],
        8 => ['ON_CLOSE_WRITE', 'File opened for writing was closed'],
        16 => ['ON_CLOSE_NOWRITE', 'File not opened for writing was closed'],
        32 => ['ON_OPEN', 'File was opened'],
        128 => ['ON_MOVED_TO', 'File moved into watched directory'],
        64 => ['ON_MOVED_FROM', 'File moved out of watched directory'],
        256 => ['ON_CREATE', 'File or directory created in watched directory'],
        512 => ['ON_DELETE', 'File or directory deleted in watched directory'],
        1024 => ['ON_DELETE_SELF', 'Watched file or directory was deleted'],
        2048 => ['ON_MOVE_SELF', 'Watch file or directory was moved'],
        24 => ['ON_CLOSE', 'Equals to ON_CLOSE_WRITE | ON_CLOSE_NOWRITE'],
        192 => ['ON_MOVE', 'Equals to ON_MOVED_FROM | ON_MOVED_TO'],
        4095 => ['ON_ALL_EVENTS', 'Bitmask of all the above constants'],
        8192 => ['ON_UNMOUNT', 'File system containing watched object was unmounted'],
        16384 => ['ON_Q_OVERFLOW', 'Event queue overflowed (wd is -1 for this event)'],
        32768 => ['ON_IGNORED', 'Watch was removed (explicitly by inotify_rm_watch() or because file was removed or filesystem unmounted'],
        1073741824 => ['ON_ISDIR', 'Subject of this event is a directory'],
        1073741840 => ['ON_CLOSE_NOWRITE', 'High-bit: File not opened for writing was closed'],
        1073741856 => ['ON_OPEN', 'High-bit: File was opened'],
        1073742080 => ['ON_CREATE', 'High-bit: File or directory created in watched directory'],
        1073742336 => ['ON_DELETE', 'High-bit: File or directory deleted in watched directory'],
        16777216 => ['ON_ONLYDIR', 'Only watch pathname if it is a directory (Since Linux 2.6.15)'],
        33554432 => ['ON_DONT_FOLLOW', 'Do not dereference pathname if it is a symlink (Since Linux 2.6.15)'],
        536870912 => ['ON_MASK_ADD', 'Add events to watch mask for this pathname if it already exists (instead of replacing mask).'],
        2147483648 => ['ON_ONESHOT', 'Monitor pathname for one event, then remove from watch list.'],
    ];


    protected EventEmitter $eventEmitter;
    protected bool $willWatchAny = false;


    #[Pure] public function __construct()
    {
        $this->eventEmitter = new EventEmitter();
    }

    public function on(int $code, callable $handler): static
    {
        $this->eventEmitter->on($code, $handler);
        return $this;
    }

    public function once(int $code, callable $handler): static
    {
        $this->eventEmitter->once($code, $handler);
        return $this;
    }

    /**
     * @param callable $handler
     * @param bool $fireOnce Indicates that a single event will be fired,
     * this is very helpful when it comes to working with directories.
     * @return $this
     */
    public function onAny(callable $handler, bool $fireOnce = true): static
    {
        $this->eventEmitter->on('ON_ALL_EVENTS', $handler);
        $this->willWatchAny = true;
        return $this;
    }
}