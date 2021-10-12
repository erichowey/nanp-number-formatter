<?php

use PHPUnit\Framework\TestCase;
use Erichowey\NanpNumberFormatter\NanpNumberFormatter;
use Erichowey\NanpNumberFormatter\NanpNumberFormatterException;

class NanpNumberFormatterTest extends TestCase
{
    /** @test */
    public function it_throws_an_exception_on_empty()
    {
        $this->expectException(NanpNumberFormatterException::class);
        $this->expectExceptionMessage('The number parameter is required');
        NanpNumberFormatter::format('');
    }

    /** @test */
    public function it_throws_an_exception_on_null()
    {
        $this->expectException(NanpNumberFormatterException::class);
        $this->expectExceptionMessage('The number parameter is required');
        NanpNumberFormatter::format(null);
    }

    /** @test */
    public function it_can_format_a_local_number()
    {
        $testNumbers = [
            '12125550123',
            '2125550123',
            '(212) 555-0123',
            '1 (212) 555-0123',
            '212.555.0123',
            '212-555-0123',
            '1-212-555-0123',
            '+1 (212) 555-0123',
            '+12125550123',
            '   1 (212) 555-0123  '
        ];

        foreach ($testNumbers as $testNumber) {
            $number = NanpNumberFormatter::format($testNumber);
            $this->assertNotEmpty($number, 'Formatter returned nothing when it should have returned an object');
            $this->assertEquals('+12125550123', $number->e164, 'e164 is invalid');
            $this->assertEquals('2125550123', $number->tendigit, 'The tendigit value is incorrect');
            $this->assertEquals('12125550123', $number->elevendigit, 'The elevendigit value is incorrect');
            $this->assertEquals('tel:+12125550123', $number->uri, 'The uri value is incorrect');
            $this->assertEquals('212.555.0123', $number->dotFormat, 'The dotFormat is incorrect');
            $this->assertEquals('212-555-0123', $number->hyphenFormat, 'The hyphenFormat is incorrect');
            $this->assertEquals('(212) 555-0123', $number->nationalFormat, 'The nationalFormat value is incorrect');
            $this->assertEquals('1 (212) 555-0123', $number->nationalFormatPlusOne, 'The nationalFormatPlusOne value is incorrect');
            $this->assertEquals('+1 212 555 0123', $number->internationalFormat, 'The internationalFormat value is incorrect');
            $this->assertEquals('212', $number->npa, 'The npa value is incorrect');
            $this->assertEquals('555', $number->nxx, 'The nxx value is incorrect');
            $this->assertEquals('0123', $number->line, 'The line value is incorrect');
        }
    }

    /** @test */
    public function it_can_format_a_toll_free_number()
    {
        $testNumbers = [
            '18008008000',
            '8008008000',
            '(800) 800-8000',
            '1 (800) 800-8000',
            '800.800.8000',
            '800-800-8000',
            '1-800-800-8000',
            '+1 (800) 800-8000',
            '+18008008000',
            '   1 (800) 800-8000  '
        ];

        foreach ($testNumbers as $testNumber) {
            $number = NanpNumberFormatter::format($testNumber);
            $this->assertNotEmpty($number, 'Formatter returned nothing when it should have returned an object');
            $this->assertEquals('+18008008000', $number->e164, 'e164 is invalid');
            $this->assertEquals('8008008000', $number->tendigit, 'The tendigit value is incorrect');
            $this->assertEquals('18008008000', $number->elevendigit, 'The elevendigit value is incorrect');
            $this->assertEquals('tel:+18008008000', $number->uri, 'The uri value is incorrect');
            $this->assertEquals('800.800.8000', $number->dotFormat, 'The dotFormat is incorrect');
            $this->assertEquals('800-800-8000', $number->hyphenFormat, 'The hyphenFormat is incorrect');
            $this->assertEquals('(800) 800-8000', $number->nationalFormat, 'The nationalFormat value is incorrect');
            $this->assertEquals('1 (800) 800-8000', $number->nationalFormatPlusOne, 'The nationalFormatPlusOne value is incorrect');
            $this->assertEquals('+1 800 800 8000', $number->internationalFormat, 'The internationalFormat value is incorrect');
            $this->assertEquals('800', $number->npa, 'The npa value is incorrect');
            $this->assertEquals('800', $number->nxx, 'The nxx value is incorrect');
            $this->assertEquals('8000', $number->line, 'The line value is incorrect');
        }
    }

    /** @test */
    public function it_can_format_vanity_numbers()
    {
        $number = NanpNumberFormatter::format('1800FLOWERS');
        $this->assertNotEmpty($number, 'Formatter returned nothing when it should have returned an object');
        $this->assertEquals('+18003569377', $number->e164, 'e164 is invalid');
        $this->assertEquals('8003569377', $number->tendigit, 'The tendigit value is incorrect');
        $this->assertEquals('18003569377', $number->elevendigit, 'The elevendigit value is incorrect');
        $this->assertEquals('tel:+18003569377', $number->uri, 'The uri value is incorrect');
        $this->assertEquals('800.356.9377', $number->dotFormat, 'The dotFormat is incorrect');
        $this->assertEquals('800-356-9377', $number->hyphenFormat, 'The hyphenFormat is incorrect');
        $this->assertEquals('(800) 356-9377', $number->nationalFormat, 'The nationalFormat value is incorrect');
        $this->assertEquals('1 (800) 356-9377', $number->nationalFormatPlusOne, 'The nationalFormatPlusOne value is incorrect');
        $this->assertEquals('+1 800 356 9377', $number->internationalFormat, 'The internationalFormat value is incorrect');
        $this->assertEquals('800', $number->npa, 'The npa value is incorrect');
        $this->assertEquals('356', $number->nxx, 'The nxx value is incorrect');
        $this->assertEquals('9377', $number->line, 'The line value is incorrect');
    }

    /** @test */
    public function it_throws_an_exception_when_a_number_is_less_than_10_characters()
    {
        $this->expectException(NanpNumberFormatterException::class);
        $this->expectExceptionMessage('15235 is less than 10 characters');
        NanpNumberFormatter::format('15235');
    }

    /**
     * If the string contains any other characters except 0-9, (, ), -, ., +, or space
     * @test
     */
    public function it_throws_an_exception_on_invalid_characters()
    {
        $this->expectException(NanpNumberFormatterException::class);
        $this->expectExceptionMessage('1212555^123 contains invalid characters');
        NanpNumberFormatter::format('1212555^123');
    }

    /** @test */
    public function it_throws_an_exception_on_non_nanp_numbers()
    {
        $this->expectException(NanpNumberFormatterException::class);
        $this->expectExceptionMessage('Only "+1" phone numbers are allowed: +023212555123');
        NanpNumberFormatter::format('+023212555123');
    }

    /** @test */
    public function it_throws_an_exception_when_the_number_is_too_long()
    {
        $this->expectException(NanpNumberFormatterException::class);
        $this->expectExceptionMessage('The number should be a valid 10,11 or 12 digit e164 NANP number: +13125550123456');
        NanpNumberFormatter::format('+13125550123456');
    }

    /** @test */
    public function it_throws_an_exception_when_the_number_doesnt_match_the_1NXXNXXXXXX_pattern()
    {
        $this->expectException(NanpNumberFormatterException::class);
        $this->expectExceptionMessage('The number needs to match the +1NXXNXXXXXX pattern: +12121321234');
        NanpNumberFormatter::format('2121321234');
    }

    /** @test */
    public function it_throws_an_exception_if_wildcards_are_inputted_but_not_enabled()
    {
        $this->expectException(NanpNumberFormatterException::class);
        $this->expectExceptionMessage('(212) - 555 01** contains invalid characters');
        NanpNumberFormatter::format('(212) - 555 01**');
    }

    /** @test */
    public function it_can_format_with_wildcards()
    {
        $number = NanpNumberFormatter::format('(212) - 555 01**', true);
        $this->assertNotEmpty($number, 'Formatter returned nothing when it should have returned an object');
        $this->assertEquals('+121255501**', $number->e164, 'e164 is incorrect');
        $this->assertEquals('21255501**', $number->tendigit, 'The tendigit value is incorrect');
        $this->assertEquals('121255501**', $number->elevendigit, 'The elevendigit value is incorrect');
        $this->assertEquals('tel:+121255501**', $number->uri, 'The uri value is incorrect');
        $this->assertEquals('212.555.01**', $number->dotFormat, 'The dotFormat is incorrect');
        $this->assertEquals('212-555-01**', $number->hyphenFormat, 'The hyphenFormat is incorrect');
        $this->assertEquals('(212) 555-01**', $number->nationalFormat, 'The nationalFormat value is incorrect');
        $this->assertEquals('1 (212) 555-01**', $number->nationalFormatPlusOne, 'The nationalFormatPlusOne value is incorrect');
        $this->assertEquals('+1 212 555 01**', $number->internationalFormat, 'The internationalFormat value is incorrect');
        $this->assertEquals('212', $number->npa, 'The npa value is incorrect');
        $this->assertEquals('555', $number->nxx, 'The nxx value is incorrect');
        $this->assertEquals('01**', $number->line, 'The line value is incorrect');
    }
}
