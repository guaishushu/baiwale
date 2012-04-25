<?php

class WebApplication implements SplObserver
{
    public function update(SplSubject $subject)
    {
        if (!defined('HTTPS')) {
            if (isset($_SERVER['HTTPS']) && !strcasecmp($_SERVER['HTTPS'], 'on')) {
                define('HTTPS', 1);
            } else {
                define('HTTPS', 0);
            }
        }

        if (!defined('SITE_URL')) {
            $site_url = HTTPS ? 'https://' : 'http://';
            $site_url.= isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];

            if (isset($_SERVER['SERVER_PORT'])) {
                if ((HTTPS && $_SERVER['SERVER_PORT'] != 443) || ($_SERVER['SERVER_PORT'] != 80)) {
                    $site_url.= ':'.$_SERVER['SERVER_PORT'];
                }
            }

          // auto check subdir
          if (isset($_SERVER['PHP_SELF'])) {
              $site_url.= dirname($_SERVER['PHP_SELF']);
          } elseif (isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT']) === 0) {
              $site_url.= dirname(substr($_SERVER['SCRIPT_FILENAME'], strlen($_SERVER['DOCUMENT_ROOT'])));
          }

          define('SITE_URL', rtrim($site_url, '/'));
        }

        // fix SCRIPT_URL
        if (empty($_SERVER['SCRIPT_URL'])) {
            if (!empty($_SERVER['REDIRECT_URL'])) {
                $_SERVER['SCRIPT_URL'] = $_SERVER['REDIRECT_URL'];
            } elseif (!empty($_SERVER['REQUEST_URI'])) {
                $p = parse_url($_SERVER['REQUEST_URI']);
                $_SERVER['SCRIPT_URL'] = $p['path'];
            }
        }

        if (get_magic_quotes_gpc()) {
            $_GET    = self::stripslashes_recursive($_GET);
            $_POST   = self::stripslashes_recursive($_POST);
            $_COOKIE = self::stripslashes_recursive($_COOKIE);
        }

        $this->run();
    }

    protected function run()
    {
        if (strpos($_SERVER['SCRIPT_URL'], 'index.php') !== false || $_SERVER['SCRIPT_URL'] == '/') {
            $url = @$_GET['url'];
        } else {
            $url = $_SERVER['SCRIPT_URL'];
            $t = explode('/', SITE_URL, 4);
            if (isset($t[3])) {
                $url = ltrim($url, '/');
                $url = substr($url, strlen($t[3]));
            }
            $url == '/' && $url = @$_GET['url'];
        }
        $c = new Controller();
        $c->setActionDir(ROOT_PATH.'/controller');
        $c->setModuleDir(ROOT_PATH.'/module', 'controller');

        return $c->run($url);
    }

    private static function stripslashes_recursive($array)
    {
        $array = is_array($array) ? array_map(array(__CLASS__, 'stripslashes_recursive'), $array) : stripslashes($array);

        return $array;
    }
}

// helper function
function _model($name = null) {
    static $models = array();

    if (!isset($name)) {
        $name = '__null__';
    }

    if (!isset($models[$name])) {
        $file = ROOT_PATH."/model/$name.php";
        $t = explode('/', $name);
        $table_name = end($t);

        if (file_exists($file)) {
            require_once $file;
            $class = $table_name.'_model';
        } else {
            $class = 'Model';
        }
        
        $models[$name] = new $class(Site::getInstance()->db);

        if ($name !== '__null__') {
            $models[$name]->table = Database::getTable($table_name);
        }
    }

    return $models[$name];
}

function getClienip() {
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
         $onlineip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
         $onlineip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
         $onlineip = $_SERVER['REMOTE_ADDR'];
    } else {
        return 'unknown';
    }

    return filter_var($onlineip, FILTER_VALIDATE_IP) !== false ? $onlineip : 'unknown';
}

function _GET($key = '', $default = '') {
    if (empty($key)) {
        return $_GET;
    }
    if (!isset($_GET[$key])) {
        return $default;
    }
    if (is_string($default)) {
        return trim($_GET[$key]);
    }
    if (is_int($default)) {
        return intval($_GET[$key]);
    }
    if (is_array($default)) {
        return (array)$_GET[$key];
    }
    return floatval($_GET[$key]);
}

function _POST($key = '', $default = '') {
    if (empty($key)) {
        return $_POST;
    }
    if (!isset($_POST[$key])) {
        return $default;
    }
    if (is_string($default)) {
        return trim($_POST[$key]);
    }
    if (is_int($default)) {
        return intval($_POST[$key]);
    }
    if (is_array($default)) {
        return (array)$_POST[$key];
    }
    return floatval($_POST[$key]);
}

?>