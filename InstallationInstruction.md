![http://farm4.static.flickr.com/3204/3052558460_7bf5e4c52d.jpg](http://farm4.static.flickr.com/3204/3052558460_7bf5e4c52d.jpg)

# Installation #
  1. Download the code from the Source section and unzip it somewhere accessible by your webserver. Note, however, that the zip files in the Downloads section are usually outdated. It is almost always better to go to [Source Checkout](http://code.google.com/p/qbsql/source/checkout).
  1. Edit the file `config.php` as necessary.
  1. Import `structure.sql` into your MySQL database, using a command like:
```
mysql -u my_username -p my_password my_database < structure.sql
```
  1. Pull up the page `index.php` in your web browser. You should be redirected to the Tournament List.
  1. Create a new tournament by clicking on "New Tournament".

# Creating Tournaments #
  1. Click the "New Tournament" link on the tournament list, which should bring you to the "Manage tournaments" page.
  1. Fill out the tournament details:
  * _Name_: A name for the tournament. This will show up in the tournament list and on its front page.
  * _Prefix_: This should be a short, lowercase name for the tournament. Letters and numbers **only**, no spaces. The first character must be a letter.
  * _Description_: This should be a prose description of the tournament.
  * _Default game length_: This is the default number of tossups in a round. This can be overridden at any point.
  * _Tournament username_, _Tournament password_: The username and password you will use to log in and modify tournament stats. This should be different from the master username and password.
  * _Master username_, _Master password_: This is the master username and password that you set in `config.php`.

![http://farm4.static.flickr.com/3294/3052558482_2a1ca73555.jpg](http://farm4.static.flickr.com/3294/3052558482_2a1ca73555.jpg)

# Running a Tournament #
  1. **Logging in**: Select your newly-created tournament from the list. Enter the tournament username and password in the fields provided and click "Log in".
  1. **Creating teams**: Click "Add teams". In the fields provided, enter full and short names for each team and click the "Add teams" button. The short names are used in places like individual score lists where the full name may not fit. If you have more than eight teams, enter the first eight, then return to the page and add more.
  1. **Add players to teams**: Click "Add players". Select the appropriate team from the drop-down menu and enter the player names in the fields below, then click the "Add players" button. As with adding teams, you can return to the add players page and add more if you have more than eight players on a team.
  1. **Add games**: Click "Add a game". Select the two teams and enter the round number, and click the "Continue" button. Fill in the game stats and click "Submit game". You may leave any field blank if it would be zero. The team scores must not be left blank.
  1. Enjoy!
  1. Until something goes wrong.
  1. **Editing games**: From the **Round summaries** or **Game detail** page, click "Edit". Change the numbers as necessary and click the "Apply changes" button.
  1. **Deleting games**: Go to the edit page as above, then check the "Confirm delete" checkbox and click the "Delete this round" button. Deletes cannot be undone.
  1. **Editing players**: From the **Team rosters** page, click "Edit".
  1. **Deleting players**: Go to the edit page as above, then check the checkbox and click the "Delete" button. Deletes cannot be undone. Note that you cannot delete a player that has games in the database. You will have to delete any games the player played before deleting the player.
  1. **Deleting and editing teams**: This is about the same as deleting players.

# Things to watch for #
  * When adding players and teams, be careful to check that the player wasn't already added. You can look at the **Team rosters** page to see who's already been added.