<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;

class BookingConversation extends Conversation
{
    protected function askLocation()
    {
//        $question = Question::create("Привіт! Радий тебе бачити! У якому місті знаходишся?")
//            ->addButton(
//                Button::create("Харків")->value(0)
//            )
//            ->addButton(
//                Button::create("Львів")->value(1)
//            );

        return $this->ask("testtetsetste",
            function (Answer $answer) {
                //$this->askDate();
            },
            Keyboard::create()
                ->type(Keyboard::TYPE_INLINE)
                ->addRow(
                    KeyboardButton::create('test')->callbackData('test')
                )->toArray()
        );
    }

    protected function askDate()
    {
        $question = Question::create("Обирай дату коли хочеш навчатися:")
            ->addButton(
                Button::create("Answer")->value(0)
            );

        return $this->ask($question,
            function (Answer $answer) {

            },
            Keyboard::create()
                ->type(Keyboard::TYPE_INLINE)
                ->addRow(
                    KeyboardButton::create('test')->callbackData('test')
                )
        );
    }

    ////Store location
    //                $this->ask('Обирай дату, коли хочеш займатися індивідуально:',
    //                    function (Answer $answer){
    //
    //                    },
    //                    $this->getDateKeyboard()->toArray()
    //                );

    protected function getDateKeyboard()
    {
        $keyboard = Keyboard::create()
            ->type(Keyboard::TYPE_INLINE);
        $now = new \DateTime();
        $weekAfter = clone $now;
        $weekAfter->modify('+7d');

        $diff = $weekAfter->diff($now);

        foreach ($diff as $date) {
            $keyboard->addRow(
                KeyboardButton::create($date->format('d.m.Y'))->callbackData('')
            );
        }

        return $keyboard;
    }

    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        $this->askLocation();
    }
}
