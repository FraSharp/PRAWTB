<?php
/**
 * Wrapper to send latest reddit posts in a telegram channel automatically
 *
 * @version     0.1-alpha
 * @author      Francesco Duca | FraSharp - 2022
 * @since       0.1-alpha
 */

require("vendor/autoload.php");
use skrtdev\NovaGram\Bot;

class SubReddit
{
    /**
     * The base reddit.com URL for SubReddit(s)
     *
     * @var string
     */
    private const  BaseURL      = 'https://www.reddit.com/r/';

    /**
     * The final part of the reddit.com API(s)
     *
     * @var string
     */
    private const  URLAppendix  = '/new.json?sort=new&limit=';

    /**
     * The Telegram ChatID of a chat (group|channel)
     *
     * @var int
     */
    private int    $ChatID;

    /**
     * The SubReddit name you want to track
     *
     * @var string
     */
    public  string $SubRedditName;

    /**
     * The actual URL that will be used to perform requests to Reddit API(s)
     *
     * @var string
     */
    private string $RequestURL;

    /**
     * The Novagram\Bot object instance
     *
     * @var Bot
     */
    private Bot    $Bot;

    /**
     * The number of posts to retrieve
     *
     * @var int|null
     */
    private ?int $limit;

    /**
     * The title of the last sent post
     *
     * @var string
     */
    public string $LastTitle = " ";

    /**
     * SubReddit constructor
     *
     * @param string $SubRedditName The subreddit name
     * @param Bot $Bot The NovaGram\Bot instance
     * @param int $ChatID The Relegram ChatID where posts will be sent
     */
    public function __construct(string $SubRedditName, Bot $Bot, int $ChatID) {
        $this->SubRedditName = $SubRedditName;
        $this->RequestURL = self::BaseURL . $this->SubRedditName . self::URLAppendix;
        $this->Bot = $Bot;
        $this->ChatID = $ChatID;
    }

    /**
     * Set the amount of posts to retrieve from a subreddit
     *
     * @param int $limit The number of posts to retrieve
     * @return void
     */
    final public function SetSubRedditPostsLimit (int $limit): void {
        $this->limit = $limit;
        $this->RequestURL .= $this->limit;
    }

    /**
     * Do a request to reddit api(s) and get a json
     *
     * @param int $i
     * @return array
     * @throws JsonException
     */
    private function GetJSON(int $i): array {
        $context = stream_context_create(array('http' => array('header'=>'Connection: close\r\n')));
        $JSONData = json_decode(file_get_contents($this->RequestURL, false, $context),
            true, 512, JSON_THROW_ON_ERROR);

        return $JSONData['data']['children'][$i]['data'];
    }

    /**
     * Check if post is a photo
     *
     * @param int $i
     * @return bool
     * @throws JsonException
     */
    private function PostIsPhoto(int $i): bool {
        return isset($this->GetJSON($i)['preview']) && !is_null($this->GetJSON($i)['preview']);
    }

    /**
     * Check if post is a video
     *
     * @param int $i
     * @return bool
     * @throws JsonException
     */
    private function PostIsVideo(int $i): bool {
        return isset($this->GetJSON($i)['media']) &&
            (!is_null($this->GetJSON($i)['media']) ||
                !is_null($this->GetJSON($i)['media']['is_gif']));
    }

    /**
     * Check whether there's a new post
     *
     * @param int $i
     * @return bool
     * @throws JsonException
     */
    private function CheckNewPost(int $i): bool {
        $PostTitle = $this->GetJSON($i)['title'];

        return $PostTitle !== $this->LastTitle;
    }

    /**
     * Send post to the Telegram chat (group/channel)
     *
     * @return void
     * @throws JsonException
     */
    private function SendPost(): void
    {
        for ($i = 0; $i < $this->limit; $i++) {

            if (strlen($this->GetJSON($i)['selftext']) >= 1024) {
                $caption = "<b>" . $this->GetJSON($i)['title'] . "</b>\n" . "✖ post is too long\n" .
                    "<a href=\"https://reddit.com" . $this->GetJSON($i)['permalink'] . "\">post link</a>\n\nfrom <em>" .
                    $this->GetJSON($i)['subreddit_name_prefixed'] . "</em>, by @adhd_subreddit\n";
            } else {
                $caption = "<b>" . $this->GetJSON($i)['title'] . "</b>\n" . $this->GetJSON($i)['selftext'] .
                    "\n<a href=\"https://reddit.com" . $this->GetJSON($i)['permalink'] . "\">post link</a>\n\nfrom <em>" .
                    $this->GetJSON($i)['subreddit_name_prefixed'] . "</em>, by @adhd_subreddit\n";
            }

            if ($this->PostIsVideo($i)) {

                $this->Bot->sendVideo([
                    "chat_id" => $this->ChatID,
                    "video" => $this->GetJSON($i)['media']['reddit_video']['fallback_url'],
                    "caption" => $caption
                ]);

            } elseif ($this->PostIsPhoto($i)) {

                $this->Bot->sendPhoto([
                    "chat_id" => $this->ChatID,
                    "photo" => $this->GetJSON($i)['url'],
                    "caption" => $caption
                ]);

            } else {

                $this->Bot->sendMessage([
                    "chat_id" => $this->ChatID,
                    "text" => $caption,
                    "disable_web_page_preview" => true
                ]);

            }

            $this->LastTitle = $this->GetJSON($i)['title'];
        }
    }

    // GetPost() which returns an array of the post information (isvideo, isphoto, selftext, title, subreddit, etc...)
    // and it's used to store every post in another array
    // put that array in a for, and SendPost() every position.
    // NB: every position = post

    /**
     * Checks for new posts, and if so, send them
     *
     * @param int $seconds The amount of seconds to wait before checking for new posts
     * @return void
     */
    final public function Poll(int $seconds): void {
        echo("\nPolling: checking for new post every $seconds seconds");
        while (true) {
            for ($i = 0; $i < $this->limit; $i++) {
                if ($this->CheckNewPost($i)) {
                    $this->SendPost();
                }
            }
            sleep($seconds);
        }
    }
}