CREATE TABLE `DbVersion` (
  `DbVersionId` int(11) NOT NULL AUTO_INCREMENT,
  `Major` int(11) NOT NULL,
  `Minor` int(11) NOT NULL,
  `Build` int(11) NOT NULL,
  `VersionDate` datetime NOT NULL,
  PRIMARY KEY (`DbVersionId`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 24, 2013 at 10:07 PM
-- Server version: 5.1.41
-- PHP Version: 5.3.2-1ubuntu4.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `SHL`
--

-- --------------------------------------------------------

--
-- Table structure for table `About`
--

DROP TABLE IF EXISTS `About`;
CREATE TABLE IF NOT EXISTS `About` (
  `AboutID` int(11) NOT NULL AUTO_INCREMENT,
  `DateCreated` datetime NOT NULL,
  `Poster` int(11) NOT NULL,
  `Content` text NOT NULL,
  PRIMARY KEY (`AboutID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `Game`
--

DROP TABLE IF EXISTS `Game`;
CREATE TABLE IF NOT EXISTS `Game` (
  `GameDate` date DEFAULT NULL,
  `GameEnd` time DEFAULT NULL,
  `GameID` int(11) NOT NULL AUTO_INCREMENT,
  `GameNum` int(11) NOT NULL,
  `GameStart` time DEFAULT NULL,
  `Playoff` tinyint(1) NOT NULL,
  `SeasonID` int(11) NOT NULL,
  PRIMARY KEY (`GameID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Stand-in structure for view `GameScores`
--
DROP VIEW IF EXISTS `GameScores`;
CREATE TABLE IF NOT EXISTS `GameScores` (
`GameID` int(11)
,`BlackPoints` decimal(23,0)
,`WhitePoints` decimal(23,0)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `GameTimeLength`
--
DROP VIEW IF EXISTS `GameTimeLength`;
CREATE TABLE IF NOT EXISTS `GameTimeLength` (
`GameID` int(11)
,`GameSeconds` bigint(10)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `GameWinners`
--
DROP VIEW IF EXISTS `GameWinners`;
CREATE TABLE IF NOT EXISTS `GameWinners` (
`GameID` int(11)
,`Winner` int(1)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `GameWinningGoals`
--
DROP VIEW IF EXISTS `GameWinningGoals`;
CREATE TABLE IF NOT EXISTS `GameWinningGoals` (
`PlayerID` int(11)
,`TeamPlayerID` int(11)
,`GameID` int(11)
,`PointID` int(11)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `GoalieGAA`
--
DROP VIEW IF EXISTS `GoalieGAA`;
CREATE TABLE IF NOT EXISTS `GoalieGAA` (
`PlayerID` int(11)
,`SeasonID` int(11)
,`Playoff` tinyint(1)
,`GoalsAgainst` decimal(41,0)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `GoalieGameTime`
--
DROP VIEW IF EXISTS `GoalieGameTime`;
CREATE TABLE IF NOT EXISTS `GoalieGameTime` (
`PlayerID` int(11)
,`SeasonID` int(11)
,`Playoff` tinyint(1)
,`TotalGameSeconds` decimal(41,0)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `GoalieShutOut`
--
DROP VIEW IF EXISTS `GoalieShutOut`;
CREATE TABLE IF NOT EXISTS `GoalieShutOut` (
`PlayerID` int(11)
,`SeasonID` int(11)
,`Playoff` tinyint(1)
,`ShutOutCount` bigint(21)
);
-- --------------------------------------------------------

--
-- Table structure for table `IDType`
--

DROP TABLE IF EXISTS `IDType`;
CREATE TABLE IF NOT EXISTS `IDType` (
  `IDTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(500) NOT NULL,
  `URL` varchar(500) NOT NULL,
  PRIMARY KEY (`IDTypeID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `PageSecurity`
--

DROP TABLE IF EXISTS `PageSecurity`;
CREATE TABLE IF NOT EXISTS `PageSecurity` (
  `PageSecurityID` int(11) NOT NULL AUTO_INCREMENT,
  `Page` varchar(500) NOT NULL,
  `SecurityLevel` int(1) NOT NULL DEFAULT '1',
  `PlayerID` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`PageSecurityID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `Player`
--

DROP TABLE IF EXISTS `Player`;
CREATE TABLE IF NOT EXISTS `Player` (
  `Access` int(11) NOT NULL DEFAULT '0',
  `Email` varchar(200) DEFAULT NULL,
  `FName` varchar(200) NOT NULL,
  `LName` varchar(200) NOT NULL,
  `Password` varchar(200) NOT NULL,
  `PhoneNumb` varchar(13) DEFAULT NULL,
  `PlayerID` int(11) NOT NULL AUTO_INCREMENT,
  `UserName` varchar(200) NOT NULL,
  `FacebookAccess` varchar(500) NOT NULL DEFAULT '',
  `FacebookID` varchar(500) NOT NULL DEFAULT '',
  `Height` varchar(500) NOT NULL DEFAULT '',
  `Shoots` varchar(500) NOT NULL DEFAULT '',
  `FavPro` varchar(500) NOT NULL DEFAULT '',
  `FavProTeam` varchar(500) NOT NULL DEFAULT '',
  `PlayerPicture` varchar(500) NOT NULL,
  PRIMARY KEY (`PlayerID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Stand-in structure for view `PlayerGameResult`
--
DROP VIEW IF EXISTS `PlayerGameResult`;
CREATE TABLE IF NOT EXISTS `PlayerGameResult` (
`PlayerID` int(11)
,`SeasonID` int(11)
,`Playoff` tinyint(1)
,`GameNum` int(11)
,`Position` int(11)
,`GameResult` varchar(1)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `PlayerGameWinningGoals`
--
DROP VIEW IF EXISTS `PlayerGameWinningGoals`;
CREATE TABLE IF NOT EXISTS `PlayerGameWinningGoals` (
`SeasonID` int(11)
,`Playoff` tinyint(1)
,`PlayerID` int(11)
,`WinningGoals` bigint(21)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `PlayerGoalsAssists`
--
DROP VIEW IF EXISTS `PlayerGoalsAssists`;
CREATE TABLE IF NOT EXISTS `PlayerGoalsAssists` (
`SeasonID` int(11)
,`Playoff` tinyint(1)
,`PlayerID` int(11)
,`Position` int(11)
,`Goals` decimal(23,0)
,`Assists` decimal(23,0)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `PlayerWinsLossesByPosition`
--
DROP VIEW IF EXISTS `PlayerWinsLossesByPosition`;
CREATE TABLE IF NOT EXISTS `PlayerWinsLossesByPosition` (
`PlayerID` int(11)
,`SeasonID` int(11)
,`Playoff` tinyint(1)
,`Position` int(11)
,`Wins` decimal(23,0)
,`Losses` decimal(23,0)
);
-- --------------------------------------------------------

--
-- Table structure for table `Point`
--

DROP TABLE IF EXISTS `Point`;
CREATE TABLE IF NOT EXISTS `Point` (
  `PointID` int(11) NOT NULL AUTO_INCREMENT,
  `PointNum` int(11) NOT NULL,
  `PointType` int(11) NOT NULL,
  `TeamPlayerID` int(11) NOT NULL,
  PRIMARY KEY (`PointID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `Post`
--

DROP TABLE IF EXISTS `Post`;
CREATE TABLE IF NOT EXISTS `Post` (
  `PostID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(500) NOT NULL,
  `DateCreated` datetime NOT NULL,
  `Poster` int(11) NOT NULL,
  `Content` text NOT NULL,
  PRIMARY KEY (`PostID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `QuickGameScores`
--

DROP TABLE IF EXISTS `QuickGameScores`;
CREATE TABLE IF NOT EXISTS `QuickGameScores` (
  `GameID` int(11) DEFAULT NULL,
  `BlackPoints` int(11) DEFAULT NULL,
  `WhitePoints` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `QuickGameTimeLength`
--

DROP TABLE IF EXISTS `QuickGameTimeLength`;
CREATE TABLE IF NOT EXISTS `QuickGameTimeLength` (
  `GameID` int(11) DEFAULT NULL,
  `GameSeconds` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `QuickGameWinners`
--

DROP TABLE IF EXISTS `QuickGameWinners`;
CREATE TABLE IF NOT EXISTS `QuickGameWinners` (
  `GameID` int(11) DEFAULT NULL,
  `Winner` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `QuickGameWinningGoals`
--

DROP TABLE IF EXISTS `QuickGameWinningGoals`;
CREATE TABLE IF NOT EXISTS `QuickGameWinningGoals` (
  `PlayerID` int(11) DEFAULT NULL,
  `TeamPlayerID` int(11) DEFAULT NULL,
  `GameID` int(11) DEFAULT NULL,
  `PointID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Stand-in structure for view `QuickGoalieData`
--
DROP VIEW IF EXISTS `QuickGoalieData`;
CREATE TABLE IF NOT EXISTS `QuickGoalieData` (
`PlayerID` int(11)
,`SeasonID` int(11)
,`Playoff` tinyint(1)
,`GoalsAgainst` decimal(41,0)
,`Wins` decimal(23,0)
,`Losses` decimal(23,0)
,`Goals` decimal(23,0)
,`Assists` decimal(23,0)
,`ShutOutCount` bigint(20)
,`TotalGameSeconds` decimal(41,0)
);
-- --------------------------------------------------------

--
-- Table structure for table `QuickGoalieDataTable`
--

DROP TABLE IF EXISTS `QuickGoalieDataTable`;
CREATE TABLE IF NOT EXISTS `QuickGoalieDataTable` (
  `PlayerID` int(11) NOT NULL,
  `SeasonID` int(11) NOT NULL,
  `Playoff` int(11) NOT NULL,
  `GoalsAgainst` int(11) NOT NULL,
  `Wins` int(11) NOT NULL,
  `Losses` int(11) NOT NULL,
  `Goals` int(11) NOT NULL,
  `Assists` int(11) NOT NULL,
  `ShutOutCount` int(11) NOT NULL,
  `TotalGameSeconds` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `QuickGoalieGAA`
--

DROP TABLE IF EXISTS `QuickGoalieGAA`;
CREATE TABLE IF NOT EXISTS `QuickGoalieGAA` (
  `PlayerID` int(11) DEFAULT NULL,
  `SeasonID` int(11) DEFAULT NULL,
  `Playoff` tinyint(4) DEFAULT NULL,
  `GoalsAgainst` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `QuickGoalieGameTime`
--

DROP TABLE IF EXISTS `QuickGoalieGameTime`;
CREATE TABLE IF NOT EXISTS `QuickGoalieGameTime` (
  `PlayerID` int(11) DEFAULT NULL,
  `SeasonID` int(11) DEFAULT NULL,
  `Playoff` tinyint(4) DEFAULT NULL,
  `TotalGameSeconds` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `QuickGoalieShutOut`
--

DROP TABLE IF EXISTS `QuickGoalieShutOut`;
CREATE TABLE IF NOT EXISTS `QuickGoalieShutOut` (
  `PlayerID` int(11) DEFAULT NULL,
  `SeasonID` int(11) DEFAULT NULL,
  `Playoff` tinyint(4) DEFAULT NULL,
  `ShutOutCount` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `QuickHookupSummary`
--

DROP TABLE IF EXISTS `QuickHookupSummary`;
CREATE TABLE IF NOT EXISTS `QuickHookupSummary` (
  `SeasonID` int(11) DEFAULT NULL,
  `Playoff` tinyint(4) DEFAULT NULL,
  `FirstPlayerID` int(11) DEFAULT NULL,
  `SecondPlayerID` int(11) DEFAULT NULL,
  `Hookup` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Stand-in structure for view `QuickPlayerData`
--
DROP VIEW IF EXISTS `QuickPlayerData`;
CREATE TABLE IF NOT EXISTS `QuickPlayerData` (
`SeasonID` int(11)
,`Playoff` tinyint(1)
,`PlayerID` int(11)
,`Goals` decimal(23,0)
,`Assists` decimal(23,0)
,`Wins` decimal(23,0)
,`Losses` decimal(23,0)
,`WinningGoals` bigint(20)
);
-- --------------------------------------------------------

--
-- Table structure for table `QuickPlayerDataTable`
--

DROP TABLE IF EXISTS `QuickPlayerDataTable`;
CREATE TABLE IF NOT EXISTS `QuickPlayerDataTable` (
  `SeasonID` int(11) NOT NULL,
  `Playoff` tinyint(1) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `Goals` int(11) DEFAULT NULL,
  `Assists` int(11) DEFAULT NULL,
  `Wins` int(11) DEFAULT NULL,
  `Losses` int(11) DEFAULT NULL,
  `WinningGoals` int(11) DEFAULT NULL,
  `TeamScores` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `QuickPlayerGameResult`
--

DROP TABLE IF EXISTS `QuickPlayerGameResult`;
CREATE TABLE IF NOT EXISTS `QuickPlayerGameResult` (
  `PlayerID` int(11) DEFAULT NULL,
  `SeasonID` int(11) DEFAULT NULL,
  `Playoff` tinyint(4) DEFAULT NULL,
  `GameNum` int(11) DEFAULT NULL,
  `Position` int(11) DEFAULT NULL,
  `GameResult` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `QuickPlayerGameWinningGoals`
--

DROP TABLE IF EXISTS `QuickPlayerGameWinningGoals`;
CREATE TABLE IF NOT EXISTS `QuickPlayerGameWinningGoals` (
  `SeasonID` int(11) DEFAULT NULL,
  `Playoff` tinyint(4) DEFAULT NULL,
  `PlayerID` int(11) DEFAULT NULL,
  `WinningGoals` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `QuickPlayerGoalsAssists`
--

DROP TABLE IF EXISTS `QuickPlayerGoalsAssists`;
CREATE TABLE IF NOT EXISTS `QuickPlayerGoalsAssists` (
  `SeasonID` int(11) DEFAULT NULL,
  `Playoff` tinyint(4) DEFAULT NULL,
  `PlayerID` int(11) DEFAULT NULL,
  `Position` int(11) DEFAULT NULL,
  `Goals` int(11) DEFAULT NULL,
  `Assists` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `QuickPlayerHookup`
--

DROP TABLE IF EXISTS `QuickPlayerHookup`;
CREATE TABLE IF NOT EXISTS `QuickPlayerHookup` (
  `SeasonID` int(11) DEFAULT NULL,
  `Playoff` tinyint(4) DEFAULT NULL,
  `GoalPlayer` int(11) DEFAULT NULL,
  `AssistPlayer` int(11) DEFAULT NULL,
  `HookupCount` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `QuickPlayerTeamScores`
--

DROP TABLE IF EXISTS `QuickPlayerTeamScores`;
CREATE TABLE IF NOT EXISTS `QuickPlayerTeamScores` (
  `SeasonID` int(11) DEFAULT NULL,
  `Playoff` tinyint(4) DEFAULT NULL,
  `PlayerID` int(11) DEFAULT NULL,
  `TeamScore` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `QuickPlayerWinsLossesByPosition`
--

DROP TABLE IF EXISTS `QuickPlayerWinsLossesByPosition`;
CREATE TABLE IF NOT EXISTS `QuickPlayerWinsLossesByPosition` (
  `PlayerID` int(11) DEFAULT NULL,
  `SeasonID` int(11) DEFAULT NULL,
  `Playoff` tinyint(4) DEFAULT NULL,
  `Position` int(11) DEFAULT NULL,
  `Wins` int(11) DEFAULT NULL,
  `Losses` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `QuickPointDetail`
--

DROP TABLE IF EXISTS `QuickPointDetail`;
CREATE TABLE IF NOT EXISTS `QuickPointDetail` (
  `GoalPlayerID` int(11) NOT NULL,
  `AssistPlayerID` int(11) DEFAULT NULL,
  `GameID` int(11) NOT NULL,
  `SeasonID` int(11) NOT NULL,
  `Playoff` tinyint(4) NOT NULL,
  `Color` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `QuickTeamScores`
--

DROP TABLE IF EXISTS `QuickTeamScores`;
CREATE TABLE IF NOT EXISTS `QuickTeamScores` (
  `GameID` int(11) DEFAULT NULL,
  `Color` int(11) DEFAULT NULL,
  `Score` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Record`
--

DROP TABLE IF EXISTS `Record`;
CREATE TABLE IF NOT EXISTS `Record` (
  `RecordID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(500) NOT NULL,
  PRIMARY KEY (`RecordID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `RecordHolder`
--

DROP TABLE IF EXISTS `RecordHolder`;
CREATE TABLE IF NOT EXISTS `RecordHolder` (
  `RecordHolderID` int(11) NOT NULL AUTO_INCREMENT,
  `Value` varchar(500) NOT NULL,
  `RecordID` int(11) NOT NULL,
  PRIMARY KEY (`RecordHolderID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `RecordHolderID`
--

DROP TABLE IF EXISTS `RecordHolderID`;
CREATE TABLE IF NOT EXISTS `RecordHolderID` (
  `RecordHolderIDID` int(11) NOT NULL AUTO_INCREMENT,
  `RepresentativeID` int(11) NOT NULL,
  `IDTypeID` int(11) NOT NULL,
  `RecordHolderID` int(11) NOT NULL,
  PRIMARY KEY (`RecordHolderIDID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `Request`
--

DROP TABLE IF EXISTS `Request`;
CREATE TABLE IF NOT EXISTS `Request` (
  `RequestID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(500) DEFAULT NULL,
  `DateCreated` date DEFAULT NULL,
  `Content` text NOT NULL,
  `Priority` int(11) NOT NULL,
  `Status` int(11) NOT NULL,
  `Poster` int(11) NOT NULL,
  PRIMARY KEY (`RequestID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `TeamPlayer`
--

DROP TABLE IF EXISTS `TeamPlayer`;
CREATE TABLE IF NOT EXISTS `TeamPlayer` (
  `Color` int(11) NOT NULL,
  `GameID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `Position` int(11) NOT NULL,
  `TeamPlayerID` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`TeamPlayerID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Stand-in structure for view `TeamScores`
--
DROP VIEW IF EXISTS `TeamScores`;
CREATE TABLE IF NOT EXISTS `TeamScores` (
`GameID` int(11)
,`Color` int(11)
,`Score` bigint(21)
);
-- --------------------------------------------------------

--
-- Structure for view `GameScores`
--
DROP TABLE IF EXISTS `GameScores`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `GameScores` AS select `G`.`GameID` AS `GameID`,sum(if((`TP`.`Color` = 1),1,0)) AS `BlackPoints`,sum(if((`TP`.`Color` = 2),1,0)) AS `WhitePoints` from ((`Game` `G` join `TeamPlayer` `TP` on((`G`.`GameID` = `TP`.`GameID`))) join `Point` `P` on((`TP`.`TeamPlayerID` = `P`.`TeamPlayerID`))) where (`P`.`PointType` = 1) group by `G`.`GameID`;

-- --------------------------------------------------------

--
-- Structure for view `GameTimeLength`
--
DROP TABLE IF EXISTS `GameTimeLength`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `GameTimeLength` AS select `Game`.`GameID` AS `GameID`,time_to_sec(timediff(`Game`.`GameEnd`,`Game`.`GameStart`)) AS `GameSeconds` from `Game`;

-- --------------------------------------------------------

--
-- Structure for view `GameWinners`
--
DROP TABLE IF EXISTS `GameWinners`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `GameWinners` AS select `GameScores`.`GameID` AS `GameID`,if((`GameScores`.`BlackPoints` > `GameScores`.`WhitePoints`),1,2) AS `Winner` from `GameScores` group by `GameScores`.`GameID`;

-- --------------------------------------------------------

--
-- Structure for view `GameWinningGoals`
--
DROP TABLE IF EXISTS `GameWinningGoals`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `GameWinningGoals` AS select `TP`.`PlayerID` AS `PlayerID`,`TP`.`TeamPlayerID` AS `TeamPlayerID`,`TP`.`GameID` AS `GameID`,`P`.`PointID` AS `PointID` from (((`GameWinners` `GW` join `TeamScores` `TS` on(((`GW`.`GameID` = `TS`.`GameID`) and (`GW`.`Winner` = `TS`.`Color`)))) join `TeamPlayer` `TP` on(((`GW`.`GameID` = `TP`.`GameID`) and (`GW`.`Winner` = `TP`.`Color`)))) join `Point` `P` on(((`TP`.`TeamPlayerID` = `P`.`TeamPlayerID`) and (`TS`.`Score` = `P`.`PointNum`)))) where (`P`.`PointType` = 1);

-- --------------------------------------------------------

--
-- Structure for view `GoalieGAA`
--
DROP TABLE IF EXISTS `GoalieGAA`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `GoalieGAA` AS select `TP`.`PlayerID` AS `PlayerID`,`G`.`SeasonID` AS `SeasonID`,`G`.`Playoff` AS `Playoff`,sum(`TS`.`Score`) AS `GoalsAgainst` from ((`TeamPlayer` `TP` join `TeamScores` `TS` on(((`TP`.`GameID` = `TS`.`GameID`) and (`TP`.`Color` <> `TS`.`Color`)))) join `Game` `G` on((`TP`.`GameID` = `G`.`GameID`))) where (`TP`.`Position` = 1) group by `TP`.`PlayerID`,`G`.`SeasonID`,`G`.`Playoff`;

-- --------------------------------------------------------

--
-- Structure for view `GoalieGameTime`
--
DROP TABLE IF EXISTS `GoalieGameTime`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `GoalieGameTime` AS select `TP`.`PlayerID` AS `PlayerID`,`G`.`SeasonID` AS `SeasonID`,`G`.`Playoff` AS `Playoff`,sum(`GTL`.`GameSeconds`) AS `TotalGameSeconds` from ((`TeamPlayer` `TP` join `Game` `G` on((`TP`.`GameID` = `G`.`GameID`))) join `GameTimeLength` `GTL` on((`G`.`GameID` = `GTL`.`GameID`))) where (`TP`.`Position` = 1) group by `TP`.`PlayerID`,`G`.`SeasonID`,`G`.`Playoff`;

-- --------------------------------------------------------

--
-- Structure for view `GoalieShutOut`
--
DROP TABLE IF EXISTS `GoalieShutOut`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `GoalieShutOut` AS select `TP`.`PlayerID` AS `PlayerID`,`G`.`SeasonID` AS `SeasonID`,`G`.`Playoff` AS `Playoff`,count(`G`.`GameID`) AS `ShutOutCount` from ((`TeamPlayer` `TP` join `GameScores` `GS` on((`TP`.`GameID` = `GS`.`GameID`))) join `Game` `G` on((`TP`.`GameID` = `G`.`GameID`))) where ((`TP`.`Position` = 1) and (((`TP`.`Color` = 2) and (`GS`.`BlackPoints` = 0)) or ((`TP`.`Color` = 1) and (`GS`.`WhitePoints` = 0)))) group by `TP`.`PlayerID`,`G`.`SeasonID`,`G`.`Playoff`;

-- --------------------------------------------------------

--
-- Structure for view `PlayerGameResult`
--
DROP TABLE IF EXISTS `PlayerGameResult`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `PlayerGameResult` AS select `TP`.`PlayerID` AS `PlayerID`,`G`.`SeasonID` AS `SeasonID`,`G`.`Playoff` AS `Playoff`,`G`.`GameNum` AS `GameNum`,`TP`.`Position` AS `Position`,(case coalesce(`GW`.`Winner`,0) when 0 then 'L' else 'W' end) AS `GameResult` from ((`TeamPlayer` `TP` join `Game` `G` on((`TP`.`GameID` = `G`.`GameID`))) left join `GameWinners` `GW` on(((`TP`.`Color` = `GW`.`Winner`) and (`TP`.`GameID` = `GW`.`GameID`)))) order by `TP`.`PlayerID`,`G`.`SeasonID`,`G`.`Playoff`,`G`.`GameNum`;

-- --------------------------------------------------------

--
-- Structure for view `PlayerGameWinningGoals`
--
DROP TABLE IF EXISTS `PlayerGameWinningGoals`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `PlayerGameWinningGoals` AS select `G`.`SeasonID` AS `SeasonID`,`G`.`Playoff` AS `Playoff`,`GWG`.`PlayerID` AS `PlayerID`,count(`GWG`.`PointID`) AS `WinningGoals` from (`GameWinningGoals` `GWG` join `Game` `G` on((`GWG`.`GameID` = `G`.`GameID`))) group by `G`.`SeasonID`,`G`.`Playoff`,`GWG`.`PlayerID`;

-- --------------------------------------------------------

--
-- Structure for view `PlayerGoalsAssists`
--
DROP TABLE IF EXISTS `PlayerGoalsAssists`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `PlayerGoalsAssists` AS select `g`.`SeasonID` AS `SeasonID`,`g`.`Playoff` AS `Playoff`,`p`.`PlayerID` AS `PlayerID`,`tp`.`Position` AS `Position`,sum((case `pt`.`PointType` when 1 then 1 else 0 end)) AS `Goals`,sum((case `pt`.`PointType` when 2 then 1 else 0 end)) AS `Assists` from (((`Player` `p` join `TeamPlayer` `tp` on((`p`.`PlayerID` = `tp`.`PlayerID`))) join `Point` `pt` on((`tp`.`TeamPlayerID` = `pt`.`TeamPlayerID`))) join `Game` `g` on((`tp`.`GameID` = `g`.`GameID`))) group by `g`.`SeasonID`,`g`.`Playoff`,`p`.`PlayerID`,`tp`.`Position`;

-- --------------------------------------------------------

--
-- Structure for view `PlayerWinsLossesByPosition`
--
DROP TABLE IF EXISTS `PlayerWinsLossesByPosition`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `PlayerWinsLossesByPosition` AS select `P`.`PlayerID` AS `PlayerID`,`G`.`SeasonID` AS `SeasonID`,`G`.`Playoff` AS `Playoff`,`TP`.`Position` AS `Position`,sum(if((`TP`.`Color` = `GW`.`Winner`),1,0)) AS `Wins`,sum(if((`TP`.`Color` <> `GW`.`Winner`),1,0)) AS `Losses` from (((`Player` `P` join `TeamPlayer` `TP` on((`P`.`PlayerID` = `TP`.`PlayerID`))) join `GameWinners` `GW` on((`TP`.`GameID` = `GW`.`GameID`))) join `Game` `G` on((`TP`.`GameID` = `G`.`GameID`))) group by `P`.`PlayerID`,`G`.`SeasonID`,`G`.`Playoff`,`TP`.`Position`;

-- --------------------------------------------------------

--
-- Structure for view `QuickGoalieData`
--
DROP TABLE IF EXISTS `QuickGoalieData`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `QuickGoalieData` AS select `GGAA`.`PlayerID` AS `PlayerID`,`GGAA`.`SeasonID` AS `SeasonID`,`GGAA`.`Playoff` AS `Playoff`,`GGAA`.`GoalsAgainst` AS `GoalsAgainst`,`PWLBP`.`Wins` AS `Wins`,`PWLBP`.`Losses` AS `Losses`,coalesce(`PGA`.`Goals`,0) AS `Goals`,coalesce(`PGA`.`Assists`,0) AS `Assists`,coalesce(`GSO`.`ShutOutCount`,0) AS `ShutOutCount`,`GGT`.`TotalGameSeconds` AS `TotalGameSeconds` from ((((`GoalieGAA` `GGAA` join `PlayerWinsLossesByPosition` `PWLBP` on(((`GGAA`.`PlayerID` = `PWLBP`.`PlayerID`) and (`GGAA`.`SeasonID` = `PWLBP`.`SeasonID`) and (`GGAA`.`Playoff` = `PWLBP`.`Playoff`)))) join `GoalieGameTime` `GGT` on(((`GGAA`.`PlayerID` = `GGT`.`PlayerID`) and (`GGAA`.`SeasonID` = `GGT`.`SeasonID`) and (`GGAA`.`Playoff` = `GGT`.`Playoff`)))) left join `GoalieShutOut` `GSO` on(((`GGAA`.`PlayerID` = `GSO`.`PlayerID`) and (`GGAA`.`SeasonID` = `GSO`.`SeasonID`) and (`GGAA`.`Playoff` = `GSO`.`Playoff`)))) left join `PlayerGoalsAssists` `PGA` on(((`PWLBP`.`PlayerID` = `PGA`.`PlayerID`) and (`PWLBP`.`SeasonID` = `PGA`.`SeasonID`) and (`PWLBP`.`Playoff` = `PGA`.`Playoff`) and (`PWLBP`.`Position` = `PGA`.`Position`)))) where (`PWLBP`.`Position` = 1);

-- --------------------------------------------------------

--
-- Structure for view `QuickPlayerData`
--
DROP TABLE IF EXISTS `QuickPlayerData`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `QuickPlayerData` AS select `PGA`.`SeasonID` AS `SeasonID`,`PGA`.`Playoff` AS `Playoff`,`PGA`.`PlayerID` AS `PlayerID`,`PGA`.`Goals` AS `Goals`,`PGA`.`Assists` AS `Assists`,`PWLBP`.`Wins` AS `Wins`,`PWLBP`.`Losses` AS `Losses`,coalesce(`PGWG`.`WinningGoals`,0) AS `WinningGoals` from ((`PlayerGoalsAssists` `PGA` join `PlayerWinsLossesByPosition` `PWLBP` on(((`PGA`.`PlayerID` = `PWLBP`.`PlayerID`) and (`PGA`.`SeasonID` = `PWLBP`.`SeasonID`) and (`PGA`.`Playoff` = `PWLBP`.`Playoff`) and (`PGA`.`Position` = `PWLBP`.`Position`)))) left join `PlayerGameWinningGoals` `PGWG` on(((`PGA`.`PlayerID` = `PGWG`.`PlayerID`) and (`PGA`.`SeasonID` = `PGWG`.`SeasonID`) and (`PGA`.`Playoff` = `PGWG`.`Playoff`)))) where (`PGA`.`Position` = 2);

-- --------------------------------------------------------

--
-- Structure for view `TeamScores`
--
DROP TABLE IF EXISTS `TeamScores`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `TeamScores` AS select `g`.`GameID` AS `GameID`,`tp`.`Color` AS `Color`,count(`p`.`PointID`) AS `Score` from ((`Game` `g` join `TeamPlayer` `tp` on((`g`.`GameID` = `tp`.`GameID`))) join `Point` `p` on(((`tp`.`TeamPlayerID` = `p`.`TeamPlayerID`) and (`p`.`PointType` = 1)))) group by `g`.`GameID`,`tp`.`Color`;

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
