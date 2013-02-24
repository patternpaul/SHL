<?php
/*
* example.php
* class_db.php example usage
* Author: Troy Wolf (troy@troywolf.com)
*/
/*
Include the class. Modify path according to where you put the class file.
*/
require_once(dirname(__FILE__).'/class_db.php'); 

/*
Instantiate a new db object. If you have multiple databases to connect to
or need to work with multiple datasets at the same time, you can create
more instances. Within the class, you can define your connections. You can
then pass an index to the constructor to select a specific connection. You
can define multiple databases of the same type or different types. The default id is zero, so if you pass in nothing, the zero case will be used.
In this example, we use the default connection.
*/
$d = new db(0); 

/*
Where do you want to store your cache files?
Default is current dir. You can set it here, or hard-code in the class.
You must end this value with a "/".
*/
$d->dir = "/home/foo/bar/"; 

/*
Execute a query to return data. fetch() returns an index array where each item
is a row produced by your query.  Each item in this array is an associative
array where each item is a column from your query. fetch() returns FALSE if
there was a failure. In this example, we've decided not to use caching.
*/
$data = $d->fetch("select * from users order by last_name");
if ($data === FALSE) {
  /*
  There was a problem with the fetch! The class has a 'log' property that
  contains a log of events. This log is useful for testing and debugging.
  */
  echo "<h2>db fetch() failed!</h2>";
  echo $d->log;
  exit();
} 

/*
Execute a query, but this time, cache the data using the name "cars_less_100000",
and consider the cached data good for 5 minutes.
*/
$sql = "select year, make, model, mileage"
  ." from cars"
  ." where mileage < 100000"
  ." order by mileage";

$data = $d->fetch($sql, 300, "cars_less_100000");
if ($data === FALSE) {
  /*
  There was a problem with the fetch! The class has a 'log' property that
  contains a log of events. This log is useful for testing and debugging.
  */
  echo "<h2>db fetch() failed!</h2>";
  echo $d->log;
  exit();
} 

/*
Optionally, you can leave off the query name, and one will be automatically
generated using an MD5 hash of the SQL statement.
*/
$data = $d->fetch($sql, 300);
if ($data === FALSE) {  
  /*
  There was a problem with the fetch! The class has a 'log' property that
  contains a log of events. This log is useful for testing and debugging.
  */
  echo "<h2>db fetch() failed!</h2>";
  echo $d->log;
  exit();
} 

/*
A special TTL value of "daily" means the data is "good" if it was queried
today. Otherwise, get the data from the database and recreate the cache file.
*/
$data = $d->fetch($sql, "daily");
if ($data === FALSE) {
  /*
  There was a problem with the fetch! The class has a 'log' property that
  contains a log of events. This log is useful for testing and debugging.
  */
  echo "<h2>db fetch() failed!</h2>";
  echo $d->log;
  exit();
} 


/*
The dump() method outputs a basic table of the data. This is useful for
testing and debugging. Pass in the data array returned by fetch.
Review the dump() method in the class file for an example of how to work with
the dataset returned in the data array.
*/
$d->dump($data);

/*
Here is how to iterate through the rows in the data[] array returned by fetch().
*/
foreach($data as $row) {
  echo "<hr />Year: ".$row['year']
    ."<br />Make: ".$row['make']
    ."<br />Model: ".$row['model']
    ."<br />Mileage: ".formatnumber($row['mileage'],0);
}

/*
Access a specific column in a specific row.
*/
echo "<hr />Data in the 'model' column of the 5th row: ".$data[4]['model'];


/*
You can use the static methods fmt() and fmt2() to help create your SQL statements. Read the comments in the class file for more detail.
*/
$sql = "insert into cars (year,make,model,mileage) VALUES ("
  .db::fmt($year,0)
  .db::fmt2($make,0)
  .db::fmt2($model,0)
  .db::fmt2($mileage,1)
  .")";

/*
Use the exec() method for any SQL statement that does not return a dataset such
as INSERT, UPDATE, and DELETE statements. exec() returns the number of rows
affected or FALSE if failure.
*/
$rows_affected = $d->exec($sql);
if ($rows_affected === FALSE) {
  /*
  There was a problem with the query! The class has a 'log' property that
  contains a log of events. This log is useful for testing and debugging.
  */
  echo "<h2>Query execution failed!</h2>";
  echo $d->log;
  exit();
}

/*
Two ways to see the number of affected rows. Either use the returned value or
the object property. (This number is also in the object's log text.)
*/
echo "<br />".$rows_affected." rows affected<br />";
echo "<br />".$d->rows_affected." rows affected<br />";

/*
For INSERTs, if your table has an identity column or autonumber column, you can
use the last_id property.
*/
echo "New ID: ".$d->last_id."<br />";


/*
The log property contains a log of the objects events. Very useful for
testing and debugging. If there are problems, the log will tell you what
is wrong. For example, if the cache dir specified does not have write privs,
the log will tell you it could not open the cache file. If there is an error
in your sql statement, the log will tell you what it is.
*/
echo "<h1>Log</h1>";
echo $d->log; 
?>
