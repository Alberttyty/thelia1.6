<?php
if (! function_exists('mysql_query')) {
    function mysql_connect($host, $login, $password)
    {
        return mysqli_connect($host, $login, $password);
    }

    function mysql_error($link)
    {
        return mysqli_error($link);
    }

    function mysql_fetch_object($resul, $classname = "stdClass")
    {
        return mysqli_fetch_object($resul, $classname);
    }

    function mysql_insert_id($link)
    {
        return mysqli_insert_id($link);
    }

    function mysql_num_rows($res)
    {
        return mysqli_num_rows($res);
    }

    function mysql_query($query, $link = null)
    {
        if ($link == null) $link = (new Cnx())->link;
        return mysqli_query($link, $query);
    }

    function mysql_real_escape_string($string, $link = null)
    {
        return mysqli_real_escape_string($link, $string);
    }

    function mysql_result($res, $row=0, $col=0)
    {
        return mysqli_result($res, $row=0, $col=0);
    }

    function mysql_select_db($dbName, $link)
    {
        return mysqli_select_db($link, $dbName);
    }
}

function mysqli_result($res,$row=0,$col=0) {
    $numrows = mysqli_num_rows($res);

    if ($numrows && $row <= ($numrows-1) && $row >=0) {
        mysqli_data_seek($res,$row);
        $resrow = (is_numeric($col)) ? mysqli_fetch_row($res) : mysqli_fetch_assoc($res);
        if (isset($resrow[$col])) return $resrow[$col];
    }

    return false;
}
?>
