<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author xuanyan <xuanyan@geek-zoo.com>
 *
 */
class caiji_ifeng
{
    function getOption()
    {
        return array(
            'newstype' => array(
                'type' => 'radio',
                'value' => array(
                    '资讯' => 'news',
                    '财经' => 'finance',       
                    '时尚' => 'fashion',
                    '娱乐' => 'ent'
                )
            ), 

            'option' => array(
                'type' => 'checkbox',
                'value' => array(
                    '资讯' => 'news',
                    '时尚' => 'fashion',
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
        return '凤凰网';
    }
    function getNames()
    {
        return 'ifeng';
    }

    function exec($option)
    {
        if(empty($option)){
            $this->redirect('admin/collection/create');
        }
        foreach($option as $v){
            $contents = file_get_contents('http://'.$v.'.ifeng.com');
            if($v == 'news'){
               $h = '/<h1 id=\"artical_topic\">(.+?)<\/h1>/';
               $t = '/<div id=\"artical_real\">(.*?)<span class=\"ifengLogo\">/ism'; 
               $c = '/<a href="(http:\/\/'.$v.'\.ifeng\.com\/\w+\/[^\"\']+\/\d+_0.shtml)" target=\"_blank\">.+<\/a>/i';
            }else if($v == 'finance'){
               $h = '/<h1 id=\"artical_topic\">(.+?)<\/h1>/';
               $t = '/<div id=\"artical_real\">(.*?)<span class=\"ifengLogo\">/ism'; 
               $c = '/<a href="(http:\/\/'.$v.'\.ifeng\.com\/\w+\/[^\"\']+\/\d+.shtml)" target=\"_blank\">.+<\/a>/i';
            }elseif ($v == 'ent') {
               $h = '/<h1 id=\"artical_topic\">(.+?)<\/h1>/';
               $t = '/<div id=\"artical_real\">(.*?)<span class=\"ifengLogo\">/ism';
               $c = '/<a href="(http:\/\/'.$v.'\.ifeng\.com\/\w+\/[^\"\']+\/\d+_0.shtml)" target=\"_blank\">.+<\/a>/i'; 
            }elseif ($v == 'fashion') {
               $h = '/<h1 id=\"artical_topic\">(.+?)<\/h1>/';
               $t = '/<div id=\"artical_real\">(.*?)<span class=\"ifengLogo\">/ism'; 
               $c = '/<a href="(http:\/\/'.$v.'\.ifeng\.com\/\w+\/[^\"\']+\/\d+_0.shtml)" target=\"_blank\">.+<\/a>/i'; 
            }
           preg_match_all($c,$contents,$arr1);
           $arr1[1] = array_unique($arr1[1]); 
            foreach($arr1[1] as $val){
                $str = file_get_contents($val);
                preg_match($h,$str,$ar1);
                preg_match($t,$str,$ar2);
                $time = time(); 
                if(!empty($ar1) && !empty($ar2)){
                    $title  = $ar1[1];
                    $content   = $ar2[1];
                    $search = array ("'<script[^>]*?>.*?</script>'si",  // 去掉 javascript
                 "'<[\/\!]*?[^<>]*?>'si",           // 去掉 HTML 标记
                 "'([\r\n])[\s]+'",                 // 去掉空白字符
                 "'&(quot|#34);'i",                 // 替换 HTML 实体
                 "'&(amp|#38);'i",
                 "'&(lt|#60);'i",
                 "'&(gt|#62);'i",
                 "'&(nbsp|#160);'i",
                 "'&(iexcl|#161);'i",
                 "'&(cent|#162);'i",
                 "'&(pound|#163);'i",
                 "'&(copy|#169);'i",
                 "'&#(\d+);'e");                    // 作为 PHP 代码运行

                $replace = array ("",
                                  "",
                                  "\\1",
                                  "\"",
                                  "&",
                                  "<",
                                  ">",
                                  " ",
                                  chr(161),
                                  chr(162),
                                  chr(163),
                                  chr(169),
                                  "chr(\\1)");
                $text = preg_replace ($search, $replace, $content);
                    $data = _model('information_list')->create(array('title' => $title,'content' => $text,'source' => 'ifeng','information_class' => $v,'type' => 0,'add_time' => $time));
                };
                
            }
        }  
    }
}


?>