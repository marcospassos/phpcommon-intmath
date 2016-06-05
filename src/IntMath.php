<?php

/**
 * This file is part of the phpcommon/intmath package.
 *
 * (c) Marcos Passos <marcos@marcospassos.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace PhpCommon\IntMath;

use InvalidArgumentException;

// Only available on PHP >= 7
if (!defined('PHP_INT_MIN')) {
    define('PHP_INT_MIN', -PHP_INT_MAX - 1);
}

/**
 * Utility class for arithmetic operations on integers that wraps around upon
 * overflow.
 *
 * **Important**
 *
 * This class is not intended for use as a way to properly perform arithmetic
 * operations on integers and should not be used in place of the native
 * arithmetic operators or any other library designed for such purpose.
 *
 * Unlike other languages that overflow large positive integers into large
 * negative integers, PHP actually overflows integers to floating-point
 * numbers. In most cases, arithmetic overflows must be treated as an unusual
 * circumstance which requires special handling. However, there are some cases
 * where such _wrap-around_ behavior is actually useful - for example with TCP
 * sequence numbers or certain algorithms, such as hash calculation. This
 * utility class provides basic arithmetic functions that operate in accordance
 * to that behaviour.
 *
 * To illustrate, consider the following example:
 *
 * ```php
 * // Output on 64-bit system: float(9.2233720368548E+18)
 * var_dump(PHP_MAX_INT + 1);
 *
 * // Output on 64-bit system: int(-9223372036854775808)
 * var_dump(IntMath::add(PHP_MAX_INT, 1));
 * ```
 *
 * As previously shown, adding one to the largest supported integer using
 * native arithmetic operators will result in a floating-point number. By
 * contrast, using {@link IntMath::add()} will cause an overflow, resulting
 * in the smallest integer supported in this build of PHP.
 *
 * @author Marcos Passos <marcos@marcospassos.com>
 */
class IntMath
{
    /**
     * The largest supported integer
     */
    const MAX_INT = PHP_INT_MAX;

    /**
     * The smallest supported integer
     */
    const MIN_INT = PHP_INT_MIN;

    /**
     * Returns the negation of the argument.
     *
     * For integer values, negation is the same as subtraction from zero.
     * Because PHP uses two's-complement representation for integers and the
     * range of two's-complement values is not symmetric, the negation of the
     * maximum negative integer results in that same maximum negative number.
     * Despite the fact that overflow has occurred, no exception is thrown.
     *
     * For all integer values `$a`, `-$a` equals `(~$a) + 1`.
     *
     * @param integer $a The value.
     *
     * @return integer The result.
     *
     * @throws InvalidArgumentException If the argument is not an integer.
     */
    public static function negate($a)
    {
        self::assertInteger($a);

        if ($a === self::MIN_INT) {
            return $a;
        }

        return -$a;
    }

    /**
     * Returns the sum of the arguments.
     *
     * The result is the low-order bits of the true mathematical result as
     * represented in a sufficiently wide two's-complement format. If overflow
     * occurs, then the sign of the result may not be the same as the sign of
     * the mathematical sum of the two values. Despite the overflow, no
     * exception is thrown in this case.
     *
     * @param integer $a The addend.
     * @param integer $b The addend.
     *
     * @return integer The sum.
     *
     * @throws InvalidArgumentException If one of the arguments is not an
     *                                  integer.
     */
    public static function add($a, $b)
    {
        self::assertInteger($a);
        self::assertInteger($b);

        if (($b > 0) && ($a <= (PHP_INT_MAX - $b))) {
            return $a + $b;
        }

        if (($b < 0) && ($a >= (PHP_INT_MIN - $b))) {
            return $a + $b;
        }

        while ($b !== 0) {
            // Carry now contains common set bits of the addends
            $carry = $a & $b;
            // Sum of bits of $x and $y,
            // where at least one of the bits is not set
            $a ^= $b;
            // Left-shift by one
            $b = $carry << 1;
        }

        return $a;
    }

    /**
     * Returns the difference of the arguments.
     *
     * The subtraction of a positive number yields the same result as the
     * addition of a negative number of equal magnitude. Furthermore, the
     * subtraction from zero is the same as negation.
     *
     * The result is the low-order bits of the true mathematical result as
     * represented in a sufficiently wide two's-complement format. If overflow
     * occurs, then the sign of the result may not be the same as the sign of
     * the mathematical difference of the two values. Despite the overflow, no
     * exception is thrown in this case.
     *
     * @param integer $a The minuend.
     * @param integer $b The subtrahend.
     *
     * @return integer The difference.
     *
     * @throws InvalidArgumentException If one of the arguments is not an
     *                                  integer.
     */
    public static function subtract($a, $b)
    {
        self::assertInteger($a);
        self::assertInteger($b);

        return self::add((int) $a, self::negate($b));
    }

    /**
     * Returns the product of the arguments.
     *
     * The result is the low-order bits of the true mathematical result as
     * represented in a sufficiently wide two's-complement format. If overflow
     * occurs, then the sign of the result may not be the same as the sign of
     * the mathematical product of the two values. Despite the overflow, no
     * exception is thrown in this case.
     *
     * @param integer $a The multiplicand.
     * @param integer $b The multiplier.
     *
     * @return integer The product.
     *
     * @throws InvalidArgumentException If one of the arguments is not an
     *                                  integer.
     */
    public static function multiply($a, $b)
    {
        self::assertInteger($a);
        self::assertInteger($b);

        if ($a === 0 || $b === 0) {
            return 0;
        }

        // If the multiplicand or the multiplier are the smallest integer
        // supported, then the product is `0` or the smallest integer supported,
        // according as the other operand is odd or even respectively.
        if ($a === self::MIN_INT) {
            return $b & 0x01 ? $a : 0;
        }

        if ($b === self::MIN_INT) {
            return $a & 0x01 ? $b : 0;
        }

        $max = self::MIN_INT;

        // Same sign
        if ($a >= 0 && $b >= 0 || $a < 0 && $b < 0) {
            $max = self::MAX_INT;
        }

        // Use native operators unless the operation causes an overflow
        if (($b > 0 && $b <= ($max / $a)) || ($b < 0 && $b >= ($max / $a))) {
            return $a * $b;
        }

        // Signed multiplication can be accomplished by doing an unsigned
        // multiplication and taking manually care of the negative-signs.
        $sign = false;

        if ($a < 0) {
            // Toggle the signed flag
            $sign = !$sign;
            // Negate $a
            $a = self::negate($a);
        }

        if ($b < 0) {
            // Toggle the signed flag
            $sign = !$sign;
            // Negate $b
            $b = self::negate($b);
        }

        $product = 0;
        // Both operands are now positive, perform unsigned multiplication
        while ($a !== 0) {
            // Test the least significant bit (LSB) of multiplier
            if (($a & 0x01) !== 0) {
                // If 1, add the multiplicand to the product
                $product = self::add($product, $b);
            }

            // Left-shift by one, or divide by 2
            $a >>= 1;

            // Right-shift by one, or multiply by 2
            $b <<= 1;
        }

        if ($sign) {
            // Negate the product
            $product = self::negate($product);
        }

        return $product;
    }

    /**
     * Returns the quotient of the arguments.
     *
     * The division rounds the result towards zero. Thus the absolute value of
     * the result is the largest possible integer that is less than or equal to
     * the absolute value of the quotient of the two operands. The result is
     * zero or positive when the two operands have the same sign and zero or
     * negative when the two operands have opposite signs.
     *
     * There is one special case that does not satisfy this rule: if the
     * dividend is the negative integer of largest possible magnitude for its
     * type, and the divisor is `-1`, then integer overflow occurs and the
     * result is equal to the dividend. Despite the overflow, no exception is
     * thrown in this case. On the other hand if the value of the divisor in an
     * integer division is `0`, then a `DivisionByZeroException` is thrown.
     *
     * @param integer $a The dividend.
     * @param integer $b The divisor.
     *
     * @return integer The quotient.
     *
     * @throws InvalidArgumentException If one of the arguments is not an
     *                                  integer.
     * @throws DivisionByZeroException  If the divisor is zero.
     */
    public static function divide($a, $b)
    {
        self::assertInteger($a);
        self::assertInteger($b);

        if (0 === $b) {
            throw new DivisionByZeroException('Division by zero.');
        }

        if ($a === self::MIN_INT && $b === -1) {
            return $a;
        }

        return ($a - ($a % $b)) / $b;
    }

    /**
     * Asserts the specified value is an integer.
     *
     * @param mixed $value The value to assert.
     *
     * @throws InvalidArgumentException If the value is not an integer.
     */
    private static function assertInteger($value)
    {
        if (is_int($value)) {
            return;
        }

        switch (true) {
            case is_float($value) && is_infinite($value):
                $type = $value < 0 ? '-INF' : 'INF';
                break;

            case is_float($value) && is_nan($value):
                $type = 'NAN';
                break;

            default:
                $type = gettype($value);
        }

        throw new InvalidArgumentException(sprintf(
            'Expected an integer, but got "%s"',
            $type
        ));
    }
}
