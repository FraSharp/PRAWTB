# TODOs

- [ ] Allow getting posts from more than 1 subreddit.
- [ ] Make _$RequestURL_ generation faster.
- [ ] Get every new post since when the bot has been offline, this means that _$limit_ can't be hardcoded
       on such situations.
- [x] Handle more than 1 post at a time.
- [x] Fix _$LastTitle_ implementation (in order not to send the same post more than 1 time).
- [ ] Do not send last post if it's same as the previous (happens when you turn on the bot and there hasn't been any 
  new post).
- [ ] Handle new posts within the X seconds of sleep (currently the bot only sends the last post, but there may be 
  more than 1 post posted in the last 60 seconds).