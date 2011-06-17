<?php

/**
 * Julia Pictures Resizer
 *
 * Copyright (c) 2011, Artem Poluhovich <nergalic@ya.ru>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * PHP Version 5
 *
 * @category  Resizer
 * @package   Juila
 * @author    Artem Poluhovich <nergalic@ya.ru>
 * @copyright 2011 Artem Poluhovich <nergalic@ya.ru>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      https://github.com/Nergal/julia/
 */

define('APPLICATION_PATH', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);

require_once APPLICATION_PATH."julia/exception.julia.php";

$config_path = realpath(APPLICATION_PATH.'config.php');
if ($config_path) {
    $config = include_once $config_path;
} else {
    throw new Juila_Exception('Config file not found');
}

if ( ! $config['installed']) {
    $install_script = dirname($_SERVER['REQUEST_URI']).'install.php';
    header('Location: '.$install_script);
    die();
}

require_once APPLICATION_PATH."julia/class.julia.php";
$julia = new Juila($config);

$julia->setRequest($_SERVER['REQUEST_URI']);
