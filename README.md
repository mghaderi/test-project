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
- I didn't count number of times that CommentWritten or LessonWatched has been fired for each user, each time these events fired, I go and find number of comments of user from comments table and number of lessons that user watched from user_lesson table. <br> In other words, I assumed that before CommentWritten or LessonWatched fired, new record(s) had been added to comments or user_lessons respectively. <br> So for instance If CommentWritten event just being fired without any new record in comments table for the same user, nothing will happen and the user will not get closer to next achievement or badge even so the event has been fired.
## Features
- With this implementation, achievements/badges can be updated, extended or removed (if no user got them already) just by changing, adding or removing them from achievements/badges table.
- If any record is updated in achievement/badge table, next time CommentWritten or LessonWathed is fired for a user, user achievements/badges will cahnge based on the updated record.
