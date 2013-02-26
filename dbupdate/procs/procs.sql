DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `gameScoresProc`$$
CREATE DEFINER=`shl`@`%` PROCEDURE `gameScoresProc`(PassedSeasonID int)
BEGIN

DELETE FROM QuickGameScores 
WHERE GameID IN (
  SELECT GameID
  FROM    Game
  WHERE   SeasonID = PassedSeasonID
);

INSERT INTO QuickGameScores 
SELECT  G.GameID AS GameID,
        SUM(IF((TP.Color = 1),1,0)) AS BlackPoints,
        SUM(IF((TP.Color = 2),1,0)) AS WhitePoints 
FROM Game AS G 
INNER JOIN TeamPlayer AS TP ON G.GameID = TP.GameID 
INNER JOIN Point AS P ON TP.TeamPlayerID = P.TeamPlayerID
WHERE P.PointType = 1 
AND   G.SeasonID = PassedSeasonID
GROUP by G.GameID;
  
  
  
END$$

DROP PROCEDURE IF EXISTS `gameTimeLengthProc`$$
CREATE DEFINER=`shl`@`%` PROCEDURE `gameTimeLengthProc`(PassedSeasonID int)
BEGIN
	DELETE FROM QuickGameTimeLength WHERE GameID IN (
  SELECT GameID
  FROM    Game
  WHERE   SeasonID = PassedSeasonID
);
  INSERT INTO QuickGameTimeLength
  SELECT Game.GameID AS GameID,time_to_sec(timediff(Game.GameEnd,Game.GameStart)) AS GameSeconds 
  FROM Game
  WHERE SeasonID = PassedSeasonID;

  
END$$

DROP PROCEDURE IF EXISTS `gameWinnersProc`$$
CREATE DEFINER=`shl`@`%` PROCEDURE `gameWinnersProc`(PassedSeasonID int)
BEGIN
  DELETE FROM QuickGameWinners WHERE GameID IN (
  SELECT GameID
  FROM    Game
  WHERE   SeasonID = PassedSeasonID
);
	INSERT INTO QuickGameWinners
  SELECT  QuickGameScores.GameID AS GameID,
          IF((QuickGameScores.BlackPoints > QuickGameScores.WhitePoints),1,2) AS Winner 
  FROM QuickGameScores 
  WHERE QuickGameScores.GameID IN (
  SELECT GameID
  FROM    Game
  WHERE   SeasonID = PassedSeasonID
)
  GROUP BY QuickGameScores.GameID;

END$$

DROP PROCEDURE IF EXISTS `gameWinningGoalsProc`$$
CREATE DEFINER=`shl`@`%` PROCEDURE `gameWinningGoalsProc`(PassedSeasonID int)
BEGIN
  DELETE FROM QuickGameWinningGoals WHERE GameID IN (
  SELECT GameID
  FROM    Game
  WHERE   SeasonID = PassedSeasonID
);
  INSERT INTO QuickGameWinningGoals
  SELECT  TP.PlayerID AS PlayerID,TP.TeamPlayerID AS TeamPlayerID,
          TP.GameID AS GameID, P.PointID AS PointID 
  FROM QuickGameWinners AS GW 
  INNER JOIN QuickTeamScores AS TS ON GW.GameID = TS.GameID and GW.Winner = TS.Color 
  INNER JOIN TeamPlayer AS TP ON GW.GameID = TP.GameID and GW.Winner = TP.Color
  INNER JOIN Point AS P ON TP.TeamPlayerID = P.TeamPlayerID and TS.Score = P.PointNum 
  WHERE P.PointType = 1
  AND   GW.GameID IN (
  SELECT  GameID
  FROM    Game
  WHERE   SeasonID = PassedSeasonID
);
	
END$$

DROP PROCEDURE IF EXISTS `getQuickPlayerData`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getQuickPlayerData`(	 
	  IN PlayerIDParm	int,
          IN PlayoffParm      int,
	  IN SeasonIDParm    int
	)
BEGIN

            SELECT  SUM(Goals) AS Goals,
                    SUM(Assists) AS Assists,
                    SUM(Wins) AS Wins,
                    SUM(Losses) AS Losses,
                    SUM(WinningGoals) AS WinningGoals
            FROM QuickPlayerData AS QPD
            WHERE 	QPD.PlayerID = PlayerIDParm
        		AND (
        			PlayoffParm = -1
        			OR
        			QPD.Playoff = PlayoffParm
        		)
        		AND (
        			SeasonIDParm = 0
        			OR
        			QPD.SeasonID = SeasonIDParm
        		)
        		
        GROUP BY QPD.PlayerID;
        		
        	
END$$

DROP PROCEDURE IF EXISTS `goalieDataProc`$$
CREATE DEFINER=`shl`@`%` PROCEDURE `goalieDataProc`(PassedSeasonID int)
BEGIN
  DELETE FROM QuickGoalieDataTable WHERE SeasonID = PassedSeasonID;
  INSERT INTO QuickGoalieDataTable
  SELECT GGAA.PlayerID AS PlayerID,
  GGAA.SeasonID AS SeasonID,
  GGAA.Playoff AS Playoff,
  GGAA.GoalsAgainst AS GoalsAgainst,
  PWLBP.Wins AS Wins,
  PWLBP.Losses AS Losses,
  COALESCE(PGA.Goals,0) AS Goals,
  COALESCE(PGA.Assists,0) AS Assists,
  COALESCE(GSO.ShutOutCount,0) AS ShutOutCount,
  GGT.TotalGameSeconds AS TotalGameSeconds 
  FROM QuickGoalieGAA AS GGAA 
  INNER JOIN  QuickPlayerWinsLossesByPosition AS PWLBP ON GGAA.PlayerID = PWLBP.PlayerID 
              AND GGAA.SeasonID = PWLBP.SeasonID 
              AND GGAA.Playoff = PWLBP.Playoff 
  INNER JOIN  QuickGoalieGameTime AS GGT ON GGAA.PlayerID = GGT.PlayerID 
              AND GGAA.SeasonID = GGT.SeasonID 
              AND GGAA.Playoff = GGT.Playoff 
  LEFT JOIN   QuickGoalieShutOut AS GSO ON GGAA.PlayerID = GSO.PlayerID 
              AND GGAA.SeasonID = GSO.SeasonID 
              AND GGAA.Playoff = GSO.Playoff 
  LEFT JOIN   QuickPlayerGoalsAssists AS PGA ON PWLBP.PlayerID = PGA.PlayerID 
              AND PWLBP.SeasonID = PGA.SeasonID 
              AND PWLBP.Playoff = PGA.Playoff 
              AND PWLBP.Position = PGA.Position
  WHERE PWLBP.Position = 1
  AND GGAA.SeasonID = PassedSeasonID;


END$$

DROP PROCEDURE IF EXISTS `goalieGAAProc`$$
CREATE DEFINER=`shl`@`%` PROCEDURE `goalieGAAProc`(PassedSeasonID int)
BEGIN
  DELETE FROM QuickGoalieGAA WHERE SeasonID = PassedSeasonID;
  
  INSERT INTO QuickGoalieGAA
  SELECT TP.PlayerID AS PlayerID,G.SeasonID AS SeasonID,
  G.Playoff AS Playoff, SUM(TS.Score) AS GoalsAgainst 
  FROM TeamPlayer AS TP 
  INNER JOIN QuickTeamScores AS TS ON TP.GameID = TS.GameID AND TP.Color <> TS.Color 
  INNER JOIN Game G ON TP.GameID = G.GameID 
  WHERE  TP.Position = 1
  AND G.SeasonID = PassedSeasonID
  GROUP BY TP.PlayerID,G.SeasonID,G.Playoff;

END$$

DROP PROCEDURE IF EXISTS `goalieGameTimeProc`$$
CREATE DEFINER=`shl`@`%` PROCEDURE `goalieGameTimeProc`(PassedSeasonID int)
BEGIN
	DELETE FROM QuickGoalieGameTime WHERE SeasonID = PassedSeasonID;
  
  INSERT INTO QuickGoalieGameTime
  SELECT  TP.PlayerID AS PlayerID,
          G.SeasonID AS SeasonID,
          G.Playoff AS Playoff,
          SUM(GTL.GameSeconds) AS TotalGameSeconds 
  FROM  TeamPlayer AS TP 
  INNER JOIN Game AS G ON TP.GameID = G.GameID 
  INNER JOIN QuickGameTimeLength AS GTL ON G.GameID = GTL.GameID 
  WHERE TP.Position = 1
  AND G.SeasonID = PassedSeasonID
  GROUP BY TP.PlayerID, G.SeasonID, G.Playoff;
  
  
  
END$$

DROP PROCEDURE IF EXISTS `goalieShutOutProc`$$
CREATE DEFINER=`shl`@`%` PROCEDURE `goalieShutOutProc`(PassedSeasonID int)
BEGIN
	DELETE FROM QuickGoalieShutOut WHERE SeasonID = PassedSeasonID;
  INSERT INTO QuickGoalieShutOut
  SELECT TP.PlayerID AS PlayerID, G.SeasonID AS SeasonID,
          G.Playoff AS Playoff, COUNT(G.GameID) AS ShutOutCount
  FROM TeamPlayer AS TP 
  INNER JOIN QuickGameScores AS GS ON TP.GameID = GS.GameID 
  INNER JOIN Game AS G ON TP.GameID = G.GameID 
  WHERE ((TP.Position = 1) 
  AND (((TP.Color = 2) 
  AND (GS.BlackPoints = 0)) 
  OR ((TP.Color = 1) 
  AND (GS.WhitePoints = 0)))) 
  AND G.SeasonID = PassedSeasonID
  GROUP BY TP.PlayerID,G.SeasonID,G.Playoff;
 
  
END$$

DROP PROCEDURE IF EXISTS `hookupSummaryProc`$$
CREATE DEFINER=`shl`@`%` PROCEDURE `hookupSummaryProc`(PassedSeasonID int)
BEGIN
  DELETE FROM QuickHookupSummary WHERE SeasonID = PassedSeasonID;
  
	INSERT INTO QuickHookupSummary
    SELECT GPH.SeasonID, GPH.Playoff, GPH.GoalPlayer, APH.GoalPlayer, 
            (GPH.HookupCount + APH.HookupCount) AS HookupCount
            
    FROM QuickPlayerHookup AS GPH
    INNER JOIN QuickPlayerHookup AS APH ON GPH.GoalPlayer = APH.AssistPlayer
    AND APH.GoalPlayer = GPH.AssistPlayer
    AND GPH.SeasonID = APH.SeasonID
    AND GPH.Playoff = APH.Playoff
    WHERE GPH.SeasonID = PassedSeasonID
    GROUP BY GPH.SeasonID, GPH.Playoff, GPH.GoalPlayer, APH.GoalPlayer;

END$$

DROP PROCEDURE IF EXISTS `masterUpdateProc`$$
CREATE DEFINER=`shl`@`%` PROCEDURE `masterUpdateProc`(PassedSeasonID int)
BEGIN
  /* FIRST LEVEL */
  CALL pointDetailProc(PassedSeasonID);
	CALL teamScoresProc(PassedSeasonID);
  CALL gameScoresProc(PassedSeasonID);
  CALL gameTimeLengthProc(PassedSeasonID);
  CALL playerGoalsAssistsProc(PassedSeasonID);
  
  /* SECOND LEVEL */
  CALL playerHookupProc(PassedSeasonID);
  CALL goalieGAAProc(PassedSeasonID);
  CALL gameWinnersProc(PassedSeasonID);
  CALL goalieGameTimeProc(PassedSeasonID);
  CALL goalieShutOutProc(PassedSeasonID);
  CALL playerTeamScoresProc(PassedSeasonID);
  
  /* THIRD LEVEL */
  CALL hookupSummaryProc(PassedSeasonID);
  CALL gameWinningGoalsProc(PassedSeasonID);
  CALL playerGameResultProc(PassedSeasonID);
  CALL playerWinsLossesByPositionProc(PassedSeasonID);
  
  /* FOURTH LEVEL */
  CALL playerGameWinningGoalsProc(PassedSeasonID);
  
  /* FIFTH LEVEL */
  CALL goalieDataProc(PassedSeasonID);
  CALL playerDataProc(PassedSeasonID);
END$$

DROP PROCEDURE IF EXISTS `playerDataProc`$$
CREATE DEFINER=`shl`@`%` PROCEDURE `playerDataProc`(PassedSeasonID int)
BEGIN
  DELETE FROM QuickPlayerDataTable WHERE SeasonID = PassedSeasonID;
  INSERT INTO QuickPlayerDataTable
	SELECT PGA.SeasonID AS SeasonID,
  PGA.Playoff AS Playoff,
  PGA.PlayerID AS PlayerID,
  PGA.Goals AS Goals,
  PGA.Assists AS Assists,
  PWLBP.Wins AS Wins,
  PWLBP.Losses AS Losses,
  COALESCE(PGWG.WinningGoals,0) AS WinningGoals,
  QPTS.TeamScore AS TeamScores
  FROM QuickPlayerGoalsAssists AS PGA 
  INNER JOIN  QuickPlayerWinsLossesByPosition AS PWLBP ON PGA.PlayerID = PWLBP.PlayerID
              AND PGA.SeasonID = PWLBP.SeasonID 
              AND PGA.Playoff = PWLBP.Playoff 
              AND PGA.Position = PWLBP.Position 
  INNER JOIN QuickPlayerTeamScores AS QPTS ON PGA.PlayerID = QPTS.PlayerID
              AND PGA.SeasonID = QPTS.SeasonID 
              AND PGA.Playoff = QPTS.Playoff 
  LEFT JOIN   QuickPlayerGameWinningGoals AS PGWG ON PGA.PlayerID = PGWG.PlayerID 
              AND PGA.SeasonID = PGWG.SeasonID 
              AND PGA.Playoff = PGWG.Playoff 
  WHERE PGA.Position = 2
  AND PGA.SeasonID = PassedSeasonID;

END$$

DROP PROCEDURE IF EXISTS `playerGameResultProc`$$
CREATE DEFINER=`shl`@`%` PROCEDURE `playerGameResultProc`(PassedSeasonID int)
BEGIN
  DELETE FROM QuickPlayerGameResult WHERE SeasonID = PassedSeasonID;
  INSERT INTO QuickPlayerGameResult
  SELECT TP.PlayerID AS PlayerID,G.SeasonID AS SeasonID,
  G.Playoff AS Playoff,
  G.GameNum AS GameNum,
  TP.Position AS Position,
  (CASE COALESCE(GW.Winner,0) WHEN 0 THEN 'L' ELSE 'W' END) AS GameResult 
  FROM TeamPlayer AS TP 
  INNER JOIN Game AS G ON TP.GameID = G.GameID
  LEFT JOIN QuickGameWinners AS GW ON TP.Color = GW.Winner AND TP.GameID = GW.GameID
  WHERE G.SeasonID = PassedSeasonID
  ORDER BY TP.PlayerID,G.SeasonID,G.Playoff,G.GameNum;

	
END$$

DROP PROCEDURE IF EXISTS `playerGameWinningGoalsProc`$$
CREATE DEFINER=`shl`@`%` PROCEDURE `playerGameWinningGoalsProc`(PassedSeasonID int)
BEGIN
	DELETE FROM QuickPlayerGameWinningGoals WHERE SeasonID = PassedSeasonID;
  INSERT INTO QuickPlayerGameWinningGoals
  SELECT  G.SeasonID AS SeasonID, G.Playoff AS Playoff,
          GWG.PlayerID AS PlayerID, COUNT(GWG.PointID) AS WinningGoals 
  FROM QuickGameWinningGoals AS GWG 
  JOIN Game AS G ON GWG.GameID = G.GameID 
  WHERE G.SeasonID = PassedSeasonID
  GROUP BY G.SeasonID, G.Playoff, GWG.PlayerID;
 
  
  
END$$

DROP PROCEDURE IF EXISTS `playerGoalsAssistsProc`$$
CREATE DEFINER=`shl`@`%` PROCEDURE `playerGoalsAssistsProc`(PassedSeasonID int)
BEGIN
  DELETE FROM QuickPlayerGoalsAssists WHERE SeasonID = PassedSeasonID;
  INSERT INTO QuickPlayerGoalsAssists
  SELECT g.SeasonID AS SeasonID,g.Playoff AS Playoff,
  p.PlayerID AS PlayerID,tp.Position AS Position,
  SUM((CASE pt.PointType WHEN 1 THEN 1 ELSE 0 END)) AS Goals,
  SUM((CASE pt.PointType WHEN 2 THEN 1 ELSE 0 END)) AS Assists 
  FROM Player AS p 
  INNER JOIN TeamPlayer AS tp ON p.PlayerID = tp.PlayerID 
  INNER JOIN Point AS pt ON tp.TeamPlayerID = pt.TeamPlayerID 
  INNER JOIN Game AS g ON tp.GameID = g.GameID
  WHERE g.SeasonID = PassedSeasonID
  GROUP BY g.SeasonID,g.Playoff,p.PlayerID,tp.Position;
END$$

DROP PROCEDURE IF EXISTS `playerHookupProc`$$
CREATE DEFINER=`shl`@`%` PROCEDURE `playerHookupProc`(PassedSeasonID int)
BEGIN
  DELETE FROM QuickPlayerHookup WHERE SeasonID = PassedSeasonID;
  
	INSERT INTO QuickPlayerHookup
  SELECT SeasonID, Playoff, GoalPlayerID, AssistPlayerID, COUNT(GoalPlayerID) AS Hookups 
  FROM SHL.QuickPointDetail
  WHERE AssistPlayerID > 0
  AND SeasonID = PassedSeasonID
  GROUP BY SeasonID, Playoff, GoalPlayerID, AssistPlayerID;

END$$

DROP PROCEDURE IF EXISTS `playerTeamScoresProc`$$
CREATE DEFINER=`shl`@`%` PROCEDURE `playerTeamScoresProc`(PassedSeasonID int)
BEGIN
  DELETE FROM QuickPlayerTeamScores WHERE SeasonID = PassedSeasonID;
	INSERT INTO QuickPlayerTeamScores
  SELECT SeasonID, Playoff, PlayerID, TeamScore
  FROM (
        SELECT TP.PlayerID, G.SeasonID, G.Playoff, SUM(QTS.Score) AS TeamScore
        FROM TeamPlayer AS TP
        INNER JOIN Game AS G ON TP.GameID = G.GameID
        INNER JOIN QuickTeamScores AS QTS ON G.GameID = QTS.GameID AND TP.Color = QTS.Color
        WHERE TP.Position = 2
        AND G.SeasonId = PassedSeasonID
        GROUP BY TP.PlayerID, G.SeasonId, G.Playoff
        ) AS PlayerTeamScores;

END$$

DROP PROCEDURE IF EXISTS `playerWinsLossesByPositionProc`$$
CREATE DEFINER=`shl`@`%` PROCEDURE `playerWinsLossesByPositionProc`(PassedSeasonID int)
BEGIN
	DELETE FROM QuickPlayerWinsLossesByPosition WHERE SeasonID = PassedSeasonID;
  INSERT INTO QuickPlayerWinsLossesByPosition
  SELECT P.PlayerID AS PlayerID,G.SeasonID AS SeasonID,
  G.Playoff AS Playoff,TP.Position AS Position,
  SUM(IF((TP.Color = GW.Winner),1,0)) AS Wins,
  SUM(IF((TP.Color <> GW.Winner),1,0)) AS Losses 
  FROM Player AS P 
  INNER JOIN TeamPlayer AS TP ON P.PlayerID = TP.PlayerID 
  INNER JOIN QuickGameWinners AS GW ON TP.GameID = GW.GameID
  INNER JOIN Game AS G ON TP.GameID = G.GameID
  WHERE G.SeasonID = PassedSeasonID
  GROUP BY P.PlayerID, G.SeasonID, G.Playoff, TP.Position;
 
  
END$$

DROP PROCEDURE IF EXISTS `pointDetailProc`$$
CREATE DEFINER=`shl`@`%` PROCEDURE `pointDetailProc`(PassedSeasonID int)
BEGIN
  DELETE FROM QuickPointDetail WHERE GameID IN (
  SELECT GameID
  FROM    Game
  WHERE   SeasonID = PassedSeasonID
);
	INSERT INTO QuickPointDetail
  SELECT Goal, Assist, GameID, SeasonID, Playoff, Color
  FROM (
    SELECT  g.SeasonID, g.Playoff, g.GameID, tp.Color, p.PointNum,
            MAX(IF(p.PointType = 1, tp.PlayerID, NULL)) AS Goal,
            MAX(IF(p.PointType = 2, tp.PlayerID, NULL)) AS Assist
    FROM    Point AS p
    INNER JOIN  TeamPlayer AS tp ON p.TeamPlayerID = tp.TeamPlayerID
    INNER JOIN Game AS g ON tp.GameID = g.GameID
    WHERE g.SeasonID = PassedSeasonID
    GROUP BY g.SeasonID, g.Playoff, g.GameID, tp.Color, p.PointNum
   ) AS PointSelect;

END$$

DROP PROCEDURE IF EXISTS `simpleproc`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `simpleproc`()
BEGIN
SELECT g.SeasonID, g.Playoff, p.PlayerID, tp.Position,
            SUM(CASE pt.PointType WHEN 1 THEN 1 ELSE 0 END) AS Goals,
            SUM(CASE pt.PointType WHEN 2 THEN 1 ELSE 0 END) AS Assists
            FROM Player AS p
            INNER JOIN TeamPlayer AS tp ON p.PlayerID = tp.PlayerID
            INNER JOIN Point AS pt ON tp.TeamPlayerID = pt.TeamPlayerID
            INNER JOIN Game AS g ON tp.GameID = g.GameID
GROUP BY g.SeasonID, g.Playoff, p.PlayerID, tp.Position;
END$$

DROP PROCEDURE IF EXISTS `teamScoresProc`$$
CREATE DEFINER=`shl`@`%` PROCEDURE `teamScoresProc`(PassedSeasonID int)
BEGIN
	DELETE FROM QuickTeamScores WHERE GameID IN (
  SELECT GameID
  FROM    Game
  WHERE   SeasonID = PassedSeasonID
);
  INSERT INTO QuickTeamScores
  SELECT g.GameID AS GameID,tp.Color AS Color,COUNT(p.PointID) AS Score 
  FROM Game AS g 
  JOIN TeamPlayer AS tp ON g.GameID = tp.GameID 
  JOIN Point AS p ON tp.TeamPlayerID = p.TeamPlayerID AND p.PointType = 1
  WHERE g.GameID IN (
    SELECT GameID
    FROM    Game
    WHERE   SeasonID = PassedSeasonID
  )
  GROUP BY g.GameID, tp.Color;
  
END$$

DROP PROCEDURE IF EXISTS `testProc`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `testProc`()
BEGIN

SELECT g.SeasonID, g.Playoff, p.PlayerID, tp.Position,
            SUM(CASE pt.PointType WHEN 1 THEN 1 ELSE 0 END) AS Goals,
            SUM(CASE pt.PointType WHEN 2 THEN 1 ELSE 0 END) AS Assists
            FROM Player AS p
            INNER JOIN TeamPlayer AS tp ON p.PlayerID = tp.PlayerID
            INNER JOIN Point AS pt ON tp.TeamPlayerID = pt.TeamPlayerID
            INNER JOIN Game AS g ON tp.GameID = g.GameID
GROUP BY g.SeasonID, g.Playoff, p.PlayerID, tp.Position;



SELECT g.GameID, tp.Color, COUNT(p.PointID) AS Score 
FROM Game AS g
INNER JOIN TeamPlayer AS tp ON g.GameID = tp.GameID
INNER JOIN Point AS p ON tp.TeamPlayerID = p.TeamPlayerID
AND p.PointType = 1
GROUP BY g.GameID, tp.Color;	
END$$

DELIMITER ;
