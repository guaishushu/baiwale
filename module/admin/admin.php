<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author xuanyan <xuanyan@geek-zoo.com>
 *
 */

class adminModule extends Module
{
    function init()
    {
        //require dirname(__FILE__).'/lib/adminAction.php';
        define('ADMIN_PATH', dirname(__FILE__));
    }

 	function search($name1,$name2,$name3,$name4 = '')
    {
        $name1 = Database::getTable($name1);
        $sql = "SELECT * FROM `{$name1}` where $name2 like '%$name3%' $name4";
        return $this->db->getAll($sql);
    }

    function weather($city = '')
    {
        $city = empty($city) ? 'beijing' : $city;
        if (!$weather = Site::getInstance()->cache->get($city)) {
            $content = file_get_contents("http://www.google.com/ig/api?hl=zh-cn&weather=$city");
            if (!$content) {
                return false;
            }
            $content = mb_convert_encoding($content, 'UTF-8', 'GBK');
            $xml = simplexml_load_string($content);

            //$date = $xml->weather->forecast_information->forecast_date->attributes();
            $current = $xml->weather->current_conditions;

            if (empty($current)) {
                return false;
            }

            $condition = $current->condition->attributes(); // 天气
            $temp_c = $current->temp_c->attributes(); // 温度
            $humidity = $current->humidity->attributes(); // 湿度
            $icon = $current->icon->attributes(); // 图标
            $wind = $current->wind_condition->attributes(); // 风向

            $format = $this->get_config('weather_format');

            $weather = array(
                '{condition}' => $condition,
                '{temp_c}'    => $temp_c,
                '{humidity}'  => $humidity,
                '{wind}'      => $wind,
            );

            $weather = strtr($format, $weather);
            
            if (!$weather) {
                return false;
            }

            Site::getInstance()->cache->set($city, $weather, 3600);
        }
        else
        {
            $weather = Site::getInstance()->cache->get($city);
        }
        return $weather;
    }
}
?>