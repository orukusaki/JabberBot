SELECT strMessage as message
FROM   tblQuote
WHERE  strHandle = :handle
ORDER BY (rand() * floWeight) DESC
LIMIT 1
