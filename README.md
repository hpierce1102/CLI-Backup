Backup-CLI
==========
Copies files on your file system to a cloud service for purposes of backing up.


Goals
-----
1. Files should be trivially copied - allowing them to be recovered without the
need to reinstall this software.
2. Easily automatable via system tools - should be simple to automate via Windows
Task Scheduler, or crontab by using a simple command line interface.
3. Portability - should work on most common platforms.

Non Goals
---------
This project does not attempt to do these things:

1. Provide a GUI / web interface - All commands must be executed via the command line.
2. Compress files - Compressing files is nice for optimizing for storage space, which may
be needed for large backups to public cloud storage spaces, but removes the trivality of 
retrieving files from these services. 

Installation
------------

1. Install dependencies via composer:

`$ composer install`

2. Commands are run by the `backup` file within `bin/`:

`$ ./bin/backup backup ...`

Commands
--------

**RegisterUser**: An interactive wizard that creates new authentication methods 
and profiles to connect to various external servers. When first running the command,
you will be prompted to choose a service (Google Drive, AWS S3, etc.). After choosing,
you will be walked through requirements for interacting with the chosen
service. For instance when using AWS S3, you will be asked for a bucket,
region and credentials profile.

*This step must be done first before any of the other commands will work.*

**Usage**:

`$ ./bin/backup registerUser`

**Backup**: Saves files based on the user profile provided. Since each user is
created with a specific service, only the alias associated with the given user is
required here. Backup will save directories found in config (files -> sourceFiles)
and place them with a prefix given in the config (files -> location).

**Usage**

`$ ./bin/backup backup userAlias`

**CleanUp**: Permanently deletes old files that are saved based on a user provided
time. Defaults to "2 weeks", meaning that when this command is run, files synced up
that are older than 2 weeks will be deleted. Based on your needs, this command need not
ever be run, or may require a significantly higher time limit.

**Usage**

`$ ./bin/backup cleanUp userAlias [timeLimit:2 weeks]`

**Debug** Displays all users stored in all storage engines. This is useful for
seeing which users have already been created and which settings they use.

**Usage**

`$ ./bin/backup debug`

Notifiers - Error Handling
--------------------------

Occasionally, a scheduled task can fail for one reason or another. Reasons for this could be:
an API limit being hit, or files get misplaced in the operating system. In any case, it's 
important to send out notifications about a task's success or failure so you can intervene 
when needed to ensure regular backups are actually occuring.

Backup-CLI provides this feature, which can be enabled by enabling each notifier in the configuration
 file:
    
    # config/config.yml
    Notifier:
      notificationLevel: 3 #0 - never, 1 - errors, 3 - always
      EmailNotifier:
        enabled: ~ # ~ means the notifier is enabled, leave blank for disabled.
        # ...

When more than one notifier is enabled, all of them will be run. This leaves the
opportunity open for future expansion into more notifiers (such as Slack or Hipchat notifications).

The types of notifications can also be configured via the `notifcationLevel` option.

`0` - Never, no notifications will ever be sent (not recommended).

`1` - Errors, notifications will only be send if an exception occurs (recommended).

`3` - Always, notifications will be sent after all commands, this includes registering users and the debug commands. 

**Notifier Specific information:**

[Email Notifier](docs/Notifiers/EmailNotifier.md)


Read More
---------
[Application Architecture](docs/Architecture.md)

Uploader Specific information:

[Amazon Web Services S3](docs/Uploaders/AmazonS3.md)

[Google Drive (not GCP)](docs/Uploaders/GoogleServiceAccount.md) 


