<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author xuanyan <xuanyan@geek-zoo.com>
 *
 */


class Action extends ModuleAction
{
    function index()
    {
        //var_dump($this->module->checkUser('xuanyan', '123456789'));
        var_dump($this->module->checkUser('qq349497580', '349497580'));
    }
}

?>