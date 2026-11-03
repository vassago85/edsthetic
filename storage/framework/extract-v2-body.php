<?php

$h = file_get_contents('C:/Users/pchar/Downloads/esthetic-renovations-v2.html');
$h = preg_replace('/src="data:[^"]+"/', 'src="[IMAGE]"', $h);
$h = preg_replace('/<style>.*?<\/style>/s', '', $h);
file_put_contents(__DIR__.'/v2-body.html', $h);
echo strlen($h), " bytes\n";
