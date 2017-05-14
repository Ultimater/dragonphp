<?php
/**
 * DragonPHP : Delightfully simple forum software
 *
 * Licensed under The GNU License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link    http://DragonPHP.com DragonPHP Project
 * @since   1.0.0
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 */
namespace DragonPHP\Cli\Tasks;

use Phalcon\CLI\Task;
use DragonPHP\Mail\SendSpool;
use DragonPHP\Cli\Library\Output;

class SendSpoolTask extends Task
{
    public function mainAction()
    {
        Output::stdout('Send email for post reply');
        $spool = new SendSpool();
        try {
            var_dump($spool->sendRemaining());
        } catch (\Exception $e) {
            echo $e->getMessage(), PHP_EOL;
            echo $e->getTraceAsString();
        }
    }
}
