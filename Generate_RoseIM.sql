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

CREATE PROCEDURE `Add_Sport_Data`()
BEGIN

	SET @a = Create_Sport('Softball', 'Hit the ball and run through the bases.');
    SET @b = Create_Sport('Basketball', 'Shoot the ball into the hoop.');
    SET @c = Create_Sport('Flag Football', 'Run or catch the ball in the endzone.');
    SET @d = Create_Sport('Ultimate Frisbee', 'Catch the frisbee in the endzone.');
    SET @e = Create_Sport('Volleyball', 'Hit the ball onto the other side of the net. 3 hits allowed per side. Ball cannot touch the ground.');
	SET @x = Create_Sport('Soccer', 'Kick the ball into the goal. No hands allowed.');

END//

CREATE PROCEDURE `Add_League_Data`()
BEGIN

	Set @a = Create_League('A League', 'Softball');
    SET @b = Create_League('B League', 'Softball');
    SET @c = Create_League('C League', 'Softball');
    SET @d = Create_League('Greek League', 'Softball');

    SET @a = Create_League('A League', 'Basketball');
    SET @b = Create_League('B League', 'Basketball');
    SET @c = Create_League('C League', 'Basketball');
    SET @d = Create_League('Greek League', 'Basketball');
    
    SET @a = Create_League('A League', 'Flag Football');
    SET @b = Create_League('B League', 'Flag Football');
    SET @c = Create_League('C League', 'Flag Football');
    SET @d = Create_League('Greek League', 'Flag Football');
    
    SET @a = Create_League('A League', 'Ultimate Frisbee');
    SET @b = Create_League('B League', 'Ultimate Frisbee');
    SET @c = Create_League('C League', 'Ultimate Frisbee');
    SET @d = Create_League('Greek League', 'Ultimate Frisbee');
    
    SET @a = Create_League('A League', 'Volleyball');
    SET @b = Create_League('B League', 'Volleyball');
    SET @c = Create_League('C League', 'Volleyball');
    SET @d = Create_League('Greek League', 'Volleyball');
    
	SET @a = Create_League('A League', 'Soccer');
    SET @b = Create_League('B League', 'Soccer');
    SET @c = Create_League('C League', 'Soccer');
    SET @d = Create_League('Greek League', 'Soccer');

END//

CREATE PROCEDURE `Add_Person_Data`()
BEGIN

	SET @a = Create_Person('Joe', 'Hood', 'hood.joe@rose-hulman.edu', '$2y$10$GVIUE.Eq/r/7MQbrWoXWkObCnnHdn308Rm6YYbT62b7SaJW4bM19i','Male');	#hello
    SET @b = Create_Person('Johnson', 'Joe', 'johnson.joe@rose-hulman.edu', '$2y$10$iRtwZvI/d4nnxNnFYbjHMOMFtCcvYL2tORwTxoejRX8PBBJHEVNDS','Male');	# personsyeet
    SET @c = Create_Person('Jacob', 'Petrisko', 'petrisjj@rose-hulman.edu', '$2y$10$QuAP9R8B4jNF76GJ9h/d1uTyi5gFtaqa9wpzlfgdeDi8Sq09frimK','Male');	# blue
    SET @d = Create_Person('Jose', 'Alvarez', 'alvarez@rose-hulman.edu', '$2y$10$vo1h5gKpBCT36Yb5ijxfheP0v6IFDfalT8lL2ikBUcNbBdXvqJN4K','Male');	# yellow
    SET @e = Create_Person('Bob', 'Sutton', 'sutton@rose-hulman.edu', '$2y$10$UyLyWsrvxLD1f0bFkk11V.O2TNMmULQqYlyTmZD8kr1uJ7fDbawqO','Male');	# somethingElseYuh128475
	SET @f = Create_Person('James', 'Taylor', 'tayjames@rose-hulman.edu', '$2y$10$E9BIvKh2YTs9Dsg3lk9iXeM4KdJes9NWBPqrYh0uCTW.YOPLupHaK','Male');	# routine
    SET @g = Create_Person('Jack', 'Ryan', 'ryan.jack@rose-hulman.edu', '$2y$10$.7pnqp79l3.rF9cXq0wPWuKAqLEGT.tg4HwP6NXBfbPbLfCHR9Bly','Male');		# data
    SET @h = Create_Person('Praneet', 'Chakraborty', 'chakrap@rose-hulman.edu', '$2y$10$3QN/lJnV/Q8R23tJU6bPQOYJo3.VLfJRcSRtZwp4S4gumDXiiaf2u','Male');	# mysqlYuhY7
	SET @i = Create_Person('Willy', 'Smith', 'bruh@rose-hulman.edu', '$2y$10$W7.IyW8kpauyPZJXiz4rTOcIhnR0Q11yZAwllUABK/dE5grl/YtSC','Male');	# sriram_mohan
    SET @j = Create_Person('Ryan', 'Dinkleburg', 'dinklebuuuuuurg@rose-hulman.edu', '$2y$10$6ebkEc9Xn5sRUbbAGn1v1.2td7LQSCj9QjysSOPaYP5dFxwhBkjiu','Male');	# databases333
    SET @k = Create_Person('Sam', 'Flickinger', 'flick@rose-hulman.edu', '$2y$10$DP2iAD7KkVnatm28q.n.1eBv6yrXvYuJxAftGJ1YxU5OALpo4KhOu','Male');	# 333-03
    SET @l = Create_Person('Chang', 'Wang', 'wutangclan@rose-hulman.edu', '$2y$10$hziwBmSHc7WxIiqM9s8JT.CQTULlZWvJfWBAScxoP.fRThM3SnzU6','Male');	# apply!revert
    SET @m = Create_Person('Reeve', 'Vixon', 'vixon@rose-hulman.edu', '$2y$10$K3B4eIZIjBs2dOiqJsPAee9MN3l/g3FlsJrKmxIs0pWkE68qwhnJ6','Male');	# generate_tables
    SET @n = Create_Person('Alexander', 'Pikachu', 'charizard@rose-hulman.edu', '$2y$10$K3B4eIZIjBs2dOiqJsPAee9MN3l/g3FlsJrKmxIs0pWkE68qwhnJ6','Male');	# generate_tables
    SET @o = Create_Person('Hannah', 'Smith', 'smithyboi@rose-hulman.edu', '$2y$10$WYbkEbq.B65XO8o65fD9hOq93cB04Z3PsOzJVwj2ON8H00Uz9IZuW','Female');	# bighandsmallhand
    SET @p = Create_Person('Laura', 'Hulman', 'rosehulman@rose-hulman.edu', '$2y$10$gDg1bDzTcA7iLgbMKt2hse3iIwSEhwpXDDtAbBPXqVqcKxtR23gvK','Female');	# blondebiotch
    SET @q = Create_Person('Lia', 'Apple', 'google@rose-hulman.edu', '$2y$10$z9EEQ5GfbMwhcq9zWFqz8OY5JC8r.ULMXUnpmBwBOHCPNjo.l4Wb2','Female');	# phpmysqljava

END//

CREATE PROCEDURE `Add_Player_Data`()
BEGIN

	SET @a = Create_Player('Joe', 'Hood', 'hood.joe@rose-hulman.edu', null, 'Male');
    SET @b = Create_Player('Johnson', 'Joe', 'johnson.joe@rose-hulman.edu', null, 'Male');
    SET @c = Create_Player('Jacob', 'Petrisko', 'petrisjj@rose-hulman.edu', null, 'Male');
    SET @d = Create_Player('Jose', 'Alvarez', 'alvarez@rose-hulman.edu', null, 'Male');
    SET @e = Create_Player('Bob', 'Sutton', 'sutton@rose-hulman.edu', null, 'Male');
	SET @f = Create_Player('James', 'Taylor', 'tayjames@rose-hulman.edu', null, 'Male');
    SET @g = Create_Player('Jack', 'Ryan', 'ryan.jack@rose-hulman.edu', null, 'Male');
    SET @h = Create_Player('Praneet', 'Chakraborty', 'chakrap@rose-hulman.edu', null, 'Male'); # up until here, already created in Add_Person
    SET @i = Create_Player('Abby', 'Shang', 'shang@rose-hulman.edu', '$2y$10$EOgK0MSi6G4WTdrZV74EZ.ijS7sN8Hvm0oYGmG2BTijuEoFGzTqqa', 'Female');	# Shang69!
	SET @j = Create_Player('Dell', 'Laptop', 'laptop@rose-hulman.edu', '$2y$10$SBeaBiom.I.jCokegd8x5eXKTyh79ABl7sm2N698WFbF/aRZkD94q', 'Female');	# female@rose
	SET @k = Create_Player('Up', 'Down', 'direction@rose-hulman.edu', '$2y$10$Xnr0IumiPdDtHT4hCwf1ZetbSbMGaFROFbdDBpoWk3fbLXyfrsVwC', 'Female');	# female@rose!2
	SET @l = Create_Player('Mike', 'Jagger', 'jagger@rose-hulman.edu', '$2y$10$quSKDGedI.VxWb.BfBFO6uxTUY0BcguoPBvPpc.7/5oNWkP5hg4J2', 'Male');	# malesgaloreYEA
	SET @m = Create_Player('Donald', 'Trump', 'thebiggest@rose-hulman.edu', '$2y$10$3TRFkuWDabeAWpWwwhPEK.nKkuR9zhLJBHZTrXCAoLZGqqtYAoJI6', 'Male');	# WeWillBuildAWall
    
END//

CREATE PROCEDURE `Add_Referee_Data`()
BEGIN

	SET @i = Create_Referee('Willy', 'Smith', 'bruh@rose-hulman.edu', null, 'Male');
    SET @j = Create_Referee('Ryan', 'Dinkleburg', 'dinklebuuuuuurg@rose-hulman.edu', null, 'Male');
    SET @k = Create_Referee('Sam', 'Flickinger', 'flick@rose-hulman.edu', null, 'Male');
    SET @l = Create_Referee('Chang', 'Wang', 'wutangclan@rose-hulman.edu', null, 'Male');
    SET @m = Create_Referee('Reeve', 'Vixon', 'vixon@rose-hulman.edu', null, 'Male');
    SET @n = Create_Referee('Alexander', 'Pikachu', 'charizard@rose-hulman.edu', null, 'Male');
    SET @o = Create_Referee('Hannah', 'Smith', 'smithyboi@rose-hulman.edu', null, 'Female');
    SET @p = Create_Referee('Laura', 'Hulman', 'rosehulman@rose-hulman.edu', null, 'Female');
    SET @q = Create_Referee('Lia', 'Apple', 'google@rose-hulman.edu', null, 'Female');	# already exists in Add_Person
    SET @a = Create_Referee('Rob', 'Coons', 'coons@rose-hulman.edu', '$2y$10$IZKGs6hC79oI0Y9SqaVN5.I6SN7esmTZZbPUesnNe.cODkqHiFNPi', 'Male');	# ManchesterUnitedRashford10
    SET @b = Create_Referee('Sriram', 'Mohan', 'mohan@rose-hulman.edu', '$2y$10$eUQSIsGv8NpIrIuPv2PfTexEKZgUwlbLYF/cDBNVlhBv6igzP3G/q', 'Male');	# roseIMStuff
    
END//

CREATE PROCEDURE `Add_Team_Data`()
BEGIN

	Set @a = Create_Team('Hit Squad', 17);
    Set @b = Create_Team('Layout Losers', 13);
    Set @c = Create_Team('Ultimate Team', 13);
    Set @d = Create_Team('AOII', 8);
    Set @e = Create_Team('Setting Ducks', 17);
    Set @f = Create_Team('BSBabes', 9);
    Set @g = Create_Team('Ball Busters', 9);
    Set @h = Create_Team('Big Baseball',1);
    Set @i = Create_Team('Easy Money',1);
    Set @j = Create_Team('Thisccccc',15);
    Set @k = Create_Team('Jump Score',15);
    Set @l = Create_Team('DSig', 8);
    Set @m = Create_Team('FIJI', 8);
    Set @n = Create_Team('BSB2', 9);

END//

CREATE PROCEDURE `Add_PlaysOn_Data`(IN input int)
BEGIN
	
    Set @x = -25;

	Set @a = Create_PlaysOn(1, 26+@x, 'Captain');
	Set @b = Create_PlaysOn(2, 27+@x, 'Captain');
    Set @c = Create_PlaysOn(3, 28+@x, 'Captain');
    Set @d = Create_PlaysOn(4, 29+@x, 'Captain');
    Set @e = Create_PlaysOn(5, 30+@x, 'Captain');
    Set @f = Create_PlaysOn(6, 31+@x, 'Captain');
    Set @g = Create_PlaysOn(7, 32+@x, 'Captain');
    Set @h = Create_PlaysOn(8, 33+@x, 'Captain');
    Set @i = Create_PlaysOn(18, 34+@x, 'Captain');
	Set @j = Create_PlaysOn(19, 35+@x, 'Captain');
    Set @k = Create_PlaysOn(20, 36+@x, 'Captain');
    Set @l = Create_PlaysOn(21, 37+@x, 'Captain');
    Set @m = Create_PlaysOn(22, 38+@x, 'Captain');
    Set @n = Create_PlaysOn(23, 39+@x, 'Captain');
    Set @o = Create_PlaysOn(24, 26+@x, 'Player');
    Set @p = Create_PlaysOn(25, 27+@x, 'Player');
    Set @q = Create_PlaysOn(26, 28+@x, 'Player');
    Set @r = Create_PlaysOn(27, 29+@x, 'Player');
    Set @s = Create_PlaysOn(28, 30+@x, 'Player');
    Set @t = Create_PlaysOn(29, 31+@x, 'Player');
    Set @u = Create_PlaysOn(30, 32+@x, 'Player');
    Set @v = Create_PlaysOn(31, 33+@x, 'Player');

END//

CREATE PROCEDURE `Get_Games_Ref`(IN mail varchar(50))
BEGIN
   SELECT game_ID, Team1 as Team1_ID, Team2 as Team2_ID, (SELECT location FROM Facility WHERE facility_ID = facility) AS Location, StartDateTime AS StartTime, (SELECT name FROM Team WHERE team_ID = Plays.team1) as Team1, (SELECT name FROM Team  WHERE team_ID = Plays.team2) AS Team2, home_Score as Team1Score, away_Score as Team2Score
    FROM Game
    JOIN Plays ON game = game_ID
    WHERE ref = (SELECT person_ID FROM Person WHERE email = mail);
END//

CREATE  PROCEDURE `get_permission`(IN mail varchar(50), OUT permission varchar(20))
BEGIN

SET @id = (SELECT person_ID FROM Person WHERE email = mail);

IF(@id IN (SELECT person_ID FROM Player))
THEN
SELECT 'Player' INTO permission;
END IF;

IF(@id IN (SELECT person_ID FROM Referee))
THEN
SELECT 'Referee' INTO permission;
END IF;

IF(@id IN (SELECT player FROM PlaysOn WHERE role = 'Captain'))
THEN
SELECT 'Captain' INTO permission;
END IF;

END//

CREATE PROCEDURE `Get_Roster`(IN teamID int)
BEGIN

SELECT Person.firstName as First, Person.lastName as Last, role as Role
FROM Person
JOIN PlaysOn ON player = person_ID
JOIN Team ON team_ID = team
WHERE team_ID = teamID;

END//

CREATE PROCEDURE `Get_Schedule`(IN teamid int)
BEGIN
    
    Select Plays.team1 as Team1_ID, Plays.team2 as Team2_ID,  (SELECT name FROM Team WHERE team_ID = Plays.team1) as Team1,  (SELECT name FROM Team WHERE team_ID = Plays.team2) as Team2, Game.StartDateTime as StartTime, Facility.location as  Location, home_Score as Team1Score, away_Score as Team2Score
    From Plays
		Join Game ON Plays.game = Game.game_ID
        Join Facility ON Facility.facility_ID = Game.facility
	Where Plays.team1 = teamid OR Plays.team2 = teamid;
    
END//

CREATE PROCEDURE `Get_Teams`(
IN mail varchar(50))
BEGIN

	SET @id = (SELECT person_ID FROM Person WHERE email = mail);
    
    Select league_ID, team_ID, sport as Sport, League.name as League, Team.name as Team, (SELECT rules FROM Sport WHERE name = sport) as Rules
    From League
    Join Team ON League.league_ID = Team.league
    Where Team.team_ID IN (Select team
							From PlaysOn
							Where player = @id);

END//

CREATE PROCEDURE `League_Standings`(IN leagueID INT)
BEGIN
SELECT name, wins, losses
FROM Team_Win_Percentage
WHERE league_ID = leagueID
ORDER BY WinPercentage DESC;

END//

CREATE PROCEDURE `Remove_League_Data`()
BEGIN
	DELETE FROM League;
END//

CREATE PROCEDURE `Remove_Sport_Data`()
BEGIN

	DELETE FROM Sport;

END//

CREATE TRIGGER Update_Record AFTER UPDATE ON Plays
FOR EACH ROW
BEGIN

SET @TeamID = NEW.game;
SET @Team1Score = NEW.home_Score;
SET @Team2Score = NEW.away_Score;

IF(OLD.home_Score IS NOT NULL OR OLD.away_Score IS NOT NULL)
THEN
IF(OLD.home_Score > OLD.away_Score)
THEN

IF(@Team1Score < @Team2Score)
THEN
UPDATE Team
SET losses = losses + 1,
wins = wins - 1
WHERE team_ID = OLD.team1;


UPDATE Team
SET wins = wins + 1,
losses = losses - 1
WHERE team_ID = OLD.team2;

END IF;

END IF;

IF(OLD.home_Score < OLD.away_Score)
THEN


IF(@Team1Score > @Team2Score)
THEN
UPDATE Team
SET losses = losses - 1,
wins = wins + 1
WHERE team_ID = OLD.team1;


UPDATE Team
SET wins = wins - 1,
losses = losses + 1
WHERE team_ID = OLD.team2;

END IF;


END IF;

ELSE


IF(@Team1Score > @Team2Score)
THEN
UPDATE Team
SET wins = wins + 1
WHERE team_ID = OLD.team1;

UPDATE Team
SET losses = losses + 1
WHERE team_ID = OLD.team2;

END IF;


IF(@Team2Score > @Team1Score)
THEN
UPDATE Team
SET wins = wins + 1
WHERE team_ID = OLD.team2;

UPDATE Team
SET losses = losses + 1
WHERE team_ID = OLD.team1;
END IF;

END IF;

END//

