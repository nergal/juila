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
 * @category  Config
 * @package   Juila
 * @author    Artem Poluhovich <nergalic@ya.ru>
 * @copyright 2011 Artem Poluhovich <nergalic@ya.ru>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      https://github.com/Nergal/julia/
 */

return array(
    'installed'     => true,
    'cache'         => false,
    'allowed_sizes' => array(
        '100x100',
        '200x200',
        array(200, 150),
    ),
    'resize_type'   => array(
        'res'    => array(
            'file'  => './types/res.php',
            'class' => 'Resizer',
        ),
        'crop'   => array(
            'file'  => './types/crop.php',
            'class' => 'Crop',
        ),
        'cropr'  => array(
            'file'  => './types/cropr.php',
            'class' => 'CropResized',
        ),
    ),
    'uploads_path'  => 'pictures',
    'cache_path'    => 'cache',
    'folder_mode'   => 0755,
    'file_mode'     => 0644,
);

