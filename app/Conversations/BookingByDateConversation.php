<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;

class BookingByDateConversation extends Conversation
{
    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        $this->askDate();
    }

    protected function askDate()
    {
        return $this->ask($this->getDateQuestion(),
            function (Answer $answer) {
                return $this->askLesson();
            },
            $this->getDateKeyboard()->toArray()
        );
    }

    protected function askLesson()
    {
        return $this->ask($this->getLessonQuestion(),
            function (Answer $answer) {
                return $this->askApprove();
            },
            $this->getLessonKeyboard()->toArray()
        );
    }

    protected function askApprove()
    {
        return $this->ask($this->getApproveQuestion(),
            function (Answer $answer) {
                return $this->finish();
            },
            $this->getApproveKeyboard()->toArray()
        );
    }

    protected function finish()
    {
        $this->say("That's all folks");
    }

    protected function getDateQuestion(): Question
    {
        return Question::create("Обирай дату");
    }

    protected function getDateKeyboard(): Keyboard
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

    protected function getLessonQuestion(): Question
    {
        return Question::create("Обирай слот");
    }

    protected function getLessonKeyboard(): Keyboard
    {
        $lessons = [
            ['teacher' => 'Kseniya', 'slot' => new \DateTime()],
            ['teacher' => 'Kseniya', 'slot' => new \DateTime('+1 day')]
        ];

        $keyboard = Keyboard::create();
        foreach ($lessons as $key => $lesson) {
            $keyboardButton = KeyboardButton::create(
                sprintf("%s | :s", [$lesson['teacher'], $lesson['slot']->format("Y-m-d H:i")])
            )->callbackData($key);

            $keyboard->addRow(
                $keyboardButton
            );
        }

        return $keyboard;
    }

    protected function getApproveQuestion(): Question
    {
        return Question::create("Перевір ще раз інформацію, усе вірно?");
    }

    protected function getApproveKeyboard(): Keyboard
    {
        return Keyboard::create()
            ->addRow(
                KeyboardButton::create("Вірно")->callbackData('ok'),
                KeyboardButton::create("Корегувати")->callbackData('fix')
            );
    }
}
