<?php

$filename = '/cli/birthdays.txt'; 

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
        echo "Строка успешно удалена!\n";
    } else {
        echo "Строка не найдена.\n";
    }
}