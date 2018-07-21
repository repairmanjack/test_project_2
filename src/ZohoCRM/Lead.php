<?php 

namespace ZohoCRM;

use Service\Sender;
use ZohoCRM\Potential;

class Lead
{
	public function __construct(array $params = [])
	{
		foreach($params as $param => $value) {
			$this->{$param} = $value;
		}
	}
	public function save() 
	{
		$answer = Sender::get("https://crm.zoho.com/crm/private/json/Leads/getMyRecords");
		
		if(!isset($answer['response']['nodata'])) {
			$answer = $answer['response']['result']['Leads']['row'];
			
			$leadList = [];
			
			if(isset($answer['FL'])) {
				$leadList[] = $this->parseLead($answer);
			} else {
				foreach($answer as $leadRow) {
					$leadList[] = $this->parseLead($leadRow);
				}
			}
				
			foreach($leadList as $currentLead) {
				if($currentLead->Phone == $this->Phone) {
					
					// лид с таким номером уже есть, конвертируем
					Sender::send("https://crm.zoho.com/crm/private/json/Leads/convertLead", [
						'xmlData' => new Potential($currentLead->SMOWNERID),
						'leadId' => $currentLead->LEADID,
					]);
					
					return false;
				}				
			}
		}
		
		// лид с таким номером отсутствует, добавляем
		Sender::send("https://crm.zoho.com/crm/private/json/Leads/insertRecords", [
			'xmlData' => $this
		]);
		return true;
	}
	
	public function __toString() 
	{
		$xml = new \SimpleXMLElement("<Leads><row no=\"1\"></row></Leads>");
		$refObj = new \ReflectionObject($this);
		foreach($refObj->getProperties(\ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
			$pName = $reflectionProperty->name;
			$fl = $xml->row->addChild('FL', htmlspecialchars($this->$pName));
			$fl->addAttribute('val', str_replace('_', ' ', $pName));
		}
		return trim($xml->asXML());
	}
	
	private function parseLead($leadRow) {
		$leadParams = [];
		foreach($leadRow['FL'] as $leadField) {
			$leadParams[$leadField['val']] = $leadField['content'];
		}
		return new Lead($leadParams);
	}
}