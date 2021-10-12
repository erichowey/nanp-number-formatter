# Format a NANP Phone Number

This utility takes a NANP (North American Numbering Plan) phone number, parses it and returns the number in a variety of formats. 

## Install
You can install this package via composer

    composer require erichowey/nanp-number-formatter

## Supported Input Formats and Characters
- 10 Digit `2125550123`
- 11 Digit `12125550123`
- E.164 `+12125550123`
- Parenthesis `(212) 555-0123`
- Dot `212.555.0123`
- Hyphen `212-555-0123`
- Space `212 555 0123`
- Letters `1800FLOWERS`

etc...

## Outputted Formats
- E.164 `+12125550123`
- Dot Format `212.555.0123`
- Hyphen Format `212-555-0123`
- National Format `(212) 555-0123`
- National Format Plus One `1 (212) 555-0123`
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
echo $number->nationalFormatPlusOne; // 1 (212) 555-0123
echo $number->internationalFormat; // +1 212 555 0123
echo $number->dotFormat; // 212.555.0123
echo $number->hyphenFormat; // 212-555-0123
echo $number->tendigit; // 2125550123
echo $number->elevendigit; // 12125550123
echo $number->uri; // tel:+12125550123
echo $number->npa; // 212
echo $number->nxx; // 555
echo $number->line; // 0123
...
```

## Wildcards
Historically, NANP telephone number wildcards have been represented with the following characters: `X` for 0-9, `N` 
for 2-9 and occasionally `Y` for 1-9 in dialplan matching. Since this tool accepts letter input, these wildcard 
characters cannot be used. To utilize wildcard characters, set the second parameter of the `format()` method to `true`. 
Use `*` to represent a wildcard character.
```php
$number = NanpNumberFormatter::format("(212) 555-****", true);
```

## Errors
If an invalid or non nanp number is attempted to be formatted, a `NanpNumberFormatterException` will be thrown. They can
be handled with the typical try/catch pattern:
```php
try {
    $number = NanpNumberFormatter::format("1234");
} catch (NanpNumberFormatterException $e) {
    echo $e->getMessage(); // 1234 is less than 10 characters
}
```

## Contributing
Contributions are welcome. Criticism is even more welcome. You're welcome to submit a PR or open an issue. Please 
conform to PSR-12 standards and create tests for your PR.

## Testing
Run
```
./vendor/bin/phpunit
```
