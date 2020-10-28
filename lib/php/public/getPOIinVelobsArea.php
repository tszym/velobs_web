<?php
session_start();
include_once '../key.php';
if (isset ( $_POST ['lat']) && isset ( $_POST ['lon']) ){
    if (DEBUG) {
        error_log(date("Y-m-d H:i:s") . " - public/getPOIinVelobsArea.php \n", 3, LOG_FILE);
    }
    switch (SGBD) {
			case 'mysql':
			    $link = mysql_connect(DB_HOST,DB_USER,DB_PASS);
			    mysql_select_db(DB_NAME);
			    mysql_query("SET NAMES utf8mb4");
			    if (DEBUG){
					error_log(date("Y-m-d H:i:s") . " " .__FUNCTION__ . " - getPOIinVelobsArea.php -  ".mysql_real_escape_string ($_POST ['lat']).", ".mysql_real_escape_string ($_POST ['lon'])."\n", 3, LOG_FILE);
				}
				//détermination de la commune concernée par croisement du polygone de la commune avec latitude et longitude				
				$commune_id_commune = 99999;
				$sql = "SELECT id_commune FROM commune where st_within(st_geomfromtext('POINT(".mysql_real_escape_string ($_POST ['lon'])." ".mysql_real_escape_string ($_POST ['lat']).")'), geom_commune)";
				if (DEBUG){
					error_log(date("Y-m-d H:i:s") . " " .__FUNCTION__ . " - getPOIinVelobsArea.php -  ".$latitude_poi.", ".$longitude_poi." $sql\n", 3, LOG_FILE);
				}
				$result = mysql_query($sql);
				
				$return['success'] = false;
				$return['pb'] = "L'observation semble être dans une zone non couverte par VelObs. Vérifiez en affichant la limite des communes en bas de cette page. Si votre observation est bien dans une commune déclarée dans VelObs, merci de nous contacter à l'adresse " . MAIL_FROM;
				while ($row = mysql_fetch_array($result)) {
				    
				    $return['success'] = true;
				}
				echo json_encode($return);
												
				break;
			case 'postgresql':
				// TODO
				break;
		}
}
?>