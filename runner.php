<?php

include('api.php');

$elisoftDocuments = json_decode(call('/elisoft_documents.json'));

// Etap 1 - pobieranie z API
if (count($elisoftDocuments) > 0) {
	
	foreach ($elisoftDocuments as $document) {
		// 1. Insert to database 
		// 2. Send post to elisoftDocuments/id with status + 1
			var_dump($document);
	}
	
} else {
	echo "Brak dokumentow do pobrania";
}


// Etap 2 - sprawdzanie faktur w Elisoft i ich wysylka na API

