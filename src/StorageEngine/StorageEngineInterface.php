<?php
/**
 * @copyright Hayden Pierce (hayden@haydenpierce.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Backup\StorageEngine;

use Backup\User\User;

interface StorageEngineInterface
{
    public static function initFromConfig(Array $config);
    public function persistUser(String $userAlias, User $user);
    public function retrieveUser(String $userAlias);
}