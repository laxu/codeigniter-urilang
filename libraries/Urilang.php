<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Sets the language specified in the URI-string
 * as the system's default language during runtime.
 *
 * @author  Max Zender <maxzender@gmail.com>
 * @package CodeIgniter
 * @license MIT License
 */
class URILang {

  /**
   * @var object The CI superobject
   */
  protected $_ci;

  /**
   * @var string The new default language's identifier
   */
  protected $_lang = '';

  /**
   * @var array The supported languages specified in the config file
   */
  protected $_supported_langs = array();


  /**
   * The constructor
   *
   * Sets the new default language on initialization of this class
   */
  public function __construct()
  {
    // get CI instance
    $this->_ci =& get_instance();
    $this->_ci->load->config('urilang');

    // get lang abbreviations from uri and config file
    $this->_supported_langs = $this->_ci->config->item('supported_languages');

    $this->determine_new_lang();

    log_message('debug', "URILang Library Initialized");
  }

  /**
   * Sets a new pref_lang cookie containg the given value
   *
   * @param string The language identifier to be stored in the cookie
   */
  protected function _set_new_cookie($value)
  {
    // remove old cookie
    $this->_ci->input->set_cookie('pref_lang', null, -1);
    unset($_COOKIE['pref_lang']);

    // set the new one
    $this->_ci->input->set_cookie('pref_lang', $value, $this->_ci->config->item('sess_expiration'));
  }

  /**
   * Sets the given language as the system's new default language
   *
   * @param string The new language's identifier
   */
  protected function _set_new_lang($lang)
  {
    $this->_set_new_cookie($lang);
    $this->_lang = $lang;

    // set the detected language as the system's new default
    $this->_ci->config->set_item('language', $this->_supported_langs[$this->_lang]);
  }

  /**
   * Determines the system's new language by:
   * 1. URI
   * 2. Cookie
   * 3. Request header (browser)
   * 4. The system's default language
   */
  public function determine_new_lang()
  {
    // the potential new language identifiers sorted by priority
    $potential_langs = array(
      $this->get_lang_by_uri(), // from the URI
      $this->get_lang_by_cookie(), // from the cookie
      $this->get_lang_by_request_header(), // from the request header
      $this->get_default_lang() // fallback language
    );

    $new_lang = $this->find_supported_lang($potential_langs);
    $this->_set_new_lang($new_lang);
  }

  /**
   * Returns the first language from the given array that is supported
   *
   * @param  array  The languages that should be checked
   * @return string The first supported language found
   */
  public function find_supported_lang($langs)
  {
    $first_match = '';

    foreach($langs as $lang)
    {
      if (empty($first_match) && $this->lang_is_supported($lang))
      {
        $first_match = $lang;
      }
    }

    return $first_match;
  }

  /**
   * Gets the language identifier from the current URI
   *
   * @return string The language identifier
   */
  public function get_lang_by_uri()
  {
    return $this->_ci->uri->segment(1);
  }

  /**
   * Gets the language identifier stored in the pref_lang cookie
   *
   * @return string|null The language identifier or null if the cookie doesn't exist
   */
  public function get_lang_by_cookie()
  {
    return $this->_ci->input->cookie('pref_lang', true);
  }

  /**
   * Gets the language identifier from the request's header (the browser's default language)
   *
   * @return string The language identifier
   */
  public function get_lang_by_request_header()
  {
    return substr(strtolower($this->_ci->input->server('HTTP_ACCEPT_LANGUAGE', true)), 0, 2);
  }

  /**
   * Checks if the passed language identifier is supported
   *
   * @param  string The language identifier to be verified
   * @return bool   Whether the language is supported or not
   */
  public function lang_is_supported($lang)
  {
    return isset($this->_supported_langs[$lang]);
  }

  /**
   * Determines the system's default language as set in the config file
   *
   * @return string The identifier of the default language
   */
  public function get_default_lang()
  {
    // check if default language is specified in supported languages
    if (in_array($this->_ci->config->item('language'), $this->_supported_langs))
    {
      $default_lang = array_search($this->_ci->config->item('language'), $this->_supported_langs, true);
    }
    else
    {
      // fall back to english
      $default_lang = 'en';
    }

    return $default_lang;
  }

  /**
   * Builds an array containing all supported languages (led by the current language),
   * which can be used to build a language selector
   *
   * @return array The array mentioned above
   */
  public function get_lang_array()
  {
    $default_lang = array($this->_lang => $this->_supported_langs[$this->_lang]);
    return $default_lang + $this->_supported_langs;
  }

  /**
   * Returns the selected language.
   *
   * @param bool Return an array with 'en' => 'english' or just the key 'en'
   */
  public function selected_language($fullResponse = FALSE)
  {
    $selected_language = array($this->_lang => $this->_supported_langs[$this->_lang]);
    if ($fullResponse)
    {
      return $selected_language;
    }
    else
    {
      return key($selected_language);
    }
  }

}

/* End of file */
