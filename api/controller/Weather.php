<?php
namespace app\api\controller;
use think\Controller;
class Weather extends Controller{
	public function get(){
		$id = input('id');
      	$url = "https://free-api.heweather.com/s6/weather/forecast?location=CN".$id."&key=5c4e2a77c97647bfa783ecc5c783dc12";
      	$res = file_get_contents($url); 
      	$data = json_decode($res,true);
		if($data){
			$code = 200;
		}else{
			$code = 404;
		}
		$result = [
			'code' => $code,
			'data' => $data
		];
		return json($result);
	}
  	public function getCode(){
    	$city = input('city');
		$model = model('Weather');
		$data = $model->getInfo($city);
		if($data){
			$code = 200;
		}else{
			$code = 404;
		}
		$data = [
			'code' => $code,
			'city_code' => $data
		];
		return json($data);
    }
}
?>