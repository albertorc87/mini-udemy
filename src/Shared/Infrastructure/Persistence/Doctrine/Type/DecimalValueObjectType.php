<?php

declare(strict_types=1);

namespace Udemy\Shared\Infrastructure\Persistence\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DecimalType;
use Udemy\Shared\Domain\ValueObject\DecimalValueObject;

/**
 * Custom Type para mapear Value Objects que extienden DecimalValueObject
 * 
 * Este tipo convierte entre el Value Object del dominio (float) y su representación
 * como decimal en la base de datos (string).
 * 
 * NOTA: Este tipo es genérico y no puede instanciar Value Objects específicos.
 * Para usar este tipo, las propiedades deben ser de tipo float en PHP,
 * y luego se pueden convertir a Value Objects en el dominio.
 */
final class DecimalValueObjectType extends DecimalType
{
	public function getName(): string
	{
		return 'decimal_value_object';
	}

	/**
	 * Convierte el valor de la base de datos (string) a float
	 * 
	 * Los embeddables tienen propiedades float, no DecimalValueObject
	 */
	public function convertToPHPValue($value, AbstractPlatform $platform): ?float
	{
		if ($value === null) {
			return null;
		}

		return (float) $value;
	}

	/**
	 * Convierte el float a su representación en la base de datos (string)
	 * 
	 * Los embeddables tienen propiedades float, no DecimalValueObject
	 */
	public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
	{
		if ($value === null) {
			return null;
		}

		if (is_float($value) || is_int($value)) {
			return (string) $value;
		}

		if (is_string($value)) {
			return $value;
		}

		// También acepta DecimalValueObject por si acaso
		if ($value instanceof DecimalValueObject) {
			return (string) $value->value();
		}

		throw new \InvalidArgumentException(
			sprintf('Expected float, int, string or DecimalValueObject, got %s', gettype($value))
		);
	}

	public function requiresSQLCommentHint(AbstractPlatform $platform): bool
	{
		return true;
	}
}

