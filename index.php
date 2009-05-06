<?php
/*
Pubmed Central file list
EXPERIMENTAL CODE!! EXPERIMENTAL CODE!! EXPERIMENTAL CODE!!
Program outline:
  Prepares database and returns query results.
  - load database if it exists
   > if not create database
    - load text file if it exists
      > if not download text file

2009-05-05 First commit Michael Chelen http://www.mikechelen.com
  
License: Public Domain http://creativecommons.org/licenses/publicdomain/

References:
  http://www.litewebsite.com/?c=49
  http://www.sqlite.org/sqlite.html
*/

// variable config
// file name for the database
$databaseFile = "database.db";
// path in the filesystem for the database, relative to $_SERVER['DOCUMENT_ROOT']
$databasePath = "/pmc/pmcftp-filelist/";
// table name in the database
$tableName = "filelist";
// file name for the text file
$textFile = "file_list.txt";
// text file url
$url = "ftp://ftp.ncbi.nlm.nih.gov/pub/pmc/file_list.txt";
// base for the paths
$pathBase = "ftp://ftp.ncbi.nlm.nih.gov/pub/pmc/";

$inputSearch = $_GET['inputSearch'];
$inputField = $_GET['inputField'];
$inputId = $_GET['inputId'];

$resultField = "path"; // fields to return in results 
$searchField = "title"; // column names: path, title, id
$searchString = "%Plos Bio%"; // sql LIKE statement

// check if database and table exist, if not then create them 
// return a sqlite3 database handle with PDO
// if database does not exist, create it
try {
// echo 'sqlite:'.$_SERVER['DOCUMENT_ROOT'].$databasePath.$databaseFile;
  $dbHandle = new PDO('sqlite:'.$_SERVER['DOCUMENT_ROOT'].$databasePath.$databaseFile);
  }
catch( PDOException $exception ) {
  setup();
  createDatabase($databaseFile, $tableName); //create database & table
// die($exception->getMessage());
}
// check if any tables exist
$statement = $dbHandle->query('SELECT name FROM sqlite_master WHERE type = \'table\'');
$result = $statement->fetchAll();
if( sizeof($result) == 0 ) {
  createDatabase($databaseFile, $tableName); //create database & table
}
else {
//  echo ' database table exists';
  $statement = $dbHandle->query('SELECT * FROM '.$tableName);
  $result = $statement->fetchAll();
  if( sizeof($result) == 0 ) {
//  echo " should load file";
    loadFile($textFile, $tableName, $databaseFile, $url); //load text file into db
  }
  else {
    $sql = 'SELECT '.$resultField.' FROM '.$tableName.' WHERE '.$searchField.' LIKE "'.$searchString.'"';
//  echo $sql;
    $statement = $dbHandle->query($sql);
    $result = $statement->fetchAll();
    for($i=0; $i<count($result); $i++)
    {
      $row = $result[$i];
      if ($searchField="path")
      {
      echo $pathBase;
      }
      echo $row[0]."\n";
    }
  //  print_r($result);
  }
}

function createDatabase ($databaseFile,$tableName) {
//create database & table
  $cmd =('sqlite3 '.$databaseFile.' \'create table '.$tableName.' ( path varchar(128), title varchar(128), id varchar(128) );\'');
//print_r($cmd);
  exec($cmd);
}

function loadFile ($textFile, $tableName, $databaseFile, $url) {
//download text file
  downloadFile($url,$textFile);
  $cmd= ' sh loadtext.sh';
//$cmd = escapeshellcmd($cmd);
print_r($cmd);
  exec($cmd);
}

function downloadFile ($url,$textFile) {
//download file by url if it does not exist
  if(!file_exists($textFile)) {
    exec("wget $url");
//  trim header row
    $cmd = 'sed -i \'1d\' '.$textFile;
    exec($cmd);
  }
}

/*
if ($inputSearch && $inputSearch && $inputId) {
}
else{
  print_r('<form action="index.php" method="get">
  Search: <input type="text" name="inputSearch" />
  Field: <input type="text" name="inputField" />
  ID: <input type="text" name="inputId" />
  <input type="submit" />
  </form>');

  print_r($inputSearch);
  print_r($inputField);
  print_r($inputId);
}
*/

// $result = getResult($databaseFile, $tableName, $searchField, $searchString, $url, $resultField, $textFile);

// $finalOutput = $result;

// print_r $finalOutput;

/*

to do

*/
// load database and check for table



/* Functions
 *
 *
 *
 *
 */

/* function to get the results from the database
should ideally return a result array from fetch() 
*/
function getResult ($databaseFile, $tableName, $searchField, $searchString, $url, $resultField, $textFile) {

$cmd = 'sqlite3 '.$databaseFile.' \'select * from '.$tableName.'\' | awk \'{printf "<tr><td>%s<td>%s\n",$1,$2 }\'';

// sqlite3 database.db 'select * from filelist' | awk '{printf "%s,%s,%s\n",$1,$2,$3 }'


//  see if table needs creation 
    if ($q === false) {

createTable($databaseFile,$tableName);       
     
      }
    else {
    
    //    load text file
      loadfile($textFile, $tableName, $databaseFile, $url);

        $q = @$db->query('SELECT * FROM ' . $tableName .' WHERE '. $searchField .' LIKE "'.$searchString.'"');
    
    
      $sql='SELECT '.$resultField.' FROM '.$tableName.' WHERE '.$searchField.' LIKE "'.$searchString.'";';
      print_r($sql);
      $q = @$db->query($sql);
      $result = $q->fetch();
      return $result;
    }
}

function createTable ($databaseFile,$tableName) {
//    create table
  $cmd =('sqlite3 '.$databaseFile.' \'create table '.$tableName.' ( path varchar(128), title varchar(128), id varchar(128) );\'');
// sqlite3 database.db 'create table filelist ( path varchar(128), title varchar(128), id varchar(128) );'
//          print_r($cmd);
  exec($cmd);
}



function loadDatabase ($textFile,$tableName) {

if ($db = new SQLiteDatabase('database.db')) {

  // tries to load a query from the db
  $q = @$db->query('SELECT * FROM ' . $tableName);
  if ($q === false) {
    return false;
  }
    
  $q = @$db->query('SELECT * FROM ' . $tableName .'filelist WHERE title LIKE "%Plos Bio%"');

  // checks if any query results have been returned
  if ($q === false) {
/*            $db->queryExec('CREATE TABLE tableName (id int, requests int, PRIMARY KEY (id)); INSERT INTO tableName VALUES (1,1)');
            $hits = 1;        */
    }
        else {
            $result = $q->fetchSingle();
            echo $result;
        }
        
    } else {
    die($err);
    }


}


// tries to load database object can be created

/*
$stringData = "Floppy Jalopy\n";
fwrite($fh, $stringData);
$stringData = "Pointy Pinto\n";
fwrite($fh, $stringData);
fclose($fh);
*/


?>

