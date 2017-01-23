<?php
/**
 * @copyright Hayden Pierce (hayden@haydenpierce.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backup\Notifier;

interface NotifierInterface
{
    public static function getName();
    public function sendNotification(String $command, Bool $success, String $note);
    public static function initFromConfig($config);
}