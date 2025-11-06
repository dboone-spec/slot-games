<?php

// Укажите путь к папке, которую нужно сканировать
$sourceDir = '/var/www/agt/application/logs';
// Укажите путь к папке для старых файлов

$archivePath = '/home/s/bkp/'.date('Y-m-d_H:i:s').'.zip';

$daysOld = 30;

// Проверки
if (!is_dir($sourceDir)) die("Ошибка: Исходная директория не существует!");
if (!class_exists('ZipArchive')) die("Требуется расширение ZipArchive!");

// Создаем архив
$zip = new ZipArchive();
if ($zip->open($archivePath, ZipArchive::CREATE) !== TRUE) {
    die("Не удалось создать архив!");
}

// Ищем и добавляем файлы в архив
$now = time();
$timeAgo = $daysOld * 24 * 60 * 60;
$filesAdded = 0;

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($sourceDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $item) {
    if ($item->isFile() && ($now - $item->getMTime() > $timeAgo)) {
        $filePath = $item->getPathname();

		
        $relativePath = substr($filePath, strlen($sourceDir) + 1);

        if ($zip->addFile($filePath, $relativePath)) {
            $filesAdded++;
            echo "Добавлен в архив: $relativePath\n";
        }
    }
}

$zip->close();
echo "Добавлено файлов: $filesAdded\n";

// Удаляем оригинальные файлы (опционально)
if ($filesAdded > 0) {
    foreach ($iterator as $item) {
        if ($item->isFile() && ($now - $item->getMTime() > $timeAgo)) {
            unlink($item->getPathname());
        }
    }

    // Удаляем пустые директории
    $dirIterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($sourceDir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($dirIterator as $item) {
        if ($item->isDir() && count(scandir($item->getPathname())) <= 2) {
            rmdir($item->getPathname());
        }
    }

    echo "Оригинальные файлы удалены\n";
}

echo "Архив создан: $archivePath\n";