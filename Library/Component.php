<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author xuanyan <xuanyan@geek-zoo.com>
 *
 */


class Component
{
    private $config = array();
    private $classMap = array();

    function __construct($config = array())
    {
        $this->config = $config;
    }

    public function __set($name, $value)
    {
        $this->classMap[$name] = $value;
    }

    public function __get($name)
    {
        if (!isset($this->classMap[$name])) {
            if (!isset($this->config[$name])) {
                throw new Exception("cant load ".$name);
            }
            $config = $this->config[$name];
            if (!isset($config['class'])) {
                throw new Exception("must set class attr");
            }
            if (is_array($config['class'])) {
                $class = array_shift($config['class']);
                if (stripos($class, '::')) {
                    list($class, $method) = explode('::', $class);
                    $this->classMap[$name] = call_user_func_array(array($class, $method), $config['class']);
                } else {
                    $this->classMap[$name] = call_user_func_array(array(new ReflectionClass($class), 'newInstance'), $config['class']);
                }
            } else {
                $this->classMap[$name] = new $config['class']();
            }
            unset($config['class']);
            foreach ($config as $key => $val) {
                if (is_numeric($key) && is_array($val)) {
                    $fun = array_shift($val);
                    call_user_func_array(array($this->classMap[$name], $fun), $val);
                } else {
                    $this->classMap[$name]->$key = $val;
                }
            }
        }

        return $this->classMap[$name];
    }
}

?>