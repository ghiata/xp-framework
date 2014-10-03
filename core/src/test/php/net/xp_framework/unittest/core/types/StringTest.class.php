<?php namespace net\xp_framework\unittest\core\types;

use unittest\TestCase;
use lang\types\String;

/**
 * TestCase
 *
 * @see   xp://lang.types.String
 */
class StringTest extends TestCase {

  #[@test]
  public function stringIsEqualToItself() {
    $a= new String('');
    $this->assertTrue($a->equals($a));
  }

  #[@test]
  public function stringIsEqualSameString() {
    $this->assertTrue(create(new String('ABC'))->equals(new String('ABC')));
  }

  #[@test]
  public function stringIsNotEqualToDifferentString() {
    $this->assertFalse(create(new String('ABC'))->equals(new String('CBA')));
  }

  #[@test, @expect('lang.FormatException')]
  public function incompleteMultiByteCharacter() {
    new String('�', 'utf-8');
  }

  #[@test, @expect('lang.FormatException')]
  public function illegalCharacter() {
    new String('�', 'US-ASCII');
  }

  #[@test]
  public function usAsciiString() {
    $str= new String('Hello');
    $this->assertEquals(new \lang\types\Bytes('Hello'), $str->getBytes());
    $this->assertEquals(5, $str->length());
  }

  #[@test]
  public function integerString() {
    $str= new String(1);
    $this->assertEquals(new \lang\types\Bytes('1'), $str->getBytes());
    $this->assertEquals(1, $str->length());
  }

  #[@test]
  public function characterString() {
    $str= new String(new \lang\types\Character('�'));
    $this->assertEquals(new \lang\types\Bytes("\304"), $str->getBytes('iso-8859-1'));
    $this->assertEquals(1, $str->length());
  }

  #[@test]
  public function doubleString() {
    $str= new String(1.1);
    $this->assertEquals(new \lang\types\Bytes('1.1'), $str->getBytes());
    $this->assertEquals(3, $str->length());
  }

  #[@test]
  public function trueString() {
    $str= new String(TRUE);
    $this->assertEquals(new \lang\types\Bytes('1'), $str->getBytes());
    $this->assertEquals(1, $str->length());
  }

  #[@test]
  public function falseString() {
    $str= new String(FALSE);
    $this->assertEquals(new \lang\types\Bytes(''), $str->getBytes());
    $this->assertEquals(0, $str->length());
  }

  #[@test]
  public function nullString() {
    $str= new String(NULL);
    $this->assertEquals(new \lang\types\Bytes(''), $str->getBytes());
    $this->assertEquals(0, $str->length());
  }

  #[@test]
  public function umlautString() {
    $str= new String('H�llo');
    $this->assertEquals(new \lang\types\Bytes('Hällo'), $str->getBytes('utf-8'));
    $this->assertEquals(5, $str->length());
  }

  #[@test]
  public function utf8String() {
    $this->assertEquals(
      new String('Hällo', 'utf-8'),
      new String('H�llo', 'iso-8859-1')
    );
  }

  #[@test, @ignore('Does not work with all iconv implementations')]
  public function transliteration() {
    $this->assertEquals(
      'Trenciansky kraj', 
      create(new String('Trenčiansky kraj', 'utf-8'))->toString()
    );
  }

  #[@test]
  public function indexOf() {
    $str= new String('H�llo');
    $this->assertEquals(0, $str->indexOf('H'));
    $this->assertEquals(1, $str->indexOf('�'));
    $this->assertEquals(1, $str->indexOf(new String('�')));
    $this->assertEquals(-1, $str->indexOf(''));
    $this->assertEquals(-1, $str->indexOf('4'));
  }

  #[@test]
  public function lastIndexOf() {
    $str= new String('H�lloH');
    $this->assertEquals($str->length()- 1, $str->lastIndexOf('H'));
    $this->assertEquals(1, $str->lastIndexOf('�'));
    $this->assertEquals(1, $str->lastIndexOf(new String('�')));
    $this->assertEquals(-1, $str->lastIndexOf(''));
    $this->assertEquals(-1, $str->lastIndexOf('4'));
  }

  #[@test]
  public function contains() {
    $str= new String('H�llo');
    $this->assertTrue($str->contains('H'));
    $this->assertTrue($str->contains('�'));
    $this->assertTrue($str->contains('o'));
    $this->assertFalse($str->contains(''));
    $this->assertFalse($str->contains('4'));
  }

  #[@test]
  public function substring() {
    $str= new String('H�llo');
    $this->assertEquals(new String('�llo'), $str->substring(1));
    $this->assertEquals(new String('ll'), $str->substring(2, -1));
    $this->assertEquals(new String('o'), $str->substring(-1, 1));
  }

  #[@test]
  public function startsWith() {
    $str= new String('www.m�ller.com');
    $this->assertTrue($str->startsWith('www.'));
    $this->assertFalse($str->startsWith('ww.'));
    $this->assertFalse($str->startsWith('m�ller'));
  }

  #[@test]
  public function endsWith() {
    $str= new String('www.m�ller.com');
    $this->assertTrue($str->endsWith('.com'));
    $this->assertTrue($str->endsWith('�ller.com'));
    $this->assertFalse($str->endsWith('.co'));
    $this->assertFalse($str->endsWith('m�ller'));
  }

  #[@test]
  public function concat() {
    $this->assertEquals(new String('www.m�ller.com'), create(new String('www'))
      ->concat(new \lang\types\Character('.'))
      ->concat('m�ller')
      ->concat('.com')
    );
  }
  
  #[@test]
  public function hashesOfSameStringEqual() {
    $this->assertEquals(
      create(new String(''))->hashCode(),
      create(new String(''))->hashCode()
    );
  }

  #[@test]
  public function hashesOfDifferentStringsNotEqual() {
    $this->assertNotEquals(
      create(new String('A'))->hashCode(),
      create(new String('B'))->hashCode()
    );
  }
  
  #[@test]
  public function charAt() {
    $this->assertEquals(new \lang\types\Character('�'), create(new String('www.m�ller.com'))->charAt(5));
  }

  #[@test, @expect('lang.IndexOutOfBoundsException')]
  public function charAtNegative() {
    create(new String('ABC'))->charAt(-1);
  }

  #[@test, @expect('lang.IndexOutOfBoundsException')]
  public function charAtAfterEnd() {
    create(new String('ABC'))->charAt(4);
  }

  #[@test, @expect('lang.IndexOutOfBoundsException')]
  public function charAtEnd() {
    create(new String('ABC'))->charAt(3);
  }

  #[@test]
  public function replace() {
    $str= new String('www.m�ller.com');
    $this->assertEquals(new String('m�ller'), $str->replace('www.')->replace('.com'));
    $this->assertEquals(new String('muller'), $str->replace('�', 'u'));
  }

  #[@test]
  public function offsetSet() {
    $str= new String('www.m�ller.com');
    $str[5]= 'u';
    $this->assertEquals(new String('www.muller.com'), $str);
  }

  #[@test, @expect('lang.IndexOutOfBoundsException')]
  public function offsetSetNegative() {
    $str= new String('www.m�ller.com');
    $str[-1]= 'u';
  }

  #[@test, @expect('lang.IndexOutOfBoundsException')]
  public function offsetSetAfterEnd() {
    $str= new String('www.m�ller.com');
    $str[$str->length()]= 'u';
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function offsetSetIncorrectLength() {
    $str= new String('www.m�ller.com');
    $str[5]= 'ue';
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function offsetAdd() {
    $str= new String('www.m�ller.com');
    $str[]= '.';
  }

  #[@test]
  public function offsetGet() {
    $str= new String('www.m�ller.com');
    $this->assertEquals(new \lang\types\Character('�'), $str[5]);
  }

  #[@test]
  public function offsetExists() {
    $str= new String('www.m�ller.com');
    $this->assertTrue(isset($str[0]), 0);
    $this->assertTrue(isset($str[5]), 5);
    $this->assertFalse(isset($str[-1]), -1);
    $this->assertFalse(isset($str[1024]), 1024);
  }

  #[@test]
  public function offsetUnsetAtBeginning() {
    $str= new String('www.m�ller.com');
    unset($str[0]);
    $this->assertEquals(new String('ww.m�ller.com'), $str);
  }

  #[@test]
  public function offsetUnsetAtEnd() {
    $str= new String('www.m�ller.com');
    unset($str[$str->length()- 1]);
    $this->assertEquals(new String('www.m�ller.co'), $str);
  }

  #[@test]
  public function offsetUnsetInBetween() {
    $str= new String('www.m�ller.com');
    unset($str[5]);
    $this->assertEquals(new String('www.mller.com'), $str);
  }

  #[@test, @expect('lang.IndexOutOfBoundsException')]
  public function offsetUnsetNegative() {
    $str= new String('www.m�ller.com');
    unset($str[-1]);
  }

  #[@test, @expect('lang.IndexOutOfBoundsException')]
  public function offsetUnsetAfterEnd() {
    $str= new String('www.m�ller.com');
    unset($str[1024]);
  }

  #[@test]
  public function worksWithEchoStatement() {
    ob_start();
    echo new String('www.m�ller.com');
    $this->assertEquals('www.m�ller.com', ob_get_clean());
  }

  #[@test]
  public function stringCast() {
    $this->assertEquals('www.m�ller.com', (string)new String('www.m�ller.com'));
  }

  #[@test]
  public function usedInStringFunction() {
    $this->assertEquals(
      'ftp.m�ller.com', 
      str_replace('www', 'ftp', new String('www.m�ller.com')
    ));
  }

  #[@test, @expect('lang.FormatException')]
  public function getUmlautsAsAsciiBytes() {
    create(new String('���', 'iso-8859-1'))->getBytes('ASCII');
  }

  #[@test]
  public function getAsciiAsAsciiBytes() {
    $this->assertEquals(
      new \lang\types\Bytes('aou'), 
      create(new String('aou', 'iso-8859-1'))->getBytes('ASCII')
    );
  }
}
