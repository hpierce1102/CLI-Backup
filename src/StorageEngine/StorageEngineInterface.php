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
    public static function getName();
    public function persistUser(String $userAlias, User $user);
    public function retrieveUser(String $userAlias);

    /*
     * This method must return an multidimensional array where the 2nd dimension contains an array like:
     * array(
     *  'alias' => 'name',
     *  'user' => $userInstance
     * )
     * The alias cannot be provided as the key, because this method is intended for helping with debugging.
     * We would not want users to be overwritten if multiple users existed with the same alias - that would
     * defeat the purpose.
     */
    public function listUsers();
}