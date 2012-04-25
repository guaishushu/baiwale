<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author xuanyan <xuanyan@geek-zoo.com>
 *
 */


class ServicesModule extends Module
{
    public $path = '';

    function init()
    {
        $this->path = dirname(__FILE__);
    }

    function install()
    {
        $dirs = array(
            $this->path.'/attachments'
        );

        foreach ($dirs as $key => $val) {
            if (!file_exists($val)) {
                mkdir($val, 0777);
            }
        }

        return true;
    }
}

?>