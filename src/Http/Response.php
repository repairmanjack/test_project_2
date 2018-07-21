<?php 

namespace Http;

class Response
{
	private $headers;
	private $body;
	private function addHeader($header, $value = '') 
	{
		$this->headers[] = $value ? [$header, $value] : $header;
	}
	private function setBody($body) 
	{
		$this->body = $body;
	}
	public function __construct($body, array $headers = []) 
	{
		$this->headers = $headers;
		$this->body = $body;
	}
	public function withHeader($header, $value = '') 
	{
		$new = clone $this;
		$new->addHeader($header, $value);
		return $new;
	}
	public function withBody($body) 
	{
		$new = clone $this;
		$new->setBody($body);
		return $new;
	}
	public function emit() {
		
		foreach($this->headers as $header) {
			if(is_array($header)) {
				list($headerTitle, $headerValue) = $header;
				header("{$headerTitle}: {$headerValue}");				
			} else {
				header($header);
			}
		}
		echo $this->body;
	}
}