Amazon Web Services S3
======================

Copies files to AWS S3.

Required Parameters
---------------
The following items will be required when you attempt to create an Amazon S3 user:

**Bucket**: AmazonS3 sections off space by categorizing objects into buckets. Backup-CLI
will not attempt to create your bucket, so you must first create the bucket yourself via
the AWS console and provide the bucket name here.

It is important the Backup-CLI gets it's own bucket and does not share one with another
service. The `cleanUp` command will have access to everything in the bucket provided
and will delete anything that is older than the age you provide.

Following Java-esque naming conventions, I generally name buckets prefixed by my
domain name:

Example: `com.haydenpierce.backup`

**Region**: Each bucket is placed into a region within AWS. The region the bucket
resides here is required here.

Note that a valid region might look like `us-west-2` and not `oregon`.

A list of valid regions can be found in [AWS documentation](http://docs.aws.amazon.com/general/latest/gr/rande.html#s3_region):


Example: `us-west-2`.

**Profile**: To authenticate with AWS, a credential file should be used which contains
one or more key/id pairs. Each of these pairs has a name. Provide the name of one of the pairs
here:

More information on the credential file can be found in the [AWS documentation](http://docs.aws.amazon.com/sdk-for-java/v1/developer-guide/credentials.html).

    [default]
    aws_access_key_id = /* Placeholder */
    aws_secret_access_key = /* Placeholder */
    
Example: `default`

Tips
====

By default, running `backup` will overwrite files with the same keys that were already there. That means, there
is no explicit need to run `cleanUp` since only one copy of the backup will be available.
This is typically not preferable, and more versions of the files are desired.

[Versioning should be enabled](http://docs.aws.amazon.com/AmazonS3/latest/dev/Versioning.html) in the bucket with a lifecycle rule to remove aging backups to
maintain multiple versions of the files. This is all done within the AWS S3 console and
cannot be done in Backup-CLI.
