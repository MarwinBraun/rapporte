<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');



$serverName = "192.168.252.98\KWP";
$connectionInfo = array("Database"=>"BNWINS", "UID"=>"sa", "PWD"=>"kwpsarix", "CharacterSet" => "UTF-8", "TrustServerCertificate"=>true);
$conn = sqlsrv_connect( $serverName, $connectionInfo);
if( $conn === false ) {
     die( print_r( sqlsrv_errors(), true));
}


$adrkuerzel = $_GET['adrkuerzel'];
$datum = $_GET['datum'];
$datumbis = $_GET['datumbis'];


$sql = "Select MontJourn.MontJournID, MontJourn.AuftrErledigt, Projekt.ProjNr, Projekt.AuftragsNr as BestellNr, Projekt.Benutzer1 as SAPNr, FORMAT(MontJourn.Termin, 'dd.MM.yyyy') as Termin, 
MontJourn.AuftrErledigt, Projekt.Auftraggeber, adrAdressen.Name,Betreff, AuftrAusgefArb from MontJourn, adrAdressen, Projekt 
where MontJourn.ADRNRGES = adrAdressen.AdrNrGes and MontJourn.VorgangsNr = Projekt.ProjNr and 
MontJourn.ADRNRGES = '".$adrkuerzel."'
and Status = 60 and convert(date, MontJourn.Termin) >= '".$datum."' and convert(date, MontJourn.Termin) <= '".$datumbis."' order by MontJourn.Termin desc";

$stmt = sqlsrv_query( $conn, $sql);
if( $stmt === false ) {
     die( print_r( sqlsrv_errors(), true));
}

$json = new stdClass();
$json->daten = [];

while($obj = sqlsrv_fetch_object( $stmt)) {
$helper = new stdClass();

$helper->MontJournID = utf8_encode(utf8_decode($obj->MontJournID));
$helper->ProjNr = utf8_encode(utf8_decode($obj->ProjNr));
$helper->AuftrErledigt = utf8_encode(utf8_decode($obj->AuftrErledigt));
$helper->BestellNr = utf8_encode(utf8_decode($obj->BestellNr));
$helper->SAPNr = utf8_encode(utf8_decode($obj->SAPNr));
$helper->Termin = utf8_encode(utf8_decode($obj->Termin));
$helper->Auftraggeber = utf8_encode(utf8_decode($obj->Auftraggeber));
$helper->Name = utf8_encode(utf8_decode($obj->Name));
$helper->Betreff = utf8_encode(utf8_decode($obj->Betreff));
$helper->AuftrAusgefArb = utf8_encode(utf8_decode($obj->AuftrAusgefArb));


array_push($json->daten, $helper);




}

echo json_encode($json);











?>