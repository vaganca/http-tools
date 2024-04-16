<?php
/*
 * This file is part of the Vaganca project.
 *
 * (c) Benjamin Wagner <wagner@vaganca.de>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

use Vaganca\Component\HttpTools\Session\Session;

require '../../vendor/autoload.php';

$session = null;

try {
    $session = new Session();
    $session->start();
} catch (LogicException $exception) {
    echo $exception->getMessage();
}

echo "User heißt: " .$session->get('user') . PHP_EOL;

$session->set('user',['Ben']);
