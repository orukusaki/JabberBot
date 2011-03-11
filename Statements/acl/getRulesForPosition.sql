SELECT 
  acl.bolAllow as allow, 
  acl.strProperty as property, 
  p.strFnName as fnName, 
  acl.strValue as value
FROM
  tblAcl acl
  JOIN tblAclProperty p
    ON acl.strProperty = p.strHandle
WHERE strPosition = :strPosition
ORDER BY p.intPreference ASC,
          bolAllow ASC
