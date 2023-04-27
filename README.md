### This repository is now considered legacy and no longer supported. Please take a look at our recent repositories and help documentation at the following links.

 - https://help.form.io
 - https://github.com/formio/formio.js
 - https://github.com/formio/formio
 - https://github.com/formio/react
 - https://github.com/formio/angular
 - https://github.com/formio/vue

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

**Login as an employee and create a resource.**
```
<?php
  require_once('Formio.php');
  $formio = new Formio('https://myproject.form.io', array(
    'resource' => 'employee'
  ));
  $employee = $formio->login('employee@example.com', '123testing');

  // The employee object.
  print_r($employee);

  // The users token...
  print $formio->token;

  // This will now post using the Employee's auth token.
  $resource = $formio->post('resource', array('data' => array(
    'employee' => $employee['_id'],
    'status' => 'opened',
    'title' => 'TPS Report'
  )));

  print_r($resource['body']);
?>
```
