## Installation
- `composer install`
- config .env
- `php artisan migrate`
- I saved data of achievements and badges in database. For feeding databse with initial data that is defined in the test description, please run: `php artisan db:seed`

## Tables
Four tables has been added,
- achievements
  - name of achievement
  - type of achievement (lesson or comment)
  - minimum number for getting the achievement
- badges
  - name of badge
  - minimum achievement for getting the badge
- user_achievements
  - user_id
  - achievement_id
- user_badges
  - user_id
  - badge_id

## Notice
- I didn't count the number of times that CommentWritten or LessonWatched has been fired for each user. Each time these events are fired, I go and find the number of comments of the user from the comments table and the number of lessons that the user watched from the user_lesson table.. <br> In other words, I assumed that before CommentWritten or LessonWatched fired, new record(s) had been added to comments or user_lesson, respectively. <br> So, for instance, if the CommentWritten event is fired without any new record in the comments table for the same user, nothing will happen, and the user will not get closer to the next achievement or badge, even though the event has been fired.
## Features
- With this implementation, achievements/badges can be updated, extended, or removed (if no user has obtained them already) just by changing, adding, or removing them from the achievements/badges table.
- If any record is updated in the achievement/badge table, the next time CommentWritten or LessonWatched is fired for a user, the user's achievements/badges will change based on the updated record.
## Testing
- I wrote some tests for the endpoint, listeners, and methods that I wrote. You can run them with `php artian test`

## Thanks!
Thank you so much for your time and energy. If you have anything to say about my code, I would be really glad and thankful to hear it. I am really looking forward to hearing from you. Best regards!
