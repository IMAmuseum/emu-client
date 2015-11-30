<?php
/* KE Software Open Source Licence
** 
** Notice: Copyright (c) 2011-2013 KE SOFTWARE PTY LTD (ACN 006 213 298)
** (the "Owner"). All rights reserved.
** 
** Licence: Permission is hereby granted, free of charge, to any person
** obtaining a copy of this software and associated documentation files
** (the "Software"), to deal with the Software without restriction,
** including without limitation the rights to use, copy, modify, merge,
** publish, distribute, sublicense, and/or sell copies of the Software,
** and to permit persons to whom the Software is furnished to do so,
** subject to the following conditions.
** 
** Conditions: The Software is licensed on condition that:
** 
** (1) Redistributions of source code must retain the above Notice,
**     these Conditions and the following Limitations.
** 
** (2) Redistributions in binary form must reproduce the above Notice,
**     these Conditions and the following Limitations in the
**     documentation and/or other materials provided with the distribution.
** 
** (3) Neither the names of the Owner, nor the names of its contributors
**     may be used to endorse or promote products derived from this
**     Software without specific prior written permission.
** 
** Limitations: Any person exercising any of the permissions in the
** relevant licence will be taken to have accepted the following as
** legally binding terms severally with the Owner and any other
** copyright owners (collectively "Participants"):
** 
** TO THE EXTENT PERMITTED BY LAW, THE SOFTWARE IS PROVIDED "AS IS",
** WITHOUT ANY REPRESENTATION, WARRANTY OR CONDITION OF ANY KIND, EXPRESS
** OR IMPLIED, INCLUDING (WITHOUT LIMITATION) AS TO MERCHANTABILITY,
** FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. TO THE EXTENT
** PERMITTED BY LAW, IN NO EVENT SHALL ANY PARTICIPANT BE LIABLE FOR ANY
** CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
** TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
** SOFTWARE OR THE USE OR OTHER DEALINGS WITH THE SOFTWARE.
** 
** WHERE BY LAW A LIABILITY (ON ANY BASIS) OF ANY PARTICIPANT IN RELATION
** TO THE SOFTWARE CANNOT BE EXCLUDED, THEN TO THE EXTENT PERMITTED BY
** LAW THAT LIABILITY IS LIMITED AT THE OPTION OF THE PARTICIPANT TO THE
** REPLACEMENT, REPAIR OR RESUPPLY OF THE RELEVANT GOODS OR SERVICES
** (INCLUDING BUT NOT LIMITED TO SOFTWARE) OR THE PAYMENT OF THE COST OF SAME.
*/
require_once dirname(__FILE__) . '/IMu.php';

/* MIME type information
**
** Inspired by CPAN perl package MIME::Types
*/
class IMuMIME
{
	public static function
	byFile($file)
	{
		self::load();

		$file = preg_replace('/^.*\./', '', $file);
		$file = strtolower($file);
		if (array_key_exists($file, self::$_extensions))
			return self::$_extensions[$file];
		return self::$_default;
	}

	public static function
	byType($type)
	{
		self::load();

		$type = strtolower($type);
		if (array_key_exists($type, self::$_types))
			return self::$_types[$file];
		return self::$_default;
	}

	public static function
	getExtension($type)
	{
		$mime = self::byType($type);
		if (count($mime->extensions) >= 1)
			return $mime->extensions[0];
		return null;
	}

	public static function
	getType($file)
	{
		$mime = self::byFile($file);
		return $mime->type;
	}

	private static $_default = null;
	private static $_extensions = null;
	private static $_types = null;

	private static function
	load()
	{
		if (self::$_default !== null)
			return;

		self::$_default = new IMuMIMEType('application/x-unknown');
		self::$_default->default = true;

		self::$_extensions = array();
		self::$_types = array();

		/* Open the file to access the data.
		** Any errors are silently ignored.
		*/
		$handle = fopen(__FILE__, 'r');
		if ($handle === false)
			return;
		if (fseek($handle, __COMPILER_HALT_OFFSET__) < 0)
			return;
		// discard remainder of line after __halt_compiler();
		fgets($handle);

		for (;;)
		{
			$line = fgets($handle);
			if ($line === false)
				break;
			$line = preg_replace('/\s+$/', '', $line);
			if ($line == '')
				continue;

			$parts = explode(';', $line);
			$mime = new IMuMIMEType($parts[0]);
			if (count($parts) > 1)
			{
				$extensions = $parts[1];
				$extensions = explode(',', $extensions);
				foreach ($extensions as $extension)
				{
					$extension = preg_replace('/^\s+/', '', $extension);
					$extension = preg_replace('/\s+$/', '', $extension);
					if ($extension != '')
						$mime->extensions[] = $extension;
				}
			}
			if (count($parts) > 2)
				$mime->encoding = $parts[2];

			foreach ($mime->extensions as $extension)
			{
				/* Only add new entries to the index.
				**
				** This means that in cases where the same extension is
				** associated with more than one mime type the first entry
				** in the __halt_compiler() section below will always be
				** returned.
				*/
				if (! array_key_exists($extension, self::$_extensions))
				{
					self::$_extensions[$extension] = $mime;
				}

			}

			$type = strtolower($mime->type);
			if (! array_key_exists($type, self::$_types))
				self::$_types[$type] = $mime;
		}
		fclose($handle);
	}
}

class IMuMIMEType
{
	public function
	__construct($type)
	{
		$this->type = $type;

		$parts = explode('/', $type, 2);
		$this->top = $parts[0];
		$this->sub = $parts[1];
		$this->extensions = array();
		$this->encoding = null;
		$this->default = false;
	}

	public $top;
	public $sub;
	public $extensions;
	public $encoding;
	public $default;
}

/* Sources of entries below
**
** CPAN MIME::Types module (version 1.30)
** http://www.w3schools.com/media/media_mimeref.asp (5 May 2011)
** http://www.feedforall.com/mime-types.htm (16 Sep 2011)
** http://ftyps.com/ (16 Sep 2011)
*/
__halt_compiler();
application/activemessage
application/andrew-inset;ez
application/annodex;anx
application/appledouble;;base64
application/applefile;;base64
application/atom+xml;atom;8bit
application/atomcat+xml;atomcat
application/atomicmail
application/atomserv+xml;atomsrv
application/batch-smtp
application/bbolin;lin
application/beep+xml
application/cals-1840
application/cap;cap,pcap
application/cnrp+xml
application/commonground
application/cpl+xml
application/cu-seeme;cu
application/cybercash
application/davmount+xml;davmount
application/dca-rft
application/dec-dx
application/dicom
application/docbook+xml
application/dsptype;tsp
application/dvcs
application/ecmascript;es
application/edi-consent
application/edi-x12
application/edifact
application/envoy;evy
application/eshop
application/font-tdpfr;pfr
application/fractals;fif
application/futuresplash;spl
application/ghostview
application/hta;hta
application/http
application/hyperstudio;stk
application/iges
application/index
application/index.cmd
application/index.obj
application/index.response
application/index.vnd
application/internet-property-stream;acx
application/iotp
application/ipp
application/isup
application/java-archive;jar
application/java-serialized-object;ser
application/java-vm;class
application/javascript;js;8bit
application/json;json;8bit
application/m3g;m3g
application/mac-binhex40;hqx;8bit
application/mac-compactpro;cpt
application/macwriteii
application/marc
application/mathematica;nb,nbp
application/mathml+xml;mathml
application/mpeg4-generic
application/ms-tnef
application/msaccess;mdb
application/msword;doc,dot
application/mxf;mxf
application/news-message-id
application/news-transmission
application/ocsp-request;orq
application/ocsp-response;ors
application/octet-stream;bin,exe,ani,so,dll,class,dms,lha,lzh,dmg;base64
application/oda;oda
application/ogg;ogx,ogg
application/olescript;axs
application/parityfec
application/pdf;pdf;base64
application/pgp-encrypted;;7bit
application/pgp-keys;key;7bit
application/pgp-signature;sig,pgp;base64
application/pics-rules;prf
application/pidf+xml
application/pkcs10;p10
application/pkcs7-mime;p7m,p7c
application/pkcs7-signature;p7s
application/pkix-cert;cer
application/pkix-crl;crl
application/pkix-pkipath;pkipath
application/pkixcmp;pki
application/postscript;ps-z,ps,ai,eps,epsi,epsf,eps2,eps3;base64
application/prs.alvestrand.titrax-sheet
application/prs.cww;cw,cww
application/prs.nprend;rnd,rct
application/prs.plucker
application/qsig
application/rar;rar
application/rdf+xml;rdf;8bit
application/reginfo+xml
application/remote-printing
application/riscos
application/rss+xml;rss
application/rtf;rtf;8bit
application/sdp
application/set-payment
application/set-payment-initiation;setpay
application/set-registration
application/set-registration-initiation;setreg
application/sgml
application/sgml-open-catalog;soc
application/sieve;siv
application/slate
application/smil;smi,smil;8bit
application/srgs;gram
application/srgs+xml;grxml
application/timestamp-query
application/timestamp-reply
application/toolbook;tbk
application/tve-trigger
application/vemmi
application/vnd.3gpp.pic-bw-large;plb
application/vnd.3gpp.pic-bw-small;psb
application/vnd.3gpp.pic-bw-var;pvb
application/vnd.3gpp.sms;sms
application/vnd.3m.post-it-notes
application/vnd.accpac.simply.aso
application/vnd.accpac.simply.imp
application/vnd.acucobol
application/vnd.acucorp;atc,acutc;7bit
application/vnd.adobe.xfdf;xfdf
application/vnd.aether.imp
application/vnd.amiga.amu;ami
application/vnd.android.package-archive;apk
application/vnd.anser-web-certificate-issue-initiation
application/vnd.anser-web-funds-transfer-initiation
application/vnd.audiograph
application/vnd.blueice.multipass;mpm
application/vnd.bmi
application/vnd.businessobjects
application/vnd.canon-cpdl
application/vnd.canon-lips
application/vnd.cinderella;cdy
application/vnd.claymore
application/vnd.commerce-battelle
application/vnd.commonspace
application/vnd.comsocaller
application/vnd.contact.cmsg
application/vnd.cosmocaller;cmc
application/vnd.criticaltools.wbs+xml;wbs
application/vnd.ctc-posml
application/vnd.cups-postscript
application/vnd.cups-raster
application/vnd.cups-raw
application/vnd.curl;curl
application/vnd.cybank
application/vnd.data-vision.rdz;rdz
application/vnd.dna
application/vnd.dpgraph
application/vnd.dreamfactory;dfac
application/vnd.dxr
application/vnd.ecdis-update
application/vnd.ecowin.chart
application/vnd.ecowin.filerequest
application/vnd.ecowin.fileupdate
application/vnd.ecowin.series
application/vnd.ecowin.seriesrequest
application/vnd.ecowin.seriesupdate
application/vnd.enliven
application/vnd.epson.esf
application/vnd.epson.msf
application/vnd.epson.quickanime
application/vnd.epson.salt
application/vnd.epson.ssf
application/vnd.ericsson.quickcall
application/vnd.eudora.data
application/vnd.fdf
application/vnd.ffsns
application/vnd.fints
application/vnd.flographit
application/vnd.framemaker
application/vnd.fsc.weblauch;fsc;7bit
application/vnd.fsc.weblaunch
application/vnd.fujitsu.oasys
application/vnd.fujitsu.oasys2
application/vnd.fujitsu.oasys3
application/vnd.fujitsu.oasysgp
application/vnd.fujitsu.oasysprs
application/vnd.fujixerox.ddd
application/vnd.fujixerox.docuworks
application/vnd.fujixerox.docuworks.binder
application/vnd.fut-misnet
application/vnd.genomatix.tuxedo;txd
application/vnd.google-earth.kml+xml;kml;8bit
application/vnd.google-earth.kmz;kmz;8bit
application/vnd.grafeq
application/vnd.groove-account
application/vnd.groove-help
application/vnd.groove-identity-message
application/vnd.groove-injector
application/vnd.groove-tool-message
application/vnd.groove-tool-template
application/vnd.groove-vcard
application/vnd.hbci;hbci,hbc,kom,upa,pkd,bpd
application/vnd.hhe.lesson-player;les
application/vnd.hp-hpgl;plt,hpgl
application/vnd.hp-hpid
application/vnd.hp-hps
application/vnd.hp-pcl
application/vnd.hp-pclxl
application/vnd.httphone
application/vnd.hzn-3d-crossword
application/vnd.ibm.afplinedata
application/vnd.ibm.electronic-media;emm
application/vnd.ibm.minipay
application/vnd.ibm.modcap
application/vnd.ibm.rights-management;irm
application/vnd.ibm.secure-container;sc
application/vnd.informix-visionary
application/vnd.intercon.formnet
application/vnd.intertrust.digibox
application/vnd.intertrust.nncp
application/vnd.intu.qbo
application/vnd.intu.qfx
application/vnd.ipunplugged.rcprofile;rcprofile
application/vnd.irepository.package+xml;irp
application/vnd.is-xpr
application/vnd.japannet-directory-service
application/vnd.japannet-jpnstore-wakeup
application/vnd.japannet-payment-wakeup
application/vnd.japannet-registration
application/vnd.japannet-registration-wakeup
application/vnd.japannet-setstore-wakeup
application/vnd.japannet-verification
application/vnd.japannet-verification-wakeup
application/vnd.jisp;jisp
application/vnd.kde.karbon;karbon
application/vnd.kde.kchart;chrt
application/vnd.kde.kformula;kfo
application/vnd.kde.kivio;flw
application/vnd.kde.kontour;kon
application/vnd.kde.kpresenter;kpr,kpt
application/vnd.kde.kspread;ksp
application/vnd.kde.kword;kwd,kwt
application/vnd.kenameapp;htke
application/vnd.kidspiration;kia
application/vnd.kinar;kne,knp
application/vnd.koan
application/vnd.liberty-request+xml
application/vnd.llamagraphics.life-balance.desktop;lbd
application/vnd.llamagraphics.life-balance.exchange+xml;lbe
application/vnd.lotus-1-2-3;wks,123
application/vnd.lotus-approach
application/vnd.lotus-freelance
application/vnd.lotus-notes
application/vnd.lotus-organizer
application/vnd.lotus-screencam
application/vnd.lotus-wordpro
application/vnd.mcd;mcd
application/vnd.mediastation.cdkey
application/vnd.meridian-slingshot
application/vnd.mfmp;mfm
application/vnd.micrografx.flo;flo
application/vnd.micrografx.igx;igx
application/vnd.mif;mif
application/vnd.minisoft-hp3000-save
application/vnd.mitsubishi.misty-guard.trustweb
application/vnd.mobius.daf
application/vnd.mobius.dis
application/vnd.mobius.mbk
application/vnd.mobius.mqy
application/vnd.mobius.msl
application/vnd.mobius.plc
application/vnd.mobius.txf
application/vnd.mophun.application;mpn
application/vnd.mophun.certificate;mpc
application/vnd.motorola.flexsuite
application/vnd.motorola.flexsuite.adsi
application/vnd.motorola.flexsuite.fis
application/vnd.motorola.flexsuite.gotap
application/vnd.motorola.flexsuite.kmr
application/vnd.motorola.flexsuite.ttc
application/vnd.motorola.flexsuite.wem
application/vnd.mozilla.xul+xml;xul
application/vnd.ms-artgalry;cil
application/vnd.ms-asf;asf
application/vnd.ms-excel;xls,xlt,xlb,xla,xlc,xlm,xlw;base64
application/vnd.ms-excel.sheet.binary.macroenabled.12;xlsb
application/vnd.ms-excel.sheet.macroenabled.12;xlsm
application/vnd.ms-lrm;lrm
application/vnd.ms-pki.seccat;cat
application/vnd.ms-pki.stl;stl
application/vnd.ms-pkicertstore;sst
application/vnd.ms-pkiseccat;cat
application/vnd.ms-pkistl;stl
application/vnd.ms-powerpoint;ppt,pps,pot;base64
application/vnd.ms-powerpoint.presentation.macroenabled.12;pptm
application/vnd.ms-powerpoint.slideshow.macroenabled.12;ppsm
application/vnd.ms-project;mpp;base64
application/vnd.ms-tnef;;base64
application/vnd.ms-word.document.macroenabled.12;docm
application/vnd.ms-word.template.macroenabled.12;dotm
application/vnd.ms-works;wcm,wdb,wks,wps;base64
application/vnd.ms-wpl;wpl;base64
application/vnd.ms-xpsdocument;xps;8bit
application/vnd.mseq;mseq
application/vnd.msign
application/vnd.music-niff
application/vnd.musician
application/vnd.nervana;ent,entity,req,request,bkm,kcm
application/vnd.netfpx
application/vnd.noblenet-directory
application/vnd.noblenet-sealer
application/vnd.noblenet-web
application/vnd.nokia.radio-preset;rpst
application/vnd.nokia.radio-presets;rpss
application/vnd.novadigm.edm
application/vnd.novadigm.edx
application/vnd.novadigm.ext
application/vnd.oasis.opendocument.chart;odc
application/vnd.oasis.opendocument.database;odb
application/vnd.oasis.opendocument.formula;odf
application/vnd.oasis.opendocument.graphics;odg
application/vnd.oasis.opendocument.graphics-template;otg
application/vnd.oasis.opendocument.image;odi
application/vnd.oasis.opendocument.presentation;odp
application/vnd.oasis.opendocument.presentation-template;otp
application/vnd.oasis.opendocument.spreadsheet;ods
application/vnd.oasis.opendocument.spreadsheet-template;ots
application/vnd.oasis.opendocument.text;odt
application/vnd.oasis.opendocument.text-master;odm
application/vnd.oasis.opendocument.text-template;ott
application/vnd.oasis.opendocument.text-web;oth
application/vnd.obn
application/vnd.openxmlformats-officedocument.presentationml.presentation;pptx
application/vnd.openxmlformats-officedocument.presentationml.slideshow;ppsx
application/vnd.openxmlformats-officedocument.presentationml.template;potx
application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;xlsx;binary
application/vnd.openxmlformats-officedocument.spreadsheetml.template;xltx
application/vnd.openxmlformats-officedocument.wordprocessingml.document;docx
application/vnd.openxmlformats-officedocument.wordprocessingml.template;dotx
application/vnd.osa.netdeploy
application/vnd.palm;prc,pdb,pqa,oprc
application/vnd.paos.xml
application/vnd.pg.format
application/vnd.pg.osasli
application/vnd.picsel;efif
application/vnd.powerbuilder6
application/vnd.powerbuilder6-s
application/vnd.powerbuilder7
application/vnd.powerbuilder7-s
application/vnd.powerbuilder75
application/vnd.powerbuilder75-s
application/vnd.previewsystems.box
application/vnd.publishare-delta-tree
application/vnd.pvi.ptid1;pti,ptid
application/vnd.pwg-multiplexed
application/vnd.pwg-xhtml-print+xml
application/vnd.pwg-xmhtml-print+xml
application/vnd.quark.quarkxpress;qxd,qxt,qwd,qwt,qxl,qxb;8bit
application/vnd.rapid
application/vnd.renlearn.rlprint
application/vnd.rim.cod;cod
application/vnd.rn-realmedia;rm
application/vnd.s3sms
application/vnd.sealed.doc;sdoc,sdo,s1w
application/vnd.sealed.eml;seml,sem
application/vnd.sealed.mht;smht,smh
application/vnd.sealed.net
application/vnd.sealed.ppt;sppt,spp,s1p
application/vnd.sealed.xls;sxls,sxl,s1e
application/vnd.sealedmedia.softseal.html;stml,stm,s1h
application/vnd.sealedmedia.softseal.pdf;spdf,spd,s1a
application/vnd.seemail;see
application/vnd.shana.informed.formdata
application/vnd.shana.informed.formtemplate
application/vnd.shana.informed.interchange
application/vnd.shana.informed.package
application/vnd.smaf;mmf
application/vnd.sss-cod
application/vnd.sss-dtf
application/vnd.sss-ntf
application/vnd.stardivision.calc;sdc
application/vnd.stardivision.chart;sds
application/vnd.stardivision.draw;sda
application/vnd.stardivision.impress;sdd
application/vnd.stardivision.math;sdf
application/vnd.stardivision.writer;sdw
application/vnd.stardivision.writer-global;sgl
application/vnd.street-stream
application/vnd.sun.xml.calc;sxc
application/vnd.sun.xml.calc.template;stc
application/vnd.sun.xml.draw;sxd
application/vnd.sun.xml.draw.template;std
application/vnd.sun.xml.impress;sxi
application/vnd.sun.xml.impress.template;sti
application/vnd.sun.xml.math;sxm
application/vnd.sun.xml.writer;sxw
application/vnd.sun.xml.writer.global;sxg
application/vnd.sun.xml.writer.template;stw
application/vnd.sus-calendar;sus,susp
application/vnd.svd
application/vnd.swiftview-ics
application/vnd.symbian.install;sis
application/vnd.syncml.ds.notification
application/vnd.triscape.mxs
application/vnd.trueapp
application/vnd.truedoc
application/vnd.tve-trigger
application/vnd.ufdl
application/vnd.uiq.theme
application/vnd.uplanet.alert
application/vnd.uplanet.alert-wbxml
application/vnd.uplanet.bearer-choice
application/vnd.uplanet.bearer-choice-wbxml
application/vnd.uplanet.cacheop
application/vnd.uplanet.cacheop-wbxml
application/vnd.uplanet.channel
application/vnd.uplanet.channel-wbxml
application/vnd.uplanet.list
application/vnd.uplanet.list-wbxml
application/vnd.uplanet.listcmd
application/vnd.uplanet.listcmd-wbxml
application/vnd.uplanet.signal
application/vnd.vcx
application/vnd.vectorworks
application/vnd.vidsoft.vidconference;vsc;8bit
application/vnd.visio;vsd,vst,vsw,vss
application/vnd.visionary;vis
application/vnd.vividence.scriptfile
application/vnd.vsf
application/vnd.wap.sic;sic
application/vnd.wap.slc;slc
application/vnd.wap.wbxml;wbxml,wbmxl
application/vnd.wap.wmlc;wmlc
application/vnd.wap.wmlscriptc;wmlsc
application/vnd.webturbo;wtb
application/vnd.wordperfect;wpd
application/vnd.wordperfect5.1;wp5
application/vnd.wqd;wqd
application/vnd.wrq-hp3000-labelled
application/vnd.wt.stf
application/vnd.wv.csp+wbxml;wv
application/vnd.wv.csp+xml;;8bit
application/vnd.wv.ssp+xml;;8bit
application/vnd.xara
application/vnd.xfdl
application/vnd.yamaha.hv-dic;hvd
application/vnd.yamaha.hv-script;hvs
application/vnd.yamaha.hv-voice;hvp
application/vnd.yamaha.smaf-audio;saf
application/vnd.yamaha.smaf-phrase;spf
application/vnd.yellowriver-custom-menu
application/voicexml+xml;vxml
application/watcherinfo+xml;wif
application/whoispp-query
application/whoispp-response
application/winhlp;hlp
application/wita
application/wordperfect5.1;wp5,wp
application/x-123;wk
application/x-7z-compressed;7z
application/x-abiword;abw
application/x-access
application/x-apple-diskimage;dmg
application/x-bcpio;bcpio
application/x-bittorrent;torrent
application/x-bleeper;bleep;base64
application/x-bzip2;bz2
application/x-cab;cab
application/x-cbr;cbr
application/x-cbz;cbz
application/x-cdf;cdf,cda
application/x-cdlink;vcd
application/x-chess-pgn;pgn
application/x-clariscad
application/x-compress;z,z;base64
application/x-compressed;tgz
application/x-core
application/x-cpio;cpio;base64
application/x-csh;csh;8bit
application/x-cu-seeme;csm,cu
application/x-debian-package;deb,udeb
application/x-director;dcr,dir,dxr
application/x-dms;dms
application/x-doom;wad
application/x-drafting
application/x-dvi;dvi;base64
application/x-dxf
application/x-excel
application/x-executable
application/x-font;pfa,pfb,gsf,pcf,pcf.z
application/x-fractals
application/x-freemind;mm
application/x-futuresplash;spl
application/x-ghostview
application/x-gnumeric;gnumeric
application/x-go-sgf;sgf
application/x-graphing-calculator;gcf
application/x-gtar;gtar,tgz,tbz2,tbz,taz;base64
application/x-gunzip
application/x-gzip;gz;base64
application/x-hdf;hdf
application/x-hep;hep
application/x-html+ruby;rhtml;8bit
application/x-httpd-eruby;rhtml
application/x-httpd-php;phtml,pht,php;8bit
application/x-httpd-php-source;phps
application/x-httpd-php3;php3
application/x-httpd-php3-preprocessed;php3p
application/x-httpd-php4;php4
application/x-httpd-php5;php5
application/x-ica;ica
application/x-ideas
application/x-imagemap;imagemap,imap;8bit
application/x-info;info
application/x-internet-signup;ins,isp
application/x-iphone;iii
application/x-iso9660-image;iso
application/x-jam;jam
application/x-java-applet
application/x-java-archive;jar
application/x-java-bean
application/x-java-jnlp-file;jnlp
application/x-java-serialized-object;ser
application/x-java-vm;class
application/x-javascript;js
application/x-jmol;jmz
application/x-kchart;chrt
application/x-kdelnk
application/x-killustrator;kil
application/x-koan;skp,skd,skt,skm
application/x-kpresenter;kpr,kpt
application/x-kspread;ksp
application/x-kword;kwd,kwt
application/x-latex;latex;8bit
application/x-lha;lha
application/x-lotus-123
application/x-lyx;lyx
application/x-lzh;lzh
application/x-lzx;lzx
application/x-mac-compactpro;cpt
application/x-maker;frm,maker,frame,fm,fb,book,fbdoc
application/x-mathcad;#;mcd,
application/x-mif;mif
application/x-ms-wmd;wmd
application/x-ms-wmz;wmz
application/x-msaccess;mda,mdb,mde,mdf;base64
application/x-mscardfile;crd
application/x-msclip;clp
application/x-msdos-program;com,exe,bat,dll;base64
application/x-msdownload;dll;base64
application/x-msi;msi
application/x-msmediaview;m13,m14,mvb
application/x-msmetafile;wmf
application/x-msmoney;mny
application/x-mspublisher;pub
application/x-msschedule;scd
application/x-msterminal;trm
application/x-msword;doc,dot,wrd;base64
application/x-mswrite;wri
application/x-netcdf;nc,cdf
application/x-ns-proxy-autoconfig;pac,dat
application/x-nwc;nwc
application/x-object;o
application/x-oz-application;oza
application/x-pagemaker;pm5,pt5,pm
application/x-perfmon;pma,pmc,pml,pmr,pmw
application/x-perl;pl,pm;8bit
application/x-pgp;pgp
application/x-pkcs12;p12,pfx
application/x-pkcs7-certificates;p7b,spc
application/x-pkcs7-certreqresp;p7r
application/x-pkcs7-crl;crl
application/x-pkcs7-mime;p7c,p7m
application/x-pkcs7-signature;p7s
application/x-python;py;8bit
application/x-python-code;pyc,pyo
application/x-qgis;qgs,shp,shx
application/x-quicktimeplayer;qtl
application/x-rar-compressed;rar;base64
application/x-redhat-package-manager;rpm
application/x-remote_printing
application/x-ruby;rb,rbw;8bit
application/x-rx
application/x-set
application/x-sh;sh;8bit
application/x-shar;shar;8bit
application/x-shellscript
application/x-shockwave-flash;swf,swfl
application/x-silverlight;scr
application/x-sla
application/x-solids
application/x-spss;sav,sbs,sps,spo,spp
application/x-stuffit;sit,sitx;base64
application/x-sv4cpio;sv4cpio;base64
application/x-sv4crc;sv4crc;base64
application/x-tar;tar;base64
application/x-tcl;tcl;8bit
application/x-tex;tex;8bit
application/x-tex-gf;gf
application/x-tex-pk;pk
application/x-texinfo;texinfo,texi;8bit
application/x-trash;~,%,bak,old,sik
application/x-troff;t,tr,roff;8bit
application/x-troff-man;man;8bit
application/x-troff-me;me
application/x-troff-ms;ms
application/x-ustar;ustar;base64
application/x-vda
application/x-videolan
application/x-vmsbackup;bck;base64
application/x-wais-source;src
application/x-wingz;wz
application/x-word;;base64
application/x-wordperfect6.1;wp6
application/x-x400-bp
application/x-x509-ca-cert;crt,cer,der;base64
application/x-xcf;xcf
application/x-xfig;fig
application/x-xpinstall;xpi
application/x400-bp
application/xhtml+xml;xhtml,xht;8bit
application/xml;xml,xsl,xsd;8bit
application/xml-dtd;dtd;8bit
application/xml-external-parsed-entity
application/xslt+xml;xslt;8bit
application/xspf+xml;xspf
application/ynd.ms-pkipko;pko
application/zip;zip;base64
audio/32kadpcm
audio/3gpp
audio/3gpp2
audio/amr;amr;base64
audio/amr-wb;awb;base64
audio/annodex;axa
audio/basic;au,snd;base64
audio/cn
audio/dat12
audio/dsr-es201108
audio/dvi4
audio/evrc;evc
audio/evrc-qcp
audio/evrc0
audio/flac;flac
audio/g.722.1
audio/g722
audio/g723
audio/g726-16
audio/g726-24
audio/g726-32
audio/g726-40
audio/g728
audio/g729
audio/g729d
audio/g729e
audio/gsm
audio/gsm-efr
audio/l16;l16
audio/l20
audio/l24
audio/l8
audio/lpc
audio/mid;mid,rmi
audio/midi;mid,midi,kar
audio/mp4;f4a,f4b,m4b,m4p,msnv,ndas
audio/mp4a-latm;m4a,m4b,m4p
audio/mpa
audio/mpa-robust
audio/mpeg;mpga,mp2,mp3,mpega,m4a;base64
audio/mpeg4-generic
audio/mpegurl;m3u
audio/ogg;ogg,oga,spx
audio/parityfec
audio/pcma
audio/pcmu
audio/prs.sid;sid,psid
audio/qcelp;qcp
audio/red
audio/smv;smv
audio/smv-qcp
audio/smv0
audio/telephone-event
audio/tone
audio/vdvi
audio/vnd.3gpp.iufp
audio/vnd.audiokoz;koz
audio/vnd.cisco.nse
audio/vnd.cns.anp1
audio/vnd.cns.inf1
audio/vnd.digital-winds;eol;7bit
audio/vnd.everad.plj;plj
audio/vnd.lucent.voice;lvp
audio/vnd.nokia.mobile-xmf;mxmf
audio/vnd.nortel.vbk;vbk
audio/vnd.nuera.ecelp4800;ecelp4800
audio/vnd.nuera.ecelp7470;ecelp7470
audio/vnd.nuera.ecelp9600;ecelp9600
audio/vnd.octel.sbc
audio/vnd.qcelp
audio/vnd.rhetorex.32kadpcm
audio/vnd.sealedmedia.softseal.mpeg;smp3,smp,s1m
audio/vnd.vmx.cvsd
audio/x-aiff;aif,aifc,aiff;base64
audio/x-gsm;gsm
audio/x-m4a;m4a
audio/x-midi;mid,midi,kar;base64
audio/x-mpegurl;m3u
audio/x-ms-wax;wax
audio/x-ms-wma;wma
audio/x-pn-realaudio;rm,ram,ra;base64
audio/x-pn-realaudio-plugin;rpm
audio/x-realaudio;ra;base64
audio/x-scpls;pls
audio/x-sd2;sd2
audio/x-wav;wav;base64
chemical/x-alchemy;alc
chemical/x-cache;cac,cache
chemical/x-cache-csf;csf
chemical/x-cactvs-binary;cbin,cascii,ctab
chemical/x-cdx;cdx
chemical/x-cerius;cer
chemical/x-chem3d;c3d
chemical/x-chemdraw;chm
chemical/x-cif;cif
chemical/x-cmdf;cmdf
chemical/x-cml;cml
chemical/x-compass;cpa
chemical/x-crossfire;bsd
chemical/x-csml;csml,csm
chemical/x-ctx;ctx
chemical/x-cxf;cxf,cef
chemical/x-embl-dl-nucleotide;emb,embl
chemical/x-galactic-spc;spc
chemical/x-gamess-input;inp,gam,gamin
chemical/x-gaussian-checkpoint;fch,fchk
chemical/x-gaussian-cube;cub
chemical/x-gaussian-input;gau,gjc,gjf
chemical/x-gaussian-log;gal
chemical/x-gcg8-sequence;gcg
chemical/x-genbank;gen
chemical/x-hin;hin
chemical/x-isostar;istr,ist
chemical/x-jcamp-dx;jdx,dx
chemical/x-kinemage;kin
chemical/x-macmolecule;mcm
chemical/x-macromodel-input;mmd,mmod
chemical/x-mdl-molfile;mol
chemical/x-mdl-rdfile;rd
chemical/x-mdl-rxnfile;rxn
chemical/x-mdl-sdfile;sd,sdf
chemical/x-mdl-tgf;tgf
chemical/x-mmcif;mcif
chemical/x-mol2;mol2
chemical/x-molconn-z;b
chemical/x-mopac-graph;gpt
chemical/x-mopac-input;mop,mopcrt,mpc,zmt
chemical/x-mopac-out;moo
chemical/x-mopac-vib;mvb
chemical/x-ncbi-asn1;asn
chemical/x-ncbi-asn1-ascii;prt,ent
chemical/x-ncbi-asn1-binary;val,aso
chemical/x-ncbi-asn1-spec;asn
chemical/x-pdb;pdb,ent
chemical/x-rosdal;ros
chemical/x-swissprot;sw
chemical/x-vamas-iso14976;vms
chemical/x-vmd;vmd
chemical/x-xtel;xtel
chemical/x-xyz;xyz
drawing/dwf;dwf
image/bmp;bmp
image/cgm;cgm
image/cis-cod;cod
image/g3fax
image/gif;gif;base64
image/ief;ief;base64
image/jp2;jp2,jpg2;base64
image/jpeg;jpeg,jpg,jpe;base64
image/jpm;jpm,jpgm
image/jpx;jpf,jpx
image/naplps
image/pcx;pcx
image/pict;pct,pic,pict
image/pipeg;jfif
image/png;png;base64
image/prs.btif
image/prs.pti
image/svg+xml;svg,svgz;8bit
image/t38
image/targa;tga
image/tiff;tiff,tif;base64
image/tiff-fx
image/vnd.cns.inf2
image/vnd.dgn;dgn
image/vnd.djvu;djvu,djv
image/vnd.dwg;dwg
image/vnd.dxf
image/vnd.fastbidsheet
image/vnd.fpx
image/vnd.fst
image/vnd.fujixerox.edmics-mmr
image/vnd.fujixerox.edmics-rlc
image/vnd.glocalgraphics.pgb;pgb
image/vnd.microsoft.icon;ico
image/vnd.mix
image/vnd.ms-modi;mdi
image/vnd.net-fpx
image/vnd.sealed.png;spng,spn,s1n
image/vnd.sealedmedia.softseal.gif;sgif,sgi,s1g
image/vnd.sealedmedia.softseal.jpg;sjpg,sjp,s1j
image/vnd.svf
image/vnd.wap.wbmp;wbmp
image/vnd.xiff
image/x-adobe-dng;dng
image/x-bmp;bmp
image/x-canon-cr2;cr2
image/x-canon-crw;crw
image/x-cmu-raster;ras
image/x-cmx;cmx
image/x-coreldraw;cdr
image/x-coreldrawpattern;pat
image/x-coreldrawtemplate;cdt
image/x-corelphotopaint;cpt
image/x-epson-erf;erf
image/x-icon;ico
image/x-jg;art
image/x-jng;jng
image/x-macpaint;mac,pnt,pntg
image/x-ms-bmp;bmp
image/x-nikon-nef;nef
image/x-olympus-orf;orf
image/x-photoshop;psd
image/x-portable-anymap;pnm;base64
image/x-portable-bitmap;pbm;base64
image/x-portable-graymap;pgm;base64
image/x-portable-pixmap;ppm;base64
image/x-quicktime;qti,qtif
image/x-rgb;rgb;base64
image/x-xbitmap;xbm;7bit
image/x-xpixmap;xpm;8bit
image/x-xwindowdump;xwd;base64
inode/blockdevice
inode/chardevice
inode/directory
inode/directory-locked
inode/fifo
inode/socket
message/cpim
message/delivery-status
message/disposition-notification
message/external-body;;8bit
message/http
message/news;;8bit
message/partial;;8bit
message/rfc822;eml,mhtml,mht,nws;8bit
message/s-http
message/sip
message/sipfrag
model/iges;igs,iges
model/mesh;msh,mesh,silo
model/vnd.dwf
model/vnd.flatland.3dml
model/vnd.gdl
model/vnd.gs-gdl
model/vnd.gtw
model/vnd.mts
model/vnd.parasolid.transmit.binary;x_b,xmt_bin
model/vnd.parasolid.transmit.text;x_t,xmt_txt;quoted-printable
model/vnd.vtu
model/vrml;wrl,vrml
model/x3d+binary;x3db
model/x3d+vrml;x3dv
model/x3d+xml;x3d
multipart/alternative;;8bit
multipart/appledouble;;8bit
multipart/byteranges
multipart/digest;;8bit
multipart/encrypted
multipart/form-data
multipart/header-set
multipart/mixed;;8bit
multipart/parallel;;8bit
multipart/related
multipart/report
multipart/signed
multipart/voice-message
multipart/x-gzip
multipart/x-mixed-replace
multipart/x-tar
multipart/x-ustar
multipart/x-www-form-urlencoded
multipart/x-zip
text/cache-manifest;manifest
text/calendar;ics,icz,ifb
text/comma-separated-values;;8bit
text/css;css;8bit
text/csv;csv;8bit
text/directory
text/english
text/enriched
text/h323;323
text/html;html,htm,htmlx,shtml,htx,stm;8bit
text/iuls;uls
text/mathml;mml
text/parityfec
text/plain;txt,asc,c,cc,h,hh,cpp,hpp,dat,hlp,text,pot,brf,bas;8bit
text/prs.fallenstein.rst;rst
text/prs.lines.tag
text/rfc822-headers
text/richtext;rtx;8bit
text/rtf;rtf;8bit
text/scriptlet;sct,wsc
text/sgml;sgml,sgm
text/t140
text/tab-separated-values;tsv
text/texmacs;tm,ts
text/uri-list
text/vnd.abc
text/vnd.curl
text/vnd.dmclientscript
text/vnd.flatland.3dml
text/vnd.fly
text/vnd.fmi.flexstor
text/vnd.in3d.3dml
text/vnd.in3d.spot
text/vnd.iptc.newsml
text/vnd.iptc.nitf
text/vnd.latex-z
text/vnd.motorola.reflex
text/vnd.ms-mediapackage
text/vnd.net2phone.commcenter.command;ccc
text/vnd.sun.j2me.app-descriptor;jad;8bit
text/vnd.wap.si;si
text/vnd.wap.sl;sl
text/vnd.wap.wml;wml
text/vnd.wap.wmlscript;wmls
text/webviewhtml;htt
text/x-bibtex;bib
text/x-boo;boo
text/x-c++hdr;h++,hpp,hxx,hh
text/x-c++src;c++,cpp,cxx,cc
text/x-chdr;h
text/x-component;htc;8bit
text/x-crontab
text/x-csh;csh
text/x-csrc;c
text/x-diff;diff,patch
text/x-dsrc;d
text/x-haskell;hs
text/x-java;java
text/x-literate-haskell;lhs
text/x-makefile
text/x-moc;moc
text/x-pascal;p,pas
text/x-pcs-gcd;gcd
text/x-perl;pl,pm
text/x-python;py
text/x-scala;scala
text/x-server-parsed-html
text/x-setext;etx
text/x-sgml;sgml,sgm;8bit
text/x-sh;sh
text/x-tcl;tcl,tk
text/x-tex;tex,ltx,sty,cls
text/x-vcalendar;vcs
text/x-vcard;vcf
text/xml
text/xml-external-parsed-entity
video/3gpp;3gp,3gpp,3ge6,3ge7,3gg6,3gp1,3gp2,3gp3,3gp4,3gp5,3gp6,3gs7;base64
video/3gpp2;3g2,3gpp2,3g2a,3g2b,3g2c,kddi;base64
video/annodex;axv
video/bmpeg
video/bt656
video/celb
video/dl;dl;base64
video/dv;dif,dv
video/fli;fli
video/gl;gl;base64
video/h261
video/h263
video/h263-1998
video/h263-2000
video/jpeg
video/mj2;mj2,mjp2,mj2s
video/mp1s
video/mp2p
video/mp2t
video/mp4;mp4,f4v,f4p,avc1,iso2,isom,mmp4,mp41,mp42,ndsc,ndsh,ndsm,ndsp,ndss,ndxc,ndxh,ndxm,ndxp,ndxs
video/mp4v-es
video/mpeg;mp2,mpe,mpeg,mpg,mpa,mpv2;base64
video/mpeg4-generic
video/mpv
video/nv
video/ogg;ogv
video/parityfec
video/pointer
video/quicktime;qt,mov,mqt;base64
video/smpte292m
video/vnd.dvb.file;dvr1,dvt1
video/vnd.fvt;fvt
video/vnd.motorola.video
video/vnd.motorola.videop
video/vnd.mpegurl;mxu,m4u;8bit
video/vnd.mts
video/vnd.nokia.interleaved-multimedia;nim
video/vnd.objectvideo;mp4
video/vnd.sealed.mpeg1;s11
video/vnd.sealed.mpeg4;smpg,s14
video/vnd.sealed.swf;sswf,ssw
video/vnd.sealedmedia.softseal.mov;smov,smo,s1q
video/vnd.vivo;viv,vivo
video/x-dv;dif,dv
video/x-fli;fli;base64
video/x-flv;flv;base64
video/x-la-asf;lsf,lsx
video/x-m4v;m4v,m4vh,m4vp
video/x-matroska;mpv,mkv
video/x-mng;mng
video/x-ms-asf;asf,asx,asr
video/x-ms-wm;wm
video/x-ms-wmv;wmv
video/x-ms-wmx;wmx
video/x-ms-wvx;wvx
video/x-msvideo;avi;base64
video/x-sgi-movie;movie;base64
x-chemical/x-pdb;pdb
x-chemical/x-xyz;xyz
x-conference/x-cooltalk;ice
x-drawing/dwf;dwf
x-epoc/x-sisx-app;sisx
x-world/x-vrml;wrl,vrml,vrm,flr,wrz,xaf,xof
