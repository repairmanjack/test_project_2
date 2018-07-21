<?php 

namespace ZohoCRM;

class Potential 
{
	protected $assignTo;
	public function __construct($assignTo) 
	{
		$this->assignTo = $assignTo;
	}
	
	public function __toString() 
	{
		$xml = new \SimpleXMLElement("<Potentials><row no=\"1\"></row></Potentials>");
		foreach([
			'createPotential' => 'true',
			'assignTo' => $this->assignTo,
			'notifyLeadOwner' => 'true',
			'notifyNewEntityOwner' => 'true',
		] as $propertyName => $propertyValue) {
			$option = $xml->row->addChild('option', $propertyValue);
			$option->addAttribute('val', $propertyName);
		}
		
		$row2 = $xml->addChild('row');
		$row2->addAttribute('no', 2);
		
		foreach([
			'Potential Name' => 'Sample Potential',
			'Closing Date' => date("m/d/Y", strtotime("+3 month")),
			'Potential Stage' => 'Closed Won',
			'Contact Role' => 'Покупка',
			'Amount' => '100500.42',
			'Probability' => '100'
		] as $propertyName => $propertyValue) {
			$option = $row2->addChild('FL', $propertyValue);
			$option->addAttribute('val', $propertyName);
		}
		
		return trim($xml->asXML());
	}
}