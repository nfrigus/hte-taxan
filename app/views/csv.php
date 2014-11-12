<?php

isset($delimiter) or $delimiter = ";";
isset($enclosure) or $enclosure = '"';
isset($nl) or $nl = "\n";


foreach($data as $row){
	echo $enclosure.implode($enclosure.$delimiter.$enclosure,$row).$enclosure.$nl;
}