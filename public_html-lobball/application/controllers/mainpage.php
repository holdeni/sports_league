<?php

// $Id: mainpage.php 246 2012-05-15 00:08:54Z Henry $
// Last Change: $Date: 2012-05-14 20:08:54 -0400 (Mon, 14 May 2012) $

class Mainpage extends CI_Controller {

/*****
 * Function: Mainpage (constructor)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function __construct() {
      parent::__construct();
      session_start();
    }



/*****
 * Function: index
 *
 * Arguments:
 *    $mainView - name of view to display or if not provided, use internally defined default value
 *
 * Returns:
 *    -none-
 *
 *****/
   function index( $data = array()) {

/* ... data declarations */
      $data['announcements'] = array();
      $todaysDate = date( "Y-m-d" );

/* ... define values for template variables to display on page */
      if (!array_key_exists( 'title', $data )) {
         $data['title'] = $this->config->item( 'siteName' );
       }
      $data['firstMonday'] = $this->config->item( 'firstMonday' );
      $data['endOfSeason'] = date( "Y", strtotime( $data['firstMonday'] ) )."-10-01";

/* ... set the name of the page to be displayed (and any header attributes) */
      if (!array_key_exists( 'main', $data )) {
         $data['main'] = "home";
         $data['noCache'] = TRUE;
       }

/* ... if we're displaying the main Home page, see what announcements are to appear on it */
      if ($data['main'] == "home") {
         $data['announcements'] = $this->Model_Announcement->getCurrentSet();
         $data['announcements'] = $this->Model_Announcement->reviewSet( $data['announcements'] );
       }

/* ... if displaying main page and we are in midst of season, display 2 week's of schedule */
      if ($data['main'] == 'home'  &&  strtotime( $todaysDate ) < strtotime( $data['endOfSeason'] )) {

/* ... determine last Sunday to current date, unless today is Sunday; then from there, a week prior and ahead */
         if (date( "D" ) != "Sun") {
            $curSunday = date( "Y-m-d", strtotime( "last Sunday" ) );
          }
         else {
            $curSunday = date( "Y-m-d" );
          }
         $prevSunday = date( "Y-m-d", strtotime( $curSunday." - 1 week" ) );
         $nextSunday = date( "Y-m-d", strtotime( $curSunday." + 1 week" ) );

/* ... get the slate of games from last week */
         $lastWeekGames = $this->Model_Schedule->getSetOfGames( $prevSunday, $curSunday, FALSE );
         if (count( $lastWeekGames ) > 0) {
            $data['lastWeekSched'] = $this->_displayWeeksGames( $lastWeekGames );
          }

/* ... get the slate of games from this week */
         $thisWeekGames = $this->Model_Schedule->getSetOfGames( $curSunday, $nextSunday, FALSE );
         if (count( $thisWeekGames ) > 0) {
            $data['thisWeekSched'] = $this->_displayWeeksGames( $thisWeekGames );
          }
       }

/* ... determine which view for the small left hand contextual navigation menu */
      if (array_key_exists( 'UserId', $_SESSION )) {
         $data['contextNav'] = "loggedIn";
       }
      else {
         $data['contextNav'] = "loggedOut";
       }

/* ... enable our template variables and then display the template, as we want it shown */
      $this->load->vars( $data );
      $this->load->view( "template" );

/* ... time to go */
      return;
    }



/*****
 * Function: processLogin (Used to check account login)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *
 *
 *****/
   function processLogin() {

      $_SESSION['loginMsg'] = "unknown";

/* ... setup and perform validation on the basic data the login form should have provided us */
      $this->load->library( 'form_validation' );
      $this->form_validation->set_rules( "email", "Email Address", "required|valid_email" );
      $this->form_validation->set_rules( "password", "Password", "required" );

      if ($this->form_validation->run()) {

/* ... form was used correctly so now we check the data in the form to see if we know who is trying to login */
         $userId = $this->Model_Account->verifyAccount( $this->input->post( 'email' ), $this->input->post( 'password' ) );
         if ($userId > 0) {
            $this->Model_Account->loginAccount( $userId );
            $_SESSION['loginMsg'] = "verified";
          }
         else {
            $_SESSION['loginMsg'] = "invalid";
          }
       }

/* ... time to go - in this case cause the main page to be redisplayed */
      $this->index();
      return;
    }



/*****
 * Function: logout (Used to clear SESSION variables associated with a logged in account)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function logout() {

/* ... remove all appropriate SESSION variables */
      $this->Model_Account->logoutAccount();
      unset( $_SESSION['loginMsg'] );
      $this->index();

/* ... time to go */
      return;
    }



/*****
 * Function: register (Form to register new account to site)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function register() {

/* ... this form is not to be shown when logged in; if logged in just show the home page instead (unless we just completed registering an account) */
      if ($this->Model_Account->amILoggedIn()) {
         if (!array_key_exists( 'registerMsg', $_SESSION )) {
            redirect( "mainpage/index", "refresh" );
          }
       }

/* ... define values for template variables to display on page */
      $data['title'] = "Account Registration - ".$this->config->item( 'siteName' );

/* ... set the name of the page to be displayed */
      $data['main'] = "register";

/* ... complete our flow as we would for a normal page */
      $this->index( $data );

/* ... time to go */
      return;
    }



/*****
 * Function: processRegistration (Used to check new account registration)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *
 *
 *****/
   function processRegistration() {

      $_SESSION['registerMsg'] = "unknown";

/* ... setup and perform validation on the basic data the new account form should have provided us */
      $this->load->library( 'form_validation' );
      $this->form_validation->set_rules( "r_email", "Email Address", "required|valid_email" );
      $this->form_validation->set_rules( "r_password", "Password", "required|min_length[8]|callback_checkPasswordComplexity" );
      $this->form_validation->set_rules( "passwordConf", "Password Confirmation", "required|matches[r_password]" );
      $this->form_validation->set_rules( "firstname", "First Name", "required" );
      $this->form_validation->set_rules( "lastname", "Last Name", "required" );
      $this->form_validation->set_rules( "homephone", "Home Phone", "required|exact_length[10]|numeric" );
      $this->form_validation->set_rules( "workphone", "Work Phone", "exact_length[10]|numeric" );
      $this->form_validation->set_rules( "cellphone", "Cell Phone", "exact_length[10]|numeric" );
      $this->form_validation->set_rules( "altemail", "Alternative Email", "valid_email" );

 /* ... validate the information in the form */
      if ($this->form_validation->run()) {

/* ... need to check to ensure we don't have an entry for this email account (since that is our unique key) */
         if (!$this->Model_Account->checkEmail( $this->input->post( 'r_email' ) )) {

/* ... add the new user record to the database */
            $data = array(
               "EmailAccount"     => $this->input->post( 'r_email' ),
               "Password"  => md5( $this->input->post( 'r_password' ) ),
               "FirstName" => $this->input->post( 'firstname' ),
               "LastName"  => $this->input->post( 'lastname' ),
               "HomePhone" => $this->input->post( 'homephone' ),
               "WorkPhone" => $this->input->post( 'workphone' ),
               "CellPhone" => $this->input->post( 'cellphone' ),
               "AltEmail"  => $this->input->post( 'altemail' ),
               "Status"    => 'ACTIVE',
             );
            $userId = $this->Model_Account->addAccount( $data );

/* ... there are a few related tasks we need to take care of using the numeric user ID for this account */
            if ( $userId > 0) {

/* ... see if we need to update any team contacts to use reference a known account, not just a stored email address */
               $this->Model_Team->contactArriving( $data['EmailAccount'], $userId );

/* ... log the account into the website */
               $this->Model_Account->loginAccount( $userId );
               $_SESSION['registerMsg'] = "valid";

/* ... send the account owner an email with the registration details */
               $this->Model_Account->sendRegistrationEmail( $userId, $this->input->post( 'r_password' ), TRUE );

             }

          }
         else {

/* ... indicate that the form failed validation in some way -- this will help display appropriate field error messages */
            $_SESSION['registerMsg'] = "invalid";
          }

       }

/* ... time to go */
      $this->register();
      return;
    }



/*****
 * Function: notLoggedIn (Display appropriate information when trying to access a page only available if logged in)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function notLoggedIn() {

/* ... define values for template variables to display on page */
      $data['title'] = "Login Error - ".$this->config->item( 'siteName' );

/* ... set the name of the page to be displayed */
      $data['main'] = "notloggedin";

/* ... determine which view for the small left hand contextual navigation menu */
      if (array_key_exists( 'UserId', $_SESSION )) {
         $data['contextNav'] = "loggedIn";
       }
      else {
         $data['contextNav'] = "loggedOut";
       }

/* ... enable our template variables and then display the template, as we want it shown */
      $this->load->vars( $data );
      $this->load->view( "template" );

/* ... time to go */
      return;
    }



/*****
 * Function: privacyPolicy (Display the site's privacy policy)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function privacyPolicy() {

/* ... define values for template variables to display on page */
      $data['title'] = "Privacy Policy - ".$this->config->item( 'siteName' );

/* ... set the name of the page to be displayed */
      $data['main'] = "privacyPolicy";

/* ... determine which view for the small left hand contextual navigation menu */
      if (array_key_exists( 'UserId', $_SESSION )) {
         $data['contextNav'] = "loggedIn";
       }
      else {
         $data['contextNav'] = "loggedOut";
       }

/* ... enable our template variables and then display the template, as we want it shown */
      $this->load->vars( $data );
      $this->load->view( "template" );

/* ... time to go */
      return;
    }



/*****
 * Function: checkPasswordComplexity (Check complexity of a password string against some basic rules)
 *
 * Arguments:
 *    $password - string containing password to be checked
 *
 * Returns:
 *    -none-
 *
 *****/
   function checkPasswordComplexity( $password ) {

      $this->load->library( "Passwordvalidator" );

/* ... check the complexity of the password */
      $this->passwordvalidator->setPassword( $password );
      if ($this->passwordvalidator->validate_non_numeric( 1 )) {                //Password must have 1 non-alpha character in it.
         $this->passwordvalidator->validate_whitespace();                       //No whitespace please
       }

/* ... if we didn't pass, set an appropriate error message */
      if ($this->passwordvalidator->getValid() == 0) {
         $this->form_validation->set_message('checkPasswordComplexity', 'The %s field must contain a string with no spaces and at least 1 non-alpha character');
       }

/* ... time to go */
      return( $this->passwordvalidator->getValid() == 1 ? TRUE : FALSE );
    }



/*****
 * Function: recover (Display form for email address requiring a password reset)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function recover() {

/* ... this form is not to be shown when logged in; if logged in just show the home page instead */
      if ($this->Model_Account->amILoggedIn()) {
         redirect( "mainpage/index", "refresh" );;
       }

/* ... define values for template variables to display on page */
      $data['title'] = "Account Recovery - ".$this->config->item( 'siteName' );

/* ... set the name of the page to be displayed */
      $data['main'] = "recovery";

/* ... complete our flow as we would for a normal page */
      $this->index( $data );

/* ... time to go */
      return;
    }



/*****
 * Function: processRecovery (Used set temporary password on account and email it to account owner)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *
 *
 *****/
   function processRecovery() {

      $_SESSION['verifMsg'] = "unknown";

/* ... setup and perform validation on the basic data the login form should have provided us */
      $this->load->library( 'form_validation' );
      $this->form_validation->set_rules( "r_email", "Email Address", "required|valid_email" );

      if ($this->form_validation->run()) {

/* ... form was used correctly so now we check the data in the form to see if werecogonize the account */
         if ($this->Model_Account->checkEmail( $this->input->post( 'r_email' ) )) {

            $_SESSION['verifMsg'] = "verified";

/* ... generate a new temporary password for the account */
            $this->load->library( "RandomConditionPass" );
            $this->randomconditionpass->initialize( 10, array( 'caps' => 4, 'small' => 4, 'nums'=> 2, 'specs' => 0  ) );
            $newPassword = $this->randomconditionpass->PassGen();
            $newInfo = array (
               'Password' => md5( $newPassword ),
             );
            $this->Model_Account->updateAccount( $this->Model_Account->getUserID( $this->input->post( 'r_email' ) ), $newInfo );

/* ... email the new password to the account's email address */
            $toAddr = array( $this->input->post( 'r_email' ) );
            $bccAddr = array();
            $subject = "Password reset requested for Kanata Lobball website";
            $body  = "This email has been sent by an automated process and does not require a reply.\n\n";
            $body .= "Someone recently requested a new password for your account at the website. Hopefully this was you, ";
            $body .= "or done with your knowledge. Below is the new password assigned to your account:\n";
            $body .= "\n".$newPassword."\n\n";
            $body .= "Note: the new password above is case sensitive and consists of 10 characters exactly. It must be typed ";
            $body .= "exactly as shown on the line.\n\n";
            $body .= "Visit the website ".base_url().", and login to your account using this password. Then you may ";
            $body .= "select 'My Account' from the left side contextual navigation and reset your password to something that you will ";
            $body .= "remember easily.\n\n";
            $body .= "If you did NOT initiate this change for your password, please forward this email to ";
            $body .= $this->config->item( 'my_replyAddress' )." stating this fact so that we may investigate on your behalf.\n\n";

            $this->Model_Mail->sendTextEmail( $toAddr, array(), array(), $subject, $body );

          }
         else {
            $_SESSION['verifMsg'] = "invalid";
          }
       }

/* ... time to go - in this case cause the main page to be redisplayed */
      $this->recover();
      return;
    }



/*****
 * Function: _displayWeeksGames (Display a week's set of games)
 *
 * Arguments:
 *    $schedGames - array of games with full details
 *
 * Returns:
 *
 *
 *****/
   function _displayWeeksGames( $schedGames ) {

/* ... data declarations */
      $dayOfWeek = array();
      $textBlock = "";

/* ... figure out which games are on which days of the week */
      for ($i = 0; $i < count( $schedGames ); $i++) {
         $dayOfWeek[date( "D", strtotime( $schedGames[$i]['Date'] ) )][] = $i;
       }

/* ... we'll buffer the XHTML output and then pass it back in a string */
      ob_start();

/* ... for each day of the week with games, we will now build a block with that day's games */
      $firstBlock = true;
      foreach ($dayOfWeek as $dow => $games) {

?>
      <div id="dayOfGames" class="<?= !$firstBlock ? 'notFirst' : '' ?>">
         <table id="<?= $dow ?>" width="100%">
         <caption><?= $dow ?></caption>

<?php
         foreach ($games as $gameIndex) {
            if ($schedGames[$gameIndex]['Status'] == "PLAYED") {
               if ($schedGames[$gameIndex]['VisitScore'] >= $schedGames[$gameIndex]['HomeScore']) {
                  $team1 = htmlspecialchars( $this->Model_Team->getTeamName( $schedGames[$gameIndex]['VisitTeamID'] ) ).": ".$schedGames[$gameIndex]['VisitScore'];
                  $state = "vs";
                  $team2 = htmlspecialchars( $this->Model_Team->getTeamName( $schedGames[$gameIndex]['HomeTeamID'] ) ).": ".$schedGames[$gameIndex]['HomeScore'];
                }
               else {
                  $team1 = htmlspecialchars( $this->Model_Team->getTeamName( $schedGames[$gameIndex]['HomeTeamID'] ) ).": ".$schedGames[$gameIndex]['HomeScore'];
                  $state = "vs";
                  $team2 = htmlspecialchars( $this->Model_Team->getTeamName( $schedGames[$gameIndex]['VisitTeamID'] ) ).": ".$schedGames[$gameIndex]['VisitScore'];
                }
             }
            elseif ($schedGames[$gameIndex]['Status'] == "SCHEDULED") {
               $team1 = htmlspecialchars( $this->Model_Team->getTeamName( $schedGames[$gameIndex]['VisitTeamID'] ) );
               $state = "at";
               $team2 = htmlspecialchars( $this->Model_Team->getTeamName( $schedGames[$gameIndex]['HomeTeamID'] ) );
             }
            else {
               $team1 = htmlspecialchars( $this->Model_Team->getTeamName( $schedGames[$gameIndex]['VisitTeamID'] ) );
               $state = "RAINED OUT";
               $team2 = htmlspecialchars( $this->Model_Team->getTeamName( $schedGames[$gameIndex]['HomeTeamID'] ) );
             }
?>
<!--
            <tr>
               <td><?= htmlspecialchars( $this->Model_Team->getTeamName( $schedGames[$gameIndex]['VisitTeamID'] ) ).":" ?></td>
               <td><?= $schedGames[$gameIndex]['VisitScore'] ?></td>
               <td> at </td>
               <td><?= htmlspecialchars( $this->Model_Team->getTeamName( $schedGames[$gameIndex]['HomeTeamID'] ) ).":" ?></td>
               <td><?= $schedGames[$gameIndex]['HomeScore'] ?></td>
            </tr>
-->
            <tr>
               <td><?= $team1 ?></td>
               <td><?= $state ?> </td>
               <td><?= $team2 ?></td>
            </tr>

<?php
          }
?>
         </table>
      </div>

<?php
/* ... if this is the first block of games for the week, unset our flag used to define spacing between subsequent blocks */
         $firstBlock = false;

       }

/* ... time to go */
      $textBlock = ob_get_contents();
      ob_end_clean();
      return( $textBlock );
    }



  } /* ... End of Controller */
