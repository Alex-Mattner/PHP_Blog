<?php 
/**************************************************************************************************************/

					/**
					*
					* Stellt eine Verbindung zu einer Datenbank mittels PDO her
					*
					* @param [String $dbname]	Name der zu verbindenden Datenbank
					*
					* @return Object 			DB-Verbindungsobjekt
					*
					*/



					function dbConnect($dbname=DB_NAME) {
if(DEBUG) 				echo "<p class='debugDb'>Versuche mit der Datenbank <b>$dbname</b> zu verbinden...</p>";						
						
						
						// charset=utf8 ist optional, braucht man nur wenn man per Hand daten in mySQL eingeben hat
						// bei automatischen Datenbankporzessen braucht man das utf8 nicht extra nochmal "erzwingen"
						// in stelle 2 und 3 des Objektes kommmt der Username der Datenbank und in den 3. String das PW
						
						// in Objektorientierung gibt es das sog. Exception Handling 
						// EXCEPTION HANDLING (Umgang/Auffangen von Fehlern)
						// try - catch , try -- Versuche eine Datenbankverbindung aufzubauen
						
						try {
							// wirf, falls fehlgeschlagen, eine Fehlermeldung "in den leeren Raum"
							//$pdo = new PDO("mysql:host=localhost; dbname=market; charset=utf8", "root", ""); 
							// unviersel nutzbar lautet obige Anweisung wie folgt: 
							$pdo = new PDO(DB_SYSTEM . ":host=" . DB_HOST . "; dbname=$dbname; charset=utf8", DB_USER, DB_PWD);
							
						// falls eine Fehlermeldung geworfen wurde, wird sie hier aufgefangen
						} catch( PDOException $error ) {
							// Ausgabe der Fehlermeldung 
if(DEBUG)					echo "<p class='error'><i>FEHLER: </i>" . $error->GetMessage() . "</p>";
							// Skript abbrechen
							exit;
						}
						// Falls das Skript nicht abgebrochen wurde (also kein Fehler vorhanden), geht es hier weiter
if(DEBUG)					echo "<p class='debugDb'>Erfolgreich mit Datenbank <b>$dbname</b> verbunden.</p>";
						
						// DB-Verbindungsobjekt zurückgeben
						return $pdo;
						
					}




/**************************************************************************************************************/
?>