<?php
query_patch("update variable set valeur='1542' where nom='version'");

query_patch("update pays set isoalpha2='MG' where isoalpha2='MD' and isoalpha3='MDG'");