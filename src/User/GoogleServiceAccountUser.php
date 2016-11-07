<?php
/**
 * @copyright Hayden Pierce (hayden@haydenpierce.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backup\User;

class GoogleServiceAccountUser extends User
{
    protected $appName;
    //With respect to the application root.
    protected $pathToPrivateKeyFile;
    protected $clientId;

    /**
     * @return mixed
     */
    public function getAppName()
    {
        return $this->appName;
    }

    /**
     * @param mixed $appName
     */
    public function setAppName($appName)
    {
        $this->appName = $appName;
    }
    
    /**
     * @return mixed
     */
    public function getPathToPrivateKeyFile()
    {
        return $this->pathToPrivateKeyFile;
    }

    /**
     * @param mixed $pathToPrivateKeyFile
     */
    public function setPathToPrivateKeyFile($pathToPrivateKeyFile)
    {
        $this->pathToPrivateKeyFile = $pathToPrivateKeyFile;
    }

    /**
     * @return mixed
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param mixed $clientId
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }
}