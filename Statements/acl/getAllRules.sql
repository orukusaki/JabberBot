SELECT intAclId as id,
       strPosition as position,
       if (bolAllow, 'allow', 'deny') as directive,
       strProperty as property, 
       strValue as value
FROM tblAcl
ORDER By strPosition
