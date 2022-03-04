<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*		email : info@thelia.net                                                      */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/

/**
 * Autoloader find classes for inclusion
 *
 * inspire by classLoader symfony component
 *
 */

class Autoload
{
    protected $directories = [];

    private static $instance = false;

    protected function __construct()
    {}

    /**
     *
     * @return Autoload
     */
    public static function getInstance()
    {
        if (self::$instance === false) self::$instance = new Autoload();
        return self::$instance;
    }


    /**
     *
     * add path directory where autoload can search files
     *
     * @param string $directory
     * @return \Autoload
     */
    public function addDirectory($directory)
    {
        if (substr($directory, strlen($directory)-1,1) == DIRECTORY_SEPARATOR){
            $directory = substr($directory, 0, strlen($directory)-1);
        }

        $this->directories[] = $directory;

        return $this;
    }

    /**
     *
     * add multiple path directory in an array where autoload can search files
     *
     * @param array $directories
     * @return \Autoload
     */
    public function addDirectories(array $directories)
    {
        foreach($directories as $directory){
            $this->addDirectory($directory);
        }

        return $this;
    }

    public function getDirectories()
    {
        return $this->directories;
    }

    /**
     * Registers this instance as an autoloader.
     *
     * @param Boolean $prepend Whether to prepend the autoloader or not
     *
     * @api
     */
    public function register($prepend = false)
    {
        spl_autoload_register(array($this, 'loadClass'), true, $prepend);
    }

    /**
     *
     * @param string $class Name of the class
     */
    public function loadClass($class)
    {
        if ($file = $this->findFile($class)) require_once $file;

    }

    public function findFile($class)
    {
        if ('\\' == $class[0]) $class = substr($class, 1);

        foreach($this->directories as $directory){
            if (is_file($directory.DIRECTORY_SEPARATOR.$class.".class.php")){
                return $directory.DIRECTORY_SEPARATOR.$class.".class.php";
            }

            if (is_file($directory.DIRECTORY_SEPARATOR.$class.".interface.php")){
                return $directory.DIRECTORY_SEPARATOR.$class.".interface.php";
            }
        }
    }
}

?>
