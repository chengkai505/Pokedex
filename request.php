<?php
class Pokedex {
	public $ID;
	public $Type = array();
	public $Chinese;
	public $English;
	public $Japanese;
}

if (!isset($argv[1])) {
	header('Content-Type: application/json; charset=utf-8');
}

$request = intval(isset($argv[1]) ? $argv[1] : $_GET['q'], 10);

if (!$request) exit();

$document = new DomDocument;
$document->load('https://wiki.52poke.com/zh-hant/%E5%AE%9D%E5%8F%AF%E6%A2%A6%E5%88%97%E8%A1%A8%EF%BC%88%E6%8C%89%E5%85%A8%E5%9B%BD%E5%9B%BE%E9%89%B4%E7%BC%96%E5%8F%B7%EF%BC%89/%E7%AE%80%E5%8D%95%E7%89%88');
$data = $document->getElementsByTagName('table')[2]->getElementsByTagName('tr');
foreach ($data as $row) {
	$td = $row->getElementsByTagName('td');
	if ($td->length != 4) continue;
	if (intval(substr($td[0]->nodeValue, 1), 10) == $request) {
		$singleData = new Pokedex;
		$singleData->ID = intval(substr($td[0]->nodeValue, 1), 10);
		$singleData->Chinese = substr($td[1]->nodeValue, 0, -1);
		$singleData->Japanese = substr($td[2]->nodeValue, 0, -1);
		$singleData->English = substr($td[3]->nodeValue, 0, -1);
		
		$rawHTML = file_get_contents("https://www.pokemon.com/us/pokedex/{$singleData->English}");
		$typeStart = strpos($rawHTML, '<h3>Type</h3>') + strlen('<h3>Type</h3>');
		$typeEnd = strpos($rawHTML, '</ul>', $typeStart) + strlen('</ul>');
		
		$pokedex = new DomDocument;
		$pokedex->loadHTML(substr($rawHTML, $typeStart, $typeEnd - $typeStart));
		
		foreach ($pokedex->getElementsByTagName('li') as $attr) {
			$singleData->Type[] = preg_replace("/\s+/", "", $attr->nodeValue);
		}
		echo json_encode($singleData, JSON_UNESCAPED_UNICODE);
		break;
	}
}
?>