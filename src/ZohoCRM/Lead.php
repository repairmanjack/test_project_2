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
			$answer = $answer['response']['result']['Leads'];
			foreach($answer as $leadRow) {
				$leadParams = [];
				foreach($leadRow['FL'] as $leadField) {
					$leadParams[$leadField['val']] = $leadField['content'];
				}
				$currentLead = new Lead($leadParams);
				
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
}