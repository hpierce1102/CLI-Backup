<?php
/**
 * @copyright Hayden Pierce (hayden@haydenpierce.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backup\Tests\StorageEngine;

use Backup\StorageEngine\SQLiteStorageEngine;
use Backup\User\AmazonS3User;
use PHPUnit\Framework\TestCase;

class SQLiteStorageEngineTest extends TestCase
{
    /** @var  SQLiteStorageEngine */
    protected $storageEngine;

    /** @var  AmazonS3User */
    protected $user;

    /** @var  String */
    protected $alias;

    public function setup()
    {
        $this->user = new AmazonS3User();
        $this->alias = 'user';
    }

    //Ensures that there doesn't happen to be a file with a similar name to the test DB that would get deleted.
    //Seems unlikely - but better safe than sorry.
    public static function setUpBeforeClass()
    {
        $files = scandir(__DIR__);

        foreach($files as $file){
            if(strpos($file, 'TempSQLiteTestDB') === 0){
                throw new \Exception(sprintf("A file \"%s/%s\" exists. Running tests will delete files like this.
In order to prevent unwanted desturction, delete this file before running the tests.",
                    __DIR__,
                    $file));
            }
        }
    }

    public static function tearDownAfterClass()
    {
        $files = scandir(__DIR__);

        foreach($files as $file){
            if(strpos($file, 'TempSQLiteTestDB') === 0){
                unlink(__DIR__ .'/' . $file);
            }
        }
    }

    public function testPersistUser()
    {
        $tempDB = tempnam(__DIR__, 'TempSQLiteTestDB');
        $storageEngine = new SQLiteStorageEngine($tempDB);

        $true = $storageEngine->persistUser($this->alias, $this->user);
        $this->assertTrue($true, 'StorageEngineInterface::persistUser() should return boolean.');

        return $storageEngine;
    }

    /**
     * @param SQLiteStorageEngine $storageEngine
     * @depends testPersistUser
     */
    public function testRetrieveUser($storageEngine)
    {
        $actualUser = $storageEngine->retrieveUser($this->alias);
        $this->assertEquals($this->user, $actualUser);
    }

    /**
     * @param SQLiteStorageEngine $storageEngine
     * @depends testPersistUser
     */
    public function testListUsers($storageEngine)
    {
        $actualUsers = $storageEngine->listUsers();
        $expectedUsers = [
            [
                'alias' => $this->alias,
                'user' => $this->user
            ]
        ];

        $this->assertEquals($expectedUsers, $actualUsers);
    }

    /**
     * @param SQLiteStorageEngine $storageEngine
     * @depends testPersistUser
     * @expectedException \ErrorException
     * @expectedExceptionMessage SQLite3Stmt::execute(): Unable to execute statement: UNIQUE constraint failed: users.alias
     */
    public function testDuplicatePersistUser($storageEngine)
    {
        $storageEngine->persistUser($this->alias, $this->user);
    }
}