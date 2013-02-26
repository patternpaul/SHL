CREATE TABLE `DbVersion` (
  `DbVersionId` int(11) NOT NULL AUTO_INCREMENT,
  `Major` int(11) NOT NULL,
  `Minor` int(11) NOT NULL,
  `Build` int(11) NOT NULL,
  `VersionDate` datetime NOT NULL,
  PRIMARY KEY (`DbVersionId`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `About` (
  `AboutID` int(11) NOT NULL AUTO_INCREMENT,
  `DateCreated` datetime NOT NULL,
  `Poster` int(11) NOT NULL,
  `Content` text NOT NULL,
  PRIMARY KEY (`AboutID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `Game` (
  `GameDate` date DEFAULT NULL,
  `GameEnd` time DEFAULT NULL,
  `GameID` int(11) NOT NULL AUTO_INCREMENT,
  `GameNum` int(11) NOT NULL,
  `GameStart` time DEFAULT NULL,
  `Playoff` tinyint(1) NOT NULL,
  `SeasonID` int(11) NOT NULL,
  PRIMARY KEY (`GameID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `IDType` (
  `IDTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(500) NOT NULL,
  `URL` varchar(500) NOT NULL,
  PRIMARY KEY (`IDTypeID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `PageSecurity` (
  `PageSecurityID` int(11) NOT NULL AUTO_INCREMENT,
  `Page` varchar(500) NOT NULL,
  `SecurityLevel` int(1) NOT NULL DEFAULT '1',
  `PlayerID` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`PageSecurityID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `Point` (
  `PointID` int(11) NOT NULL AUTO_INCREMENT,
  `PointNum` int(11) NOT NULL,
  `PointType` int(11) NOT NULL,
  `TeamPlayerID` int(11) NOT NULL,
  PRIMARY KEY (`PointID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `Post` (
  `PostID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(500) NOT NULL,
  `DateCreated` datetime NOT NULL,
  `Poster` int(11) NOT NULL,
  `Content` text NOT NULL,
  PRIMARY KEY (`PostID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `QuickGameScores` (
  `GameID` int(11) DEFAULT NULL,
  `BlackPoints` int(11) DEFAULT NULL,
  `WhitePoints` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `QuickGameTimeLength` (
  `GameID` int(11) DEFAULT NULL,
  `GameSeconds` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `QuickGameWinners` (
  `GameID` int(11) DEFAULT NULL,
  `Winner` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `QuickGameWinningGoals` (
  `PlayerID` int(11) DEFAULT NULL,
  `TeamPlayerID` int(11) DEFAULT NULL,
  `GameID` int(11) DEFAULT NULL,
  `PointID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

CREATE TABLE IF NOT EXISTS `QuickGoalieGAA` (
  `PlayerID` int(11) DEFAULT NULL,
  `SeasonID` int(11) DEFAULT NULL,
  `Playoff` tinyint(4) DEFAULT NULL,
  `GoalsAgainst` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `QuickGoalieGameTime` (
  `PlayerID` int(11) DEFAULT NULL,
  `SeasonID` int(11) DEFAULT NULL,
  `Playoff` tinyint(4) DEFAULT NULL,
  `TotalGameSeconds` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `QuickGoalieShutOut` (
  `PlayerID` int(11) DEFAULT NULL,
  `SeasonID` int(11) DEFAULT NULL,
  `Playoff` tinyint(4) DEFAULT NULL,
  `ShutOutCount` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `QuickHookupSummary` (
  `SeasonID` int(11) DEFAULT NULL,
  `Playoff` tinyint(4) DEFAULT NULL,
  `FirstPlayerID` int(11) DEFAULT NULL,
  `SecondPlayerID` int(11) DEFAULT NULL,
  `Hookup` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

CREATE TABLE IF NOT EXISTS `QuickPlayerGameResult` (
  `PlayerID` int(11) DEFAULT NULL,
  `SeasonID` int(11) DEFAULT NULL,
  `Playoff` tinyint(4) DEFAULT NULL,
  `GameNum` int(11) DEFAULT NULL,
  `Position` int(11) DEFAULT NULL,
  `GameResult` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `QuickPlayerGameWinningGoals` (
  `SeasonID` int(11) DEFAULT NULL,
  `Playoff` tinyint(4) DEFAULT NULL,
  `PlayerID` int(11) DEFAULT NULL,
  `WinningGoals` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `QuickPlayerGoalsAssists` (
  `SeasonID` int(11) DEFAULT NULL,
  `Playoff` tinyint(4) DEFAULT NULL,
  `PlayerID` int(11) DEFAULT NULL,
  `Position` int(11) DEFAULT NULL,
  `Goals` int(11) DEFAULT NULL,
  `Assists` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `QuickPlayerHookup` (
  `SeasonID` int(11) DEFAULT NULL,
  `Playoff` tinyint(4) DEFAULT NULL,
  `GoalPlayer` int(11) DEFAULT NULL,
  `AssistPlayer` int(11) DEFAULT NULL,
  `HookupCount` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `QuickPlayerTeamScores` (
  `SeasonID` int(11) DEFAULT NULL,
  `Playoff` tinyint(4) DEFAULT NULL,
  `PlayerID` int(11) DEFAULT NULL,
  `TeamScore` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `QuickPlayerWinsLossesByPosition` (
  `PlayerID` int(11) DEFAULT NULL,
  `SeasonID` int(11) DEFAULT NULL,
  `Playoff` tinyint(4) DEFAULT NULL,
  `Position` int(11) DEFAULT NULL,
  `Wins` int(11) DEFAULT NULL,
  `Losses` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `QuickPointDetail` (
  `GoalPlayerID` int(11) NOT NULL,
  `AssistPlayerID` int(11) DEFAULT NULL,
  `GameID` int(11) NOT NULL,
  `SeasonID` int(11) NOT NULL,
  `Playoff` tinyint(4) NOT NULL,
  `Color` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `QuickTeamScores` (
  `GameID` int(11) DEFAULT NULL,
  `Color` int(11) DEFAULT NULL,
  `Score` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `Record` (
  `RecordID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(500) NOT NULL,
  PRIMARY KEY (`RecordID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `RecordHolder` (
  `RecordHolderID` int(11) NOT NULL AUTO_INCREMENT,
  `Value` varchar(500) NOT NULL,
  `RecordID` int(11) NOT NULL,
  PRIMARY KEY (`RecordHolderID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `RecordHolderID` (
  `RecordHolderIDID` int(11) NOT NULL AUTO_INCREMENT,
  `RepresentativeID` int(11) NOT NULL,
  `IDTypeID` int(11) NOT NULL,
  `RecordHolderID` int(11) NOT NULL,
  PRIMARY KEY (`RecordHolderIDID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `Request` (
  `RequestID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(500) DEFAULT NULL,
  `DateCreated` date DEFAULT NULL,
  `Content` text NOT NULL,
  `Priority` int(11) NOT NULL,
  `Status` int(11) NOT NULL,
  `Poster` int(11) NOT NULL,
  PRIMARY KEY (`RequestID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `TeamPlayer` (
  `Color` int(11) NOT NULL,
  `GameID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `Position` int(11) NOT NULL,
  `TeamPlayerID` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`TeamPlayerID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;