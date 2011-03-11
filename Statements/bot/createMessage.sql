INSERT INTO tblMessageQueue 
SET  
    strTo = :to, 
    strType = :type, 
    strMessage = :message,
    dtmSent = NULL,
    dtmCancelled = NULL,
    dtmDue = now()
