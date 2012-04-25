<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author xuanyan <xuanyan@geek-zoo.com>
 *
 */

abstract class ModuleAction extends SiteAction
{
	protected $module = null;

    public function __construct($controller)
    {
		parent::__construct($controller);
		$this->module = Module::Load($this->controller->module);
    }

    protected function display($file, $param = array())
    {
        $this->smarty->template_dir = ROOT_PATH.'/module/'.$this->controller->module.'/template';
        parent::display($file, $param);
        // $file = ROOT_PATH.'/module/'.$this->controller->module.'/template/'.$file;
        // $template = $this->smarty->createTemplate($file);
        // foreach ($param as $key => $val) {
        //     $template->assign($key, $val);
        // }
        // $template->display();
    }
}


?>