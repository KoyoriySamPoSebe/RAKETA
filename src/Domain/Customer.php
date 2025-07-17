<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Domain;

readonly class Customer
{
    public function __construct(
        private int    $id,
        private string $uuid,
        private string $firstName,
        private string $lastName,
        private string $middlename,
        private string $email
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }


    public function getUuid(): string
    {
        return $this->uuid;
    }
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getMiddleName(): string
    {
        return $this->middlename;
    }
    public function getLastName(): string
    {
        return $this->lastName;
    }
    public function getEmail(): string
    {
        return $this->email;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['id']          ?? 0,
            $data['uuid']              ?? '',
            $data['firstName']         ?? '',
            $data['lastName']          ?? '',
            $data['middleName']        ?? '',
            $data['email']             ?? ''
        );
    }
}
