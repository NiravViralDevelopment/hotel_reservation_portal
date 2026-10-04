<?php

namespace App\Support;

use RuntimeException;
use ZipArchive;

class SimpleXlsxReader
{
    /**
     * @return list<list<string|null>>
     */
    public static function rows(string $path, int $sheetIndex = 1): array
    {
        if (! is_file($path)) {
            throw new RuntimeException('Excel file not found.');
        }

        $zip = new ZipArchive;
        if ($zip->open($path) !== true) {
            throw new RuntimeException('Unable to open Excel file.');
        }

        $shared = [];
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedXml !== false) {
            $xml = simplexml_load_string($sharedXml);
            if ($xml !== false) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $shared[] = (string) $si->t;
                    } else {
                        $text = '';
                        foreach ($si->r as $run) {
                            $text .= (string) $run->t;
                        }
                        $shared[] = $text;
                    }
                }
            }
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet'.$sheetIndex.'.xml');
        $zip->close();

        if ($sheetXml === false) {
            throw new RuntimeException('Worksheet not found in Excel file.');
        }

        $sheet = simplexml_load_string($sheetXml);
        if ($sheet === false) {
            throw new RuntimeException('Unable to parse worksheet.');
        }

        $rows = [];
        foreach ($sheet->sheetData->row as $row) {
            $cells = [];
            $max = -1;

            foreach ($row->c as $cell) {
                $ref = (string) $cell['r'];
                if (! preg_match('/^([A-Z]+)/', $ref, $matches)) {
                    continue;
                }

                $col = 0;
                foreach (str_split($matches[1]) as $char) {
                    $col = ($col * 26) + (ord($char) - 64);
                }
                $col--;
                $max = max($max, $col);

                $value = (string) ($cell->v ?? '');
                if ((string) $cell['t'] === 's') {
                    $value = $shared[(int) $value] ?? '';
                }

                $cells[$col] = $value;
            }

            for ($i = 0; $i <= $max; $i++) {
                if (! array_key_exists($i, $cells)) {
                    $cells[$i] = '';
                }
            }

            ksort($cells);
            $rows[] = array_values($cells);
        }

        return $rows;
    }
}
