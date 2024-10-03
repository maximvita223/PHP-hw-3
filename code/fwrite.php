<?php

$address = '/cli/birthdays.txt';
$data = "Василий, 05-06-1992";

$fileHandler = fopen($address, 'a');
fwrite($fileHandler, $data);
fclose($fileHandler);