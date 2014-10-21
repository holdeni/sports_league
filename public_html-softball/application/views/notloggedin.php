<?php
// $Id: notloggedin.php 84 2011-04-13 19:22:44Z Henry $
// Last Change: $Date: 2011-04-13 15:22:44 -0400 (Wed, 13 Apr 2011) $

?>

<div id="error">
   
<h1>Page Not Accessible</h1>

<p>You are trying to access a page that can only be accessed after you have logged into the web site.
   Please login using the form to the left and then try to reference the page again. If you don't have
   an account for this website, you may use the <em>register</em> link in the login panel to create an
   account.
</p>

<p>If you believe are logged in and were directed to this page, please send an <a href="mailto:<?= $this->config->item( 'my_replyAddress' ); ?>">email to the site's 
   webmaster</a> with the details as to what you were trying to do when before you arrived at this page.
   You are considered logged in when the login form is not visible to the left and the site displays
   your account name with a greeting.
</p>

</div>