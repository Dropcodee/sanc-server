<?php
	class Db {
		protected $dbhost = "sql7.freemysqlhosting.net";
		protected $dbname = "sql7262031";
		protected $dbuser = "sql7262031";
		protected $dbpass = "Jesus_999";

		public function connect() {
			$dsn = 'mysql:host='.$this->dbhost.';dbname='.$this->dbname.'';
			$dbcon = new PDO($dsn, $this->dbuser, $this->dbpass);
			$dbcon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $dbcon;
		}

		/*************************************************************
 		******************** GET QUERY FUNCTION **********************
 		*************************************************************/
		public function getQuery($sql, $params = null) {
			try {
				// Connection to Database
				$pdo = $this->connect();
				// Query
				$statement = $pdo->prepare($sql);
				// Get the parameters and execute them
				$statement->execute($params);
				// Check if there is data in the database
				$count = $statement->rowCount();
				if ($count > 0) {
					return $statement->fetchAll(PDO::FETCH_OBJ);
				} else {
					return false;
				}
				$pdo = null;
			} catch (PDOException $e) {
				echo '{"error": {"text": "'.$e->getMessage().'"}}';
			}
		}

		/************************************************************
 		******************** POST QUERY FUNCTION ********************
 		************************************************************/
		public function postQuery($sql, $params = null, $msg) {
			try {
				// Connection to Database 
				$pdo = $this->connect();

				// Query
				$statement = $pdo->prepare($sql);

				##### Get the parameters and execute them	#####
				$statement->execute($params);

				##### Print out the Response #####
				echo '{"success": {"text": "'.$msg.'"}}';

				// Close the Connection
				$pdo = null;
			} catch(PDOException $e) {
				echo '{"error": {"text": "'.$e->getMessage().'"}}';
			}
		}

	}
