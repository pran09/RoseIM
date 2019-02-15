CREATE DATABASE `RoseIM` /*!40100 DEFAULT CHARACTER SET latin1 */;

DELIMITER //

CREATE PROCEDURE `Generate_Tables`()
Begin

CREATE TABLE Person(
	person_ID INT AUTO_INCREMENT PRIMARY KEY,
	firstName varchar(20) NOT NULL,
	lastName varchar(25) NOT NULL,
	email varchar(50) NOT NULL UNIQUE CHECK(email LIKE '@rose-hulman.edu$'),
    password varchar(100) NOT NULL,
	sex varchar(6) NOT NULL CHECK(sex = 'Male' or sex = 'Female')
);

CREATE TABLE Player(
	person_ID INT PRIMARY KEY,
	FOREIGN KEY (person_ID)
		REFERENCES Person(person_ID)
		ON DELETE RESTRICT
);

CREATE TABLE Referee (
	person_ID INT PRIMARY KEY,
	FOREIGN KEY (person_ID)
		REFERENCES Person(person_ID)
		ON DELETE RESTRICT
);

CREATE TABLE Sport (
	name varchar(25) PRIMARY KEY,
	rules varchar(5000)
);

CREATE TABLE League (
	league_ID INT AUTO_INCREMENT PRIMARY KEY,
	name varchar(30),
	sport varchar(25),
	FOREIGN KEY (sport)
		REFERENCES Sport(name)
		ON DELETE RESTRICT
);

CREATE TABLE Facility(
	facility_ID INT AUTO_INCREMENT PRIMARY KEY,
	location VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE Game (
	game_ID INT AUTO_INCREMENT PRIMARY KEY,
	ref INT NOT NULL,
	facility INT NOT NULL,
	league INT,
    StartDateTime DATETIME NOT NULL,
	FOREIGN KEY (ref)
		REFERENCES Referee(person_ID)
		ON DELETE RESTRICT,
	FOREIGN KEY (facility)
		REFERENCES Facility(facility_ID)
		ON DELETE RESTRICT,
	FOREIGN KEY (league)
		REFERENCES League(league_ID)
		ON DELETE RESTRICT
);

CREATE TABLE Team (
	name varchar(50) NOT NULL,
	team_ID INT AUTO_INCREMENT PRIMARY KEY,
	league INT,
	FOREIGN KEY (league)
		REFERENCES League(league_ID)
		ON DELETE RESTRICT
);

CREATE TABLE PlaysOn(
	role varchar(30) NOT NULL CHECK(role = 'Captain' OR role = 'Player'),
	player INT NOT NULL,
	team INT NOT NULL,
	FOREIGN KEY (player)
		REFERENCES Person(person_ID)
		ON DELETE RESTRICT,
	FOREIGN KEY (team)
		REFERENCES Team(team_ID)
		ON DELETE RESTRICT
);

CREATE TABLE Plays(
	isHome BOOLEAN NOT NULL,
	home_Score INT CHECK(home_Score >= 0 or home_Score = NULL),
	away_Score INT CHECK(away_Score >= 0 or away_Score = NULL),
	team1 INT NOT NULL,
	team2 INT NOT NULL CHECK(team1 != team2),
	game INT NOT NULL UNIQUE,
	FOREIGN KEY (team1)
		REFERENCES Team(team_ID)
		ON DELETE RESTRICT,
	FOREIGN KEY (team2)
		REFERENCES Team(team_ID)
		ON DELETE RESTRICT,
	FOREIGN KEY (game)
		REFERENCES Game(game_ID)
		ON DELETE RESTRICT
);
END//

CREATE VIEW `RoseIM`.`My_Teams` AS
    SELECT 
        `RoseIM`.`Team`.`league` AS `league`,
        `RoseIM`.`Team`.`team_ID` AS `team`,
        `RoseIM`.`League`.`sport` AS `sport`,
        `Player_Person`.`email` AS `email`
    FROM
        (((`RoseIM`.`Team`
        JOIN `RoseIM`.`League`)
        JOIN `RoseIM`.`Player_Person`)
        JOIN `RoseIM`.`PlaysOn`)
    WHERE
        ((`RoseIM`.`Team`.`team_ID` = `RoseIM`.`PlaysOn`.`team`)
            AND (`RoseIM`.`PlaysOn`.`player` = `Player_Person`.`person_ID`)
            AND (`RoseIM`.`Team`.`league` = `RoseIM`.`League`.`league_ID`)
            AND (`RoseIM`.`PlaysOn`.`team` = `RoseIM`.`Team`.`team_ID`))//
            
CREATE VIEW `RoseIM`.`Player_Person` AS
    SELECT 
        `RoseIM`.`Person`.`person_ID` AS `person_ID`,
        `RoseIM`.`Person`.`firstName` AS `firstName`,
        `RoseIM`.`Person`.`lastName` AS `lastName`,
        `RoseIM`.`Person`.`email` AS `email`,
        `RoseIM`.`Person`.`sex` AS `sex`
    FROM
        (`RoseIM`.`Person`
        JOIN `RoseIM`.`Player` ON ((`RoseIM`.`Person`.`person_ID` = `RoseIM`.`Player`.`person_ID`)))//
        
CREATE VIEW `RoseIM`.`Ref_Person` AS
    SELECT 
        `RoseIM`.`Person`.`person_ID` AS `person_ID`,
        `RoseIM`.`Person`.`firstName` AS `firstName`,
        `RoseIM`.`Person`.`lastName` AS `lastName`,
        `RoseIM`.`Person`.`email` AS `email`,
        `RoseIM`.`Person`.`sex` AS `sex`
    FROM
        (`RoseIM`.`Person`
        JOIN `RoseIM`.`Referee` ON ((`RoseIM`.`Person`.`person_ID` = `RoseIM`.`Referee`.`person_ID`)))//
        
CREATE VIEW `RoseIM`.`Team_Win_Percentage` AS
    SELECT 
        `RoseIM`.`Team`.`name` AS `name`,
        (`RoseIM`.`Team`.`wins` / (`RoseIM`.`Team`.`wins` + `RoseIM`.`Team`.`losses`)) AS `WinPercentage`,
        `RoseIM`.`Team`.`team_ID` AS `team_ID`,
        `RoseIM`.`League`.`league_ID` AS `league_ID`,
        `RoseIM`.`Team`.`wins` AS `wins`,
        `RoseIM`.`Team`.`losses` AS `losses`
    FROM
        (`RoseIM`.`Team`
        JOIN `RoseIM`.`League` ON ((`RoseIM`.`Team`.`league` = `RoseIM`.`League`.`league_ID`)))//
        
CREATE FUNCTION `Create_Facility`(loc varchar(100)) RETURNS int(11)
BEGIN

	IF (loc IS NULL)
		THEN
			RETURN 1; # Location is null
	ELSEIF(loc IN (Select location From Facility))
		THEN
			RETURN 2;	# location already exists
	END IF;
	INSERT INTO Facility(location)
	VALUES (loc);
	RETURN 0;
END//

CREATE FUNCTION `Create_Game`(spor varchar(25), reff INT, fac INT, league varchar(30), datetim DATETIME) RETURNS int(11)
BEGIN

	SET @leagueid = (SELECT league_ID FROM League WHERE sport = spor AND League.name = league);


	IF (datetim IN (SELECT StartDateTime FROM Game WHERE ref = reff))
		THEN
			RETURN 6; #Ref already assigned to a game at this time
	END IF;

	INSERT INTO Game(ref, facility, league, StartDateTime)
	VALUES (reff, fac, @leagueid, datetim);

	RETURN 0;
END//

CREATE FUNCTION `Create_League`(NewName VARCHAR(20), InSport VARCHAR(20)) RETURNS int(11)
BEGIN

	IF (NewName IS NULL)
		THEN
			RETURN 1; #NewName is null
	END IF;
    
    IF (InSport IS NULL)
		THEN
			RETURN 2; #InSport is null 
	END IF;
    
	IF ((InSport NOT IN (SELECT name FROM Sport)) OR (NewName IN (SELECT name FROM League WHERE sport = InSport)))
		THEN
			RETURN 3; #League already created for that sport and sport exists
	ELSE
		INSERT INTO League(name, sport)
		VALUES (NewName, InSport);
	END IF;
  
	RETURN 0;
END//

CREATE FUNCTION `Create_Person`(firstN varchar(20), lastN varchar(25), mail varchar(50), pwrd varchar(100), gender varchar(6)) RETURNS int(11)
BEGIN

	IF(firstN IS NULL)
		THEN
			RETURN 1; #Null email address
	END IF;
    
    IF(lastN IS NULL)
		THEN
			RETURN 2; #Null email address
	END IF;
    
    IF(mail IS NULL)
		THEN
			RETURN 3; #Null email address
	END IF;
    
    IF(pwrd IS NULL)
		THEN
			RETURN 4; #Null email address
	END IF;
    
    IF(gender IS NULL)
		THEN
			RETURN 5; #Null email address
	END IF;
    
    
	IF(mail NOT LIKE '%@rose-hulman.edu')
		THEN
			RETURN 6;	# email not a Rose-Hulman email
	ELSEIF(mail IN (Select email From Person))
		THEN
			RETURN 7;	# person already exists
	ELSEIF(pwrd IS NULL)
		THEN
			RETURN 8;	# password cannot be null
	ELSEIF(gender != 'Male' AND gender != 'Female')
		THEN
		RETURN 9;	# sex is not male or female
	END IF;

    INSERT INTO Person(firstName, lastName, email, password, sex)
			VALUES(firstN, lastN, mail, pwrd, gender);
            
    RETURN 0;

END//

CREATE FUNCTION `Create_Player`(firstN varchar(20), lastN varchar(25), mail varchar(50), pwd varchar(100), gender varchar(6)) RETURNS int(11)
BEGIN

	IF(mail IN (SELECT email FROM Person))
		THEN
			INSERT INTO Player(person_ID)
			VALUES((Select person_ID From Person Where email = mail AND lastName = lastName AND firstName = firstN));
            RETURN 0;
	END IF;
    
	SET @x = Create_Person(firstN, lastN, mail, pwd, gender);
	IF(@x != 0)
		THEN
			RETURN @x;
	ELSE
        INSERT INTO Player(person_ID)
        VALUES((Select person_ID From Person Where email = mail AND lastName = lastName AND firstName = firstN));
	END IF;
    
    RETURN @x;
    
END//

CREATE FUNCTION `Create_Plays`(h_score int, a_score int, team_one int, team_two int, aGame int) RETURNS int(11)
BEGIN

	IF(team_one IS NULL OR team_two IS NULL OR aGame IS NULL)
		THEN
			RETURN 1;  # null inputs not allowed in home and teams
	ELSEIF(team_one = team_two)
		THEN
			RETURN 2;	# two same teams have been added
	ELSEIF(team_one NOT IN (Select team_ID From Team) OR team_two NOT IN (Select team_ID from Team))
		THEN
			RETURN 3;	# not valid teams
	ELSEIF(aGame NOT IN (Select game_ID From Game))
		THEN
			RETURN 4;	# not valid game
	ELSE
		INSERT INTO Plays(home_score, away_score, team1, team2, game)
        VALUES(h_score, a_score, team_one, team_two, aGame);
        RETURN 0;
	END IF;

END//

CREATE FUNCTION `Create_PlaysOn`(player_id int, team_id int, player_role varchar(30)) RETURNS int(11)
BEGIN

	IF(player_id IS NULL OR team_id IS NULL OR player_role IS NULL)
		THEN
			RETURN 1;	# inputs cannot be null
	ELSEIF(player_id NOT IN (Select person_ID From Player))
		THEN
			RETURN 2;	# invalid player
	ELSEIF(team_id NOT IN (Select team_ID From Team))
		THEN
			RETURN 3;	# invalid team
	ELSE
		INSERT INTO PlaysOn (role, player, team)
		VALUES(player_role, player_id, team_id);
        RETURN 0;
	END IF;

END//

CREATE FUNCTION `Create_Referee`(firstN varchar(20), lastN varchar(25), mail varchar(50), pwd varchar(100), gender varchar(6)) RETURNS int(11)
BEGIN

	IF(mail IN (SELECT email FROM Person))
		THEN
			INSERT INTO Referee(person_ID)
			VALUES((Select person_ID From Person Where email = mail AND lastName = lastName AND firstName = firstN));
            RETURN 0;
	END IF;
    
	SET @x = Create_Person(firstN, lastN, mail, pwd, gender);
	IF(@x != 0)
		THEN
			RETURN @x;
	ELSE
        INSERT INTO Referee(person_ID)
        VALUES((Select person_ID From Person Where email = mail AND lastName = lastName AND firstName = firstN));
	END IF;
    
    RETURN @x;
    
END//

CREATE FUNCTION `Create_Sport`(NewName VARCHAR(20), rules VARCHAR(200)) RETURNS int(11)
BEGIN

	IF (NewName IS NULL)
    THEN
		RETURN 1; #NewName is null
	END IF;
    
    IF (rules IS NULL)
    THEN
		RETURN 2; #rules is null 
	END IF;

  IF (NewName IN (SELECT name FROM Sport))
  THEN
  RETURN 3; #This Sport already exists
  ELSE
  INSERT INTO Sport(name, rules)
  VALUES
  (NewName, rules);
  END IF;
 
   RETURN 0;
 
END//

CREATE FUNCTION `Create_Team`(team_name varchar(50), leagueid int) RETURNS int(11)
BEGIN

	IF (team_name IS NULL)
    THEN
		RETURN 1; #team_name is null
	END IF;
    
    IF (leagueid IS NULL)
    THEN
		RETURN 2; #leagueid is null 
	END IF;
    

	IF (team_name IN (SELECT name FROM Team WHERE league = leagueid))
    THEN
		RETURN 3;  # team name already exists in this league
	ELSEIF (leagueid NOT IN (SELECT league_ID FROM League))
    THEN
		RETURN 4; # league does not exist
	ELSE
		INSERT INTO Team (name, league)
		VALUES(team_name, leagueid);
    END IF;
    
    RETURN 0;
END//

CREATE FUNCTION `Login`(mail varchar(50), pwrd varchar(100)) RETURNS int(11)
BEGIN

	IF(mail IS NULL)
	THEN
		RETURN 1; #Null email address
	END IF;

	IF(mail NOT IN (SELECT email FROM Person))
    THEN
    RETURN 3; #Not a valid email
    END IF;

	IF(pwrd NOT IN (SELECT password FROM Person WHERE email = mail))
	THEN
		RETURN 4; #Invalid password
	END IF;
	RETURN 0;

END//

CREATE FUNCTION `Remove_Facility`(FID INT) RETURNS int(11)
BEGIN

IF (FID IS NULL)
THEN
RETURN 0; #FacilityID is null
END IF;

IF (FID IN (SELECT facility_ID FROM  Facility))
THEN
  DELETE FROM Facility
  WHERE facility_ID = FID;
RETURN 0;
END IF;

RETURN 1; #Facility does not exist

END//

CREATE FUNCTION `Remove_Game`(gameid INT) RETURNS int(11)
BEGIN

IF (gameid IS NULL)
THEN
RETURN 0; #GameID is null
END IF;

IF (gameid NOT IN (SELECT game_ID FROM Game))
THEN
RETURN 1; #Game_ID does not exist
END IF;

DELETE FROM Game
WHERE game_ID = gameid;

RETURN 0;
END//

CREATE FUNCTION `Remove_League`(NewName VARCHAR(20), InSport VARCHAR(20)) RETURNS int(11)
BEGIN

	IF (NewName IS NULL)
    THEN
		RETURN 1; #NewName is null
	END IF;
    
    IF (InSport IS NULL)
    THEN
		RETURN 2; #InSport is null 
	END IF;
    
    
  IF (NewName IN (SELECT name FROM League WHERE League.sport = sport))
  THEN
  DELETE FROM League
  WHERE name = NewName AND sport = InSport;
  RETURN 0;
  END IF;
  
  RETURN 3; #Name is not a leage
END//

CREATE FUNCTION `Remove_Person`(mail varchar(50)) RETURNS int(11)
BEGIN

	IF (mail IS NULL)
		THEN
			RETURN 1; #mail is null
	END IF;
    
    
	IF(mail NOT IN (SELECT email FROM Person))
		THEN
		RETURN 2; #Email does not exist
	ELSE
		DELETE FROM Person
		WHERE email = mail;
		RETURN 0;
	END IF;

END//

CREATE FUNCTION `Remove_Player`(mail varchar(50)) RETURNS int(11)
BEGIN

	IF (mail IS NULL)
		THEN
			RETURN 1; #email is null
	END IF;
    
	IF(mail NOT IN (Player_Person))
		THEN
			RETURN 2; #Email does not exist
	ELSE
		DELETE FROM Player
		WHERE person_ID IN (SELECT person_ID
							FROM Player_Person 
							WHERE email = mail);
		RETURN 0;
	END IF;
    
END//

CREATE FUNCTION `Remove_Plays`(aGame int) RETURNS int(11)
BEGIN

	IF(aGame IS NULL)
		THEN
			RETURN 1;	# null input not allowed
	ELSEIF(aGame NOT IN (Select game_ID From Game))
		THEN
			RETURN 2;	# not valid game
	ELSE
		DELETE FROM Plays
		WHERE game = aGame;
        RETURN 0;
	END IF;

END//

CREATE FUNCTION `Remove_PlaysOn`(player_id int, team_id int) RETURNS int(11)
BEGIN

	IF(player_id NOT IN (Select player From PlaysOn Where team = team_id))
		THEN
			RETURN 1;	# player doesn't play on team
	ELSE
		DELETE FROM PlaysOn
        WHERE player = player_id AND team = team_id;
	END IF;

END//

CREATE FUNCTION `Remove_Referee`(mail varchar(50)) RETURNS int(11)
BEGIN

	IF (mail IS NULL)
		THEN
			RETURN 1; #mail is null
	END IF;
    
	IF(mail NOT IN (SELECT *
					FROM Person p
					JOIN Referee r ON r.person_ID = p.person_ID))
		THEN
			RETURN 2; #Email does not exist
	ELSE
		DELETE FROM Referee
		WHERE person_ID IN (SELECT person_ID 
							FROM Ref_Person 
							WHERE email = mail);
		RETURN 0;
	END IF;

END//

CREATE FUNCTION `Remove_Sport`(NewName VARCHAR(20)) RETURNS int(11)
BEGIN

IF (NewName IS NULL)
    THEN
		RETURN 1; #NewName is null
	END IF;
    
    
  IF (NewName IN (SELECT name FROM Sport))
  THEN
  DELETE FROM Sport
  WHERE name = NewName;
  RETURN 0;
  END IF;
  
  RETURN 2; #Sport does not exist
  
END//

CREATE FUNCTION `Remove_Team`(team_name varchar(50), leagueid int) RETURNS int(11)
BEGIN


	IF (team_name IS NULL)
    THEN
		RETURN 1; #team_name is null
	END IF;
    
    IF (leagueid IS NULL)
    THEN
		RETURN 2; #leagueid is null
	END IF;
    
    
    
	IF (team_name NOT IN (SELECT name FROM Team WHERE league = leagueid))
    THEN
		RETURN 3; # team does not exist
	ELSEIF (leagueid NOT IN (SELECT league_id FROM League))
    THEN
		RETURN 4; # league does not exist
	ELSE
		DELETE FROM Team
		WHERE name = team_name AND league = leagueid;
	END IF;
    
    RETURN 0;
END//

CREATE FUNCTION `Update_Facility`(FacilityID INT, NewLocation varchar(100)) RETURNS int(11)
BEGIN

	IF (FacilityID IS NULL)
	THEN
		RETURN 0; #FacilityID is null
	END IF;

	IF(FacilityID NOT IN (SELECT facility_ID FROM  Facility))
    THEN
		RETURN 1; #Facility does not exist
	END IF;


	IF (NewLocation IS NULL)
	THEN
		RETURN 2; #NewLocation is null
	END IF;

		UPDATE FACILITY
		SET location = NewLocation
		Where facility_ID = FacilityID;
		RETURN 0;
END//

CREATE DEFINER=`root`@`localhost` FUNCTION `Update_Game`(GameID INT, NewRef INT, NewFacility INT, NewStartDateTime DATETIME) RETURNS int(11)
BEGIN
	IF (GameID IS NULL)
	THEN
		RETURN 1; #GameID is null
	END IF;

	IF (GameID NOT IN (SELECT game_ID FROM Game))
	THEN
		RETURN 2; #Game_ID does not exist
	END IF;

	IF (NewRef IS NOT NULL)
	THEN
		SET @datim = (SELECT StartDateTime FROM Game WHERE game_ID = GameID);

		IF (NewRef NOT IN (SELECT person_ID FROM Referee))
		THEN
			RETURN 3; #Referee does not exist
		ELSEIF (@datim IN (SELECT StartDateTime FROM Game WHERE ref = NewRef))
		THEN
			RETURN 4; #Ref already assigned to a game at this time
		END IF;

	UPDATE Game
	SET ref = NewRef
	WHERE game_ID = GameID;
	END IF;

	IF (NewFacility IS NOT NULL)
	THEN

		IF (NewFacility NOT IN (SELECT facility_ID FROM Facility))
		THEN
			RETURN 5; #Facility does not exist
		END IF;

	UPDATE Game
	SET facility = NewFacility
	WHERE game_ID = GameID;

	END IF;

	IF (NewStartDateTime IS NOT NULL)
	THEN
		UPDATE Game
		SET StartDateTime = NewStartDateTime
		WHERE game_ID = GameID;
	END IF;
RETURN 0;

END//

CREATE FUNCTION `Update_League`(CurrentName VARCHAR(20), NewName VARCHAR(20), ForSport VARCHAR(20)) RETURNS int(11)
BEGIN


	IF (CurrentName IS NULL)
    THEN
		RETURN 1; #OldName is null
	END IF;
    
    IF (CurrentName NOT IN (SELECT name FROM League WHERE sport = ForSport))
    THEN
		RETURN 2; #CurrentLeague does not exist
	END IF;
    
    
    IF (NewName IS NULL)
    THEN
		RETURN 3; #NewName is null 
	END IF;
    
    
    IF (NewName IN (SELECT name FROM League WHERE sport = ForSport))
    THEN
		RETURN 4; #NewName already exists
	END IF;
    

  UPDATE League
  SET name = NewName
  Where name = CurrentName AND sport = ForSport;
  RETURN 0;
 
END//

CREATE FUNCTION `Update_Person`(newFirst varchar(20), newLast varchar(25), currentMail varchar(50), newMail varchar(50)) RETURNS int(11)
BEGIN

	IF(currentMail IS NULL)
		THEN
			RETURN 1;		#old mail is null
	ELSEIF(currentMail NOT IN (SELECT email FROM Person))
		THEN
			RETURN 2;		#invalid email, doesn't exist
	ELSEIF(newFirst IS NOT NULL)
		THEN
			UPDATE Person
            SET firstName = newFirst
            WHERE email = currentMail;
	END IF;
	IF(newLast IS NOT NULL)
		THEN
			UPDATE Person
            SET lastName = newLast
            WHERE email = currentMail;
	END IF;
	IF(newMail IS NOT NULL)
		THEN
			UPDATE Person
            SET email = newMail
            WHERE email = currentMail;
	END IF;
    
    RETURN 0;

END//

CREATE FUNCTION `Update_Player`(newFirst varchar(20), newLast varchar(25), currentMail varchar(50), newMail varchar(50)) RETURNS int(11)
BEGIN

	IF(currentMail IS NULL)
		THEN
			RETURN 1;		#old email is null
	ELSEIF(currentMail NOT IN (SELECT email FROM Player_Person))
		THEN
			RETURN 2;		#email doesn't exist for referee
	END IF;
    IF(newLast IS NOT NULL)
		THEN
			UPDATE Player_Person
            SET lastName = newLast
            WHERE email = currentMail;
	END IF;
    IF(newFirst IS NOT NULL)
		THEN
			UPDATE Player_Person
            SET firstName = newFirst
            WHERE email = currentMail;
	END IF;
    IF(newMail IS NOT NULL)
		THEN
			UPDATE Player_Person
            SET email = newMail
            WHERE email = currentMail;
	END IF;

END//

CREATE FUNCTION `Update_Plays`(h_score int, a_score int, aGame int) RETURNS int(11)
BEGIN

	IF(aGame IS NULL OR h_score IS NULL OR a_score IS NULL)
		THEN
			RETURN 1;	# null inputs not allowed
	ELSEIF(h_score = a_score)
    THEN
			RETURN 3; #No ties allowed
	ELSEIF(aGame NOT IN (Select game_ID From Game))
		THEN
			RETURN 2;	# not valid game
	ELSE
		UPDATE Plays
        SET home_score = h_score, away_score = a_score
        WHERE game = aGame;
        RETURN 0;
	END IF;

END//

CREATE FUNCTION `Update_Referee`(newFirst varchar(20), newLast varchar(25), currentMail varchar(50), newMail varchar(50)) RETURNS int(11)
BEGIN

	IF(currentMail IS NULL)
		THEN
			RETURN 1;		#old email is null
	ELSEIF(currentMail NOT IN (SELECT email FROM Ref_Person))
		THEN
			RETURN 2;		#email doesn't exist for referee
	END IF;
    IF(newLast IS NOT NULL)
		THEN
			UPDATE Ref_Person
            SET lastName = newLast
            WHERE email = currentMail;
	END IF;
    IF(newFirst IS NOT NULL)
		THEN
			UPDATE Ref_Person
            SET firstName = newFirst
            WHERE email = currentMail;
	END IF;
    IF(newMail IS NOT NULL)
		THEN
			UPDATE Ref_Person
            SET email = newMail
            WHERE email = currentMail;
	END IF;

END//

CREATE FUNCTION `Update_Sport`(CurrentName VARCHAR(20), NewName VARCHAR(20), NewRules VARCHAR(5000)) RETURNS int(11)
BEGIN

	IF (CurrentName IS NULL)
    THEN
		RETURN 1; #CurrentName is null
	ELSEIF(CurrentName NOT IN (SELECT name FROM Sport))
    THEN
		RETURN 2; #Sport does not exist
	END IF;
    
    
    
    IF (NewName IS NOT NULL)
    THEN
		IF (NewName NOT IN (SELECT name FROM Sport))
		THEN
			UPDATE Sport
			SET name = NewName
			WHERE name = CurrentName;
		ELSE RETURN 3; #NewName already exists
		END IF;
	END IF;
    
    
	IF (NewRules IS NOT NULL)
    THEN
		UPDATE Sport
		SET rules = NewRules
		WHERE CurrentName = SportName;
	END IF;
 
  
  RETURN 0; 
END//

CREATE FUNCTION `Update_Team`(CurrentName varchar(20), NewName varchar(20), LeagueID int, win int, loss int) RETURNS int(11)
Begin

	IF (CurrentName IS NULL)
    THEN
		RETURN 1; #CurrentName is null
	END IF;
    
	IF (LeagueID IS NULL)
    THEN
		RETURN 2; #LeagueID is null 
	END IF;
    
    IF(CurrentName NOT IN (Select name from Team Where league = LeagueID))
    THEN
		RETURN 3; #CurrentTeam does not exist
	END IF;
    
    IF (LeagueID NOT IN (SELECT league_ID FROM League))
    THEN
		RETURN 4; #League does not exist
	END IF;
    
    
    IF (NewName IS NOT NULL)
    THEN
		IF(NewName IN (SELECT name FROM Team WHERE league = LeagueID))
			THEN
				RETURN 5; #Team already exists
		END IF;
        
		UPDATE Team
		SET name = NewName
		WHERE name = CurrentName AND league = LeagueID;
	END IF;
    
    IF(win IS NOT NULL)
		THEN
			UPDATE Team
            SET wins = win
            WHERE name = CurrentName AND league = LeagueID;
	END IF;
    
    IF(loss IS NOT NULL)
		THEN
			UPDATE Team
            SET losses = loss
            WHERE name = CurrentName AND league = LeagueID;
	END IF;

	RETURN 0;

END//