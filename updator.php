<?php
$origin = json_decode(file_get_contents('index.json'), true);
for ($i = 0; $i < 893; $i++) {
	$origin[$i] = json_decode(file_get_contents('https://path.to/request.php?id=' . ($i + 1)), true);
	file_put_contents('index.json', json_encode($origin, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	printf("\r%03d", $i + 1);
}
?>