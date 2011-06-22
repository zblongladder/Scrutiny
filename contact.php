#!/usr/local/bin/php
<?php
include("include/init_template");
?>
<head>
<title>Email Scrutiny!</title>
<?php
include("include/stylesheets");
?>
</head>
<body>
<?php
include("include/sql_connect");
include("include/header");
include("include/navbar");
?>
<div class="farside"></div>
<div class="contents">
  <h1>Email us!</h1>

<form action="submit_contact.php" method="post">
  <p>Your name:<input type="text" name="sender" size="60" /></p>
  <p>Your email address: <input type="text" name="reply_addr" size="60" /></p>
  <p>Subject:<input type="text" name="subject" size="60" /></p>
  <p><textarea name="email_body" rows="20" cols="60"></textarea></p>
  <p><input type="submit" value="Submit" /></p>
  </form>
  </div>
  </body>
  </html>
  