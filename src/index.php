<?php
require("SubReddit.php");
require("vendor/autoload.php");
require("config.php");
use skrtdev\NovaGram\Bot;
use skrtdev\NovaGram\CurlException;
use skrtdev\Telegram\Message;

// $data = json_decode(file_get_contents("http://www.reddit.com/r/frareddittest/new.json?sort=new&limit=1"), true);

// echo file_put_contents("datavideo.txt", print_r($data, true));

try {
    $Bot = new Bot(getenv("token"), [
        "command_prefixes" => [':'],
        "skip_old_updates" => true,
        "threshold" => 50,
        "parse_mode" => "HTML"
    ]);
} catch (CurlException|\skrtdev\NovaGram\Exception $e) {
}
// -1623344012

/*
if (isset($Bot)) {
    $Bot->onChannelPost(function (Message $message) {
        $s_ADHD = new SubReddit("frareddittest");
        $s_ADHD->SetSubRedditPostsLimit(1);
        $val = $s_ADHD->GetJSON();

        if ($s_ADHD->PostIsPhoto() || $s_ADHD->PostIsVideo()) {
            $caption = $val['title'] . "\n<a href=\"https://reddit.com" . $val['permalink'] . "\">post link</a>\n\nfrom <em>"
                . $val['subreddit_name_prefixed'] . "</em>, by @adhd_subreddit\n";
        } else {
            $caption = $val['title'] . "\n\n" . $val['selftext'] . "\n<a href=\"https://reddit.com" . $val['permalink'] .
                "\">post link</a>\n\nfrom <em>" . $val['subreddit_name_prefixed'] . "</em>, by @adhd_subreddit\n";
        }

        // $message->chat->sendMessage(($val['title']));
        if ($s_ADHD->PostIsPhoto() && !$s_ADHD->PostIsVideo()) {
            $message->chat->sendPhoto($val['url'], $caption);
        } elseif ($s_ADHD->PostIsVideo()) {
            echo "yes";
            $message->chat->sendVideo([
                "video" => $val['media']['reddit_video']['fallback_url'],
                "caption" => $caption
            ]);
        }
    });
}
*/

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