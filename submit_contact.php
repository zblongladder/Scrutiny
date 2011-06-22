#!/usr/local/bin/php
<?php
include("include/sql_connect");
$a_query=mysql_query("SELECT address FROM contact_emails WHERE is_active=1")
  or die(mysql_error());
if(!$_POST['reply_addr']){
  die("Please provide an email address");
 }
while($row=mysql_fetch_row($a_query)){
  mail($row[0],$_POST['sender'].": ".$_POST['subject'],
       $_POST['sender']." (".$_POST['reply_addr'].") ".
       " writes:\n\n".$_POST['email_body']);
 }
//redirecting back to index
header("Location: ./");
exit;

?>