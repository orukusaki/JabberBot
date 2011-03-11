UPDATE 
    tblMessageQueue
  SET 
    dtmSent = now()
WHERE 
    intMessageQueueId = :intMessageQueueId
