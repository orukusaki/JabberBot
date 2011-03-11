SELECT 
  acl.bolAllow, acl.strProperty, p.strFnName, acl.strValue
FROM
  tblAcl acl
  JOIN tblAclProperty p
    ON acl.strProperty = p.strHandle
WHERE strPosition = :strPosition
ORDER BY p.intPreference DESC,
          bolAllow ASC
