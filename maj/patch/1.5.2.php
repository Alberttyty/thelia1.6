<?php
	/* ------------------------------------------------------------------ */

	if (version_compare(PHP_VERSION, '5.3.0', '<')) {
		throw new Exception('<span class="erreur">PHP 5.3 est nécessaire pour cette mise à jour.</span><br />', 2);
	}

        define('IN_UPDATE_THELIA_152', true);
	// Attention, le path doit être relatif, sinon file_get_contents() interptète le code PHP
	// qui se trouve dans les fichiers au lieu de retourner le contenu.
	$basedir = __DIR__."/../..";

	$configorigfilepath  = "$basedir/install/patch/1.5.2/config_thelia.php";
	$configfilepath  = "$basedir/client/config_thelia.php";
	$cnxfilepath     = "$basedir/classes/Cnx.class.php";
	$cnxfileorigpath = "$basedir/classes/Cnx.class.php.orig";

	// Vérifier les permissions sur les fichiers à modifier
	$files = array(
			$cnxfilepath,
			$cnxfileorigpath,
            "$basedir/client"
	);

	$err = '';

	foreach($files as $file) {
		if (! is_writable($file)) {

            $err .= '<span class="erreur">Le fichier '.str_replace($basedir, '', $file). ' n\'est pas accessible en &eacute;criture ou bien n\'existe pas.</span><br />';
		}
	}

	if (! empty($err)) {
		throw new Exception($err, 1);
	}

	$configfile = file_get_contents($configorigfilepath);
	$cnxfile = file_get_contents($cnxfilepath);
	$cnxfileorig = file_get_contents($cnxfileorigpath);

	$cnxok = false;

	if (preg_match('/public static \$host[^\"]*\"([^\"]*)\"/', $cnxfile, $res)) {
		$configfile = str_replace("votre_serveur",$res[1],$configfile);

		if (preg_match('/public static \$login_mysql[^\"]*\"([^\"]*)\"/', $cnxfile, $res)) {
			$configfile = str_replace("votre_login_mysql",$res[1],$configfile);

			if (preg_match('/public static \$password_mysql[^\"]*\"([^\"]*)\"/', $cnxfile, $res)) {
				$configfile = str_replace("votre_motdepasse_mysql",$res[1],$configfile);

				if (preg_match('/public static \$db[^\"_]*\"([^\"]*)\"/', $cnxfile, $res)) {
					$configfile = str_replace("bdd_sql",$res[1],$configfile);

				    // On met à jour le fichier dans client
					file_put_contents($configfilepath, $configfile);

					$cnxok = true;
				}
			}
		}
	}

	if (! $cnxok) {
		throw new Exception('<span class="erreur">Le fichier '.$configfilepath .' ne peut pas être mise à jour</span><br />', 2);
	}

	// Mise en place du Cnx.class.php définitif
	unlink($cnxfilepath);
	rename($cnxfileorigpath, $cnxfilepath);

	// Buggé dans la MAJ 1.5.1
	query_patch("ALTER TABLE `produitdesc` ENGINE=MYISAM");
	query_patch("ALTER TABLE `contenudesc` ENGINE=MYISAM");
	query_patch("ALTER TABLE `rubriquedesc` ENGINE=MYISAM");
	query_patch("ALTER TABLE `dossierdesc` ENGINE=MYISAM");

	// Buggé dans la MAJ 1.5.1
        if(! mysql_num_rows(query_patch('SHOW INDEX FROM `produitdesc` WHERE key_name = "recherche"')) ){
            query_patch("ALTER TABLE `produitdesc` ADD FULLTEXT `recherche` (`titre` , `chapo` , `description` , `postscriptum`)");
        }
        if(! mysql_num_rows(query_patch('SHOW INDEX FROM `contenudesc` WHERE key_name = "recherche"')) ){
            query_patch("ALTER TABLE `contenudesc` ADD FULLTEXT `recherche` (`titre` , `chapo` , `description` , `postscriptum`)");
        }
        if(! mysql_num_rows(query_patch('SHOW INDEX FROM `rubriquedesc` WHERE key_name = "recherche"')) ){
            query_patch("ALTER TABLE `rubriquedesc` ADD FULLTEXT `recherche` (`titre` , `chapo` , `description` , `postscriptum`)");
        }
        if(! mysql_num_rows(query_patch('SHOW INDEX FROM `dossierdesc` WHERE key_name = "recherche"')) ){
            query_patch("ALTER TABLE `dossierdesc` ADD FULLTEXT `recherche` (`titre` , `chapo` , `description` , `postscriptum`)");
        }

	query_patch("ALTER TABLE  `promo` CHANGE  `utilise`  `utilise` INT( 11 ) NOT NULL DEFAULT  '0', CHANGE  `illimite`  `limite` SMALLINT( 6 ) NOT NULL DEFAULT  '0', CHANGE  `datefin`  `datefin` DATE NOT NULL");
	query_patch("ALTER TABLE  `promo` ADD  `actif` TINYINT( 1 ) NOT NULL");
	query_patch("ALTER TABLE  `promoutil` ADD  `code` TEXT NOT NULL, ADD  `type` SMALLINT( 6 ) NOT NULL, ADD  `valeur` FLOAT NOT NULL");

	query_patch("ALTER TABLE `pays` ADD `isocode` varchar( 4 ) NOT NULL DEFAULT '',ADD `isoalpha2` VARCHAR( 2 ) NOT NULL DEFAULT '',ADD `isoalpha3` VARCHAR( 4 ) NOT NULL DEFAULT ''");

	query_patch("update pays set isocode='004', isoalpha3='AFG', isoalpha2='AF' where id=1");
	query_patch("update pays set isocode='710', isoalpha3='ZAF', isoalpha2='ZA' where id=2");
	query_patch("update pays set isocode='008', isoalpha3='ALB', isoalpha2='AL' where id=3");
	query_patch("update pays set isocode='012', isoalpha3='DZA', isoalpha2='DZ' where id=4");
	query_patch("update pays set isocode='276', isoalpha3='DEU', isoalpha2='DE' where id=5");
	query_patch("update pays set isocode='020', isoalpha3='AND', isoalpha2='AD' where id=6");
	query_patch("update pays set isocode='024', isoalpha3='AGO', isoalpha2='AO' where id=7");
	query_patch("update pays set isocode='028', isoalpha3='ATG', isoalpha2='AG' where id=8");
	query_patch("update pays set isocode='682', isoalpha3='SAU', isoalpha2='SA' where id=9");
	query_patch("update pays set isocode='032', isoalpha3='ARG', isoalpha2='AR' where id=10");
	query_patch("update pays set isocode='051', isoalpha3='ARM', isoalpha2='AM' where id=11");
	query_patch("update pays set isocode='036', isoalpha3='AUS', isoalpha2='AU' where id=12");
	query_patch("update pays set isocode='040', isoalpha3='AUT', isoalpha2='AT' where id=13");
	query_patch("update pays set isocode='031', isoalpha3='AZE', isoalpha2='AZ' where id=14");
	query_patch("update pays set isocode='044', isoalpha3='BHS', isoalpha2='BS' where id=15");
	query_patch("update pays set isocode='048', isoalpha3='BHR', isoalpha2='BR' where id=16");
	query_patch("update pays set isocode='050', isoalpha3='BGD', isoalpha2='BD' where id=17");
	query_patch("update pays set isocode='052', isoalpha3='BRB', isoalpha2='BB' where id=18");
	query_patch("update pays set isocode='585', isoalpha3='PLW', isoalpha2='PW' where id=19");
	query_patch("update pays set isocode='056', isoalpha3='BEL', isoalpha2='BE' where id=20");
	query_patch("update pays set isocode='084', isoalpha3='BLZ', isoalpha2='BL' where id=21");
	query_patch("update pays set isocode='204', isoalpha3='BEN', isoalpha2='BJ' where id=22");
	query_patch("update pays set isocode='064', isoalpha3='BTN', isoalpha2='BT' where id=23");
	query_patch("update pays set isocode='112', isoalpha3='BLR', isoalpha2='BY' where id=24");
	query_patch("update pays set isocode='104', isoalpha3='MMR', isoalpha2='MM' where id=25");
	query_patch("update pays set isocode='068', isoalpha3='BOL', isoalpha2='BO' where id=26");
	query_patch("update pays set isocode='070', isoalpha3='BIH', isoalpha2='BA' where id=27");
	query_patch("update pays set isocode='072', isoalpha3='BWA', isoalpha2='BW' where id=28");
	query_patch("update pays set isocode='076', isoalpha3='BRA', isoalpha2='BR' where id=29");
	query_patch("update pays set isocode='096', isoalpha3='BRN', isoalpha2='BN' where id=30");
	query_patch("update pays set isocode='100', isoalpha3='BGR', isoalpha2='BG' where id=31");
	query_patch("update pays set isocode='854', isoalpha3='BFA', isoalpha2='BF' where id=32");
	query_patch("update pays set isocode='108', isoalpha3='BDI', isoalpha2='BI' where id=33");
	query_patch("update pays set isocode='116', isoalpha3='KHM', isoalpha2='KH' where id=34");
	query_patch("update pays set isocode='120', isoalpha3='CMR', isoalpha2='CM' where id=35");
	query_patch("update pays set isocode='124', isoalpha3='CAN', isoalpha2='CA' where id=247");
	query_patch("update pays set isocode='124', isoalpha3='CAN', isoalpha2='CA' where id=246");
	query_patch("update pays set isocode='124', isoalpha3='CAN', isoalpha2='CA' where id=254");
	query_patch("update pays set isocode='124', isoalpha3='CAN', isoalpha2='CA' where id=249");
	query_patch("update pays set isocode='124', isoalpha3='CAN', isoalpha2='CA' where id=252");
	query_patch("update pays set isocode='124', isoalpha3='CAN', isoalpha2='CA' where id=253");
	query_patch("update pays set isocode='124', isoalpha3='CAN', isoalpha2='CA' where id=258");
	query_patch("update pays set isocode='124', isoalpha3='CAN', isoalpha2='CA' where id=250");
	query_patch("update pays set isocode='124', isoalpha3='CAN', isoalpha2='CA' where id=251");
	query_patch("update pays set isocode='124', isoalpha3='CAN', isoalpha2='CA' where id=248");
	query_patch("update pays set isocode='124', isoalpha3='CAN', isoalpha2='CA' where id=255");
	query_patch("update pays set isocode='124', isoalpha3='CAN', isoalpha2='CA' where id=257");
	query_patch("update pays set isocode='124', isoalpha3='CAN', isoalpha2='CA' where id=256");
	query_patch("update pays set isocode='132', isoalpha3='CPV', isoalpha2='CV' where id=37");
	query_patch("update pays set isocode='152', isoalpha3='CHL', isoalpha2='CL' where id=38");
	query_patch("update pays set isocode='156', isoalpha3='CHN', isoalpha2='CN' where id=39");
	query_patch("update pays set isocode='196', isoalpha3='CYP', isoalpha2='CY' where id=40");
	query_patch("update pays set isocode='170', isoalpha3='COL', isoalpha2='CO' where id=41");
	query_patch("update pays set isocode='124', isoalpha3='CAN', isoalpha2='CA' where id=246");
	query_patch("update pays set isocode='174', isoalpha3='COM', isoalpha2='KM' where id=42");
	query_patch("update pays set isocode='178', isoalpha3='COG', isoalpha2='CG' where id=43");
	query_patch("update pays set isocode='184', isoalpha3='COK', isoalpha2='CK' where id=44");
	query_patch("update pays set isocode='408', isoalpha3='PRK', isoalpha2='KP' where id=45");
	query_patch("update pays set isocode='410', isoalpha3='KOR', isoalpha2='KR' where id=46");
	query_patch("update pays set isocode='188', isoalpha3='CRI', isoalpha2='CR' where id=47");
	query_patch("update pays set isocode='384', isoalpha3='CIV', isoalpha2='CI' where id=48");
	query_patch("update pays set isocode='191', isoalpha3='HRV', isoalpha2='HR' where id=49");
	query_patch("update pays set isocode='192', isoalpha3='CUB', isoalpha2='CU' where id=50");
	query_patch("update pays set isocode='208', isoalpha3='DNK', isoalpha2='DK' where id=51");
	query_patch("update pays set isocode='262', isoalpha3='DJI', isoalpha2='DJ' where id=52");
	query_patch("update pays set isocode='212', isoalpha3='DMA', isoalpha2='DM' where id=53");
	query_patch("update pays set isocode='818', isoalpha3='EGY', isoalpha2='EG' where id=54");
	query_patch("update pays set isocode='784', isoalpha3='ARE', isoalpha2='AE' where id=55");
	query_patch("update pays set isocode='218', isoalpha3='ECU', isoalpha2='EC' where id=56");
	query_patch("update pays set isocode='232', isoalpha3='ERI', isoalpha2='ER' where id=57");
	query_patch("update pays set isocode='724', isoalpha3='ESP', isoalpha2='ES' where id=58");
	query_patch("update pays set isocode='233', isoalpha3='EST', isoalpha2='EE' where id=59");
	query_patch("update pays set isocode='231', isoalpha3='ETH', isoalpha2='ET' where id=61");
	query_patch("update pays set isocode='242', isoalpha3='FJI', isoalpha2='FJ' where id=62");
	query_patch("update pays set isocode='246', isoalpha3='FIN', isoalpha2='FI' where id=63");
	query_patch("update pays set isocode='250', isoalpha3='FRA', isoalpha2='FR' where id=64");
	query_patch("update pays set isocode='266', isoalpha3='GAB', isoalpha2='GA' where id=65");
	query_patch("update pays set isocode='270', isoalpha3='GMB', isoalpha2='GM' where id=66");
	query_patch("update pays set isocode='268', isoalpha3='GEO', isoalpha2='GE' where id=67");
	query_patch("update pays set isocode='288', isoalpha3='GHA', isoalpha2='GH' where id=68");
	query_patch("update pays set isocode='300', isoalpha3='GRC', isoalpha2='GR' where id=69");
	query_patch("update pays set isocode='308', isoalpha3='GRD', isoalpha2='GD' where id=70");
	query_patch("update pays set isocode='312', isoalpha3='GLP', isoalpha2='GP' where id=259");
	query_patch("update pays set isocode='320', isoalpha3='GTM', isoalpha2='GT' where id=71");
	query_patch("update pays set isocode='324', isoalpha3='GIN', isoalpha2='GN' where id=72");
	query_patch("update pays set isocode='226', isoalpha3='GNQ', isoalpha2='GQ' where id=74");
	query_patch("update pays set isocode='624', isoalpha3='GNB', isoalpha2='GW' where id=73");
	query_patch("update pays set isocode='328', isoalpha3='GUY', isoalpha2='GY' where id=75");
	query_patch("update pays set isocode='254', isoalpha3='GUF', isoalpha2='GF' where id=260");
	query_patch("update pays set isocode='332', isoalpha3='HTI', isoalpha2='HT' where id=76");
	query_patch("update pays set isocode='340', isoalpha3='HND', isoalpha2='HN' where id=77");
	query_patch("update pays set isocode='348', isoalpha3='HUN', isoalpha2='HU' where id=78");
	query_patch("update pays set isocode='356', isoalpha3='IND', isoalpha2='IN' where id=79");
	query_patch("update pays set isocode='360', isoalpha3='IDN', isoalpha2='ID' where id=80");
	query_patch("update pays set isocode='364', isoalpha3='IRN', isoalpha2='IR' where id=81");
	query_patch("update pays set isocode='368', isoalpha3='IRQ', isoalpha2='IQ' where id=82");
	query_patch("update pays set isocode='372', isoalpha3='IRL', isoalpha2='IE' where id=83");
	query_patch("update pays set isocode='352', isoalpha3='ISL', isoalpha2='IS' where id=84");
	query_patch("update pays set isocode='376', isoalpha3='ISR', isoalpha2='IL' where id=85");
	query_patch("update pays set isocode='380', isoalpha3='ITA', isoalpha2='IT' where id=86");
	query_patch("update pays set isocode='388', isoalpha3='JAM', isoalpha2='JM' where id=87");
	query_patch("update pays set isocode='392', isoalpha3='JPN', isoalpha2='JP' where id=88");
	query_patch("update pays set isocode='400', isoalpha3='JOR', isoalpha2='JO' where id=89");
	query_patch("update pays set isocode='398', isoalpha3='KAZ', isoalpha2='KZ' where id=90");
	query_patch("update pays set isocode='404', isoalpha3='KEN', isoalpha2='KE' where id=91");
	query_patch("update pays set isocode='417', isoalpha3='KGZ', isoalpha2='KG' where id=92");
	query_patch("update pays set isocode='296', isoalpha3='KIR', isoalpha2='KI' where id=93");
	query_patch("update pays set isocode='414', isoalpha3='KWT', isoalpha2='KW' where id=94");
	query_patch("update pays set isocode='418', isoalpha3='LAO', isoalpha2='LA' where id=95");
	query_patch("update pays set isocode='426', isoalpha3='LSO', isoalpha2='LS' where id=96");
	query_patch("update pays set isocode='428', isoalpha3='LVA', isoalpha2='LV' where id=97");
	query_patch("update pays set isocode='422', isoalpha3='LBN', isoalpha2='LB' where id=98");
	query_patch("update pays set isocode='430', isoalpha3='LBR', isoalpha2='LR' where id=99");
	query_patch("update pays set isocode='343', isoalpha3='LBY', isoalpha2='LY' where id=100");
	query_patch("update pays set isocode='438', isoalpha3='LIE', isoalpha2='LI' where id=101");
	query_patch("update pays set isocode='440', isoalpha3='LTU', isoalpha2='LT' where id=102");
	query_patch("update pays set isocode='442', isoalpha3='LUX', isoalpha2='LU' where id=103");
	query_patch("update pays set isocode='807', isoalpha3='MKD', isoalpha2='MK' where id=104");
	query_patch("update pays set isocode='450', isoalpha3='MDG', isoalpha2='MD' where id=105");
	query_patch("update pays set isocode='458', isoalpha3='MYS', isoalpha2='MY' where id=106");
	query_patch("update pays set isocode='454', isoalpha3='MWI', isoalpha2='MW' where id=107");
	query_patch("update pays set isocode='462', isoalpha3='MDV', isoalpha2='MV' where id=108");
	query_patch("update pays set isocode='466', isoalpha3='MLI', isoalpha2='ML' where id=109");
	query_patch("update pays set isocode='470', isoalpha3='MLT', isoalpha2='MT' where id=110");
	query_patch("update pays set isocode='504', isoalpha3='MAR', isoalpha2='MA' where id=111");
	query_patch("update pays set isocode='584', isoalpha3='MHL', isoalpha2='MH' where id=112");
	query_patch("update pays set isocode='474', isoalpha3='MTQ', isoalpha2='MQ' where id=261");
	query_patch("update pays set isocode='480', isoalpha3='MUS', isoalpha2='MU' where id=113");
	query_patch("update pays set isocode='478', isoalpha3='MRT', isoalpha2='MR' where id=114");
	query_patch("update pays set isocode='175', isoalpha3='MYT', isoalpha2='YT' where id=262");
	query_patch("update pays set isocode='484', isoalpha3='MEX', isoalpha2='MX' where id=115");
	query_patch("update pays set isocode='583', isoalpha3='FSM', isoalpha2='FM' where id=116");
	query_patch("update pays set isocode='498', isoalpha3='MDA', isoalpha2='MD' where id=117");
	query_patch("update pays set isocode='492', isoalpha3='MCO', isoalpha2='MC' where id=118");
	query_patch("update pays set isocode='496', isoalpha3='MNG', isoalpha2='MN' where id=119");
	query_patch("update pays set isocode='508', isoalpha3='MOZ', isoalpha2='MZ' where id=120");
	query_patch("update pays set isocode='516', isoalpha3='NAM', isoalpha2='NA' where id=121");
	query_patch("update pays set isocode='520', isoalpha3='NRU', isoalpha2='NR' where id=122");
	query_patch("update pays set isocode='524', isoalpha3='NPL', isoalpha2='NP' where id=123");
	query_patch("update pays set isocode='558', isoalpha3='NIC', isoalpha2='NI' where id=124");
	query_patch("update pays set isocode='562', isoalpha3='NER', isoalpha2='NE' where id=125");
	query_patch("update pays set isocode='566', isoalpha3='NGA', isoalpha2='NG' where id=126");
	query_patch("update pays set isocode='570', isoalpha3='NIU', isoalpha2='NU' where id=127");
	query_patch("update pays set isocode='578', isoalpha3='NOR', isoalpha2='NO' where id=128");
	query_patch("update pays set isocode='540', isoalpha3='NCL', isoalpha2='NC' where id=265");
	query_patch("update pays set isocode='554', isoalpha3='NZL', isoalpha2='NZ' where id=129");
	query_patch("update pays set isocode='512', isoalpha3='OMN', isoalpha2='OM' where id=130");
	query_patch("update pays set isocode='800', isoalpha3='UGA', isoalpha2='UG' where id=131");
	query_patch("update pays set isocode='860', isoalpha3='UZB', isoalpha2='UZ' where id=132");
	query_patch("update pays set isocode='586', isoalpha3='PAK', isoalpha2='PK' where id=133");
	query_patch("update pays set isocode='591', isoalpha3='PAN', isoalpha2='PA' where id=134");
	query_patch("update pays set isocode='598', isoalpha3='PNG', isoalpha2='PG' where id=135");
	query_patch("update pays set isocode='600', isoalpha3='PRY', isoalpha2='PY' where id=136");
	query_patch("update pays set isocode='528', isoalpha3='NLD', isoalpha2='NL' where id=137");
	query_patch("update pays set isocode='604', isoalpha3='PER', isoalpha2='PE' where id=138");
	query_patch("update pays set isocode='608', isoalpha3='PHL', isoalpha2='PH' where id=139");
	query_patch("update pays set isocode='616', isoalpha3='POL', isoalpha2='PL' where id=140");
	query_patch("update pays set isocode='258', isoalpha3='PYF', isoalpha2='PF' where id=266");
	query_patch("update pays set isocode='620', isoalpha3='PRT', isoalpha2='PT' where id=141");
	query_patch("update pays set isocode='634', isoalpha3='QAT', isoalpha2='QA' where id=142");
	query_patch("update pays set isocode='140', isoalpha3='CAF', isoalpha2='CF' where id=143");
	query_patch("update pays set isocode='214', isoalpha3='DOM', isoalpha2='DO' where id=144");
	query_patch("update pays set isocode='203', isoalpha3='CZE', isoalpha2='CZ' where id=145");
	query_patch("update pays set isocode='638', isoalpha3='REU', isoalpha2='RE' where id=263");
	query_patch("update pays set isocode='642', isoalpha3='ROU', isoalpha2='RO' where id=146");
	query_patch("update pays set isocode='826', isoalpha3='GBR', isoalpha2='GB' where id=147");
	query_patch("update pays set isocode='643', isoalpha3='RUS', isoalpha2='RU' where id=148");
	query_patch("update pays set isocode='646', isoalpha3='RWA', isoalpha2='RW' where id=149");
	query_patch("update pays set isocode='659', isoalpha3='KNA', isoalpha2='KN' where id=150");
	query_patch("update pays set isocode='674', isoalpha3='SMR', isoalpha2='SM' where id=152");
	query_patch("update pays set isocode='670', isoalpha3='VCT', isoalpha2='VC' where id=153");
	query_patch("update pays set isocode='662', isoalpha3='LCA', isoalpha2='LC' where id=151");
	query_patch("update pays set isocode='090', isoalpha3='SLB', isoalpha2='SB' where id=154");
	query_patch("update pays set isocode='222', isoalpha3='SLV', isoalpha2='SV' where id=155");
	query_patch("update pays set isocode='882', isoalpha3='WSM', isoalpha2='WS' where id=156");
	query_patch("update pays set isocode='678', isoalpha3='STP', isoalpha2='ST' where id=157");
	query_patch("update pays set isocode='686', isoalpha3='SEN', isoalpha2='SN' where id=158");
	query_patch("update pays set isocode='690', isoalpha3='SYC', isoalpha2='SC' where id=159");
	query_patch("update pays set isocode='694', isoalpha3='SLE', isoalpha2='SL' where id=160");
	query_patch("update pays set isocode='702', isoalpha3='SGP', isoalpha2='SG' where id=161");
	query_patch("update pays set isocode='703', isoalpha3='SVK', isoalpha2='SK' where id=162");
	query_patch("update pays set isocode='705', isoalpha3='SVN', isoalpha2='SI' where id=163");
	query_patch("update pays set isocode='706', isoalpha3='SOM', isoalpha2='SO' where id=164");
	query_patch("update pays set isocode='729', isoalpha3='SDN', isoalpha2='SD' where id=165");
	query_patch("update pays set isocode='144', isoalpha3='LKA', isoalpha2='LK' where id=166");
	query_patch("update pays set isocode='666', isoalpha3='SPM', isoalpha2='PM' where id=264");
	query_patch("update pays set isocode='752', isoalpha3='SWE', isoalpha2='SE' where id=167");
	query_patch("update pays set isocode='756', isoalpha3='CHE', isoalpha2='CH' where id=168");
	query_patch("update pays set isocode='740', isoalpha3='SUR', isoalpha2='SR' where id=169");
	query_patch("update pays set isocode='748', isoalpha3='SWZ', isoalpha2='SZ' where id=170");
	query_patch("update pays set isocode='760', isoalpha3='SYR', isoalpha2='SY' where id=171");
	query_patch("update pays set isocode='762', isoalpha3='TJK', isoalpha2='TJ' where id=172");
	query_patch("update pays set isocode='834', isoalpha3='TZA', isoalpha2='TZ' where id=173");
	query_patch("update pays set isocode='148', isoalpha3='TCD', isoalpha2='TD' where id=174");
	query_patch("update pays set isocode='764', isoalpha3='THA', isoalpha2='TH' where id=175");
	query_patch("update pays set isocode='768', isoalpha3='TGO', isoalpha2='TG' where id=176");
	query_patch("update pays set isocode='776', isoalpha3='TON', isoalpha2='TO' where id=177");
	query_patch("update pays set isocode='780', isoalpha3='TTO', isoalpha2='TT' where id=178");
	query_patch("update pays set isocode='788', isoalpha3='TUN', isoalpha2='TN' where id=179");
	query_patch("update pays set isocode='795', isoalpha3='TKM', isoalpha2='TM' where id=180");
	query_patch("update pays set isocode='792', isoalpha3='TUR', isoalpha2='TR' where id=181");
	query_patch("update pays set isocode='798', isoalpha3='TUV', isoalpha2='TV' where id=182");
	query_patch("update pays set isocode='804', isoalpha3='UKR', isoalpha2='UA' where id=183");
	query_patch("update pays set isocode='858', isoalpha3='URY', isoalpha2='UY' where id=184");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=268");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=196");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=197");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=198");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=199");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=200");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=201");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=202");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=203");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=204");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=205");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=206");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=207");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=208");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=209");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=210");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=211");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=212");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=213");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=214");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=215");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=216");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=217");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=218");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=219");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=220");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=221");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=222");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=223");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=224");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=225");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=226");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=227");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=228");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=229");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=230");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=231");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=232");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=233");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=234");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=235");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=236");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=237");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=238");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=239");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=240");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=241");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=242");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=243");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=244");
	query_patch("update pays set isocode='840', isoalpha3='USA', isoalpha2='US' where id=245");
	query_patch("update pays set isocode='548', isoalpha3='VUT', isoalpha2='VU' where id=186");
	query_patch("update pays set isocode='336', isoalpha3='VAT', isoalpha2='VA' where id=185");
	query_patch("update pays set isocode='862', isoalpha3='VEN', isoalpha2='VE' where id=187");
	query_patch("update pays set isocode='704', isoalpha3='VNM', isoalpha2='VN' where id=188");
	query_patch("update pays set isocode='876', isoalpha3='WLF', isoalpha2='WF' where id=267");
	query_patch("update pays set isocode='887', isoalpha3='YEM', isoalpha2='YE' where id=189");
	query_patch("update pays set isocode='807', isoalpha3='MKD', isoalpha2='MK' where id=190");
	query_patch("update pays set isocode='180', isoalpha3='COD', isoalpha2='CD' where id=191");
	query_patch("update pays set isocode='894', isoalpha3='ZMB', isoalpha2='ZM' where id=192");
	query_patch("update pays set isocode='716', isoalpha3='ZWE', isoalpha2='ZW' where id=193");

	query_patch("INSERT INTO `variable` (`nom`, `valeur`, `protege`, `cache`) VALUES('un_domaine_par_langue', '0', 0, 1)");
	query_patch("INSERT INTO `variable` (`nom`, `valeur`, `protege`, `cache`) VALUES('action_si_trad_absente', '1', 0, 1)");

	query_patch("INSERT INTO `variable` (`nom`, `valeur`, `protege`, `cache`) VALUES('utilisercacheplugin', '0', 0, 0)");
	query_patch("INSERT INTO `variable` (`nom`, `valeur`, `protege`, `cache`) VALUES('emailscommande', '', 0, 0)");

	query_patch("ALTER TABLE `client` ADD `datecrea` DATETIME NOT NULL AFTER `ref`");

	query_patch("ALTER TABLE `devise` ADD `defaut` int(1) NOT NULL default '0'");
	query_patch("UPDATE `devise` set defaut=1 where id=1");

	@mkdir("$basedir/client/cache/flux", 0777);

	query_patch("update variable set valeur='152' where nom='version'");

?>