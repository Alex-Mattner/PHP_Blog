<?php 
/********************************************************************************************/

			/**
			*
			* Wandelt ein ISO Datums-/Uhrzeitformat in ein europäisches Datums-/Uhrzeitformat um
			* und separiert Datum von Uhrzeit
			*
			* @param String Das ISO Datum/Uhrzeit
			*
			* @return Array Das deutsche Datum plus die Uhrzeit
			*
			*/
			
		
			function isoToEuDateTime($dateTime) {
if(DEBUG_F)		echo "<p class='debug'>Aufruf isoToEuDateTime($dateTime)</p>";				
				
				// mögliche Übernahmewerte
				// 2017-06-21 09:17:48
				// 2017-06-21 
				
				// gewünschte Ausgabewerte
				// 21.06.2017    // 09:26
				// 21.06.2017
				
				// TODO: Aus diesem ISO-Datum String das Datum auschneiden und umformatieren
				$year = substr($dateTime, 0, 4);
				$month = substr($dateTime, 5, 2);
				$day = substr($dateTime, 8, 2);
				
				$euDate = "$day.$month.$year";
				
				// TODO: ggf. Uhrzeit ausschneiden und Sekunden abschneiden
					//Prüfen ob $dateTime eine Uhrzeit enthält
					if( strlen($dateTime) > 10 ) {
						$time = substr($dateTime, 11, 5);
					} else {
						$time = NULL;
						// alternativ auch oben im Programm gleich NULL setzen
					}
				
				// TODO: Datum und Uhrzeit zurückgeben
				return array("date"=>$euDate, "time"=>$time);
			}

			
/********************************************************************************************/

?>