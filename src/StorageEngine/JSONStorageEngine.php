<?php
/**
 * @copyright Hayden Pierce (hayden@haydenpierce.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Backup\StorageEngine;

use Backup\Exception\InvalidConfigStateException;
use Backup\Exception\JSONException;
use Backup\User\User;

class JSONStorageEngine implements StorageEngineInterface
{
    protected $path;
    protected $users;

    public function __construct($path)
    {
        $fileEmpty = !file_exists($path) || (filesize($path) == 0);

        if($fileEmpty){
            file_put_contents($path, json_encode( [] ));
        }

        $this->path = $path;
        $this->users = json_decode(file_get_contents($path));

        if(JSON_ERROR_NONE !== json_last_error()){
            throw new JSONException(json_last_error_msg());
        }
    }

    public static function getName()
    {
        return "JSON";
    }

    public static function initFromConfig(Array $config)
    {
        if(!isset($config['StorageEngine']['JSON']['file'])){
            throw new InvalidConfigStateException('Could not read file option in Config:StorageEngine:JSON:file');
        } else {
            return new static($config['StorageEngine']['JSON']['file']);
        }

    }
    
    public function persistUser(String $userAlias, User $user)
    {
        $record = new \stdClass();

        if($this->userExists($userAlias)){
            throw new \InvalidArgumentException(sprintf('User %s already exists in the JSON. Cannot add this user.',
                $userAlias
            ));
        };

        $record->alias = $userAlias;
        $record->params = serialize($user);

        $this->users[] = $record;

        $bytes = file_put_contents($this->path, json_encode($this->users));

        return is_int($bytes) && $bytes > 0;
    }

    public function retrieveUser(String $userAlias)
    {
        foreach($this->users as $user){
            if($user->alias == $userAlias){
                $retrievedUser = $user;
                break;
            }
        }

        if(isset($retrievedUser)){
            $retrievedUser = unserialize($retrievedUser->params);

            return $retrievedUser;
        } else {
            return null;
        }

    }

    public function listUsers()
    {
        return array_map(function($user){
            $listItem = [
                'alias' => $user->alias,
                'user' => unserialize($user->params)
            ];
            return $listItem;
        },$this->users);
    }

    private function userExists(String $alias)
    {
        $aliases = array_map(function($user){
            return $user->alias;
        }, $this->users);

        return in_array($alias, $aliases);
    }
}