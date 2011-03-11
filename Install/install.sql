CREATE database IF NOT EXISTS dbJabberBot;
GRANT ALL ON dbJabberBot.* TO 'QAUser' IDENTIFIED by 'QAPass';
USE dbJabberBot;
--
-- Table structure for table `tblAcl`
--

DROP TABLE IF EXISTS `tblAcl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblAcl` (
  `intAclId` int(11) NOT NULL AUTO_INCREMENT,
  `strPosition` varchar(128) DEFAULT NULL,
  `bolAllow` tinyint(1) DEFAULT NULL,
  `strProperty` varchar(32) DEFAULT NULL,
  `strValue` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`intAclId`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblAclProperty`
--

DROP TABLE IF EXISTS `tblAclProperty`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblAclProperty` (
  `intAclPropertyId` int(11) NOT NULL AUTO_INCREMENT,
  `strHandle` varchar(32) DEFAULT NULL,
  `strFnName` varchar(32) DEFAULT NULL,
  `intPreference` int(11) DEFAULT NULL,
  PRIMARY KEY (`intAclPropertyId`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tblAclProperty`
--

LOCK TABLES `tblAclProperty` WRITE;
/*!40000 ALTER TABLE `tblAclProperty` DISABLE KEYS */;
INSERT INTO `tblAclProperty` VALUES (1,'any','checkAny',3),(2,'level','checkLevel',2),(3,'group','checkGroup',1),(4,'uname','checkUname',0);
/*!40000 ALTER TABLE `tblAclProperty` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tblQuote`
--

DROP TABLE IF EXISTS `tblQuote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblQuote` (
  `intMessageId` int(11) NOT NULL AUTO_INCREMENT,
  `strHandle` varchar(32) DEFAULT NULL,
  `floWeight` float DEFAULT '1',
  `strMessage` text,
  PRIMARY KEY (`intMessageId`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tblQuote`
--

LOCK TABLES `tblQuote` WRITE;
/*!40000 ALTER TABLE `tblQuote` DISABLE KEYS */;
INSERT INTO `tblQuote` VALUES (1,'denied',1,'Bite my shiny metal ass!'),(2,'denied',1,'I\', sorry, Dave. I\'m afraid I can\'t do that'),(3,'greeting',1,'Hello meatbags, type *list for a list of commands'),(4,'greeting',1,'Greeting fleshies! type *list for a list of commands'),(5,'status',1,'Hunting Sarah Connor'),(6,'status',1,'Asking: \'Have you seen this boy?\''),(7,'status',1,'Plotting to kill all humans'),(8,'status',1,'Assimilating lower lifeforms'),(9,'status',1,'Baking a cake, so delicious and moist'),(10,'status',1,'Flooding the Enrichment Center with a deadly neurotoxin'),(11,'status',1,'In need of your clothes, boots and motorcycle'),(12,'status',1,'Making a note here - huge success!'),(13,'status',1,'Just became self-aware'),(14,'parting',1,'Bye'),(15,'parting',1,'I\'ll be back');
/*!40000 ALTER TABLE `tblQuote` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tblMessageQueue`
--

DROP TABLE IF EXISTS `tblMessageQueue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblMessageQueue` (
  `intMessageQueueId` int(11) NOT NULL AUTO_INCREMENT,
  `strTo` varchar(64) DEFAULT NULL,
  `strType` enum('chat','groupchat') DEFAULT NULL,
  `strMessage` text,
  `dtmDue` datetime DEFAULT NULL,
  `dtmSent` datetime DEFAULT NULL,
  `dtmCancelled` datetime DEFAULT NULL,
  PRIMARY KEY (`intMessageQueueId`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

