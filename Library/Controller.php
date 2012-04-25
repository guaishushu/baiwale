<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2011 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author xuanyan <xuanyan@geek-zoo.com>
 *
 */

class Controller
{
    public $format = null;
    public $params = array();
    private $actionDir = null;
    private $moduleDir = array();

    public $module = '';
    public $controller = '';
    public $action = '';
    public $url = '';

    private static function getValue($value, $default)
    {
        if (is_string($default)) {
            return trim($value);
        }
        if (is_int($default)) {
            return intval($value);
        }
        if (is_array($default)) {
            return (array)$value;
        }

        return floatval($value);
    }

    public function getModulePath()
    {
        if ($this->moduleDir) {
            return $this->moduleDir[0].'/'.$this->module;
        }
        return '';
    }

    public function setActionDir($dir)
    {
        $this->actionDir = $dir;
    }

    public function setModuleDir($moduleDir, $controllerDir)
    {
        $this->moduleDir = func_get_args();
    }

    private function getDispatchParams($path, $tmp)
    {
        if (empty($tmp)) {
            $this->params = array(
                'controller' => 'index',
                'action'     => 'index',
                'param'      => $tmp,
                'file'       => $path.'/index.php'
            );
            $this->controller = 'index';
            $this->action = 'index';
            
            return $this->params;
        }

        $count = count($tmp);

        for ($i = 0; $i < $count; $i++) {
            if (!is_dir($path.'/'.$tmp[$i])) {
                break;
            }
            $path .= '/'.$tmp[$i];
        }

        if (isset($tmp[$i])) {
            $controller = $tmp[$i];
            $i++;
            if (isset($tmp[$i])) {
                $action = $tmp[$i];
                $i++;
            } else {
                $action = 'index';
            }
        } else {
            $controller = 'index';
            $action = 'index';
        }

        $file = $path.'/'.$controller.'.php';

        $i && $tmp = array_slice($tmp, $i);

        $this->params = array(
            'controller' => $controller,
            'action'     => $action,
            'param'      => $tmp,
            'file'       => $file
        );

        $this->controller = $controller;
        $this->action = $action;

        return $this->params;
    }

    /**
     * dispatch url function
     *
     * @param string $url
     * @param string $path
     * @param string $delimiter
     * @return mix
     */
    public function run($url, $delimiter = '/')
    {
        $t_path = $path = $this->actionDir;


        $url = trim($url, ' '.$delimiter);


        // trim the url extention (xxx/xxx.html or yyy/yyy.asp or any extention)
        if (($pos = strrpos($url, '.')) !== false) {
            $this->format = substr($url, $pos+1);
            $url = substr($url, 0, $pos);
        }
        $this->url = $url;

        $tmp = $url ? array_filter(explode($delimiter, $url)) : array();
        $param = $this->getDispatchParams($this->actionDir, $tmp);

        if (!file_exists($param['file'])) {
            if (isset($tmp[0]) && $this->moduleDir) {
                $this->module = array_shift($tmp);
                $param = $this->getDispatchParams($this->moduleDir[0].'/'.$this->module.'/'.$this->moduleDir[1], $tmp);
                if (!file_exists($param['file'])) {
                    throw new Exception("Module Controller not exists: {$param['controller']}", 404);
                }
            } else {
                throw new Exception("Controller not exists: {$param['controller']}", 404);
            }
        }

        include $param['file'];

        $class = new Action($this);

        try {
            $method = new ReflectionMethod($class, $param['action']);
            if ($method->getNumberOfParameters() > 0) {
                $ps = array();
                foreach($method->getParameters() as $i => $val)
                {
                    $name = $val->getName();
                    $default = $val->isDefaultValueAvailable() ? $val->getDefaultValue() : '';
                    if (isset($param['param'][$i])) {
                        $ps[] = self::getValue($param['param'][$i], $default);
                    } elseif (isset($_GET[$name])) {
                        $ps[] = self::getValue($_GET[$name], $default);
                    } else {
                        $ps[] = $default;
                    }
                }
                return $method->invokeArgs($class, $ps);
            }
        } catch (ReflectionException $e) {
            
        } catch (Exception $e) {
            throw $e;
        }

        return call_user_func_array(array($class, $param['action']), $param['param']);
    }
}

?>