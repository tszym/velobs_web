<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();
include_once '../key.php';
include_once '../commonfunction.php';

if (isset($_SESSION['user']) && $_SESSION["type"] == 1) {
    switch (SGBD) {
        case 'mysql':
            if (DEBUG) {
                error_log(date("Y-m-d H:i:s") . " - admin/getObsMails.php\n", 3, LOG_FILE);
            }
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            $sql = "SELECT DISTINCT(poi.id_poi) AS idd,
						poi.*, 
						commune.lib_commune, 
						x(poi.geom_poi) AS X, 
						y(poi.geom_poi) AS Y, 
						subcategory.icon_subcategory,
						subcategory.lib_subcategory,
						priorite.lib_priorite,
						lib_pole,
						lib_status,
						color_status,
						users.lib_users
					FROM poi  ";

            $sql .= "
					INNER JOIN subcategory ON (subcategory.id_subcategory = poi.subcategory_id_subcategory) 
					INNER JOIN commune ON (commune.id_commune = poi.commune_id_commune) 
					INNER JOIN priorite ON (poi.priorite_id_priorite = priorite.id_priorite)
					INNER JOIN pole ON (poi.pole_id_pole = pole.id_pole) 
					INNER JOIN status ON (poi.status_id_status = status.id_status) 
					LEFT JOIN users ON (poi.lastmodif_user_poi = users.id_users)
                    WHERE priorite.lib_priorite in ('A modérer','Priorité 1','Priorité 2','Urgence') 
                    AND poi.delete_poi = 0
                    AND poi.datecreation_poi < '2020-01-01'
                    ORDER BY poi.id_poi";
            // TODO : chek user type and pole

            if (DEBUG) {
                error_log(date("Y-m-d H:i:s") . " - admin/getObsMails.php sql = $sql\n", 3, LOG_FILE);
            }
            $result = mysql_query($sql);
            if (DEBUG) {
                error_log(date("Y-m-d H:i:s") . " - admin/getObsMails.php avant while\n", 3, LOG_FILE);
            }

            $i = 0;
            while ($row = mysql_fetch_array($result)) {
                $arr[$row['mail_poi']]['Obs'][$i]['id_poi'] = $row['id_poi'];
                $arr[$row['mail_poi']]['Obs'][$i]['lib_subcategory'] = stripslashes($row['lib_subcategory']);
                $arr[$row['mail_poi']]['Obs'][$i]['datecreation_poi'] = $row['datecreation_poi'];
                $arr[$row['mail_poi']]['Obs'][$i]['desc_poi'] = nl2br(stripslashes($row['desc_poi']));
                $arr[$row['mail_poi']]['Obs'][$i]['commentfinal_poi'] = stripslashes($row['commentfinal_poi']);
                $arr[$row['mail_poi']]['Obs'][$i]['prop_poi'] = stripslashes($row['prop_poi']);
                $arr[$row['mail_poi']]['Obs'][$i]['photo_poi'] = $row['photo_poi'];
                $arr[$row['mail_poi']]['Obs'][$i]['num_poi'] = stripslashes($row['num_poi']);
                $arr[$row['mail_poi']]['Obs'][$i]['rue_poi'] = stripslashes($row['rue_poi']);
                $arr[$row['mail_poi']]['Obs'][$i]['lib_commune'] = stripslashes($row['lib_commune']);
                $arr[$row['mail_poi']]['Obs'][$i]['lib_priorite'] = stripslashes($row['lib_priorite']);
                $arr[$row['mail_poi']]['Obs'][$i]['lib_pole'] = stripslashes($row['lib_pole']);
                $arr[$row['mail_poi']]['Obs'][$i]['transmission_poi'] = stripslashes($row['transmission_poi']);
                $arr[$row['mail_poi']]['Obs'][$i]['lib_status'] = stripslashes($row['lib_status']);

                $i ++;
            }

            $sql2 = "SELECT comm.*, pr.lib_priorite FROM commentaires comm 
            INNER JOIN poi p on p.id_poi = comm.poi_id_poi 
            INNER JOIN priorite pr ON p.priorite_id_priorite = pr.id_priorite
            WHERE comm.display_commentaires = 'Modéré accepté' 
            AND pr.lib_priorite in ('Priorité 1','Priorité 2','Urgence') 
            AND p.delete_poi = 0
            AND comm.datecreation < '2020-01-01'
            ORDER BY comm.id_poi_id";

            $result2 = mysql_query($sql2);
            while ($row2 = mysql_fetch_array($result2)) {
                $arr[$row2['mail_commentaires']]['Commentaire'][$i]['text_commentaires'] = nl2br($row2['text_commentaires']);
                $arr[$row2['mail_commentaires']]['Commentaire'][$i]['id_poi'] = $row2['poi_id_poi'];
                $arr[$row2['mail_commentaires']]['Commentaire'][$i]['lib_priorite'] = $row2['lib_priorite'];
                $arr[$row2['mail_commentaires']]['Commentaire'][$i]['datecreation'] = $row2['datecreation'];
                $i ++;
            }
            $globalMailcontent = "
            Bonjour,
            
            Vous recevez ce mail car vous avez soumis au moins une observation ou un commentaire sur <a href=\"https://velobs.2p2r.org\" target=\"_blank\">VelObs</a>.
            
            Bonne nouvelle… un grand ménage d’automne est en cours sur <a href=\"https://velobs.2p2r.org\" target=\"_blank\">VelObs</a> !
            
            Certaines zones n’étant couvertes par aucun modérateur bénévole, des observations sont modérées tardivement. Nous comprenons la frustration que cette situation peut impliquer et nous en sommes désolés. Nous espérons à l’avenir, avec votre participation, parvenir à nous maintenir à jour.
            
            Nous requérons donc votre aide pour mettre à jour les observations et faciliter le travail avec les collectivités. Vous pouvez nous signaler, en ajoutant un commentaire sur les observations :<ul><li>celles dont le problème est réglé, ce qui nous permettra de les clore</li><li>celles qui sont toujours en cours quelques mois/années après leur ouverture</li></ul>Vous trouverez en bas de ce courrier la liste des observations et/ou des commentaires que vous avez soumis pour vous aider dans cette tâche. N'hésitez pas à mettre à jour les autres observations qui seraient encore ouvertes et qui selon vous seraient à clôturer.

            L’équipe de modérateurs étant en franc déficit d’effectifs (notamment sur le centre-ville toulousain), si vous avez un peu de temps à donner et pouvez renforcer l’équipe, votre aide sera la bienvenue.
            
            Nous vous remercions d’avance pour votre compréhension et votre soutien dans cette action.
            
            Si vous souhaitez en savoir plus sur <a href=\"https://velobs.2p2r.org\">VelObs</a>, ça se passe <a href=\"https://2p2r.org/articles-divers/page-sommaire/article/velobs\" target=\"_blank\">ici</a>.
            
            Cordialement
            
            L’équipe de modérateurs <a href=\"https://velobs.2p2r.org\" target=\"_blank\">VelObs</a> de 2P2R.
            
            
            PS : si vous ne souhaitez plus recevoir de mails concernant VelObs, merci de nous le préciser par retour de mail afin que nous supprimions votre adresse des observations et des commentaires que vous auriez pu soumettre
            ";
            
            
            $tableObsHeader = "<tr style=\"border: 1px solid black;\"><td style=\"border: 1px solid black;\">\r\nLien vers l'observation sur VelObs</td><td style=\"border: 1px solid black;\">Date de création</td>\r\n<td style=\"border: 1px solid black;\">Priorité donnée par le bénévole 2P2R</td>\r\n<td style=\"border: 1px solid black;\">Description de l'observation</td>\r\n</tr>\r\n";
            $tableCommentHeader = "<tr style=\"border: 1px solid black;\"><td style=\"border: 1px solid black;\">\r\nLien vers l'observation sur VelObs</td><td style=\"border: 1px solid black;\">Date de création du commentaire</td>\r\n<td style=\"border: 1px solid black;\">Commentaire soumis</td>\r\n</tr>\r\n";
            $users = array_keys($arr);
            foreach ($users as $user) {
                $userMailContent = '';
                if (count($arr[$user]) > 0) {
                    echo "<H1>" . $user . "</H1>";
                    if (count($arr[$user]['Obs']) > 0) {
                        $userMailContent .= "<H2>Observations soumises : </H2>\r\n<table style=\"border: 1px solid black;border-collapse: collapse;padding:3px;\">\r\n";
                        $userMailContent .= $tableObsHeader;
                        foreach (array_keys($arr[$user]['Obs']) as $obs) {
                            $userMailContent .= "<tr style=\"border: 1px solid black;\"><td style=\"border: 1px solid black;\">\r\n<a href=\"" . URL . "/index.php?id=" . $arr[$user]['Obs'][$obs]['id_poi'] . "\" target=\"_blank\">" . $arr[$user]['Obs'][$obs]['id_poi'] . "</a></td><td style=\"border: 1px solid black;\">" . $arr[$user]['Obs'][$obs]['datecreation_poi'] . "</td>\r\n<td style=\"border: 1px solid black;\">" . $arr[$user]['Obs'][$obs]['lib_priorite'] . "</td>\r\n<td style=\"border: 1px solid black;\">" . $arr[$user]['Obs'][$obs]['desc_poi'] . "</td>\r\n</tr>\r\n";
                        }
                        $userMailContent .= "</table>\r\n";
                    }
                    if (count($arr[$user]['Commentaire']) > 0) {
                        $userMailContent .= "<H2>Commentaires soumis : </H2>\r\n<table style=\"border: 1px solid black;border-collapse: collapse;padding:3px;\">\r\n";
                        $userMailContent .= $tableCommentHeader;
                        foreach (array_keys($arr[$user]['Commentaire']) as $comment) {
                            if ($arr[$user]['Commentaire'][$comment]['text_commentaires'] != ""){
                            $userMailContent .= "<tr style=\"border: 1px solid black;\">\r\n<td style=\"border: 1px solid black;\">Commentaire sur l'observation <a href=\"" . URL . "/index.php?id=" . $arr[$user]['Commentaire'][$comment]['id_poi'] . "\" target=\"_blank\">" . $arr[$user]['Commentaire'][$comment]['id_poi'] . "</a></td>\r\n<td style=\"border: 1px solid black;\">" . $arr[$user]['Commentaire'][$comment]['datecreation'] . "</td>\r\n<td style=\"border: 1px solid black;\">" . $arr[$user]['Commentaire'][$comment]['text_commentaires'] . "</td>\r\n\r\n</tr>\r\n";
                            }
                            }
                        $userMailContent .= "</table>\r\n";
                    }
                    $userMailContent .= "\r\n";
                    echo $userMailContent;
                   //sendMail($user, "Mise à jour VelObs", nl2br($globalMailcontent) .$userMailContent);
                }
            }

            // echo '{"markers":' . json_encode($arr) . '}';
            mysql_free_result($result);
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

?>
