<?php
include_once '../key.php';
include_once '../commonfunction.php';

/*
 * List of functions - getMarkerIcon - updateMarkerIcon - getCategory - updateCategory - createCategory - deleteCategorys - getSubCategory - updateSubCategory - createSubCategory - deleteSubCategorys - getPoi - updatePoi - deletePois - deletePoisCorbeille - getCommune - updateCommune - createCommune - deleteCommunes - getPole - updatePole - createPole - deletePoles - getQuartier - updateQuartier - createQuartier - deleteQuartiers - getPriorite - updatePriorite - createPriorite - deletePriorites - getStatus - updateStatus - createStatus - deleteStatuss - getUser - updateUser - createUser - deleteUsers - resetPhotoPoi - isModerate - updateGeoPoi - resetGeoPoi - updateGeoDefaultMap - createPublicPoi - isPropPublic - normalize - is_in_polygon - getNumPageIdParam - getComments - displayComment - editComment - getPhotos
 */

/*
 * Function name : getMarkerIcon
 * Input :
 * Output :
 * Object : populate icon grid
 * Date : Jan. 18, 2012
 */
function getMarkerIcon($start, $limit)
{
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $sql = "SELECT * FROM iconmarker ORDER BY id_iconmarker ASC";
            $result = mysql_query($sql);
            $nbrows = mysql_num_rows($result);
            $sql .= " LIMIT " . $limit . " OFFSET " . $start;
            $result = mysql_query($sql);
            
            $i = 0;
            if ($nbrows > 0) {
                while ($row = mysql_fetch_array($result)) {
                    $arr[$i]['id_icon'] = $row['id_iconmarker'];
                    $arr[$i]['lib_icon'] = $row['name_iconmarker'];
                    $arr[$i]['urllib_icon'] = $row['urlname_iconmarker'];
                    $arr[$i]['color_icon'] = $row['color_iconmarker'];
                    $arr[$i]['img_icon'] = 'resources/icon/marker/' . $arr[$i]['urllib_icon'] . '.png';
                    $i ++;
                }
                echo '({"total":"' . $nbrows . '","results":' . json_encode($arr) . '})';
            } else {
                echo '({"total":"0", "results":""})';
            }
            mysql_free_result($result);
            mysql_close($link);
            
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : updateMarkerIcon Input : Output : success => '1' / failed => '2' Object : update icon grid Date : Jan. 19, 2012
 */
function updateMarkerIcon()
{
    $id_iconmarker = $_POST['id_icon'];
    $name_iconmarker = $_POST['lib_icon'];
    $color_iconmarker = $_POST['color_icon'];
    
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $sql = "UPDATE iconmarker SET name_iconmarker = '" . $name_iconmarker . "', color_iconmarker = '" . $color_iconmarker . "' WHERE id_iconmarker = " . $id_iconmarker;
            $result = mysql_query($sql);
            if (! $result) {
                echo '2';
            } else {
                echo '1';
            }
            
            mysql_close($link);
            
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : getCategory Input : start, limit Output : json categories Object : populate category grid Date : Jan. 18, 2012
 */
function getCategory($start, $limit)
{
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $sql = "SELECT * FROM category ORDER BY lib_category ASC";
            $result = mysql_query($sql);
            $nbrows = mysql_num_rows($result);
            $sql .= " LIMIT " . $limit . " OFFSET " . $start;
            $result = mysql_query($sql);
            
            $i = 0;
            if ($nbrows > 0) {
                while ($row = mysql_fetch_array($result)) {
                    $arr[$i]['id_category'] = $row['id_category'];
                    $arr[$i]['lib_category'] = stripslashes($row['lib_category']);
                    $arr[$i]['icon_category'] = $row['icon_category'];
                    $arr[$i]['treerank_category'] = $row['treerank_category'];
                    $arr[$i]['display_category'] = $row['display_category'];
                    $i ++;
                }
                echo '({"total":"' . $nbrows . '","results":' . json_encode($arr) . '})';
            } else {
                echo '({"total":"0", "results":""})';
            }
            
            mysql_free_result($result);
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : updateCategory Input : Output : success => '1' / failed => '2' Object : update category grid Date : Jan. 18, 2012
 */
function updateCategory()
{
    $id_category = $_POST['id_category'];
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            if (isset($_POST['display_category'])) {
                $display_category = $_POST['display_category'];
                $sql = "UPDATE category SET display_category = $display_category WHERE id_category = $id_category";
            } else {
                $lib_category = mysql_real_escape_string($_POST['lib_category']);
                $icon_category = mysql_real_escape_string($_POST['icon_category']);
                $sql = "UPDATE category SET lib_category = '$lib_category', icon_category = '$icon_category' WHERE id_category = $id_category";
            }
            $result = mysql_query($sql);
            if (! $result) {
                echo '2';
            } else {
                echo '1';
            }
            
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : createCategory Input : Output : success => '1' / failed => '2' Object : create category Date : Jan. 19, 2012
 */
function createCategory()
{
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $lib_category = mysql_real_escape_string($_POST['lib_category']);
            $icon_category = mysql_real_escape_string($_POST['icon_category']);
            $display_category = $_POST['display_category'];
            
            $sql = "SELECT max(treerank_category) AS max FROM category";
            $result = mysql_query($sql);
            if (mysql_result($result, 0) == NULL) {
                $treerank_category = 0;
            } else {
                $treerank_category = mysql_result($result, 0);
                $treerank_category += 1;
            }
            
            $sql = "INSERT INTO category (lib_category, icon_category, treerank_category, display_category) VALUES ('$lib_category', '$icon_category', $treerank_category, $display_category)";
            $result = mysql_query($sql);
            if (! $result) {
                echo '2';
            } else {
                echo '1';
            }
            
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : deleteCategorys Input : Output : success => '1' / failed => '2' Object : delete category(s) Date : Jan. 19, 2012
 */
function deleteCategorys()
{
    $ids = $_POST['ids'];
    $idcategorys = json_decode(stripslashes($ids));
    
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            if (sizeof($idcategorys) < 1) {
                echo '0';
            } else if (sizeof($idcategorys) == 1) {
                $sql = "DELETE FROM category WHERE id_category = " . $idcategorys[0];
                $result = mysql_query($sql);
            } else {
                $sql = "DELETE FROM category WHERE ";
                for ($i = 0; $i < sizeof($idcategorys); $i ++) {
                    $sql = $sql . "id_category = " . $idcategorys[$i];
                    if ($i < sizeof($idcategorys) - 1) {
                        $sql = $sql . " OR ";
                    }
                }
                $result = mysql_query($sql);
            }
            if (! $result) {
                echo '2';
            } else {
                echo '1';
            }
            
            mysql_free_result($result);
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : getSubCategory Input : start, limit Output : json subcategories Object : populate subcategory grid Date : Jan. 31, 2012
 */
function getSubCategory($start, $limit)
{
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $sql = "SELECT * FROM subcategory INNER JOIN category ON (category.id_category = subcategory.category_id_category) ORDER BY treerank_subcategory ASC";
            $result = mysql_query($sql);
            $nbrows = mysql_num_rows($result);
            $sql .= " LIMIT " . $limit . " OFFSET " . $start;
            $result = mysql_query($sql);
            
            $i = 0;
            if ($nbrows > 0) {
                while ($row = mysql_fetch_array($result)) {
                    $arr[$i]['id_subcategory'] = $row['id_subcategory'];
                    $arr[$i]['lib_subcategory'] = stripslashes($row['lib_subcategory']);
                    $arr[$i]['icon_subcategory'] = $row['icon_subcategory'];
                    $arr[$i]['lib_category'] = stripslashes($row['lib_category']);
                    $arr[$i]['display_subcategory'] = $row['display_subcategory'];
                    $arr[$i]['proppublic_subcategory'] = $row['proppublic_subcategory'];
                    $i ++;
                }
                echo '({"total":"' . $nbrows . '","results":' . json_encode($arr) . '})';
            } else {
                echo '({"total":"0", "results":""})';
            }
            
            mysql_free_result($result);
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : updateSubCategory Input : Output : success => '1' / failed => '2' Object : update subcategory grid Date : Jan. 31, 2012
 */
function updateSubCategory()
{
    $id_subcategory = $_POST['id_subcategory'];
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            if (isset($_POST['display_subcategory'])) {
                $display_subcategory = $_POST['display_subcategory'];
                $sql = "UPDATE subcategory SET display_subcategory = $display_subcategory WHERE id_subcategory = $id_subcategory";
                $result = mysql_query($sql);
            } else if (isset($_POST['proppublic_subcategory'])) {
                $proppublic_subcategory = $_POST['proppublic_subcategory'];
                $sql = "UPDATE subcategory SET proppublic_subcategory = $proppublic_subcategory WHERE id_subcategory = $id_subcategory";
                $result = mysql_query($sql);
            } else {
                $lib_subcategory = mysql_real_escape_string($_POST['lib_subcategory']);
                $icon_subcategory = mysql_real_escape_string($_POST['icon_subcategory']);
                if (is_numeric($_POST['category_id_category'])) {
                    $category_id_category = $_POST['category_id_category'];
                    
                    $sql2 = "SELECT category_id_category FROM subcategory WHERE id_subcategory = " . $id_subcategory;
                    $result2 = mysql_query($sql2);
                    
                    if (mysql_result($result2, 0) != $category_id_category) {
                        $sql3 = "SELECT max(treerank_subcategory) AS max FROM subcategory WHERE category_id_category = " . $category_id_category;
                        $result3 = mysql_query($sql3);
                        if (mysql_result($result3, 0) != NULL) {
                            $treerank_subcategory = mysql_result($result3, 0);
                            $treerank_subcategory += 1;
                        } else {
                            $treerank_subcategory = 0;
                        }
                        
                        // modifier les treerank des autres subcategories
                        $sql = "SELECT treerank_subcategory FROM subcategory WHERE id_subcategory = " . $id_subcategory;
                        $result = mysql_query($sql);
                        $currentTreerank = mysql_result($result, 0);
                        $sql = "UPDATE subcategory SET treerank_subcategory = (treerank_subcategory - 1) WHERE category_id_category = " . mysql_result($result2, 0) . " AND id_subcategory <> " . $id_subcategory . " AND treerank_subcategory > " . $currentTreerank;
                        $result = mysql_query($sql);
                        
                        $sql = "UPDATE subcategory SET lib_subcategory = '$lib_subcategory', icon_subcategory = '$icon_subcategory', category_id_category = $category_id_category, treerank_subcategory = $treerank_subcategory WHERE id_subcategory = $id_subcategory";
                        $result = mysql_query($sql);
                    } else {
                        $sql = "UPDATE subcategory SET lib_subcategory = '$lib_subcategory', icon_subcategory = '$icon_subcategory', category_id_category = $category_id_category WHERE id_subcategory = $id_subcategory";
                        $result = mysql_query($sql);
                    }
                } else {
                    $sql = "UPDATE subcategory SET lib_subcategory = '$lib_subcategory', icon_subcategory = '$icon_subcategory' WHERE id_subcategory = $id_subcategory";
                    $result = mysql_query($sql);
                }
            }
            
            if (! $result) {
                echo '2';
            } else {
                echo '1';
            }
            
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : createSubCategory Input : Output : success => '1' / failed => '2' Object : create subcategory Date : Jan. 31, 2012
 */
function createSubCategory()
{
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $lib_subcategory = mysql_real_escape_string($_POST['lib_subcategory']);
            $icon_subcategory = mysql_real_escape_string($_POST['icon_subcategory']);
            
            $category_id_category = $_POST['category_id_category'];
            $display_subcategory = $_POST['display_subcategory'];
            $proppublic_subcategory = $_POST['proppublic_subcategory'];
            
            $sql = "SELECT max(treerank_subcategory) AS max FROM subcategory WHERE category_id_category = " . $category_id_category;
            $result = mysql_query($sql);
            if (mysql_result($result, 0) != NULL) {
                $treerank_subcategory = 0;
            } else {
                $treerank_subcategory = mysql_result($result, 0);
                $treerank_subcategory += 1;
            }
            
            $sql = "INSERT INTO subcategory (lib_subcategory, icon_subcategory, treerank_subcategory, category_id_category, display_subcategory, proppublic_subcategory) VALUES ('$lib_subcategory', '$icon_subcategory', $treerank_subcategory, $category_id_category , $display_subcategory, $proppublic_subcategory)";
            
            $result = mysql_query($sql);
            if (! $result) {
                echo '2';
            } else {
                echo '1';
            }
            
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : deleteSubCategorys Input : Output : success => '1' / failed => '2' Object : delete subcategory(s) Date : Jan. 19, 2012
 */
function deleteSubCategorys()
{
    $ids = $_POST['ids'];
    $idsubcategorys = json_decode(stripslashes($ids));
    
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            if (sizeof($idsubcategorys) < 1) {
                echo '0';
            } else if (sizeof($idsubcategorys) == 1) {
                $sql = "DELETE FROM subcategory WHERE id_subcategory = " . $idsubcategorys[0];
                $result = mysql_query($sql);
            } else {
                $sql = "DELETE FROM subcategory WHERE ";
                for ($i = 0; $i < sizeof($idsubcategorys); $i ++) {
                    $sql = $sql . "id_subcategory = " . $idsubcategorys[$i];
                    if ($i < sizeof($idsubcategorys) - 1) {
                        $sql = $sql . " OR ";
                    }
                }
                $result = mysql_query($sql);
            }
            if (! $result) {
                echo '2';
            } else {
                echo '1';
            }
            
            mysql_free_result($result);
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : getPoi Input : Output : Object : populate poi grid Date : May 2, 2012
 */
function getPoi($start, $limit, $asc, $sort, $dir)
{
    switch (SGBD) {
        case 'mysql':
            if (DEBUG) {
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " - getPoi \n", 3, LOG_FILE);
            }
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            $whereClause = ' delete_poi = FALSE ';
            if (isset($_POST["basket"])) {
                $whereClause = " delete_poi = TRUE ";
            }
            
            if ($_SESSION["type"] == 1 && isset($_POST["priority"])) {
                $whereClause .= ' AND priorite.id_priorite = ' . $_POST["priority"];
            } elseif ($_SESSION["type"] == 2) {
                $whereClause .= ' AND moderation_poi = 1 
							AND display_poi = 1 
							AND commune_id_commune IN (' . str_replace(';', ',', $_SESSION['territoire']) . ') AND delete_poi = FALSE 
							AND priorite.non_visible_par_collectivite = 0 ';
            } elseif ($_SESSION["type"] == 3) {
                $whereClause .= ' AND moderation_poi = 1 
							AND display_poi = 1 
							AND transmission_poi = 1 
							AND delete_poi = FALSE 
							AND poi.pole_id_pole = ' . $_SESSION["pole"] . ' 
							AND priorite.non_visible_par_collectivite = 0 ';
            } 
//             elseif ($_SESSION["type"] == 4) {
//                 $whereClause .= ' AND poi.pole_id_pole IN (' . $_SESSION["pole"] . ') ';
//             }
            $sql = "SELECT poi.*, subcategory.lib_subcategory, commune.lib_commune, pole.lib_pole, quartier.lib_quartier, priorite.lib_priorite, status.lib_status, x(poi.geom_poi) AS X, y(poi.geom_poi) AS Y FROM poi INNER JOIN subcategory ON (subcategory.id_subcategory = poi.subcategory_id_subcategory) INNER JOIN commune ON (commune.id_commune = poi.commune_id_commune) INNER JOIN pole ON (pole.id_pole = poi.pole_id_pole) INNER JOIN quartier ON (quartier.id_quartier = poi.quartier_id_quartier) INNER JOIN priorite ON (priorite.id_priorite = poi.priorite_id_priorite) INNER JOIN status ON (status.id_status = poi.status_id_status) WHERE $whereClause ";
            
            $sql .= " ORDER BY ";
            if ($sort == '0' && $dir == '0') {
                switch ($asc) {
                    case 'subcategory':
                        $sql .= " lib_subcategory ASC";
                        break;
                    case 'lib':
                        $sql .= " lib_poi ASC";
                        break;
                    default:
                        $sql .= " id_poi DESC";
                        break;
                }
            } else {
                $sql .= $sort . " " . $dir;
            }
            
            $result = mysql_query($sql);
            $nbrows = mysql_num_rows($result);
            $sql .= " LIMIT " . $limit . " OFFSET " . $start;
            $result = mysql_query($sql);
            if (DEBUG) {
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " - getPoi sql $sql retourne $nbrows (s'il n'y avait pas de limites)\n", 3, LOG_FILE);
            }
            $i = 0;
            if ($nbrows > 0) {
                while ($row = mysql_fetch_array($result)) {
                    $arr[$i]['id_poi'] = $row['id_poi'];
                    $arr[$i]['lib_poi'] = stripslashes($row['lib_subcategory']);
                    $arr[$i]['adherent_poi'] = stripslashes($row['adherent_poi']);
                    $arr[$i]['num_poi'] = stripslashes($row['num_poi']);
                    $arr[$i]['rue_poi'] = stripslashes($row['rue_poi']);
                    $arr[$i]['tel_poi'] = stripslashes($row['tel_poi']);
                    if ($_SESSION["type"] == 4 || $_SESSION["type"] == 1) {
                        $arr[$i]['mail_poi'] = stripslashes($row['mail_poi']);
                    } else {
                        $arr[$i]['mail_poi'] = "******";
                    }
                    $arr[$i]['desc_poi'] = stripslashes($row['desc_poi']);
                    $arr[$i]['prop_poi'] = stripslashes($row['prop_poi']);
                    $arr[$i]['observationterrain_poi'] = stripslashes($row['observationterrain_poi']);
                    $arr[$i]['reponse_collectivite_poi'] = stripslashes($row['reponse_collectivite_poi']);
                    $arr[$i]['reponsepole_poi'] = stripslashes($row['reponsepole_poi']);
                    $arr[$i]['commentfinal_poi'] = stripslashes($row['commentfinal_poi']);
                    $arr[$i]['display_poi'] = $row['display_poi'];
                    $arr[$i]['fix_poi'] = $row['fix_poi'];
                    $arr[$i]['traiteparpole_poi'] = $row['traiteparpole_poi'];
                    $arr[$i]['moderation_poi'] = $row['moderation_poi'];
                    $arr[$i]['transmission_poi'] = $row['transmission_poi'];
                    $arr[$i]['photo_poi'] = $row['photo_poi'];
                    $arr[$i]['datecreation_poi'] = $row['datecreation_poi'];
                    $arr[$i]['datefix_poi'] = $row['datefix_poi'];
                    $arr[$i]['lib_subcategory'] = stripslashes($row['lib_subcategory']);
                    $arr[$i]['lib_commune'] = stripslashes($row['lib_commune']);
                    $arr[$i]['lib_pole'] = stripslashes($row['lib_pole']);
                    $arr[$i]['lib_quartier'] = stripslashes($row['lib_quartier']);
                    $arr[$i]['lib_priorite'] = stripslashes($row['lib_priorite']);
                    $arr[$i]['lib_status'] = stripslashes($row['lib_status']);
                    $arr[$i]['geolocatemode_poi'] = $row['geolocatemode_poi'];
                    $arr[$i]['longitude_poi'] = $row['X'];
                    $arr[$i]['latitude_poi'] = $row['Y'];
                    $arr[$i]['lastdatemodif_poi'] = $row['lastdatemodif_poi'];
                    
                    $sql2 = "SELECT * FROM commentaires WHERE poi_id_poi = " . $row['id_poi'];
                    $result2 = mysql_query($sql2);
                    $nb2 = mysql_num_rows($result2);
                    
                    $j = 0;
                    $comments = '<b>Commentaires</b><br />';
                    $acceptedCommentCount = 0;
                    while ($row2 = mysql_fetch_array($result2)) {
                        
                        if ($_SESSION["type"] == 4 || $_SESSION["type"] == 1) {
                            $color = 'green';
                            if ($row2['display_commentaires'] == 'Non modéré') {
                                $color = 'orange';
                            } else if ($row2['display_commentaires'] == 'Modéré refusé') {
                                $color = 'red';
                            }
                            $comments .= '<ul><li style="color:' . $color . ';">' . ($j + 1) . '. ';
                            if ($row2['datecreation'] != '0000-00-00 00:00:00') {
                                $comments .= 'Ajouté le ' . $row2['datecreation'] . '';
                            } else {
                                $comments .= 'Ajouté le ?';
                            }
                            if ($row2['mail_commentaires'] != '') {
                                $comments .= ", par " . $row2['mail_commentaires'] . " : ";
                            } else {
                                $comments .= ', par ? : ';
                            }
                            
                            $comments .= nl2br($row2['text_commentaires']) . '</i></li>';
                            if ($row2['url_photo'] != "") {
                                $comments .= '<li><a href="./resources/pictures/' . $row2['url_photo'] . '" target="_blank">Photo associée</a></li>';
                            }
                            $comments .= '</ul><hr />';
                        } else if ($_SESSION["type"] == 2 || $_SESSION["type"] == 3) {
                            if ($row2['display_commentaires'] == 'Modéré accepté') {
                                $acceptedCommentCount ++;
                                $comments .= '<ul><li>' . $acceptedCommentCount . '. ';
                                if ($row2['datecreation'] != '0000-00-00 00:00:00') {
                                    $comments .= 'Ajouté le ' . $row2['datecreation'] . ' : ';
                                } else {
                                    $comments .= 'Ajouté le ? : ';
                                }
                                $comments .= nl2br($row2['text_commentaires']) . '</i></li>';
                                if ($row2['url_photo'] != "") {
                                    $comments .= '<li><a href="./resources/pictures/' . $row2['url_photo'] . '" target="_blank">Photo associée</a></li>';
                                }
                                $comments .= '</ul><hr />';
                            }
                        }
                        
                        $j ++;
                    }
                    $arr[$i]['num_comments'] = $j;
                    $arr[$i]['num_accepted_comments'] = $acceptedCommentCount;
                    if ($j > 0) {
                        if ($_SESSION["type"] == 4 || $_SESSION["type"] == 1) {
                            $comments .= "Cliquer sur le bouton \"Commentaires\" ci-dessous pour le(s) modérer.";
                        } else {
                            $arr[$i]['num_comments'] = $acceptedCommentCount;
                            if ($acceptedCommentCount > 0) {
                                $comments .= "Cliquer sur le bouton \"Commentaires\" ci-dessous pour le(s) afficher en vue tableau.";
                            } else {
                                $comments .= "Encore aucun commentaire associé";
                            }
                        }
                    } else {
                        $comments .= "Encore aucun commentaire associé";
                    }
                    $arr[$i]['comments'] = stripslashes($comments);
                    $i ++;
                }
                echo '({"total":"' . $nbrows . '","results":' . json_encode($arr) . '})';
            } else {
                echo '({"total":"0", "results":""})';
            }
            mysql_free_result($result);
            mysql_close($link);
            
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : updatePoi //methode appelée par poi1 à 4, quand quelqu'un modifie une information via la datatable sur l'interface d'administration Input : Output : success => '1' / pas de modification => 2 : failed => '3' Object : update poi grid Date : July 13, 2015
 */
function updatePoi()
{
    $id_poi = stripslashes($_POST['id_poi']);
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $arrayObs = getObservationDetailsInArray($id_poi);
            
            $arrayDetailsAndUpdateSQL = getObservationDetailsInString($arrayObs);
            if (DEBUG) {
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " - Il y a " . count($arrayDetailsAndUpdateSQL) . " infos chargées pour l'update de l'obs $id_poi \n", 3, LOG_FILE);
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " - updateObsBoolean " . $arrayDetailsAndUpdateSQL['updateObsBoolean'] . " pour l'update de l'obs $id_poi \n", 3, LOG_FILE);
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " - sqlUpdate " . $arrayDetailsAndUpdateSQL['sqlUpdate'] . " pour l'update de l'obs $id_poi \n", 3, LOG_FILE);
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " - detailObservationString " . $arrayDetailsAndUpdateSQL['detailObservationString'] . " pour l'update de l'obs $id_poi \n", 3, LOG_FILE);
            }
            if ($arrayDetailsAndUpdateSQL['updateObsBoolean']) {
                $sql = "UPDATE poi SET " . $arrayDetailsAndUpdateSQL['sqlUpdate'] . " WHERE id_poi = " . $id_poi;
                
                $mails = array();
                // usertype_id_usertype : 1=Admin, 2=comcom, 3=pole tech, 4=moderateur
                // mail à la comcom si un pole a édité le champ 'Réponse pole'
                $poleedit = 0;
                if (isset($_POST['poleedit'])) {
                    $poleedit = mysql_real_escape_string($_POST['poleedit']);
                }
                
                // mail aux comptes comcom du territoire concerné par l'observation et aux modérateurs
                if ($poleedit == 1) {
                    $subject = 'Modification de l\'observation n°' . $arrayObs['id_poi'] . ' par le pole ' . $arrayObs['lib_pole'];
                    $message = 'Bonjour !<br />
Le pole ' . $arrayObs['lib_pole'] . ' a modifié l\'observation n°' . $arrayObs['id_poi'] . "<br />\n";
                    $message .= "Lien vers la modération : " . URL . '/admin.php?id=' . $arrayObs['id_poi'] . "\n" . $arrayDetailsAndUpdateSQL['detailObservationString'] . "<br />\n";
                    
                    $message .= "Cordialement, l'Association " . VELOBS_ASSOCIATION . " :)<br />";
                    $whereClause = "(u.usertype_id_usertype = 2 AND ulp.territoire_id_territoire = " . $arrayObs['territoire_id_territoire'] . ") OR (u.usertype_id_usertype = 4 AND ulp.num_pole = " . $arrayObs['pole_id_pole'] . ")";
                    $mailsComComModo = getMailsToSend($whereClause, $subject, $message,$arrayObs['id_poi']);
                }
                // Priorités et leur iD
                // "1","Priorité 1"
                // "2","Priorité 2"
                // "4","A modérer"
                // "6","Clôturé"
                // "7","Refusé par l'association" : non affiché sur l'interface publique
                // "8","Urgence" : non affiché sur l'interface publique
                // "12","Refusé par la collectivité"
                // "15","Doublon"
                // on ne traite priorite_id_priorite que si il a été mis à jour 
                $checkModerateBoxOnPOIGrid = 0;
                $updatePOI = 1; // flag permettant de savoir si on doit mettre à jour l'observation (en fonction de règles définies ci-dessous) et envoyer un mail au contributeur
                $returnCode = 0;
                if (isset($_POST['priorite_id_priorite']) && is_numeric($_POST['priorite_id_priorite']) && $arrayObs['priorite_id_priorite'] != $_POST['priorite_id_priorite']) {
                    $checkModerateBoxOnPOIGrid = 1;
                    $new_id_priorite = $_POST['priorite_id_priorite'];
                    $sqlPriorite = "SELECT * FROM priorite WHERE id_priorite = " . $new_id_priorite;
                    $resultPriorite = mysql_query($sqlPriorite);
                    $priorite = mysql_fetch_array($resultPriorite);
                    $subject = $priorite['priorite_sujet_email'];
                    $message = str_replace("#VELOBS_ASSOCIATION#", VELOBS_ASSOCIATION, $priorite['priorite_corps_email']) . "\n" . $arrayDetailsAndUpdateSQL['detailObservationString'] . "\n" . $signature;
                    if ($priorite['besoin_commentaire_association'] && ($arrayObs['commentfinal_poi'] == '' && $_POST['commentfinal_poi'] == '')) {
                        $updatePOI = 0;
                        $returnCode = 10;
                    }
                    $mailsFollowers = getMailsToSendFromVotesAndComments($id_poi, $subject, "Vous recevez ce mail car vous avez souhaité suivre l'évolution de cette observation. Message envoyé à la personne qui a remonté l'observation : \n".$message);
                    if ($updatePOI == 1 && $subject != "") {
                        $mailArray = [
                            $arrayObs['mail_poi'],
                            'Contributeur',
                            $subject,
                            $message
                        ];
                        array_push($mails, $mailArray);
                    }
                }
                // si la modif a été faite par la comcom ou un pole technique
                if (isset($_SESSION['type']) && ($_SESSION['type'] == 2 || $_SESSION['type'] == 3)) {
                    // mail à l'association vélo pour prévenir d'une modif + mail au(x) responsable(s) du pole
                    $subject = 'Modification sur l\'observation n°' . $arrayObs['id_poi'] . ' - ' . $arrayObs['lib_pole'];
                    $message = 'Bonjour !<br />
La collectivité ou un pôle technique (compte ' . $_SESSION['user'] . ') a modifié l\'observation n°' . $arrayObs['id_poi'] . ' du pole ' . $arrayObs['lib_pole'] . "<br />\n
Veuillez consulter l'interface d'administration pour consulter les informations relatives.<br />
Lien vers la modération : " . URL . '/admin.php?id=' . $arrayObs['id_poi'] . "<br />\n" . $arrayDetailsAndUpdateSQL['detailObservationString'] . '
' . $signature;
                    // usertype_id_usertype : 1=Admin, 2=comcom, 3=pole tech, 4=moderateur
                    // mail aux admins velobs et aux modérateurs du pole concerné par l'observation
                    $whereClause = " u.usertype_id_usertype = 1 OR (u.usertype_id_usertype = 4 AND ulp.num_pole = " . $arrayObs['pole_id_pole'] . ")";
                    $mailsAsso = getMailsToSend($whereClause, $subject, $message,$arrayObs['id_poi']);
                }
                // si une règle de modération n'est pas respectée, on ne met pas à jour l'observation et on n'envoie pas de mail, et on retourne un code d'erreur
                if ($updatePOI == 0) {
                    echo $returnCode;
                } else {
                    // on met à jour l'observation
                    if (DEBUG) {
                        error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " - sql " . $sql . " pour l'update de l'obs $id_poi \n", 3, LOG_FILE);
                    }
                    $result = mysql_query($sql);
                    // en cas d'erreur sur la requête,; on envoie un mail d'information à l'administrateur
                    if (! $result) {
                        if (DEBUG) {
                            error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " Erreur " . mysql_errno($link) . " : " . mysql_error($link), 3, LOG_FILE);
                        }
                        sendMail(MAIL_FROM, "Erreur méthode updatePoi", "Erreur = " . mysql_error($link) . ", requête = " . $sql);
                        echo '3';
                    } else {
                        // si la mise à jour de l'observation s'est bien déroulée, on envoie les mails
                        if (isset($mailsComComModo)) {
                            $succes = sendMails($mailsComComModo);
                        }
                        if (isset($mailsAsso)) {
                            $succes = sendMails($mailsAsso);
                        }
                        if (isset($mails)) {
                            $succes = sendMails($mails);
                        }
                        if (isset($mailsFollowers)) {
                            $succes = sendMails($mailsFollowers);
                        }
                        // on retourne un code de succès à l'interface
                        if ($checkModerateBoxOnPOIGrid) {
                            echo 4;
                        } else {
                            echo 1;
                        }
                    }
                }
                // }
            } else {
                // aucune mise à jour n'a été effectuée, car aucune information n'a été modifiée
                echo 2;
            }
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : fusionPoi //methode appelée par poi1 et 4, pour fusionner 2 observations https://github.com/2p2r/velobs_web/issues/262#issue-747196059
 */
function fusionPoi()
{
    $id_poi1 = stripslashes($_POST['id_poi1']);
    $id_poi2 = stripslashes($_POST['id_poi2']);
    
    if (DEBUG) {
        error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . ", id_poi1 : $id_poi1 and id_poi2 = id_poi2\n", 3, LOG_FILE);
    }
    
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            //vérifie la validité des id des observations à fusionner 
            $newerPoi = $id_poi1;
            $olderPoi = $id_poi2;
            if (DEBUG) {
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . ", newerPoi : $newerPoi and olderPoi = $olderPoi\n", 3, LOG_FILE);
            }
            
            if ($id_poi2 > $id_poi1){
                $newerPoi = $id_poi2;
                $olderPoi = $id_poi1;
            }
            
            $arrayNewerPoi = getObservationDetailsInArray($newerPoi);
            $arrayOlderPoi = getObservationDetailsInArray($olderPoi);
            $return = array();
            if(!$arrayNewerPoi || !$arrayOlderPoi ){
                $return['success'] = false;
                $return['msg'] = "Au moins une des observations ".$newerPoi." / ".$olderPoi." semble ne pas exister. Merci de vérifier les numéros.";
            }
            if ($id_poi1 == $id_poi2){
                $return['success'] = false;
                $return['msg'] = "Les numéros des observations à fusionner doivent être différents. Merci de vérifier les numéros.";
            }
            //en cas d'incohérence, on retourne un message d'erreur 
            if (array_key_exists('success',$return)){
                echo json_encode($return);
                exit;
            }
            if (DEBUG) {
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . ", Observations récupérées\n", 3, LOG_FILE);
            }
            //les id sont cohérents, on fusionne les 2 observations dans la plus ancienne
            $message = '';
            //si A n'a pas de photo et B en a une, de la reprendre
            if ($arrayOlderPoi['photo_poi'] == "" && $arrayNewerPoi['photo_poi'] != ""){
                
                $sql = "UPDATE poi SET photo_poi = '".$arrayNewerPoi['photo_poi']."' WHERE id_poi = " . $olderPoi;
                $result = mysql_query($sql);
                if (DEBUG) {
                    error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . ", copie de photo de $newerPoi dans $olderPoi $sql\n", 3, LOG_FILE);
                }
                $message .= "Copie de la photo de $newerPoi dans $olderPoi\n";
            }
                if (DEBUG) {
                    error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . ", copie de photo de $newerPoi dans $olderPoi\n", 3, LOG_FILE);
                }
                $message .= "Ajout d'un commentaire sur $olderPoi avec la proposition faite dans $newerPoi, et la photo le cas échéant\n";
                //de créer un commentaire sur A avec l'e-mail de l'observateur de B (en mode silencieux) du type "{B.observation}.\nProposition : {B.proposition}" et, si A avait déjà une photo (étape précédente), la photo de B
                $sql = "INSERT INTO commentaires (text_commentaires, display_commentaires, mail_commentaires, poi_id_poi,url_photo) VALUES ('Ajout de la photo et de la proposision de l\observation $newerPoi lors de la fusion d\'observations. Proposition : ".$arrayNewerPoi['prop_poi']."', 'Modéré accepté', '".$arrayNewerPoi['mail_poi']."',$olderPoi,'".$arrayNewerPoi['photo_poi']."')";
                $result = mysql_query($sql);
                if (DEBUG) {
                    error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . ", sql : $sql\n", 3, LOG_FILE);
                }
                $id_commentaire = mysql_insert_id();
                
                if (! $result) {
                    $return['success'] = false;
                    $return['pb'] = "Erreur lors de l'ajout du commentaire.";
                } else {
            }
            $message .= "Ajout d'un vote sur $olderPoi avec le mail de la personne qui a créé l'observation $newerPoi\n";
            //d'ajouter un vote à A avec l'e-mail de l'observateur de B (en mode silencieux mais en activant les notifications ultérieures)
            $sql = "INSERT INTO support_poi (poi_poi_id, support_poi_mail, support_poi_follow) VALUES ($olderPoi,'".$arrayNewerPoi['mail_poi']."', 1)";
            $result = mysql_query($sql);
            if (DEBUG) {
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . ", sql : $sql\n", 3, LOG_FILE);
            }
            
            //de copier dans A tous les commentaires de B
            $sql2 = "SELECT * FROM commentaires WHERE poi_id_poi = " . $newerPoi;
            $result2 = mysql_query($sql2);
            if (DEBUG) {
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . ", sql : $sql2\n", 3, LOG_FILE);
            }
            $j=0;
            while ($row2 = mysql_fetch_array($result2)) {
                $sql = "INSERT INTO commentaires (text_commentaires, display_commentaires, mail_commentaires, poi_id_poi,url_photo, datecreation, lastmodif_comment, lastmodif_user_comment) VALUES ('".$row2['text_commentaires']." (Commentaire issu de l\'observation $newerPoi, copié ici après fusion par un modérateur).', '".$row2['display_commentaires']."', '".$row2['mail_commentaires']."',$olderPoi,'".$row2['url_photo']."','".$row2['datecreation']."','".$row2['lasmodif_comment']."','".$row2['lasmodif_user_comment']."')";
                $result = mysql_query($sql);
                if (DEBUG) {
                    error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . ", sql : $sql\n", 3, LOG_FILE);
                }
                $j++;
            }
            if ($j>0){
                $message .= "Ajout de(s) $j commentaire(s) de $newerPoi à $olderPoi \n";
            }
            
            $j=0;
            //de copier sur A tous les votes de B
            $sql2 = "SELECT * FROM support_poi WHERE poi_poi_id = " . $newerPoi;
            $result2 = mysql_query($sql2);
            if (DEBUG) {
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . ", sql : $sql2\n", 3, LOG_FILE);
            }
            
            while ($row2 = mysql_fetch_array($result2)) {
                $sql = "INSERT INTO support_poi (poi_poi_id, support_poi_mail, support_poi_follow) VALUES ($olderPoi,'".$row2['support_poi_mail']."', '".$row2['support_poi_follow']."')";
                $result = mysql_query($sql);
                $j++;
                if (DEBUG) {
                    error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . ", sql : $sql\n", 3, LOG_FILE);
                }
            }
            if ($j>0){
                $message .= "Ajout de(s) $j vote(s) de $newerPoi à $olderPoi \n";
            }
            
            
            $message .= "Ajout d'un commentaire à $newerPoi indiquant qu'il a été fusionné avec $olderPoi \n";
            //d'ajouter un commentaire à B : "Observation fusionnée avec #{A.id} : https://velobs.2p2r.org/index.php?id={A.id}" et de marquer B comme "Doublon".
            $sql = "INSERT INTO commentaires (text_commentaires, display_commentaires, mail_commentaires, poi_id_poi,url_photo) VALUES ('Observation fusionnée avec ".$olderPoi.". La priorité de cette observation doit être passée en Doublon', 'Modéré accepté', '',$newerPoi,'')";
            $result = mysql_query($sql);
            if (DEBUG) {
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . ", sql : $sql\n", 3, LOG_FILE);
            }
            
            $subject = 'Fusion des observations ' . $olderPoi . ' et ' . $newerPoi ;
            $messageMail = 'Bonjour !<br />
Les observations '. $olderPoi . ' et ' . $newerPoi . ' viennent d\être fusionnées. Les actions suivantes ont automatiquement été réalisées :'.
nl2br($message).'
Lien vers la modération : ' . URL . '/admin.php?id=' . $newerPoi . " fusionné dans ".URL . '/admin.php?id=' . $olderPoi."<br />\n" .  '
' . $signature;
            // usertype_id_usertype : 1=Admin, 2=comcom, 3=pole tech, 4=moderateur
            // mail aux admins velobs et aux modérateurs du pole concerné par l'observation
            $whereClause = " u.usertype_id_usertype = 1 OR (u.usertype_id_usertype = 4 AND ulp.num_pole = " . $arrayOlderPoi['pole_id_pole'] . ")";
            $mailsAsso = getMailsToSend($whereClause, $subject, $messageMail,$olderPoi);
            $succes = sendMails($mailsAsso);
            $return['success'] = true;
            $return['msg'] = "Les observations $olderPoi et $newerPoi ont été fusionnées. L'observation plus ancienne a reçu les observations et les votes de l'observation plus récente le cas échéant. L'observation plus récente doit être passée en priorité \"Doublon\" manuellement.";
            echo json_encode($return);
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : deletePois Input : Output : success => '1' / failed => '2' Object : delete poi(s) Date : Jan. 22, 2012
 */
function deletePois()
{
    $ids = $_POST['ids'];
    $idpois = json_decode(stripslashes($ids));
    
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            if (sizeof($idpois) < 1) {
                echo '0';
            } else if (sizeof($idpois) == 1) {
                $sql = "DELETE FROM poi WHERE id_poi = " . $idpois[0];
                $result = mysql_query($sql);
            } else {
                $sql = "DELETE FROM poi WHERE ";
                for ($i = 0; $i < sizeof($idpois); $i ++) {
                    $sql = $sql . "id_poi = " . $idpois[$i];
                    if ($i < sizeof($idpois) - 1) {
                        $sql = $sql . " OR ";
                    }
                }
                $result = mysql_query($sql);
            }
            if (! $result) {
                echo '2';
            } else {
                echo '1';
            }
            
            mysql_free_result($result);
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : deletePoisCorbeille Input : Output : success => '1' / failed => '2' Object : delete poi(s) Date : Jan. 22, 2012
 */
function deletePoisCorbeille()
{
    $ids = $_POST['ids'];
    $idpois = json_decode(stripslashes($ids));
    
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            if (sizeof($idpois) < 1) {
                echo '0';
            } else if (sizeof($idpois) == 1) {
                // $sql = "DELETE FROM poi WHERE id_poi = ".$idpois[0];
                $sql = "UPDATE poi SET delete_poi = TRUE WHERE id_poi = " . $idpois[0];
                $result = mysql_query($sql);
            } else {
                $sql = "DELETE FROM poi WHERE ";
                for ($i = 0; $i < sizeof($idpois); $i ++) {
                    /*
                     * $sql = $sql . "id_poi = ".$idpois[$i]; if ($i < sizeof($idpois) - 1){ $sql = $sql . " OR "; }
                     */
                    
                    $sql = "UPDATE poi SET delete_poi = TRUE WHERE id_poi = " . $idpois[$i];
                    $result = mysql_query($sql);
                }
                // $result = mysql_query($sql);
            }
            if (! $result) {
                echo '2';
            } else {
                echo '1';
            }
            
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : getCommune Input : start, limit Output : json communes Object : populate commune grid Date : Jan. 24, 2012
 */
function getCommune($start, $limit)
{
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $sql = "SELECT * FROM commune ORDER BY lib_commune ASC";
            $result = mysql_query($sql);
            $nbrows = mysql_num_rows($result);
            $sql .= " LIMIT " . $limit . " OFFSET " . $start;
            $result = mysql_query($sql);
            
            $i = 0;
            if ($nbrows > 0) {
                while ($row = mysql_fetch_array($result)) {
                    $arr[$i]['id_commune'] = $row['id_commune'];
                    $arr[$i]['lib_commune'] = stripslashes($row['lib_commune']);
                    $i ++;
                }
                echo '({"total":"' . $nbrows . '","results":' . json_encode($arr) . '})';
            } else {
                echo '({"total":"0", "results":""})';
            }
            
            mysql_free_result($result);
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : updateCommune Input : Output : success => '1' / failed => '2' Object : update commune grid Date : Jan. 24, 2012
 */
function updateCommune()
{
    $id_commune = $_POST['id_commune'];
    $lib_commune = $_POST['lib_commune'];
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $sql = "UPDATE commune SET lib_commune = '$lib_commune' WHERE id_commune = $id_commune";
            $result = mysql_query($sql);
            $sql = "UPDATE commune SET id_commune = $id_commune WHERE lib_commune = '$lib_commune'";
            $result = mysql_query($sql);
            if (! $result) {
                echo '2';
            } else {
                echo '1';
            }
            
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : createCommune Input : Output : success => '1' / failed => '2' Object : create commune Date : Jan. 24, 2012
 */
function createCommune()
{
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $lib_commune = mysql_real_escape_string($_POST['lib_commune']);
            $id_commune = $_POST['id_commune'];
            
            $sql = "INSERT INTO commune (lib_commune, id_commune) VALUES ('$lib_commune', $id_commune)";
            $result = mysql_query($sql);
            if (! $result) {
                echo '2';
            } else {
                echo '1';
            }
            
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : deleteCommunes Input : Output : success => '1' / failed => '2' Object : delete commune(s) Date : Jan. 24, 2012
 */
function deleteCommunes()
{
    $ids = $_POST['ids'];
    $idcitys = json_decode(stripslashes($ids));
    
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            if (sizeof($idcitys) < 1) {
                echo '0';
            } else if (sizeof($idcitys) == 1) {
                $sql = "DELETE FROM commune WHERE id_commune = " . $idcitys[0];
                $result = mysql_query($sql);
            } else {
                $sql = "DELETE FROM commune WHERE ";
                for ($i = 0; $i < sizeof($idcitys); $i ++) {
                    $sql = $sql . "id_commune = " . $idcitys[$i];
                    if ($i < sizeof($idcitys) - 1) {
                        $sql = $sql . " OR ";
                    }
                }
                $result = mysql_query($sql);
            }
            if (! $result) {
                echo '2';
            } else {
                echo '1';
            }
            
            mysql_free_result($result);
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : getPole Input : start, limit Output : json poles Object : populate pole grid Date : May 2, 2012
 */
function getPole($start, $limit)
{
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $sql = "SELECT * FROM pole ORDER BY lib_pole ASC";
            $result = mysql_query($sql);
            $nbrows = mysql_num_rows($result);
            $sql .= " LIMIT " . $limit . " OFFSET " . $start;
            $result = mysql_query($sql);
            
            $i = 0;
            if ($nbrows > 0) {
                while ($row = mysql_fetch_array($result)) {
                    $arr[$i]['id_pole'] = $row['id_pole'];
                    $arr[$i]['lib_pole'] = stripslashes($row['lib_pole']);
                    $i ++;
                }
                echo '({"total":"' . $nbrows . '","results":' . json_encode($arr) . '})';
            } else {
                echo '({"total":"0", "results":""})';
            }
            
            mysql_free_result($result);
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : updatePole Input : Output : success => '1' / failed => '2' Object : update pole grid Date : May 2, 2012
 */
function updatePole()
{
    $id_pole = $_POST['id_pole'];
    $lib_pole = $_POST['lib_pole'];
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $sql = "UPDATE pole SET lib_pole = '$lib_pole' WHERE id_pole = $id_pole";
            $result = mysql_query($sql);
            // $sql = "UPDATE pole SET id_pole = $id_pole WHERE lib_pole = '$lib_pole'";
            // $result = mysql_query($sql);
            if (! $result) {
                echo '2';
            } else {
                echo '1';
            }
            
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : createPole Input : Output : success => '1' / failed => '2' Object : create pole Date : May 2, 2012
 */
function createPole()
{
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $lib_pole = mysql_real_escape_string($_POST['lib_pole']);
            $id_pole = $_POST['id_pole'];
            
            $sql = "INSERT INTO pole (lib_pole, id_pole) VALUES ('$lib_pole', $id_pole)";
            $result = mysql_query($sql);
            if (! $result) {
                echo '2';
            } else {
                echo '1';
            }
            
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : deletePoles Input : Output : success => '1' / failed => '2' Object : delete pole(s) Date : May 2, 2012
 */
function deletePoles()
{
    $ids = $_POST['ids'];
    $idpoles = json_decode(stripslashes($ids));
    
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            if (sizeof($idpoles) < 1) {
                echo '0';
            } else if (sizeof($idpoles) == 1) {
                $sql = "DELETE FROM pole WHERE id_pole = " . $idpoles[0];
                $result = mysql_query($sql);
            } else {
                $sql = "DELETE FROM pole WHERE ";
                for ($i = 0; $i < sizeof($idpoles); $i ++) {
                    $sql = $sql . "id_pole = " . $idpoles[$i];
                    if ($i < sizeof($idpoles) - 1) {
                        $sql = $sql . " OR ";
                    }
                }
                $result = mysql_query($sql);
            }
            if (! $result) {
                echo '2';
            } else {
                echo '1';
            }
            
            mysql_free_result($result);
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : getQuartier Input : start, limit Output : json quartiers Object : populate quartier grid Date : May 2, 2012
 */
function getQuartier($start, $limit)
{
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $sql = "SELECT * FROM quartier ORDER BY lib_quartier ASC";
            $result = mysql_query($sql);
            $nbrows = mysql_num_rows($result);
            $sql .= " LIMIT " . $limit . " OFFSET " . $start;
            $result = mysql_query($sql);
            
            $i = 0;
            if ($nbrows > 0) {
                while ($row = mysql_fetch_array($result)) {
                    $arr[$i]['id_quartier'] = $row['id_quartier'];
                    $arr[$i]['lib_quartier'] = stripslashes($row['lib_quartier']);
                    $i ++;
                }
                echo '({"total":"' . $nbrows . '","results":' . json_encode($arr) . '})';
            } else {
                echo '({"total":"0", "results":""})';
            }
            
            mysql_free_result($result);
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : updateQuartier Input : Output : success => '1' / failed => '2' Object : update quartier grid Date : May 2, 2012
 */

// function updateQuartier() {
// switch (SGBD) {
// case 'mysql':
// $link = mysql_connect(DB_HOST,DB_USER,DB_PASS);
// mysql_select_db(DB_NAME);
// mysql_query("SET NAMES utf8mb4");

// $id_quartier = $_POST['id_quartier'];
// $lib_quartier = $_POST['lib_quartier'];

// $sql = "UPDATE quartier SET lib_quartier = '$lib_quartier' WHERE id_quartier = $id_quartier";
// $result = mysql_query($sql);
// if (!$result) {
// echo '2';
// } else {
// echo '1';
// }

// mysql_free_result($result);
// mysql_close($link);
// break;
// case 'postgresql':
// // TODO
// break;
// }

// }

/*
 * Function name : createQuartier Input : Output : success => '1' / failed => '2' Object : create quartier Date : May 2, 2012
 */

// function createQuartier() {
// switch (SGBD) {
// case 'mysql':
// $link = mysql_connect(DB_HOST,DB_USER,DB_PASS);
// mysql_select_db(DB_NAME);
// mysql_query("SET NAMES utf8mb4");

// $lib_quartier = mysql_real_escape_string($_POST['lib_quartier']);

// $sql = "INSERT INTO quartier (lib_quartier) VALUES ('$lib_quartier')";
// $result = mysql_query($sql);
// if (!$result) {
// echo '2';
// } else {
// echo '1';
// }

// mysql_free_result($result);
// mysql_close($link);
// break;
// case 'postgresql':
// // TODO
// break;
// }
// }

/*
 * Function name : deleteQuartiers Input : Output : success => '1' / failed => '2' Object : delete quartier(s) Date : May 2, 2012
 */

// function deleteQuartiers() {
// $ids = $_POST['ids'];
// $idquartiers = json_decode(stripslashes($ids));

// switch (SGBD) {
// case 'mysql':
// $link = mysql_connect(DB_HOST,DB_USER,DB_PASS);
// mysql_select_db(DB_NAME);
// mysql_query("SET NAMES utf8mb4");

// if (sizeof($idquartiers) < 1){
// echo '0';
// } else if (sizeof($idquartiers) == 1){
// $sql = "DELETE FROM quartier WHERE id_quartier = ".$idquartiers[0];
// $result = mysql_query($sql);
// } else {
// $sql = "DELETE FROM quartier WHERE ";
// for ($i = 0; $i < sizeof($idquartiers); $i++){
// $sql = $sql . "id_quartier = ".$idquartiers[$i];
// if ($i < sizeof($idquartiers) - 1){
// $sql = $sql . " OR ";
// }
// }
// $result = mysql_query($sql);
// }
// if (!$result) {
// echo '2';
// } else {
// echo '1';
// }

// mysql_free_result($result);
// mysql_close($link);
// break;
// case 'postgresql':
// // TODO
// break;
// }

// }

/*
 * Function name : getPriorite Input : start, limit Output : json priorites Object : populate priorite grid Date : May 3, 2012
 */
function getPriorite($start, $limit)
{
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $sql = "SELECT * FROM priorite ORDER BY lib_priorite ASC";
            $result = mysql_query($sql);
            $nbrows = mysql_num_rows($result);
            $sql .= " LIMIT " . $limit . " OFFSET " . $start;
            $result = mysql_query($sql);
            
            $i = 0;
            if ($nbrows > 0) {
                while ($row = mysql_fetch_array($result)) {
                    $arr[$i]['id_priorite'] = $row['id_priorite'];
                    $arr[$i]['lib_priorite'] = stripslashes($row['lib_priorite']);
                    $arr[$i]['non_visible_par_collectivite'] = stripslashes($row['non_visible_par_collectivite']);
                    $arr[$i]['non_visible_par_public'] = stripslashes($row['non_visible_par_public']);
                    $arr[$i]['priorite_sujet_email'] = stripslashes($row['priorite_sujet_email']);
                    $arr[$i]['priorite_corps_email'] = stripslashes($row['priorite_corps_email']);
                    $arr[$i]['besoin_commentaire_association'] = stripslashes($row['besoin_commentaire_association']);
                    $arr[$i]['visible_public_par_defaut'] = stripslashes($row['visible_public_par_defaut']);
                    $arr[$i]['visible_moderateur_par_defaut'] = stripslashes($row['visible_moderateur_par_defaut']);
                    $i ++;
                }
                echo '({"total":"' . $nbrows . '","results":' . json_encode($arr) . '})';
            } else {
                echo '({"total":"0", "results":""})';
            }
            
            mysql_free_result($result);
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : updatePriorite Input : Output : success => '1' / failed => '2' Object : update priorite grid Date : May 3, 2012
 */
function updatePriorite()
{
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $id_priorite = mysql_real_escape_string($_POST['id_priorite']);
            $lib_priorite = mysql_real_escape_string($_POST['lib_priorite']);
            $non_visible_par_collectivite = mysql_real_escape_string($_POST['non_visible_par_collectivite']);
            $non_visible_par_public = mysql_real_escape_string($_POST['non_visible_par_public']);
            $priorite_sujet_email = mysql_real_escape_string($_POST['priorite_sujet_email']);
            $priorite_corps_email = mysql_real_escape_string($_POST['priorite_corps_email']);
            $besoin_commentaire_association = mysql_real_escape_string($_POST['besoin_commentaire_association']);
            $visible_public_par_defaut = mysql_real_escape_string($_POST['visible_public_par_defaut']);
            $visible_moderateur_par_defaut = mysql_real_escape_string($_POST['visible_moderateur_par_defaut']);
            
            $sql = "UPDATE priorite SET lib_priorite = '$lib_priorite', 
					non_visible_par_collectivite = $non_visible_par_collectivite, 
					non_visible_par_public = $non_visible_par_public,
					priorite_sujet_email = '$priorite_sujet_email',
					priorite_corps_email = '$priorite_corps_email',
					besoin_commentaire_association = $besoin_commentaire_association,
					visible_public_par_defaut = $visible_public_par_defaut,
					visible_moderateur_par_defaut = $visible_moderateur_par_defaut
					 WHERE id_priorite = $id_priorite";
            $result = mysql_query($sql);
            if (! $result) {
                echo '2';
            } else {
                echo '1';
            }
            
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : createPriorite Input : Output : success => '1' / failed => '2' Object : create priorite Date : May 3, 2012
 */
function createPriorite()
{
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $lib_priorite = mysql_real_escape_string($_POST['lib_priorite']);
            
            $sql = "INSERT INTO priorite (lib_priorite) VALUES ('$lib_priorite')";
            $result = mysql_query($sql);
            if (! $result) {
                echo '2';
            } else {
                echo '1';
            }
            
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : deletePriorites Input : Output : success => '1' / failed => '2' Object : delete priorite(s) Date : May 3, 2012
 */
function deletePriorites()
{
    $ids = $_POST['ids'];
    $idpriorites = json_decode(stripslashes($ids));
    
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            if (sizeof($idpriorites) < 1) {
                echo '0';
            } else if (sizeof($idpriorites) == 1) {
                $sql = "DELETE FROM priorite WHERE id_priorite = " . $idpriorites[0];
                $result = mysql_query($sql);
            } else {
                $sql = "DELETE FROM priorite WHERE ";
                for ($i = 0; $i < sizeof($idpriorites); $i ++) {
                    $sql = $sql . "id_priorite = " . $idpriorites[$i];
                    if ($i < sizeof($idpriorites) - 1) {
                        $sql = $sql . " OR ";
                    }
                }
                $result = mysql_query($sql);
            }
            if (! $result) {
                echo '2';
            } else {
                echo '1';
            }
            
            mysql_free_result($result);
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : getStatus Input : start, limit Output : json status Object : populate status grid Date : May 3, 2012
 */
function getStatus($start, $limit)
{
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $sql = "SELECT * FROM status ORDER BY lib_status ASC";
            $result = mysql_query($sql);
            $nbrows = mysql_num_rows($result);
            $sql .= " LIMIT " . $limit . " OFFSET " . $start;
            $result = mysql_query($sql);
            
            $i = 0;
            if ($nbrows > 0) {
                while ($row = mysql_fetch_array($result)) {
                    $arr[$i]['id_status'] = $row['id_status'];
                    $arr[$i]['lib_status'] = stripslashes($row['lib_status']);
                    $arr[$i]['color_status'] = stripslashes($row['color_status']);
                    $arr[$i]['is_active_status'] = stripslashes($row['is_active_status']);
                    $i ++;
                }
                echo '({"total":"' . $nbrows . '","results":' . json_encode($arr) . '})';
            } else {
                echo '({"total":"0", "results":""})';
            }
            
            mysql_free_result($result);
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : updateStatus Input : Output : success => '1' / failed => '2' Object : update status grid Date : May 3, 2012
 */
function updateStatus()
{
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            $id_status = mysql_real_escape_string($_POST['id_status']);
            $sql = "UPDATE status SET ";
            if (isset($_POST['lib_status']) && $_POST['lib_status'] != '') {
                $sql .= "lib_status = '" . mysql_real_escape_string($_POST['lib_status']) . "',";
            }
            if (isset($_POST['color_status']) && $_POST['color_status'] != '') {
                $sql .= "color_status = '" . mysql_real_escape_string($_POST['color_status']) . "',";
            }
            if (isset($_POST['is_active_status']) && $_POST['is_active_status'] != '') {
                if ($_POST['is_active_status'] == 0) {
                    $sqlNbPOI = "SELECT id_poi FROM poi WHERE status_id_status = $id_status";
                    $resultNbPOI = mysql_query($sqlNbPOI);
                    $nbrows = mysql_num_rows($resultNbPOI);
                    //if obsevation exist with this status, do not allow to deactivate it
                    if ($nbrows > 0) {
                        echo '2: Erreur lors de la mise à jour du statut - vous ne pouvez pas le désactiver tant que des observations sont liées à ce statut';
                    } else {
                        $sql .= "is_active_status = '" . mysql_real_escape_string($_POST['is_active_status']) . "',";
                    }
                }
            }
            $sql = substr($sql, 0, - 1);
            $sql .= " WHERE id_status = " .$id_status;
            
            $result = mysql_query($sql);
            if (! $result) {
                echo '2:Erreur lors de la mise à jour du statut.';
            } else {
                echo '1:Mise à jour du statut effectuée';
            }
            
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : createStatus Input : Output : success => '1' / failed => '2' Object : create status Date : May 3, 2012
 */
function createStatus()
{
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $lib_status = mysql_real_escape_string($_POST['lib_status']);
            $color_status = mysql_real_escape_string($_POST['color_status']);
            $sql = "INSERT INTO status (lib_status, color_status) VALUES ('$lib_status', '$color_status')";
            $result = mysql_query($sql);
            if (! $result) {
                echo '2';
            } else {
                echo '1';
            }
            
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : deleteStatuss Input : Output : success => '1' / failed => '2' Object : delete statut(s) Date : May 3, 2012
 */
function deleteStatuss()
{
    $ids = $_POST['ids'];
    $idstatuss = json_decode(stripslashes($ids));
    
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            if (sizeof($idstatuss) < 1) {
                echo '0';
            } else if (sizeof($idstatuss) == 1) {
                $sql = "DELETE FROM status WHERE id_status = " . $idstatuss[0];
                $result = mysql_query($sql);
            } else {
                $sql = "DELETE FROM status WHERE ";
                for ($i = 0; $i < sizeof($idstatuss); $i ++) {
                    $sql = $sql . "id_status = " . $idstatuss[$i];
                    if ($i < sizeof($idstatuss) - 1) {
                        $sql = $sql . " OR ";
                    }
                }
                $result = mysql_query($sql);
            }
            if (! $result) {
                echo '2';
            } else {
                echo '1';
            }
            
            mysql_free_result($result);
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : createLinkUserPole
 * Input :
 * Output : success => '1' / failed => '2'
 * Object : create link betwwen user and pole
 * Date : Jan 30 2019
 */
function createLinkUserPole()
{
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $id_users = mysql_real_escape_string($_POST['id_user']);
            $num_pole = $_POST['num_pole'];
            
            $sqlPole = "SELECT p.lib_pole, t.lib_territoire, t.id_territoire FROM pole p INNER JOIN territoire t ON t.id_territoire = p.territoire_id_territoire WHERE id_pole = $num_pole";
            $resultPole = mysql_query($sqlPole);
            $lib_pole = mysql_result($resultPole, 0, "lib_pole");
            $lib_territoire = mysql_result($resultPole, 0, "lib_territoire");
            $territoire_id_territoire = mysql_result($resultPole, 0, "id_territoire");
            
            $sql = "INSERT INTO users_link_pole (id_user, num_pole, territoire_id_territoire) VALUES ('$id_users', '$num_pole', '$territoire_id_territoire')";
            if (DEBUG) {
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " creating link user/pole $sql\n", 3, LOG_FILE);
            }
            $result = mysql_query($sql);
            $error = 0;
            if (! $result) {
                $error = 1;
                echo '2:Erreur lors de l\'insertion du lien Modérateur/Pôle en base de données.';
                if (mysql_errno($link) == 1062) {
                    echo 'Ce lien existe déjà';
                }
            } else {
                $sqlUser = "SELECT lib_users, mail_users FROM users WHERE id_users = $id_users";
                $resultUser = mysql_query($sqlUser);
                $lib_users = mysql_result($resultUser, 0, "lib_users");
                $mail_users = mysql_result($resultUser, 0, "mail_users");
                
                $message = "Bonjour,<br />
Votre compte " . $lib_users . " est maintenant modérateur sur le pôle \n" . $lib_pole . " (territoire " . $lib_territoire . ").\n Vous pouvez vous connecter à l'interface d'administration à l'adresse :
" . URL . "/admin.php\n<br />
En cas de question, vous pouvez trouver des informations sur\n https://github.com/2p2r/velobs_web.\n N'hésitez pas à envoyer un courriel à " . MAIL_ALIAS_OBSERVATION_ADHERENTS . " pour toute question sur VelObs.\n<br />";
                sendMail($mail_users, "Création lien compte modérateur / pôle", $message);
                echo '1:OK';
            }
            
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

function getLinksUserPole($start, $limit)
{
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $sql = "SELECT ulp.user_link_pole_id, u.id_users, u.lib_users, t.lib_territoire, p.lib_pole FROM users_link_pole ulp INNER JOIN territoire t ON t.id_territoire = ulp.territoire_id_territoire INNER JOIN pole p on p.id_pole = ulp.num_pole INNER JOIN users u ON u.id_users = ulp.id_user ORDER BY u.lib_users ASC ";
            if (DEBUG) {
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " - sql - " . $sql . "\n", 3, LOG_FILE);
            }
            $result = mysql_query($sql);
            $nbrows = mysql_num_rows($result);
            $sql .= " LIMIT " . $limit . " OFFSET " . $start;
            $result = mysql_query($sql);
            
            $i = 0;
            if ($nbrows > 0) {
                while ($row = mysql_fetch_array($result)) {
                    $arr[$i]['user_link_pole_id'] = $row['user_link_pole_id'];
                    $arr[$i]['id_users'] = $row['id_users'];
                    $arr[$i]['lib_users'] = stripslashes($row['lib_users']);
                    $arr[$i]['lib_territoire'] = stripslashes($row['lib_territoire']);
                    $arr[$i]['lib_pole'] = stripslashes($row['lib_pole']);
                    
                    $i ++;
                }
                echo '({"total":"' . $nbrows . '","results":' . json_encode($arr) . '})';
            } else {
                echo '({"total":"0", "results":""})';
            }
            if (DEBUG) {
                // error_log(date("Y-m-d H:i:s") . " " .__FUNCTION__ . " - " . getLocations($latitude_poi,$longitude_poi)[1]."\n", 3, LOG_FILE);
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " - nbrows - " . $nbrows . "\n", 3, LOG_FILE);
            }
            mysql_free_result($result);
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : deleteLinkUserPole Input : Output : success => '1' / failed => '2' Object : delete link user / pole (s) Date : Jan 30, 2019
 */
function deleteLinkUserPole()
{
    $ids = $_POST['ids'];
    $idLinksUserPole = json_decode(stripslashes($ids));
    
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            // Allow only moderator link to be deleted
            $sqlSelectUser = "SELECT u.usertype_id_usertype FROM users u INNER JOIN users_link_pole ulp ON ulp.id_user = u.id_users WHERE ulp.user_link_pole_id = " . $idLinksUserPole[0];
            $resultSelectUser = mysql_query($sqlSelectUser);
            $usertype = mysql_result($resultSelectUser, 0);
            mysql_free_result($resultSelectUser);
            if ($usertype != 4) {
                echo '0:Uniquement les comptes modérateurs peuvent être déliés d\'un pôle.';
            } else {
                if (sizeof($idLinksUserPole) < 1) {
                    echo '0:Aucun lien modérateur/pôle ne semble avoir été fourni.';
                } else if (sizeof($idLinksUserPole) == 1) {
                    $sql = "DELETE FROM users_link_pole WHERE user_link_pole_id = " . $idLinksUserPole[0];
                    $result = mysql_query($sql);
                } else {
                    $sql = "DELETE FROM users_link_pole WHERE ";
                    for ($i = 0; $i < sizeof($idLinksUserPole); $i ++) {
                        $sql = $sql . "user_link_pole_id = " . $idLinksUserPole[$i];
                        if ($i < sizeof($idLinksUserPole) - 1) {
                            $sql = $sql . " OR ";
                        }
                    }
                    $result = mysql_query($sql);
                }
                if (! $result) {
                    echo '2:Une erreur s\'est produite lors de la suppression du lien Modérateur/Pôle n° ' + $idLinksUserPole;
                } else {
                    echo '1:Le lien Modérateur/Pôle a bien été supprimé.';
                }
            }
            mysql_free_result($result);
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : getUsers Input : start, limit Output : json status Object : populate user grid Date : July 9, 2015
 */
function getUsers($start, $limit)
{
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $sql = "SELECT users.*, usertype.lib_usertype FROM users INNER JOIN usertype ON (usertype.id_usertype = users.usertype_id_usertype) ORDER BY id_users ASC";
            if (DEBUG) {
                // error_log(date("Y-m-d H:i:s") . " " .__FUNCTION__ . " - " . getLocations($latitude_poi,$longitude_poi)[1]."\n", 3, LOG_FILE);
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " - sql - " . $sql . "\n", 3, LOG_FILE);
            }
            $result = mysql_query($sql);
            $nbrows = mysql_num_rows($result);
            $sql .= " LIMIT " . $limit . " OFFSET " . $start;
            $result = mysql_query($sql);
            
            $i = 0;
            if ($nbrows > 0) {
                while ($row = mysql_fetch_array($result)) {
                    $arr[$i]['id_users'] = $row['id_users'];
                    $arr[$i]['lib_users'] = stripslashes($row['lib_users']);
                    $arr[$i]['pass_users'] = stripslashes($row['pass_users']);
                    $arr[$i]['nom_users'] = stripslashes($row['nom_users']);
                    $arr[$i]['mail_users'] = stripslashes($row['mail_users']);
                    $arr[$i]['lib_usertype'] = stripslashes($row['lib_usertype']);
                    $arr[$i]['is_active_user'] = stripslashes($row['is_active_user']);
                    
                    $i ++;
                }
                echo '({"total":"' . $nbrows . '","results":' . json_encode($arr) . '})';
            } else {
                echo '({"total":"0", "results":""})';
            }
            if (DEBUG) {
                // error_log(date("Y-m-d H:i:s") . " " .__FUNCTION__ . " - " . getLocations($latitude_poi,$longitude_poi)[1]."\n", 3, LOG_FILE);
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " - nbrows - " . $nbrows . "\n", 3, LOG_FILE);
            }
            mysql_free_result($result);
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : getUser Output : json status Object : populate user grid Date : March 17, 2018
 */
function getUser()
{
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $sql = "SELECT users.*, usertype.lib_usertype FROM users INNER JOIN usertype ON (usertype.id_usertype = users.usertype_id_usertype) WHERE lib_users = '" . $_SESSION['user'] . "'";
            if (DEBUG) {
                // error_log(date("Y-m-d H:i:s") . " " .__FUNCTION__ . " - " . getLocations($latitude_poi,$longitude_poi)[1]."\n", 3, LOG_FILE);
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " - sql - " . $sql . "\n", 3, LOG_FILE);
            }
            $result = mysql_query($sql);
            $nbrows = mysql_num_rows($result);
            $result = mysql_query($sql);
            
            $i = 0;
            if ($nbrows > 0) {
                while ($row = mysql_fetch_array($result)) {
                    $arr[$i]['id_users'] = $row['id_users'];
                    $arr[$i]['lib_users'] = stripslashes($row['lib_users']);
                    $arr[$i]['pass_users'] = stripslashes($row['pass_users']);
                    $arr[$i]['nom_users'] = stripslashes($row['nom_users']);
                    $arr[$i]['mail_users'] = stripslashes($row['mail_users']);
                    $arr[$i]['lib_usertype'] = stripslashes($row['lib_usertype']);
                    $arr[$i]['is_active_user'] = stripslashes($row['is_active_user']);
                    $i ++;
                }
                echo '({"total":"' . $nbrows . '","results":' . json_encode($arr) . '})';
            } else {
                echo '({"total":"0", "results":""})';
            }
            if (DEBUG) {
                // error_log(date("Y-m-d H:i:s") . " " .__FUNCTION__ . " - " . getLocations($latitude_poi,$longitude_poi)[1]."\n", 3, LOG_FILE);
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " - nbrows - " . $nbrows . "\n", 3, LOG_FILE);
            }
            mysql_free_result($result);
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

function resetUserPassword()
{
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $userId = mysql_real_escape_string($_POST['userId']);
            $userMail = mysql_real_escape_string($_POST['userMail']);
            $userLogin = mysql_real_escape_string($_POST['userLogin']);
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            $clearPassword = substr(str_shuffle($chars), 0, 8);
            $pass_users = create_password_hash($clearPassword, PASSWORD_BCRYPT);
            
            $sql = "UPDATE users SET pass_users = '$pass_users' WHERE id_users = $userId";
            if (DEBUG) {
                // error_log(date("Y-m-d H:i:s") . " " .__FUNCTION__ . " - " . getLocations($latitude_poi,$longitude_poi)[1]."\n", 3, LOG_FILE);
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " - sql - " . $sql . ", mot de passe \n", 3, LOG_FILE);
            }
            
            $result = mysql_query($sql);
            if (! $result) {
                echo '2';
            } else {
                echo '1';
                $message = "Bonjour,<br />
Votre compte sur VelObs a été mis à jour.\n Vous pouvez vous connecter à l'interface d'administration à l'adresse :\n<br />
" . URL . "/admin.php
					Vos identifiants sont :\n<br />
					- Login : ".$userLogin."\n<br />
					- Mot de passe : " . $clearPassword . "\n<br />
Pour modifier votre mot de passe, cliquer sur le menu \"Mes coordonnées\" à droite sur l'interface d'administration, puis sur \"Modifier mes coordonnées\".<br />
					En cas de question, vous pouvez trouver des informations\n sur https://github.com/2p2r/velobs_web. N'hésitez pas à envoyer un courriel à\n " . MAIL_ALIAS_OBSERVATION_ADHERENTS . " pour toute question sur VelObs.\n<br />";
                sendMail($userMail, "Réinitialisation mote de passe sur VelObs", $message);
            }
            
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : updateUser Input : Output : success => '1' / failed => '2' Object : update user grid Date : July 9, 2015
 */
function updateUser()
{
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            $message = '';
            $id_users = mysql_real_escape_string($_POST['id_users']);
            $sql = "UPDATE users SET ";
            if (isset($_POST['lib_users']) && $_POST['lib_users'] != '') {
                $sql .= "lib_users = '" . mysql_real_escape_string($_POST['lib_users']) . "',";
                $message .= "	- Login : " . $_POST['lib_users'] . "<br />\n";
            }
            if (isset($_POST['mail_users']) && $_POST['mail_users'] != '') {
                $mail_users = mysql_real_escape_string($_POST['mail_users']);
                $sql .= "mail_users = '" . $mail_users . "',";
                $message .= "	- Mail : " . $_POST['mail_users'] . "<br />\n";
            }
            if (isset($_POST['nom_users']) && $_POST['nom_users'] != '') {
                $sql .= "nom_users = '" . mysql_real_escape_string($_POST['nom_users']) . "',";
                $message .= "	- Nom : " . $_POST['nom_users'] . "<br />\n";
            }
            if (isset($_POST['pass_users']) && $_POST['pass_users'] != '') {
                $pass_users = create_password_hash($_POST['pass_users'], PASSWORD_BCRYPT);
                $sql .= "pass_users = '" . $pass_users . "',";
                $message .= "	- Mot de passe : " . $_POST['pass_users'] . "<br />\n";
            }
            
            if (isset($_POST['territoire_id_territoire']) && is_numeric($_POST['territoire_id_territoire'])) {
                $territoire_id_territoire = $_POST['territoire_id_territoire'];
                $sql .= " territoire_id_territoire = $territoire_id_territoire,";
            }
            if (isset($_POST['usertype_id_usertype']) && is_numeric($_POST['usertype_id_usertype'])) {
                $usertype_id_usertype = $_POST['usertype_id_usertype'];
                $sql .= "usertype_id_usertype = $usertype_id_usertype,";
            }
            if (isset($_POST['num_pole']) && is_numeric($_POST['num_pole'])) {
                $num_pole = $_POST['num_pole'];
                $sql .= " num_pole = $num_pole ,";
            }
            if (isset($_POST['is_active_user']) && $_POST['is_active_user'] != '') {
                $is_active_user = $_POST['is_active_user'];
                $sql .= " is_active_user = $is_active_user ,";
            }
            $sql = substr($sql, 0, - 1);
            $sql .= " WHERE id_users = " . mysql_real_escape_string($_POST['id_users']);
            if (DEBUG) {
                // error_log(date("Y-m-d H:i:s") . " " .__FUNCTION__ . " - " . getLocations($latitude_poi,$longitude_poi)[1]."\n", 3, LOG_FILE);
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " - sql - " . $sql . " \n", 3, LOG_FILE);
            }
            $result = mysql_query($sql);
            if (! $result) {
                echo '2';
            } else {
                echo '1';
                $message = "Bonjour,<br />
Votre compte sur VelObs a été mis à jour.\n Vous pouvez vous connecter à l'interface d'administration à l'adresse :<br />\n
" . URL . "/admin.php\n<br />
" . $message . "<br />
Pour modifier votre mot de passe, cliquer sur le menu \"Mes coordonnées\" à droite sur l'interface d'administration, puis sur \"Modifier mes coordonnées\".<br />
En cas de question, vous pouvez trouver des informations\n sur https://github.com/2p2r/velobs_web. \nN'hésitez pas à envoyer un courriel à \n" . MAIL_ALIAS_OBSERVATION_ADHERENTS . " pour toute question sur VelObs.\n<br />";
                sendMail($mail_users, "Modification coordonnées sur VelObs", $message);
            }
            
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : createUser
 * Input :
 * Output : success => '1' / failed => '2'
 * Object : create user
 * Date : July 9, 2015
 */
function createUser()
{
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $lib_users = mysql_real_escape_string($_POST['lib_users']);
            $nom_users = mysql_real_escape_string($_POST['nom_users']);
            $pass_users = create_password_hash($_POST['pass_users'], PASSWORD_BCRYPT);
            $mail_users = mysql_real_escape_string($_POST['mail_users']);
            
            $usertype_id_usertype = $_POST['usertype_id_usertype'];
            $num_pole = $_POST['num_pole'];
            $territoire_id_territoire = $_POST['territoire_id_territoire'];
            
            $sql = "INSERT INTO users (lib_users, nom_users, pass_users, mail_users, usertype_id_usertype, language_id_language) VALUES ('$lib_users', '$nom_users', '$pass_users', '$mail_users', $usertype_id_usertype, 1)";
            if (DEBUG) {
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " creating user $sql\n", 3, LOG_FILE);
            }
            $result = mysql_query($sql);
            $id_user = mysql_insert_id();
            $error = 0;
            if (! $result) {
                $error = 1;
                echo '2';
            } else {
                
                if (DEBUG) {
                    error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " id created user =  - " . $id_user . "\n", 3, LOG_FILE);
                }
                // TODO : check consistency between pole and territoire
                
                // Si le compte est un pole technhique ou un modérateur
                if ($usertype_id_usertype == 3 || $usertype_id_usertype == 4) {
                    $sqlCheck = "SELECT count(*) FROM pole WHERE id_pole = $num_pole AND territoire_id_territoire = $territoire_id_territoire";
                    $resultCheck = mysql_query($sqlCheck);
                    $link_pole_territoire_exists = mysql_result($resultCheck, 0);
                    // si le lien pole/territoire n'existe pas, on affiche une erreur d'incohérence
                    if ($link_pole_territoire_exists == 0) {
                        echo 3;
                        $error = 1;
                    } else {
                        $sqlUPL = "INSERT INTO users_link_pole (id_user, territoire_id_territoire, num_pole) VALUES ($id_user, $territoire_id_territoire, $num_pole)";
                    }
                    // Si le compte est administrateur
                } else if ($usertype_id_usertype == 1) {
                    
                    $sqlUPL = "INSERT INTO users_link_pole (id_user, territoire_id_territoire, num_pole) VALUES ($id_user, 0, 9)";
                } else {
                    if ($territoire_id_territoire == 0) {
                        $error = 1;
                        echo 3;
                    }
                    $sqlUPL = "INSERT INTO users_link_pole (id_user, territoire_id_territoire, num_pole) VALUES ($id_user, $territoire_id_territoire, $num_pole)";
                }
            }
            if (! $error) {
                $result = mysql_query($sqlUPL);
                $message = "Bonjour,<br />
Vous disposez maintenant d'un compte sur velobs\n vous permettant de mettre à jour les observations\n enregistrées dans le système. Vous pouvez vous connecter\n à l'interface d'administration à l'adresse :\n<br />
" . URL . "/admin.php\n<br />
Vos identifiants sont :\n<br />
	- Login : ".$lib_users."\n<br />
	- Mot de passe : " . $_POST['pass_users'] . "\n<br />
En cas de question, vous pouvez trouver des informations\n sur https://github.com/2p2r/velobs_web.\n N'hésitez pas à envoyer un courriel à\n " . MAIL_ALIAS_OBSERVATION_ADHERENTS . " pour toute question sur VelObs.\n<br />";
                sendMail($mail_users, "Création compte sur VelObs", $message);
                echo '1';
            }else{
                
                $result = mysql_query("DELETE FROM users WHERE id_users = " .$id_user );
            }
            
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : deleteUsers Input : Output : success => '1' / failed => '2' Object : delete user(s) Date : July 9, 2015
 */
function deleteUsers()
{
    $ids = $_POST['ids'];
    $idusers = json_decode(stripslashes($ids));
    
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            if (sizeof($idusers) < 1) {
                echo '0';
            } else if (sizeof($idusers) == 1) {
                $sql = "DELETE FROM users WHERE id_users = " . $idusers[0];
                $result = mysql_query($sql);
            } else {
                $sql = "DELETE FROM users WHERE ";
                for ($i = 0; $i < sizeof($idusers); $i ++) {
                    $sql = $sql . "id_users = " . $idusers[$i];
                    if ($i < sizeof($idusers) - 1) {
                        $sql = $sql . " OR ";
                    }
                }
                $result = mysql_query($sql);
            }
            if (! $result) {
                echo '2';
            } else {
                echo '1';
            }
            
            mysql_free_result($result);
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : resetPhotoPoi Input : Output : success => '1' / failed => '2' Object : reset photo Date : Jan. 22, 2012
 */
function resetPhotoPoi()
{
    $id_poi = $_POST['id_poi'];
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $sql = "SELECT photo_poi FROM poi WHERE id_poi = $id_poi";
            $result = mysql_query($sql);
            while ($row = mysql_fetch_array($result)) {
                unlink("../../../resources/pictures/" . $row['photo_poi']);
            }
            $lastdatemodif_poi = date("Y-m-d H:i:s");
            $sql = "UPDATE poi SET lastdatemodif_poi = '$lastdatemodif_poi', lastmodif_user_poi = " . $_SESSION['id_users'] . ", photo_poi = NULL WHERE id_poi = $id_poi";
            $result = mysql_query($sql);
            
            if (! $result) {
                echo '2';
            } else {
                echo '1';
            }
            
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : updateGeoPoi Input : Output : success => '1' / failed => '2' Object : modif geo poi Date : Jan. 23, 2012
 */
// function updateGeoPoi() {
// $id_poi = $_POST ['id_poi'];
// $latitude_poi = $_POST ['latitude_poi'];
// $longitude_poi = $_POST ['longitude_poi'];
// switch (SGBD) {
// case 'mysql' :
// $link = mysql_connect ( DB_HOST, DB_USER, DB_PASS );
// mysql_select_db ( DB_NAME );
// mysql_query ( "SET NAMES utf8mb4" );
// $locations = getLocations ( $latitude_poi, $longitude_poi );
// if (DEBUG) {
// // error_log(date("Y-m-d H:i:s") . " " .__FUNCTION__ . " - " . getLocations($latitude_poi,$longitude_poi)[1]."\n", 3, LOG_FILE);
// error_log ( date ( "Y-m-d H:i:s" ) . " " . __FUNCTION__ . " - locations - " . $locations [0] . ", " . $locations [1] . ", " . $locations [2] . ", " . $locations [3] . "\n", 3, LOG_FILE );
// }
// $commune_id_commune = $locations [0];
// $lib_commune = $locations [1];
// $pole_id_pole = $locations [2];
// $lib_pole = $locations [3];

// $lastdatemodif_poi = date ( "Y-m-d H:i:s" );
// $sql = "UPDATE poi SET commune_id_commune = " . $commune_id_commune . ", pole_id_pole = " . $pole_id_pole . ", geom_poi = GeomFromText('POINT(" . $longitude_poi . " " . $latitude_poi . ")'), geolocatemode_poi = 1, lastdatemodif_poi = '$lastdatemodif_poi' WHERE id_poi = $id_poi";
// $result = mysql_query ( $sql );

// if (! $result) {
// echo '2';
// } else {
// // echo $sql2;
// echo '1';
// }

// mysql_free_result ( $result );
// mysql_close ( $link );
// break;
// case 'postgresql' :
// // TODO
// break;
// }
// }

/*
 * Function name : resetGeoPoi Input : Output : success => '1' / failed => '2' Object : reset geo poi Date : Jan. 23, 2012
 */
function resetGeoPoi()
{
    $id_poi = $_POST['id_poi'];
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            $lastdatemodif_poi = date("Y-m-d H:i:s");
            $sql = "UPDATE poi SET lastdatemodif_poi = '$lastdatemodif_poi', lastmodif_user_poi = " . $_SESSION['id_users'] . ",geom_poi = NULL, geolocatemode_poi = NULL WHERE id_poi = $id_poi";
            $result = mysql_query($sql);
            
            if (! $result) {
                echo '2';
            } else {
                echo '1';
            }
            
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : updateGeoDefaultMap Input : Output : success => '1' / failed => '2' Object : update geo map settings default Date : Jan. 23, 2012
 */
function updateGeoDefaultMap()
{
    $lat = $_POST['lat'];
    $lon = $_POST['lon'];
    $zoom = $_POST['zoom'];
    $baselayer = $_POST['baselayer'];
    
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $sql = "UPDATE configmap SET lat_configmap = " . $lat . ", lon_configmap = " . $lon . ", zoom_configmap = " . $zoom . ", baselayer_configmap = " . $baselayer . " WHERE id_configmap = 1";
            $result = mysql_query($sql);
            
            if (! $result) {
                echo '2';
            } else {
                echo '1';
            }
            
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : createPublicPoi Input : Output : success => '1' / failed => '2' Object : create public poi Date : May 8, 2012
 */
function createPublicPoi()
{
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $num_poi = mysql_real_escape_string($_POST['num_poi']);
            $mail_poi = mysql_real_escape_string($_POST['mail_poi']);
            $tel_poi = mysql_real_escape_string($_POST['tel_poi']);
            $rue_poi = mysql_real_escape_string($_POST['rue_poi']);
            $desc_poi = mysql_real_escape_string($_POST['desc_poi']);
            $prop_poi = mysql_real_escape_string($_POST['prop_poi']);
            $adherent_poi = mysql_real_escape_string($_POST['adherent_poi']);
            $latitude_poi = $_POST['latitude_poi'];
            $longitude_poi = $_POST['longitude_poi'];
            $subcategory_id_subcategory = $_POST['subcategory_id_subcategory'];
            
            $sql = "SELECT lib_subcategory FROM subcategory WHERE id_subcategory = " . $subcategory_id_subcategory;
            $result = mysql_query($sql);
            $row = mysql_fetch_assoc($result);
            $lib_subcategory = mysql_real_escape_string($row['lib_subcategory']);
            
            // détermination de la commune et du pole concernés par croisement du polygone de la commune ave latitude et longitude
            $commune_id_commune = 99999;
            $pole_id_pole = 9;
            $quartier_id_quartier = 99999;
            $locations = getLocations($latitude_poi, $longitude_poi);
            if (DEBUG) {
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " - place locations - " . $locations[0] . ", " . $locations[1] . ", " . $locations[2] . ", " . $locations[3] . "\n", 3, LOG_FILE);
            }
            
            $commune_id_commune = $locations[0];
            $lib_commune = $locations[1];
            $pole_id_pole = $locations[2];
            $lib_pole = $locations[3];
            if ($commune_id_commune == 99999) {
                if (DEBUG) {
                    error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " L'observation semble être dans une zone non couverte par velobs\n", 3, LOG_FILE);
                }
                $erreur = "L'observation semble être dans une zone non couverte par VelObs, si ce n'est pas le cas, merci de nous contacter à l'adresse " . MAIL_FROM;
                $return['success'] = false;
                $return['pb'] = $erreur;
            }
            if (DEBUG) {
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " - isset(_FILES['photo-path']) - " . isset($_FILES['photo-path']['name']) . "\n", 3, LOG_FILE);
            }
            // si une photo a été associée au commentaire, on la traite
            if (! isset($erreur) && isset($_FILES['photo-path']) && $_FILES['photo-path']['name'] != "") {
                if (DEBUG) {
                    error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " photo-path isset " . $_FILES['photo-path'] . "\n", 3, LOG_FILE);
                }
                $dossier = '../../../resources/pictures/';
                $fichier = basename($_FILES['photo-path']['name']);
//                 $taille_maxi = 6291456;
//                 $taille = filesize($_FILES['photo-path']['tmp_name']);
                $taille_maxi = maximum_upload_size();
                //$taille_maxi = 6291456;
                $taille = filesize($_FILES['photo-path']['tmp_name']);
                if (DEBUG) {
                    error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " image size =  " . $taille . ", and apache upload_max_filesize = ".$taille_maxi."\n", 3, LOG_FILE);
                }
                
                $extensions = array(
                    '.png',
                    '.gif',
                    '.jpg',
                    '.jpeg',
                    '.PNG',
                    '.GIF',
                    '.JPG',
                    '.JPEG'
                );
                $extension = strrchr($_FILES['photo-path']['name'], '.');
                if (DEBUG) {
                    error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " l'extension du fichier est  " . $extension . "\n", 3, LOG_FILE);
                }
                if (! in_array($extension, $extensions)) {
                    if (DEBUG) {
                        error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " l'extension !in_array \n", 3, LOG_FILE);
                    }
                    $erreur = getTranslation(1, 'ERROR');
                    $return['success'] = false;
                    $return['pb'] = getTranslation(1, 'PICTUREPNGGIFJPGJPEG');
                }else if ($taille =="") {
                    $erreur = getTranslation(1, 'ERROR');
                    $return['success'] = false;
                    $return['pb'] = getTranslation(1, 'PICTURESIZE')." Taille maximum autorisée : " .$taille_maxi;
                }
                
                if (! isset($erreur)) {
                    if (DEBUG) {
                        error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " pas d'erreur, on continue \n", 3, LOG_FILE);
                    }
                    $fichier = strtr($fichier, 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ_', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy-');
                    $fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
                    $fichier = 'poi_' . $fichier;
                    $pathphoto = $dossier . $fichier;
                    if (DEBUG) {
                        error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " path photo ".$pathphoto." \n", 3, LOG_FILE);
                    }
                    if (move_uploaded_file($_FILES['photo-path']['tmp_name'], $pathphoto)) {
                        if (DEBUG) {
                            error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " dans move_uploaded_file \n", 3, LOG_FILE);
                        }
                        $size = getimagesize($pathphoto);
                        
                        if ($size[0] > 1024 || $size[1] > 1024) {
                            if ($size[0] > $size[1]) {
                                generate_image_thumbnail($pathphoto, $pathphoto, 1024, 768);
                            } else {
                                generate_image_thumbnail($pathphoto, $pathphoto, 768, 1024);
                            }
                        }
                        
                        $size = getimagesize($pathphoto);
                        $newnamefichier = $size[0] . 'x' . $size[1] . 'x' . $fichier;
                        $newpathphoto = $dossier . $newnamefichier;
                        rename($pathphoto, $newpathphoto);
                        $url_photo = $newnamefichier;
                        
                        $return['success'] = true;
                        $return['ok'] = getTranslation($_SESSION['id_language'], 'PHOTOTRANSFERTDONE');
                    } else {
                        $erreur = "Erreur lors du traitement de la photo.";
                        $return['success'] = false;
                        $return['pb'] = $erreur;
                    }
                }
            }
            if (DEBUG) {
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " - entre 2 " . $return['success'] . " " . $return['pb'] . "\n", 3, LOG_FILE);
            }
            // si une photo a été associée au commentaire et que tout s'est bien passé, ou bien s'il n'y avait pas de photo, on peut créer l'observation dans la base de données
            if (! isset($erreur) && ((isset($_FILES['photo-path']['name']) && $return['success'] == true) || (isset($_FILES['photo-path']['name']) && $_FILES['photo-path']['name'] == ''))) {
                if (DEBUG) {
                    error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " - place locations - " . $commune_id_commune . ", " . $lib_commune . ", " . $pole_id_pole . ", " . $lib_pole . "\n", 3, LOG_FILE);
                }
                
                $datecreation_poi = date('Y-m-d H:i:s');
                
                // si le mail de la personne qui soumet le POI est aussi un modérateur ou un administrateur, on positionne moderation_poi à vrai et on met la priorité à 1
                $sql2 = "SELECT mail_users FROM users WHERE (usertype_id_usertype = 1 OR usertype_id_usertype = 4) AND mail_users LIKE '" . $mail_poi . "'";
                $result2 = mysql_query($sql2);
                $num_rows2 = mysql_num_rows($result2);
                $priorityId = 1;
                $moderationFlag = 1;
                if ($num_rows2 == 0) {
                    $priorityId = 4;
                    $moderationFlag = 0;
                }
                if (DEBUG) {
                    error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " - place locations - " . $commune_id_commune . ", " . $lib_commune . ", " . $pole_id_pole . ", " . $lib_pole . "\n", 3, LOG_FILE);
                }
                $sql = "INSERT INTO poi (adherent_poi, priorite_id_priorite, quartier_id_quartier, pole_id_pole, lib_poi, mail_poi, tel_poi, num_poi, rue_poi, commune_id_commune, desc_poi, prop_poi, subcategory_id_subcategory, display_poi, fix_poi, datecreation_poi, geolocatemode_poi, moderation_poi, geom_poi, status_id_status, photo_poi) VALUES ('$adherent_poi', $priorityId, $quartier_id_quartier, $pole_id_pole, '$lib_subcategory', '$mail_poi', '$tel_poi', '$num_poi', '$rue_poi', $commune_id_commune, '$desc_poi', '$prop_poi', $subcategory_id_subcategory , TRUE, FALSE, '$datecreation_poi', 1, $moderationFlag, GeomFromText('POINT(" . $longitude_poi . " " . $latitude_poi . ")'), 5, '$url_photo')";
                
                if (DEBUG) {
                    error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " - createPublicPoi - Requete d'insertion sql = " . $sql . "\n", 3, LOG_FILE);
                    error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " Erreur " . mysql_errno($link) . " : " . mysql_error($link) . "\n", 3, LOG_FILE);
                }
                $result = mysql_query($sql);
                if ($result) {
                    $poiId = mysql_insert_id();
                    $arrayObs = getObservationDetailsInArray($poiId);
                    $arrayDetailsAndUpdateSQL = getObservationDetailsInString($arrayObs);
                    if (DEBUG) {
                        error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " - Il y a " . count($arrayDetailsAndUpdateSQL) . " infos chargées pour l'update de l'obs $id_poi \n", 3, LOG_FILE);
                        error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " - updateObsBoolean " . $arrayDetailsAndUpdateSQL['updateObsBoolean'] . " pour l'update de l'obs $id_poi \n", 3, LOG_FILE);
                        error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " - sqlUpdate " . $arrayDetailsAndUpdateSQL['sqlUpdate'] . " pour l'update de l'obs $id_poi \n", 3, LOG_FILE);
                        error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " - detailObservationString " . $arrayDetailsAndUpdateSQL['detailObservationString'] . " pour l'update de l'obs $id_poi \n", 3, LOG_FILE);
                    }
                    $return['success'] = true;
                    $return['ok'] = "L'observation a été correctement ajoutée sous le numéro $poiId et est directement affichée (après rechargement de la page) puisque vous êtes référencé(e) comme modérateur ou administrateur de VelObs.";
                    
                    // si le contributeur n'est pas un modérateur ni un administrateur par ailleurs, on envoie des mails
                    if ($num_rows2 == 0) {
                        $return['ok'] = "L'observation a bien été créée et est en attente de modération. Un courriel reprenant l'ensemble des informations de cette observation vous a été envoyé.";
                        
                        /* envoi d'un mail aux administrateurs de l'association et modérateurs */
                        $whereClause = "u.usertype_id_usertype = 1 OR (u.usertype_id_usertype = 4 AND ulp.num_pole = " . $arrayObs['pole_id_pole'] . ")";
                        $subject = 'Nouvelle observation à modérer sur le pole ' . $arrayObs['lib_pole'];
                        $message = "Bonjour !\n<br />
Une nouvelle observation a été ajoutée sur le pole\n " . $arrayObs['lib_pole'] . ".\n Veuillez vous connecter à l'interface\n d'administration pour la modérer.\n<br />
Lien vers la modération : \n" . URL . '/admin.php?id=' . $arrayObs['id_poi'] . "\n<br />" . $arrayDetailsAndUpdateSQL['detailObservationString'] . "\n<br />";
                        $mails = array();
                        $mails = getMailsToSend($whereClause, $subject, $message,$arrayObs['id_poi']);
                        if (DEBUG) {
                            error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " Il y a " . count($mails) . " mails à envoyer \n", 3, LOG_FILE);
                        }
                        // $succes = sendMails($mails);
                        
                        /* debut envoi d'un mail au contributeur */
                        $subject = 'Observation en attente de modération';
                        $message = "Bonjour !\n<br />
Vous venez d'ajouter une observation à VelObs\n et vous en remercions. Celle-ci devrait\n être administrée sous peu.\n<br />" . $arrayDetailsAndUpdateSQL['detailObservationString'] . "\n<br />
Cordialement, l'Association " . VELOBS_ASSOCIATION . " :)\n<br />";
                        $mailArray = [
                            $arrayObs['mail_poi'],
                            "Soumetteur",
                            $subject,
                            $message
                        ];
                        array_push($mails, $mailArray);
                    }
                    if (DEBUG) {
                        error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " - Il y a " . count($mails) . " mails à envoyer\n", 3, LOG_FILE);
                    }
                    $succes = sendMails($mails);
                } else {
                    
                    if (DEBUG) {
                        error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " Erreur " . mysql_errno($link) . " : " . mysql_error($link) . "\n", 3, LOG_FILE);
                    }
                    sendMail(MAIL_FROM, "Erreur méthode createPublicPoi", "Erreur = " . mysql_error($link) . ", requête = " . $sql);
                    $return['success'] = false;
                    $return['pb'] = "Erreur lors de l'ajout de l'observation, un mail a été envoyé aux administrateurs. Veuillez nous excuser pour ce dysfonctionnement.";
                }
            } else {
                
                $infoPOI = "Repere : $num_poi\nMail : $mail_poi\nTel : $tel_poi\nRue : $rue_poi\nDescription : $desc_poi\nProposition : $prop_poi\nNom : $adherent_poi\nLatitude : $latitude_poi\nLongitude : $longitude_poi\n Categorie : $subcategory_id_subcategory";
                sendMail(MAIL_FROM, "Erreur méthode createPublicPoi", "Erreur = " . $return['pb'] . "\n" . $infoPOI);
                $return['success'] = false;
                $return['pb'] = "Erreur lors de l'ajout de l'observation : " . $return['pb'];
            }
            // retourne le résultat du traitement du commentaire
            echo json_encode($return);
            mysql_close($link);
            
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : isPropPublic Input : Output : success => '1' / failed => '2' Object : display proposition in public map Date : Jan. 31, 2012
 */
function isPropPublic()
{
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $sql = "SELECT id_subcategory FROM subcategory WHERE proppublic_subcategory = 1";
            $result = mysql_query($sql);
            $nbrows = mysql_num_rows($result);
            
            if ($nbrows > 0) {
                echo '1';
            } else {
                echo '2';
            }
            
            mysql_free_result($result);
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : getNumPageIdParam Input : id record, usertype and number of record per page in the store Output : number of page to load Object : set the right page in the store to popup the edit map Date : July 25, 2015
 */
function getNumPageIdParam($idToFind, $usertype, $numRecordPerPage)
{
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $line = - 1;
            switch ($usertype) {
                case '1':
                    $sql = "SELECT poi.id_poi FROM poi WHERE delete_poi = FALSE ORDER BY id_poi DESC";
                    $result = mysql_query($sql);
                    
                    $i = 0;
                    while ($row = mysql_fetch_array($result)) {
                        $i ++;
                        if ($idToFind == $row['id_poi']) {
                            $line = $i;
                        }
                    }
                    
                    if ($line != - 1) {
                        $numerofpage = ceil($line / $numRecordPerPage);
                        echo '' . $numerofpage . '';
                    } else {
                        echo '-1';
                    }
                    
                    break;
                case '2':
                    $sql = "SELECT poi.id_poi 
								FROM poi 
								INNER JOIN subcategory ON (subcategory.id_subcategory = poi.subcategory_id_subcategory) 
								INNER JOIN commune ON (commune.id_commune = poi.commune_id_commune) 
								INNER JOIN pole ON (pole.id_pole = poi.pole_id_pole) 
								INNER JOIN quartier ON (quartier.id_quartier = poi.quartier_id_quartier) 
								INNER JOIN priorite ON (priorite.id_priorite = poi.priorite_id_priorite) 
								INNER JOIN status ON (status.id_status = poi.status_id_status) 
								WHERE moderation_poi = 1 
									AND commune_id_commune IN (" . str_replace(';', ',', $_SESSION['territoire']) . ") 
									AND delete_poi = FALSE 
									AND priorite.non_visible_par_collectivite = 0 
								ORDER BY id_poi DESC";
                    $result = mysql_query($sql);
                    
                    $i = 0;
                    while ($row = mysql_fetch_array($result)) {
                        $i ++;
                        if ($idToFind == $row['id_poi']) {
                            $line = $i;
                        }
                    }
                    
                    if ($line != - 1) {
                        $numerofpage = ceil($line / $numRecordPerPage);
                        echo '' . $numerofpage . '';
                    } else {
                        echo '-1';
                    }
                    
                    break;
                case '3':
                    $sql = "SELECT poi.id_poi 
								FROM poi 
								INNER JOIN subcategory ON (subcategory.id_subcategory = poi.subcategory_id_subcategory) 
								INNER JOIN commune ON (commune.id_commune = poi.commune_id_commune) 
								INNER JOIN pole ON (pole.id_pole = poi.pole_id_pole) 
								INNER JOIN quartier ON (quartier.id_quartier = poi.quartier_id_quartier) 
								INNER JOIN priorite ON (priorite.id_priorite = poi.priorite_id_priorite) 
								WHERE moderation_poi = 1 
									AND pole_id_pole IN (" . $_SESSION['pole'] . ") 
									AND transmission_poi = 1 
									AND delete_poi = FALSE 
								ORDER BY id_poi DESC";
                    $result = mysql_query($sql);
                    
                    $i = 0;
                    while ($row = mysql_fetch_array($result)) {
                        $i ++;
                        if ($idToFind == $row['id_poi']) {
                            $line = $i;
                        }
                    }
                    
                    if ($line != - 1) {
                        $numerofpage = ceil($line / $numRecordPerPage);
                        echo '' . $numerofpage . '';
                    } else {
                        echo '-1';
                    }
                    
                    break;
                case '4':
                    $sql = "SELECT poi.*, 
									subcategory.lib_subcategory, 
									commune.lib_commune, 
									pole.lib_pole, 
									quartier.lib_quartier, 
									priorite.lib_priorite, 
									status.lib_status, 
									x(poi.geom_poi) AS X, 
									y(poi.geom_poi) AS Y 
								FROM poi 
								INNER JOIN subcategory ON (subcategory.id_subcategory = poi.subcategory_id_subcategory) 
								INNER JOIN commune ON (commune.id_commune = poi.commune_id_commune) 
								INNER JOIN pole ON (pole.id_pole = poi.pole_id_pole) 
								INNER JOIN quartier ON (quartier.id_quartier = poi.quartier_id_quartier) 
								INNER JOIN priorite ON (priorite.id_priorite = poi.priorite_id_priorite) 
								INNER JOIN status ON (status.id_status = poi.status_id_status) 
								WHERE delete_poi = FALSE 
								ORDER BY id_poi DESC";
                    $result = mysql_query($sql);
                    
                    $i = 0;
                    while ($row = mysql_fetch_array($result)) {
                        $i ++;
                        if ($idToFind == $row['id_poi']) {
                            $line = $i;
                        }
                    }
                    
                    if ($line != - 1) {
                        $numerofpage = ceil($line / $numRecordPerPage);
                        echo '' . $numerofpage . '';
                    } else {
                        echo '-1';
                    }
                    break;
            }
            
            mysql_free_result($result);
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : normalize Input : string Output : normalize string Object : remove accent Date : Feb. 2, 2012
 */
function normalize($string)
{
    $table = array(
        'Š' => 'S',
        'š' => 's',
        'Đ' => 'Dj',
        'đ' => 'dj',
        'Ž' => 'Z',
        'ž' => 'z',
        'Č' => 'C',
        'č' => 'c',
        'Ć' => 'C',
        'ć' => 'c',
        'À' => 'A',
        'Á' => 'A',
        'Â' => 'A',
        'Ã' => 'A',
        'Ä' => 'A',
        'Å' => 'A',
        'Æ' => 'A',
        'Z' => 'C',
        'È' => 'E',
        'É' => 'E',
        'Ê' => 'E',
        'Ë' => 'E',
        'Ì' => 'I',
        'Í' => 'I',
        'Î' => 'I',
        'Ï' => 'I',
        'Ñ' => 'N',
        'Ò' => 'O',
        'Ó' => 'O',
        'Ô' => 'O',
        'Õ' => 'O',
        'Ö' => 'O',
        'Ø' => 'O',
        'Ù' => 'U',
        'Ú' => 'U',
        'Û' => 'U',
        'Ü' => 'U',
        'Ý' => 'Y',
        'Þ' => 'B',
        'ß' => 'Ss',
        'à' => 'a',
        'á' => 'a',
        'â' => 'a',
        'ã' => 'a',
        'ä' => 'a',
        'å' => 'a',
        'æ' => 'a',
        'Z' => 'c',
        'è' => 'e',
        'é' => 'e',
        'ê' => 'e',
        'ë' => 'e',
        'ì' => 'i',
        'í' => 'i',
        'î' => 'i',
        'ï' => 'i',
        'ð' => 'o',
        'ñ' => 'n',
        'ò' => 'o',
        'ó' => 'o',
        'ô' => 'o',
        'õ' => 'o',
        'ö' => 'o',
        'ø' => 'o',
        'ù' => 'u',
        'ú' => 'u',
        'û' => 'u',
        'ý' => 'y',
        'ý' => 'y',
        'þ' => 'b',
        'ÿ' => 'y',
        'Ŕ' => 'R',
        'ŕ' => 'r'
    );
    
    return strtr($string, $table);
}

/*
 * Function name : is_in_polygon Input : tableau de latitudes, tableau de longitudes, latitude et longitude du point à chercher, nombre de points Output : 0 si pas dans le polygone, 1 si dans le polygone Object : find point in polygon Date : Nov. 30, 2012
 */
function is_in_polygon($points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y)
{
    $i = $j = $c = 0;
    for ($i = 0, $j = $points_polygon; $i < $points_polygon; $j = $i ++) {
        if ((($vertices_y[$i] > $latitude_y != ($vertices_y[$j] > $latitude_y)) && ($longitude_x < ($vertices_x[$j] - $vertices_x[$i]) * ($latitude_y - $vertices_y[$i]) / ($vertices_y[$j] - $vertices_y[$i]) + $vertices_x[$i])))
            $c = ! $c;
    }
    return $c;
}

/*
 * Function name : getComments Input : id Output : json Object : get comments per ID Date : Dec. 13, 2015
 */
function getComments($id_poi)
{
    switch (SGBD) {
        case 'mysql':
            
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            $whereAppend = '';
            if ($_SESSION["type"] == 3 || $_SESSION["type"] == 2) { // is communaute de communes or pole technique
                $whereAppend = ' AND display_commentaires = \'Modéré accepté\'';
            }
            $sql = "SELECT * FROM commentaires WHERE poi_id_poi = " . $id_poi . " " . $whereAppend;
            if (DEBUG) {
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . ", SESSION type = " . $_SESSION["type"] . ", $sql \n", 3, LOG_FILE);
            }
            $result = mysql_query($sql);
            $nbrows = mysql_num_rows($result);
            
            $i = 0;
            if ($nbrows > 0) {
                while ($row = mysql_fetch_array($result)) {
                    $arr[$i]['id_commentaires'] = $row['id_commentaires'];
                    $arr[$i]['text_commentaires'] = stripslashes($row['text_commentaires']);
                    $arr[$i]['display_commentaires'] = $row['display_commentaires'];
                    $arr[$i]['url_photo'] = $row['url_photo'];
                    $arr[$i]['datecreation'] = $row['datecreation'];
                    if ($_SESSION["type"] == 4 || $_SESSION["type"] == 1) {
                        $arr[$i]['mail_commentaires'] = stripslashes($row['mail_commentaires']);
                    } else {
                        $arr[$i]['mail_commentaires'] = "******";
                    }
                    $i ++;
                }
                echo '({"total":"' . $nbrows . '","results":' . json_encode($arr) . '})';
            } else {
                echo '({"total":"0", "results":""})';
            }
            
            mysql_free_result($result);
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : editComment Input : id_comment, text_comment Output : json Object : edit comments per ID Date : Dec. 13, 2015
 */
function editComment($id_comment, $text_comment, $status_comment)
{
    switch (SGBD) {
        case 'mysql':
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $text = mysql_real_escape_string($text_comment);
            $status = mysql_real_escape_string($status_comment);
            $lastdatemodif_poi = date("Y-m-d H:i:s");
            $sql = "UPDATE commentaires SET text_commentaires = '$text', display_commentaires = '$status', lastdatemodif_comment = '$lastdatemodif_poi',lastmodif_user_comment = " . $_SESSION['id_users'] . " WHERE id_commentaires = $id_comment";
            
            $result = mysql_query($sql);
            if (DEBUG) {
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . ", $sql  \n", 3, LOG_FILE);
            }
            
            $sql = "SELECT c.poi_id_poi, p.mail_poi FROM commentaires c INNER JOIN poi p ON p.id_poi = c.poi_id_poi WHERE c.id_commentaires = " . $id_comment;
            $res = mysql_query($sql);
            $id_poi = mysql_result($res, 0, "poi_id_poi");
            $mail_poi = mysql_result($res, 0, "mail_poi");
            $sql3 = "UPDATE poi SET lastdatemodif_poi = '$lastdatemodif_poi', lastmodif_user_poi = " . $_SESSION['id_users'] . " WHERE id_poi = $id_poi";
            $result3 = mysql_query($sql3);
            
            if (! $result || ! $result3) {
                echo '2';
            } else {
                echo '1';
            }
            
            if ($status == 'Modéré accepté'){
                $subject = 'Nouveau commentaire validé sur l\'observation ' . $id_poi;
                $message = "Bonjour !\n<br />
Un nouveau commentaire a été validé\n sur l'observation n° $id_poi.\n<br />
Lien vers l'observation :\n " . URL . '/index.php?id=' . $id_poi . "\n<br />";
                $mailsFollowers = array();
                $mailsFollowers = getMailsToSendFromVotesAndComments($id_poi, $subject, "Vous recevez ce mail car vous avez souhaité\n suivre l'évolution de cette observation.\n Message envoyé à la personne\n qui a remonté l'observation : \n<br />".$message);
            
                $mails = array();
                
                /* debut envoi d'un mail au contributeur */
                $mailArray = [
                    $mail_poi,
                    "Soumetteur",
                    $subject,
                    $message
                ];
                array_push($mails, $mailArray);
                
                if (isset($mailsFollowers)) {
                    $succes = sendMails($mailsFollowers);
                }
                if (isset($mails)) {
                    $succes = sendMails($mails);
                }
            }
            
            
            
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : createPublicComment Output : json Object : add comment on POI Date : Dec. 13, 2015
 */
function createPublicComment()
{
    switch (SGBD) {
        case 'mysql':
            if (DEBUG) {
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . "\n", 3, LOG_FILE);
            }
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $id_poi = $_POST['id_poi'];
            $text = mysql_real_escape_string($_POST['text_comment']);
            $mail_commentaires = mysql_real_escape_string($_POST['mail_comment']);
            $follow = mysql_real_escape_string($_POST['beInformedField']);
            if ($follow == 'on') {
                $follow = 1;
            }else{
                $follow = 0;
            }
            $url_photo = '';
            $return = array();
            // si une photo a été associée au commentaire, on la traite
            if (isset($_FILES['photo-path']) && $_FILES['photo-path']['name'] != "") {
                if (DEBUG) {
                    error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " photo-path isset " . $_FILES['photo-path']['name'] . "\n", 3, LOG_FILE);
                }
                $dossier = '../../../resources/pictures/';
                $fichier = basename($_FILES['photo-path']['name']);
                
                $taille_maxi = maximum_upload_size();
                //$taille_maxi = 6291456;
                $taille = filesize($_FILES['photo-path']['tmp_name']);
                if (DEBUG) {
                    error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " image size =  " . $taille . ", and apache upload_max_filesize = ".$taille_maxi."\n", 3, LOG_FILE);
                }
                $extensions = array(
                    '.png',
                    '.gif',
                    '.jpg',
                    '.jpeg',
                    '.PNG',
                    '.GIF',
                    '.JPG',
                    '.JPEG'
                );
                $extension = strrchr($_FILES['photo-path']['name'], '.');
                
                if (! in_array($extension, $extensions)) {
                    $erreur = getTranslation($_SESSION['id_language'], 'ERROR');
                    $return['success'] = false;
                    $return['pb'] = getTranslation($_SESSION['id_language'], 'PICTUREPNGGIFJPGJPEG');
                }
                
                if ($taille =="") {
                    $erreur = getTranslation($_SESSION['id_language'], 'ERROR');
                    $return['success'] = false;
                    $return['pb'] = getTranslation($_SESSION['id_language'], 'PICTURESIZE')." Taille maximum autorisée : " .$taille_maxi;
                }
                
                if (! isset($erreur)) {
                    if (DEBUG) {
                        error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " pas d'erreur, on continue \n", 3, LOG_FILE);
                    }
                    $fichier = strtr($fichier, 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ_', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy-');
                    $fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
                    $fichier = 'poi_' . $id_poi . '_' . $fichier;
                    $pathphoto = $dossier . $fichier;
                    if (move_uploaded_file($_FILES['photo-path']['tmp_name'], $pathphoto)) {
                        if (DEBUG) {
                            error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " dans move_uploaded_file \n", 3, LOG_FILE);
                        }
                        $size = getimagesize($pathphoto);
                        
                        if ($size[0] > 1024 || $size[1] > 1024) {
                            if ($size[0] > $size[1]) {
                                generate_image_thumbnail($pathphoto, $pathphoto, 1024, 768);
                            } else {
                                generate_image_thumbnail($pathphoto, $pathphoto, 768, 1024);
                            }
                        }
                        
                        $size = getimagesize($pathphoto);
                        $newnamefichier = $size[0] . 'x' . $size[1] . 'x' . $fichier;
                        $newpathphoto = $dossier . $newnamefichier;
                        rename($pathphoto, $newpathphoto);
                        $url_photo = $newnamefichier;
                        
                        $return['success'] = true;
                        $return['ok'] = getTranslation($_SESSION['id_language'], 'PHOTOTRANSFERTDONE');
                    } else {
                        $return['success'] = false;
                        $return['pb'] = "Erreur lors du traitement de la photo.";
                    }
                }
            }
            
            // si une photo a été associée au commentaire et que tout s'est bien passé, ou bien s'il n'y avaotr pas de photo, on peut crer le commentaire dans la base de données
            if ((isset($_FILES['photo-path']['name']) && isset($return['success']) && $return['success'] == true) || (isset($_FILES['photo-path']['name']) && $_FILES['photo-path']['name'] == "")) {
                // si le mail est un administrateur ou un modérateur alors on bypasse la modération
                $sql2 = "SELECT id_users FROM users WHERE (usertype_id_usertype = 1 OR usertype_id_usertype = 4) AND mail_users LIKE '" . $mail_commentaires . "'";
                $result2 = mysql_query($sql2);
                $num_rows2 = mysql_num_rows($result2);
                $displayFlag = 'Modéré accepté';
                if ($num_rows2 == 0) {
                    $displayFlag = 'Non modéré';
                }
                $sql = "INSERT INTO commentaires (text_commentaires, display_commentaires, mail_commentaires, poi_id_poi,url_photo) VALUES ('$text', '$displayFlag', '$mail_commentaires',$id_poi,'$url_photo')";
                $result = mysql_query($sql);
                if (DEBUG) {
                    error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " " . $_POST['task'] . ", sql : $sql\n", 3, LOG_FILE);
                    error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " Erreur " . mysql_errno($link) . " : " . mysql_error($link) . "\n", 3, LOG_FILE);
                }
                $id_commentaire = mysql_insert_id();
                
                if (! $result) {
                    $return['success'] = false;
                    $return['pb'] = "Erreur lors de l'ajout du commentaire.";
                } else {
                    $lastdatemodif_poi = date("Y-m-d H:i:s");
                    $sql3 = "UPDATE poi SET lastdatemodif_poi = '$lastdatemodif_poi', lastmodif_user_poi = " . $_SESSION['id_users'] . " WHERE id_poi = $id_poi";
                    if (DEBUG) {
                        error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " sql $sql3 \n", 3, LOG_FILE);
                        error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " Erreur " . mysql_errno($link) . " : " . mysql_error($link) . "\n", 3, LOG_FILE);
                    }
                    $result3 = mysql_query($sql3);
                    $return['success'] = true;
                    $return['ok'] = "Le commentaire a été correctement ajouté et est directement affiché (après rechargement de la page) puisque vous êtes référencé(e) comme modérateur ou administrateur de VelObs.";
                    // si le contributeur n'est pas un modérateur ni un administrateur par ailleurs, on envoie des mails
                    if ($num_rows2 == 0) {
                        $return['ok'] = "Le commentaire a été correctement ajouté et est en attente de modération. Merci pour votre aide.";
                        $arrayObs = getObservationDetailsInArray($id_poi);
                        $arrayDetailsAndUpdateSQL = getObservationDetailsInString($arrayObs);
                        $newCommentInfo = "Nouveau commentaire : $text \nPosté par $mail_commentaires \n";
                        if ($url_photo != "") {
                            $newCommentInfo .= "Photo : " . URL . "/resources/pictures/" . $url_photo . "\n";
                        }
                        /* envoi d'un mail aux administrateurs de l'association et modérateurs */
                        $whereClause = "u.usertype_id_usertype = 1 OR (u.usertype_id_usertype = 4 AND ulp.num_pole = " . $arrayObs['pole_id_pole'] . ")";
                        $subject = 'Nouveau commentaire à modérer sur le pole ' . $arrayObs['lib_pole'];
                        $message = "Bonjour !\n<br />
Un nouveau commentaire a été ajouté\n sur le pole " . $arrayObs['lib_pole'] . ".\n Veuillez vous connecter à l'interface d'administration\n pour le modérer (cliquer sur le bouton \"Commentaires\",\n en bas à droite, une fois les détails de l'observation affichés).\n<br />
Lien vers la modération : " . URL . '/admin.php?id=' . $arrayObs['id_poi'] . "\n<br />" . $newCommentInfo . $arrayDetailsAndUpdateSQL['detailObservationString'] . "\n<br />";
                        $mails = array();
                        $mails = getMailsToSend($whereClause, $subject, $message,$arrayObs['id_poi']);
                        
                        /* debut envoi d'un mail au contributeur */
                        $subject = 'Commentaire en attente de modération';
                        $message = "Bonjour !\n<br />
Vous venez d'ajouter un commentaire à l'observation \n" . $arrayObs['id_poi'] . " sur VelObs et nous vous en remercions.\n Celui-ci devrait être administré sous peu.\n<br />" . $newCommentInfo . $arrayDetailsAndUpdateSQL['detailObservationString'] . "\n<br />
Cordialement, l'Association " . VELOBS_ASSOCIATION . " :)\n<br />";
                        $mailArray = [
                            $mail_commentaires,
                            "Soumetteur",
                            $subject,
                            $message
                        ];
                        array_push($mails, $mailArray);
                        if (DEBUG) {
                            error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " - Il y a " . count($mails) . " mails à envoyer\n", 3, LOG_FILE);
                        }
                        $succes = sendMails($mails);
                    }
                }
            }
            
            // retourne le résultat du traitement du commentaire
            echo json_encode($return);
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

/*
 * Function name : createSupport Output : json Object : add support on POI Date : Jan 30 2019
 */
function createSupport()
{
    switch (SGBD) {
        case 'mysql':
            if (DEBUG) {
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . "\n", 3, LOG_FILE);
            }
            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
            mysql_query("SET NAMES utf8mb4");
            
            $id_poi = $_POST['id_poi'];
            $follow = mysql_real_escape_string($_POST['beInformedSupportField']);
            if ($follow == 'on') {
                $follow = 1;
            }else{
                $follow = 0;
            }
            $mail = mysql_real_escape_string($_POST['mailSupportField']);
            $return = array();
            // si une photo a été associée au commentaire et que tout s'est bien passé, ou bien s'il n'y avaotr pas de photo, on peut crer le commentaire dans la base de données
            // si le mail est un administrateur ou un modérateur alors on bypasse la modération
            $sql = "INSERT INTO support_poi (poi_poi_id, support_poi_mail, support_poi_follow) VALUES ($id_poi,'$mail', '$follow')";
            $result = mysql_query($sql);
            if (DEBUG) {
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " " . $_POST['task'] . ", sql : $sql\n", 3, LOG_FILE);
                error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " Erreur " . mysql_errno($link) . " : " . mysql_error($link) . "\n", 3, LOG_FILE);
            }
            if (! $result) {
                $return['success'] = false;
                $return['msg'] = "Erreur lors de l'ajout du vote. Vous avez sans doute déjà voté pour cette observation avec l'adresse email ". $mail.".";
            } else {
                $lastdatemodif_poi = date("Y-m-d H:i:s");
                $sql3 = "UPDATE poi SET lastdatemodif_poi = '$lastdatemodif_poi', lastmodif_user_poi = " . $_SESSION['id_users'] . " WHERE id_poi = $id_poi";
                if (DEBUG) {
                    error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " sql $sql3 \n", 3, LOG_FILE);
                    error_log(date("Y-m-d H:i:s") . " " . __FUNCTION__ . " Erreur " . mysql_errno($link) . " : " . mysql_error($link) . "\n", 3, LOG_FILE);
                }
                $result3 = mysql_query($sql3);
                $return['success'] = true;
                $return['msg'] = "Votre vote a été correctement ajouté, nous vous remercions.";
                if ($follow) {
                    $return['msg'] .= " Vous serez averti(e) à chaque mise à jour de cette fiche.";
                } else {
                    $return['msg'] .= " Vous avez choisi de ne pas être averti(e) à chaque mise à jour de cette fiche.";
                }
            }
            // retourne le résultat du traitement du commentaire
            echo json_encode($return);
            mysql_close($link);
            break;
        case 'postgresql':
            // TODO
            break;
    }
}

// When you need to hash a password, just feed it to the function
// and it will return the hash which you can store in your database.
// The important thing here is that you don’t have to provide a salt
// value or a cost parameter. The new API will take care of all of
// that for you. And the salt is part of the hash, so you don’t
// have to store it separately.
//
// Links:
// http://www.sitepoint.com/hashing-passwords-php-5-5-password-hashing-api/
// http://stackoverflow.com/questions/536584/non-random-salt-for-password-hashes/536756#536756
//
// Here is a imlementation for PHP 5.5 and older:
function create_password_hash($strPassword, $numAlgo = 1, $arrOptions = array())
{
    if (function_exists('password_hash')) {
        // php >= 5.5
        $hash = password_hash($strPassword, $numAlgo, $arrOptions);
    } else {
        $salt = mcrypt_create_iv(22, MCRYPT_DEV_URANDOM);
        $salt = base64_encode($salt);
        $salt = str_replace('+', '.', $salt);
        $hash = crypt($strPassword, '$2y$10$' . $salt . '$');
    }
    return $hash;
}

function verify_password_hash($strPassword, $strHash)
{
    if (function_exists('password_verify')) {
        // php >= 5.5
        $boolReturn = password_verify($strPassword, $strHash);
    } else {
        $strHash2 = crypt($strPassword, $strHash);
        $boolReturn = $strHash == $strHash2;
    }
    return $boolReturn;
}
?>
