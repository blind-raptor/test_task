#!/usr/bin/php
<?php

$begin = microtime(true);

if (count($argv) > 3) {
    print("Only two arguments must be: sourcefile and filter amount \n");
    die;
}

$groupedData = [];
$sourceFile = $argv[1];
$filterAmount = $argv[2];

function getCsvRows($sourceFile) {
    $maxCsvStrLine = 200;
    $fieldsNumber = 3;
    $delimiter = ',';
    $firstLine = TRUE;
    $header = [];
 
    $handle = fopen($sourceFile, 'r');
    if ($handle !== FALSE) {
        while (($row = fgetcsv($handle, $maxCsvStrLine, $delimiter)) !== FALSE) {
            if ($firstLine) {
                $header = $row;
                $firstLine = FALSE;
                continue;
            }

            yield array_combine($header, $row);
        }
        fclose($handle);
    }
}

foreach(getCsvRows($sourceFile) as $row) {
    $groupedData[$row['uid']][$row['date']] = $groupedData[$row['uid']][$row['date']] ?? 0;
    $groupedData[$row['uid']][$row['date']] += $row['sum'];
}

foreach ($groupedData as $userId => $row) {
    $currentAmount = 0;
    ksort($row);
    foreach($row as $date => $sum) {
        $currentAmount += $sum;
        if ($currentAmount >= $filterAmount) {
            echo $userId , ',', $date, "\n";
            break;
        }
    }
}
echo microtime(true)-$begin;
