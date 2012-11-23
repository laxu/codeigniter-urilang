<?php
define('BASEPATH', TRUE);
require_once('libraries/Urilang.php');
require_once('stubs.php');

class UrilangTest extends PHPUnit_Framework_TestCase {

  public function setUp() {
    $stub_methods = array(
      '_set_new_cookie',
      'get_lang_by_uri',
      'get_lang_by_cookie',
      'get_lang_by_request_header'
    );
    $this->urilang = $this->getMock('URILang', $stub_methods);
  }

  public function testGetDefaultLang()
  {
    $this->assertEquals('en', $this->urilang->get_default_lang());
  }

  public function testSelectedLanguage()
  {
    $this->assertEquals('en', $this->urilang->selected_language());
    $this->assertEquals(array('en' => 'english'), $this->urilang->selected_language(true));
  }

  public function testLangIsSupported()
  {
    $this->assertFalse($this->urilang->lang_is_supported('de'));
    $this->assertTrue($this->urilang->lang_is_supported('fr'));
  }

  public function testFindSupportedLang()
  {
    $langs = array('ab', 'fr', 'es');
    $this->assertEquals('fr', $this->urilang->find_supported_lang($langs));
  }

  public function testDetermineNewLang()
  {
    $this->_methodShouldReturn('get_lang_by_cookie', 'es');
    $this->urilang->determine_new_lang();
    $this->assertEquals('es', $this->urilang->selected_language());

    $this->_methodShouldReturn('get_lang_by_uri', 'fr');
    $this->urilang->determine_new_lang();
    $this->assertEquals('fr', $this->urilang->selected_language());
  }

  public function testGetLangArray()
  {
    $this->_methodShouldReturn('get_lang_by_uri', 'de');
    $this->_methodShouldReturn('get_lang_by_cookie', 'de');
    // only supported language
    $this->_methodShouldReturn('get_lang_by_request_header', 'fr');

    $this->urilang->determine_new_lang();

    $return_array = array(
      'fr' => 'french',
      'en' => 'english',
      'es' => 'spanish'
    );
    $this->assertEquals($return_array, $this->urilang->get_lang_array());
  }

  private function _methodShouldReturn($method, $value)
  {
    $this->urilang->expects($this->any())
      ->method($method)
      ->will($this->returnValue($value));
  }

}

/* End of file */
