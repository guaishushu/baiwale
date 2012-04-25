<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author xuanyan <xuanyan@geek-zoo.com>
 *
 */

require dirname(__FILE__).'/baseAction.php';

abstract class adminAction extends baseAction
{
    public function __construct($controller)
    {
        parent::__construct($controller);
        if (!isset($_SESSION['admin_user'])) {
            $this->redirect('admin/login');
        }
    }
}

?>