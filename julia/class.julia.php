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

require_once APPLICATION_PATH."julia/exception.julia.php";
require_once APPLICATION_PATH."julia/abstract.julia.php";

/**
 * Resizer class
 *
 * @category Resizer
 * @package  Juila
 * @author   Artem Poluhovich <nergalic@ya.ru>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: 0.0.1a
 * @link     https://github.com/Nergal/julia/
 */
final class Juila
{
    /**
     * Configuration
     * @var array $config
     */
    protected $config = array();

    /**
     * Available resizers
     * @var array
     */
    protected $loaded_resizers = array();

    /**
     * Available sizes
     * @var array
     */
    protected $loaded_sizes = array();

    /**
     * Setup all needed settings
     *
     * @param array $config Configuration array
     *
     * @return self
     */
    public function __construct(Array $config)
    {
        $this->config = (object) $config;

        $this->setupResizers();
        $this->setupSizes();
    }

    /**
     * Parse picture request
     *
     * @param string $uri Requested URI
     *
     * @return void
     */
    public function setRequest($uri)
    {
        $path = explode('/', $uri);
        $path = array_filter($path);

        try {
            if (count($path) < 3) {
                throw new Juila_Exception('No work for juila');
            }

            $cache_path = array_shift($path);
            $image_name = array_pop($path);
            $settings   = array_pop($path);

            $real_cache_path = explode('/', $this->config->cache_path);
            $real_cache_path = end($real_cache_path);

            if ($real_cache_path == $cache_path) {
                list($type, $width, $height) = $this->validateSettings($settings);

                if ($real_path = $this->checkImage($path, $image_name)) {
                    $image_path = $this->_clearPath(APPLICATION_PATH.$uri, false);

                    if ($this->config->cache AND file_exists($image_path)) {
                        $data = file_get_contents($image_path);
                        // TODO: correct define mime-type
                        $mime = 'image/png';
                        $image = array($data, $mime);
                    } else {
                        $resizer = $this->loaded_resizers[$type];
                        $image = $resizer->proceed($real_path, $width, $height);
                    }

                    if ($this->config->cache) {
                        $this->cacheImage($image, $image_path);
                    }
                    $this->renderResponse($image);
                } else {
                    throw new Juila_Exception('Source image not found');
                }
            } else {
                throw new Juila_Exception('Wrong cache path');
            }
        } catch (Juila_Exception $e) {
            $this->return404($e->getMessage());
        }
    }

    /**
     * Return resized image
     *
     * @param array $data Response data
     *
     * @return void
     */
    public function renderResponse($data)
    {
        if ( ! empty($data)) {
            list($content, $type) = $data;

            header('Content-type: '.$type);
            echo $content;
        }
    }


    /**
     * Load available resizers
     *
     * @uses   self::$loaded_resizers
     * @throws Juila_Exception
     * @return void
     */
    protected function setupResizers()
    {
        foreach ($this->config->resize_type as $name => $data) {
            $filename = $data['file'];
            $classname = 'Juila_Types_'.$data['class'];

            if ( ! in_array($filename[0], array('.', DIRECTORY_SEPARATOR))) {
                $filename = APPLICATION_PATH.$filename;
            }

            $filename = realpath($filename);
            if ($filename) {
                include_once $filename;

                if (class_exists($classname)) {
                    $object = new $classname;

                    $this->loaded_resizers[$name] = $object;
                    unset($object);
                } else {
                    throw new Juila_Exception('Wrong resized definition');
                }
            }
        }
    }

    /**
     * Load available sizes
     *
     * @uses   self::$loaded_sizes
     *
     * @return void
     */
    protected function setupSizes()
    {
        $expr = '#^(?P<x>[0-9]+)x(?P<y>[0-9]+)$#i';
        foreach ($this->config->allowed_sizes as $size) {
            if (is_array($size)) {
                $size = implode('x', $size);
            }

            if (preg_match($expr, $size)) {
                $this->loaded_sizes[] = $size;
            }
        }

        $this->loaded_sizes = array_unique($this->loaded_sizes, SORT_NUMERIC);
    }

    /**
     * Render 404 error
     *
     * @param string $message Exception message
     *
     * @return void
     */
    protected function return404($message = null)
    {
        echo "<h1>Not found, {$message}</h1>";
        die();
    }

    /**
     * Cache image to filesystem
     *
     * @param array  $image Image array
     * @param string $path  Image cache path
     *
     * @return void
     */
    protected function cacheImage($image, $path)
    {
        $cache_dir = dirname($path);

        var_dump($cache_dir);
        if ( ! mkdir($cache_dir, $this->config->folder_mode, true)) {
            throw new Juila_Exception('Unable to create cache directory');
        }

        return file_put_contents($path, $image[0]);
    }

    /**
     * Check if image exists
     *
     * @param array  $path Sliced image path
     * @param string $name Real image name
     *
     * @return string
     */
    protected function checkImage(Array $path, $name)
    {
        array_push($path, $name);
        array_unshift($path, $this->config->uploads_path);
        array_unshift($path, APPLICATION_PATH);

        $path = $this->_clearPath($path);

        return $path;
    }

    /**
     * Validate resize settings
     *
     * @param string $settings Settings url part
     *
     * @throws Julia_Exception
     * @return array
     */
    protected function validateSettings($settings)
    {
        $expr = '#^(?P<type>[a-z0-9]+?)_(?P<x>[0-9]+?)x(?P<y>[0-9]+?)$#ui';
        if (preg_match($expr, $settings, $match)) {
            if ( ! in_array($match['type'], array_keys($this->loaded_resizers))) {
                throw new Juila_Exception('Unknow resizer');
            }

            $size = $match['x'].'x'.$match['y'];
            if ( ! in_array($size, $this->loaded_sizes)) {
                throw new Juila_Exception('Unknow size');
            }

            return array($match['type'], $match['x'], $match['y']);
        }

        throw new Juila_Exception('Wrong settings');
    }


    /**
     * Clear image path
     *
     * @param string|array $path    Image path
     * @param boolean      $do_real Do realpath over path
     *
     * @return string|boolean
     */
    private function _clearPath($path, $do_real = true)
    {
        if (is_array($path)) {
            $path = implode(DIRECTORY_SEPARATOR, $path);
        }

        $path = str_replace('//', '/', $path);

        if ($do_real) {
            $path = realpath($path);
        }

        return $path;
    }
}
