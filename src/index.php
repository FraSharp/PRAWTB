<?php
require("SubReddit.php");
require("vendor/autoload.php");
require("config.php");
use skrtdev\NovaGram\Bot;
use skrtdev\NovaGram\CurlException;

/*
 * this function can be used to dump json body into a text file called "dump.txt", currently you get the last post
 * from r/frareddittest
 */
/**
 * @throws JsonException
 */
function DumpJSON(): void {
    $data = json_decode(file_get_contents("https://www.reddit.com/r/frareddittest/new.json?sort=new&limit=1"),
        true, 512, JSON_THROW_ON_ERROR);
    file_put_contents("dump.txt", print_r($data, true));
}

try {
    $Bot = new Bot(getenv("token"), [
        "skip_old_updates" => true,
        "parse_mode" => "HTML"
    ]);
} catch (CurlException|\skrtdev\NovaGram\Exception $e) {
}

// CHANNEL_ID is defined in config.php
$a = new SubReddit(SUBREDDIT_NAME, !isset($Bot) ?: $Bot, CHANNEL_ID);
// set the limit of posts to retrieve
$a->SetSubRedditPostsLimit(1);
// check for new posts and if so, send them
$a->Poll(30);

try {
    !isset($Bot) ?: $Bot->start();
} catch (\skrtdev\NovaGram\Exception $e) {
}
