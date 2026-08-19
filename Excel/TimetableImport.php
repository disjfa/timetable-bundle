<?php

declare(strict_types=1);

namespace Disjfa\TimetableBundle\Excel;

use Disjfa\TimetableBundle\Entity\Timetable;
use Disjfa\TimetableBundle\Entity\TimetableDate;
use Disjfa\TimetableBundle\Entity\TimetableItem;
use Disjfa\TimetableBundle\Entity\TimetablePlace;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\String\Slugger\AsciiSlugger;

class TimetableImport
{
    private AsciiSlugger $slugger;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $this->slugger = new AsciiSlugger();
    }

    public function import(Timetable $timetable, File $file): ImportResult
    {
        $result = new ImportResult();

        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();

        // Read headers from row 1
        $headers = [];
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        for ($col = 1; $col <= $highestColumnIndex; ++$col) {
            $header = (string) $sheet->getCell(Coordinate::stringFromColumnIndex($col).'1')->getValue();
            if ('' !== $header) {
                $headers[$header] = $col;
            }
        }

        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; ++$row) {
            $rowData = $this->readRow($sheet, $headers, $row);

            // Skip entirely empty rows
            if ([] === array_filter($rowData)) {
                continue;
            }

            $rowErrors = $this->validateRow($rowData, $row);
            if ([] !== $rowErrors) {
                foreach ($rowErrors as [$field, $message]) {
                    $result->addError($row, $field, $message);
                }
                continue;
            }

            $dateAt = $this->parseDateTime($rowData['date_at']);
            $start = $this->parseDateTime($rowData['start']);
            $end = $this->parseDateTime($rowData['end']);

            $timetableDate = $this->resolveDate($timetable, $rowData, $dateAt);
            $timetablePlace = $this->resolvePlace($timetable, $rowData);

            $item = $this->resolveItem($timetable, $rowData, $timetableDate, $timetablePlace);
            $item->setTitle((string) $rowData['title']);
            $item->setDescription((string) ($rowData['description'] ?? ''));
            $item->setDateStart($start);
            $item->setDateEnd($end);

            $this->entityManager->persist($item);
            $result->incrementImported();
        }

        $this->entityManager->persist($timetable);
        $this->entityManager->flush();

        return $result;
    }

    /**
     * @param array<string, int> $headers
     *
     * @return array<string, mixed>
     */
    private function readRow(Worksheet $sheet, array $headers, int $row): array
    {
        $data = [];
        foreach ($headers as $header => $col) {
            $cell = $sheet->getCell(Coordinate::stringFromColumnIndex($col).$row);
            $data[$header] = $this->getCellValue($cell);
        }

        return $data;
    }

    private function getCellValue(Cell $cell): mixed
    {
        $value = $cell->getValue();

        // PhpSpreadsheet stores Excel date serials as floats
        if (is_float($value) || is_int($value)) {
            if (Date::isDateTime($cell)) {
                return Date::excelToDateTimeObject((float) $value);
            }
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $rowData
     *
     * @return list<array{0: string, 1: string}>
     */
    private function validateRow(array $rowData, int $row): array
    {
        $errors = [];

        $required = ['title', 'date_at', 'start', 'end', 'place_title'];
        foreach ($required as $field) {
            if (empty($rowData[$field])) {
                $errors[] = [$field, sprintf('Row %d: "%s" is required.', $row, $field)];
            }
        }

        // If required fields are missing we can stop here
        if ([] !== $errors) {
            return $errors;
        }

        // Date constraints
        $dateAt = $this->parseDateTime($rowData['date_at']);
        $start = $this->parseDateTime($rowData['start']);
        $end = $this->parseDateTime($rowData['end']);

        if (null === $dateAt) {
            $errors[] = ['date_at', sprintf('Row %d: "date_at" is not a valid date.', $row)];
        }
        if (null === $start) {
            $errors[] = ['start', sprintf('Row %d: "start" is not a valid datetime.', $row)];
        }
        if (null === $end) {
            $errors[] = ['end', sprintf('Row %d: "end" is not a valid datetime.', $row)];
        }

        if ($dateAt instanceof \DateTime && $start instanceof \DateTime && $end instanceof \DateTime) {
            if ($start > $end) {
                $errors[] = ['start', sprintf('Row %d: "start" must be before or equal to "end".', $row)];
            }

            $windowStart = (clone $dateAt)->setTime(0, 0, 0);
            $windowEnd = (clone $windowStart)->modify('+2 days');

            if ($start <= $windowStart || $start >= $windowEnd) {
                $errors[] = ['start', sprintf('Row %d: "start" must be greater than date_at 00:00 and smaller than 2 days after date_at.', $row)];
            }

            if ($end <= $windowStart || $end >= $windowEnd) {
                $errors[] = ['end', sprintf('Row %d: "end" must be greater than date_at 00:00 and smaller than 2 days after date_at.', $row)];
            }
        }

        return $errors;
    }

    private function parseDateTime(mixed $value): ?\DateTime
    {
        if ($value instanceof \DateTimeInterface) {
            return \DateTime::createFromInterface($value);
        }

        if (!is_string($value) || '' === $value) {
            return null;
        }

        try {
            return new \DateTime($value);
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * @param array<string, mixed> $rowData
     */
    private function resolveDate(
        Timetable $timetable,
        array $rowData,
        \DateTime $dateAt,
    ): TimetableDate {
        $dateId = (string) ($rowData['date_id'] ?? '');
        $dateAtKey = $dateAt->format('Y-m-d');

        foreach ($timetable->getDates() as $existing) {
            if ('' !== $dateId && $existing->getId() === $dateId) {
                return $existing;
            }
            if ($existing->getDateAt()->format('Y-m-d') === $dateAtKey) {
                return $existing;
            }
        }

        // Create new TimetableDate and add it to the timetable
        $timetableDate = new TimetableDate();
        $timetableDate->setTimetable($timetable);
        $timetableDate->setDateAt($dateAt);
        $title = (string) ($rowData['date_title'] ?? '');
        $timetableDate->setTitle('' !== $title ? $title : $dateAtKey);

        $timetable->getDates()->add($timetableDate);

        return $timetableDate;
    }

    /**
     * @param array<string, mixed> $rowData
     */
    private function resolvePlace(
        Timetable $timetable,
        array $rowData,
    ): TimetablePlace {
        $placeId = (string) ($rowData['place_id'] ?? '');
        $placeTitle = (string) ($rowData['place_title'] ?? '');
        $placeTitleSlug = $this->toKebab($placeTitle);

        foreach ($timetable->getPlaces() as $existing) {
            if ('' !== $placeId && $existing->getId() === $placeId) {
                return $existing;
            }
            if ($this->toKebab($existing->getTitle()) === $placeTitleSlug) {
                return $existing;
            }
        }

        // Create new TimetablePlace and add it to the timetable
        $timetablePlace = new TimetablePlace();
        $timetablePlace->setTimetable($timetable);
        $timetablePlace->setTitle($placeTitle);
        $timetablePlace->setSeqnr($timetable->getPlaces()->count() + 1);

        $timetable->getPlaces()->add($timetablePlace);

        return $timetablePlace;
    }

    /**
     * @param array<string, mixed> $rowData
     */
    private function resolveItem(
        Timetable $timetable,
        array $rowData,
        TimetableDate $timetableDate,
        TimetablePlace $timetablePlace,
    ): TimetableItem {
        $itemId = (string) ($rowData['id'] ?? '');
        $title = (string) ($rowData['title'] ?? '');
        $titleSlug = $this->toKebab($title);

        foreach ($timetable->getDates() as $date) {
            foreach ($date->getItems() as $existing) {
                if ('' !== $itemId && $existing->getId() === $itemId) {
                    return $existing;
                }

                if ($this->toKebab($existing->getTitle()) === $titleSlug) {
                    return $existing;
                }
            }
        }

        $item = new TimetableItem($timetablePlace, $timetableDate);

        return $item;
    }

    private function toKebab(string $value): string
    {
        return strtolower((string) $this->slugger->slug($value));
    }
}
