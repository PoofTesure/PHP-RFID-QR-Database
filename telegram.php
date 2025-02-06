<?php
namespace SergiX44\Nutgram\Telegram\Types\Keyboard;
require __DIR__ . '/vendor/autoload.php';

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Configuration;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Media\Contact;
use SergiX44\Nutgram\Telegram\Properties\MessageType;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Telegram\Types\Internal\InputFile;
use SergiX44\Nutgram\Telegram\Types\Message\Message;


$config = new Configuration(
    enableHttp2: false
);

function log($message)
{
    $message = date("H:i:s") . " - $message - ".PHP_EOL;
    print($message);
    flush();
    ob_flush();
}

class MyConversation extends Conversation {
    public function start(Nutgram $bot)
    {
        $bot->sendMessage(
            text: "Silahkan tekan tombol Buat QR untuk membuat QR Tamu",
            reply_markup: ReplyKeyboardMarkup::make()
            ->addRow(
                KeyboardButton::make(
                    text: "Buat QR",
                    request_contact: true,
                ),
            )
        );
        $this->next("secondStep");
    }
    public function secondStep(Nutgram $bot)
    {
       $phone_number = $bot->message()->contact->phone_number;
       $photo = fopen("http://localhost/script/qr.php?contact=".$phone_number,"r");
       $bot->sendPhoto(
        photo: InputFile::make($photo),
       );
       $this->next("start");
    }
}

$bot = new Nutgram("7512909618:AAG9pHE2HVjznshJoIisRxVycqmcp8HEesI",$config);

$bot->onCommand("start",MyConversation::class);

$bot->run();


?>