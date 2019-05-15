/**
 * Rotator.js
 * Used for rotating an HTML element
 * 
 * AUTHOR: Thomas Green <P460247@tafe.wa.edu.au>
 * ID:     P460247
 */

var looper;
var degrees = 0;
var bounceBack = false;

function rotateHTMLElement(elem, speed)
{
    var element = document.getElementById(elem);
    //Depending on the browser, use a suitable transform
    if (navigator.userAgent.match("Chrome")) {
        element.style.WebkitTransform = "rotate(" + degrees + "deg)";
    } else if (navigator.userAgent.match("Firefox")) {
        element.style.MozTransform = "rotate(" + degrees + "deg)";
    } else if (navigator.userAgent.match("MSIE")) {
        element.style.msTransform = "rotate(" + degrees + "deg)";
    } else if (navigator.userAgent.match("Opera")) {
        element.style.OTransform = "rotate(" + degrees + "deg)";
    } else {
        element.style.transform = "rotate(" + degrees + "deg)";
    }
    //Loop the function every *speed* seconds
    looper = setTimeout(
        'rotateHTMLElement(\'' + elem + '\',' + 
        speed + ')', speed
    );

    
    //Reverse the transform back and forth between -10 and 10 degrees
    if (degrees > 10) {
        degrees--;
        bounceBack = true;
    } else if (degrees < -10) {
        degrees++;
        bounceBack = false;
    } else if (bounceBack == true) {
        degrees--;
    } else if (bounceBack == false) {
        degrees++;
    }
}
