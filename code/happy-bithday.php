<?php
$filename = '/cli/birthdays.txt'; // Путь к файлу

$currentDate = date('d-m');

$lines = file($filename);

foreach ($lines as $line) {
    list($name, $birthdate) = explode(',', $line);
    $birthdate = trim($birthdate);
    // создание костыля
    $year = substr($birthdate, 6);
    $crutch = $currentDate . '-' . $year;

    // Сравнение дат
    if ($birthdate == $crutch) {
        echo "Сегодня день рождения у $name! \n";
    }

}