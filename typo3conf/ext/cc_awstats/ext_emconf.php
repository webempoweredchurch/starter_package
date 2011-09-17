<?php

########################################################################
# Extension Manager/Repository config file for ext: "cc_awstats"
#
# Auto generated 23-08-2006 14:52
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'AWStats',
	'description' => 'Includes the AWStats logfile analyzer as a backend module.',
	'category' => 'module',
	'shy' => 0,
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod1',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author' => 'René Fritz',
	'author_email' => 'r.fritz@colorcube.de',
	'author_company' => 'Colorcube - digital media lab, www.colorcube.de',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'version' => '0.10.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '3.0.0-',
			'php' => '3.0.0-',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:552:{s:12:"ext_icon.gif";s:4:"706e";s:14:"ext_tables.php";s:4:"49dd";s:20:"awstats/awstats.conf";s:4:"0dd0";s:26:"awstats/awstats.model.conf";s:4:"3ba8";s:18:"awstats/awstats.pl";s:4:"99b2";s:33:"awstats/classes/awgraphapplet.jar";s:4:"5174";s:38:"awstats/classes/src/AWGraphApplet.java";s:4:"c1e4";s:31:"awstats/classes/src/Makefile.pl";s:4:"bb1c";s:26:"awstats/css/awstats_bw.css";s:4:"7f33";s:31:"awstats/css/awstats_default.css";s:4:"c3cd";s:30:"awstats/icon/browser/adobe.png";s:4:"ab77";s:30:"awstats/icon/browser/amaya.png";s:4:"af65";s:37:"awstats/icon/browser/amigavoyager.png";s:4:"3c7f";s:28:"awstats/icon/browser/apt.png";s:4:"6dc1";s:30:"awstats/icon/browser/avant.png";s:4:"c9fa";s:29:"awstats/icon/browser/aweb.png";s:4:"f32c";s:30:"awstats/icon/browser/bpftp.png";s:4:"55e3";s:32:"awstats/icon/browser/chimera.png";s:4:"4a71";s:33:"awstats/icon/browser/cyberdog.png";s:4:"55c6";s:27:"awstats/icon/browser/da.png";s:4:"e8bc";s:30:"awstats/icon/browser/dillo.png";s:4:"182d";s:34:"awstats/icon/browser/dreamcast.png";s:4:"8a60";s:31:"awstats/icon/browser/ecatch.png";s:4:"c407";s:34:"awstats/icon/browser/encompass.png";s:4:"ae43";s:32:"awstats/icon/browser/firefox.png";s:4:"a8f7";s:34:"awstats/icon/browser/fpexpress.png";s:4:"51bc";s:31:"awstats/icon/browser/fresco.png";s:4:"b233";s:31:"awstats/icon/browser/galeon.png";s:4:"df55";s:33:"awstats/icon/browser/getright.png";s:4:"2b3c";s:32:"awstats/icon/browser/gozilla.png";s:4:"c7ee";s:32:"awstats/icon/browser/hotjava.png";s:4:"3c2a";s:32:"awstats/icon/browser/ibrowse.png";s:4:"f2fa";s:29:"awstats/icon/browser/icab.png";s:4:"5e3c";s:32:"awstats/icon/browser/kmeleon.png";s:4:"a9c9";s:34:"awstats/icon/browser/konqueror.png";s:4:"87d4";s:35:"awstats/icon/browser/lotusnotes.png";s:4:"2735";s:29:"awstats/icon/browser/lynx.png";s:4:"28b0";s:31:"awstats/icon/browser/macweb.png";s:4:"1b29";s:36:"awstats/icon/browser/mediaplayer.png";s:4:"7d89";s:32:"awstats/icon/browser/mozilla.png";s:4:"83e4";s:29:"awstats/icon/browser/msie.png";s:4:"4054";s:35:"awstats/icon/browser/msie_large.png";s:4:"aa1b";s:35:"awstats/icon/browser/multizilla.png";s:4:"5802";s:36:"awstats/icon/browser/ncsa_mosaic.png";s:4:"ba9f";s:36:"awstats/icon/browser/netpositive.png";s:4:"4748";s:33:"awstats/icon/browser/netscape.png";s:4:"d378";s:39:"awstats/icon/browser/netscape_large.png";s:4:"9c64";s:37:"awstats/icon/browser/notavailable.png";s:4:"925c";s:32:"awstats/icon/browser/omniweb.png";s:4:"7f26";s:30:"awstats/icon/browser/opera.png";s:4:"daa3";s:33:"awstats/icon/browser/pdaphone.png";s:4:"cbd9";s:32:"awstats/icon/browser/phoenix.png";s:4:"5ef5";s:28:"awstats/icon/browser/rss.png";s:4:"376b";s:31:"awstats/icon/browser/safari.png";s:4:"d62f";s:35:"awstats/icon/browser/staroffice.png";s:4:"cd43";s:33:"awstats/icon/browser/teleport.png";s:4:"295b";s:32:"awstats/icon/browser/unknown.png";s:4:"8f1b";s:34:"awstats/icon/browser/webcopier.png";s:4:"585e";s:30:"awstats/icon/browser/webtv.png";s:4:"dcc4";s:31:"awstats/icon/browser/webzip.png";s:4:"af2e";s:26:"awstats/icon/clock/hr1.png";s:4:"ecc2";s:27:"awstats/icon/clock/hr10.png";s:4:"22b6";s:27:"awstats/icon/clock/hr11.png";s:4:"04e6";s:27:"awstats/icon/clock/hr12.png";s:4:"0c30";s:26:"awstats/icon/clock/hr2.png";s:4:"713d";s:26:"awstats/icon/clock/hr3.png";s:4:"4d43";s:26:"awstats/icon/clock/hr4.png";s:4:"926d";s:26:"awstats/icon/clock/hr5.png";s:4:"4ac3";s:26:"awstats/icon/clock/hr6.png";s:4:"e95a";s:26:"awstats/icon/clock/hr7.png";s:4:"8031";s:26:"awstats/icon/clock/hr8.png";s:4:"94b8";s:26:"awstats/icon/clock/hr9.png";s:4:"5f28";s:28:"awstats/icon/cpu/digital.png";s:4:"de01";s:23:"awstats/icon/cpu/hp.png";s:4:"f8f9";s:24:"awstats/icon/cpu/ibm.png";s:4:"0a4f";s:26:"awstats/icon/cpu/intel.png";s:4:"26c0";s:25:"awstats/icon/cpu/java.png";s:4:"4a81";s:25:"awstats/icon/cpu/mips.png";s:4:"552d";s:29:"awstats/icon/cpu/motorola.png";s:4:"08a2";s:24:"awstats/icon/cpu/sun.png";s:4:"aba0";s:28:"awstats/icon/cpu/unknown.png";s:4:"16a3";s:25:"awstats/icon/flags/a2.png";s:4:"d2a7";s:25:"awstats/icon/flags/ac.png";s:4:"13c4";s:25:"awstats/icon/flags/ad.png";s:4:"8cb9";s:25:"awstats/icon/flags/ae.png";s:4:"1c2a";s:27:"awstats/icon/flags/aero.png";s:4:"22bf";s:25:"awstats/icon/flags/af.png";s:4:"d6df";s:25:"awstats/icon/flags/ag.png";s:4:"3f5e";s:25:"awstats/icon/flags/ai.png";s:4:"8c59";s:25:"awstats/icon/flags/al.png";s:4:"7cc9";s:25:"awstats/icon/flags/am.png";s:4:"a98d";s:25:"awstats/icon/flags/an.png";s:4:"c26c";s:25:"awstats/icon/flags/ao.png";s:4:"10bd";s:25:"awstats/icon/flags/aq.png";s:4:"c6c5";s:25:"awstats/icon/flags/ar.png";s:4:"53b8";s:27:"awstats/icon/flags/arpa.png";s:4:"f65c";s:25:"awstats/icon/flags/as.png";s:4:"431d";s:25:"awstats/icon/flags/at.png";s:4:"17cf";s:25:"awstats/icon/flags/au.png";s:4:"6bf4";s:25:"awstats/icon/flags/aw.png";s:4:"7fdb";s:25:"awstats/icon/flags/az.png";s:4:"77d5";s:25:"awstats/icon/flags/ba.png";s:4:"d1b1";s:25:"awstats/icon/flags/bb.png";s:4:"8901";s:25:"awstats/icon/flags/bd.png";s:4:"13a1";s:25:"awstats/icon/flags/be.png";s:4:"5efc";s:25:"awstats/icon/flags/bf.png";s:4:"8c57";s:25:"awstats/icon/flags/bg.png";s:4:"5b89";s:25:"awstats/icon/flags/bh.png";s:4:"7f1d";s:25:"awstats/icon/flags/bi.png";s:4:"7625";s:26:"awstats/icon/flags/biz.png";s:4:"ff7b";s:25:"awstats/icon/flags/bj.png";s:4:"dc2f";s:25:"awstats/icon/flags/bm.png";s:4:"e87c";s:25:"awstats/icon/flags/bn.png";s:4:"5e54";s:25:"awstats/icon/flags/bo.png";s:4:"bd54";s:25:"awstats/icon/flags/br.png";s:4:"fdfa";s:25:"awstats/icon/flags/bs.png";s:4:"a8b0";s:25:"awstats/icon/flags/bt.png";s:4:"2c07";s:25:"awstats/icon/flags/bv.png";s:4:"e2af";s:25:"awstats/icon/flags/bw.png";s:4:"3225";s:25:"awstats/icon/flags/by.png";s:4:"b38b";s:25:"awstats/icon/flags/bz.png";s:4:"9e2c";s:25:"awstats/icon/flags/ca.png";s:4:"1af8";s:25:"awstats/icon/flags/cc.png";s:4:"c74c";s:25:"awstats/icon/flags/cd.png";s:4:"a60f";s:25:"awstats/icon/flags/cf.png";s:4:"1413";s:25:"awstats/icon/flags/cg.png";s:4:"77d6";s:25:"awstats/icon/flags/ch.png";s:4:"af76";s:25:"awstats/icon/flags/ci.png";s:4:"5430";s:25:"awstats/icon/flags/ck.png";s:4:"0f56";s:25:"awstats/icon/flags/cl.png";s:4:"12a4";s:25:"awstats/icon/flags/cm.png";s:4:"fd21";s:25:"awstats/icon/flags/cn.png";s:4:"428d";s:25:"awstats/icon/flags/co.png";s:4:"8c87";s:26:"awstats/icon/flags/com.png";s:4:"bd80";s:27:"awstats/icon/flags/coop.png";s:4:"bd80";s:25:"awstats/icon/flags/cr.png";s:4:"3002";s:25:"awstats/icon/flags/cs.png";s:4:"dae9";s:25:"awstats/icon/flags/cu.png";s:4:"877f";s:25:"awstats/icon/flags/cv.png";s:4:"79ce";s:25:"awstats/icon/flags/cx.png";s:4:"839a";s:25:"awstats/icon/flags/cy.png";s:4:"8c50";s:25:"awstats/icon/flags/cz.png";s:4:"dae9";s:25:"awstats/icon/flags/de.png";s:4:"370d";s:25:"awstats/icon/flags/dj.png";s:4:"3da1";s:25:"awstats/icon/flags/dk.png";s:4:"4030";s:25:"awstats/icon/flags/dm.png";s:4:"8a1e";s:25:"awstats/icon/flags/do.png";s:4:"9a56";s:25:"awstats/icon/flags/dz.png";s:4:"390c";s:25:"awstats/icon/flags/ec.png";s:4:"ddf2";s:26:"awstats/icon/flags/edu.png";s:4:"5947";s:25:"awstats/icon/flags/ee.png";s:4:"5d33";s:25:"awstats/icon/flags/eg.png";s:4:"36af";s:25:"awstats/icon/flags/eh.png";s:4:"aaa2";s:25:"awstats/icon/flags/en.png";s:4:"2d56";s:25:"awstats/icon/flags/er.png";s:4:"827f";s:25:"awstats/icon/flags/es.png";s:4:"1906";s:29:"awstats/icon/flags/es_cat.png";s:4:"287c";s:28:"awstats/icon/flags/es_eu.png";s:4:"3a43";s:25:"awstats/icon/flags/et.png";s:4:"9dd6";s:25:"awstats/icon/flags/eu.png";s:4:"854b";s:25:"awstats/icon/flags/fi.png";s:4:"035c";s:25:"awstats/icon/flags/fj.png";s:4:"ef06";s:25:"awstats/icon/flags/fk.png";s:4:"19f6";s:25:"awstats/icon/flags/fm.png";s:4:"d005";s:25:"awstats/icon/flags/fo.png";s:4:"ffa4";s:25:"awstats/icon/flags/fr.png";s:4:"dd71";s:25:"awstats/icon/flags/fx.png";s:4:"dd71";s:25:"awstats/icon/flags/ga.png";s:4:"f7d1";s:25:"awstats/icon/flags/gb.png";s:4:"aaba";s:25:"awstats/icon/flags/gd.png";s:4:"15c7";s:25:"awstats/icon/flags/ge.png";s:4:"ef02";s:25:"awstats/icon/flags/gf.png";s:4:"93a1";s:25:"awstats/icon/flags/gh.png";s:4:"25a2";s:25:"awstats/icon/flags/gi.png";s:4:"42f2";s:25:"awstats/icon/flags/gl.png";s:4:"8375";s:26:"awstats/icon/flags/glg.png";s:4:"29d1";s:25:"awstats/icon/flags/gm.png";s:4:"fee8";s:25:"awstats/icon/flags/gn.png";s:4:"c10f";s:26:"awstats/icon/flags/gov.png";s:4:"5aa7";s:25:"awstats/icon/flags/gp.png";s:4:"68c3";s:25:"awstats/icon/flags/gq.png";s:4:"c65a";s:25:"awstats/icon/flags/gr.png";s:4:"b15b";s:25:"awstats/icon/flags/gs.png";s:4:"8c08";s:25:"awstats/icon/flags/gt.png";s:4:"d816";s:25:"awstats/icon/flags/gu.png";s:4:"33ca";s:25:"awstats/icon/flags/gw.png";s:4:"1d98";s:25:"awstats/icon/flags/gy.png";s:4:"09e3";s:25:"awstats/icon/flags/hk.png";s:4:"58c3";s:25:"awstats/icon/flags/hm.png";s:4:"1053";s:25:"awstats/icon/flags/hn.png";s:4:"5c27";s:25:"awstats/icon/flags/hr.png";s:4:"d779";s:25:"awstats/icon/flags/ht.png";s:4:"783b";s:25:"awstats/icon/flags/hu.png";s:4:"36eb";s:25:"awstats/icon/flags/i0.png";s:4:"f65c";s:25:"awstats/icon/flags/id.png";s:4:"310d";s:25:"awstats/icon/flags/ie.png";s:4:"be9b";s:25:"awstats/icon/flags/il.png";s:4:"4e59";s:25:"awstats/icon/flags/im.png";s:4:"e9cb";s:25:"awstats/icon/flags/in.png";s:4:"c766";s:27:"awstats/icon/flags/info.png";s:4:"ff7b";s:26:"awstats/icon/flags/int.png";s:4:"ce69";s:25:"awstats/icon/flags/io.png";s:4:"3bec";s:25:"awstats/icon/flags/ip.png";s:4:"c1dc";s:25:"awstats/icon/flags/iq.png";s:4:"88ee";s:25:"awstats/icon/flags/ir.png";s:4:"d8d1";s:25:"awstats/icon/flags/is.png";s:4:"32e3";s:25:"awstats/icon/flags/it.png";s:4:"613d";s:25:"awstats/icon/flags/jm.png";s:4:"20c9";s:25:"awstats/icon/flags/jo.png";s:4:"a5dc";s:25:"awstats/icon/flags/jp.png";s:4:"d08e";s:25:"awstats/icon/flags/ke.png";s:4:"59d9";s:25:"awstats/icon/flags/kg.png";s:4:"00fe";s:25:"awstats/icon/flags/kh.png";s:4:"e56e";s:25:"awstats/icon/flags/ki.png";s:4:"ce4d";s:25:"awstats/icon/flags/km.png";s:4:"754d";s:25:"awstats/icon/flags/kn.png";s:4:"8f32";s:25:"awstats/icon/flags/kp.png";s:4:"fb82";s:25:"awstats/icon/flags/kr.png";s:4:"b73b";s:25:"awstats/icon/flags/kw.png";s:4:"18ff";s:25:"awstats/icon/flags/ky.png";s:4:"82b4";s:25:"awstats/icon/flags/kz.png";s:4:"41d9";s:25:"awstats/icon/flags/la.png";s:4:"dad1";s:25:"awstats/icon/flags/lb.png";s:4:"ce59";s:25:"awstats/icon/flags/lc.png";s:4:"50ba";s:25:"awstats/icon/flags/li.png";s:4:"15fe";s:25:"awstats/icon/flags/lk.png";s:4:"43ad";s:25:"awstats/icon/flags/lr.png";s:4:"bdc9";s:25:"awstats/icon/flags/ls.png";s:4:"fabe";s:25:"awstats/icon/flags/lt.png";s:4:"8419";s:25:"awstats/icon/flags/lu.png";s:4:"9d1a";s:25:"awstats/icon/flags/lv.png";s:4:"9275";s:25:"awstats/icon/flags/ly.png";s:4:"7ddd";s:25:"awstats/icon/flags/ma.png";s:4:"905c";s:25:"awstats/icon/flags/mc.png";s:4:"fcee";s:25:"awstats/icon/flags/md.png";s:4:"d52a";s:25:"awstats/icon/flags/mg.png";s:4:"4289";s:26:"awstats/icon/flags/mil.png";s:4:"90ed";s:25:"awstats/icon/flags/mk.png";s:4:"af23";s:25:"awstats/icon/flags/ml.png";s:4:"78b0";s:25:"awstats/icon/flags/mm.png";s:4:"58e3";s:25:"awstats/icon/flags/mn.png";s:4:"e7d7";s:25:"awstats/icon/flags/mo.png";s:4:"caa9";s:25:"awstats/icon/flags/mp.png";s:4:"5ee3";s:25:"awstats/icon/flags/mq.png";s:4:"d55a";s:25:"awstats/icon/flags/mr.png";s:4:"2239";s:25:"awstats/icon/flags/ms.png";s:4:"7d3c";s:25:"awstats/icon/flags/mt.png";s:4:"9417";s:25:"awstats/icon/flags/mu.png";s:4:"3cf2";s:29:"awstats/icon/flags/museum.png";s:4:"ff7b";s:25:"awstats/icon/flags/mv.png";s:4:"05c4";s:25:"awstats/icon/flags/mx.png";s:4:"1a96";s:25:"awstats/icon/flags/my.png";s:4:"eb16";s:25:"awstats/icon/flags/mz.png";s:4:"cade";s:25:"awstats/icon/flags/na.png";s:4:"f548";s:27:"awstats/icon/flags/name.png";s:4:"e956";s:27:"awstats/icon/flags/nato.png";s:4:"7b0f";s:25:"awstats/icon/flags/nb.png";s:4:"d3ca";s:25:"awstats/icon/flags/nc.png";s:4:"dd71";s:25:"awstats/icon/flags/ne.png";s:4:"6194";s:26:"awstats/icon/flags/net.png";s:4:"f60a";s:25:"awstats/icon/flags/ng.png";s:4:"22d9";s:25:"awstats/icon/flags/ni.png";s:4:"3a17";s:25:"awstats/icon/flags/nl.png";s:4:"13a3";s:25:"awstats/icon/flags/nn.png";s:4:"d3ca";s:25:"awstats/icon/flags/no.png";s:4:"d3ca";s:25:"awstats/icon/flags/np.png";s:4:"4c49";s:25:"awstats/icon/flags/nt.png";s:4:"ff7b";s:25:"awstats/icon/flags/nu.png";s:4:"00bf";s:25:"awstats/icon/flags/nz.png";s:4:"0044";s:25:"awstats/icon/flags/om.png";s:4:"cd69";s:26:"awstats/icon/flags/org.png";s:4:"d2a7";s:25:"awstats/icon/flags/pa.png";s:4:"6cd3";s:25:"awstats/icon/flags/pe.png";s:4:"5909";s:25:"awstats/icon/flags/pf.png";s:4:"2137";s:25:"awstats/icon/flags/pg.png";s:4:"6b3d";s:25:"awstats/icon/flags/ph.png";s:4:"7047";s:25:"awstats/icon/flags/pk.png";s:4:"5e18";s:25:"awstats/icon/flags/pl.png";s:4:"7dc7";s:25:"awstats/icon/flags/pr.png";s:4:"15c2";s:26:"awstats/icon/flags/pro.png";s:4:"ff7b";s:25:"awstats/icon/flags/ps.png";s:4:"29dd";s:25:"awstats/icon/flags/pt.png";s:4:"0148";s:25:"awstats/icon/flags/py.png";s:4:"4388";s:25:"awstats/icon/flags/qa.png";s:4:"5b03";s:25:"awstats/icon/flags/ro.png";s:4:"cdbd";s:25:"awstats/icon/flags/ru.png";s:4:"de59";s:25:"awstats/icon/flags/rw.png";s:4:"3775";s:25:"awstats/icon/flags/sa.png";s:4:"4b49";s:25:"awstats/icon/flags/sb.png";s:4:"8092";s:25:"awstats/icon/flags/sc.png";s:4:"2b9a";s:25:"awstats/icon/flags/sd.png";s:4:"f0f6";s:25:"awstats/icon/flags/se.png";s:4:"50b9";s:25:"awstats/icon/flags/sg.png";s:4:"7389";s:25:"awstats/icon/flags/si.png";s:4:"c4be";s:25:"awstats/icon/flags/sk.png";s:4:"d803";s:25:"awstats/icon/flags/sm.png";s:4:"ddc5";s:25:"awstats/icon/flags/sn.png";s:4:"d295";s:25:"awstats/icon/flags/sr.png";s:4:"63d2";s:25:"awstats/icon/flags/st.png";s:4:"eb46";s:25:"awstats/icon/flags/su.png";s:4:"085b";s:25:"awstats/icon/flags/sv.png";s:4:"24be";s:25:"awstats/icon/flags/sy.png";s:4:"1749";s:25:"awstats/icon/flags/sz.png";s:4:"0ec6";s:25:"awstats/icon/flags/tc.png";s:4:"8f1e";s:25:"awstats/icon/flags/td.png";s:4:"5b5b";s:25:"awstats/icon/flags/tf.png";s:4:"0ed9";s:25:"awstats/icon/flags/tg.png";s:4:"fb3f";s:25:"awstats/icon/flags/th.png";s:4:"220b";s:25:"awstats/icon/flags/tk.png";s:4:"b3b2";s:25:"awstats/icon/flags/tm.png";s:4:"7dbd";s:25:"awstats/icon/flags/tn.png";s:4:"2c87";s:25:"awstats/icon/flags/to.png";s:4:"7cb7";s:25:"awstats/icon/flags/tr.png";s:4:"0a85";s:25:"awstats/icon/flags/tt.png";s:4:"1089";s:25:"awstats/icon/flags/tv.png";s:4:"3cad";s:25:"awstats/icon/flags/tw.png";s:4:"4efe";s:25:"awstats/icon/flags/tz.png";s:4:"9f3d";s:25:"awstats/icon/flags/ua.png";s:4:"6b53";s:25:"awstats/icon/flags/ug.png";s:4:"6c4f";s:25:"awstats/icon/flags/uk.png";s:4:"ab88";s:25:"awstats/icon/flags/um.png";s:4:"2750";s:30:"awstats/icon/flags/unknown.png";s:4:"00f6";s:25:"awstats/icon/flags/us.png";s:4:"72db";s:25:"awstats/icon/flags/uy.png";s:4:"b8e4";s:25:"awstats/icon/flags/uz.png";s:4:"0dcc";s:25:"awstats/icon/flags/va.png";s:4:"4880";s:25:"awstats/icon/flags/vc.png";s:4:"20cd";s:25:"awstats/icon/flags/ve.png";s:4:"22a7";s:25:"awstats/icon/flags/vg.png";s:4:"d0b9";s:25:"awstats/icon/flags/vi.png";s:4:"b3b2";s:25:"awstats/icon/flags/vn.png";s:4:"d153";s:25:"awstats/icon/flags/vu.png";s:4:"e8cf";s:25:"awstats/icon/flags/wf.png";s:4:"b9ba";s:26:"awstats/icon/flags/wlk.png";s:4:"6261";s:25:"awstats/icon/flags/ws.png";s:4:"26a8";s:25:"awstats/icon/flags/ye.png";s:4:"e2bf";s:25:"awstats/icon/flags/yt.png";s:4:"0861";s:25:"awstats/icon/flags/yu.png";s:4:"84e4";s:25:"awstats/icon/flags/za.png";s:4:"4ca5";s:25:"awstats/icon/flags/ze.png";s:4:"9f8e";s:25:"awstats/icon/flags/zm.png";s:4:"c8af";s:25:"awstats/icon/flags/zw.png";s:4:"e305";s:29:"awstats/icon/mime/archive.png";s:4:"5f29";s:27:"awstats/icon/mime/audio.png";s:4:"1847";s:25:"awstats/icon/mime/doc.png";s:4:"8941";s:26:"awstats/icon/mime/html.png";s:4:"1184";s:27:"awstats/icon/mime/image.png";s:4:"b050";s:34:"awstats/icon/mime/notavailable.png";s:4:"925c";s:27:"awstats/icon/mime/other.png";s:4:"b193";s:25:"awstats/icon/mime/pdf.png";s:4:"e4ac";s:28:"awstats/icon/mime/script.png";s:4:"fafb";s:26:"awstats/icon/mime/text.png";s:4:"002d";s:29:"awstats/icon/mime/unknown.png";s:4:"8f1b";s:27:"awstats/icon/mime/video.png";s:4:"7c28";s:23:"awstats/icon/os/aix.png";s:4:"45ec";s:27:"awstats/icon/os/amigaos.png";s:4:"d46d";s:25:"awstats/icon/os/apple.png";s:4:"440a";s:25:"awstats/icon/os/atari.png";s:4:"9e2e";s:24:"awstats/icon/os/beos.png";s:4:"4975";s:24:"awstats/icon/os/bsdi.png";s:4:"7e3f";s:23:"awstats/icon/os/cpm.png";s:4:"0dd3";s:26:"awstats/icon/os/debian.png";s:4:"6dc1";s:27:"awstats/icon/os/digital.png";s:4:"c90c";s:23:"awstats/icon/os/dos.png";s:4:"81cc";s:29:"awstats/icon/os/dreamcast.png";s:4:"f453";s:27:"awstats/icon/os/freebsd.png";s:4:"cf15";s:23:"awstats/icon/os/gnu.png";s:4:"ead0";s:24:"awstats/icon/os/hpux.png";s:4:"2972";s:23:"awstats/icon/os/ibm.png";s:4:"483f";s:25:"awstats/icon/os/imode.png";s:4:"1a6a";s:24:"awstats/icon/os/irix.png";s:4:"b7fc";s:24:"awstats/icon/os/java.png";s:4:"36bc";s:25:"awstats/icon/os/linux.png";s:4:"8647";s:23:"awstats/icon/os/mac.png";s:4:"a4b7";s:29:"awstats/icon/os/macintosh.png";s:4:"a4b7";s:26:"awstats/icon/os/macosx.png";s:4:"7d57";s:26:"awstats/icon/os/netbsd.png";s:4:"cf15";s:27:"awstats/icon/os/netware.png";s:4:"4bb8";s:24:"awstats/icon/os/next.png";s:4:"985e";s:27:"awstats/icon/os/openbsd.png";s:4:"2bd3";s:23:"awstats/icon/os/os2.png";s:4:"4a95";s:23:"awstats/icon/os/osf.png";s:4:"d1e4";s:23:"awstats/icon/os/qnx.png";s:4:"f048";s:26:"awstats/icon/os/riscos.png";s:4:"5891";s:23:"awstats/icon/os/sco.png";s:4:"6f2b";s:25:"awstats/icon/os/sunos.png";s:4:"2fb2";s:27:"awstats/icon/os/symbian.png";s:4:"b013";s:24:"awstats/icon/os/unix.png";s:4:"7451";s:27:"awstats/icon/os/unknown.png";s:4:"8f1b";s:23:"awstats/icon/os/vms.png";s:4:"7436";s:25:"awstats/icon/os/webtv.png";s:4:"dcc4";s:23:"awstats/icon/os/win.png";s:4:"227d";s:25:"awstats/icon/os/win16.png";s:4:"227d";s:27:"awstats/icon/os/win2000.png";s:4:"227d";s:27:"awstats/icon/os/win2003.png";s:4:"f6e6";s:25:"awstats/icon/os/win95.png";s:4:"227d";s:25:"awstats/icon/os/win98.png";s:4:"227d";s:25:"awstats/icon/os/wince.png";s:4:"227d";s:27:"awstats/icon/os/winlong.png";s:4:"e9dd";s:25:"awstats/icon/os/winme.png";s:4:"227d";s:25:"awstats/icon/os/winnt.png";s:4:"227d";s:25:"awstats/icon/os/winxp.png";s:4:"f6e6";s:36:"awstats/icon/other/awstats_logo1.png";s:4:"d248";s:36:"awstats/icon/other/awstats_logo5.png";s:4:"fd4c";s:36:"awstats/icon/other/awstats_logo6.png";s:4:"b776";s:31:"awstats/icon/other/backleft.png";s:4:"c02d";s:29:"awstats/icon/other/button.gif";s:4:"c919";s:25:"awstats/icon/other/he.png";s:4:"f60a";s:25:"awstats/icon/other/hh.png";s:4:"da3d";s:25:"awstats/icon/other/hk.png";s:4:"9243";s:25:"awstats/icon/other/hp.png";s:4:"015a";s:25:"awstats/icon/other/ht.png";s:4:"bfba";s:25:"awstats/icon/other/hx.png";s:4:"990d";s:28:"awstats/icon/other/menu1.png";s:4:"63ab";s:28:"awstats/icon/other/menu2.png";s:4:"181f";s:28:"awstats/icon/other/menu3.png";s:4:"c4eb";s:28:"awstats/icon/other/menu4.png";s:4:"c763";s:28:"awstats/icon/other/menu5.png";s:4:"ddf6";s:28:"awstats/icon/other/menu6.png";s:4:"79ac";s:28:"awstats/icon/other/menu7.png";s:4:"d9a7";s:28:"awstats/icon/other/menu8.png";s:4:"8027";s:27:"awstats/icon/other/page.png";s:4:"7298";s:25:"awstats/icon/other/vh.png";s:4:"4ba7";s:25:"awstats/icon/other/vk.png";s:4:"7070";s:25:"awstats/icon/other/vp.png";s:4:"c7d4";s:25:"awstats/icon/other/vu.png";s:4:"736a";s:25:"awstats/icon/other/vv.png";s:4:"9a3e";s:34:"awstats/js/awstats_misc_tracker.js";s:4:"48ac";s:27:"awstats/lang/awstats-al.txt";s:4:"6a47";s:27:"awstats/lang/awstats-ar.txt";s:4:"3fef";s:27:"awstats/lang/awstats-ba.txt";s:4:"4a43";s:27:"awstats/lang/awstats-bg.txt";s:4:"4744";s:27:"awstats/lang/awstats-br.txt";s:4:"cb5d";s:27:"awstats/lang/awstats-ca.txt";s:4:"68c7";s:27:"awstats/lang/awstats-cn.txt";s:4:"6bfd";s:27:"awstats/lang/awstats-cy.txt";s:4:"5eba";s:27:"awstats/lang/awstats-cz.txt";s:4:"21ef";s:27:"awstats/lang/awstats-de.txt";s:4:"34d2";s:27:"awstats/lang/awstats-dk.txt";s:4:"94ae";s:27:"awstats/lang/awstats-en.txt";s:4:"98f3";s:27:"awstats/lang/awstats-es.txt";s:4:"52bb";s:27:"awstats/lang/awstats-et.txt";s:4:"348d";s:27:"awstats/lang/awstats-eu.txt";s:4:"1e7a";s:27:"awstats/lang/awstats-fi.txt";s:4:"b026";s:27:"awstats/lang/awstats-fr.txt";s:4:"3672";s:27:"awstats/lang/awstats-gl.txt";s:4:"e135";s:27:"awstats/lang/awstats-gr.txt";s:4:"be85";s:27:"awstats/lang/awstats-he.txt";s:4:"4450";s:27:"awstats/lang/awstats-hr.txt";s:4:"90a4";s:27:"awstats/lang/awstats-hu.txt";s:4:"a102";s:27:"awstats/lang/awstats-id.txt";s:4:"761b";s:27:"awstats/lang/awstats-is.txt";s:4:"6a4a";s:27:"awstats/lang/awstats-it.txt";s:4:"d2f3";s:27:"awstats/lang/awstats-jp.txt";s:4:"15ad";s:27:"awstats/lang/awstats-kr.txt";s:4:"d1a1";s:27:"awstats/lang/awstats-lv.txt";s:4:"cb03";s:27:"awstats/lang/awstats-nb.txt";s:4:"11e0";s:27:"awstats/lang/awstats-nl.txt";s:4:"3cc1";s:27:"awstats/lang/awstats-nn.txt";s:4:"4787";s:27:"awstats/lang/awstats-pl.txt";s:4:"5bd3";s:27:"awstats/lang/awstats-pt.txt";s:4:"e090";s:27:"awstats/lang/awstats-ro.txt";s:4:"b8f1";s:27:"awstats/lang/awstats-ru.txt";s:4:"20be";s:27:"awstats/lang/awstats-se.txt";s:4:"38c1";s:27:"awstats/lang/awstats-si.txt";s:4:"78c4";s:27:"awstats/lang/awstats-sk.txt";s:4:"89d5";s:27:"awstats/lang/awstats-sr.txt";s:4:"3015";s:27:"awstats/lang/awstats-th.txt";s:4:"2a42";s:27:"awstats/lang/awstats-tr.txt";s:4:"2f63";s:27:"awstats/lang/awstats-tw.txt";s:4:"03e3";s:27:"awstats/lang/awstats-ua.txt";s:4:"9d97";s:41:"awstats/lang/tooltips_f/awstats-tt-br.txt";s:4:"fca9";s:41:"awstats/lang/tooltips_f/awstats-tt-cz.txt";s:4:"4dff";s:41:"awstats/lang/tooltips_f/awstats-tt-en.txt";s:4:"df76";s:41:"awstats/lang/tooltips_f/awstats-tt-is.txt";s:4:"b7d8";s:41:"awstats/lang/tooltips_m/awstats-tt-br.txt";s:4:"8758";s:41:"awstats/lang/tooltips_m/awstats-tt-en.txt";s:4:"32a4";s:41:"awstats/lang/tooltips_m/awstats-tt-fr.txt";s:4:"34b7";s:41:"awstats/lang/tooltips_m/awstats-tt-is.txt";s:4:"3cb0";s:41:"awstats/lang/tooltips_w/awstats-tt-al.txt";s:4:"ae30";s:41:"awstats/lang/tooltips_w/awstats-tt-ba.txt";s:4:"4cda";s:41:"awstats/lang/tooltips_w/awstats-tt-bg.txt";s:4:"cd40";s:41:"awstats/lang/tooltips_w/awstats-tt-br.txt";s:4:"edde";s:41:"awstats/lang/tooltips_w/awstats-tt-ca.txt";s:4:"9893";s:41:"awstats/lang/tooltips_w/awstats-tt-cn.txt";s:4:"0e21";s:41:"awstats/lang/tooltips_w/awstats-tt-cz.txt";s:4:"289c";s:41:"awstats/lang/tooltips_w/awstats-tt-de.txt";s:4:"5c40";s:41:"awstats/lang/tooltips_w/awstats-tt-dk.txt";s:4:"9531";s:41:"awstats/lang/tooltips_w/awstats-tt-en.txt";s:4:"4e35";s:41:"awstats/lang/tooltips_w/awstats-tt-es.txt";s:4:"f08a";s:41:"awstats/lang/tooltips_w/awstats-tt-fi.txt";s:4:"23b0";s:41:"awstats/lang/tooltips_w/awstats-tt-fr.txt";s:4:"8399";s:41:"awstats/lang/tooltips_w/awstats-tt-gl.txt";s:4:"e6ea";s:41:"awstats/lang/tooltips_w/awstats-tt-hu.txt";s:4:"769b";s:41:"awstats/lang/tooltips_w/awstats-tt-is.txt";s:4:"19a3";s:41:"awstats/lang/tooltips_w/awstats-tt-it.txt";s:4:"ec61";s:41:"awstats/lang/tooltips_w/awstats-tt-jp.txt";s:4:"ea0c";s:41:"awstats/lang/tooltips_w/awstats-tt-kr.txt";s:4:"9905";s:41:"awstats/lang/tooltips_w/awstats-tt-nb.txt";s:4:"98dd";s:41:"awstats/lang/tooltips_w/awstats-tt-nl.txt";s:4:"dc05";s:41:"awstats/lang/tooltips_w/awstats-tt-nn.txt";s:4:"ec18";s:41:"awstats/lang/tooltips_w/awstats-tt-pl.txt";s:4:"bef0";s:41:"awstats/lang/tooltips_w/awstats-tt-ro.txt";s:4:"3c88";s:41:"awstats/lang/tooltips_w/awstats-tt-ru.txt";s:4:"67dd";s:41:"awstats/lang/tooltips_w/awstats-tt-se.txt";s:4:"dfb1";s:41:"awstats/lang/tooltips_w/awstats-tt-sk.txt";s:4:"8a03";s:41:"awstats/lang/tooltips_w/awstats-tt-sr.txt";s:4:"f5ee";s:41:"awstats/lang/tooltips_w/awstats-tt-tr.txt";s:4:"549f";s:41:"awstats/lang/tooltips_w/awstats-tt-tw.txt";s:4:"12e3";s:41:"awstats/lang/tooltips_w/awstats-tt-ua.txt";s:4:"d856";s:23:"awstats/lib/browsers.pm";s:4:"369c";s:29:"awstats/lib/browsers_phone.pm";s:4:"a629";s:22:"awstats/lib/domains.pm";s:4:"e474";s:19:"awstats/lib/mime.pm";s:4:"a43b";s:32:"awstats/lib/operating_systems.pm";s:4:"c032";s:27:"awstats/lib/referer_spam.pm";s:4:"45a9";s:21:"awstats/lib/robots.pm";s:4:"ddd0";s:29:"awstats/lib/search_engines.pm";s:4:"faac";s:26:"awstats/lib/status_http.pm";s:4:"5c0c";s:26:"awstats/lib/status_smtp.pm";s:4:"12db";s:20:"awstats/lib/worms.pm";s:4:"4d15";s:30:"awstats/plugins/clusterinfo.pm";s:4:"29f1";s:32:"awstats/plugins/decodeutfkeys.pm";s:4:"f929";s:24:"awstats/plugins/geoip.pm";s:4:"28c8";s:37:"awstats/plugins/geoip_city_maxmind.pm";s:4:"43d2";s:36:"awstats/plugins/geoip_isp_maxmind.pm";s:4:"5ec3";s:36:"awstats/plugins/geoip_org_maxmind.pm";s:4:"3e2b";s:39:"awstats/plugins/geoip_region_maxmind.pm";s:4:"f799";s:28:"awstats/plugins/geoipfree.pm";s:4:"b8d8";s:30:"awstats/plugins/graphapplet.pm";s:4:"e3e6";s:28:"awstats/plugins/hashfiles.pm";s:4:"7111";s:27:"awstats/plugins/hostinfo.pm";s:4:"e614";s:23:"awstats/plugins/ipv6.pm";s:4:"67e6";s:25:"awstats/plugins/rawlog.pm";s:4:"8cd3";s:28:"awstats/plugins/timehires.pm";s:4:"582b";s:27:"awstats/plugins/timezone.pm";s:4:"d8b5";s:27:"awstats/plugins/tooltips.pm";s:4:"1d00";s:27:"awstats/plugins/urlalias.pm";s:4:"4ff5";s:27:"awstats/plugins/userinfo.pm";s:4:"9be8";s:34:"awstats/plugins/example/example.pm";s:4:"6712";s:14:"doc/manual.sxw";s:4:"2507";s:23:"doc/awstats/COPYING.TXT";s:4:"2066";s:23:"doc/awstats/LICENSE.TXT";s:4:"2066";s:22:"doc/awstats/README.TXT";s:4:"0894";s:33:"doc/awstats/awstats_changelog.txt";s:4:"bc5f";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"3649";s:14:"mod1/index.php";s:4:"e759";s:18:"mod1/locallang.php";s:4:"b242";s:16:"mod1/logfile.gif";s:4:"415b";s:19:"mod1/moduleicon.gif";s:4:"706e";s:23:"res/awstats_default.css";s:4:"e77b";}',
);

?>