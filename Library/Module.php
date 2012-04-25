<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author xuanyan <xuanyan@geek-zoo.com>
 *
 */

class Module
{
	protected $db = null;
    protected $name = null;
	private static $class = array();

	public static function Load($name)
	{
		if (!isset(self::$class[$name])) {
			$moduleFile = ROOT_PATH.'/module/'.$name.'/'.$name.'.php';
			require_once $moduleFile;
			$class = $name.'Module';
			self::$class[$name] = new $class(Site::getInstance()->db);
		}

		return self::$class[$name];
	}

	function init(){}

	function install()
    {
	    return true;
	}

	function uninstall(){}

    function __construct($db)
    {
		$this->db = $db;
        if (!file_exists(ROOT_PATH.'/tmp/Module')) {
            mkdir(ROOT_PATH.'/tmp/Module', 0777);
        }
		$this->name = substr(get_class($this), 0, -6);
        $checkFile = ROOT_PATH.'/tmp/Module/'.$this->name.'.lock';

        $this->init();

        if (!file_exists($checkFile)) {
            if ($this->install()) {                
                touch($checkFile);
            } else {
                $this->uninstall();
            }
        }
    }
}


?>