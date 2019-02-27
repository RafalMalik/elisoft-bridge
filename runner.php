<?php

require_once('api.php');
require_once('config.php');

$elisoftDocuments = json_decode(call('/elisoft_documents.json'));

var_dump($elisoftDocuments);

// Etap 1 - pobieranie z API
if (count($elisoftDocuments) > 0) {

    foreach ($elisoftDocuments as $document) {
        // 1. Insert to database 
        //var_dump($conn->execute('INSERT INTO [dbo].[ExtDokument] ([RodzajDokumentu]) VALUES (9)'));

        //var_dump($document);
        // 2. Send post to elisoftDocuments/id with status + 1
        $document->type = 2;
        $resp = call('/elisoft_documents/' . $document->id, 'PUT', $document);
        var_dump(json_decode($resp));
//
unset($document->id);
        $resp = call('/elisoft_documents', 'POST', $document);
        var_dump(json_decode($resp));
    }
} else {
    echo "Brak dokumentow do pobrania";
}


// Etap 2 - sprawdzanie faktur w Elisoft i ich wysylka na API

