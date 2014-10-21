<?php
// $Id: MY_form_helper.php 84 2011-04-13 19:22:44Z Henry $
// Last Change: $Date: 2011-04-13 15:22:44 -0400 (Wed, 13 Apr 2011) $

/*****
 * Function: my_DisplayForm (Display a form given various data parameters)
 *
 * Arguments:
 *    $formFields - list of fields with specific attributes
 *    $options    - list of select options for dropdown fields
 *    $data       - list of XHTML attributes for fields
 *    $buttons    - list of buttons to manage form
 *    $formAction - routine name that will process the submitted form contents
 *
 * Returns:
 *    -none-
 *
 *****/
function my_DisplayForm( $formFields, $options, $data, $buttons, $formAction ) {

/* ... start the form */
   echo form_open( $formAction );
?>
   
   <table>
      
<?php
/* ... cycle once through all the form fields */
   for ($i=0; $i < count( $formFields ); $i++) {

/* ... if the field has known errors from a recent processing, then display the error message */
?>
      <tr>
         <td colspan="3"><?= form_error( $formFields[$i]['fieldName'], "<p class='error'>", "</p>" ) ?></td>
      </tr>
      <tr>
   
<?php
/* ... handle if this field is a hidden field */
      if ($formFields[$i]['type'] == "hidden") {
?>
         <td><?= form_hidden( $data[$formFields[$i]['fieldName']] ) ?></td>
   
<?php
       }
      else {

/* ... display the text label for the field */
?>
         <td><?= form_label( $formFields[$i]['fieldText'], $formFields[$i]['fieldName'] ) ?></td>
   
<?php
/* ... if this is an input field, handle it */
         if ($formFields[$i]['type'] == "input") {
?>
         <td><?= form_input( $data[$formFields[$i]['fieldName']] ) ?></td>
   
<?php
          }

/* ... if this is a dropdown field, handle it */
         elseif ($formFields[$i]['type'] == "dropdown") {
            $selectorAttributes = "";
            if (is_array( $data[$formFields[$i]['fieldName']] )  &&  count( $data[$formFields[$i]['fieldName']] ) > 0) {
               foreach ($data[$formFields[$i]['fieldName']] as $attribute => $value) {
                  $selectorAttributes .= $attribute."='".$value."' ";
                }
             }
?>
         <td><?= form_dropdown( $formFields[$i]['fieldName'], $options[$formFields[$i]['fieldName']], $formFields[$i]['default'] != "" ? $formFields[$i]['default'] : "", $selectorAttributes ) ?></td>
         
<?php
          }
       }

/* ... if this field is meant to be required, we need to say so */
?>
         <td class="required"><?= $formFields[$i]['required'] ? "required" : " " ?></td>
      </tr>
   
<?php
    }
?>
   </table>
   
<?php
   /* ... do we have any buttons to put on the bottome of our form? */
   if (count( $buttons ) > 0) {      
      echo br( 2 );
      foreach ($buttons as $buttonType => $buttonAttr) {
         if ($buttonType == "reset") {
            echo form_reset( $buttonAttr[0], $buttonAttr[1] );
            echo nbs( 1 );
          }
         elseif ($buttonType == "regular") {
            echo form_button( $buttonAttr[0], $buttonAttr[1] );
            echo nbs( 1 );
          }
         elseif ($buttonType == "submit") {
            echo form_submit( $buttonAttr[0], $buttonAttr[1] );
            echo nbs( 1 );
          }
       }
    }

/* ... close out the form - we're done! */
   echo form_close();

/* ... time to go */
   return;
 }

