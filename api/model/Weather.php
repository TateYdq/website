<?php
namespace app\api\model;
use think\Model;
use think\Db;
class Weather extends Model
{
    public function getInfo($city)
    {
        $result = Db::name('ins_county')->where('county_name', $city)->select();
      	$res = $result[0]["weather_code"];
        return $res;
    }

    public function getNewsList()
    {
        $res = Db::name('news')->select();
        return $res;
    }
}
?>