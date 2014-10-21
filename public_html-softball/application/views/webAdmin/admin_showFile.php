<?php
// $Id: admin_showFile.php 163 2011-06-08 13:41:10Z Henry $
// Last Change: $Date: 2011-06-08 09:41:10 -0400 (Wed, 08 Jun 2011) $
?>
<h1>File contents of <?= $pathname ?></h1>

<div id="showFile">
<pre>

<?= htmlspecialchars( $fileContents ) ?>

</pre>
</div>