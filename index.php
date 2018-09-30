<?php
/********************************************************************************************/

				
			/***********************************/
			/********** KONFIGURATION **********/
			/***********************************/
			
			// Externe Dateien einbinden:
			require_once("include/config.inc.php");
			require_once("include/form.inc.php");
			require_once("include/db.inc.php");
			require_once("include/date_time.inc.php");
			
			$pdo = dbConnect(); 
			
			
/********************************************************************************************/


			/**********************************************/
			/********** VARIABLEN INITIALISIEREN **********/
			/**********************************************/
			
			$loginMessage = NULL;




/********************************************************************************************/

				
			/************************************/
			/********** SESSION START **********/
			/************************************/
			
			// Session starten um zu prüfen, ob schon eingeloggt
			session_start();
			
			
/********************************************************************************************/


		/*********************************************************************/
		/************* KATEGORIEN AUS DB AUSLESEN ******************/
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

		/*********************************************************************/
		/************* BLOGEINTRÄGE AUS DB AUSLESEN ******************/
		/*********************************************************************/
		
		// DB-Operation über alle 3 Tabellen
		$statement = $pdo->prepare(" 
											SELECT * FROM blogs 
											INNER JOIN categories USING (cat_id)
											INNER JOIN users USING (usr_id)
											ORDER BY blog_date DESC
											");
		
		$statement->execute() OR DIE( $statement->errorInfo()[2] );
		
		
		
		// Schritt 4 DB: Daten weiterverarbeiten
		// In $blogEntriesArray das komplette 2-Dimensiolae Array aus der DB speichern
		$blogEntriesArray = $statement->fetchAll();
		
/*		
			echo "<pre>";
			print_r($blogEntriesArray);
			echo "</pre>";
*/
			
			
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
				
				
				/********** LOGOUT **********/
				if( $action == "logout" ) {
if (DEBUG)		echo "<p class='debug'>LOGOUT wird durchgeführt</p>"; 	

				
					// Schritt 4 URL: Daten weiterverarbeiten
				
					// TODO: ggfs. Timestamp LOGOUT In DB speichern --> dann aber auch TIMESTAMP LOGIN (in index.php) in DB speichern
					
					// TODO:  Session löschen
					/********** SESSION LÖSCHEN **********/
					session_destroy();
					header("Location: index.php");
				
				// ENDE LOGOUT	

				/********** SHOW CATEGORY **********/
				} elseif( $action == "showCategory" ) {
if (DEBUG)		echo "<p class='debug'>Kategoriefilter ist aktiv...</p>";					
					
					$catId = cleanString($_GET['catId']);
if(DEBUG)		echo "<p class='debug'>\$catId: $catId</p>";					
					
					// DB-Operation
					// DB-Operation über alle 3 Tabellen
					$statement = $pdo->prepare(" 
														SELECT * FROM blogs 
														INNER JOIN categories USING (cat_id)
														INNER JOIN users USING (usr_id)
														WHERE cat_id = :ph_cat_id
														ORDER BY blog_date DESC
														");
					
					$statement->execute( array(
														"ph_cat_id" => $catId
														) ) OR DIE( $statement->errorInfo()[2] );
					
					// In $blogEntriesArray das komplette 2-Dimensiolae Array aus der DB speichern
					$blogEntriesArray = $statement->fetchAll();
					
				}
		
			} // ENDE URL-PARAMETERVERARBEITUNG


/********************************************************************************************/



			/******************************************/
			/********** FORMULARVERARBEITUNG **********/
			/******************************************/

			// Schritt 1 FORM: Prüfen ob Formular abgeschickt wurde
			if( isset($_POST['formsentLoginData']) ){
if(DEBUG)	echo "<p class='debug'>Formular wurde abgeschickt</p>";

				echo "<pre>";
				print_r($_POST);
				echo "</pre>";
 			
			
				// Schritt 2 FORM: Werte auslesen, entschärfen, DEBUG-Ausgabe
				$loginname = cleanString( $_POST['loginname']);
				$password = cleanString( $_POST['password']);
if(DEBUG)		echo "<p class='debug'>\$loginname: $loginname</p>";
if(DEBUG)		echo "<p class='debug'>\$password: $password</p>";

				// Schritt 3 FORM: Werte validieren
				// DB-Operation nur wenn tatsächlich Werte eingetragen, valide Emailadresse, PW min. 4 Zeichen
				$errorLoginname	= checkEmail( $loginname );
				$errorPassword		= checkInputString( $password, 4 ); 
				
				if( $errorLoginname OR $errorPassword ) {
					// Fehlerfall
if(DEBUG)		echo "<p class='debug'>FEHLER bei Eingabe von Loginname oder Password</p>";					
					$loginMessage = "<p class='error'>Logindaten sind ungültig!</p>";
				} else {
					// Erfolgsfall
if(DEBUG)		echo "<p class='debug'>Formular ist korrekt ausgefüllt - Logindaten werden nun geprüft...</p>";						
					
					
					// Schritt 4 FORM: Daten weiterverarbeiten
					
					/************* DATENBANKOPERATIONEN *************/
					
						// Schritt 1 DB: Connect DB
						 
					
						// Schritt 2 DB: SQL-Statement vorbereiten
						$statement = $pdo->prepare("SELECT * FROM users
															WHERE usr_email = :ph_usr_email");
						
						// Schritt 3 DB: SQL-Statement ausführen, ggfs. Platzhalter füllen
						$statement->execute( array( "ph_usr_email" 		=> $loginname													
													) ) OR DIE( $statement->errorInfo()[2] );
						
						// Schritt 4 DB: Daten weiterverarbeiten 
						$row = $statement->fetch();
						
						if( !$row ) {
							// Fehlerfall
if(DEBUG)				echo "<p class='debug'>Email-Adresse existieren nicht in der DB</p>";	
							// Ausgabe für USER
							$loginMessage = "<p class='error'>Loginname oder Passwort sind falsch</p>";
							
						} else {
							// Erfolgsfall
if(DEBUG)				echo "<p class='debug'>Email-Adresse existiert in der DB</p>";
							
							echo "<pre>";
							print_r($row);
							echo "</pre>";
							
							
							// TODO: Passwort prüfen
							/**************** PASSWORT PRÜFEN ********************/ 
							
							if( !password_verify($password, $row['usr_password']) ) {
								// Fehlerfall
if(DEBUG)					echo "<p class='debug'>Passwort stimmt nicht mit dem in der DB überein</p>";
								// Ausgabe für USER 
								$loginMessage = "<p class='error'>Loginname oder Passwort sind falsch</p>";
								
							} else {
								// Erfolgsfall 
if(DEBUG)					echo "<p class='debug'>Passwort stimmt mit dem in der DB überein. Login wird verarbeitet...</p>";	
							
								// TODO: Bei Erfolg: Session starten, user_firstname, lastname, uid in session speichern
								/**************** LOGIN VERARBEITEN ********************/ 
								
								// Session starten
								// bereits oben gestartet 
								
								$_SESSION['usr_id'] 			= $row['usr_id'];
								$_SESSION['usr_firstname'] = $row['usr_firstname']; 
								$_SESSION['usr_lastname'] 	= $row['usr_lastname'];
								
								echo "<pre>";
								print_r($_SESSION);
								echo "</pre>";
								
								
								// TODO: Weiterleitung auf Dashboard
								/********** WEITERLEITUNG AUF dashboard.php VORNEHMEN *********/
								
								header("Location: dashboard.php");
							
							
							} // ENDE LOGIN DATEN VERARBEITEN
						
						} // ENDE PASSWRORT PRÜFEN
					
				} // ENDE Schritt 3 Werte validieren /  Login u. Password check 
				
			} // ENDE FORMULARVERARBEITUNG
			

/********************************************************************************************/


			
?>


<!doctype html>

<html>

	<head>
		<meta charset="utf-8">
		<title>Blog Projekt Index</title>
		<link rel="stylesheet" href="css/main.css">
	</head>

	<body>
		
			
			<?php if( !isset($_SESSION['usr_id']) ): ?>  					<!-- nach Login ausblenden 1/3 -->
			<h1>Login Blog</h1>
				<form action="" method="POST">
					<input type="hidden" name="formsentLoginData">
					
					<fieldset>
						<legend>Login</legend>
						
						<?php echo $loginMessage  ?>
						<input class="short" type="text" name="loginname" placeholder="Login with your email Adress"> 
						<input class="short" type="password" name="password" placeholder="password"> 
						
						<input class="short" type="submit" value="Anmelden">
						
					</fieldset>
				</form>
			
			<?php else: ?>					 											<!-- nach Login ausblenden 2/3 -->
				<p><a href="?action=logout">Logout</a></p>
				<p><a href="dashboard.php">Zum Dashboard</a></p>
			<?php endif ?> 															<!-- nach Login ausblenden 3/3 -->
			
			
			<h1>Blog-Eintrag</h1>
			
				<p><a href="index.php">Alle Blogeinträge zeigen</a></p>
				<main class='fleft' style='border: 2px solid black; width: 60%; border-radius: 5px; padding: 25px;'>
				
				<?php 
				
					
					
					
					/*
					echo "<pre>";
					print_r($row);
					echo "</pre>";
					*/
					
					
					// Schleife Start 
					// blog_date umwandeln
					// Das 2-dimensionale Array $blogEntriesArray durchlaufen und in jedem Durchlauf einen
					// Blog-Eintrag in Form eines einzelnen Arrays auslesen
					foreach( $blogEntriesArray AS $blogEntry ) {
						
						
						
						$catName 		= $blogEntry['cat_name'];
						$usrFirstName 	= $blogEntry['usr_firstname'];
						$usrLastName 	= $blogEntry['usr_lastname'];
						$blogHeadline	= $blogEntry['blog_headline'];
						$blogContent	= $blogEntry['blog_content'];
						
						$blogImageAlignment = $blogEntry['blog_imageAlignment'];
						$blogImage		= $blogEntry['blog_image'];
						$blogDate 		= $blogEntry['blog_date'];
						$blogTime 		= $blogEntry['blog_date'];
					
						$euDateTimeArray = isoToEuDateTime($blogDate);
					
						
						echo "<article>";
							echo "<h3>Kategorie: $catName</h3>";
							echo "<p>$usrFirstName $usrLastName schrieb am $euDateTimeArray[date] um $euDateTimeArray[time] Uhr:</p>";
							echo "<h2>$blogHeadline</h2>";
							echo "<p>$blogContent</p>";
							if( $blogImage ) {
								echo "<img class='avatar $blogImageAlignment' src='$blogImage' alt='$blogHeadline' title='$blogHeadline'>";
							}
							
							// Alternativ auch möglich so: 
							/*	<?php if($blogEntry['blog_image']): ?>
									<img style="width: 120px; float: <?= $blogEntry['blog_imageAlignment'] ?>" src=" <?= $blogEntry['blog_image'] ?>" alt=" <?= $blogEntry['blog_headline'] ?>" title=" <?= $blogEntry['blog_headline'] ?>">
								<?php endif ?>*/
								
							echo "<div class='clearer'></div>";
						echo "</article>";
						echo "<hr>";
						
					} // ENDE SCHLEIFE
					
					
				?>
				</main>
			
			<div class="fright" style="border: 2px solid black; width: 30%;  border-radius: 5px; padding: 25px;">
				<?php 
					foreach( $categoriesArray AS $key=>$value ) {
						echo "<p><a href='?action=showCategory&catId=$key'>$value</a></p>";
					}
				?>
			</div>
			<div class="clearer"></div>
			
	</body>

</html>