### Simple PHP wrapper for reddit, oriented to Telegram bot(s)
#### Usage:
```php
$SubReddit = new SubReddit($SubRedditName)

// to set the number of posts to retrieve, 1 in this example
$SubReddit->SetSubRedditPostsLimit(1);

// sends the post, returns a bool
$SubReddit->sendPost();
```