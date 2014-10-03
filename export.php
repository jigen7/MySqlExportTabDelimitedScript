<?php // RAY_db_to_excel.php
error_reporting(E_ALL);
echo "<pre>\n";


// DEMONSTRATE HOW TO EXPORT A TABLE SO THAT IT CAN BE USED IN EXCEL


// SET YOUR TABLE NAME HERE - OR MAYBE USE THE URL GET ARGUMENT?
$table_name = '';


// CONNECTION AND SELECTION VARIABLES FOR THE DATABASE
$db_host = ""; // PROBABLY THIS IS OK
$db_name = "";        // GET THESE FROM YOUR HOSTING COMPANY
$db_user = "";
$db_word = "";

// OPEN A CONNECTION TO THE DATA BASE SERVER
if (!$db_connection = mysql_connect("$db_host", "$db_user", "$db_word"))
{
    $errmsg = mysql_errno() . ' ' . mysql_error();
    echo "<br/>NO DB CONNECTION: ";
    echo "<br/> $errmsg <br/>";
}

// SELECT THE MYSQL DATA BASE
if (!$db_sel = mysql_select_db($db_name, $db_connection))
{
    $errmsg = mysql_errno() . ' ' . mysql_error();
    echo "<br/>NO DB SELECTION: ";
    echo "<br/> $errmsg <br/>";
    die('NO DATA BASE');
}

// OPEN THE CSV FILE - PUT YOUR FAVORITE NAME HERE
$csv = 'EXPORT_' . date('Ymdhis') . "_$table_name" . '.csv';
$fp  = fopen($csv, 'w');

// GET THE COLUMN NAMES
$sql = "SHOW COLUMNS FROM $table_name";
if (!$res = mysql_query($sql))
{
    $errmsg = mysql_errno() . ' ' . mysql_error();
    echo "<br/>QUERY FAIL: ";
    echo "<br/>$sql <br/>";
    die($errmsg);
}
if (mysql_num_rows($res) == 0)
{
    die("WTF? $table_name HAS NO COLUMNS");
}
else
{
    // MAN PAGE: http://php.net/manual/en/function.mysql-fetch-assoc.php
    while ($show_columns = mysql_fetch_assoc($res))
    {
        $my_columns[] = $show_columns["Field"];
    }
    // var_dump($my_columns); ACTIVATE THIS TO SEE THE COLUMNS
}

// WRITE THE COLUMN NAMES TO THE CSV
if (!fputcsv($fp, $my_columns)) die('DISASTER');

// GET THE ROWS OF DATA
$sql = "SELECT * FROM $table_name";
$res = mysql_query($sql);
if (!$res)
{
    $errmsg = mysql_errno() . ' ' . mysql_error();
    echo "<br/>QUERY FAIL: ";
    echo "<br/>$sql <br/>";
    die($errmsg);
}

// ITERATE OVER THE DATA SET
while ($row = mysql_fetch_row($res))
{
    // WRITE THE COMMA-SEPARATED VALUES.  MAN PAGE http://php.net/manual/en/function.fputcsv.php
    if (!fputcsv($fp, $row)) die('CATASTROPHE');
}

// ALL DONE
fclose($fp);

// SHOW THE CLIENT A LINK
echo "<p><a href=\"$csv\">$csv</a></p>\n";