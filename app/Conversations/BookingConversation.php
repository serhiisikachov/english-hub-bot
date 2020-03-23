<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;

class BookingConversation extends Conversation
{
    protected $bookingByTeacher;

    protected $bookingByDate;

    public function __construct()
    {
        $this->bookingByTeacher = new BookingByTeacherConversation();
        $this->bookingByDate = new BookingByDateConversation();
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

    protected function askLocation()
    {
        return $this->ask($this->getLocationQuestion(),
            function (Answer $answer) {
                return $this->askTeacherOrDate();
            },
            $this->getLocationKeyboard()->toArray()
        );
    }

    protected function askTeacherOrDate(): self
    {
        return $this->ask($this->getTeacherOrDateQuestion(),
            function (Answer $answer) {
                if ($this->isTeacherStrategy($answer)) {
                    $this->bookingByTeacher->run();

                    return;
                }

                $this->bookingByDate->run();
            },
            $this->getTeacherorDateKeyboard()->toArray()
        );
    }

    protected function getLocationQuestion(): Question
    {
        return Question::create("Привіт! Обирай місто в якому плануєш навчатися!");
    }

    protected function getLocationKeyboard(): Keyboard
    {
        $locations = ["Харків", "Львів"];

        $keyboard = Keyboard::create()
            ->type(Keyboard::TYPE_INLINE)
            ->oneTimeKeyboard(true);

        foreach ($locations as $id => $key) {
            $keyboard->addRow(
                KeyboardButton::create($key)->callbackData($key)
            );
        }

        return $keyboard;
    }

    protected function getTeacherOrDateQuestion(): Question
    {
        return Question::create("Хочеш обирати по вчителю чи по даті?");
    }

    protected function getTeacherOrDateKeyboard(): Keyboard
    {
        $keyboard = Keyboard::create()
            ->type(Keyboard::TYPE_INLINE)
            ->oneTimeKeyboard(true);

        $keyboard->addRow(
            KeyboardButton::create('Вчитель')->callbackData('teacher'),
            KeyboardButton::create('Дата')->callbackData('date')
        );

        return $keyboard;
    }

    protected function isTeacherStrategy(Answer $answer): bool
    {
        return $answer->getValue() === 'teacher';
    }





    /*protected function askLocation()
    {
        $locations = ["Харків", "Львів"];

        $keyboard = Keyboard::create()
            ->type(Keyboard::TYPE_INLINE)
            ->oneTimeKeyboard(true);

        foreach ($locations as $id => $key) {
            $keyboard->addRow(
                KeyboardButton::create($key)->callbackData($id)
            );
        }


        return $this->ask("Choose location",
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
    */
}
