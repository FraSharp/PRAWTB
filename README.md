### Simple PHP wrapper for reddit, oriented to Telegram bot(s)
#### Setup:
- rename `config_example.php` to `config.php`
- set botToken from BotFather @ Telegram
- set `CHANNEL_ID`
- set `SUBREDDIT_NAME`

#### Usage:
```php
$SubReddit = new SubReddit($SubRedditName)

// to set the number of posts to retrieve, 1 in this example
$SubReddit->SetSubRedditPostsLimit(1);

// checks for any new post every 60 seconds
$SubReddit->Poll(60);
```