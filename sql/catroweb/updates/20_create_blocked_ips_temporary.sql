CREATE OR REPLACE FUNCTION make_plpgsql()
RETURNS VOID
LANGUAGE SQL
AS $$
CREATE LANGUAGE plpgsql;
$$;
 
SELECT
    CASE
    WHEN EXISTS(
        SELECT 1
        FROM pg_catalog.pg_language
        WHERE lanname='plpgsql'
    )
    THEN NULL
    ELSE make_plpgsql() END;
 
DROP FUNCTION make_plpgsql();

CREATE TABLE blocked_ips_temporary
(
  id serial NOT NULL PRIMARY KEY,
  ip_address character varying(32) NOT NULL UNIQUE,
  blocked_until timestamp with time zone,
  last_attempt timestamp with time zone NOT NULL,
  attempts integer NOT NULL
);

CREATE OR REPLACE FUNCTION save_failed_attempt(varchar) RETURNS VOID AS
$$
DECLARE
	pIP_address ALIAS FOR $1;
	vblocked_ips_temporary blocked_ips_temporary%rowtype;
BEGIN
    BEGIN
	INSERT INTO blocked_ips_temporary 
	(ip_address, last_attempt, attempts) VALUES(pIP_address, current_timestamp, 1);

    EXCEPTION WHEN unique_violation THEN
	SELECT *
	  into vblocked_ips_temporary
	  from blocked_ips_temporary 
         where ip_address=pIP_address;

	if coalesce(vblocked_ips_temporary.blocked_until,current_timestamp) <= current_timestamp
	then
		vblocked_ips_temporary.attempts =
		 case vblocked_ips_temporary.last_attempt <= current_timestamp - interval '30 second'
			when true then 1
				  else vblocked_ips_temporary.attempts + 1
		 end;
		
		vblocked_ips_temporary.blocked_until =
		 case vblocked_ips_temporary.attempts >= 5
			when true then current_timestamp + interval '30 second'
				  else null
		 end;

		vblocked_ips_temporary.last_attempt = current_timestamp;
		
		UPDATE blocked_ips_temporary 
		set attempts      = vblocked_ips_temporary.attempts
		  , last_attempt  = vblocked_ips_temporary.last_attempt
		  , blocked_until = vblocked_ips_temporary.blocked_until
		WHERE ip_address=pIP_address;
	end if;
    END;
END;
$$
LANGUAGE plpgsql;
