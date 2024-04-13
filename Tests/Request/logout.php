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

header('HTTP/1.1 401 Unauthorized');
