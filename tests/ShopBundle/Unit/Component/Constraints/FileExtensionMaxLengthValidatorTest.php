<?php

namespace Tests\ShopBundle\Unit\Component\Constraints;

use Shopsys\ShopBundle\Component\Constraints\FileExtensionMaxLength;
use Shopsys\ShopBundle\Component\Constraints\FileExtensionMaxLengthValidator;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class FileExtensionMaxLengthValidatorTest extends ConstraintValidatorTestCase
{
    /**
     * @inheritdoc
     */
    protected function createValidator()
    {
        return new FileExtensionMaxLengthValidator();
    }

    public function testValidateValidLength()
    {
        $file = new File(__DIR__ . '/' . 'non-existent.file', false);

        $constraint = new FileExtensionMaxLength([
            'limit' => 4,
            'message' => 'myMessage',
        ]);

        $this->validator->validate($file, $constraint);
        $this->assertNoViolation();
    }

    public function testValidateInvalidLength()
    {
        $file = new File(__DIR__ . '/' . 'non-existent.file', false);

        $constraint = new FileExtensionMaxLength([
            'limit' => 3,
            'message' => 'myMessage',
        ]);

        $this->validator->validate($file, $constraint);

        $this->buildViolation('myMessage')
            ->setParameter('{{ value }}', '"' . $file->getExtension() . '"')
            ->setParameter('{{ limit }}', 3)
            ->assertRaised();
    }
}