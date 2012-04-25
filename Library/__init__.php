<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author xuanyan <xuanyan@geek-zoo.com>
 *
 */

defined('ROOT_PATH') || define('ROOT_PATH', getcwd());
define('LIB_PATH', dirname(__FILE__));

//$backtrace = current(debug_backtrace());
//define('ROOT_PATH', dirname($backtrace['file']));
final class Site
{
    private static $instance = null;
    private $component = null;
    private $classMap = array();
    private $appendClassMap = array();

    private function __construct() {

        $this->appendClassMap = array(
            'Cache'=> LIB_PATH.'/3rd/SuperNuts/Cache.php',
            'Smarty' => LIB_PATH.'/3rd/Smarty/Smarty.class.php',
            'Database' => LIB_PATH.'/Database/Database.php',
            'Databas2e' => LIB_PATH.'/Database/Database.php',
        );

        if (!file_exists(ROOT_PATH.'/tmp')) {
            mkdir(ROOT_PATH.'/tmp', 0777);
        }

        spl_autoload_register(array($this, '__autoload'));
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __get($name)
    {
        return $this->component->$name;
    }

    public function registAutoload($classMap = null)
    {
        $this->appendClassMap = array_merge($this->appendClassMap, $classMap);

        return $this;
    }

    public function run($obj = null)
    {
        if (isset($obj) && ($obj instanceof SplObserver)) {
            $subject = new Subject();
            $subject->attach($obj);
            $subject->notify();
        }
    }

    public function setComponent($config)
    {
        $this->component = new Component($config);

        return $this;
    }

    private function __autoload($classname)
    {
        if (!$this->classMap) {
            $classMapFile = ROOT_PATH.'/tmp/'.md5(json_encode($this->appendClassMap)).'.php';
            if (!file_exists($classMapFile)) {
                $this->classMap = $this->appendClassMap;
                $iterator = new DirectoryIterator(dirname(__FILE__));
                foreach ($iterator as $fileinfo) {
                    if ($fileinfo->isFile()) {
                        $this->classMap[basename($fileinfo->getFilename(), '.php')] = $fileinfo->getPathname();
                    }
                }
                file_put_contents($classMapFile, '<?php return '.var_export($this->classMap, true).'; ?>');
            } else {
                $this->classMap = require $classMapFile;
            }
        }
        if (isset($this->classMap[$classname])) {
            require_once $this->classMap[$classname];
        }
    }
}

?>