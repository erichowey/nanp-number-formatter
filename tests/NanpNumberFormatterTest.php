<?php

use PHPUnit\Framework\TestCase;
use Erichowey\NanpNumberFormatter\NanpNumberFormatter;

class NanpNumberFormatterTest extends TestCase
{
    /**
     * @return void
     * @test
     */
    public function testEmpty()
    {
        $testNumbers = [
          "",
          null,
        ];

        foreach ($testNumbers as $testNumber) {
            $number = NanpNumberFormatter::format($testNumber);
            $this->assertNotEmpty($number, "Formatter returned nothing when it should have returned an object");
            $this->assertEquals("The number parameter is required", $number->errorMessage, "errorMessage is incorrect");
            $this->assertEquals(false, $number->isValid, "isValid is not false for an invalid number");
            $this->assertEquals("Invalid", $number->e164, "e164 is incorrect");
            $this->assertEquals("Invalid", $number->tendigit, "The tendigit value is incorrect");
            $this->assertEquals("Invalid", $number->elevendigit, "The elevendigit value is incorrect");
            $this->assertEquals("Invalid", $number->uri, "The uri value is incorrect");
            $this->assertEquals("Invalid", $number->nationalFormat, "The nationalFormat value is incorrect");
            $this->assertEquals("Invalid", $number->internationalFormat, "The internationalFormat value is incorrect");
            $this->assertEquals("Invalid", $number->npa, "The npa value is incorrect");
            $this->assertEquals("Invalid", $number->nxx, "The nxx value is incorrect");
            $this->assertEquals("Invalid", $number->line, "The line value is incorrect");
        }
    }

    public function test12125550123()
    {
        $testNumbers = [
            '12125550123',
            '2125550123',
            '(212) 555-0123',
            '1 (212) 555-0123',
            '212.555.0123',
            '+1 (212) 555-0123',
            '+12125550123',
            '   1 (212) 555-0123  '
        ];

        foreach ($testNumbers as $testNumber) {
            $number = NanpNumberFormatter::format($testNumber);
            $this->assertNotEmpty($number, "Formatter returned nothing when it should have returned an object");
            $this->assertEquals("", $number->errorMessage, "errorMessage should be an empty string");
            $this->assertEquals(true, $number->isValid, "isValid is not true for a valid number");
            $this->assertEquals("+12125550123", $number->e164, "e164 is invalid");
            $this->assertEquals("2125550123", $number->tendigit, "The tendigit value is incorrect");
            $this->assertEquals("12125550123", $number->elevendigit, "The elevendigit value is incorrect");
            $this->assertEquals("tel:+12125550123", $number->uri, "The uri value is incorrect");
            $this->assertEquals("(212) 555-0123", $number->nationalFormat, "The nationalFormat value is incorrect");
            $this->assertEquals("+1 212 555 0123", $number->internationalFormat, "The internationalFormat value is incorrect");
            $this->assertEquals("212", $number->npa, "The npa value is incorrect");
            $this->assertEquals("555", $number->nxx, "The nxx value is incorrect");
            $this->assertEquals("0123", $number->line, "The line value is incorrect");
        }
    }

    public function test8008008000()
    {
        $testNumbers = [
            '18008008000',
            '8008008000',
            '(800) 800-8000',
            '1 (800) 800-8000',
            '800.800.8000',
            '+1 (800) 800-8000',
            '+18008008000',
            '   1 (800) 800-8000  '
        ];

        foreach ($testNumbers as $testNumber) {
            $number = NanpNumberFormatter::format($testNumber);
            $this->assertNotEmpty($number, "Formatter returned nothing when it should have returned an object");
            $this->assertEquals("", $number->errorMessage, "errorMessage should be an empty string");
            $this->assertEquals(true, $number->isValid, "isValid is not true for a valid number");
            $this->assertEquals("+18008008000", $number->e164, "e164 is invalid");
            $this->assertEquals("8008008000", $number->tendigit, "The tendigit value is incorrect");
            $this->assertEquals("18008008000", $number->elevendigit, "The elevendigit value is incorrect");
            $this->assertEquals("tel:+18008008000", $number->uri, "The uri value is incorrect");
            $this->assertEquals("(800) 800-8000", $number->nationalFormat, "The nationalFormat value is incorrect");
            $this->assertEquals("+1 800 800 8000", $number->internationalFormat, "The internationalFormat value is incorrect");
            $this->assertEquals("800", $number->npa, "The npa value is incorrect");
            $this->assertEquals("800", $number->nxx, "The nxx value is incorrect");
            $this->assertEquals("8000", $number->line, "The line value is incorrect");
        }
    }

    public function testLetterInput()
    {
        $number = NanpNumberFormatter::format("1800FLOWERS");
        $this->assertNotEmpty($number, "Formatter returned nothing when it should have returned an object");
        $this->assertEquals("", $number->errorMessage, "errorMessage should be an empty string");
        $this->assertEquals(true, $number->isValid, "isValid is not true for a valid number");
        $this->assertEquals("+18003569377", $number->e164, "e164 is invalid");
        $this->assertEquals("8003569377", $number->tendigit, "The tendigit value is incorrect");
        $this->assertEquals("18003569377", $number->elevendigit, "The elevendigit value is incorrect");
        $this->assertEquals("tel:+18003569377", $number->uri, "The uri value is incorrect");
        $this->assertEquals("(800) 356-9377", $number->nationalFormat, "The nationalFormat value is incorrect");
        $this->assertEquals("+1 800 356 9377", $number->internationalFormat, "The internationalFormat value is incorrect");
        $this->assertEquals("800", $number->npa, "The npa value is incorrect");
        $this->assertEquals("356", $number->nxx, "The nxx value is incorrect");
        $this->assertEquals("9377", $number->line, "The line value is incorrect");
    }

    public function testLessThan10Characters()
    {
        $number = NanpNumberFormatter::format("15235");
        $this->assertNotEmpty($number, "Formatter returned nothing when it should have returned an object");
        $this->assertEquals("15235 is less than 10 characters", $number->errorMessage, "errorMessage is incorrect");
        $this->assertEquals(false, $number->isValid, "isValid is not false for an invalid number");
        $this->assertEquals("Invalid", $number->e164, "e164 is incorrect");
        $this->assertEquals("Invalid", $number->tendigit, "The tendigit value is incorrect");
        $this->assertEquals("Invalid", $number->elevendigit, "The elevendigit value is incorrect");
        $this->assertEquals("Invalid", $number->uri, "The uri value is incorrect");
        $this->assertEquals("Invalid", $number->nationalFormat, "The nationalFormat value is incorrect");
        $this->assertEquals("Invalid", $number->internationalFormat, "The internationalFormat value is incorrect");
        $this->assertEquals("Invalid", $number->npa, "The npa value is incorrect");
        $this->assertEquals("Invalid", $number->nxx, "The nxx value is incorrect");
        $this->assertEquals("Invalid", $number->line, "The line value is incorrect");
    }

    // If the string contains any other characters except 0-9, (, ), -, ., +, or space
    public function testInvalidCharacters()
    {
        $number = NanpNumberFormatter::format("1212555^123");
        $this->assertNotEmpty($number, "Formatter returned nothing when it should have returned an object");
        $this->assertEquals("1212555^123 contains invalid characters", $number->errorMessage, "errorMessage is incorrect");
        $this->assertEquals(false, $number->isValid, "isValid is not false for an invalid number");
        $this->assertEquals("Invalid", $number->e164, "e164 is incorrect");
        $this->assertEquals("Invalid", $number->tendigit, "The tendigit value is incorrect");
        $this->assertEquals("Invalid", $number->elevendigit, "The elevendigit value is incorrect");
        $this->assertEquals("Invalid", $number->uri, "The uri value is incorrect");
        $this->assertEquals("Invalid", $number->nationalFormat, "The nationalFormat value is incorrect");
        $this->assertEquals("Invalid", $number->internationalFormat, "The internationalFormat value is incorrect");
        $this->assertEquals("Invalid", $number->npa, "The npa value is incorrect");
        $this->assertEquals("Invalid", $number->nxx, "The nxx value is incorrect");
        $this->assertEquals("Invalid", $number->line, "The line value is incorrect");
    }

    public function testNonNANPNumber()
    {
        $number = NanpNumberFormatter::format("+023212555123");
        $this->assertNotEmpty($number, "Formatter returned nothing when it should have returned an object");
        $this->assertEquals("Only \"+1\" phone numbers are allowed: +023212555123", $number->errorMessage, "errorMessage is incorrect");
        $this->assertEquals(false, $number->isValid, "isValid is not false for an invalid number");
        $this->assertEquals("Invalid", $number->e164, "e164 is incorrect");
        $this->assertEquals("Invalid", $number->tendigit, "The tendigit value is incorrect");
        $this->assertEquals("Invalid", $number->elevendigit, "The elevendigit value is incorrect");
        $this->assertEquals("Invalid", $number->uri, "The uri value is incorrect");
        $this->assertEquals("Invalid", $number->nationalFormat, "The nationalFormat value is incorrect");
        $this->assertEquals("Invalid", $number->internationalFormat, "The internationalFormat value is incorrect");
        $this->assertEquals("Invalid", $number->npa, "The npa value is incorrect");
        $this->assertEquals("Invalid", $number->nxx, "The nxx value is incorrect");
        $this->assertEquals("Invalid", $number->line, "The line value is incorrect");
    }

    public function testNumberTooLong()
    {
        $number = NanpNumberFormatter::format("+13125550123456");
        $this->assertNotEmpty($number, "Formatter returned nothing when it should have returned an object");
        $this->assertEquals("The number should be a valid 10,11 or 12 digit e164 NANP number: +13125550123456", $number->errorMessage, "errorMessage is incorrect");
        $this->assertEquals(false, $number->isValid, "isValid is not false for an invalid number");
        $this->assertEquals("Invalid", $number->e164, "e164 is incorrect");
        $this->assertEquals("Invalid", $number->tendigit, "The tendigit value is incorrect");
        $this->assertEquals("Invalid", $number->elevendigit, "The elevendigit value is incorrect");
        $this->assertEquals("Invalid", $number->uri, "The uri value is incorrect");
        $this->assertEquals("Invalid", $number->nationalFormat, "The nationalFormat value is incorrect");
        $this->assertEquals("Invalid", $number->internationalFormat, "The internationalFormat value is incorrect");
        $this->assertEquals("Invalid", $number->npa, "The npa value is incorrect");
        $this->assertEquals("Invalid", $number->nxx, "The nxx value is incorrect");
        $this->assertEquals("Invalid", $number->line, "The line value is incorrect");
    }

    public function testNonNXXNXXXXXX()
    {
        $number = NanpNumberFormatter::format("2121321234");
        $this->assertNotEmpty($number, "Formatter returned nothing when it should have returned an object");
        $this->assertEquals("The number needs to match the +1NXXNXXXXXX pattern: +12121321234", $number->errorMessage, "errorMessage is incorrect");
        $this->assertEquals(false, $number->isValid, "isValid is not false for an invalid number");
        $this->assertEquals("Invalid", $number->e164, "e164 is incorrect");
        $this->assertEquals("Invalid", $number->tendigit, "The tendigit value is incorrect");
        $this->assertEquals("Invalid", $number->elevendigit, "The elevendigit value is incorrect");
        $this->assertEquals("Invalid", $number->uri, "The uri value is incorrect");
        $this->assertEquals("Invalid", $number->nationalFormat, "The nationalFormat value is incorrect");
        $this->assertEquals("Invalid", $number->internationalFormat, "The internationalFormat value is incorrect");
        $this->assertEquals("Invalid", $number->npa, "The npa value is incorrect");
        $this->assertEquals("Invalid", $number->nxx, "The nxx value is incorrect");
        $this->assertEquals("Invalid", $number->line, "The line value is incorrect");
    }

    public function testWildCardWithFlagFalse()
    {
      $number = NanpNumberFormatter::format("(212) - 555 01**");
      $this->assertNotEmpty($number, "Formatter returned nothing when it should have returned an object");
      $this->assertEquals("(212) - 555 01** contains invalid characters", $number->errorMessage, "errorMessage is incorrect");
      $this->assertEquals(false, $number->isValid, "isValid is not false for an invalid number");
      $this->assertEquals("Invalid", $number->e164, "e164 is incorrect");
      $this->assertEquals("Invalid", $number->tendigit, "The tendigit value is incorrect");
      $this->assertEquals("Invalid", $number->elevendigit, "The elevendigit value is incorrect");
      $this->assertEquals("Invalid", $number->uri, "The uri value is incorrect");
      $this->assertEquals("Invalid", $number->nationalFormat, "The nationalFormat value is incorrect");
      $this->assertEquals("Invalid", $number->internationalFormat, "The internationalFormat value is incorrect");
      $this->assertEquals("Invalid", $number->npa, "The npa value is incorrect");
      $this->assertEquals("Invalid", $number->nxx, "The nxx value is incorrect");
      $this->assertEquals("Invalid", $number->line, "The line value is incorrect");
    }

    public function testWildCards()
    {
      $number = NanpNumberFormatter::format("(212) - 555 01**", true);
      $this->assertNotEmpty($number, "Formatter returned nothing when it should have returned an object");
      $this->assertEquals("", $number->errorMessage, "errorMessage is incorrect");
      $this->assertEquals(true, $number->isValid, "isValid is not true for a valid number");
      $this->assertEquals("+121255501**", $number->e164, "e164 is incorrect");
      $this->assertEquals("21255501**", $number->tendigit, "The tendigit value is incorrect");
      $this->assertEquals("121255501**", $number->elevendigit, "The elevendigit value is incorrect");
      $this->assertEquals("tel:+121255501**", $number->uri, "The uri value is incorrect");
      $this->assertEquals("(212) 555-01**", $number->nationalFormat, "The nationalFormat value is incorrect");
      $this->assertEquals("+1 212 555 01**", $number->internationalFormat, "The internationalFormat value is incorrect");
      $this->assertEquals("212", $number->npa, "The npa value is incorrect");
      $this->assertEquals("555", $number->nxx, "The nxx value is incorrect");
      $this->assertEquals("01**", $number->line, "The line value is incorrect");
    }
}
