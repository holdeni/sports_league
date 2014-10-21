// $Id: myApp-jQuery.js 214 2012-03-26 22:38:19Z Henry $
// Last Change: $Date: 2012-03-26 18:38:19 -0400 (Mon, 26 Mar 2012) $

// jQuery code used within my sport leagues' websites

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


