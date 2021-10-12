<?php

namespace Erichowey\NanpNumberFormatter;

class NanpNumberFormatter
{
    public $e164;
    public $npa;
    public $nxx;
    public $line;
    public $dotFormat;
    public $hyphenFormat;
    public $nationalFormat;
    public $nationalFormatPlusOne;
    public $internationalFormat;
    public $tendigit;
    public $elevendigit;
    public $uri;

    /**
     * Remaps all letters to the corresponding telephone keypad numbers
     *
     * @param string $number
     * @return string
     */
    private function lettersToNumbers(string $number)
    {
        $number = strtolower($number);
        $from = 'abcdefghijklmnopqrstuvwxyz';
        $to = '22233344455566677778889999';
        return strtr($number, $from, $to);
    }

    /**
     * @param $number
     * @param  bool  $wildcards
     * @return $this
     * @throws NanpNumberFormatterException
     */
    public function parse($number, bool $wildcards = false)
    {
        // If $number is empty then return invalid
        if (empty($number)) {
            throw new NanpNumberFormatterException('The number parameter is required');
        }

        // If the string is less then 10 characters
        if (strlen($number) < 10) {
            throw new NanpNumberFormatterException($number.' is less than 10 characters');
        }

        // Convert all letters to numbers
        $number = $this->lettersToNumbers($number);

        if ($wildcards === true) {
          // If the string contains any other characters except 0-9, (, ), -, ., +, * or space
          $characterPattern = '/^[- 0-9().+*]*$/';
        } else {
          // If the string contains any other characters except 0-9, (, ), -, ., +, or space
          $characterPattern = '/^[- 0-9().+]*$/';
        }

        if (! preg_match($characterPattern, $number)) {
            throw new NanpNumberFormatterException($number.' contains invalid characters');
        }

        // Remove (, ), -, ., +, and spaces
        $number = str_replace(' ', '', $number);
        $number = str_replace('(', '', $number);
        $number = str_replace(')', '', $number);
        $number = str_replace('-', '', $number);
        $number = str_replace('.', '', $number);

        // If the number starts with '+', then '1' must follow
        if (substr($number, 0, 1) === '+' && substr($number, 0, 2) !== '+1') {
            throw new NanpNumberFormatterException('Only "+1" phone numbers are allowed: '.$number);
        }

        // If the first number starts with '1', then append a '+'
        if (substr($number, 0, 1) === '1') {
            $number = '+'.$number;
        }

      if ($wildcards === true) {
        // If the first number starts with '2-9' or '*', then append '+1'
        $firstNumberPattern = '/^[2-9*]/';
      } else {
        // If the first number starts with '2-9', then append '+1'
        $firstNumberPattern = '/^[2-9]/';
      }

        if (preg_match($firstNumberPattern, $number)) {
            $number = '+1'.$number;
        }

        // The number should now be in the e164 format which is 12 characters
        if (strlen($number) !== 12) {
            throw new NanpNumberFormatterException('The number should be a valid 10,11 or 12 digit e164 NANP number: '.$number);
        }

      if ($wildcards === true) {
        // The number should be in the +1NXXNXXXXXX pattern. N is 2-9. '*' is also allowed for wildcards
        $numberPattern = '/^\+1[2-9*][0-9*]{2}[2-9*][0-9*]{6}$/';
      } else {
        // The number should be in the +1NXXNXXXXXX pattern. N is 2-9
        $numberPattern = '/^\+1[2-9][0-9]{2}[2-9][0-9]{6}$/';
      }

        if (! preg_match($numberPattern, $number)) {
            throw new NanpNumberFormatterException('The number needs to match the +1NXXNXXXXXX pattern: '.$number);
        }

        $this->e164 = $number;
        $this->npa = substr($number, 2, 3);
        $this->nxx = substr($number, 5, 3);
        $this->line = substr($number, 8, 4);
        $this->dotFormat = $this->npa.'.'.$this->nxx.'.'.$this->line;
        $this->hyphenFormat = $this->npa.'-'.$this->nxx.'-'.$this->line;
        $this->nationalFormat = '('.$this->npa.') '.$this->nxx.'-'.$this->line;
        $this->nationalFormatPlusOne = '1 '.$this->nationalFormat;
        $this->internationalFormat = '+1'.' '.$this->npa.' '.$this->nxx.' '.$this->line;
        $this->tendigit = $this->npa.$this->nxx.$this->line;
        $this->elevendigit = '1'.$this->npa.$this->nxx.$this->line;
        $this->uri = 'tel:'.$this->e164;

        return $this;
    }

    /**
     * @param $number
     * @param  bool  $wildcards
     * @return static
     * @throws NanpNumberFormatterException
     */
    public static function format($number, bool $wildcards = false)
    {
        $self = new static;
        $self->parse($number, $wildcards);
        return $self;
    }
}