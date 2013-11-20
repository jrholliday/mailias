## Requirements ##

* mailparse - http://pecl.php.net/package/mailparse

## Crontab Entry ##

Needed to clean out old emails

    # crontab entry
    # Everyday (at midnight) delete any files that are older than one week
    0 0 * * * find /var/www/Maildir/ -type f -mtime +7 | xargs rm -f
