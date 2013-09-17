-- Table: blocked_cusers
CREATE TABLE blocked_cusers
(
  user_id integer,
  user_name character varying(255),
  user_token character varying(255)
)
WITH (
  OIDS=FALSE
);

-- Table: blocked_ips
CREATE TABLE blocked_ips
(
  ip_address character varying(32)
)
WITH (
  OIDS=FALSE
);

