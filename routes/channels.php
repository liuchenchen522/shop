<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated  can listen to the channel.
|
*/

Broadcast::channel('App..{id}', function ($, $id) {
    return (int) $user->id === (int) $id;
});
