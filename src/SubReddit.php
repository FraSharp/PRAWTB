<?php

require("vendor/autoload.php");
use skrtdev\NovaGram\Bot;
use skrtdev\NovaGram\CurlException;
use skrtdev\Telegram\Message;

class SubReddit
{
    private const  BaseURL = 'https://www.reddit.com/r/';
    private const  URLAppendix = '/new.json?sort=new&limit=';
    private int    $ChatID;
    public  string $SubRedditName;
    public  string $LastTitle = " ";
    private string $RequestURL;
    private Bot    $Bot;
    private int    $limit;

    public function __construct(string $SubRedditName, Bot $Bot, int $ChatID) {
        $this->SubRedditName = $SubRedditName;
        $this->RequestURL = self::BaseURL . $this->SubRedditName . self::URLAppendix;
        $this->Bot = $Bot;
        $this->ChatID = $ChatID;
    }

    final public function SetSubRedditPostsLimit (int $limit): void {
        $this->limit = $limit;
        $this->RequestURL .= $this->limit;
    }

    /**
     * @throws JsonException
     */
    final public function GetJSON(int $i): array {
        $context = stream_context_create(array('http' => array('header'=>'Connection: close\r\n')));
        $JSONData = json_decode(file_get_contents($this->RequestURL, false, $context),
            true, 512, JSON_THROW_ON_ERROR);

        return $JSONData['data']['children'][$i]['data'];
    }

    /**
     * @throws JsonException
     */
    final public function PostIsPhoto(int $i): bool {
        return isset($this->GetJSON($i)['preview']) && !is_null($this->GetJSON($i)['preview']);
    }

    /**
     * @throws JsonException
     */
    final public function PostIsVideo(int $i): bool {
        return isset($this->GetJSON($i)['media']) &&
            (!is_null($this->GetJSON($i)['media']) ||
                !is_null($this->GetJSON($i)['media']) ||
                !is_null($this->GetJSON($i)['media']['is_gif']));
    }

    /**
     * @throws JsonException
     */
    private function IsSamePost(int $i): bool {
        return $this->LastTitle === $this->GetJSON($i)['title'];
    }

    /**
     * @throws JsonException
     */
    final public function SendPost(): bool
    {
        $ret = false;
        for ($i = 0; $i < $this->limit; $i++) {

            $caption = "<b>" . $this->GetJSON($i)['title'] . "</b>\n" . $this->GetJSON($i)['selftext'] .
                "\n<a href=\"https://reddit.com" . $this->GetJSON($i)['permalink'] . "\">post link</a>\n\nfrom <em>" .
                $this->GetJSON($i)['subreddit_name_prefixed'] . "</em>, by @adhd_subreddit\n";

            if (!$this->IsSamePost($i) && $this->PostIsVideo($i)) {

                $ret = (bool)$this->Bot->sendVideo([
                    "chat_id" => $this->ChatID,
                    "video" => $this->GetJSON($i)['media']['reddit_video']['fallback_url'],
                    "caption" => $caption
                ]);

                $this->LastTitle = $this->GetJSON($i)['title'];

            } elseif (!$this->IsSamePost($i) && $this->PostIsPhoto($i)) {

                $ret = (bool)$this->Bot->sendPhoto([
                    "chat_id" => $this->ChatID,
                    "photo" => $this->GetJSON($i)['url'],
                    "caption" => $caption
                ]);

                $this->LastTitle = $this->GetJSON($i)['title'];

            } elseif (!$this->IsSamePost($i)) {

                $ret = (bool)$this->Bot->sendMessage([
                    "chat_id" => $this->ChatID,
                    "text" => $caption,
                    "disable_web_page_preview" => true
                ]);

                $this->LastTitle = $this->GetJSON($i)['title'];

            }
            echo "\nlast_title: " . $this->LastTitle;
        }
        return $ret;
    }
}