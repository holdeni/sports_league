// $Id: myApp-jQuery.js 84 2011-04-13 19:22:44Z Henry $
// Last Change: $Date: 2011-04-13 15:22:44 -0400 (Wed, 13 Apr 2011) $

// jQuery code used within Kanata Lobball website

// Function: hideElement
//
// Argument(s):
//    $tagID - tag ID of element to be hidden
//
// Effect:
//    The element will be hidden from view.
//

function hideElement( tagID ) {
   $(tagID).hide();
 }



// Function: showElement
//
// Argument(s):
//    $tagID - tag ID of element to be revealed
//
// Effect:
//    The element will be revealed into view.
//

function showElement( tagID ) {
   $(tagID).show();
 }



// Function: toggleElement
//
// Argument(s):
//    $tagID - tag ID of element to be revealed or hidden
//
// Effect:
//    The element will be revealed if hidden, or hidden if already visible
//

function toggleElement( tagID ) {
   $(tagID).toggle();
 }


