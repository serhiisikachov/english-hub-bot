<?php

namespace App\Http\Controllers;

use App\Conversations\BookingConversation;
use BotMan\BotMan\BotMan;

class BookingController extends Controller
{
    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function startConversation(BotMan $bot)
    {
        $bot->startConversation(new BookingConversation());
    }
}
