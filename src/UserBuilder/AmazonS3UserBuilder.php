<?php

namespace Backup\UserBuilder;

use Backup\User\AmazonS3User;
use Backup\Util\Readline;
use Symfony\Component\Console\Output\OutputInterface;

class AmazonS3UserBuilder implements UserBuilderInterface
{
    public static function getName()
    {
        return "AmazonS3";
    }

    public function buildUser(OutputInterface $output)
    {
        $output->writeln('IMPORTANT: Backup-CLI MUST have its own bucket to work with that is not used by');
        $output->writeln('anything else. Backup-CLI will PERMANENTLY DELETE old files found in the bucket');
        $output->writeln('when running backup:cleanup. You will need to create a bucket via AWS console.');
        $output->writeln('Example: \'haydenpierce.com.backup\'');
        $bucket = Readline::readline('Bucket name:');

        $output->writeln('Valid examples of regions can be found here:');
        $output->writeln('http://docs.aws.amazon.com/general/latest/gr/rande.html#apigateway_region');
        $output->writeln('Example: \'us-west-2\'');
        $region = Readline::readline('Region:');

        $output->writeln('The credential name that should be used. This correlates to the credential file');
        $output->writeln('found at ~/.aws/credentials. For more information about this see AWS docs:');
        $output->write('http://docs.aws.amazon.com/sdk-for-java/v1/developer-guide/credentials.html#using-the-default-credential-provider-chain');
        $output->writeln('Example: \'default\'');
        $profile = Readline::readline("Profile (default):");
        $profile = empty($profile) ? 'default' : $profile;

        $user = new AmazonS3User();
        $user->setBucket($bucket);
        $user->setRegion($region);
        $user->setProfile($profile);

        return $user;
    }

}