PHP Integration with Form.io
============================
This serves as an initial PHP integration within Form.io. The intent is to make this library
not have any dependencies other than PHP to integrate within the Form.io API platform.

Examples
----------------------
**Single sign on (SSO) into Form.io from PHP**
```
<?php
  require_once('Formio.php');
  $formio = new Formio('https://myproject.form.io', array(
    'default_password' => '123testing'
  ));

  // This token can now be used to authenticate into Form.io API Platform
  print_r $formio->ssoToken('test@example.com');
?>
```
