<?php 

namespace Service;

class Sender 
{
	private static $boundary = 'X-SENDER-BOUNDARY';
	private static $commonParams = [
		'authtoken' => AUTHTOKEN,
		'scope' => 'crmapi'
	];
	private static function getBody(array $params = []) {
		$sendBody = '';
		foreach($params as $param => $value) {
			$sendBody .= '--'. self::$boundary . PHP_EOL;
			$sendBody .= "Content-Disposition: form-data; name=\"{$param}\"" . PHP_EOL;
			$sendBody .= PHP_EOL;
			$sendBody .= $value . PHP_EOL;
		}
		$sendBody && $sendBody .= '--' . self::$boundary . '--' . PHP_EOL;
		return $sendBody;
	}
	public static function get($url, array $params = []) {
		$params = array_merge(self::$commonParams, $params);
		return QueryHelper::execute($url . '?' . http_build_query($params));
	}
	public static function send($url, array $params = []) 
	{
		$params = array_merge(self::$commonParams, $params);
		return QueryHelper::execute($url, [
			'http' => [
				'method'  => 'POST',
				'header'  => 'Content-type: multipart/form-data; boundary=' . self::$boundary,
				'content' => self::getBody($params),
			],
		]);
	}
}