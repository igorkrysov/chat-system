npm install --save laravel-echo pusher-js

composer require beyondcode/laravel-websockets
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="migrations"
php artisan migrate
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"
////////////

BROADCAST_DRIVER=pusher




channels.php
use Techsmart\Chat\Broadcasting\ChatChannel;
Broadcast::channel('chat.{userId}', ChatChannel::class);


uncomment 
App\Providers\BroadcastServiceProvider::class,

in BroadcastServiceProvider

        Broadcast::routes(['middleware' => ['web']]);