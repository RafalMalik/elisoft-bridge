<?php

require_once('api.php');
require_once('config.php');

$elisoftDocuments = json_decode(call('/elisoft_documents.json'));

// Etap 1 - pobieranie z API
if (count($elisoftDocuments) > 0) {

    foreach ($elisoftDocuments as $document) {
        // 1. Insert to database 
        var_dump($conn->execute('INSERT INTO [dbo].[ExtDokument] ([RodzajDokumentu]) VALUES (9)'));

        var_dump($document);
        // 2. Send post to elisoftDocuments/id with status + 1
        //var_dump($document);
//        $document->type = 5;
//        $resp = call('/elisoft_documents/' . $document->id, 'PUT', json_encode(array('type' => 5)));
//        var_dump(json_decode($resp));
//
//        $resp = call('/elisoft_documents', 'POST', json_encode($document));
//        var_dump(json_decode($resp));
    }
} else {
    echo "Brak dokumentow do pobrania";
}


// Etap 2 - sprawdzanie faktur w Elisoft i ich wysylka na API

