<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;

class  BookingByTeacherConversation extends Conversation
{
    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        $this->askTeacher();
    }

    protected function askTeacher()
    {
        return $this->ask($this->getTeacherQuestion(),
            function (Answer $answer) {
                return $this->askLesson();
            },
            $this->getTeacherKeyboard()->toArray()
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
        $this->say("Дякую! Ти зареестрований!");
    }

    protected function getTeacherQuestion(): Question
    {
        return Question::create("Обирай вчителя");
    }

    protected function getTeacherKeyboard(): Keyboard
    {
        $teachers = ["Kseniya", "Polina"];
        $teacherButtons = [];

        foreach ($teachers as $id => $name) {
            $teacherButtons[] = KeyboardButton::create($name)->callbackData($id);
        }

        return Keyboard::create()
            ->addRow(
                ...$teacherButtons
            );
    }

    protected function getLessonQuestion()
    {
        return Question::create("Обирай час коли хочеш навчатися");
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
        return Question::create("Якщо вся інформація правильна, тисни Оk!");
    }

    protected function getApproveKeyboard(): Keyboard
    {
        Keyboard::create()->addRow(
            KeyboardButton::create("Ok")->callbackData('ok'),
            KeyboardButton::create("Почати спочатку")->callbackData('start')
        );
    }
}
