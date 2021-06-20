# Swotch

[Swoole](https://swoole.co.uk) file system changes watcher.

## Installation

```
composer require ahmard/swotch
```

## Usage

#### Basic Usage

```php
use Swotch\Watcher;

require 'vendor/autoload.php';

$paths = [
    __DIR__ . '/app/',
    __DIR__ . '/views/',
];

Watcher::watch($paths)->onAny(function (){
    echo "File changes detected\n";
});
```

#### Swoole Server Integration

```php
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;
use Swotch\Watcher;

require 'vendor/autoload.php';

$server = new Server('0.0.0.0', 9000);
$server->on('request', function (Request $request, Response $response) {
    $response->end('Hello world');
});

$server->on('start', function (Server $server) {
    echo "Server started at http://0.0.0.0:9000\n";
    
    $paths = [
        __DIR__ . '/app/',
        __DIR__ . '/views/',
    ];
    
    Watcher::watch($paths)->onAny(fn() => $server->reload());
});

$server->start();
```