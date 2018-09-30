<?php
/********************************************************************************************/

				
			/***********************************/
			/********** KONFIGURATION **********/
			/***********************************/
			
			// Externe Dateien einbinden:
			require_once("include/config.inc.php");
			require_once("include/form.inc.php");
			require_once("include/db.inc.php");
			
			$pdo = dbConnect();
			
/********************************************************************************************/


			/**********************************************/
			/********** VARIABLEN INITIALISIEREN **********/
			/**********************************************/
			
			$blogImagePath 		= NULL;
			
			$errorHeading 			= NULL;
			$errorInputTextarea 	= NULL;
			$errorCategory			= NULL;
			$errorImageUpload 	= NULL;

			//$newCategory 			= NULL;
			$errorNewCategory 	= NULL;
			
			$updateSuccess			= NULL;
			$dbMessageNewCat		= NULL;
			$dbMessageNewBlog		= NULL;
			

/********************************************************************************************/

				
			/************************************/
			/********** ZUGRIFFSSCHUTZ **********/
			/************************************/
			
			// Session fortführen
			session_start();
			
			/*
			echo "<pre>";
			print_r($_SESSION);
			echo "</pre>";
			*/
			
			if( !isset($_SESSION['usr_id']) ) {
				session_destroy();
				header("Location: index.php");
				exit;
			}
			
			
/********************************************************************************************/


			/***********************************************/
			/********** URL-PARAMETERVERARBEITUNG **********/
			/***********************************************/

			
			
			// Schritt 1 URL: Prüfen, ob Parameter übergeben wurde
			if( isset($_GET['action']) ) {
if (DEBUG)	echo "<p class='debug'>URL-PARAMETER action wurde übergeben</p>"; 	


				// Schritt 2 URL: Werte auslesen, entschärfen, DEBUG-Ausgabe
				$action = cleanString($_GET['action']);
if(DEBUG)		echo "<p class='debug'>\$action: $action</p>";


				// Schritt 3 URL: Auf Wert des URL-PARAMETER eigehen
				if( $action == "logout" ) {
if (DEBUG)		echo "<p class='debug'>LOGOUT wird durchgeführt</p>"; 	

				/*
				echo "<pre>";
				print_r($_GET);
				echo "</pre>";
				*/
				
				
				// Schritt 4 URL: Daten weiterverarbeiten
				
					// TODO: ggfs. Timestamp LOGOUT In DB speichern --> dann aber auch TIMESTAMP LOGIN (in index.php) in DB speichern
					
					
					// TODO:  Session löschen
					/********** SESSION LÖSCHEN **********/
					session_destroy();
					
					
					// TODO: Weiterleiten auf index.php
					/********** UMLEITEN AUF index.php **********/
					header("Location: index.php");
					exit;
					
				} // ENDE LOGOUT
		
			} // ENDE URL-PARAMETERVERARBEITUNG

			
/********************************************************************************************/


			/*****************************************************************/
			/********** FORMULARVERARBEITUNG FÜR NEUER BLOG-EINTRAG **********/
			/*****************************************************************/

			// Schritt 1 FORM: Prüfen ob Formular abgeschickt wurde
			if( isset($_POST['blogEntry']) ){
if(DEBUG)	echo "<p class='debug'>Formular wurde abgeschickt</p>";

				echo "<pre>";
				print_r($_POST);
				echo "</pre>";
				
				// Schritt 2 FORM: Werte auslesen, entschärfen, DEBUG-Ausgabe
				$category 			= 	cleanString($_POST['category']);
				$heading			 	= 	cleanString($_POST['heading']);
				$inputTextarea 	= 	cleanString($_POST['inputTextarea']);
				$position 			= 	cleanString($_POST['position']);

if(DEBUG)	echo "<p class='debug'>\$category: $category</p>";	
if(DEBUG)	echo "<p class='debug'>\$heading: $heading</p>";	
if(DEBUG)	echo "<p class='debug'>\$inputTextarea: $inputTextarea</p>";				
if(DEBUG)	echo "<p class='debug'>\$position: $position</p>";				
				
				// Schritt 3 FORM: ggfs. Daten validieren
				// $errorCategory			= checkInputString($category);
				$errorHeading 			= checkInputString($heading, 2, 120);
				$errorInputTextarea 	= checkInputString($inputTextarea, 2);
				
				
				if( !$errorHeading AND !$errorInputTextarea) {
if(DEBUG)		echo "<p class='debug'>Formular ist fehlerfrei und wird verarbeitet ... </p>";

					// Ergolgsfall ... nur wenn Pflichtfelder korret ... geht es weiter...
					
					
					/******************* FILEUPLOAD ********************/ 
if(DEBUG)		echo "<pre>";
if(DEBUG)		print_r($_FILES);
if(DEBUG)		echo "</pre>";
					
					
					
					// Prüfen, ob File hochgeladen wurde
					if( $_FILES['inputFile']['tmp_name'] != "" ) {
if(DEBUG)			echo "<p class='debug'>Bildupload ist aktiv...</p>";						
						
						// Mit Funktion imageUpload() prüfen
						$imageReturnArray = imageUpload($_FILES['inputFile']);
						
if(DEBUG)			echo "<pre>";
if(DEBUG)			print_r($imageReturnArray);
if(DEBUG)			echo "</pre>";							
	
	
						// Prüfen ob Bildupload Fehler 
						if( $imageReturnArray['imageError'] ) {
							// Fehlerfall
if(DEBUG)				echo "<p class='debug'>FEHLER: $imageReturnArray[imageError] </p>";
							// TODO: Usermeldung
							$errorImageUpload = $imageReturnArray['imageError'];
						} else {
							// Erfolgsfall
if(DEBUG)				echo "<p class='debug'>Bild wurde erfolgreich auf den Server geladen.</p>";
							// TODO: Bildpfad auslesen und in Variable speichern
							$blogImagePath = $imageReturnArray['imagePath'];
							
						} // ENDE Erfolgsfall Bildupload Prüfung
						
					} // ENDE BILDUPLOAD 
					
					if( !$errorImageUpload ) {
					
						// Schritt 4 FORM: Daten weiterverarbeiten
						
						/************* DATENBANKOPERATIONEN *************/
						
						// Schritt 1 DB: Connect DB
						// erledigt -- siehe oben
						
						// Schritt 2 DB: SQL-Statement vorbereiten
						$statement = $pdo->prepare("
															INSERT INTO blogs
															(blog_headline, blog_image, blog_imageAlignment, blog_content, cat_id, usr_id)
															VALUES 
															(:ph_blog_headline, :ph_blog_image, :ph_blog_imageAlignment, :ph_blog_content, :ph_cat_id, :ph_usr_id)
															");
					
						// Schritt 3 DB: SQL-Statement ausführen, ggfs. Platzhalter füllen
						$statement->execute( array( "ph_blog_headline" 			=> $heading,
															 "ph_blog_image" 				=> $blogImagePath,
															 "ph_blog_imageAlignment"  => $position,
															 "ph_blog_content"  			=> $inputTextarea,
															 "ph_cat_id"  					=> $category,
															 "ph_usr_id"					=> $_SESSION['usr_id']
													) ) OR DIE( $statement->errorInfo()[2] );
						
						// Schritt 4 DB: Daten weiterverarbeiten - prüfen ob Schreibvorgang erfolgreich
						$updateSuccess = $statement->rowCount();
if(DEBUG)			echo "<p class='debug'>\$updateSuccess: $updateSuccess</p>";	
			
						if( !$updateSuccess ) {
							// Fehlerfall
							$dbMessageNewBlog = "<p class='success'>Neuer Blog-Eintrag konnte leider nicht gespeichert werden</p>";
							
						} else {
							// Erfolgsfall
if(DEBUG)				echo "<p class='debug'>Es wurden $updateSuccess Datensätze geändert</p>";
							
							// User-Ausgabe
							$dbMessageNewBlog = "<p class='success'>Neuer Blog-Eintrag wurde erfolgreich gespeichert</p>";
						
						} // ENDE Erfolgsfall Kategorie in DB schreiben
					}
					
				} // ENDE DATEN VALIDIEREN SCHRITT 3 FORM
				
 			} // ENDE FORMULARVERARBEITUNG BLOG-EINTRAG

/********************************************************************************************/


			/*****************************************************************/
			/******* FORMULARVERARBEITUNG FÜR NEUE KATEGORIE ANLEGEN *********/
			/*****************************************************************/

			// Schritt 1 FORM: Prüfen ob Formular abgeschickt wurde
			if( isset($_POST['newCategory']) ) {
if(DEBUG)	echo "<p class='debug'>Formular newCategory wurde abgeschickt</p>";

				echo "<pre>";
				print_r($_POST);
				echo "</pre>";
				
				// Schritt 2 FORM: Werte auslesen, entschärfen, DEBUG-Ausgabe
				$newCategory 			= 	cleanString($_POST['inputText']);				
if(DEBUG)		echo "<p class='debug'>\$newCategory: $newCategory</p>";	
				
				// Schritt 3 FORM: ggfs. Daten validieren
				$errorNewCategory 			= checkInputString($newCategory, 2, 70);
				
				// Schritt 4 FORM: Daten weiterverarbeiten
					
					/************* DATENBANKOPERATIONEN *************/
					
						// Schritt 1 DB: Connect DB
						//$pdo = dbConnect();  
					
						// Schritt 2 DB: SQL-Statement vorbereiten
						$statement = $pdo->prepare("INSERT INTO categories
															(cat_name)
															VALUES
															(:ph_cat_name)"
															);
						
						// Schritt 3 DB: SQL-Statement ausführen, ggfs. Platzhalter füllen
						$statement->execute( array( "ph_cat_name" 		=> $newCategory													
													) ) OR DIE( $statement->errorInfo()[2] );
						
						// Schritt 4 DB: Daten weiterverarbeiten - prüfen ob Schreibvorgang erfolgreich
						$updateSuccess = $statement->rowCount();
if(DEBUG)				echo "<p class='debug'>\$updateSuccess: $updateSuccess</p>";	
				
							if( !$updateSuccess ) {
								// Fehlerfall
								$dbMessageNewCat = "<p class='success'>Neue Kategorie konnte leider nicht gespeichert werden</p>";
								
							} else {
								// Erfolgsfall
if(DEBUG)					echo "<p class='debug'>Es wurden $updateSuccess Datensätze geändert</p>";
								
								// User-Ausgabe
								$dbMessageNewCat = "<p class='success'>Neue Kategorie wurde erfolgreich gespeichert</p>";
							
							} // ENDE Erfolgsfall Kategorie in DB schreiben
				
			} // ENDE FORMULARVERARBEITUNG NEUE KATEGORIE

			
/********************************************************************************************/


		/*********************************************************************/
		/************* DATEN NEUE KATEGORIE AUS DB AUSLESEN ******************/
		/*********************************************************************/
		
		// Schritt 2 DB: SQL-Statement vorbereiten
		$statement = $pdo->prepare(" SELECT * FROM categories");
		
		// Schritt 3 DB: SQL-Statement ausführen
		$statement->execute() OR DIE( $statement->errorInfo()[2] );
		
		/*echo "<pre>";
		print_r($statement);
		echo "</pre>"; */
		
		
		// Schritt 4 DB: Daten weiterverarbeiten
		while( $row = $statement->fetch() ) {
			$categoriesArray[$row['cat_id']] = $row['cat_name'];
			
			/*echo "<pre>";
			print_r($row);
			echo "</pre>";
			
			echo "<pre>";
			print_r($categoriesArray);
			echo "</pre>";*/
			
		} 
		
		
/********************************************************************************************/

?>


<!doctype html>

<html>

	<head>
		<meta charset="utf-8">
		<title>Dashboard</title>
		<link rel="stylesheet" href="css/main.css">
	</head>

	<body>
		<h1>Dashboard</h1>
	
		<br>
		
		<p><a href="?action=logout">Logout</a></p>
		<p><a href="index.php">Zur Indexseite</a></p>

		<br>
		
		<?php echo $dbMessageNewBlog ?>
		<form action="" method="POST" enctype="multipart/form-data">
			<input type="hidden" name="blogEntry">
			
			<fieldset>
				<legend>Neuer Blog-Eintrag</legend>
					<br>
					<select name="category" value="selectCategory">
						<?php 
							foreach( $categoriesArray AS $key=>$value) {
								echo "<option value='$key'>$value</option>";							}
						?>
					</select>	
						
					<span class="marker">*</span>
					<br>
					<br>
					<span class="error"><?php echo $errorHeading ?></span><br>
					<input type="text" name="heading" placeholder="Überschrift">
					<span class="marker">*</style></span>
					<br>
					<br>
					<span class="error"><?php echo $errorImageUpload ?></span>
					<input type="file" name="inputFile" value="fileUp">
					<br>
					<br>
					<select name="position" value="picturePosition">
						<option value="fleft">Bild links vom Text</option>
						<option value="fright">Bild rechts vom Text</option>
					</select>
					<br>
					<br>
					<span class="error"><?php echo $errorInputTextarea ?></span><br>
					<textarea name="inputTextarea" placeholder="Bitte geben Sie hier ihren Text ein..."></textarea>
					<span class="marker">*</span>
					<br>
					<input type="submit" value="Neuen Blog-Eintrag speichern">
			</fieldset>
		</form>
		
		
		<?php echo $dbMessageNewCat ?>
		<form action="" method="POST">
			<input type="hidden" name="newCategory">
			
			<fieldset>
				<legend>Neue Kategorie anlegen</legend>
					<br>
				<span class="error"><?php echo $errorNewCategory ?></span><br>	
				<input type="text" name="inputText" placeholder="Kategorie...">
					<br>
				<input type="submit" value="Neue Kategorie speichern">
			</fieldset>
		</form>
	</body>

</html>