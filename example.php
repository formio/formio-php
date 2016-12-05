<?php
require_once('Formio.php');
$formio = new Formio('https://myproject.form.io', array(
  'default_password' => '123testing'
));

print $formio->ssoToken('test@example.com');
print $formio->ssoToken('test2@example.com');
print $formio->ssoToken('test3@example.com');
print $formio->ssoToken('test@example.com');
?>
