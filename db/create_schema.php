<?php
function create_schema($dbHost, $dbUsername, $dbPassword, $dbName) {
	$link = mysql_connect ( $dbHost, $dbUsername, $dbPassword );
	if (! $link) {
		echo ( 'Could not connect: ' . mysql_error () );
		exit(1);
	}
	
	$query = "DROP DATABASE IF EXISTS  " . $dbName . ";";
	$result = mysql_query ( $query, $link );
	if (! $result) {
		echo ( "Query invalida [$query]: " . mysql_error () );
		exit(1);
	}
	
	$query = "CREATE DATABASE IF NOT EXISTS  " . $dbName . ";";
	$result = mysql_query ( $query, $link );
	if (! $result) {
		echo ( "Query invalida [$query]: " . mysql_error () );
		exit(1);
	}
	echo "CREADA BASE Correctamente\n";
	
	mysql_select_db ( $dbName, $link );
	
	echo "Creando tablas...\n";

	$queries = explode ( ";\n", file_get_contents ( dirname ( __FILE__ ) . "/db.sql" ) );
	
	foreach ( $queries as $id => $query ) {
		if ($query != '') {
	
			$result = mysql_query ( $query, $link );
			if (! $result) {
				echo "Invalid query [$query]: " . mysql_error ();
				exit(1);
			}
		}
	}

	echo "Cargando Referenciales...\n";

	$queries = explode ( ";\n", file_get_contents ( dirname ( __FILE__ ) . "/common7.sql" ) );
	
	foreach ( $queries as $id => $query ) {
		if ($query != '') {
	
			$result = mysql_query ( $query, $link );
			if (! $result) {
				echo "Invalid query [$query]: " . mysql_error ();
				exit(1);
			}
		}
	}

	mysql_close ( $link );
	
	echo "Creada correctamente la base en $dbHost" . PHP_EOL;
}

function exec_query($dbHost, $dbUsername, $dbPassword, $dbName, $query) {
	$link = mysql_connect ( $dbHost, $dbUsername, $dbPassword );
	
	mysql_select_db ( $dbName, $link );
	
	if (! $link) {
		echo ( 'Could not connect: ' . mysql_error () );
		exit(1);
	}
	
	$result = mysql_query ( $query, $link );
	if (! $result) {
		echo "Query invalida  [$query]: " . mysql_error ();
		exit(1);
	}
	
	mysql_close ( $link );
}

// Se llama desde la linea de comandos entonces creo la base

if(php_sapi_name() == "cli") {
	set_include_path(implode(PATH_SEPARATOR, array(realpath('../library') , get_include_path())));
	require ('Zend/Config/Ini.php');

    $c = new Zend_Config_Ini('../application/configs/application.ini', 'production');
    
    create_schema(
	    $c->resources->db->params->host,
	    $c->resources->db->params->username,
	    $c->resources->db->params->password,
	    $c->resources->db->params->dbname
	);
}