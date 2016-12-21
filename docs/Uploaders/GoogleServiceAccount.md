Google Service Account
======================

Uploads files to a service account's Google Drive space and shares it with anther Google
user. This is done to prevent Backup-CLI from having access to a real user's
actual Google Drive files.

Required Parameters
-------------------

The following items will be requried when you attempt to create a new Google Service Account 
user:

**AppName**: The name of the application registered in the GCP console that was associated
with the service account.

**Path To Private Key File**: Keys must be created in order to authenticate the account.
The private key file should be a json file that looks like this:

    {
      "type": "service_account",
      "project_id": "",
      "private_key_id": "",
      "private_key": "",
      "client_email": "",
      "client_id": "",
      "auth_uri": "",
      "token_uri": "",
      "auth_provider_x509_cert_url": "",
      "client_x509_cert_url": ""
    }
    
By default, it will be expected to be saved at `config/GoogleServiceAccountSecret.json`, inside
same directory with `config.yml`.

**Client Id**: Client ID can be found in the JSON file that was provided with the 
private key download.

**Google Apps Email Share Target**: After the service account uploads the files, it will
attempt to share them with this user. This parameter should be a any Google account, either 
Gmail account or another G Suite account. Consent from this user is not required,
so any files share with them will become instantly without further action.

Notes
-----

Uploading with this uploader is manually rate limited to stay within free limitations of a service
account. Expect backups to take a while when using this uploader.

I have no idea if "[Storage](https://cloud.google.com/storage/)" is related to Google Drive space.
It's possible that buying additional Storage space will add more space to the account's Drive
limitations (seems likely?). Without trying that, there is a limit of 15 GB of space.

