////////////////////////////////////////////////////////////////
// GLOBALS
KEY = ""
EBIBLE_SNIPPET_URL = "http://ebible.com/api/ebibleSnippet"


/////////////////////////////////////////////////////////////////////
// Utility functions taken from standard.js

// We are passed a dom_element and we will
//  Collect all of the descendants of dom_element that are of class <className>
// into an array.
function findDescendantsByClass(dom_element, className) {
  var elements = new Array();

  for (var i=0; i < dom_element.childNodes.length; ++i) {
    var element = dom_element.childNodes[i];

    //alert("checking element.id=" + element.id + " element.className=" + element.className);

    // Get all of the descendants that are of class <className>
    var descendant_elements = findDescendantsByClass(element, className);
    elements = elements.concat(descendant_elements);  // Append them to our list

    // Make sure that the tagName attribute exists before the comparison!
    // It might not if "element" is not a DOM Element (for example, it could
    // be a #text element
    if (element.tagName) {
      // Check element to see if it's a member of <className>
      if (StyleClass.contains(element, className)) {
        // It is, so push it onto our <elements> list
        elements.push(element);
      }
    }
  }

  return elements;
}

// This function takes a dom element <element> and searches through its
// ancestors until if finds an element with <tagName>.  If it cannot find an
// ancestor it returns null.
function findAncestorByTag(element, tagName) {
  // Make sure element is valid.
  tagName = tagName.toUpperCase();

  if (element) {
    element = element.parentNode;

    // Keep going up the tree until we find an element of className
    while (element && element.tagName) {
      if (element.tagName.toUpperCase() == tagName) {
        return(element)
      }
      element = element.parentNode;
    }
  }

  return(null);
}

function eventTarget(event) {
  if (event) {
    if (event.target) {
      return(event.target)
    }

    if (event.srcElement) {
      return(event.srcElement)
    }
  }
  else {
    return(null)
  }
}

/******************************************************************************
*  Cross browser way to add and remove events
******************************************************************************/
function addEBEvent(obj, evType, fn, useCapture){
  if (obj.addEventListener){
    obj.addEventListener(evType, fn, useCapture);
    return true;
  } else if (obj.attachEvent){
    var r = obj.attachEvent("on"+evType, fn);
    return r;
  } else {
    //alert("Handler could not be attached");
  }
}

function removeEBEvent(obj, evType, fn, useCapture){
  if (obj.removeEventListener){
    obj.removeEventListener(evType, fn, useCapture);
    return true;
  } else if (obj.detachEvent){
    var r = obj.detachEvent("on"+evType, fn);
    return r;
  } else {
    //alert("Handler could not be removed");
  }
}


/******************************************************************************
Here are some functions to help dealing with CSS classes.
******************************************************************************/

function StyleClass() {
}

StyleClass.split = function(classes_string) {
  if (classes_string) {
    return classes_string.split(" ");
  }
  else {
    return(new Array());
  }
}

StyleClass.join = function(classes) {
  // Returns a class name suitable for insertion into a dom elements
  // className attribute.
  if (classes) {
    return(classes.join(" "));
  }
  else {
    return("");
  }
}

// <container> is a dom_element, a string, or an array.
// Returns true if <container> contains style class <className> and false
// otherwise.
StyleClass.contains = function(container, className) {
  // dom_element_or_class_string
  //   Must be a DOM ELEMENT or a class STRING
  var classes;
  if (typeof container == "string") {
    classes = StyleClass.split(container);
  }
  else if (typeof container == "array") {
    classes = container;
  }
  else {
    // Assume container is a DOM element.
    classes = StyleClass.split(container.className);
  }

  // Returns true of classes collection contains className
  return(contains(classes, className));
}

// <array> is an array.  The function returns true if <array> contains an object
// that is equal to <obj> (but not necessarily the same object!), and false
// otherwise.
function contains(array, obj) {
  var idx = indexOf(array, obj);

  if (idx >= 0) {
    return(true);
  }
  else {
    return(false);
  }
}

// <array> is an array.  The function returns the index of the first object in
// the array that is equal to <obj> (but not necessarily the same object!).
// If no object equal to <obj> is found, returns -1.
function indexOf(array, obj) {
  for (var i = 0; i<array.length; i++) {
    if (array[i] == obj) {
      return(i);
    }
  }

  return(-1);
}

// Author: Jason Levitt
// Date: December 7th, 2005
// Constructor -- pass a REST request URL to the constructor
//
function JSONscriptRequest(fullUrl) {
    // REST request path
    this.fullUrl = fullUrl; 
    // Keep IE from caching requests
    this.noCacheIE = '&noCacheIE=' + (new Date()).getTime();
    // Get the DOM location to put the script tag
    this.headLoc = document.getElementsByTagName("head").item(0);
    // Generate a unique script tag id
    this.scriptId = 'YJscriptId' + JSONscriptRequest.scriptCounter++;
}

// Static script ID counter
JSONscriptRequest.scriptCounter = 1;

// buildScriptTag method
//
JSONscriptRequest.prototype.buildScriptTag = function () {

    // Create the script tag
    this.scriptObj = document.createElement("script");
    
    // Add script object attributes
    this.scriptObj.setAttribute("type", "text/javascript");
    this.scriptObj.setAttribute("src", this.fullUrl + this.noCacheIE);
    this.scriptObj.setAttribute("id", this.scriptId);
}
 
// removeScriptTag method
// 
JSONscriptRequest.prototype.removeScriptTag = function () {
    // Destroy the script tag
    this.headLoc.removeChild(this.scriptObj);  
}

// addScriptTag method
//
JSONscriptRequest.prototype.addScriptTag = function () {
    // Create the script tag
    this.headLoc.appendChild(this.scriptObj);
}

/*************************************************************************
 * orderText - orders the passage Text from eBible.com via JSON script request
 *************************************************************************/
function orderText(request) {
  // Dispatch the request
  aObj = new JSONscriptRequest(request);
  // Build the script tag
  aObj.buildScriptTag();
  // Execute (add) the script tag
  aObj.addScriptTag();

}


/* Created by Scott Luedtke Mar30 2006 

purpose: Used to show a preview of a verse as a tooltip when a user mouses-over a verse hyperlink

requirements:
  - The event target to be a 'A' object
  - The 'A' object to have 1 of the following:
      1, A name attribute with the contents being a valid verse string that will be used for an ajax verse lookup
      2, An 'HREF' attribute with one of the HTML parameters being called 'query'. This parameter will then
         be used for an ajax verse lookup

*/


 var offsetfrommouse=[10,10]; // x,y offsets from cursor position in pixels. Enter 0,0 for no offset

 var popupWidth = 275;
 var popupMaxHeight = 500;
 
 var currentVisiblePopup = null;
 var currentimageheight = 10;
 
 var popDelay = 800; //time until popup appears
 var delayID = null;
 var mouseX;
 var mouseY;

function truebody(){
  return (!window.opera && document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
}

/*************************************************************************
 * onShowVersePopup - attached to verse elements as ONMOUSEOVER            
 *************************************************************************/
function onShowVersePopup(e){
	e?e=e:e=event;

  // hide current visible popup
  if (currentVisiblePopup != null)
    onHideVersePopup(null);

  mouseX=e.pageX?e.pageX:e.clientX + document.body.scrollLeft;
  mouseY=e.pageY?e.pageY:e.clientY + document.body.scrollTop;
  var toggle = eventTarget(e); 
  var passage = getPassageFromTarget(toggle) 
  var id = passage + "-popup";

  //first we need to check if the div exists
  //If it doesn't, then we need to create it

  clearTimeout(delayID);
  currentVisiblePopup = id;

  if (!document.getElementById(id)){
    delayID=window.setTimeout("createPopup('" + id + "','" + passage + "')",popDelay);
  }
  else{
    //the div already exists, we just need to position it and show it
    delayID=window.setTimeout("showPopup('" + id + "')",popDelay);
  }
}

/*************************************************************************
 * onHideVersePopup - attached to verse elements as ONMOUSEOUT            
 *************************************************************************/
function onHideVersePopup(e){
  var popup = document.getElementById(currentVisiblePopup);

  clearTimeout(delayID); //cancel the timeout delay for the popup so it doesnt appear after we hide it.
  
  if (popup){
    popup.style.visibility = 'hidden';
    currentVisiblePopup = null;
		popup.style.opacity='1'; // Firefox
  }
}

/*************************************************************************
 * getPassageFromTarget - extracts passage from query param in HREF            
 *************************************************************************/
function getPassageFromTarget(toggle){
 var id = ""
 if(toggle.tagName != "A"){
   parentAnchor = findAncestorByTag(toggle,"a")
   toggle = parentAnchor
  }
 if(toggle.name && toggle.name != null){ //perfect, we have a verse string in the name attribute
    id = toggle.name
    //alert(toggle.name)
  }else{   //this is where we grab the url parameter 'query'
    if (toggle.href){
      //alert(toggle.href)
      var index = toggle.href.lastIndexOf("/");
      id = toggle.href.substring(index + 1);
    }
  }
 //alert(id)
 return id;
}

/*************************************************************************
 * createPopup - creates a popup element with spinner and makes JSON call            
 *************************************************************************/
function createPopup(id, passage){

  var popup = document.createElement('div');
  popup.id = id;
  
  popup.style.visibility="hidden"
  popup.style.position="absolute";
  //popup.style.border='2px solid #C8BA92';
  popup.style.fontFamily='arial';
  popup.style.fontSize='11';
  popup.style.padding='0';
  popup.style.zIndex='5';
  popup.style.color='#777';
  popup.style.background='#FFFFFF';
  popup.style.filter='alpha(opacity=9S9)'; // IE
  popup.style.opacity='0.99'; // Firefox
  
  popup.style.width=popupWidth + 'px';
  popup.style.maxHeight = popupMaxHeight + 'px';
  
  document.body.appendChild(popup);

  showPopup(id);

  var popup_body = document.createElement('div');
  popup_body.id = passage + "-popup-body";
  var waitMessage = (KEY == 'EBIBLE_DEMO') ? "Loading passage..." : "Fetching from eBible.com...";
  popup_body.innerHTML = "<img src='http://8.7.217.25/images/spinner.gif' /> " + waitMessage;
  popup_body.className = "versePreviewBody";

  popup.appendChild(popup_body);
  //posPop(id);

  // Order the passage text via JSON
  request = EBIBLE_SNIPPET_URL + "?apiKey=" + KEY + "&q=" + passage + "&source=" + escape(eBibleicious.translation) + "&callback=receivedText4Popup&relTopics=" + eBibleicious.related_topics;
  orderText(request); 
}

/*************************************************************************
 * showPopup - positions and sets visible a popup by element id            
 *************************************************************************/
function showPopup(id){
  posPop(id);
  document.getElementById(id).style.visibility = "visible";
}

/*************************************************************************
 * posPop - positions a popup element            
 *************************************************************************/
function posPop(ele){
  ele = document.getElementById(ele)

  var xcoord=offsetfrommouse[0];
  var ycoord=offsetfrommouse[1];

  var docwidth=document.all? truebody().scrollLeft+truebody().clientWidth : pageXOffset+window.innerWidth-15
  var docheight=document.all? Math.min(truebody().scrollHeight, truebody().clientHeight) : Math.min(window.innerHeight)

    if (docwidth - mouseX < ele.offsetWidth + 30){
      xcoord = mouseX - xcoord - ele.offsetWidth - 30; // Move to the left side of the cursor
    } else {
      xcoord += mouseX;
    }

  if (docheight - mouseY < ele.offsetHeight + 30){
    //ycoord += mouseY - Math.max(0,(ele.offsetHeight + 30 + mouseY - docheight - truebody().scrollTop));
    ycoord += document.all? mouseY+ truebody().scrollTop - Math.max(0,(ele.offsetHeight + 30 + mouseY - docheight)) : mouseY - Math.max(0,(ele.offsetHeight + 30 + mouseY - docheight - truebody().scrollTop));
  } else {

    ycoord += mouseY;
    if(document.all){ //if IE we need to add the scroll offset
     ycoord += truebody().scrollTop
    }
  }

  if(document.all){//if IE we need to add the scroll offset
    //ycoord += truebody().scrollTop
   // xcoord += truebody().scrollLeft
  }
  if(ycoord < 0) { ycoord = ycoord*-1; }

  //alert(document.getElementById(currentVisiblePopup));
  //alert('left:' + mouseX + " top:" + mouseY);
  //alert(ele.style.left);
  ele.style.left=xcoord+"px";
  ele.style.top=ycoord+"px";

}

/*************************************************************************
 * receivedText4Popup - the JSON request call back that populates the popup            
 *************************************************************************/
function receivedText4Popup(verses) {   
  document.getElementById(currentVisiblePopup).innerHTML = verses[0].text;
  posPop(currentVisiblePopup); 
}


/******************************************************************************
  eBibleicious - Dynamically link or include Bible verses with one line of Javascript

  Copyright (c) 2006, Godspeed Computing Inc. 
  www.godspeedcomputing.com
  All Rights Reserved.

  Authors: Scott Luedtke, Mark Sears
  Date: April 20 2007
  
  Note: Special thanks to Scott Yang <scotty@yang.id.au> for his work on
  Scripturizer, parts of which are used here.

******************************************************************************/

/*************************************************************
** ONLOAD SCRIPT - adapted from the window.onload script
**    written by Dean Edwards - http://dean.edwards.name
**************************************************************/
function init() {
  // quit if this function has already been called
  if (arguments.callee.done) return;

  // flag this function so we don't do the same thing twice
  arguments.callee.done = true;

  // execute these items when page is loaded
  ebdInit();
};

/* for Mozilla */
if (document.addEventListener) {
  document.addEventListener("DOMContentLoaded", init, false);
}

/* for Internet Explorer */
/*@cc_on @*/
/*@if (@_win32) 
  document.write("<script id=__ie_onload defer src=javascript:void(0)></script>");
  var script = document.getElementById("__ie_onload");
  script.onreadystatechange = function() {
      if (this.readyState == "complete") {
          init(); // call the onload handler
      }
  };
/*@end @*/

/* for Safari */
if (/WebKit/i.test(navigator.userAgent)) {  // sniff
  var _timer = setInterval(function() {
      if (/loaded|complete/.test(document.readyState)) {
          clearInterval(_timer);
          init();  // call the onload handler
      }
  }, 10);
}

/* for other browsers */
window.onload = init;
/************************************************************
** END ONLOAD SCRIPT
*************************************************************/

/*************************************************************************
 * Configuration section - These options can be configured as parameters in the script include tag
 *************************************************************************/
var eBibleicious = {
    /**
     * Mode of eBibleicious can be either:
     *     'link' - hyperlinks all passage references to eBible.com "Quick View" page
     *     'study' - hyperlinks all passage references to eBible.com "Study Browser"
     *     'mouseover' - same as 'link' but also displays the passage inline when moused over
     *     'snippet' - dynamically replaces all passage references with a snippet div including the actual passage text
     */  
    mode: 'mouseover',  

    /**
     * Translation of Bible to be used.
     */  
    translation: 'NIV',

    /**
     * CSS classname to use in denoting passages.
     */  
    class_name: 'ebdPassage',

    /**
     * Number of related topics to include. This option only applies for mouseover and snippet modes.
     */  
    related_topics: 5,

    /**
     * The document element ID used by findPassages(). If it is
     * empty, or the element cannot be found, then document.body will be used,
     * i.e. the entire document will be passed through eBibleicious.
     */
    element: '',

    /**
     * Maximum number of DOM text nodes to process before handing the event
     * thread back to GUI and wait for the next round. Smaller value leaders
     * to more responsive UI, but slower to finish parsing.
     */
    max_nodes: 500,

    /**
     * Whether a link will open in a new window. This option only applies for link mode.
     */
    new_window: false
    
};

/*************************************************************************
 * Global array of passages found in this document
 *************************************************************************/
var ebdSnippets = [];

/*************************************************************************
 * ebdInit - Pulls configuration settings from the javascript include tag 
 *           and initializes everything. 
 *************************************************************************/
function ebdInit() {
    // parse out options from include tag
    var es = document.getElementsByTagName('script');
    for (i = 0; i < es.length; i ++) {
        var j, p;
        if ((j = es[i].src.indexOf('ebibleicious')) >= 0) {
            p = decodeQS(es[i].src);           
            
            if (p.element)
                eBibleicious.element = p.element;
            if (p.new_window)
                eBibleicious.new_window = p.new_window == '1';
            if (p.translation)
                eBibleicious.translation = p.translation;
            if (p.mode)
                eBibleicious.mode = p.mode;
            if (p.related_topics)
                eBibleicious.related_topics = p.related_topics;    
            if (p.class_name)
                eBibleicious.class_name = p.class_name;
            break;
        }
    }

    ebdLoad();
}

function ebdLoad() {
    if ((eBibleicious.element && (e = document.getElementById(eBibleicious.element))) || (e = document.body)) {
        // Get all passages by parsing for references and mark them with a class name
        findPassages(e, eBibleicious.class_name)    
    }

    if (eBibleicious.mode == "snippet") {
        // Get all passages specified with a class name
        ebdSnippets = findDescendantsByClass(e, eBibleicious.class_name);
        var passages = [];  
        if (ebdSnippets.length > 0){
          for (var i=0; i < ebdSnippets.length; i++) {
            var element = ebdSnippets[i];
            passages[i] = escape(element.innerHTML);
          }
          query= passages.join(",")
          request = EBIBLE_SNIPPET_URL + "?apiKey=" + KEY + "&q=" + query + "&source=" + escape(eBibleicious.translation) + "&callback=receivedText4Snippet&relTopics=" + eBibleicious.related_topics;
          orderText(request); 
        }              
    }
}


/*************************************************************************
 * findPassages - Find passage references in a DOM element and mark them 
 *                with the specified class_name. 
 *************************************************************************/
function findPassages(elm, class_name) {
    var vol = 'I+|1st|2nd|3rd|First|Second|Third|1|2|3';
    var bok = 'Genesis|Gen?|Gen\.?|Exodus|Exod?|Exod\.?|Ex\.?|Leviticus|Lev\.?|Levit\.?|' +
		'Numbers|Nmb\.?|Num\.?|Deuteronomy|Deu\.?|Deut\.?|Dt\.?|Joshua|Josh?|Josh\.?|Jsh\.?|' +
		'Judges|Jdg\.?|Judg\.?|Ruth|Samuel|Sam\.?|Sml\.?|Kings|Kn?gs\.?|Kin\.?|' +
		'Chronicles|Chr\.?|Chron\.?|Ezra|Nehemiah|Nehem\.?|Neh\.?|Esther|Esth\.?|Est\.?|Job|Psalms?|Psa\.?|Ps\.?|' +
		'Proverbs?|Prov\.?|Pr\.?|Ecclesiastes|Eccl\.?|Eccl?|Songs? of Solomon|Song\.?|' +
		'Isaiah|Isa\.?|Jeremiah|Jer\.?|Jerem\.?|Lamentations|' +
		'Lam\.?|Lament?|Lament\.?|Ezekiel|Ezek\.?|Daniel|Dan\.?|Hosea|Hos\.?|Joel|' +
		'Jo\.?|Amos|Obadiah|Obad\.?|Jonah|Jon\.?|Micah|Mic\.?|Nahum|Nah\.?|' +
		'Habakkuk|Hab?\.?|Habak\.?|Zephaniah|Zeph?\.?|Zph\.?|Haggai|Hag\.?|Hagg\.?|Zechariah|Zech?|' +
		'Malachi|Malac\.?|Mal\.?|Mat{1,2}hew|Mat\.?|Mat?|Mt\.?|Mark|Mrk\.?|Mk\.?|Luke?|Lk\.?|John|' +
		'Jh?n\.?|Acts?|Ac\.?|Romans|Rom\.?|Corinthians|Cor\.?|Corin\.?|Galatians|Gal\.?|Galat\.?|Ephesians|Eph\.?|Ephes\.?|' +
		'Philippians|Phili?|Phil\.?|Php|Colossians|Col\.?|Colos\.?|Thessalonians|Thes\.?|Timothy|Ti?m\.?|' +
		'Titus|Tts\.?|Tit\.?|Philemon|Phl?m\.?|Hebrews|Hebr\.?|Heb\.?|James|Jam\.?|Jas\.?|Jms\.?|' +
		'Peter|Pet\.?|Pt\.?|Jude|Ju\.?|Revelations?|Rv\.?';
    var ver = '\\d+(:\\d+)?(?:\\s?[-&]\\s?\\d+)?';
    var regex = '\\b(?:('+vol+')\\s+)?('+bok+')\\s+('+ver+'(?:\\s?[,]\\s?'+
        ver+')*)\\b';

    regex = new RegExp(regex, "m");

    var textproc = function(node) {
        var match = regex.exec(node.data);
        if (match) {
            var val = match[0];
            var node2 = node.splitText(match.index);
            var node3 = node2.splitText(val.length);
            var anchor;
            if (eBibleicious.mode == 'snippet') {
                anchor = node.ownerDocument.createElement('DIV');
            } else {
                anchor = node.ownerDocument.createElement('A');
                anchor.setAttribute('href', 'javascript://');
                addEBEvent(anchor, 'click', eBibleicious.onclick, false);
                //anchor.onclick = eBibleicious.onclick;
                if (eBibleicious.mode == 'mouseover') {
                    anchor.name = val;
                    addEBEvent(anchor, 'mouseover', onShowVersePopup, false);
                    addEBEvent(document, 'mouseup', onHideVersePopup, true);             
                } else {
                    addEBEvent(anchor, 'mouseover', eBibleicious.showTitle, false);
                    //anchor.mouseover = eBibleicious.showTitle;
                }
            }
            node.parentNode.replaceChild(anchor, node2);
            anchor.className = class_name;
            anchor.appendChild(node2);
            return anchor;
        } else {
            return node;
        }
    };

    traverseDOM(elm.childNodes[0], 1, textproc);
};

/*************************************************************************
 * traverseDOM - Traverses the DOM applying the regex
 *************************************************************************/

function traverseDOM(node, depth, textproc) {
	var skipre = /^(a|script|style|textarea|h1|h2|h3|h4|h5|h6|h7|h8)/i;
	var count = 0;
	while (node && depth > 0) {
		count ++;
		if (count >= eBibleicious.max_nodes) {
			var handler = function() {
				traverseDOM(node, depth, textproc);
			};
			setTimeout(handler, 50);
			return;
		}

		try {
			switch (node.nodeType) {
				case 1: // ELEMENT_NODE
				if (!skipre.test(node.tagName) && node.childNodes.length > 0) {
					node = node.childNodes[0];
					depth ++;
					continue;
				}
				break;
				case 3: // TEXT_NODE
				case 4: // CDATA_SECTION_NODE
				node = textproc(node);
				break;
			}

			if (node.nextSibling) {
				node = node.nextSibling;
			} else {
				while (depth > 0) {
					node = node.parentNode;
					depth --;
					if (node.nextSibling) {
						node = node.nextSibling;
						break;
					}
				}
			}
		} 
		catch(e) {
			return;
		}
	}
}

/*************************************************************************
 * decodeQS - Decode the query string to find configuration settings
 *************************************************************************/
function decodeQS(qs) {
    var k, v, i1, i2, r = {};
    i1 = qs.indexOf('?');
    i1 = i1 < 0 ? 0 : i1 + 1;
    while ((i1 >= 0) && ((i2 = qs.indexOf('=', i1)) >= 0)) {
        k = qs.substring(i1, i2);
        i1 = qs.indexOf('&', i2);
        v = i1 < 0 ? qs.substring(i2+1) : qs.substring(i2+1, i1++);
        r[unescape(k)] = unescape(v);
				if(unescape(k) == 'key') {
					KEY = unescape(v);
				}
    }
    return r;
}

/*************************************************************************
 * onclick - onclick function applied to passage links
 *************************************************************************/
eBibleicious.onclick = function(ev) {
    ev = ev || window.event;
    var verse = eventTarget(ev).childNodes[0].data;

    var link = verse.replace(/ /g, '+');
    link = link.replace(/[,&;]/g, '%2C');
    link = link.replace(/:]/g, '%3A');
    
    if (eBibleicious.mode == 'link' || eBibleicious.mode == 'mouseover')
        link = 'http://www.ebible.com/bible/'+eBibleicious.translation+'/' + link;
    else if (eBibleicious.mode == 'study')
        link = 'http://www.ebible.com/bible/study/'+eBibleicious.translation+'/' + link;
        
    if (eBibleicious.new_window)
        window.open(link);
    else
        window.location.href = link;

    return false;
};

/*************************************************************************
 * showTitle - showTitle function applied to passage links
 *************************************************************************/
eBibleicious.showTitle = function(ev) {

    switch (eBibleicious.mode) {
        case 'link':
            title = 'View ' + eventTarget(ev).childNodes[0].data + ' - ' + eBibleicious.translation + ' from eBible.com';
            break;
        case 'study':
            title = 'Study ' + eventTarget(ev).childNodes[0].data + ' - ' + eBibleicious.translation + ' from eBible.com';
            break;
        default:
            return;
    }

    if (eBibleicious.new_window)
        title += ' (new window)';

    eventTarget(ev).setAttribute('title', title);
};

/*************************************************************************
 * receivedText4Snippet - callback function after JSON script request that
 *                replaces the passage references with the ordered Text
 *************************************************************************/
function receivedText4Snippet(verses) {
  if (verses.length > 0){
      for (var i=0; i < verses.length; i++) {
          var newElement;
          newElement = ebdSnippets[i].ownerDocument.createElement('DIV');
          newElement.innerHTML = verses[i].text;
          ebdSnippets[i].parentNode.replaceChild(newElement, ebdSnippets[i]);
      }
  }  
}
