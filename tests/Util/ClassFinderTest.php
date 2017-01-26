<?php

namespace Backup\Tests\Util;

use Backup\Util\ClassFinder;
use PHPUnit\Framework\TestCase;

class ClassFinderTest extends TestCase
{
    public function setup()
    {
        mkdir(__DIR__ . '/../Temp');
        $validClassFileContent = <<< EOT
<?php

namespace Backup\Tests\Temp;

class Foo {}
EOT;
        $validInterfaceFileContent = <<< EOT
<?php

namespace Backup\Tests\Temp;

interface BazInterface {}
EOT;

        $validBazClassFileContent = <<< EOT
<?php

namespace Backup\Tests\Temp;

class Bar implements BazInterface {}
EOT;

        $CSVFileContent = <<< EOT
firstname, lastname, address
hayden, pierce, 123 fake street
EOT;

        $proceduralPHPFileContent = <<< EOT
<?php

\$one = 1;
\$two = \$one + \$one;
EOT;

        file_put_contents(__DIR__ . '/../Temp/Foo.php', $validClassFileContent);
        file_put_contents(__DIR__ . '/../Temp/BazInterface.php', $validInterfaceFileContent);
        file_put_contents(__DIR__ . '/../Temp/Bar.php', $validBazClassFileContent);
        file_put_contents(__DIR__ . '/../Temp/data.csv', $CSVFileContent);
        file_put_contents(__DIR__ . '/../Temp/addNumbers.php', $proceduralPHPFileContent);
    }

    public function tearDown()
    {
        unlink(__DIR__.'/../Temp/Foo.php');
        unlink(__DIR__.'/../Temp/BazInterface.php');
        unlink(__DIR__.'/../Temp/Bar.php');
        unlink(__DIR__.'/../Temp/data.csv');
        unlink(__DIR__.'/../Temp/addNumbers.php');
        rmdir(__DIR__.'/../Temp');
    }

    public function testDiscoversClasses()
    {
        $expectedClasses = [
            'Backup\Tests\Temp\Foo',
            'Backup\Tests\Temp\Bar'
        ];
        $actualClasses = ClassFinder::getClassesInNamespace('Backup\\Tests\\Temp');

        //The order they are stored in the array is irrelevant.
        sort($expectedClasses);
        sort($actualClasses);

        $this->assertEquals($expectedClasses, $actualClasses);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNonExistentNamespace()
    {
        $expectedClasses = [];
        $actualClasses = ClassFinder::getClassesInNamespace('Does\\Not\\Exist');

        $this->assertEquals($expectedClasses, $actualClasses);
    }

    public function testDiscoversClassesWithInterface()
    {
        $expectedClasses = [
            'Backup\Tests\Temp\Bar'
        ];
        $actualClasses = ClassFinder::getClassesInNamespace('Backup\\Tests\\Temp', 'Backup\\Tests\\Temp\\BazInterface');

        //Resetting the index wasn't a goal of the class so the index may start at 0.
        sort($expectedClasses);
        sort($actualClasses);

        $this->assertEquals($expectedClasses, $actualClasses);
    }
}
?>