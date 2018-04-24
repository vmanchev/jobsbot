# JobsBot - becase searching for a job is a job

Jobs.bg is the biggest jobs board in Bulgaria. One of the many 
services it provides is a new job offers notification by email. 
Well, it actually informs you about the new job offers from yesterday.
There is no immediate notification when a new job offer is posted.

Using JobsBot, one can setup a cron job to search for matching job 
offers, say every 5 minutes, and get notified by email.

## Configuration
1. Prepare an SMTP account and your database credentials. 
2. Save the .env.example file as .env and set the relevant values.

## Installation
1. Upload the files to your server.
2. Import the db.sql file into your database.
3. Run `composer install`

## Cron job
To run the search every 5 minutes, use something like:

```
*/5 * * * * php /path/to/public/index.php >/dev/null 2>&1
```

# Warning
As per their Jobs.bg Terms and Conditions, all data from jobs.bg 
can only be used for personal needs. Do not try to re-distribute 
the content or breach their policy.

# Licence
JobsBot itself is licenced under MIT (aka: use it the way you want)



