#!/bin/bash

psql -d template1 -c "CREATE OR REPLACE FUNCTION create_language_plpgsql()
RETURNS BOOLEAN AS \$\$
    CREATE LANGUAGE plpgsql;
    SELECT TRUE;
\$\$ LANGUAGE SQL;

SELECT CASE WHEN NOT
    (
        SELECT  TRUE AS exists
        FROM    pg_language
        WHERE   lanname = 'plpgsql'
        UNION
        SELECT  FALSE AS exists
        ORDER BY exists DESC
        LIMIT 1
    )
THEN
    create_language_plpgsql()
ELSE
    FALSE
END AS plpgsql_created;

DROP FUNCTION create_language_plpgsql();"



echo ""
echo -n "Enter websites SQL-password and press [ENTER]: "
read password
psql -d template1 -c "CREATE USER website WITH PASSWORD '${password}' CREATEDB;";



psql -d template1 -c "DROP SCHEMA IF EXISTS dbo CASCADE;
CREATE SCHEMA dbo;
CREATE OR REPLACE FUNCTION dbo.pg_kill_user_process(pid integer)
RETURNS boolean AS \$body\$
DECLARE
    result boolean;
BEGIN
    IF EXISTS (SELECT * FROM pg_catalog.pg_stat_activity
        WHERE usename IN (SELECT usename FROM pg_catalog.pg_stat_activity WHERE procpid = pg_backend_pid()) AND procpid = pid) THEN
            result := (SELECT pg_catalog.pg_terminate_backend(pid));
    ELSE
        result := false;
    END IF;
    RETURN result;
END;
\$body\$
    LANGUAGE plpgsql
    SECURITY DEFINER
    VOLATILE
    RETURNS NULL ON NULL INPUT
    SET search_path = pg_catalog;
REVOKE EXECUTE ON FUNCTION dbo.pg_kill_user_process(pid integer) FROM PUBLIC;
GRANT USAGE ON SCHEMA dbo TO website;
GRANT EXECUTE ON FUNCTION dbo.pg_kill_user_process(pid integer) TO website;"
