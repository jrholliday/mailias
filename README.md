# Mailias #

Personal ad hoc email addresses.  Create disposable email addresses as you need them.

* Use any inbox you like. No configuration needed.
* Inboxes are "created" when email arrives for them and are "deleted" when they're emptied.
* Make-up an address, give it out, then check it with mAiLIAS.
* Give out a mailias address any time you need an email address but don't want to get spammed!

## Requirements ##

* Procmail + Maildir
* Apache + PHP
* mailparse - http://pecl.php.net/package/mailparse

## Configuring ##

### .htaccess ###

This package is made to "just work" when installed at the root of a web directory.  If you want/need to install it in a subdirectory, indicate the (web-accessable) sud-dir in the _.htaccess_ file.

### index.php ###

At the head of _index.php_ are two configuration varibles:

* *$maildir* - Set this to the absolute location of your mail directory.  I use _'/var/www/Maildir/new/'_
* *$hide_mailbox_list* - Set this to _false_ to show a table of all current inboxes on the splash page.  Set it to _false_ to hide this table.  Hiding the table will force you to manually specify the inbox you want to check.

### Crontab Entry ###

Needed to clean out old emails

    # crontab entry
    # Everyday (at midnight) delete any files that are older than one week
    0 0 * * * find /var/www/Maildir/ -type f -mtime +7 | xargs rm -f
