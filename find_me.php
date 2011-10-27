<?php

include "dbh.php";
$table_name=$_GET['table'];


$sql2 = "SELECT gid, st_asgeojson(st_transform(the_geom,4326)) as the_geom from ".$table_name." WHERE ST_Intersects(".$table_name.".the_geom,(SELECT setsrid(the_geom,4326) from dblink('dbname=".$oc_db." port=5432 user=".$oc_id." password=".$oc_pw."','SELECT the_geom FROM oc_geo where rid=".$_GET['id']."') as s(the_geom geometry)))";


$result = pg_query($dbh, $sql2);
 if (!$result) {
     die("Error in SQL query: " . pg_last_error());
 } else {

    $geojson = array(
      'type'      => 'FeatureCollection',
      'features'  => array()
   );
	$m=0;
	while ($row = pg_fetch_assoc($result)) {
	$m++;

       $feature = array(
         'type' => 'Feature',
         'geometry' => json_decode($row['the_geom'], true),
         'crs' => array(
            'type' => 'EPSG',
            'properties' => array('code' => '4326')
         ),
         'properties' => array(
            'id' => $row['gid']			
         )
      );

	$gid=$row['gid'];
		// Add feature array to feature collection array
	      array_push($geojson['features'], $feature);
	}
echo $gid.":::";
echo json_encode($geojson);
}

?>
