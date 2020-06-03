# Format a NANP Phone Number

This utility take a NANP (North American Numbering Plan) phone number, parses it and returns the number in a variety of formats. 

## Install
You can install this package via composer

    composer require erichowey/nanp-number-formatter

## Supported Input Formats and Characters
- 10 Digit `2125550123`
- 11 Digit `12125550123`
- E.164 `+12125550123`
- Parenthesis `(212) 555-0123`
- Dot `212.555.0123`
- Space `212 555 0123`

etc...

## Outputted Formats
- E.164 `+12125550123`
- National Format `(212) 555-0123`
- International Format `+1 212 555 0123`
- 10 Digit `2125550123`
- 11 Digit `12325550123`
- URI `tel:+12125550123`
- NPA `212`
- NXX `555`
- Line `0123`

## Example Usage
```php
use Erichowey\NanpNumberFormatter\NanpNumberFormatter;
...
$number = NanpNumberFormatter::format("(212) 555-0123");

echo $number->e164; // +12125550123
echo $number->nationalFormat; // (212) 555-0123
echo $number->internationalFormat; // +1 212 555 0123
echo $number->tendigit; // 2125550123
echo $number->elevendigit; // 12125550123
echo $number->uri; // tel:+12125550123
echo $number->npa; // 212
echo $number->nxx; // 555
echo $number->line; // 0123
...
```