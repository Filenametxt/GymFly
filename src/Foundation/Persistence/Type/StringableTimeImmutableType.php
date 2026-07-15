<?php

namespace App\Foundation\Persistence\Type;

use DateTimeImmutable;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\TimeImmutableType;
use Doctrine\DBAL\Types\Exception\InvalidFormat;

class DateTimeImmutableStringable extends DateTimeImmutable
{
    public function __toString(): string
    {
        return $this->format('H:i:s');
    }
}

class StringableTimeImmutableType extends TimeImmutableType
{
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?DateTimeImmutable
    {
        if ($value == null) {
            return null;
        }

        if ($value instanceof DateTimeImmutable) {
            if ($value instanceof DateTimeImmutableStringable) {
                return $value;
            }
            return new DateTimeImmutableStringable($value->format('Y-m-d H:i:s'));
        }

        $dateTime = DateTimeImmutableStringable::createFromFormat('!' . $platform->getTimeFormatString(), $value);

        if ($dateTime !== false) {
            return $dateTime;
        }

        throw InvalidFormat::new(
            $value,
            static::class,
            $platform->getTimeFormatString(),
        );
    }
}
