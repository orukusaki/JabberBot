SELECT 
    intMessageQueueId, strTo, strType, strMessage
FROM 
    tblMessageQueue
WHERE 
      dtmSent IS NULL
  AND dtmCancelled IS NULL
  AND dtmDue <= now()
