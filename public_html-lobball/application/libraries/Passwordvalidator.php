<?php
// $Id: Passwordvalidator.php 84 2011-04-13 19:22:44Z Henry $
// Last Change: $Date: 2011-04-13 15:22:44 -0400 (Wed, 13 Apr 2011) $

/*
Author: Mick Sear, eCreate
September 05

Description: Integrate this class into your admin GUI or end-user registration to
easily ensure that passwords meet your standards.

Written specifically for PHP5

The methods return true or false depending on whether the individual checks pass or fail, 
but you don't need to check for each one.  If you're testing a few conditions, you can 
just use getValid() to test for overall success and use getError() or nl2br($instance->getError())
to display the errors to your users.
*/


class Passwordvalidator{

   private $error;
   private $pass;
   private $valid = 1;       //Start with true

   public function __construct(){
//      $this->pass = trim($pass);
   }

   /*
   Set the password string to be checked.
   */
   public function setPassword($password){

      $this->pass = $password;
      
      return;
   }

   /*
   Check for min and max length constrains.
   */
   public function validate_length($min_length=1, $max_length=50){
      if (strlen($this->pass) < $min_length){
         $this->error .= "The password is too short\n";
         $this->valid = 0;
         return false;
      }

      if (strlen($this->pass) > $max_length){
         $this->error .= "The password is too long\n";
         $this->valid = 0;
         return false;
      }
      return true;
   }


   /*
   This method checks that there is at least one non-alpha character in the password
   */
   public function validate_non_numeric($num){
      
      if(preg_match("/[^a-z\s]/i", $this->pass) < $num){//preg_match returns number of times pattern matches
         $this->error .= "The password must contain at least $num non-alpha characters.\n";
         $this->valid = 0;
         return false;
      }
      return true;
   }

   /*
   This method checks for spaces
   */
   public function validate_whitespace(){
      if (preg_match("#\s#", $this->pass)){
         $this->error .= "The password must not contain whitespace\n";
         $this->valid = 0;
         return false;
      }
      return true;
   }

   /*
   Makes sure there are no uppercase characters in the password
   */
   public function validate_no_uppercase(){
      if (preg_match("[A-Z]", $this->pass)){
         $this->error .= "The password cannot contain uppercase characters\n";
         $this->valid = 0;
         return false;
      }
      return true;
   }
   
   /*
   Makes sure there is no lowercase in the password
   */
   public function validate_no_lowercase(){
      if (preg_match("[a-z]", $this->pass)){
         $this->error .= "The password cannot contain lowercase characters\n";
         $this->valid = 0;
         return false;
      }
      return true;
   }
   
   /*
   Handles custom patterns (see example)
   */
   public function validate_custom($pattern, $errorString){
      if (preg_match($pattern, $this->pass)){
         $this->error .= $errorString."\n";
         $this->valid = 0;
         return false;
      }
      return true;
   }
   
   public function getValid(){
       return $this->valid;
   }
   
   public function getError(){
       return $this->error;
   }
   
   
}
?>
