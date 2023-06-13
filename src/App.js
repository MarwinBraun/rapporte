
import './App.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle';
import React, { useState, useEffect } from 'react';
import axios from 'axios';

function App() {

  const [datum, setdatum] = useState('');
  const [datumbis, setdatumbis] = useState('');
  const [adrkuerzel, setadrkuerzel] = useState('');
  const [daten, setdaten] = useState([]);
  const [auswahl, setauswahl] = useState([]);


  useEffect(() => {
       
    const date = new Date();

    let currentDay= String(date.getDate()).padStart(2, '0');
    
    let currentMonth = String(date.getMonth()+1).padStart(2,"0");
    
    let currentYear = date.getFullYear();
    
    // we will display the date as DD-MM-YYYY 
    
    let currentDate = `${currentYear}-${currentMonth}-${currentDay}`;
    setdatum(currentDate);
    setdatumbis(currentDate);
      
     

  }, []);


  const handleDateChange = (event) => {
    setdatum(event.target.value);
  };

  const handleDateChangeBis = (event) => {
    setdatumbis(event.target.value);
  };



  const data = (event) => {
    setadrkuerzel(event.target.value);
  };


  const leiten = () => {
    if(auswahl.length === 0){

      window.open('http://localhost/tablenachbau.php?kuerzel='+adrkuerzel+'&zeitraumvon='+datum+'&zeitraumbis='+datumbis, '_blank');

    }else{

      const str = auswahl.join(',');
      
      
     
      window.open('http://localhost/tablenachbau2.php?kuerzel='+adrkuerzel+'&zeitraumvon='+datum+'&zeitraumbis='+datumbis+'&str='+str, '_blank');

    }
  };


  const anzeige = async () => {
   
   await axios.get('http://localhost/load.php', {
      params: {
        adrkuerzel: adrkuerzel,
        datum: datum,
        datumbis: datumbis,
      }
    })
    .then(function (response) {
      setdaten(response.data.daten);
    })


  };

  const einsetzen = (wert) => {

    const index = auswahl.indexOf(wert);
    if (index > -1) {
      // ID existiert bereits in der Auswahl, also entfernen
      setauswahl(auswahl.filter((item) => item !== wert));
    } else {
      // ID zur Auswahl hinzufügen
      setauswahl([...auswahl, wert]);
    }


    

    console.log(auswahl);


  };


  const handleCheckAll = () => {
    const allIds = daten.map((objekt) => objekt.MontJournID);
    setauswahl(allIds);
  };

  const handleUncheckAll = () => {
    setauswahl([]);
  };





  return (
    <div className="container-fluid">
   <div className="container">
   <br></br>
  <div className='row'>
    <div className='col-5'>
      <img src='https://www.hoermann-fn.de/fileadmin/platzhirsche/mitglieder/2/22117/logo_hoermann.png' />
    </div>
  </div>
<br></br>
  <div className='row'>
    <div className='col-6'>
    <label class="form-label">Zeitraum Von</label>
    <input type="date"  class="form-control" id="staticEmail" value={datum} onChange={handleDateChange}/>
    <br></br>
    <label class="form-label">Zeitraum Bis</label>
    <input type="date"  class="form-control"  value={datumbis} onChange={handleDateChangeBis}/>

    </div>
   

    <div className='col-6'>
    <br></br>
    <input type="text"  class="form-control" placeholder='Adresskürzel eintragen...' onChange={data} />
    </div>
  </div>

  <br></br><br></br>
  <div className='row text-center'>
    <div className='col-12'>
    <button onClick={leiten} type="button" class="btn btn-primary">Rapporte generieren</button>
    <br></br><br></br>
    <button onClick={anzeige} type="button" class="btn btn-primary">Rapporte anzeigen</button>
    <br></br><br></br>
    <button onClick={handleCheckAll} type="button" class="btn btn-primary">Alle Ergebnisse anhaken</button>
    <br></br><br></br>
    <button onClick={handleUncheckAll} type="button" class="btn btn-primary">Alle Ergebnisse abhaken</button>
    </div>
  </div>  <br></br><br></br>

  <div className='row'>

    <div className='col-12 table-responsive'>

    <table class="table align-middle table-striped">
    <thead>
      <tr>
      <td>Auswählen</td>
      <td>ID</td>
      <td>Projektnummer</td>
      <td>Arbeit Erledigt?</td>
      <td>Bestellnummer</td>
      <td>SAP-Nummer</td>
      <td>Datum</td>
      <td>Auftraggeber</td>
      <td>Name</td>
      <td>Auszuführende Arbeiten</td>
      <td>Ausgeführte Arbeiten</td>
      </tr>
    </thead>
    <tbody>
    {daten.map((value)=>
    <tr key={value.MontJournID}>
   <td><input type='checkbox' onChange={() => einsetzen(value.MontJournID)}  checked={auswahl.includes(value.MontJournID)} /></td>
  <td >{value.MontJournID}</td>
  <td >{value.ProjNr}</td>
  {value.AuftrErledigt === 1 || value.AuftrErledigt === -1 ? <td >Arbeit nicht abgeschlossen</td> : <td >Arbeit abgeschlossen</td>} 
  <td >{value.BestellNr}</td>
  <td >{value.SAPNr}</td>
  <td >{value.Termin}</td>
  <td >{value.Auftraggeber}</td>
  <td >{value.Name}</td>
  <td >{value.Betreff}</td>
  <td >{value.AuftrAusgefArb}</td>
    </tr>
           
            )}
    </tbody>
    </table>

    </div>

  </div>

    </div>
    </div>
  );
}

export default App;
