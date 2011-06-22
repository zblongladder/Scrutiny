#!/usr/local/bin/php
<?php
/*for some reason, the <? on the xml thing was confusing the 
PHP. Thus, this workaround.*/
print "<?xml version='1.0' encoding='UTF-8'?>";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" 
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>About Scrutiny</title>
<?php
include("include/stylesheets");
?>
</head>
<body>
<?php
//getting the neccessary connection info from file & connecting to db.
include("include/sql_connect");
//including header
include("include/header");
//including navbar
include("include/navbar");
?>
<div class="contents">
<h1 class="credits_title">Credits</h1>
<ul>
<li><h2 class="credits_section">Fall '10 to present</h2>
<ul>
<li>
<strong>Programming &amp; maintenance</strong>: Jezreel Ng '14
</li>
<li><strong>AAS Scrutiny comittee</strong>: Samuel Bell '11, Alex Stein '13, Ezra Van Negri '12</li>
</ul>
</li>
<li><h2 class="credits_section">Summer '07 to Spring '10</h2>
<ul>
<li>
<strong>Programming, design, &amp; maintenance</strong>: James Buchanan '09
</li>
<li><strong>AAS Scrutiny comittee</strong>: Selena Xie '09, 
Juliet Silberstein '10</li>
</ul>
</li>
<li><h2 class="credits_section">Fall '05 to Summer '07</h2>
<ul>
<li><strong>Database programing</strong>: Tosin Onafowokan '08E</li>
<li><strong>Getting the ball rolling</strong>: Tosin Onafowokan '08E</li>
<li><strong>AAS scrutiny committee</strong>: Joshua Stein</li>
</ul></li>
<li><h2 class="credits_section">Before Fall '05</h2>
<ul>
<li><strong>Database programming</strong>: Jonathan Tang '05</li>
<li><strong>HTML Layout</strong>: Wing Mui '05</li>
<li><strong>Getting the ball rolling</strong>: Ali Armour '07</li>
<li><strong>Administrative Liaison</strong>: Ryan Park '05</li>
<li><strong>IT/registrar Liaison</strong>: Marco LoCascio '07</li>
<li><strong>"Old Scrutiny" advisor</strong>: Maryna Gray '05</li>
<li><strong>AAS scrutiny committee</strong>: Ryan Park, Boris Bulayev, Joshua Stein, Marco LoCascio, Alexandra Berman, Esther Lim </li>
</ul>
</li>
</ul>
<p>Scrutiny would also like to thank NOTE, the AAS, the registrar's office, members of the previous Scrutiny incarnation, the IT department, and Dean Lieber for their support.</p>
</div>
<div class="farside"></div>
</body>
</html>
