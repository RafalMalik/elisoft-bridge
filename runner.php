<?php

require_once('api.php');
require_once('config.php');

$elisoftDocuments = call('/api/elisoft_documents');

//var_dump($elisoftDocuments);exit();

// Etap 1 - pobieranie z API
if (count($elisoftDocuments) > 0) {

    foreach ($elisoftDocuments as $document) {

        // 1. Insert to database 
        $guid = getGUID();

//var_dump($guid);exit();

        //$contractor = $conn->select("SELECT TOP 1 * FROM [dbo].[Kontrahenci] ORDER BY [ID_Kontrahenta] DESC");

        $contractorId = 236540;

        $extDokument = $conn->execute("INSERT INTO [dbo].[ExtDokument] ("
                . "[Guid],"
                . "[RodzajDokumentu],"
                . "[ID_Kontrahenta],"
                . "[MiejsceWystawienia],"
                . "[ZaplataDni],"
                . "[ZaplataSposob],"
                . "[NumerKonta],"
                . "[DokumentWystawil],"
                . "[DokumentOdebral],"
                . "[WalutaSymbol],"
                . "[WalutaKurs],"
                . "[LiczOdCenBrutto],"
                . "[Uwagi],"
                . "[WyslijMail],"
                . "[External_ID]"
                . ") VALUES ("
                . "'" . $guid . "',"
                . "" . $document->type . ","
                . "'" . $contractorId . "',"
                . "'" . $document->placeOfIssue . "',"
                . "'" . $document->daysToPay . "',"
                . "'" . $document->method . "',"
                . "'" . $document->accountNumber . "',"
                . "'" . $document->issuing . "',"
                . "'" . $document->receiver . "',"
                . "'" . $document->currencySymbol . "',"
                . "'" . $document->currencyCourse . "',"
                . "'" . $document->isBrutto . "',"
                . "'" . $document->note . "',"
                . "'" . $document->sendMail . "',"
                . "'" . $document->id . "'"
                . ")");

var_dump($extDokument);
exit();

        foreach ($document->elisoftDocumentRows as $row) {
            $extDokumentWiersz = $conn->execute("INSERT INTO [dbo].[ExtDokumentWiersz] ("
                    . "[GUID_Dokumentu],"
                    . "[NazwaTowaru],"
                    . "[KodTowaru],"
                    . "[KodPKWiU],"
                    . "[Jednostka],"
                    . "[Cena],"
                    . "[Ilosc],"
                    . "[Vat],"
                    . "[Rabat],"
                    . "[LiczOdCenBrutto]"
                    . ") VALUES ("
                    . "'" . $guid . "',"
                    . "'" . $row->name . "',"
                    . "'" . $row->code . "',"
                    . "'" . $row->externalCode . "',"
                    . "'" . $row->unit . "',"
                    . "'" . $row->price . "',"
                    . "'" . $row->quantity . "',"
                    . "'" . $row->tax . "',"
                    . "'" . $row->discount . "',"
                    . "'" . $row->isBrutto . "'"
                    . ")");
        }
        // 2. Send post to elisoftDocuments/id with status + 1
        unset($document->id);
        $resp = call('/api/elisoft_documents', 'PUT', $document);
        var_dump(json_decode($resp));
    }
} else {
    echo "Brak dokumentow do pobrania";
}


// Etap 2 - sprawdzanie faktur w Elisoft i ich wysylka na API
$invoices = $conn->select("SELECT TOP 1 * FROM [dbo].[Faktury] ORDER BY [ID_Faktury] DESC");
if (is_array($invoices)) {
    foreach ($invoices as $invoice) {
        $invoiceToSend = new stdClass();
        $invoiceToSend->number = $invoice["NrFaktury"];
        $invoiceToSend->amount = $invoice["WartoscBrutto"];
        $invoiceToSend->issuing = $invoice["FaktureWystawil"];
        $invoiceToSend->purchasing = $invoice["FaktureOdebral"];
        $invoiceToSend->createdAt = $invoice["DataWystawienia"];
        $invoiceToSend->currency = $invoice["WalutaSymbol"];
        $invoiceToSend->status = $invoice["Zaplacona"];
        $invoiceToSend->errands = []; //a to co?
        $invoiceToSend->rental = ''; //j.w
        $invoiceToSend->name = $invoice["Nabywca_Nazwa"];
        $invoiceToSend->dateOfPayment = $invoice["TerminZaplaty"]; //czy DataZaplaty?

        $resp = call('/api/invoices', 'POST', json_encode($invoiceToSend));
    }
}



function getGUID(){
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }
    else {
     return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }
}


