<?php

require_once('api.php');
require_once('config.php');

$elisoftDocuments = call('/api/elisoft_documents');

// Etap 1 - pobieranie z API
if (count($elisoftDocuments) > 0) {

    foreach ($elisoftDocuments as $document) {

        // 1. Insert to database 
        $guid = com_create_guid();
//        $contractor = call($document->contractor);
        $contractor = $conn->select("SELECT TOP 1 * FROM [dbo].[Kontrahenci] ORDER BY [ID_Kontrahenta] DESC");

        $contractorId = $contractor['ID_Kontrahenta'];

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
                . "'" . $document->type . "',"
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
        $resp = call('/api/elisoft_documents/' . $document->id, 'PUT', json_encode($document));
        var_dump($resp);
//
//        $resp = call('/elisoft_documents', 'POST', json_encode($document));
//        var_dump(json_decode($resp));
    }
} else {
    echo "Brak dokumentow do pobrania";
}


// Etap 2 - sprawdzanie faktur w Elisoft i ich wysylka na API
$invoices = $conn->select("SELECT TOP 1 * FROM [dbo].[Faktury] ORDER BY [ID_Faktury] DESC");

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