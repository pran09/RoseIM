DELIMITER //
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
DELIMITER ;