<?php
require("SubReddit.php");
require("vendor/autoload.php");
require("config.php");
use skrtdev\NovaGram\Bot;
use skrtdev\NovaGram\CurlException;
use skrtdev\Telegram\Message;

try {
    $Bot = new Bot(getenv("token"), [
        "command_prefixes" => [':'],
        "skip_old_updates" => true,
        "threshold" => 50,
        "parse_mode" => "HTML"
    ]);
} catch (CurlException|\skrtdev\NovaGram\Exception $e) {
}

$Bot->onChannelPost(function (Message $message) use ($Bot) {
    $a = new SubReddit("frareddittest", $Bot, $message->chat->id);
    $a->SetSubRedditPostsLimit(1);
    if (!$a->sendPost()) {
        echo "\nPost already sent";
    }

});

try {
    $Bot->start();
} catch (\skrtdev\NovaGram\Exception $e) {
}