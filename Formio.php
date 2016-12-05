<?php
/**
 * Class to perform a single sign-on with PHP
 *
 *  Usage:
 *
 *    <?php
 *      require_once('Formio.php');
 *      $formio = new Formio('https://myproject.form.io', array(
 *        'default_password' => '123testing'
 *      ));
 *
 *      // Checks if the user exists, create them if not, log them in if so.
 *      print $formio->ssoToken('test@example.com');
 *    ?>
 *
 */
class Formio {
  public $project = '';
  public $options = array(
    'resource' => 'user',
    'login' => 'user/login',
    'register' => 'user/register',
    'id_field' => 'email',
    'password_field' => 'password',
    'default_password' => ''
  );
  public function __construct($project, $options = array()) {
    $this->project = $project;
    foreach ($this->options as $key => $default) {
      if (isset($options[$key])) {
        $this->options[$key] = $options[$key];
      }
    }
  }

  private function getHeaders($header) {
    $headers = array();
    foreach (explode("\r\n", $header) as $i => $line) {
      if ($i === 0) {
        $headers['http_code'] = $line;
      }
      else {
        list ($key, $value) = explode(': ', $line);
        $headers[$key] = $value;
      }
    }
    return $headers;
  }

  private function request($curl) {
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      "cache-control: no-cache",
      "content-type: application/json"
    ));
    curl_setopt($curl, CURLOPT_ENCODING, '');
    curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, 1);
    $response = curl_exec($curl);
    list($header, $body) = explode("\r\n\r\n", $response, 2);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
      echo "cURL Error #:" . $err;
      $retVal = array('headers' => array(), 'body' => array(), 'error' => $err);
    } else {
      $retVal = array(
        'headers' => $this->getHeaders($header),
        'body' => json_decode($body, true)
      );
    }
    return $retVal;
  }

  private function get($url) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_CUSTOMREQUEST => "GET",
    ));
    return $this->request($curl);
  }

  private function post($url, $body) {
    $curl = curl_init();
    $data = json_encode($body);
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $data,
    ));
    return $this->request($curl);
  }

  /**
   * Checks to see if a submission exists.
   */
  public function exists($id) {
    $url = $this->project . '/' . $this->options['resource'];
    $url .= '/exists?data.' . $this->options['id_field'] . '=' . $id;
    $response = $this->get($url);
    return !!$response['body']['_id'];
  }

  /**
   * Log in an existing user.
   */
  public function login($id) {
    $body = array('data' => array());
    $body['data'][$this->options['id_field']] = $id;
    $body['data'][$this->options['password_field']] = $this->options['default_password'];
    $response = $this->post($this->project . '/' . $this->options['login'], $body);
    return $response['headers']['x-jwt-token'];
  }

  /**
   * Register a new user.
   */
  public function register($id) {
    $body = array('data' => array());
    $body['data'][$this->options['id_field']] = $id;
    $body['data'][$this->options['password_field']] = $this->options['default_password'];
    $response = $this->post($this->project . '/' . $this->options['register'], $body);
    return $response['headers']['x-jwt-token'];
  }

  /**
   * Performs a single-sign-on within Form.io and returns their token.
   *
   *   1.) Checks to see if the user exists.
   *   2.) If so, then logs them in and returns their token.
   *   3.) If not, then it creates their account with default password and returs their token.
   */
  public function ssoToken($id) {
    if ($this->exists($id)) {
      return $this->login($id);
    }
    else {
      return $this->register($id);
    }
  }
}
?>
