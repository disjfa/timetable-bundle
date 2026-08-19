<?php

declare(strict_types=1);

namespace Disjfa\TimetableBundle\Excel;

class ImportResult
{
    /** @var list<array{row: int, field: string, message: string}> */
    private array $errors = [];
    private int $imported = 0;
    private int $skipped = 0;

    public function addError(int $row, string $field, string $message): void
    {
        $this->errors[] = ['row' => $row, 'field' => $field, 'message' => $message];
        ++$this->skipped;
    }

    public function incrementImported(): void
    {
        ++$this->imported;
    }

    /** @return list<array{row: int, field: string, message: string}> */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getImported(): int
    {
        return $this->imported;
    }

    public function getSkipped(): int
    {
        return $this->skipped;
    }

    public function hasErrors(): bool
    {
        return [] !== $this->errors;
    }
}
