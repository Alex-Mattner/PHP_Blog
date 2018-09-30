<?php 

/****************************************************************************************************************/
				
				   /*****************************************************************************/
                /******************************** BEREINIGE EINEN STRING **************************/  
                  /*****************************************************************************/
				
				/**
				*
				* Entschärft und säubert einen String
				*
				* @param String $inputString - Der zu entschärfende und zu bereinigende String 
				*
				* @return String - Der entschärfte und bereinigte String 
				*
				*/

				
			function cleanString( $inputString ) {
if(DEBUG_F) 	echo "<p class='debugCleanString'>Aufruf cleanString($inputString)</p>";					  
				
				// trim() entfernt am Anfang und am Ende eines Strings alle 
				// sog. Whitespaces (Leerzeichen, Tabulatoren, Zeilenumbrüche)
				$inputString = trim($inputString);
				
				// htmlspecialchars() entschärft HTML-Steuerzeichen wie < > & '' ""
				// und ersetzt sie durch &lt;, &gt;, &amp;, &apos; &quot;
				$inputString = htmlspecialchars($inputString);
				
				
				// bereinigten und entschärften String zurück geben
				return $inputString;
				
			}

				  

/****************************************************************************************************************/

				/**
				*
				*	Prüft einen String auf Leerstring, Mindest- und Maximallänge
				*
				*	@param String $inputString - Der zu prüfende String
				*	@param [Integer $minLength] - Die erforderliche Mindestlänge des zu prüfenden Strings
				*	@param [Integer $maxLength] - Die erlaubte Maximallänge des zu prüfenden Strings
				*
				*	@return String/NULL - Ein String bei Fehler, ansonsten NULL 
				*
				*/

				function checkInputString( $inputString, $minLength=MIN_INPUT_LENGTH, $maxLength=MAX_INPUT_LENGTH ) {
if(DEBUG_F) 		echo "<p class='debugCheckInputString'>Aufruf checkInputString($inputString  [min: $minLength, max: $maxLength])</p>";					  
					
					// Für den Fall dass wir keinen Fehler haben müssen wir $errorMessage initialisieren
					$errorMessage = NULL;
					
					// Prüfen auf Leerstring  -- prüfen bedeuted immer if-Abfrage
					// Abfrage if( $inputString == "" ) prüft nur auf Inhalt (nicht Inhalt + Datentyp) 
					//	false --> also auch true bei Null, 0, Leerstring
				
					if( $inputString === "" ) {
						$errorMessage = "Dies ist ein Pflichtfeld";
					
					// Prüfen auf Mindestlänge
					} elseif( mb_strlen($inputString) < $minLength ) { 
						$errorMessage = "Muss mindestens $minLength Zeichen lang sein";
					
					// Prüfen auf Maximallänge
					} elseif( mb_strlen($inputString) > $maxLength ) {
						$errorMessage = "Darf maximal $maxLength Zeichen lang sein";
					
					}	
					
					return $errorMessage;
					
				}
					
					
/****************************************************************************************************************/

                  /*****************************************************************************/
                /******************************** Email Validitätsprüfung **************************/  
                  /*****************************************************************************/

				  /**
				  *
				  *		Prüft eine Emal-Adresse auf Leerstring und Validität
				  *
				  *		@param String $inputString - Die zu prüfende Emal-Adresse
				  *
				  *		@return String/NULL - Ein String bei Fehler, ansonsten NULL
				  *
				  */
				  
				  
				function checkEmail ( $inputString ) {
if(DEBUG_F) 		echo "<p class='debugCheckEmail'>Aufruf checkEmail($inputString)</p>";	
					
					$errorMessage = NULL;
					
					// Umlaute umwandeln in normale Zeichen
					$inputString = str_replace( array('ä','Ä','ö','Ö','ü','Ü','ß'), "a", $inputString);
					
					// oder so....
					//$inputString = str_replace("ü", "u", $inputString);
					//$inputString = str_replace("ö", "o", $inputString);
					//$inputString = str_replace("ß", "ss", $inputString);
					//$inputString = str_replace("Ä", "a", $inputString);
					//$inputString = str_replace("Ä", "a", $inputString);
					//$inputString = str_replace("Ä", "a", $inputString);
					//$inputString = str_replace("Ä", "a", $inputString);
					
					// Prüfen auf Leerstring 
					if( $inputString === "" ) {
						$errorMessage = "Dies ist ein Pflichtfeld";
					
					// Prüfen auf Mindestlänge
					} elseif ( !filter_var($inputString, FILTER_VALIDATE_EMAIL) ) {
						$errorMessage = "Dies ist keine gültige Email Adresse!";
					}
					
					return $errorMessage;
				  
				}

/****************************************************************************************************************/
				
		  /**
		  *
		  *		Speichert und prüft ein hochgeladenes Bild auf MIME-Type, Datei- und Bildgröße
		  *
		  *		@param Array $uploadedImage 		- Das hochzuladende Bild aus $_FILES
		  *		@param [Int $maxWidth] 				- Die maximal erlaubte Bildbreite in Px
		  *		@param [Int $maxHeight] 			- Die maximal erlaubte Bildhöhe in Px
		  *		@param [Int $maxSize] 				- Die maximal erlaubte Dateigröße in Bytes
		  *		@param [String $uploadPath] 		- Das Speicher-Verzeichnis auf dem Server
		  *		@param [Array $allowedMimeTypes] - Whitelist der erlaubten MIME-Types
		  *
		  *		@return Array (String/NULL - Fehlermeldung im Fehlerfall, String - Der Speicherpfad auf dem Server)
		  *
		  **/
				
			function imageUpload($uploadedImage,
										$maxWidth=	IMAGE_MAX_WIDTH,
										$maxHeight=	IMAGE_MAX_HEIGHT,
										$maxSize=	IMAGE_MAX_SIZE,
										$uploadPath=IMAGE_UPLOAD_PATH,
										$allowedMimeTypes=IMAGE_ALLOWED_MIMETYPES
										) {
if(DEBUG_F) 		echo "<p class='debugImageUpload'>Aufruf imageUpload()</p>";				
					
					/*
					Das Array $_FILES['avatar'] bzw. $uploadedImage enthält:
					Den Dateinamen [name]
					Den generierten (also ungeprüften) MIME-Type [type]
					Den temporären Pfad auf dem Server [tmp_name]
					Die Dateigröße in Bytes [size]
					*/
					
if(DEBUG) 		echo "<pre>";
if(DEBUG) 		print_r($uploadedImage);
if(DEBUG) 		echo "</pre>";				
				
				
					/***************** BILDINFORMATIONEN SAMMELN ******************/
					
					$fileName = $uploadedImage['name'];
				
					//ggfs. Leerzeichen in Dateinammen durch "_" ersetzen
					$fileName = str_replace(" ", "_", $fileName);
					
					// obiges Prinzip standardmäßig z.B. bei Preiseingaben in Formularen anwenden, --> , in . umwandeln etc...
					
					//Dateigröße
					$fileSize = $uploadedImage['size'];
					
					//Temporären Pfad auf dem Server auslesen
					$fileTemp = $uploadedImage['tmp_name'];
					
					// Pfad zum endgültigen Speicherort vorbereiten
					// Zufälligen Dateinamen generieren in form eines Prefix --> damit keine Dateinamen doppelt auf dem Server
					// plus zufällig generierte Zeichenfolge
					$randomPrefix = rand(1, 999999) . str_shuffle("abcdefghijklmnopqrstuvwxyz") . rand(1, 999999);
					$fileTarget = $uploadPath . $randomPrefix . "_" . $fileName;
					
										
if(DEBUG_F) 	echo "<p class='debugImageUpload'>\$fileName: $fileName</p>";					
if(DEBUG_F) 	echo "<p class='debugImageUpload'>\$fileSize: " . round($fileSize/1024, 2) . " kB</p>";					
if(DEBUG_F) 	echo "<p class='debugImageUpload'>\$fileTemp: $fileTemp</p>";					
if(DEBUG_F) 	echo "<p class='debugImageUpload'>\$fileTarget: $fileTarget</p>";					
				
					// Genauere Informationen zum BILD
					$imageData = getimagesize($fileTemp);

					/*
					Die Funktion getimagesize() liefert bei gültigen Bildern ein Array zurück:
					Die Bildbreite in PX [0]
					Die Bildhöhe in PX [1]
					Einen für die HTML-Ausgabe vorbereiteten String für das IMG-Tag
					(width="480" height="532") [3]
					Die Anzahl der Bits pro Kanal ['bits']
					Die Anzahl der Farbkanäle (somit auch das Farbmodell: RGB=3, CMYK=4) ['channels']
					Den echten(!) MIME-Type ['mime']
					*/
					
if(DEBUG) 		echo "<pre>";
if(DEBUG) 		print_r($imageData);
if(DEBUG) 		echo "</pre>";					
				
					//relevanten Infos aus dem Array rausziehen
					$imageWidth = $imageData[0];
					$imageHeight = $imageData[1];					
					$imageMimeType = $imageData['mime'];
if(DEBUG_F) 	echo "<p class='debugImageUpload'>\$imageWidth: $imageWidth</p>";									
if(DEBUG_F) 	echo "<p class='debugImageUpload'>\$imageHeight: $imageHeight</p>";									
if(DEBUG_F) 	echo "<p class='debugImageUpload'>\$imageMimeType: $imageMimeType</p>";									
					
					
					/*********** BILD PRÜFEN ************/
					
					// MIME-Type prüfen
					// 4 Mime-types die ich zulassen möchte //WHITELIST anlegen
					// ist jetzt oben als optionaler Parameter $allowedMimeTypes = array("image/jpeg", "image/jpg", "image/gif", "image/png");
					
					if( !in_array($imageMimeType, $allowedMimeTypes) ) {
						$errorMessage = "Dies ist kein gültiger Bildtyp!";
						
					// Maximal erlaubte Bildhöhe, wenn Image Height größer als 800px
					} elseif( $imageHeight > $maxHeight ) {
						$errorMessage = "Die Bildhöhe darf maximal 800px betragen!";
						
					// Maximal erlaubte Bildbreite
					} elseif( $imageWidth > $maxWidth ) {
						$errorMessage = "Die Bildbreite darf maximal 800px betragen!";
						
					// Maximal erlaubte Dateigröße	
					} elseif( $fileSize > $maxSize ) {
						$errorMessage = "Die Bildgröße darf maximal " . round($maxSize/1024, 2) . " kB betragen!";
						
					// wenn es keinen Fehler gab, nullen wir die $errorMessage
					} else {
						$errorMessage = NULL;
					}
								
					
								
					/*********** BILD SPEICHERN *****************/				
					if( !$errorMessage ) {
if(DEBUG_F) 		echo "<p class='debugImageUpload'>Bildprüfung ergab keine Fehler</p>";				

						// Bild an seinen endgültigen Speicherort verschieben
						// -> liefert zurück true oder false
						if( move_uploaded_file($fileTemp, $fileTarget) ) {
if(DEBUG_F) 			echo "<p class='debugImageUpload'>Bild wurde erfolgreich unter $fileTarget gespeichert.</p>";								
						} else {
							$errorMessage = "FEHLER beim Speichern der Datei!";
						}
					}
					
					/********** FEHLERMELDUNG UND BILDPFAD RÜCKGEBEN (um ihn dann in db zu speichern) ***********/
					
					// Um mehrere Werte zurückgeben zu können, muss return ein Array zurückgeben
					return array( 	"imageError" 	=> $errorMessage,
										"imagePath"		=> $fileTarget    );
			}	
				
				
				
				
/****************************************************************************************************************/
				
				
				
				
				
				
				
				
				
				
				
				
				
?>