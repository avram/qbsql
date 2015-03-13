This is an online stats program for running quizbowl tournaments. In its default configuration, it does a nice job with NAQT- and ACF-style tournaments, and probably other formats as well.

QBSQL lets multiple people share the task of entering game statistics; in many cases, it is possible to run an event without any dedicated stats person, if moderators have access to a computer to enter stats after each round.

QBSQL can exchange data with the dominant program, SQBS, so you can import your old SQBS data to display on your new, spiffy QBSQL installation. The SQBS export can be used to submit official results to organizations that like that kind of data (i.e., NAQT).

QBSQL has error-checking, an intuitive interface, and is openly and actively maintained.

QBSQL is free. That means that you never need to pay a dime to use it on your own servers, and you are always free to modify it to fit your needs.

It works effectively and is simple to use. You do not need any knowledge of web programming to use an installed copy of QBSQL, and it can be used without issues by any computer (or phone!) with a connection to the internet. The software was designed with NAQT- and ACF-style quizbowl in mind, so it may make some assumptions that don't apply for some other forms of academic competition. If you find that it doesn't quite fit your needs, please [let me know](mailto:ajlyon+qbsql@gmail.com); I'm glad to make the software more flexible, but I don't know what other formats might require.

## Getting Started ##
To install it on your own server, see **InstallationInstruction**. You will need a server that runs PHP and MySQL to install it, but you can have your tournament stats hosted on http://quizbowl.gimranov.com/ if you prefer.

There is also a **[Live Online Demo](http://quizbowl.gimranov.com/qbsql)**. In the live demo, try out the "Test Tourney", with the username and password: `test`/`test`

If the installation instructions above don't make sense, or you don't have a place to host a copy of QBSQL, I can make a tournament for you at http://quizbowl.gimranov.com/ and you can use that copy for your tournament.

If you have questions or want to help out, [let me know](mailto:ajlyon+qbsql@gmail.com).

If you run into a bug, post it in the **Issues** section.

### Cool things you can do with qbsql: ###
  * Upload round results from multiple locations
  * Show live-updating results
  * Do stats without a Windows machine
  * Export tournament results to send to results databases (NAQT only for now)
  * Import SQBS files from older tournaments

### Tournaments that have used qbsql: ###
  * [TWAIN XII at UCLA](http://quizbowl.gimranov.com/stats/index.php?t=twain2011), October 2, 2011
  * [Minnesota Open Mirror at UCLA](http://quizbowl.gimranov.com/stats/index.php?t=mo2010), November 21, 2010
  * [2010-11 Berry College High School Scholar's Bowl](http://www.berryquiz.com/index.php?t=2010HS), November 7, 2010
  * [TWAIN XI at UCLA](http://quizbowl.gimranov.com/stats/index.php?t=TWAIN2010), October 2, 2010
  * [2010 NAQT Southern California State Championship at UCLA](http://quizbowl.gimranov.com/stats/index.php?t=naqths_champ2010), April 3, 2010
  * [2009-10 Berry College High School Scholar's Bowl](http://www.berryquiz.com/index.php?t=2010HS), February 20, 2010
  * [NAQT SCT West at UCLA](http://quizbowl.gimranov.com/stats/index.php?t=sct2010d1), February 6, 2010
  * [Winter Intramural Tournament at Stanford](http://quizbowl.stanford.edu/cgi-bin/qbsql/index.php?t=2010winterim), January 30, 2010
  * [ACF Winter at Stanford](http://quizbowl.stanford.edu/cgi-bin/qbsql/index.php?t=acfwinter2010), January 16, 2010
  * [New Trier Varsity Mirror at UCLA](http://quizbowl.gimranov.com/stats/index.php?t=ntv2010), January 9, 2010
  * [Cardinal Junior Classic at Stanford](http://quizbowl.stanford.edu/cgi-bin/qbsql/index.php?t=2009cjc), November 14, 2009
  * [37th Annual Southeastern Open Invitational at Berry College](http://www.berryquiz.com/index.php?t=2010SE), November 7, 2009
  * [Minnesota Open Mirror at UCLA](http://quizbowl.gimranov.com/stats/index.php?t=mo2009), October 17, 2009
  * [EFT Mirror at UCLA](http://quizbowl.gimranov.com/stats/index.php?t=eft2009), October 4, 2009
  * [Intramural Tournament at Stanford](http://quizbowl.stanford.edu/cgi-bin/qbsql/index.php?t=2009fallim), Fall 2009
  * [Aztlan Cup V at UCLA](http://quizbowl.gimranov.com/stats/index.php?t=aztlan20092), March 22, 2009
  * [BISCUIT III at UCLA](http://quizbowl.gimranov.com/stats/index.php?t=biscuit2009), March 21, 2009
  * [BAIT 2009 at UCLA](http://quizbowl.gimranov.com/stats/index.php?t=bait2009), March 7, 2009
  * [NAQT SCT West at UCLA](http://quizbowl.gimranov.com/stats/index.php?t=sct2009d1), February 7, 2009
  * Grinnell Undergraduate Tournament, 2004-2006
  * Grinnell High School Tournament, 2005-2006

### Where we're going: ###
This is a project that could use some work. If you like to code and like to play quiz bowl, this could use some improvements, like:
  * A way to cleanly export a tournament and make its results static
  * Something like a round-robin and bracket generator, so that qbsql could know what matches should be played, and to make tournament directors' lives easier.
  * More intelligent interfaces that might auto-fill fields and show live calculating round stats
  * Probably lots more! Feel free to add ideas to the [issue tracker](http://code.google.com/p/qbsql/issues/list), or code your own new improvement.

### Other options ###
  * If you want more options and an all-in-one Windows experience, I recommend [SQBS](http://ai.stanford.edu/~csewell/sqbs/). It is the de facto standard in quizbowl, and it supports all major formats.
  * If you don't want to pay, don't want to use Windows, and aren't afraid of Perl, you might want to try [Livestat](http://weill.org/livestat/).
  * NAQT has used [Tournament Director](http://www.tournamentdirector.org/) (site down, source at http://github.com/puls/tournament-director) to great success for HSNCT. It is better than QBSQL in some ways.
  * If it ever comes out, [BEes](http://www.beesqb.com/) might be better.
  * If it happens, you could try [QBTPS](http://qbtournaments.com/), but that site is down.
  * If you want an online system a lot like this one, and don't mind paying a little for the service of having someone else host it, you could try [Taft](http://www.taftqb.com/). They charge $25. Also, Taft is being shut down and is no longer taking new registrations.

&lt;wiki:gadget url="http://www.ohloh.net/p/63858/widgets/project\_users.xml?style=rainbow" height="100"  border="0" /&gt;