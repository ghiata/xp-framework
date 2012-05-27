<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'peer.http.HttpConstants',
    'io.streams.MemoryInputStream',
    'webservices.rest.RestXmlDeserializer',
    'webservices.rest.RestJsonDeserializer',
    'webservices.rest.RestResponse',
    'net.xp_framework.unittest.webservices.rest.IssueWithField'
  );

  /**
   * TestCase
   *
   * @see   xp://webservices.rest.RestResponse
   */
  class RestResponseTest extends TestCase {
    const JSON = 'application/json';
    const XML  = 'text/xml';

    protected static $deserializers= array();
 
    static function __static() {
      self::$deserializers[self::JSON]= new RestJsonDeserializer();
      self::$deserializers[self::XML]= new RestXmlDeserializer();
    }
  
    /**
     * Creates a new fixture
     *
     * @param   string content
     * @param   string body
     * @return  webservices.rest.RestResponse
     */
    protected function newFixture($content, $body) {
      return new RestResponse(
        HttpConstants::STATUS_OK,
        'OK',
        self::$deserializers[$content],
        array(),
        Type::forName('[:var]'),
        new MemoryInputStream($body)
      );
    }

    /**
     * Test content()
     *
     */
    #[@test]
    public function content() {
      $fixture= $this->newFixture(self::JSON, '{ "issue_id" : 1, "title" : null }');
      $this->assertEquals(
        '{ "issue_id" : 1, "title" : null }',
        $fixture->content()
      );
    }
    
    /**
     * Test data()
     *
     */
    #[@test]
    public function dataAsMap() {
      $fixture= $this->newFixture(self::JSON, '{ "issue_id" : 1, "title" : null }');
      $this->assertEquals(
        array('issue_id' => 1, 'title' => NULL), 
        $fixture->data()
      );
    }

    /**
     * Test data()
     *
     */
    #[@test]
    public function dataAsType() {
      $fixture= $this->newFixture(self::JSON, '{ "issue_id" : 1, "title" : null }');
      $this->assertEquals(
        new net�xp_framework�unittest�webservices�rest�IssueWithField(1, NULL), 
        $fixture->data(XPClass::forName('net.xp_framework.unittest.webservices.rest.IssueWithField'))
      );
    }

    /**
     * Test data()
     *
     */
    #[@test]
    public function dataAsTypeByName() {
      $fixture= $this->newFixture(self::JSON, '{ "issue_id" : 1, "title" : null }');
      $this->assertEquals(
        new net�xp_framework�unittest�webservices�rest�IssueWithField(1, NULL), 
        $fixture->data('net.xp_framework.unittest.webservices.rest.IssueWithField')
      );
    }

    /**
     * Test data()
     *
     */
    #[@test, @expect('lang.ClassNotFoundException')]
    public function dataAsNonExistantType() {
      $fixture= $this->newFixture(self::JSON, '{ "issue_id" : 1, "title" : null }');
      $fixture->data('non.existant.Type');
    }


    /**
     * Test data()
     *
     */
    #[@test]
    public function xmlAsMap() {
      $fixture= $this->newFixture(self::XML, '<issue><issue_id>1</issue_id><title/></issue>');
      $this->assertEquals(
        array('issue_id' => '1', 'title' => ''), 
        $fixture->data()
      );
    }

    /**
     * Test data()
     *
     */
    #[@test]
    public function nestedXmlAsMap() {
      $fixture= $this->newFixture(self::XML, '<book><author><id>1549</id><name>Timm</name></author></book>');
      $this->assertEquals(
        array('author' => array('id' => '1549', 'name' => 'Timm')),
        $fixture->data()
      );
    }
  }
?>
