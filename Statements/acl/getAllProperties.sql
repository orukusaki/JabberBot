SELECT intAclPropertyId as id,
        strHandle as handle,
        strFnName  as fnName,
        intPreference as preference
FROM tblAclProperty 
ORDER BY intPreference ASC
