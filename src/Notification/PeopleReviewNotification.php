<?php

namespace App\Notification;

use App\Entity\People;
use Symfony\Component\Notifier\Bridge\Telegram\Reply\Markup\Button\InlineKeyboardButton;
use Symfony\Component\Notifier\Bridge\Telegram\Reply\Markup\InlineKeyboardMarkup;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Notification\ChatNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\RecipientInterface;

class PeopleReviewNotification extends Notification implements ChatNotificationInterface
{
    public function __construct(
        private People $people,
        private string $reviewUrl
    )
    {
        parent::__construct('New people posted');
    }

    public function asChatMessage(RecipientInterface $recipient, string $transport = null): ?ChatMessage
    {
        if ('telegram' !== $transport) {
            return null;
        }

        $inlineKeyboardMarkup = new InlineKeyboardMarkup();
        // row 1
//        $inlineKeyboardMarkup->inlineKeyboard([
//            (new InlineKeyboardButton('Show People'))->url($this->reviewUrl), // column 1
//        ]);

        // row 2
        $inlineKeyboardMarkup->inlineKeyboard([
            (new InlineKeyboardButton('Accept'))->url($this->reviewUrl), // column 1
            (new InlineKeyboardButton('Reject'))->url($this->reviewUrl.'?reject=1'), // column 2
        ]);

        // Create Telegram options
        $telegramOptions = (new TelegramOptions())
            ->parseMode(TelegramOptions::PARSE_MODE_HTML)
            ->disableWebPagePreview(true)
            ->disableNotification(true)
            ->replyMarkup($inlineKeyboardMarkup);

        // message
        $subject = sprintf('New people posted: <pre><b>%s %s %s</b></pre>',
            $this->people->getFirstName(),
            $this->people->getSecondName(),
            $this->people->getMiddleName()
        );
        $chatMessage = new ChatMessage($subject);
        $chatMessage->options($telegramOptions);

        return $chatMessage;
    }

    public function getChannels(RecipientInterface $recipient): array
    {
        $this->importance(Notification::IMPORTANCE_LOW);

        return ['chat/telegram'];
    }
}