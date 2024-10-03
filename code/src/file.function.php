<?php
const MINIMUM_SUPPORTED_YEAR = 1970;

// function readAllFunction(string $address) : string {
function readAllFunction(array $config): string
{
    $address = $config['storage']['address'];

    if (file_exists($address) && is_readable($address)) {
        $file = fopen($address, "rb");

        $contents = '';

        while (!feof($file)) {
            $contents .= fread($file, 100);
        }

        fclose($file);
        return $contents;
    } else {
        return handleError("Файл не существует");
    }
}

// function addFunction(string $address) : string {
function addFunction(array $config): string
{
    $address = $config['storage']['address'];

    // $address = '/cli/birthdays.txt';

    // Запрашиваем имя и дату рождения у пользователя
    $name = trim(readline("Введите имя: "));
    $date = trim(readline("Введите дату рождения в формате ДД-ММ-ГГГГ: "));
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
        return "Запись $data добавлена в файл $address";
    } catch (Exception $e) {
        return "Произошла ошибка: " . $e->getMessage();
    }
}
function validate(string $date): bool
    {
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

    function getMaxDaysInMonth($month, $year)
    {
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

    function isLeapYear($year)
    {
        return ($year % 4 == 0 && $year % 100 != 0) || ($year % 400 == 0);
    }
// function clearFunction(string $address) : string {
function clearFunction(array $config): string
{
    $address = $config['storage']['address'];

    if (file_exists($address) && is_readable($address)) {
        $file = fopen($address, "w");

        fwrite($file, '');

        fclose($file);
        return "Файл очищен";
    } else {
        return handleError("Файл не существует");
    }
}

function helpFunction()
{
    return handleHelp();
}

function readConfig(string $configAddress): array|false
{
    return parse_ini_file($configAddress, true);
}

function readProfilesDirectory(array $config): string
{
    $profilesDirectoryAddress = $config['profiles']['address'];

    if (!is_dir($profilesDirectoryAddress)) {
        mkdir($profilesDirectoryAddress);
    }

    $files = scandir($profilesDirectoryAddress);

    $result = "";

    if (count($files) > 2) {
        foreach ($files as $file) {
            if (in_array($file, ['.', '..']))
                continue;

            $result .= $file . "\r\n";
        }
    } else {
        $result .= "Директория пуста \r\n";
    }

    return $result;
}

function readProfile(array $config): string
{
    $profilesDirectoryAddress = $config['profiles']['address'];

    if (!isset($_SERVER['argv'][2])) {
        return handleError("Не указан файл профиля");
    }

    $profileFileName = $profilesDirectoryAddress . $_SERVER['argv'][2] . ".json";

    if (!file_exists($profileFileName)) {
        return handleError("Файл $profileFileName не существует");
    }

    $contentJson = file_get_contents($profileFileName);
    $contentArray = json_decode($contentJson, true);

    $info = "Имя: " . $contentArray['name'] . "\r\n";
    $info .= "Фамилия: " . $contentArray['lastname'] . "\r\n";

    return $info;
}
function searchRemove(array $config)
{
    $filename = $config['storage']['address'];
    $searchText = trim(readline("Введите имя или дату для удаления: "));

    if ($searchText === '') {
        echo "Необходимо ввести имя или дату для удаления.\n";
    } else {
        $fileContent = file_get_contents($filename);
        $pattern = "/\Q$searchText\E/";
        if (preg_match($pattern, $fileContent)) {
            $tempFile = tempnam('/tmp', 'birthdays');
            $handle = fopen($tempFile, 'w+');
            foreach (explode("\n", $fileContent) as $line) {
                if (!preg_match($pattern, $line)) {
                    fwrite($handle, $line . "\n");
                }
            }
            fclose($handle);
            rename($tempFile, $filename);
            return "Строка успешно удалена!\n";
        } else {
            return "Строка не найдена.\n";
        }
    }
    return "";
}

function birthday(array $config)
{
    $filename = $config['storage']['address'];
    // fopen($filename);
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
            return "Сегодня день рождения у $name! \n";
        }
    }
    return "";
}
