<?php

require_once dirname(__FILE__).'/TingClientDublinCoreData.php';
require_once dirname(__FILE__).'/identifier/TingClientRecordIdentifier.php';

class TingClientRecordDataFactory
{
	
	public function fromSearchRecordData($recordData)
	{
		$formats = array('dc' => 'TingClientDublinCoreData');
		
		$data = false;
		$dataArray = array();
		foreach ($formats as $name => $dataClass)
		{
			$data = new $dataClass();
			if (is_array($recordData) &&
					isset($recordData[$name]))
			{
				$dataArray = $recordArray[$name];
			} elseif (is_object($recordData) &&
								isset($recordData->$name)) {
				$dataArray = $recordData->$name;
			}
		}
		
		if (!$data)
		{
			throw new TingClientException('Search record does not contain recognized data format');
		}
		
		foreach ($recordData->dc as $attribute => $value)
		{
			if ($attribute == 'identifier' && $value)
			{	
				$data->$attribute = array();
				foreach ($value as $i => $v)
				{
					array_push($data->$attribute, TingClientRecordIdentifier::factory($v));
				}
			}
			else
			{
				$data->$attribute = $value;
			}
		}
		
		return $data;	
	}
	
}
