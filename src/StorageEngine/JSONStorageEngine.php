<?php
/**
 * @copyright Hayden Pierce (hayden@haydenpierce.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Backup\StorageEngine;

use Backup\User\User;

class JSONStorageEngine implements StorageEngineInterface
{
    public static function initFromConfig(Array $config)
    {
        // TODO: Implement initFromConfig() method.
    }
    
    public function persistUser(String $userAlias, User $user)
    {
        // TODO: Implement persistUser() method.
    }

    public function retrieveUser(String $userAlias)
    {
        // TODO: Implement retrieveUser() method.
    }


}