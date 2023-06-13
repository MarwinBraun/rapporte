<?php
require_once __DIR__ . '/vendor/autoload.php'; 

header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');


$serverName = "192.168.252.98\KWP";
$connectionInfo = array("Database"=>"BNWINS", "UID"=>"sa", "PWD"=>"kwpsarix", "CharacterSet" => "UTF-8", "TrustServerCertificate"=>true);
$conn = sqlsrv_connect( $serverName, $connectionInfo);
if( $conn === false ) {
     die( print_r( sqlsrv_errors(), true));
}

$kuerzel = $_GET['kuerzel'];
$zeitraumvon = $_GET['zeitraumvon'];
$zeitraumbis = $_GET['zeitraumbis'];

require('mc_table.php');




    $pdf = new PDF_MC_Table('P', 'mm', 'A4');

    $sql = "Select MontJourn.MontJournID, MontJourn.AuftrErledigt, Projekt.ProjNr, Projekt.AuftragsNr as BestellNr, Projekt.Benutzer1 as SAPNr, FORMAT(MontJourn.Termin, 'dd.MM.yyyy') as Termin, 
    MontJourn.AuftrErledigt, Projekt.Auftraggeber, adrAdressen.Name,Betreff, AuftrAusgefArb from MontJourn, adrAdressen, Projekt 
    where MontJourn.ADRNRGES = adrAdressen.AdrNrGes and MontJourn.VorgangsNr = Projekt.ProjNr and 
    MontJourn.ADRNRGES = '".$kuerzel."'
    and Status = 60 and convert(date, MontJourn.Termin) >= '".$zeitraumvon."' and convert(date, MontJourn.Termin) <= '".$zeitraumbis."' order by MontJourn.Termin desc";

    $stmt = sqlsrv_query( $conn, $sql);
    if( $stmt === false ) {
     die( print_r( sqlsrv_errors(), true));
    }

    while($obj = sqlsrv_fetch_object( $stmt)) {

        $pdf->AddPage();

        // Spaltenüberschriften
        $pdf->SetFont('Arial', '', 6);
          
        $pdf->SetY(45);
        $pdf->Cell(120, 20, iconv('UTF-8', 'windows-1252',''), 1, 0, 'L');
        $pdf->Cell(70, 20, iconv('UTF-8', 'windows-1252',''), 1, 0, 'L');
        $pdf->SetFont('Arial', '', 9.2);
        $pdf->Text(11, 48, iconv('UTF-8', 'windows-1252', 'Ich bescheinige die Stundenleistung einschließlich Wegzeiten sowie den auf den'));
        $pdf->Text(11, 51, iconv('UTF-8', 'windows-1252', 'Aufmaßblättern aufgeführten '));
        $pdf->SetFont('Arial', 'B', 9.2);
        $pdf->Text(53, 51, iconv('UTF-8', 'windows-1252', 'Materialverbrauch.'));
        $pdf->SetFont('Arial', '', 9.2);
        $pdf->Text(11, 54, iconv('UTF-8', 'windows-1252', 'Mit der Berechnung bin ich einverstanden und anerkenne Ihre Forderung.'));
        $pdf->SetFont('Arial', '', 9.2);
        $pdf->Text(11, 59, iconv('UTF-8', 'windows-1252', '________________________________________________________________'));
        $pdf->SetFont('Arial', '', 9.2);
        $pdf->Text(11, 63, iconv('UTF-8', 'windows-1252', '(Datum)'));
        $pdf->Text(56, 63, iconv('UTF-8', 'windows-1252', '(Kunde)'));
       
    
        $pdf->Text(132, 48, iconv('UTF-8', 'windows-1252', 'Fahrkilometer'));
        $pdf->Text(160, 48, iconv('UTF-8', 'windows-1252', 'pauschal'));
        $pdf->Text(179, 48, iconv('UTF-8', 'windows-1252', 'km'));
        $pdf->Text(132, 49, iconv('UTF-8', 'windows-1252', '___________________________________'));
    
        $pdf->Text(132, 59, iconv('UTF-8', 'windows-1252', 'Material lt. Aufmaßblatt Seite ___ bis ___'));
        $pdf->Text(132, 60, iconv('UTF-8', 'windows-1252', '___________________________________'));
    
        $pdf->Text(8, 15, iconv('UTF-8', 'windows-1252', 'Die gestzlichen Pausenzeiten wurden eingehalten.'));
    
    
        $pdf->Image('Stempel.png', 11, 18, 35, 17, 'PNG');
    
        $pdf->Image('https://www.hoermann-fn.de/fileadmin/platzhirsche/mitglieder/2/22117/logo_hoermann.png', 130, 10, 0, 0, 'PNG');
        $pdf->SetX(120);
        $pdf->SetY(35);
        $pdf->SetTextColor(14,78,120);
        $pdf->Cell(50, 10, iconv('UTF-8', 'windows-1252','HÖRMANN GmbH & Co. KG - Otto-Lilienthal-Str. 30 - 88046 Friedrichshafen'), 0, 0, 'L');
        $pdf->Text(130, 35, iconv('UTF-8', 'windows-1252', 'Telefon 07541 / 95990 - 0 - Fax 07541 / 95990 - 80'));
        $pdf->Text(130, 39, iconv('UTF-8', 'windows-1252', 'info@hoermann-fn.de - www.hoermann-fn.de'));
        $pdf->SetY(65);
        $pdf->Cell(190, 210, iconv('UTF-8', 'windows-1252',''), 'LTR', 0, 'L');
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Text(15, 72, iconv('UTF-8', 'windows-1252', 'Name: ' .$obj->Name));
        $pdf->Text(15, 73, iconv('UTF-8', 'windows-1252', '______________________________'));
        $pdf->Text(15, 83, iconv('UTF-8', 'windows-1252', 'Werk: 2'));
        $pdf->Text(15, 84, iconv('UTF-8', 'windows-1252', '_______'));
        $pdf->Text(53, 83, iconv('UTF-8', 'windows-1252', 'Halle: 0'));
        $pdf->Text(53, 84, iconv('UTF-8', 'windows-1252', '_______'));
        $pdf->Text(15, 93, iconv('UTF-8', 'windows-1252', 'Masch-Nr.: '));
        $pdf->Text(15, 94, iconv('UTF-8', 'windows-1252', '___________'));
        $pdf->Text(125, 93, iconv('UTF-8', 'windows-1252', 'Auftrags. Nr.: ' .$obj->ProjNr));
        $pdf->Text(125, 94, iconv('UTF-8', 'windows-1252', '____________________'));
        $pdf->Text(15, 105, iconv('UTF-8', 'windows-1252', 'Auftraggeber: ' .$obj->Auftraggeber));
        $pdf->Text(15, 106, iconv('UTF-8', 'windows-1252', '__________________________'));
        $pdf->Text(125, 105, iconv('UTF-8', 'windows-1252', 'Datum: ' .$obj->Termin));
        $pdf->Text(125, 106, iconv('UTF-8', 'windows-1252', '________________'));
        $pdf->Text(10, 110, iconv('UTF-8', 'windows-1252', '________________________________________________________________________________________'));
        $pdf->Text(125, 72, iconv('UTF-8', 'windows-1252', 'SAP Nr.: ' .$obj->SAPNr));
        $pdf->Text(125, 73, iconv('UTF-8', 'windows-1252', '__________________'));
        $pdf->Text(125, 83, iconv('UTF-8', 'windows-1252', 'Bestell Nr.: ' .$obj->BestellNr));
        $pdf->Text(125, 83, iconv('UTF-8', 'windows-1252', '__________________________'));
        $pdf->SetXY(10, 111);
        $pdf->SetWidths(array(190));
        $pdf->Row(array(iconv('UTF-8', 'windows-1252','Auszuführende Arbeiten: ' . $obj->Betreff)));
        $pdf->Ln();
    
    
            $pdf->Image('StempelNot.png', 62, 87, 0, 0, 'PNG');
        
    if($obj->AuftrErledigt === 1 || $obj->AuftrErledigt === -1){
        $pdf->Text(70, 93, iconv('UTF-8', 'windows-1252', 'Arbeit abgeschlossen'));
    }else {
        $pdf->Text(70, 93, iconv('UTF-8', 'windows-1252', 'Arbeit nicht abgeschlossen'));
    }
     
    
        
        $pdf->SetWidths(array(25, 43, 59, 21, 21, 21));
        $pdf->Row(array('Datum', 'Name', iconv('UTF-8', 'windows-1252','Ausgeführte Arbeiten'), 'Normalstd.', iconv('UTF-8', 'windows-1252','Überstd.'), iconv('UTF-8', 'windows-1252','%Üstd.')));

        $sqll = "SELECT FORMAT([MontJournMonteureStundenID].Datum, 'dd.MM.yyyy') as Datum, [MontJournMonteureStundenID].PersName,
        [MontJournMonteureStundenID].Stunden, case when [MontJournMonteureStundenID].Stunden > 8 Then Round([MontJournMonteureStundenID].Stunden - 8, 2) else 0.00 end as Ueberstunden
        FROM [BNWINS].[dbo].[MontJournMonteureStundenID], MontJourn where MontJourn.MontJournID = [MontJournMonteureStundenID].fkMontjournID and MontJourn.MontJournID = " .$obj->MontJournID;
    
        $stmtt = sqlsrv_query( $conn, $sqll);
        if( $stmtt === false ) {
         die( print_r( sqlsrv_errors(), true));
        }
    
        while($objj = sqlsrv_fetch_object( $stmtt)) {

            $pdf->Row(array($objj->Datum, iconv('UTF-8', 'windows-1252', $objj->PersName), iconv('UTF-8', 'windows-1252', $obj->AuftrAusgefArb), $objj->Stunden .'h', iconv('UTF-8', 'windows-1252', $objj->Ueberstunden .'h'), iconv('UTF-8', 'windows-1252','')));

    
    }

    $pdf->Ln();

        

    
       
      
        $pdf->SetWidths(array(68, 122));
        $pdf->Row(array('Menge', 'Material'));
        $sqlll = "SELECT MontJournPos.PosMenge, MontJournPos.PosText1
        FROM [BNWINS].[dbo].[MontJournPos], MontJourn where MontJournPos.PosMenge != 0 and MontJourn.MontJournID = [MontJournPos].fkMontjournID and MontJourn.MontJournID = " .$obj->MontJournID;
    
        $stmttt = sqlsrv_query( $conn, $sqlll);
        if( $stmttt === false ) {
         die( print_r( sqlsrv_errors(), true));
        }
    
        while($objjj = sqlsrv_fetch_object( $stmttt)) {

            $pdf->Row(array($objjj->PosMenge, iconv('UTF-8', 'windows-1252',$objjj->PosText1)));
    
    }
   
    
       

    }



 
   
 

    $pdf->Output();




  


?>
