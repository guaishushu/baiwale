<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author yinmingming <yinmingming@geek-zoo.com>
 *
 */


require dirname(__FILE__).'/../lib/adminAction.php';
 
class Action extends adminAction
{
    function exec($caiji)
    {
      session_start();
      if(!$caiji['list_rule']){
        exit('列表规则不能为空！');
      }
      $caiji['url_list'] = explode("\n", $caiji['url_list']);
      $caiji['url_list'] = array_map('trim', $caiji['url_list']);
      $caiji['url_list'] = array_filter($caiji['url_list']);
      foreach ($caiji['url_list'] as $key=>$value){
        $str[] = file_get_contents($value);
        // if ($caiji['bianma'] == 1) {
        //    $str = mb_convert_encoding($str, "utf-8", 'gb2312');
        // }else if($caiji['bianma'] == 2){
        //    $str = mb_convert_encoding($str, "gb2312", 'utf-8');
        // }
        // $caiji['list_rule'] = '$'.$caiji['list_rule'].'$i';
        // preg_match_all($caiji['list_rule'],$str,$arr);
      }
      $caiji['list_rule'] = '$'.$caiji['list_rule'].'$i';
      foreach ($str as $val) {      
        preg_match_all($caiji['list_rule'],$val,$arr);
        $contents[] = $arr[1];
      }

      foreach ($contents as $v) {
        $_SESSION = $v;

        foreach ($_SESSION as $kk => $vv) {
          $strr[] = file_get_contents($vv);
          unset($_SESSION[$kk]);
          echo $kk.'条';
        }
        
          // preg_match($caiji['list_rule'],$v,$arr);
      }
     // $caiji['list_role'] = '$'.$caiji['list_rule'].'$i';
       
        
      
      // foreach ($str as $val) {
      //     preg_match_all($caiji['list_rule'],$val,$arr);
      // }
    }

}


?>
