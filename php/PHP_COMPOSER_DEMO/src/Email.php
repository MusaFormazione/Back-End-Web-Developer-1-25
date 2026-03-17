<?php declare(strict_types=1);

/**
 * @author  <>
 */
final class Email
{
	private $email;

	// phpdoc

	/**
	 * @param string $email
	 */
	private function __construct(string $email)
	{
		// Questa funzione va la validazione della mail
		$this->ensureIsValidEmail($email);

		// Qui assegnamo la mail a un campo della classe Email
		$this->email = /*$email*/;
	}

	/**
	 * Create an Email object from a string
	 * @param string $email
	 * @return Email
	 */
	public static function fromString(string $email): self
	{
		return new self($email);
	}

	public function __toString(): string
	{
		return $this->email;
	}

	private function ensureIsValidEmail(string $email): void
	{
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			throw new InvalidArgumentException(
				sprintf(
					'"%s" is not a valid email address',
					$email
				)
			);
		}
	}
}