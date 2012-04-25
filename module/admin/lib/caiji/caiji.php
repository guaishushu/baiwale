<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author xuanyan <xuanyan@geek-zoo.com>
 *
 */

class caiji_caiji
{
    function getOption()
    {
        return array(
            'newstype' => array(
                'type' => 'radio',
                'value' => array(
                    '新闻' => 'news',
                    '体育' => 'sports',
                    '娱乐' => 'ent'
                )
            ), 

            'option' => array(
                'type' => 'checkbox',
                'value' => array(
                    '新闻' => 'news',
                    '体育' => 'sports',
                    '娱乐' => 'ent'
                )
            ),

            'title' => array(
                'type' => 'input',
                'value' => '标题',
            )
        );
    }
    
    function getName()
    {
        return '新浪';
    }
    function getNames()
    {
        return 'sina';
    }

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
          echo $kk.'';
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








// if(!empty($arr1) && !empty($arr2)){
                    //     $title  = $arr1[1];
                    //     $content   = $arr2[1];
                    //     $search = array ("'<script[^>]*?>.*?</script>'si",  // 去掉 javascript
                    //  "'<[\/\!]*?[^<>]*?>'si",           // 去掉 HTML 标记
                    //  "'([\r\n])[\s]+'",                 // 去掉空白字符
                    //  "'&(quot|#34);'i",                 // 替换 HTML 实体
                    //  "'&(amp|#38);'i",
                    //  "'&(lt|#60);'i",
                    //  "'&(gt|#62);'i",
                    //  "'&(nbsp|#160);'i",
                    //  "'&(iexcl|#161);'i",
                    //  "'&(cent|#162);'i",
                    //  "'&(pound|#163);'i",
                    //  "'&(copy|#169);'i",
                    //  "'&#(\d+);'e");                    // 作为 PHP 代码运行

                    // $replace = array ("",
                    //                   "",
                    //                   "\\1",
                    //                   "\"",
                    //                   "&",
                    //                   "<",
                    //                   ">",
                    //                   " ",
                    //                   chr(161),
                    //                   chr(162),
                    //                   chr(163),
                    //                   chr(169),
                    //                   "chr(\\1)");
                    // $text = preg_replace ($search, $replace, $content);
                    //     $data = _model('information_list')->create(array('title' => $title,'content' => $text,'source' => 'ifeng','information_class' => $v,'type' => 0,'add_time' => $time));
                    // };
                    // $this->msg('已采集$count-(本页共'.$count.'条)', 'admin/collection/docaiji');