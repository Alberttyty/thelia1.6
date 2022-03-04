<?php

query_patch("update variable set valeur='1533' where nom='version'");

$sanitize = query_patch("SELECT id FROM variable where nom='sanitize_admin'");

if(! mysql_num_rows($sanitize) ){
    query_patch("insert into variable(nom, valeur, protege, cache) values('sanitize_admin', 1, 1, 0)");
}



?>