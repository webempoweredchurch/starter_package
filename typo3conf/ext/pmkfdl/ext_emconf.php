<?php

########################################################################
# Extension Manager/Repository config file for ext "pmkfdl".
#
# Auto generated 19-02-2010 19:36
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'PMK Forced Download',
	'description' => 'Makes it possible to force download of files like images, PDFs, MP3 ect., overriding the browser settings. Can create secure download links based on usergroup access. Extends stdWrap/typolink.',
	'category' => 'fe',
	'shy' => 0,
	'version' => '0.4.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Peter Klein',
	'author_email' => 'pmk@io.dk',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.0.0-0.0.0',
			'php' => '5.0.0-0.0.0',
		),
		'conflicts' => array(
			'alternet_securelink' => '0.0.0-0.0.0',
			'bzb_securelink' => '0.0.0-0.0.0',
			'securelinks' => '0.0.0-0.0.0',
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:100:{s:9:"ChangeLog";s:4:"7c28";s:10:"README.txt";s:4:"ee2d";s:19:"class.tx_pmkfdl.php";s:4:"473d";s:28:"class.tx_pmkfdl_download.php";s:4:"e7b2";s:24:"class.tx_pmkfdl_hook.php";s:4:"6f4a";s:21:"ext_conf_template.txt";s:4:"de7e";s:12:"ext_icon.gif";s:4:"84d7";s:17:"ext_localconf.php";s:4:"f17a";s:15:"ext_php_api.dat";s:4:"543b";s:14:"ext_tables.php";s:4:"e37a";s:13:"mimetypes.php";s:4:"9686";s:14:"doc/manual.sxw";s:4:"7f52";s:36:"res/class.tx_pmkfdl_process_hook.php";s:4:"fe5b";s:42:"res/class.tx_pmkfdl_ttnews_filemarkers.php";s:4:"fa5b";s:20:"res/fileicons/7z.png";s:4:"62db";s:25:"res/fileicons/README..txt";s:4:"df64";s:23:"res/fileicons/Thumbs.db";s:4:"2478";s:20:"res/fileicons/ai.png";s:4:"fcc6";s:22:"res/fileicons/aiff.png";s:4:"65bd";s:21:"res/fileicons/asc.png";s:4:"06c2";s:23:"res/fileicons/audio.png";s:4:"80b9";s:21:"res/fileicons/bin.png";s:4:"a86a";s:21:"res/fileicons/bz2.png";s:4:"e27b";s:19:"res/fileicons/c.png";s:4:"bb56";s:21:"res/fileicons/chm.png";s:4:"b4c7";s:23:"res/fileicons/class.png";s:4:"400d";s:22:"res/fileicons/conf.png";s:4:"da4d";s:21:"res/fileicons/cpp.png";s:4:"f648";s:21:"res/fileicons/css.png";s:4:"0d8d";s:21:"res/fileicons/csv.png";s:4:"704f";s:21:"res/fileicons/deb.png";s:4:"3e17";s:22:"res/fileicons/divx.png";s:4:"2844";s:21:"res/fileicons/doc.png";s:4:"9104";s:21:"res/fileicons/dot.png";s:4:"9f00";s:21:"res/fileicons/eml.png";s:4:"1d10";s:21:"res/fileicons/enc.png";s:4:"3c3b";s:22:"res/fileicons/file.png";s:4:"b840";s:21:"res/fileicons/gif.png";s:4:"1d5e";s:20:"res/fileicons/gz.png";s:4:"f831";s:21:"res/fileicons/hlp.png";s:4:"9896";s:21:"res/fileicons/htm.png";s:4:"53f8";s:22:"res/fileicons/html.png";s:4:"9fb3";s:23:"res/fileicons/image.png";s:4:"02cc";s:21:"res/fileicons/iso.png";s:4:"3fc8";s:21:"res/fileicons/jar.png";s:4:"2c3e";s:22:"res/fileicons/java.png";s:4:"2a77";s:22:"res/fileicons/jpeg.png";s:4:"633a";s:21:"res/fileicons/jpg.png";s:4:"51b5";s:20:"res/fileicons/js.png";s:4:"af53";s:19:"res/fileicons/m.png";s:4:"cb39";s:20:"res/fileicons/mm.png";s:4:"7c6a";s:21:"res/fileicons/mov.png";s:4:"b8a2";s:21:"res/fileicons/mp3.png";s:4:"1579";s:21:"res/fileicons/mpg.png";s:4:"2dd7";s:21:"res/fileicons/odc.png";s:4:"8243";s:21:"res/fileicons/odf.png";s:4:"382f";s:21:"res/fileicons/odg.png";s:4:"6278";s:21:"res/fileicons/odi.png";s:4:"56f6";s:21:"res/fileicons/odp.png";s:4:"fb64";s:21:"res/fileicons/ods.png";s:4:"ef34";s:21:"res/fileicons/odt.png";s:4:"26e1";s:21:"res/fileicons/ogg.png";s:4:"3f5c";s:21:"res/fileicons/pdf.png";s:4:"bd52";s:21:"res/fileicons/pgp.png";s:4:"a1d7";s:21:"res/fileicons/php.png";s:4:"61a7";s:20:"res/fileicons/pl.png";s:4:"1249";s:21:"res/fileicons/png.png";s:4:"2702";s:21:"res/fileicons/ppt.png";s:4:"ab47";s:20:"res/fileicons/ps.png";s:4:"50af";s:20:"res/fileicons/py.png";s:4:"b79f";s:21:"res/fileicons/ram.png";s:4:"3ac5";s:21:"res/fileicons/rar.png";s:4:"d339";s:20:"res/fileicons/rb.png";s:4:"840c";s:20:"res/fileicons/rm.png";s:4:"4910";s:21:"res/fileicons/rpm.png";s:4:"2afe";s:21:"res/fileicons/rtf.png";s:4:"29f5";s:21:"res/fileicons/sig.png";s:4:"2e3a";s:21:"res/fileicons/sql.png";s:4:"5155";s:21:"res/fileicons/swf.png";s:4:"9b0d";s:21:"res/fileicons/sxc.png";s:4:"4601";s:21:"res/fileicons/sxd.png";s:4:"7cd7";s:21:"res/fileicons/sxi.png";s:4:"35fc";s:21:"res/fileicons/sxw.png";s:4:"2adf";s:21:"res/fileicons/tar.png";s:4:"e449";s:21:"res/fileicons/tex.png";s:4:"93df";s:21:"res/fileicons/tgz.png";s:4:"3cc5";s:21:"res/fileicons/txt.png";s:4:"3dbb";s:21:"res/fileicons/vcf.png";s:4:"0f56";s:23:"res/fileicons/video.png";s:4:"68e9";s:21:"res/fileicons/vsd.png";s:4:"fd65";s:21:"res/fileicons/wav.png";s:4:"bf18";s:21:"res/fileicons/wma.png";s:4:"28cb";s:21:"res/fileicons/wmv.png";s:4:"2bf7";s:21:"res/fileicons/xls.png";s:4:"7096";s:21:"res/fileicons/xml.png";s:4:"0877";s:21:"res/fileicons/xpi.png";s:4:"e437";s:22:"res/fileicons/xvid.png";s:4:"bdd4";s:21:"res/fileicons/zip.png";s:4:"c3cc";s:27:"static/pmkfdl/constants.txt";s:4:"c01e";s:23:"static/pmkfdl/setup.txt";s:4:"6132";}',
);

?>