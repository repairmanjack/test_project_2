<?php 

namespace Service;

class QueryHelper
{
	public static function execute($url, $contextParams = []) {
		$answer = json_decode(file_get_contents($url, false, stream_context_create($contextParams)), true);
		if(isset($answer['response']['error'])) {
			throw new \Exception("Error code: {$answer['response']['error']['code']}. Error message: {$answer['response']['error']['message']}");
		} 
		return $answer;
	}
}