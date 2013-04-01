<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'unittest.TestCase',
    'unittest.TestSuite'
  );

  /**
   * Test values annotation
   *
   * @see  xp://unittest.TestSuite
   */
  class ValuesTest extends TestCase {
    protected $suite= NULL;
      
    /**
     * Setup method. Creates a new test suite.
     *
     */
    public function setUp() {
      $this->suite= new TestSuite();
    }

    /**
     * Tests inline value source
     *
     */
    #[@test]
    public function inline_value_source() {
      $test= newinstance('unittest.TestCase', array('fixture'), '{
        public $values= array();

        #[@test, @values(array(1, 2, 3))]
        public function fixture($value) {
          $this->values[]= $value;
        }
      }');
      $this->suite->runTest($test);
      $this->assertEquals(array(1, 2, 3), $test->values);
    }

    /**
     * Tests local value source
     *
     */
    #[@test]
    public function local_value_source() {
      $test= newinstance('unittest.TestCase', array('fixture'), '{
        public $values= array();

        public function values() {
          return array(1, 2, 3);
        }

        #[@test, @values("values")]
        public function fixture($value) {
          $this->values[]= $value;
        }
      }');
      $this->suite->runTest($test);
      $this->assertEquals(array(1, 2, 3), $test->values);
    }

    /**
     * Values for external_value_source tests
     *
     * @return var[]
     */
    public static function values() {
      return array(1, 2, 3);
    }

    /**
     * Tests external value source
     *
     */
    #[@test]
    public function external_value_source_fully_qualified_class() {
      $test= newinstance('unittest.TestCase', array('fixture'), '{
        public $values= array();

        #[@test, @values("net.xp_framework.unittest.tests.ValuesTest::values")]
        public function fixture($value) {
          $this->values[]= $value;
        }
      }');
      $this->suite->runTest($test);
      $this->assertEquals(array(1, 2, 3), $test->values);
    }

    /**
     * Tests external value source
     *
     */
    #[@test]
    public function external_value_source_unqualified_class() {
      $test= newinstance('unittest.TestCase', array('fixture'), '{
        public $values= array();

        #[@test, @values("ValuesTest::values")]
        public function fixture($value) {
          $this->values[]= $value;
        }
      }');
      $this->suite->runTest($test);
      $this->assertEquals(array(1, 2, 3), $test->values);
    }

    /**
     * Tests counting successes
     *
     */
    #[@test]
    public function all_variants_succeed() {
      $test= newinstance('unittest.TestCase', array('fixture'), '{
        #[@test, @values(array(1, 2, 3))]
        public function fixture($value) {
          $this->assertTrue(TRUE);
        }
      }');
      $r= $this->suite->runTest($test);
      $this->assertEquals(3, $r->successCount());
    }

    /**
     * Tests counting failures
     *
     */
    #[@test]
    public function all_variants_fail() {
      $test= newinstance('unittest.TestCase', array('fixture'), '{
        #[@test, @values(array(1, 2, 3))]
        public function fixture($value) {
          $this->assertTrue(FALSE);
        }
      }');
      $r= $this->suite->runTest($test);
      $this->assertEquals(3, $r->failureCount());
    }

    /**
     * Tests counting skipped tests
     *
     */
    #[@test]
    public function all_variants_skipped() {
      $test= newinstance('unittest.TestCase', array('fixture'), '{
        public function setUp() {
          throw new PrerequisitesNotMetError("Not ready yet");
        }

        #[@test, @values(array(1, 2, 3))]
        public function fixture($value) {
          throw new Error("Will never be reached");
        }
      }');
      $r= $this->suite->runTest($test);
      $this->assertEquals(3, $r->skipCount());
    }
  }
?>
