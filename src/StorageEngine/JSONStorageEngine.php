<?php
/**
 * @copyright Hayden Pierce (hayden@haydenpierce.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Backup\StorageEngine;

use Backup\Exception\JSONException;
use Backup\User\User;

class JSONStorageEngine implements StorageEngineInterface
{
    protected $path;
    protected $users;

    public function __construct($path)
    {
        if(!file_exists($path)){
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

        $record->alias = $userAlias;
        $record->params = serialize($user);

        $this->users[] = $record;

        file_put_contents($this->path, json_encode($this->users));
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
            $user->id = $user->alias;
            unset($user->alias);
            return (array) $user;
        },$this->users);
    }
}