npm install --save laravel-echo pusher-js

composer require beyondcode/laravel-websockets

php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="migrations"

php artisan migrate

php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"

////////////

.env

BROADCAST_DRIVER=pusher

channels.php

use Techsmart\Chat\Broadcasting\ChatChannel;

Broadcast::channel('chat.{userId}', ChatChannel::class);

uncomment 

App\Providers\BroadcastServiceProvider::class,

in BroadcastServiceProvider

Broadcast::routes(['middleware' => ['web']]);

in bootsrap.js

window.Pusher = require('pusher-js');

window.Echo = new Echo({

    broadcaster: 'pusher',
    
    key: process.env.MIX_PUSHER_APP_KEY,
    
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    
    encrypted: false,
    
    wsHost: window.location.hostname,
    
    wsPort: 6001,
    
    wssPort: 6001,
    
    disableStats: true,
    
    enabledTransports: ['ws', 'wss'],
    
    wssPort: 6001,
    
    namespace: ''
    
});


namespace:''

in Chats.vue in listen use Techsmart\\Chat\\Http\\Events\\NewMessage instead NewMessage 

OR

in Chats.vue

.listen("\\Techsmart\\Chat\\Http\\Events\\NewMessage", (e) => {
