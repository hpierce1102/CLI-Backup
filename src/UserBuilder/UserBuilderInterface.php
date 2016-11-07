<?php
/**
 * @copyright Hayden Pierce (hayden@haydenpierce.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backup\UserBuilder;

use Symfony\Component\Console\Output\OutputInterface;

interface UserBuilderInterface
{
    public static function getName();
    public function buildUser(OutputInterface $output);
}