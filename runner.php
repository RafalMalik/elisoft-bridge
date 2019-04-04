<?php

require_once('api.php');
require_once('config.php');

$elisoftDocuments = call('/api/elisoft_documents.json?status=0');

// Etap 1 - pobieranie z API
if (count($elisoftDocuments) > 0) {

    foreach ($elisoftDocuments as $document) {

        // 1. Insert to database 
        $guid = getGUID();

        $contractor = $conn->selectOne(sprintf("SELECT TOP 1 [ID_Kontrahenta] FROM [dbo].[Kontrahenci] WHERE Replace([Nip],'-','') = '%s'", $document->contractor->nin));
        if ($contractor && isset($contractor['ID_Kontrahenta'])) {
            $contractorKey = "[ID_Kontrahenta],";
            $contractorValue = "'" . $contractor['ID_Kontrahenta'] . "',";
        } else {
            $contractorKey = "[Kontrahent_Nip],"
                    . "[Kontrahent_Nazwa],"
                    . "[Kontrahent_Ulica],"
                    . "[Kontrahent_Numer],"
                    . "[Kontrahent_KodPocztowy],"
                    . "[Kontrahent_Miejscowosc],"
                    . "[Kontrahent_Panstwo],";
            $contractorValue = "'" . $document->contractor->nin . "',"
                    . "'" . $document->contractor->name . "',"
                    . "'" . $document->contractor->address->street . "',"
                    . "'" . $document->contractor->address->number . "',"
                    . "'" . $document->contractor->address->postCode . "',"
                    . "'" . $document->contractor->address->city . "',"
                    . "'" . $document->contractor->address->country . "',";
        }

        $extDokument = $conn->execute("INSERT INTO [dbo].[ExtDokument] ("
                . "[Guid],"
                . "[RodzajDokumentu],"
                . $contractorKey
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
                . "[External_ID],"
                . "[External_Symbol]"
                . ") VALUES ("
                . "'" . $guid . "',"
                . "" . $document->type . ","
                . $contractorValue
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
                . "'" . $document->id . "',"
                . "'0'"
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

        $document->status = 1;
        $resp = call('/api/elisoft_documents/' . $document->id, 'PUT', $document);
    }
} else {
    echo "Brak dokumentow do pobrania";
}
$completed = $conn->selectAll("SELECT [External_id] FROM [dbo].[extDokument] WHERE [External_Symbol] = 0 AND [IsCompleted] = 1");


foreach ($completed as $row) {
    $resp = call('/invoice/elisoft/mark/' . $row['External_id'], 'GET');
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

function getGUID() {
    if (function_exists('com_create_guid')) {
        return com_create_guid();
    } else {
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }
}
