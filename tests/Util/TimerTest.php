<?php
/**
 * @copyright Hayden Pierce (hayden@haydenpierce.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backup\Tests\Util;

use Backup\Util\Timer;
use PHPUnit\Framework\TestCase;

class TimerTest extends TestCase
{
    public function testEnd()
    {
        $timer = new Timer();

        sleep(1);

        $timer->end();

        sleep(1);

        $interval = $timer->getInterval();
        $this->assertTrue($interval->s == 1, 'Timer should not be allowed to run after calling end() method.');
    }

    public function testGetIntervalDoesntEnd()
    {
        $timer = new Timer();

        for($i = 1; $i < 4; $i++){
            sleep(1);
            $interval = $timer->getInterval();
            $this->assertTrue($interval->s == $i, sprintf('Timer should report that it was only recording for ~%s second.', $i));
        }
    }
}
?>