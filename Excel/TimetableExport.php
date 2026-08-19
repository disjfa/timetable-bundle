<?php

declare(strict_types=1);

namespace Disjfa\TimetableBundle\Excel;

use Disjfa\TimetableBundle\Entity\Timetable;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\String\Slugger\AsciiSlugger;

class TimetableExport
{
    // Column keys that should be formatted as datetime (Y-m-d H:i)
    private const DATETIME_COLUMNS = ['start', 'end'];
    // Column keys that should be formatted as date (Y-m-d)
    private const DATE_COLUMNS = ['date_at'];

    public function export(Timetable $timetable): StreamedResponse
    {
        $data = [];
        foreach ($timetable->getDates() as $date) {
            foreach ($date->getItems() as $item) {
                $data[] = [
                    'id' => $item->getId(),
                    'title' => $item->getTitle(),
                    'description' => $item->getDescription(),
                    'start' => $item->getDateStart(),
                    'end' => $item->getDateEnd(),
                    'date_id' => $date->getId(),
                    'date_title' => $date->getTitle(),
                    'date_at' => $date->getDateAt(),
                    'place_id' => $item->getPlace()->getId(),
                    'place_title' => $item->getPlace()->getTitle(),
                ];
            }
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if (!empty($data)) {
            $headers = array_keys($data[0]);

            // Write header row
            $col = 1;
            foreach ($headers as $header) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($col).'1', $header);
                ++$col;
            }

            // Build a map of header key => column index for format application
            $headerIndex = array_flip($headers);

            // Write data rows
            $row = 2;
            foreach ($data as $rowData) {
                $col = 1;
                foreach ($rowData as $value) {
                    $cellCoord = Coordinate::stringFromColumnIndex($col).$row;
                    if ($value instanceof \DateTimeInterface) {
                        $sheet->setCellValue($cellCoord, Date::PHPToExcel($value));
                    } else {
                        $sheet->setCellValue($cellCoord, $value);
                    }
                    ++$col;
                }
                ++$row;
            }

            // Apply number formats to date/datetime columns
            $lastRow = count($data) + 1;
            foreach (self::DATETIME_COLUMNS as $key) {
                if (isset($headerIndex[$key])) {
                    $colLetter = Coordinate::stringFromColumnIndex($headerIndex[$key] + 1);
                    $sheet->getStyle($colLetter.'2:'.$colLetter.$lastRow)
                        ->getNumberFormat()
                        ->setFormatCode('yyyy-mm-dd hh:mm');
                }
            }
            foreach (self::DATE_COLUMNS as $key) {
                if (isset($headerIndex[$key])) {
                    $colLetter = Coordinate::stringFromColumnIndex($headerIndex[$key] + 1);
                    $sheet->getStyle($colLetter.'2:'.$colLetter.$lastRow)
                        ->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);
                }
            }

            // Auto-size all columns; cap description at a max width
            foreach ($headers as $index => $key) {
                $colLetter = Coordinate::stringFromColumnIndex($index + 1);
                if ('description' === $key) {
                    $sheet->getColumnDimension($colLetter)->setWidth(60);
                    $sheet->getStyle($colLetter.'2:'.$colLetter.$lastRow)
                        ->getAlignment()
                        ->setWrapText(true);
                } else {
                    $sheet->getColumnDimension($colLetter)->setAutoSize(true);
                }
            }
        }

        $slugger = new AsciiSlugger();
        $slug = strtolower((string) $slugger->slug($timetable->getTitle()));
        $date = (new \DateTimeImmutable())->format('Y-m-d');
        $filename = sprintf('%s-%s.xlsx', $slug, $date);

        $writer = new Xlsx($spreadsheet);

        $response = new StreamedResponse(function () use ($writer): void {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $filename));
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
