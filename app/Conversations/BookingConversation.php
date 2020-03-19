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
        $locations = ["Харків", "Львів"];

        $row = [];
        foreach ($locations as $id => $key) {
            $row[] = KeyboardButton::create($key)->callbackData($id);
        }
        $keyboard = Keyboard::create()
            ->type(Keyboard::TYPE_INLINE)
            ->oneTimeKeyboard(true);

        foreach ($locations as $id => $key) {
            $key->addRow(
                KeyboardButton::create($key)->callbackData($id);
            );
        }


        return $this->ask("testtetsetste",
            function (Answer $answer) {
                $this->askDate();
            },
            $keyboard->toArray()
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
            $this->getDateKeyboard()->toArray()
        );
    }

    protected function getDateKeyboard()
    {
        $keyboard = Keyboard::create()
            ->type(Keyboard::TYPE_INLINE);
        $start = new \DateTime();
        $end = new \DateTime();
        $end->modify('+7 day');

        $dateInterval = new \DateInterval('P1D');
        $datePeriod = new \DatePeriod($start, $dateInterval, $end);
        foreach ($datePeriod as $date) {
            $keyboard->addRow(
                KeyboardButton::create($date->format('d.m.Y'))->callbackData($date->format('d.m.Y'))
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
