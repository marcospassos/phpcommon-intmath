# PHPCommon IntMath

[![Build Status](https://travis-ci.org/marcospassos/phpcommon-intmath.svg?branch=master)](https://travis-ci.org/marcospassos/phpcommon-intmath)
[![Code Coverage](https://scrutinizer-ci.com/g/marcospassos/phpcommon-intmath/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/marcospassos/phpcommon-intmath/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/marcospassos/phpcommon-intmath/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/marcospassos/phpcommon-intmath/?branch=master)
[![StyleCI](https://styleci.io/repos/60445417/shield)](https://styleci.io/repos/60445417)
[![Latest Stable Version](https://poser.pugx.org/phpcommon/intmath/v/stable)](https://packagist.org/packages/phpcommon/intmath)
[![Dependency Status](https://www.versioneye.com/user/projects/5753c83a7757a0003bd4af4a/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5753c83a7757a0003bd4af4a)

Latest release: [1.0.0-beta](https://packagist.org/packages/phpcommon/intmath#1.0.0)

PHP 5.3+ library for arithmetic operations on integers that wraps around upon
overflow.

> **Attention**
> 
> This library is not intended for use as a way to properly perform arithmetic
> operations on integers and should not be used in place of the native
> arithmetic operators or any other library designed for such purpose.

Unlike other languages that overflow large positive integers into large
negative integers, PHP actually overflows integers to floating-point
numbers. In most cases, arithmetic overflows must be treated as an unusual
circumstance which requires special handling. However, there are some cases
where such _wrap-around_ behavior is actually useful - for example with TCP
sequence numbers or certain algorithms, such as hash calculation. This
utility class provides basic arithmetic functions that operate in accordance
to that behaviour. 

To illustrate, consider the following example:

```php
// Output on 64-bit system: float(9.2233720368548E+18)
var_dump(PHP_MAX_INT + 1);

// Output on 64-bit system: int(-9223372036854775808)
var_dump(IntMath::add(PHP_MAX_INT, 1));
```
As previously shown, adding one to the largest integer supported using
native arithmetic operators will result in a floating-point number. By
contrast, using [IntMath::add()](#addition) will cause an overflow, resulting
in the smallest integer supported in this build of PHP.

The API is extensively documented in the source code. In addition, an
[HTML version][link-api-doc] is also available for more convenient viewing in
browser.

## Installation

Use [Composer][link-composer] to install the package:

```sh
$ composer require phpcommon/intmath
```

## Operations

Currently, only the four basic arithmetic operations (addition, subtraction,
multiplication and division) and negation are supported.

### Negation

For integer values, negation is the same as subtraction from zero. Because PHP
uses two's-complement representation for integers and the range of
two's-complement values is not symmetric, the negation of the maximum negative
integer results in that same maximum negative number. Despite the fact that
overflow has occurred, no exception is thrown.
    
For all integer values `$a`, `-$a` equals `(~$a) + 1`.

API example usage:
 ```php
 // Outputs int(-100)
 var_dump(IntMath::negate(100));
 ```

### Addition

The result of adding two integers is the low-order bits of the true
mathematical result as represented in a sufficiently wide two's-complement
format. If overflow occurs, then the sign of the result may not be the same as
the sign of the mathematical sum of the two values. Despite the overflow, no
exception is thrown in this case.

API example usage:
 ```php
 // Outputs int(300)
 var_dump(IntMath::add(100, 200));
 ```

### Subtraction

The subtraction of a positive number yields the same result as the addition of
a negative number of equal magnitude. Furthermore, the subtraction from zero is
the same as negation. The result is the low-order bits of the true mathematical
result as represented in a sufficiently wide two's-complement format. If
overflow occurs, then the sign of the result may not be the same as the sign of
the mathematical difference of the two values. Despite the overflow, no
exception is thrown in this case.

API example usage:
 ```php
 // Outputs int(90)
 IntMath::subtract(100, 10);
 ```

### Multiplication

The result of multiplying two integers is the low-order bits of the true
mathematical result as represented in a sufficiently wide two's-complement
format. If overflow occurs, then the sign of the result may not be the same as
the sign of the mathematical product of the two values. Despite the overflow,
no exception is thrown in this case.

API example usage:
 ```php
 // Outputs int(200)
 IntMath::multiply(100, 2);
 ```

### Division

The division rounds the result towards zero. Thus the absolute value of the 
result is the largest possible integer that is less than or equal to the
absolute value of the quotient of the two operands. The result is zero or
positive when the two operands have the same sign and zero or negative when the
two operands have opposite signs.

There is one special case that does not satisfy this rule: if the dividend is
the negative integer of largest possible magnitude for its type, and the
divisor is `-1`, then integer overflow occurs and the result is equal to the
dividend. Despite the overflow, no exception is thrown in this case. On the
other hand if the value of the divisor in an integer division is `0`, then a
`DivisionByZeroException` is thrown.

API example usage:
 ```php
 // Outputs int(50)
 IntMath::divide(100, 2);
 ```
 
## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.


## Testing

```sh
$ composer test
```

Check out the [Test Documentation][link-testsdoc] for more details.

## Contributing

Contributions to the package are always welcome!

* Report any bugs or issues you find on the [issue tracker][link-issue-tracker].
* You can grab the source code at the package's
[Git repository][link-repository].

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for
details.

## Security

If you discover any security related issues, please email
marcos@marcospassos.com instead of using the issue tracker.

## Credits

* [Marcos Passos][link-author]
- [All Contributors][link-contributors]

## License

All contents of this package are licensed under the [MIT license](LICENSE).

[link-api-doc]: http://marcospassos.github.io/phpcommon-intmath/docs/api
[link-testsdoc]: http://marcospassos.github.io/phpcommon-intmath/docs/test
[link-composer]: https://getcomposer.org
[link-author]: http://github.com/marcospassos
[link-contributors]: https://github.com/marcospassos/phpcommon-intmath/graphs/contributors
[link-issue-tracker]: https://github.com/marcospassos/phpcommon-intmath/issues
[link-repository]: https://github.com/marcospassos/phpcommon-intmath
