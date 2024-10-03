<?php

$address = '/cli/birthdays.txt';

// Запрашиваем имя и дату рождения у пользователя
$name = trim(readline("Введите имя: "));
$date = trim(readline("Введите дату рождения в формате ДД-ММ-ГГГГ: "));
const MINIMUM_SUPPORTED_YEAR = 1970;
// Валидация данных
if (!validate($date)) {
    echo "Введена некорректная информация";
    exit();
}

try {
    $fileHandler = fopen($address, 'a');

    if ($fileHandler === false) {
        throw new Exception("Ошибка открытия файла");
    }

    $data = $name . ", " . $date . "\r\n";

    if (!fwrite($fileHandler, $data)) {
        throw new Exception("Ошибка записи в файл");
    }

    fclose($fileHandler);
    echo "Запись $data добавлена в файл $address";
} catch (Exception $e) {
    echo "Произошла ошибка: " . $e->getMessage();
}

function validate(string $date): bool {
    $dateBlocks = explode("-", $date);

    if (count($dateBlocks) < 3 || !is_numeric($dateBlocks[0]) || !is_numeric($dateBlocks[1]) || !is_numeric($dateBlocks[2])) {
        return false;
    }

    list($day, $month, $year) = $dateBlocks;

    // Проверка дня месяца
    $maxDaysInMonth = getMaxDaysInMonth($month, $year);
    if ($day > $maxDaysInMonth) {
        return false;
    }

    // Проверка минимального года
    if ($year < MINIMUM_SUPPORTED_YEAR) {
        return false;
    }

    return true;
}

function getMaxDaysInMonth($month, $year) {
    switch ($month) {
        case '01':
            return 31;
        case '02':
            return isLeapYear($year) ? 29 : 28;
        case '03':
            return 31;
        case '04':
            return 30;
        case '05':
            return 31;
        case '06':
            return 30;
        case '07':
            return 31;
        case '08':
            return 31;
        case '09':
            return 30;
        case '10':
            return 31;
        case '11':
            return 30;
        case '12':
            return 31;
        default:
            return 0;
    }
}

function isLeapYear($year) {
    return ($year % 4 == 0 && $year % 100 != 0) || ($year % 400 == 0);
}

