<?php
require_once('./Formio.php');
$formio = new Formio('https://myproject.form.io', array(
  'default_password' => '123testing'
));

$user = $formio->sso('test@example.com');
print_r($user);

$resource = $formio->post('resource', array('data' => array(
  'title' => 'TPS Report',
  'status' => 'opened'
)))['body'];

print_r($resource);
?>
