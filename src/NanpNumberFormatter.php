<?php

namespace Erichowey\NanpNumberFormatter;

use function PHPUnit\Framework\isNull;

class NanpNumberFormatter
{
    public $errorMessage;
    public $isValid;
    public $e164;
    public $npa;
    public $nxx;
    public $line;
    public $nationalFormat;
    public $internationalFormat;
    public $tendigit;
    public $elevendigit;
    public $uri;


    function __construct()
    {
        $this->errorMessage = "";
        $this->isValid = false;
        $this->e164 = "Invalid";
        $this->npa = "Invalid";
        $this->nxx = "Invalid";
        $this->line = "Invalid";
        $this->nationalFormat = "Invalid";
        $this->internationalFormat = "Invalid";
        $this->tendigit = "Invalid";
        $this->elevendigit = "Invalid";
        $this->uri = "Invalid";
    }

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
   * This takes a number of different nanp number inputs and returns
   * a variety of formatted nanp numbers.
   *
   * @param mixed $number The phone number to be formatted
   * @param bool $wildcards
   * @return $this
   */
    public function parse($number, bool $wildcards = false)
    {
        // If $number is empty then return invalid
        if (empty($number)) {
            $this->isValid = false;
            $this->errorMessage = "The number parameter is required";
            return $this;
        }

        // If the string is less then 10 characters
        if (strlen($number) < 10) {
            $this->isValid = false;
            $this->errorMessage = $number . " is less than 10 characters";
            return $this;
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

        if (!preg_match($characterPattern, $number)) {
            $this->isValid = false;
            $this->errorMessage = $number . " contains invalid characters";
            return $this;
        }

        // Remove (, ), -, ., +, and spaces
        $number = str_replace(" ", "", $number);
        $number = str_replace("(", "", $number);
        $number = str_replace(")", "", $number);
        $number = str_replace("-", "", $number);
        $number = str_replace(".", "", $number);

        // If the number starts with '+', then '1' must follow
        if (substr($number, 0, 1) === "+" && substr($number, 0, 2) !== '+1') {
            $this->isValid = false;
            $this->errorMessage = 'Only "+1" phone numbers are allowed: '. $number;
            return $this;
        }

        // If the first number starts with '1', then append a '+'
        if (substr($number, 0, 1) === "1") {
            $number = "+" . $number;
        }

      if ($wildcards === true) {
        // If the first number starts with '2-9' or '*', then append '+1'
        $firstNumberPattern = '/^[2-9*]/';
      } else {
        // If the first number starts with '2-9', then append '+1'
        $firstNumberPattern = '/^[2-9]/';
      }

        if (preg_match($firstNumberPattern, $number)) {
            $number = "+1" . $number;
        }

        // The number should now be in the e164 format which is 12 characters
        if (strlen($number) !== 12) {
            $this->isValid = false;
            $this->errorMessage = 'The number should be a valid 10,11 or 12 digit e164 NANP number: ' . $number;
            return $this;
        }

      if ($wildcards === true) {
        // The number should be in the +1NXXNXXXXXX pattern. N is 2-9. '*' is also allowed for wildcards
        $numberPattern = '/^\+1[2-9*][0-9*]{2}[2-9*][0-9*]{6}$/';
      } else {
        // The number should be in the +1NXXNXXXXXX pattern. N is 2-9
        $numberPattern = '/^\+1[2-9][0-9]{2}[2-9][0-9]{6}$/';
      }

        if (!preg_match($numberPattern, $number)) {
            $this->isValid = false;
            $this->errorMessage = 'The number needs to match the +1NXXNXXXXXX pattern: ' . $number;
            return $this;
        }

        $this->isValid = true;
        $this->e164 = $number;
        $this->npa = substr($number, 2, 3);
        $this->nxx = substr($number, 5, 3);
        $this->line = substr($number, 8, 4);
        $this->nationalFormat = "(" . $this->npa . ") " . $this->nxx . "-" . $this->line;
        $this->internationalFormat = "+1" . " " . $this->npa . " " . $this->nxx . " " . $this->line;
        $this->tendigit = $this->npa . $this->nxx . $this->line;
        $this->elevendigit = "1" . $this->npa . $this->nxx . $this->line;
        $this->uri = "tel:" . $this->e164;

        return $this;
    }

  /**
   * This takes a number of different nanp number inputs and returns
   * a variety of formatted nanp numbers.
   *
   * @param mixed $number The phone number to be formatted
   * @param bool $wildcards
   * @return static
   */
    public static function format($number, bool $wildcards = false)
    {
        $self = new static;
        $self->parse($number, $wildcards);
        return $self;
    }
}