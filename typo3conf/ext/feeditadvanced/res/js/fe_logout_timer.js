/**
 * Initialize login expiration warning object
 */
var busy = new busy();
busy.loginRefreshed();
busy_checkLoginTimeout_timer();

/**
 * TypoSetup object.
 */
function typoSetup()	{	//
	this.PATH_typo3 = "'.$pathTYPO3.'";
	this.PATH_typo3_enc = "'.rawurlencode($pathTYPO3).'";
	this.username = "'.$GLOBALS['BE_USER']->user['username'].'";
	this.uniqueID = "'.t3lib_div::shortMD5(uniqid('')).'";
	this.navFrameWidth = 0;
}
var TS = new typoSetup();

/**
 * Functions for session-expiry detection:
 */
function busy()	{	//
	this.loginRefreshed = busy_loginRefreshed;
	this.checkLoginTimeout = busy_checkLoginTimeout;
	this.openRefreshWindow = busy_OpenRefreshWindow;
	this.busyloadTime=0;
	this.openRefreshW=0;
	this.reloginCancelled=0;
}
function busy_loginRefreshed()	{	//
	var date = new Date();
	this.busyloadTime = Math.floor(date.getTime()/1000);
	this.openRefreshW=0;
}
function busy_checkLoginTimeout()	{	//
	var date = new Date();
	var theTime = Math.floor(date.getTime()/1000);
//	if (theTime > this.busyloadTime+'.intval($GLOBALS['BE_USER']->auth_timeout_field).'-30)	{
//		return true;
//	}
}
function busy_OpenRefreshWindow()	{	//
	vHWin=window.open("login_frameset.php","relogin_"+TS.uniqueID,"height=350,width=700,status=0,menubar=0,location=1");
	vHWin.focus();
	this.openRefreshW=1;
}
function busy_checkLoginTimeout_timer()	{	//
	if (busy.checkLoginTimeout() && !busy.reloginCancelled && !busy.openRefreshW)	{
//		if (confirm('.$GLOBALS['LANG']->JScharCode($GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:mess.refresh_login')).'))	{
			busy.openRefreshWindow();
//		} else	{
//			busy.reloginCancelled = 1;
//		}
	}
	window.setTimeout("busy_checkLoginTimeout_timer();",2*1000);	// Each 2nd second is enough for checking. The popup will be triggered 10 seconds before the login expires (see above, busy_checkLoginTimeout())

		// Detecting the frameset module navigation frame widths (do this AFTER setting new timeout so that any errors in the code below does not prevent another time to be set!)
//	if (top && top.content && top.content.nav_frame && top.content.nav_frame.document && top.content.nav_frame.document.body)	{
//		TS.navFrameWidth = (top.content.nav_frame.document.documentElement && top.content.nav_frame.document.documentElement.clientWidth) ? top.content.nav_frame.document.documentElement.clientWidth : top.content.nav_frame.document.body.clientWidth;
//	}
}
