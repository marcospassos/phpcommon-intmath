<?php

/**
 * This file is part of the phpcommon/intmath package.
 *
 * (c) Marcos Passos <marcos@marcospassos.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace PhpCommon\IntMath\Tests;

use PhpCommon\IntMath\IntMath;
use PHPUnit_Framework_TestCase;

/**
 * @since  1.0
 *
 * @author Marcos Passos <marcos@marcospassos.com>
 */
class IntMathTest extends PHPUnit_Framework_TestCase
{
    public function getNonIntegerValues()
    {
        return array(
            array(1.0),
            array(INF),
            array(-INF),
            array(NAN),
            array(null),
            array(true),
            array(false),
            array('a'),
            array(new \stdClass()),
            array(curl_init()),
            array(array())
        );
    }

    /**
     * @param mixed $value
     *
     * @dataProvider getNonIntegerValues
     *
     * @expectedException \InvalidArgumentException
     *
     * @testdox The negate() method throws an exception if the argument is not an integer
     */
    public function testNegateThrowsAnExceptionIfTheArgumentIsNotAnInteger($value)
    {
        IntMath::negate($value);
    }

    /**
     * @covers PhpCommon\IntMath\IntMath::negate
     *
     * @testdox The negate() method returns the given value with the opposite sign
     */
    public function testNegateReturnsTheArgumentWithOppositeSign()
    {
        $this->assertSame(0, IntMath::negate(0));
        $this->assertSame(0, IntMath::negate(-0));
        $this->assertSame(-100, IntMath::negate(100));
        $this->assertSame(100, IntMath::negate(-100));
        $this->assertSame(-IntMath::MAX_INT, IntMath::negate(IntMath::MAX_INT));
        $this->assertSame(IntMath::MAX_INT, IntMath::negate(-IntMath::MAX_INT));
    }

    /**
     * @covers PhpCommon\IntMath\IntMath::negate
     *
     * @testdox The negate() method does not negate the smallest negative integer
     */
    public function testNegateDoesNotNegateTheSmallestNegativeInteger()
    {
        $this->assertSame(IntMath::MIN_INT, IntMath::negate(IntMath::MIN_INT));
    }

    /**
     * @covers PhpCommon\IntMath\IntMath::add
     *
     * * @testdox The add() method returns the sum of the arguments
     */
    public function testAddReturnsTheSumOfTheArguments()
    {
        $this->assertSame(0, IntMath::add(0, 0));
        $this->assertSame(1, IntMath::add(0, 1));
        $this->assertSame(1, IntMath::add(1, 0));
        $this->assertSame(0, IntMath::add(100, -100));
        $this->assertSame(-2, IntMath::add(-1, -1));
        $this->assertSame(4, IntMath::add(2, 2));
    }

    /**
     * @param mixed $value
     *
     * @dataProvider getNonIntegerValues
     *
     * @expectedException \InvalidArgumentException
     *
     * @testdox The add() method throws an exception if one of the arguments is not an integer
     */
    public function testAddThrowsAnExceptionIfOneOfTheArgumentIsNotAnInteger($value)
    {
        IntMath::add($value, $value);
    }

    /**
     * @covers PhpCommon\IntMath\IntMath::add
     *
     * @testdox The add() method wraps around the result on overflow
     */
    public function testAddWrapsAroundOnOverflow()
    {
        $this->assertSame(IntMath::MIN_INT, IntMath::add(IntMath::MAX_INT, 1));
        $this->assertSame(IntMath::MAX_INT, IntMath::add(IntMath::MIN_INT, -1));
    }

    /**
     * @param mixed $value
     *
     * @dataProvider getNonIntegerValues
     *
     * @expectedException \InvalidArgumentException
     *
     * @testdox The subtract() method throws an exception if one of the arguments is not an integer
     */
    public function testSubtractThrowsAnExceptionIfOneOfTheArgumentIsNotAnInteger($value)
    {
        IntMath::subtract($value, $value);
    }

    /**
     * @covers PhpCommon\IntMath\IntMath::subtract
     *
     * @testdox The subtract() method returns the difference of the arguments
     */
    public function testSubtractReturnsTheDifferenceOfTheArguments()
    {
        $this->assertSame(0, IntMath::subtract(0, 0));
        $this->assertSame(-1, IntMath::subtract(0, 1));
        $this->assertSame(1, IntMath::subtract(1, 0));
        $this->assertSame(200, IntMath::subtract(100, -100));
        $this->assertSame(0, IntMath::subtract(-1, -1));
    }

    /**
     * @covers PhpCommon\IntMath\IntMath::subtract
     *
     * @testdox The subtract() method wraps around the result on overflow
     */
    public function testSubtractWrapsAroundOnOverflow()
    {
        $this->assertSame(IntMath::MAX_INT, IntMath::subtract(IntMath::MIN_INT, 1));
        $this->assertSame(IntMath::MIN_INT, IntMath::subtract(IntMath::MAX_INT, -1));
        $this->assertSame(-1, IntMath::subtract(IntMath::MAX_INT, IntMath::MIN_INT));
    }

    /**
     * @param mixed $value
     *
     * @dataProvider getNonIntegerValues
     *
     * @expectedException \InvalidArgumentException
     *
     * @testdox The multiply() method throws an exception if one of the arguments is not an integer
     */
    public function testMultiplyThrowsAnExceptionIfOneOfTheArgumentIsNotAnInteger($value)
    {
        IntMath::multiply($value, $value);
    }

    /**
     * @covers PhpCommon\IntMath\IntMath::multiply
     *
     * @testdox The multiply() method returns the product of the arguments
     */
    public function testMultiplyReturnsTheProductOfTheArguments()
    {
        $this->assertSame(0, IntMath::multiply(1, 0));
        $this->assertSame(0, IntMath::multiply(0, 1));
        $this->assertSame(-1, IntMath::multiply(1, -1));
        $this->assertSame(-1, IntMath::multiply(-1, 1));
        $this->assertSame(1, IntMath::multiply(1, 1));
        $this->assertSame(-150, IntMath::multiply(-10, 15));
        $this->assertSame(-150, IntMath::multiply(10, -15));
        $this->assertSame(IntMath::MAX_INT, IntMath::multiply(1, IntMath::MAX_INT));
        $this->assertSame(-IntMath::MAX_INT, IntMath::multiply(-1, IntMath::MAX_INT));
        $this->assertSame(-IntMath::MAX_INT, IntMath::multiply(1, -IntMath::MAX_INT));
        $this->assertSame(0, IntMath::multiply(0, IntMath::MAX_INT));
        $this->assertSame(0, IntMath::multiply(0, -IntMath::MAX_INT));
        $this->assertSame(IntMath::MIN_INT, IntMath::multiply(1, IntMath::MIN_INT));
        $this->assertSame(0, IntMath::multiply(0, IntMath::MIN_INT));
    }

    /**
     * @covers PhpCommon\IntMath\IntMath::multiply
     *
     * @testdox The multiply() method wraps around the result on overflow
     */
    public function testMultiplyWrapsAroundOnOverflow()
    {
        $this->assertSame(-2, IntMath::multiply(IntMath::MAX_INT, 2));
        $this->assertSame(-2, IntMath::multiply(2, IntMath::MAX_INT));
        $this->assertSame(2, IntMath::multiply(2, -IntMath::MAX_INT));
        $this->assertSame(2, IntMath::multiply(-2, IntMath::MAX_INT));
        $this->assertSame(IntMath::MAX_INT + -2, IntMath::multiply(IntMath::MAX_INT, 3));
        $this->assertSame(IntMath::MAX_INT + -2, IntMath::multiply(3, IntMath::MAX_INT));
        $this->assertSame(1, IntMath::multiply(IntMath::MAX_INT, IntMath::MAX_INT));
        $this->assertSame(IntMath::MIN_INT, IntMath::multiply(IntMath::MIN_INT, 3));
        $this->assertSame(IntMath::MIN_INT, IntMath::multiply(3, IntMath::MIN_INT));
        $this->assertSame(IntMath::MIN_INT, IntMath::multiply(IntMath::MIN_INT, -3));
        $this->assertSame(IntMath::MIN_INT, IntMath::multiply(-3, IntMath::MIN_INT));
    }

    /**
     * @param mixed $value
     *
     * @dataProvider getNonIntegerValues
     *
     * @expectedException \InvalidArgumentException
     *
     * @testdox The divide() method throws an exception if one of the arguments is not an integer
     */
    public function testDivideThrowsAnExceptionIfOneOfTheArgumentIsNotAnInteger($value)
    {
        IntMath::divide($value, $value);
    }

    /**
     * @covers PhpCommon\IntMath\IntMath::divide
     *
     * @testdox The divide() method returns the quotient of dividing one operand from another
     */
    public function testDivideReturnsTheQuotientOfTheArguments()
    {
        $this->assertSame(1, IntMath::divide(1, 1));
        $this->assertSame(0, IntMath::divide(0, 1));
        $this->assertSame(-1, IntMath::divide(1, -1));
        $this->assertSame(-1, IntMath::divide(-1, 1));
        $this->assertSame(1, IntMath::divide(10, 10));
        $this->assertSame(-2, IntMath::divide(-20, 10));
        $this->assertSame(-2, IntMath::divide(20, -10));
        $this->assertSame(1, IntMath::divide(IntMath::MAX_INT, IntMath::MAX_INT));
        $this->assertSame(1, IntMath::divide(IntMath::MIN_INT, IntMath::MIN_INT));
    }

    /**
     * @covers PhpCommon\IntMath\IntMath::divide
     *
     * @testdox The divide() method rounds the result towards zero
     */
    public function testDivideRoundsTheResultTowardsZero()
    {
        $this->assertSame(2, IntMath::divide(5, 2));
        $this->assertSame(-2, IntMath::divide(-5, 2));
        $this->assertSame(0, IntMath::divide(1, 2));
        $this->assertSame(0, IntMath::divide(-1, 2));
        $this->assertSame(0, IntMath::divide(1, IntMath::MIN_INT));
        $this->assertSame(IntMath::MIN_INT, IntMath::divide(IntMath::MIN_INT, 1));
        $this->assertSame(0, IntMath::divide(1, IntMath::MAX_INT));
        $this->assertSame(IntMath::MAX_INT, IntMath::divide(IntMath::MAX_INT, 1));
    }

    /**
     * @expectedException \PhpCommon\IntMath\DivisionByZeroException
     *
     * @testdox The divide() method throws an exception when a division by zero occurs
     */
    public function testDivideThrowsExceptionOnDivisionByZero()
    {
        IntMath::divide(1, 0);
    }

    /**
     * @covers PhpCommon\IntMath\IntMath::divide
     *
     * @testdox The divide() method returns the negative largest integer on overflow
     */
    public function testDivideReturnsTheNegativeLargestIntegerOnOverflow()
    {
        $this->assertSame(IntMath::MIN_INT, IntMath::divide(IntMath::MIN_INT, -1));
    }
}
