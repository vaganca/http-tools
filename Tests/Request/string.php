<?php
/*
 * This file is part of the Vaganca project.
 *
 * (c) Benjamin Wagner <wagner@vaganca.de>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

use Vaganca\Component\HttpTools\Request;

require '../../vendor/autoload.php';

function verify($a, $b): bool
{
    $us = 'name';
    $psw = 'pass';
    return ($a==$us && $b==$psw);
}

$request = new Request($_GET,$_POST,$_ENV,$_COOKIE,$_SERVER);

if(!$request->headers->has('authorization')) {
    try {

        $user = $request->headers->get('php_auth_user');
        $password = $request->headers->get('php_auth_pass');

        if (verify($user, $password)) {
            $user = $request->server->get('PHP_AUTH_USER');
            echo "Willkommen $user!";
        }
    } catch (Exception $e) {

    }
} else {
    header('WWW-Authenticate: Basic realm="My Realm"');
    echo "Login erforderlich!";
    exit;
}

?>
<pre>
    <code>
        <?php print_r($request->server->getHeaders()); ?>
    </code>
</pre>

