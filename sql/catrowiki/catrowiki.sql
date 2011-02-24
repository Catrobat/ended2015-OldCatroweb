--
-- PostgreSQL database dump
--

-- Dumped from database version 8.3.14
-- Dumped by pg_dump version 9.0.1
-- Started on 2011-02-16 18:50:04

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

--
-- TOC entry 441 (class 2612 OID 16386)
-- Name: plpgsql; Type: PROCEDURAL LANGUAGE; Schema: -; Owner: website
--

DROP LANGUAGE IF EXISTS plpgsql;
CREATE LANGUAGE plpgsql;

ALTER PROCEDURAL LANGUAGE plpgsql OWNER TO website;

SET search_path = public, pg_catalog;

--
-- TOC entry 23 (class 1255 OID 34545)
-- Dependencies: 3
-- Name: add_interwiki(text, integer, smallint); Type: FUNCTION; Schema: public; Owner: website
--

CREATE FUNCTION add_interwiki(text, integer, smallint) RETURNS integer
    LANGUAGE sql
    AS $_$
 INSERT INTO interwiki (iw_prefix, iw_url, iw_local) VALUES ($1,$2,$3);
 SELECT 1;
 $_$;


ALTER FUNCTION public.add_interwiki(text, integer, smallint) OWNER TO website;

--
-- TOC entry 20 (class 1255 OID 34058)
-- Dependencies: 3 441
-- Name: page_deleted(); Type: FUNCTION; Schema: public; Owner: website
--

CREATE FUNCTION page_deleted() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
 BEGIN
 DELETE FROM recentchanges WHERE rc_namespace = OLD.page_namespace AND rc_title = OLD.page_title;
 RETURN NULL;
 END;
 $$;


ALTER FUNCTION public.page_deleted() OWNER TO website;

--
-- TOC entry 22 (class 1255 OID 34541)
-- Dependencies: 441 3
-- Name: ts2_page_text(); Type: FUNCTION; Schema: public; Owner: website
--

CREATE FUNCTION ts2_page_text() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
 BEGIN
 IF TG_OP = 'INSERT' THEN
 NEW.textvector = to_tsvector(NEW.old_text);
 ELSIF NEW.old_text != OLD.old_text THEN
 NEW.textvector := to_tsvector(NEW.old_text);
 END IF;
 RETURN NEW;
 END;
 $$;


ALTER FUNCTION public.ts2_page_text() OWNER TO website;

--
-- TOC entry 21 (class 1255 OID 34539)
-- Dependencies: 3 441
-- Name: ts2_page_title(); Type: FUNCTION; Schema: public; Owner: website
--

CREATE FUNCTION ts2_page_title() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
 BEGIN
 IF TG_OP = 'INSERT' THEN
 NEW.titlevector = to_tsvector(REPLACE(NEW.page_title,'/',' '));
 ELSIF NEW.page_title != OLD.page_title THEN
 NEW.titlevector := to_tsvector(REPLACE(NEW.page_title,'/',' '));
 END IF;
 RETURN NEW;
 END;
 $$;


ALTER FUNCTION public.ts2_page_title() OWNER TO website;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 1626 (class 1259 OID 34131)
-- Dependencies: 1948 1949 3
-- Name: archive; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE archive (
    ar_namespace smallint NOT NULL,
    ar_title text NOT NULL,
    ar_text text,
    ar_page_id integer,
    ar_parent_id integer,
    ar_comment text,
    ar_user integer,
    ar_user_text text NOT NULL,
    ar_timestamp timestamp with time zone NOT NULL,
    ar_minor_edit smallint DEFAULT 0 NOT NULL,
    ar_flags text,
    ar_rev_id integer,
    ar_text_id integer,
    ar_deleted smallint DEFAULT 0 NOT NULL,
    ar_len integer
);


ALTER TABLE public.archive OWNER TO website;

--
-- TOC entry 1663 (class 1259 OID 34577)
-- Dependencies: 3
-- Name: category_cat_id_seq; Type: SEQUENCE; Schema: public; Owner: website
--

CREATE SEQUENCE category_cat_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.category_cat_id_seq OWNER TO website;

--
-- TOC entry 2230 (class 0 OID 0)
-- Dependencies: 1663
-- Name: category_cat_id_seq; Type: SEQUENCE SET; Schema: public; Owner: website
--

SELECT pg_catalog.setval('category_cat_id_seq', 1, false);


--
-- TOC entry 1664 (class 1259 OID 34579)
-- Dependencies: 2007 2008 2009 2010 2011 3
-- Name: category; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE category (
    cat_id integer DEFAULT nextval('category_cat_id_seq'::regclass) NOT NULL,
    cat_title text NOT NULL,
    cat_pages integer DEFAULT 0 NOT NULL,
    cat_subcats integer DEFAULT 0 NOT NULL,
    cat_files integer DEFAULT 0 NOT NULL,
    cat_hidden smallint DEFAULT 0 NOT NULL
);


ALTER TABLE public.category OWNER TO website;

--
-- TOC entry 1631 (class 1259 OID 34195)
-- Dependencies: 3
-- Name: categorylinks; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE categorylinks (
    cl_from integer NOT NULL,
    cl_to text NOT NULL,
    cl_sortkey text,
    cl_timestamp timestamp with time zone NOT NULL
);


ALTER TABLE public.categorylinks OWNER TO website;

--
-- TOC entry 1665 (class 1259 OID 34594)
-- Dependencies: 3
-- Name: change_tag; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE change_tag (
    ct_rc_id integer,
    ct_log_id integer,
    ct_rev_id integer,
    ct_tag text NOT NULL,
    ct_params text
);


ALTER TABLE public.change_tag OWNER TO website;

--
-- TOC entry 1633 (class 1259 OID 34221)
-- Dependencies: 3
-- Name: external_user; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE external_user (
    eu_local_id integer NOT NULL,
    eu_external_id text
);


ALTER TABLE public.external_user OWNER TO website;

--
-- TOC entry 1632 (class 1259 OID 34208)
-- Dependencies: 3
-- Name: externallinks; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE externallinks (
    el_from integer NOT NULL,
    el_to text NOT NULL,
    el_index text NOT NULL
);


ALTER TABLE public.externallinks OWNER TO website;

--
-- TOC entry 1641 (class 1259 OID 34335)
-- Dependencies: 3
-- Name: filearchive_fa_id_seq; Type: SEQUENCE; Schema: public; Owner: website
--

CREATE SEQUENCE filearchive_fa_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.filearchive_fa_id_seq OWNER TO website;

--
-- TOC entry 2231 (class 0 OID 0)
-- Dependencies: 1641
-- Name: filearchive_fa_id_seq; Type: SEQUENCE SET; Schema: public; Owner: website
--

SELECT pg_catalog.setval('filearchive_fa_id_seq', 1, false);


--
-- TOC entry 1642 (class 1259 OID 34337)
-- Dependencies: 1976 1977 1978 1979 1980 3
-- Name: filearchive; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE filearchive (
    fa_id integer DEFAULT nextval('filearchive_fa_id_seq'::regclass) NOT NULL,
    fa_name text NOT NULL,
    fa_archive_name text,
    fa_storage_group text,
    fa_storage_key text,
    fa_deleted_user integer,
    fa_deleted_timestamp timestamp with time zone NOT NULL,
    fa_deleted_reason text,
    fa_size integer NOT NULL,
    fa_width integer NOT NULL,
    fa_height integer NOT NULL,
    fa_metadata bytea DEFAULT ''::bytea NOT NULL,
    fa_bits smallint,
    fa_media_type text,
    fa_major_mime text DEFAULT 'unknown'::text,
    fa_minor_mime text DEFAULT 'unknown'::text,
    fa_description text NOT NULL,
    fa_user integer,
    fa_user_text text NOT NULL,
    fa_timestamp timestamp with time zone,
    fa_deleted smallint DEFAULT 0 NOT NULL
);


ALTER TABLE public.filearchive OWNER TO website;

--
-- TOC entry 1636 (class 1259 OID 34256)
-- Dependencies: 3
-- Name: hitcounter; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE hitcounter (
    hc_id bigint NOT NULL
);


ALTER TABLE public.hitcounter OWNER TO website;

--
-- TOC entry 1639 (class 1259 OID 34291)
-- Dependencies: 1967 1968 1969 1970 3
-- Name: image; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE image (
    img_name text NOT NULL,
    img_size integer NOT NULL,
    img_width integer NOT NULL,
    img_height integer NOT NULL,
    img_metadata bytea DEFAULT ''::bytea NOT NULL,
    img_bits smallint,
    img_media_type text,
    img_major_mime text DEFAULT 'unknown'::text,
    img_minor_mime text DEFAULT 'unknown'::text,
    img_description text NOT NULL,
    img_user integer,
    img_user_text text NOT NULL,
    img_timestamp timestamp with time zone,
    img_sha1 text DEFAULT ''::text NOT NULL
);


ALTER TABLE public.image OWNER TO website;

--
-- TOC entry 1630 (class 1259 OID 34183)
-- Dependencies: 3
-- Name: imagelinks; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE imagelinks (
    il_from integer NOT NULL,
    il_to text NOT NULL
);


ALTER TABLE public.imagelinks OWNER TO website;

--
-- TOC entry 1647 (class 1259 OID 34420)
-- Dependencies: 1990 3
-- Name: interwiki; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE interwiki (
    iw_prefix text NOT NULL,
    iw_url text NOT NULL,
    iw_local smallint NOT NULL,
    iw_trans smallint DEFAULT 0 NOT NULL
);


ALTER TABLE public.interwiki OWNER TO website;

--
-- TOC entry 1637 (class 1259 OID 34259)
-- Dependencies: 3
-- Name: ipblocks_ipb_id_seq; Type: SEQUENCE; Schema: public; Owner: website
--

CREATE SEQUENCE ipblocks_ipb_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.ipblocks_ipb_id_seq OWNER TO website;

--
-- TOC entry 2232 (class 0 OID 0)
-- Dependencies: 1637
-- Name: ipblocks_ipb_id_seq; Type: SEQUENCE SET; Schema: public; Owner: website
--

SELECT pg_catalog.setval('ipblocks_ipb_id_seq', 1, false);


--
-- TOC entry 1638 (class 1259 OID 34261)
-- Dependencies: 1958 1959 1960 1961 1962 1963 1964 1965 1966 3
-- Name: ipblocks; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE ipblocks (
    ipb_id integer DEFAULT nextval('ipblocks_ipb_id_seq'::regclass) NOT NULL,
    ipb_address text,
    ipb_user integer,
    ipb_by integer NOT NULL,
    ipb_by_text text DEFAULT ''::text NOT NULL,
    ipb_reason text NOT NULL,
    ipb_timestamp timestamp with time zone NOT NULL,
    ipb_auto smallint DEFAULT 0 NOT NULL,
    ipb_anon_only smallint DEFAULT 0 NOT NULL,
    ipb_create_account smallint DEFAULT 1 NOT NULL,
    ipb_enable_autoblock smallint DEFAULT 1 NOT NULL,
    ipb_expiry timestamp with time zone NOT NULL,
    ipb_range_start text,
    ipb_range_end text,
    ipb_deleted smallint DEFAULT 0 NOT NULL,
    ipb_block_email smallint DEFAULT 0 NOT NULL,
    ipb_allow_usertalk smallint DEFAULT 0 NOT NULL
);


ALTER TABLE public.ipblocks OWNER TO website;

--
-- TOC entry 1658 (class 1259 OID 34527)
-- Dependencies: 3
-- Name: job_job_id_seq; Type: SEQUENCE; Schema: public; Owner: website
--

CREATE SEQUENCE job_job_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.job_job_id_seq OWNER TO website;

--
-- TOC entry 2233 (class 0 OID 0)
-- Dependencies: 1658
-- Name: job_job_id_seq; Type: SEQUENCE SET; Schema: public; Owner: website
--

SELECT pg_catalog.setval('job_job_id_seq', 1, false);


--
-- TOC entry 1659 (class 1259 OID 34529)
-- Dependencies: 2002 3
-- Name: job; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE job (
    job_id integer DEFAULT nextval('job_job_id_seq'::regclass) NOT NULL,
    job_cmd text NOT NULL,
    job_namespace smallint NOT NULL,
    job_title text NOT NULL,
    job_params text NOT NULL
);


ALTER TABLE public.job OWNER TO website;

--
-- TOC entry 1670 (class 1259 OID 34641)
-- Dependencies: 3
-- Name: l10n_cache; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE l10n_cache (
    lc_lang text NOT NULL,
    lc_key text NOT NULL,
    lc_value text NOT NULL
);


ALTER TABLE public.l10n_cache OWNER TO website;

--
-- TOC entry 1634 (class 1259 OID 34230)
-- Dependencies: 3
-- Name: langlinks; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE langlinks (
    ll_from integer NOT NULL,
    ll_lang text,
    ll_title text
);


ALTER TABLE public.langlinks OWNER TO website;

--
-- TOC entry 1655 (class 1259 OID 34500)
-- Dependencies: 2000 3
-- Name: log_search; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE log_search (
    ls_field text NOT NULL,
    ls_value text NOT NULL,
    ls_log_id integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.log_search OWNER TO website;

--
-- TOC entry 1653 (class 1259 OID 34476)
-- Dependencies: 3
-- Name: logging_log_id_seq; Type: SEQUENCE; Schema: public; Owner: website
--

CREATE SEQUENCE logging_log_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.logging_log_id_seq OWNER TO website;

--
-- TOC entry 2234 (class 0 OID 0)
-- Dependencies: 1653
-- Name: logging_log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: website
--

SELECT pg_catalog.setval('logging_log_id_seq', 1, false);


--
-- TOC entry 1654 (class 1259 OID 34478)
-- Dependencies: 1997 1998 1999 3
-- Name: logging; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE logging (
    log_id integer DEFAULT nextval('logging_log_id_seq'::regclass) NOT NULL,
    log_type text NOT NULL,
    log_action text NOT NULL,
    log_timestamp timestamp with time zone NOT NULL,
    log_user integer,
    log_namespace smallint NOT NULL,
    log_title text NOT NULL,
    log_comment text,
    log_params text,
    log_deleted smallint DEFAULT 0 NOT NULL,
    log_user_text text DEFAULT ''::text NOT NULL,
    log_page integer
);


ALTER TABLE public.logging OWNER TO website;

--
-- TOC entry 1646 (class 1259 OID 34412)
-- Dependencies: 3
-- Name: math; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE math (
    math_inputhash bytea NOT NULL,
    math_outputhash bytea NOT NULL,
    math_html_conservativeness smallint NOT NULL,
    math_html text,
    math_mathml text
);


ALTER TABLE public.math OWNER TO website;

--
-- TOC entry 1669 (class 1259 OID 34634)
-- Dependencies: 2012 3
-- Name: mediawiki_version; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE mediawiki_version (
    type text NOT NULL,
    mw_version text NOT NULL,
    notes text,
    pg_version text,
    pg_dbname text,
    pg_user text,
    pg_port text,
    mw_schema text,
    ts2_schema text,
    ctype text,
    sql_version text,
    sql_date text,
    cdate timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.mediawiki_version OWNER TO website;

--
-- TOC entry 1613 (class 1259 OID 33996)
-- Dependencies: 3
-- Name: user_user_id_seq; Type: SEQUENCE; Schema: public; Owner: website
--

CREATE SEQUENCE user_user_id_seq
    INCREMENT BY 1
    MINVALUE 0
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user_user_id_seq OWNER TO website;

--
-- TOC entry 2235 (class 0 OID 0)
-- Dependencies: 1613
-- Name: user_user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: website
--

SELECT pg_catalog.setval('user_user_id_seq', 1, true);


--
-- TOC entry 1614 (class 1259 OID 33998)
-- Dependencies: 1937 3
-- Name: mwuser; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE mwuser (
    user_id integer DEFAULT nextval('user_user_id_seq'::regclass) NOT NULL,
    user_name text NOT NULL,
    user_real_name text,
    user_password text,
    user_newpassword text,
    user_newpass_time timestamp with time zone,
    user_token text,
    user_email text,
    user_email_token text,
    user_email_token_expires timestamp with time zone,
    user_email_authenticated timestamp with time zone,
    user_options text,
    user_touched timestamp with time zone,
    user_registration timestamp with time zone,
    user_editcount integer
);


ALTER TABLE public.mwuser OWNER TO website;

--
-- TOC entry 1651 (class 1259 OID 34458)
-- Dependencies: 1996 3
-- Name: objectcache; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE objectcache (
    keyname text,
    value bytea DEFAULT ''::bytea NOT NULL,
    exptime timestamp with time zone NOT NULL
);


ALTER TABLE public.objectcache OWNER TO website;

--
-- TOC entry 1640 (class 1259 OID 34311)
-- Dependencies: 1971 1972 1973 1974 1975 3
-- Name: oldimage; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE oldimage (
    oi_name text NOT NULL,
    oi_archive_name text NOT NULL,
    oi_size integer NOT NULL,
    oi_width integer NOT NULL,
    oi_height integer NOT NULL,
    oi_bits smallint,
    oi_description text,
    oi_user integer,
    oi_user_text text NOT NULL,
    oi_timestamp timestamp with time zone,
    oi_metadata bytea DEFAULT ''::bytea NOT NULL,
    oi_media_type text,
    oi_major_mime text DEFAULT 'unknown'::text,
    oi_minor_mime text DEFAULT 'unknown'::text,
    oi_deleted smallint DEFAULT 0 NOT NULL,
    oi_sha1 text DEFAULT ''::text NOT NULL
);


ALTER TABLE public.oldimage OWNER TO website;

--
-- TOC entry 1617 (class 1259 OID 34035)
-- Dependencies: 3
-- Name: page_page_id_seq; Type: SEQUENCE; Schema: public; Owner: website
--

CREATE SEQUENCE page_page_id_seq
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.page_page_id_seq OWNER TO website;

--
-- TOC entry 2236 (class 0 OID 0)
-- Dependencies: 1617
-- Name: page_page_id_seq; Type: SEQUENCE SET; Schema: public; Owner: website
--

SELECT pg_catalog.setval('page_page_id_seq', 1, true);


--
-- TOC entry 1618 (class 1259 OID 34037)
-- Dependencies: 1938 1939 1940 1941 1942 3
-- Name: page; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE page (
    page_id integer DEFAULT nextval('page_page_id_seq'::regclass) NOT NULL,
    page_namespace smallint NOT NULL,
    page_title text NOT NULL,
    page_restrictions text,
    page_counter bigint DEFAULT 0 NOT NULL,
    page_is_redirect smallint DEFAULT 0 NOT NULL,
    page_is_new smallint DEFAULT 0 NOT NULL,
    page_random numeric(15,14) DEFAULT random() NOT NULL,
    page_touched timestamp with time zone,
    page_latest integer NOT NULL,
    page_len integer NOT NULL,
    titlevector tsvector
);


ALTER TABLE public.page OWNER TO website;

--
-- TOC entry 1625 (class 1259 OID 34117)
-- Dependencies: 3
-- Name: page_props; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE page_props (
    pp_page integer NOT NULL,
    pp_propname text NOT NULL,
    pp_value text NOT NULL
);


ALTER TABLE public.page_props OWNER TO website;

--
-- TOC entry 1623 (class 1259 OID 34099)
-- Dependencies: 3
-- Name: page_restrictions_pr_id_seq; Type: SEQUENCE; Schema: public; Owner: website
--

CREATE SEQUENCE page_restrictions_pr_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.page_restrictions_pr_id_seq OWNER TO website;

--
-- TOC entry 2237 (class 0 OID 0)
-- Dependencies: 1623
-- Name: page_restrictions_pr_id_seq; Type: SEQUENCE SET; Schema: public; Owner: website
--

SELECT pg_catalog.setval('page_restrictions_pr_id_seq', 1, false);


--
-- TOC entry 1624 (class 1259 OID 34101)
-- Dependencies: 1947 3
-- Name: page_restrictions; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE page_restrictions (
    pr_id integer DEFAULT nextval('page_restrictions_pr_id_seq'::regclass) NOT NULL,
    pr_page integer NOT NULL,
    pr_type text NOT NULL,
    pr_level text NOT NULL,
    pr_cascade smallint NOT NULL,
    pr_user integer,
    pr_expiry timestamp with time zone
);


ALTER TABLE public.page_restrictions OWNER TO website;

--
-- TOC entry 1621 (class 1259 OID 34088)
-- Dependencies: 3
-- Name: text_old_id_seq; Type: SEQUENCE; Schema: public; Owner: website
--

CREATE SEQUENCE text_old_id_seq
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.text_old_id_seq OWNER TO website;

--
-- TOC entry 2238 (class 0 OID 0)
-- Dependencies: 1621
-- Name: text_old_id_seq; Type: SEQUENCE SET; Schema: public; Owner: website
--

SELECT pg_catalog.setval('text_old_id_seq', 1, true);


--
-- TOC entry 1622 (class 1259 OID 34090)
-- Dependencies: 1946 3
-- Name: pagecontent; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE pagecontent (
    old_id integer DEFAULT nextval('text_old_id_seq'::regclass) NOT NULL,
    old_text text,
    old_flags text,
    textvector tsvector
);


ALTER TABLE public.pagecontent OWNER TO website;

--
-- TOC entry 1628 (class 1259 OID 34158)
-- Dependencies: 3
-- Name: pagelinks; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE pagelinks (
    pl_from integer NOT NULL,
    pl_namespace smallint NOT NULL,
    pl_title text NOT NULL
);


ALTER TABLE public.pagelinks OWNER TO website;

--
-- TOC entry 1660 (class 1259 OID 34546)
-- Dependencies: 2003 2004 2005 3
-- Name: profiling; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE profiling (
    pf_count integer DEFAULT 0 NOT NULL,
    pf_time numeric(18,10) DEFAULT 0 NOT NULL,
    pf_memory numeric(18,10) DEFAULT 0 NOT NULL,
    pf_name text NOT NULL,
    pf_server text
);


ALTER TABLE public.profiling OWNER TO website;

--
-- TOC entry 1661 (class 1259 OID 34556)
-- Dependencies: 2006 3
-- Name: protected_titles; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE protected_titles (
    pt_namespace smallint NOT NULL,
    pt_title text NOT NULL,
    pt_user integer,
    pt_reason text,
    pt_timestamp timestamp with time zone NOT NULL,
    pt_expiry timestamp with time zone,
    pt_create_perm text DEFAULT ''::text NOT NULL
);


ALTER TABLE public.protected_titles OWNER TO website;

--
-- TOC entry 1648 (class 1259 OID 34429)
-- Dependencies: 3
-- Name: querycache; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE querycache (
    qc_type text NOT NULL,
    qc_value integer NOT NULL,
    qc_namespace smallint NOT NULL,
    qc_title text NOT NULL
);


ALTER TABLE public.querycache OWNER TO website;

--
-- TOC entry 1649 (class 1259 OID 34436)
-- Dependencies: 3
-- Name: querycache_info; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE querycache_info (
    qci_type text,
    qci_timestamp timestamp with time zone
);


ALTER TABLE public.querycache_info OWNER TO website;

--
-- TOC entry 1650 (class 1259 OID 34444)
-- Dependencies: 1991 1992 1993 1994 1995 3
-- Name: querycachetwo; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE querycachetwo (
    qcc_type text NOT NULL,
    qcc_value integer DEFAULT 0 NOT NULL,
    qcc_namespace integer DEFAULT 0 NOT NULL,
    qcc_title text DEFAULT ''::text NOT NULL,
    qcc_namespacetwo integer DEFAULT 0 NOT NULL,
    qcc_titletwo text DEFAULT ''::text NOT NULL
);


ALTER TABLE public.querycachetwo OWNER TO website;

--
-- TOC entry 1643 (class 1259 OID 34364)
-- Dependencies: 3
-- Name: recentchanges_rc_id_seq; Type: SEQUENCE; Schema: public; Owner: website
--

CREATE SEQUENCE recentchanges_rc_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.recentchanges_rc_id_seq OWNER TO website;

--
-- TOC entry 2239 (class 0 OID 0)
-- Dependencies: 1643
-- Name: recentchanges_rc_id_seq; Type: SEQUENCE SET; Schema: public; Owner: website
--

SELECT pg_catalog.setval('recentchanges_rc_id_seq', 1, false);


--
-- TOC entry 1644 (class 1259 OID 34366)
-- Dependencies: 1981 1982 1983 1984 1985 1986 1987 1988 3
-- Name: recentchanges; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE recentchanges (
    rc_id integer DEFAULT nextval('recentchanges_rc_id_seq'::regclass) NOT NULL,
    rc_timestamp timestamp with time zone NOT NULL,
    rc_cur_time timestamp with time zone NOT NULL,
    rc_user integer,
    rc_user_text text NOT NULL,
    rc_namespace smallint NOT NULL,
    rc_title text NOT NULL,
    rc_comment text,
    rc_minor smallint DEFAULT 0 NOT NULL,
    rc_bot smallint DEFAULT 0 NOT NULL,
    rc_new smallint DEFAULT 0 NOT NULL,
    rc_cur_id integer,
    rc_this_oldid integer NOT NULL,
    rc_last_oldid integer NOT NULL,
    rc_type smallint DEFAULT 0 NOT NULL,
    rc_moved_to_ns smallint,
    rc_moved_to_title text,
    rc_patrolled smallint DEFAULT 0 NOT NULL,
    rc_ip cidr,
    rc_old_len integer,
    rc_new_len integer,
    rc_deleted smallint DEFAULT 0 NOT NULL,
    rc_logid integer DEFAULT 0 NOT NULL,
    rc_log_type text,
    rc_log_action text,
    rc_params text
);


ALTER TABLE public.recentchanges OWNER TO website;

--
-- TOC entry 1627 (class 1259 OID 34146)
-- Dependencies: 3
-- Name: redirect; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE redirect (
    rd_from integer NOT NULL,
    rd_namespace smallint NOT NULL,
    rd_title text NOT NULL,
    rd_interwiki text,
    rd_fragment text
);


ALTER TABLE public.redirect OWNER TO website;

--
-- TOC entry 1619 (class 1259 OID 34060)
-- Dependencies: 3
-- Name: revision_rev_id_seq; Type: SEQUENCE; Schema: public; Owner: website
--

CREATE SEQUENCE revision_rev_id_seq
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.revision_rev_id_seq OWNER TO website;

--
-- TOC entry 2240 (class 0 OID 0)
-- Dependencies: 1619
-- Name: revision_rev_id_seq; Type: SEQUENCE SET; Schema: public; Owner: website
--

SELECT pg_catalog.setval('revision_rev_id_seq', 1, true);


--
-- TOC entry 1620 (class 1259 OID 34062)
-- Dependencies: 1943 1944 1945 3
-- Name: revision; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE revision (
    rev_id integer DEFAULT nextval('revision_rev_id_seq'::regclass) NOT NULL,
    rev_page integer,
    rev_text_id integer,
    rev_comment text,
    rev_user integer NOT NULL,
    rev_user_text text NOT NULL,
    rev_timestamp timestamp with time zone NOT NULL,
    rev_minor_edit smallint DEFAULT 0 NOT NULL,
    rev_deleted smallint DEFAULT 0 NOT NULL,
    rev_len integer,
    rev_parent_id integer
);


ALTER TABLE public.revision OWNER TO website;

--
-- TOC entry 1635 (class 1259 OID 34243)
-- Dependencies: 1950 1951 1952 1953 1954 1955 1956 1957 3
-- Name: site_stats; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE site_stats (
    ss_row_id integer NOT NULL,
    ss_total_views integer DEFAULT 0,
    ss_total_edits integer DEFAULT 0,
    ss_good_articles integer DEFAULT 0,
    ss_total_pages integer DEFAULT (-1),
    ss_users integer DEFAULT (-1),
    ss_active_users integer DEFAULT (-1),
    ss_admins integer DEFAULT (-1),
    ss_images integer DEFAULT 0
);


ALTER TABLE public.site_stats OWNER TO website;

--
-- TOC entry 1666 (class 1259 OID 34604)
-- Dependencies: 3
-- Name: tag_summary; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE tag_summary (
    ts_rc_id integer,
    ts_log_id integer,
    ts_rev_id integer,
    ts_tags text NOT NULL
);


ALTER TABLE public.tag_summary OWNER TO website;

--
-- TOC entry 1629 (class 1259 OID 34170)
-- Dependencies: 3
-- Name: templatelinks; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE templatelinks (
    tl_from integer NOT NULL,
    tl_namespace smallint NOT NULL,
    tl_title text NOT NULL
);


ALTER TABLE public.templatelinks OWNER TO website;

--
-- TOC entry 1656 (class 1259 OID 34510)
-- Dependencies: 3
-- Name: trackbacks_tb_id_seq; Type: SEQUENCE; Schema: public; Owner: website
--

CREATE SEQUENCE trackbacks_tb_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.trackbacks_tb_id_seq OWNER TO website;

--
-- TOC entry 2241 (class 0 OID 0)
-- Dependencies: 1656
-- Name: trackbacks_tb_id_seq; Type: SEQUENCE SET; Schema: public; Owner: website
--

SELECT pg_catalog.setval('trackbacks_tb_id_seq', 1, false);


--
-- TOC entry 1657 (class 1259 OID 34512)
-- Dependencies: 2001 3
-- Name: trackbacks; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE trackbacks (
    tb_id integer DEFAULT nextval('trackbacks_tb_id_seq'::regclass) NOT NULL,
    tb_page integer,
    tb_title text NOT NULL,
    tb_url text NOT NULL,
    tb_ex text,
    tb_name text
);


ALTER TABLE public.trackbacks OWNER TO website;

--
-- TOC entry 1652 (class 1259 OID 34468)
-- Dependencies: 3
-- Name: transcache; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE transcache (
    tc_url text NOT NULL,
    tc_contents text NOT NULL,
    tc_time timestamp with time zone NOT NULL
);


ALTER TABLE public.transcache OWNER TO website;

--
-- TOC entry 1662 (class 1259 OID 34569)
-- Dependencies: 3
-- Name: updatelog; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE updatelog (
    ul_key text NOT NULL
);


ALTER TABLE public.updatelog OWNER TO website;

--
-- TOC entry 1615 (class 1259 OID 34010)
-- Dependencies: 3
-- Name: user_groups; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE user_groups (
    ug_user integer,
    ug_group text NOT NULL
);


ALTER TABLE public.user_groups OWNER TO website;

--
-- TOC entry 1616 (class 1259 OID 34022)
-- Dependencies: 3
-- Name: user_newtalk; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE user_newtalk (
    user_id integer NOT NULL,
    user_ip text,
    user_last_timestamp timestamp with time zone
);


ALTER TABLE public.user_newtalk OWNER TO website;

--
-- TOC entry 1668 (class 1259 OID 34621)
-- Dependencies: 3
-- Name: user_properties; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE user_properties (
    up_user integer,
    up_property text NOT NULL,
    up_value text
);


ALTER TABLE public.user_properties OWNER TO website;

--
-- TOC entry 1667 (class 1259 OID 34613)
-- Dependencies: 3
-- Name: valid_tag; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE valid_tag (
    vt_tag text NOT NULL
);


ALTER TABLE public.valid_tag OWNER TO website;

--
-- TOC entry 1645 (class 1259 OID 34398)
-- Dependencies: 1989 3
-- Name: watchlist; Type: TABLE; Schema: public; Owner: website; Tablespace: 
--

CREATE TABLE watchlist (
    wl_user integer NOT NULL,
    wl_namespace smallint DEFAULT 0 NOT NULL,
    wl_title text NOT NULL,
    wl_notificationtimestamp timestamp with time zone
);


ALTER TABLE public.watchlist OWNER TO website;

--
-- TOC entry 2187 (class 0 OID 34131)
-- Dependencies: 1626
-- Data for Name: archive; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2218 (class 0 OID 34579)
-- Dependencies: 1664
-- Data for Name: category; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2192 (class 0 OID 34195)
-- Dependencies: 1631
-- Data for Name: categorylinks; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2219 (class 0 OID 34594)
-- Dependencies: 1665
-- Data for Name: change_tag; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2194 (class 0 OID 34221)
-- Dependencies: 1633
-- Data for Name: external_user; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2193 (class 0 OID 34208)
-- Dependencies: 1632
-- Data for Name: externallinks; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2201 (class 0 OID 34337)
-- Dependencies: 1642
-- Data for Name: filearchive; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2197 (class 0 OID 34256)
-- Dependencies: 1636
-- Data for Name: hitcounter; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2199 (class 0 OID 34291)
-- Dependencies: 1639
-- Data for Name: image; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2191 (class 0 OID 34183)
-- Dependencies: 1630
-- Data for Name: imagelinks; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2205 (class 0 OID 34420)
-- Dependencies: 1647
-- Data for Name: interwiki; Type: TABLE DATA; Schema: public; Owner: website
--

INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('acronym', 'http://www.acronymfinder.com/af-query.asp?String=exact&Acronym=$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('advogato', 'http://www.advogato.org/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('annotationwiki', 'http://www.seedwiki.com/page.cfm?wikiid=368&doc=$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('arxiv', 'http://www.arxiv.org/abs/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('c2find', 'http://c2.com/cgi/wiki?FindPage&value=$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('cache', 'http://www.google.com/search?q=cache:$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('commons', 'http://commons.wikimedia.org/wiki/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('corpknowpedia', 'http://corpknowpedia.org/wiki/index.php/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('dictionary', 'http://www.dict.org/bin/Dict?Database=*&Form=Dict1&Strategy=*&Query=$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('disinfopedia', 'http://www.disinfopedia.org/wiki.phtml?title=$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('docbook', 'http://wiki.docbook.org/topic/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('doi', 'http://dx.doi.org/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('drumcorpswiki', 'http://www.drumcorpswiki.com/index.php/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('dwjwiki', 'http://www.suberic.net/cgi-bin/dwj/wiki.cgi?$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('emacswiki', 'http://www.emacswiki.org/cgi-bin/wiki.pl?$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('elibre', 'http://enciclopedia.us.es/index.php/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('foldoc', 'http://foldoc.org/?$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('foxwiki', 'http://fox.wikis.com/wc.dll?Wiki~$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('freebsdman', 'http://www.FreeBSD.org/cgi/man.cgi?apropos=1&query=$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('gej', 'http://www.esperanto.de/cgi-bin/aktivikio/wiki.pl?$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('gentoo-wiki', 'http://gentoo-wiki.com/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('google', 'http://www.google.com/search?q=$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('googlegroups', 'http://groups.google.com/groups?q=$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('hammondwiki', 'http://www.dairiki.org/HammondWiki/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('hewikisource', 'http://he.wikisource.org/wiki/$1', 1, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('hrwiki', 'http://www.hrwiki.org/index.php/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('imdb', 'http://us.imdb.com/Title?$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('jargonfile', 'http://sunir.org/apps/meta.pl?wiki=JargonFile&redirect=$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('jspwiki', 'http://www.jspwiki.org/wiki/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('keiki', 'http://kei.ki/en/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('kmwiki', 'http://kmwiki.wikispaces.com/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('linuxwiki', 'http://linuxwiki.de/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('lojban', 'http://www.lojban.org/tiki/tiki-index.php?page=$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('lqwiki', 'http://wiki.linuxquestions.org/wiki/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('lugkr', 'http://lug-kr.sourceforge.net/cgi-bin/lugwiki.pl?$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('mathsongswiki', 'http://SeedWiki.com/page.cfm?wikiid=237&doc=$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('meatball', 'http://www.usemod.com/cgi-bin/mb.pl?$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('mediazilla', 'https://bugzilla.wikimedia.org/$1', 1, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('mediawikiwiki', 'http://www.mediawiki.org/wiki/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('memoryalpha', 'http://www.memory-alpha.org/en/index.php/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('metawiki', 'http://sunir.org/apps/meta.pl?$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('metawikipedia', 'http://meta.wikimedia.org/wiki/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('moinmoin', 'http://purl.net/wiki/moin/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('mozillawiki', 'http://wiki.mozilla.org/index.php/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('mw', 'http://www.mediawiki.org/wiki/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('oeis', 'http://www.research.att.com/cgi-bin/access.cgi/as/njas/sequences/eisA.cgi?Anum=$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('openfacts', 'http://openfacts.berlios.de/index.phtml?title=$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('openwiki', 'http://openwiki.com/?$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('patwiki', 'http://gauss.ffii.org/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('pmeg', 'http://www.bertilow.com/pmeg/$1.php', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('ppr', 'http://c2.com/cgi/wiki?$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('pythoninfo', 'http://wiki.python.org/moin/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('rfc', 'http://www.rfc-editor.org/rfc/rfc$1.txt', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('s23wiki', 'http://is-root.de/wiki/index.php/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('seattlewiki', 'http://seattle.wikia.com/wiki/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('seattlewireless', 'http://seattlewireless.net/?$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('senseislibrary', 'http://senseis.xmp.net/?$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('slashdot', 'http://slashdot.org/article.pl?sid=$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('sourceforge', 'http://sourceforge.net/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('squeak', 'http://wiki.squeak.org/squeak/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('susning', 'http://www.susning.nu/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('svgwiki', 'http://wiki.svg.org/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('tavi', 'http://tavi.sourceforge.net/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('tejo', 'http://www.tejo.org/vikio/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('tmbw', 'http://www.tmbw.net/wiki/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('tmnet', 'http://www.technomanifestos.net/?$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('tmwiki', 'http://www.EasyTopicMaps.com/?page=$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('theopedia', 'http://www.theopedia.com/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('twiki', 'http://twiki.org/cgi-bin/view/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('uea', 'http://www.tejo.org/uea/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('unreal', 'http://wiki.beyondunreal.com/wiki/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('usemod', 'http://www.usemod.com/cgi-bin/wiki.pl?$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('vinismo', 'http://vinismo.com/en/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('webseitzwiki', 'http://webseitz.fluxent.com/wiki/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('why', 'http://clublet.com/c/c/why?$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('wiki', 'http://c2.com/cgi/wiki?$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('wikia', 'http://www.wikia.com/wiki/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('wikibooks', 'http://en.wikibooks.org/wiki/$1', 1, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('wikicities', 'http://www.wikia.com/wiki/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('wikif1', 'http://www.wikif1.org/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('wikihow', 'http://www.wikihow.com/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('wikinfo', 'http://www.wikinfo.org/index.php/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('wikimedia', 'http://wikimediafoundation.org/wiki/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('wikinews', 'http://en.wikinews.org/wiki/$1', 1, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('wikiquote', 'http://en.wikiquote.org/wiki/$1', 1, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('wikisource', 'http://wikisource.org/wiki/$1', 1, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('wikispecies', 'http://species.wikimedia.org/wiki/$1', 1, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('wikitravel', 'http://wikitravel.org/en/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('wikiversity', 'http://en.wikiversity.org/wiki/$1', 1, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('wikt', 'http://en.wiktionary.org/wiki/$1', 1, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('wiktionary', 'http://en.wiktionary.org/wiki/$1', 1, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('wlug', 'http://www.wlug.org.nz/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('zwiki', 'http://zwiki.org/$1', 0, 0);
INSERT INTO interwiki (iw_prefix, iw_url, iw_local, iw_trans) VALUES ('zzz wiki', 'http://wiki.zzz.ee/index.php/$1', 0, 0);


--
-- TOC entry 2198 (class 0 OID 34261)
-- Dependencies: 1638
-- Data for Name: ipblocks; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2214 (class 0 OID 34529)
-- Dependencies: 1659
-- Data for Name: job; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2224 (class 0 OID 34641)
-- Dependencies: 1670
-- Data for Name: l10n_cache; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2195 (class 0 OID 34230)
-- Dependencies: 1634
-- Data for Name: langlinks; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2212 (class 0 OID 34500)
-- Dependencies: 1655
-- Data for Name: log_search; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2211 (class 0 OID 34478)
-- Dependencies: 1654
-- Data for Name: logging; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2204 (class 0 OID 34412)
-- Dependencies: 1646
-- Data for Name: math; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2223 (class 0 OID 34634)
-- Dependencies: 1669
-- Data for Name: mediawiki_version; Type: TABLE DATA; Schema: public; Owner: website
--

INSERT INTO mediawiki_version (type, mw_version, notes, pg_version, pg_dbname, pg_user, pg_port, mw_schema, ts2_schema, ctype, sql_version, sql_date, cdate) VALUES ('Creation', '1.16.2', NULL, '8.3.14', 'catrowiki', 'website', '5432', 'public', 'public', 'German_Germany.1252', '$LastChangedRevision: 59842 $', '$LastChangedDate: 2009-12-09 06:32:17 +1100 (Wed, 09 Dec 2009) $', '2011-02-16 18:45:45.695+01');


--
-- TOC entry 2179 (class 0 OID 33998)
-- Dependencies: 1614
-- Data for Name: mwuser; Type: TABLE DATA; Schema: public; Owner: website
--

INSERT INTO mwuser (user_id, user_name, user_real_name, user_password, user_newpassword, user_newpass_time, user_token, user_email, user_email_token, user_email_token_expires, user_email_authenticated, user_options, user_touched, user_registration, user_editcount) VALUES (0, 'Anonymous', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2011-02-16 17:45:45.695+00', '2011-02-16 18:45:45.695+01', NULL, NULL);
INSERT INTO mwuser (user_id, user_name, user_real_name, user_password, user_newpassword, user_newpass_time, user_token, user_email, user_email_token, user_email_token_expires, user_email_authenticated, user_options, user_touched, user_registration, user_editcount) VALUES (1, 'Catroweb', '', ':B:4ec2eff9:2218396d584099d1d54e93a99aca7291', '', NULL, 'd6323912a47c6aab74e812842d674d7b', '', '', NULL, NULL, '', '2011-02-16 18:45:51+01', '2011-02-16 18:45:46+01', 0);


--
-- TOC entry 2209 (class 0 OID 34458)
-- Dependencies: 1651
-- Data for Name: objectcache; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2200 (class 0 OID 34311)
-- Dependencies: 1640
-- Data for Name: oldimage; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2182 (class 0 OID 34037)
-- Dependencies: 1618
-- Data for Name: page; Type: TABLE DATA; Schema: public; Owner: website
--

INSERT INTO page (page_id, page_namespace, page_title, page_restrictions, page_counter, page_is_redirect, page_is_new, page_random, page_touched, page_latest, page_len, titlevector) VALUES (1, 0, 'Main_Page', '', 0, 0, 0, 0.65475511108000, '2011-02-16 18:45:46+01', 1, 438, '''pag'':2 ''main'':1');


--
-- TOC entry 2186 (class 0 OID 34117)
-- Dependencies: 1625
-- Data for Name: page_props; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2185 (class 0 OID 34101)
-- Dependencies: 1624
-- Data for Name: page_restrictions; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2184 (class 0 OID 34090)
-- Dependencies: 1622
-- Data for Name: pagecontent; Type: TABLE DATA; Schema: public; Owner: website
--

INSERT INTO pagecontent (old_id, old_text, old_flags, textvector) VALUES (1, '''''''MediaWiki has been successfully installed.''''''

Consult the [http://meta.wikimedia.org/wiki/Help:Contents User''s Guide] for information on using the wiki software.

== Getting started ==
* [http://www.mediawiki.org/wiki/Manual:Configuration_settings Configuration settings list]
* [http://www.mediawiki.org/wiki/Manual:FAQ MediaWiki FAQ]
* [https://lists.wikimedia.org/mailman/listinfo/mediawiki-announce MediaWiki release mailing list]', 'utf-8', '''s'':12 ''on'':16 ''faq'':33 ''for'':14 ''has'':2 ''the'':7,18 ''been'':3 ''guid'':13 ''list'':28,40 ''user'':11 ''wiki'':19 ''using'':17 ''releas'':38 ''consult'':6 ''getting'':21 ''mailing'':39 ''setting'':27 ''softwar'':20 ''started'':22 ''installed'':5 ''mediawiki'':1,32,37 ''information'':15 ''successfully'':4 ''configuration'':26 ''/wiki/manual:faq'':31 ''www.mediawiki.org'':24,30 ''meta.wikimedia.org'':9 ''/wiki/help:contents'':10 ''lists.wikimedia.org'':35 ''meta.wikimedia.org/wiki/help'':8 ''www.mediawiki.org/wiki/manual'':23,29 ''/wiki/manual:configuration_settings'':25 ''/mailman/listinfo/mediawiki-announce'':36 ''lists.wikimedia.org/mailman/listinfo/mediawiki-announce'':34');


--
-- TOC entry 2189 (class 0 OID 34158)
-- Dependencies: 1628
-- Data for Name: pagelinks; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2215 (class 0 OID 34546)
-- Dependencies: 1660
-- Data for Name: profiling; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2216 (class 0 OID 34556)
-- Dependencies: 1661
-- Data for Name: protected_titles; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2206 (class 0 OID 34429)
-- Dependencies: 1648
-- Data for Name: querycache; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2207 (class 0 OID 34436)
-- Dependencies: 1649
-- Data for Name: querycache_info; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2208 (class 0 OID 34444)
-- Dependencies: 1650
-- Data for Name: querycachetwo; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2202 (class 0 OID 34366)
-- Dependencies: 1644
-- Data for Name: recentchanges; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2188 (class 0 OID 34146)
-- Dependencies: 1627
-- Data for Name: redirect; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2183 (class 0 OID 34062)
-- Dependencies: 1620
-- Data for Name: revision; Type: TABLE DATA; Schema: public; Owner: website
--

INSERT INTO revision (rev_id, rev_page, rev_text_id, rev_comment, rev_user, rev_user_text, rev_timestamp, rev_minor_edit, rev_deleted, rev_len, rev_parent_id) VALUES (1, 1, 1, '', 0, 'MediaWiki default', '2011-02-16 18:45:46+01', 0, 0, 438, 0);


--
-- TOC entry 2196 (class 0 OID 34243)
-- Dependencies: 1635
-- Data for Name: site_stats; Type: TABLE DATA; Schema: public; Owner: website
--

INSERT INTO site_stats (ss_row_id, ss_total_views, ss_total_edits, ss_good_articles, ss_total_pages, ss_users, ss_active_users, ss_admins, ss_images) VALUES (1, 0, 1, 0, 1, 1, -1, 1, 0);


--
-- TOC entry 2220 (class 0 OID 34604)
-- Dependencies: 1666
-- Data for Name: tag_summary; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2190 (class 0 OID 34170)
-- Dependencies: 1629
-- Data for Name: templatelinks; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2213 (class 0 OID 34512)
-- Dependencies: 1657
-- Data for Name: trackbacks; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2210 (class 0 OID 34468)
-- Dependencies: 1652
-- Data for Name: transcache; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2217 (class 0 OID 34569)
-- Dependencies: 1662
-- Data for Name: updatelog; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2180 (class 0 OID 34010)
-- Dependencies: 1615
-- Data for Name: user_groups; Type: TABLE DATA; Schema: public; Owner: website
--

INSERT INTO user_groups (ug_user, ug_group) VALUES (1, 'sysop');
INSERT INTO user_groups (ug_user, ug_group) VALUES (1, 'bureaucrat');


--
-- TOC entry 2181 (class 0 OID 34022)
-- Dependencies: 1616
-- Data for Name: user_newtalk; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2222 (class 0 OID 34621)
-- Dependencies: 1668
-- Data for Name: user_properties; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2221 (class 0 OID 34613)
-- Dependencies: 1667
-- Data for Name: valid_tag; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2203 (class 0 OID 34398)
-- Dependencies: 1645
-- Data for Name: watchlist; Type: TABLE DATA; Schema: public; Owner: website
--



--
-- TOC entry 2134 (class 2606 OID 34591)
-- Dependencies: 1664 1664
-- Name: category_pkey; Type: CONSTRAINT; Schema: public; Owner: website; Tablespace: 
--

ALTER TABLE ONLY category
    ADD CONSTRAINT category_pkey PRIMARY KEY (cat_id);


--
-- TOC entry 2062 (class 2606 OID 34228)
-- Dependencies: 1633 1633
-- Name: external_user_pkey; Type: CONSTRAINT; Schema: public; Owner: website; Tablespace: 
--

ALTER TABLE ONLY external_user
    ADD CONSTRAINT external_user_pkey PRIMARY KEY (eu_local_id);


--
-- TOC entry 2085 (class 2606 OID 34349)
-- Dependencies: 1642 1642
-- Name: filearchive_pkey; Type: CONSTRAINT; Schema: public; Owner: website; Tablespace: 
--

ALTER TABLE ONLY filearchive
    ADD CONSTRAINT filearchive_pkey PRIMARY KEY (fa_id);


--
-- TOC entry 2073 (class 2606 OID 34302)
-- Dependencies: 1639 1639
-- Name: image_pkey; Type: CONSTRAINT; Schema: public; Owner: website; Tablespace: 
--

ALTER TABLE ONLY image
    ADD CONSTRAINT image_pkey PRIMARY KEY (img_name);


--
-- TOC entry 2099 (class 2606 OID 34428)
-- Dependencies: 1647 1647
-- Name: interwiki_iw_prefix_key; Type: CONSTRAINT; Schema: public; Owner: website; Tablespace: 
--

ALTER TABLE ONLY interwiki
    ADD CONSTRAINT interwiki_iw_prefix_key UNIQUE (iw_prefix);


--
-- TOC entry 2071 (class 2606 OID 34277)
-- Dependencies: 1638 1638
-- Name: ipblocks_pkey; Type: CONSTRAINT; Schema: public; Owner: website; Tablespace: 
--

ALTER TABLE ONLY ipblocks
    ADD CONSTRAINT ipblocks_pkey PRIMARY KEY (ipb_id);


--
-- TOC entry 2127 (class 2606 OID 34537)
-- Dependencies: 1659 1659
-- Name: job_pkey; Type: CONSTRAINT; Schema: public; Owner: website; Tablespace: 
--

ALTER TABLE ONLY job
    ADD CONSTRAINT job_pkey PRIMARY KEY (job_id);


--
-- TOC entry 2120 (class 2606 OID 34508)
-- Dependencies: 1655 1655 1655 1655
-- Name: log_search_pkey; Type: CONSTRAINT; Schema: public; Owner: website; Tablespace: 
--

ALTER TABLE ONLY log_search
    ADD CONSTRAINT log_search_pkey PRIMARY KEY (ls_field, ls_value, ls_log_id);


--
-- TOC entry 2114 (class 2606 OID 34488)
-- Dependencies: 1654 1654
-- Name: logging_pkey; Type: CONSTRAINT; Schema: public; Owner: website; Tablespace: 
--

ALTER TABLE ONLY logging
    ADD CONSTRAINT logging_pkey PRIMARY KEY (log_id);


--
-- TOC entry 2097 (class 2606 OID 34419)
-- Dependencies: 1646 1646
-- Name: math_math_inputhash_key; Type: CONSTRAINT; Schema: public; Owner: website; Tablespace: 
--

ALTER TABLE ONLY math
    ADD CONSTRAINT math_math_inputhash_key UNIQUE (math_inputhash);


--
-- TOC entry 2014 (class 2606 OID 34006)
-- Dependencies: 1614 1614
-- Name: mwuser_pkey; Type: CONSTRAINT; Schema: public; Owner: website; Tablespace: 
--

ALTER TABLE ONLY mwuser
    ADD CONSTRAINT mwuser_pkey PRIMARY KEY (user_id);


--
-- TOC entry 2016 (class 2606 OID 34008)
-- Dependencies: 1614 1614
-- Name: mwuser_user_name_key; Type: CONSTRAINT; Schema: public; Owner: website; Tablespace: 
--

ALTER TABLE ONLY mwuser
    ADD CONSTRAINT mwuser_user_name_key UNIQUE (user_name);


--
-- TOC entry 2108 (class 2606 OID 34466)
-- Dependencies: 1651 1651
-- Name: objectcache_keyname_key; Type: CONSTRAINT; Schema: public; Owner: website; Tablespace: 
--

ALTER TABLE ONLY objectcache
    ADD CONSTRAINT objectcache_keyname_key UNIQUE (keyname);


--
-- TOC entry 2024 (class 2606 OID 34049)
-- Dependencies: 1618 1618
-- Name: page_pkey; Type: CONSTRAINT; Schema: public; Owner: website; Tablespace: 
--

ALTER TABLE ONLY page
    ADD CONSTRAINT page_pkey PRIMARY KEY (page_id);


--
-- TOC entry 2047 (class 2606 OID 34129)
-- Dependencies: 1625 1625 1625
-- Name: page_props_pk; Type: CONSTRAINT; Schema: public; Owner: website; Tablespace: 
--

ALTER TABLE ONLY page_props
    ADD CONSTRAINT page_props_pk PRIMARY KEY (pp_page, pp_propname);


--
-- TOC entry 2043 (class 2606 OID 34116)
-- Dependencies: 1624 1624 1624
-- Name: page_restrictions_pk; Type: CONSTRAINT; Schema: public; Owner: website; Tablespace: 
--

ALTER TABLE ONLY page_restrictions
    ADD CONSTRAINT page_restrictions_pk PRIMARY KEY (pr_page, pr_type);


--
-- TOC entry 2045 (class 2606 OID 34109)
-- Dependencies: 1624 1624
-- Name: page_restrictions_pr_id_key; Type: CONSTRAINT; Schema: public; Owner: website; Tablespace: 
--

ALTER TABLE ONLY page_restrictions
    ADD CONSTRAINT page_restrictions_pr_id_key UNIQUE (pr_id);


--
-- TOC entry 2040 (class 2606 OID 34098)
-- Dependencies: 1622 1622
-- Name: pagecontent_pkey; Type: CONSTRAINT; Schema: public; Owner: website; Tablespace: 
--

ALTER TABLE ONLY pagecontent
    ADD CONSTRAINT pagecontent_pkey PRIMARY KEY (old_id);


--
-- TOC entry 2102 (class 2606 OID 34443)
-- Dependencies: 1649 1649
-- Name: querycache_info_qci_type_key; Type: CONSTRAINT; Schema: public; Owner: website; Tablespace: 
--

ALTER TABLE ONLY querycache_info
    ADD CONSTRAINT querycache_info_qci_type_key UNIQUE (qci_type);


--
-- TOC entry 2093 (class 2606 OID 34381)
-- Dependencies: 1644 1644
-- Name: recentchanges_pkey; Type: CONSTRAINT; Schema: public; Owner: website; Tablespace: 
--

ALTER TABLE ONLY recentchanges
    ADD CONSTRAINT recentchanges_pkey PRIMARY KEY (rc_id);


--
-- TOC entry 2037 (class 2606 OID 34072)
-- Dependencies: 1620 1620
-- Name: revision_rev_id_key; Type: CONSTRAINT; Schema: public; Owner: website; Tablespace: 
--

ALTER TABLE ONLY revision
    ADD CONSTRAINT revision_rev_id_key UNIQUE (rev_id);


--
-- TOC entry 2066 (class 2606 OID 34255)
-- Dependencies: 1635 1635
-- Name: site_stats_ss_row_id_key; Type: CONSTRAINT; Schema: public; Owner: website; Tablespace: 
--

ALTER TABLE ONLY site_stats
    ADD CONSTRAINT site_stats_ss_row_id_key UNIQUE (ss_row_id);


--
-- TOC entry 2124 (class 2606 OID 34520)
-- Dependencies: 1657 1657
-- Name: trackbacks_pkey; Type: CONSTRAINT; Schema: public; Owner: website; Tablespace: 
--

ALTER TABLE ONLY trackbacks
    ADD CONSTRAINT trackbacks_pkey PRIMARY KEY (tb_id);


--
-- TOC entry 2110 (class 2606 OID 34475)
-- Dependencies: 1652 1652
-- Name: transcache_tc_url_key; Type: CONSTRAINT; Schema: public; Owner: website; Tablespace: 
--

ALTER TABLE ONLY transcache
    ADD CONSTRAINT transcache_tc_url_key UNIQUE (tc_url);


--
-- TOC entry 2131 (class 2606 OID 34576)
-- Dependencies: 1662 1662
-- Name: updatelog_pkey; Type: CONSTRAINT; Schema: public; Owner: website; Tablespace: 
--

ALTER TABLE ONLY updatelog
    ADD CONSTRAINT updatelog_pkey PRIMARY KEY (ul_key);


--
-- TOC entry 2144 (class 2606 OID 34620)
-- Dependencies: 1667 1667
-- Name: valid_tag_pkey; Type: CONSTRAINT; Schema: public; Owner: website; Tablespace: 
--

ALTER TABLE ONLY valid_tag
    ADD CONSTRAINT valid_tag_pkey PRIMARY KEY (vt_tag);


--
-- TOC entry 2049 (class 1259 OID 34144)
-- Dependencies: 1626 1626 1626
-- Name: archive_name_title_timestamp; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX archive_name_title_timestamp ON archive USING btree (ar_namespace, ar_title, ar_timestamp);


--
-- TOC entry 2050 (class 1259 OID 34145)
-- Dependencies: 1626
-- Name: archive_user_text; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX archive_user_text ON archive USING btree (ar_user_text);


--
-- TOC entry 2132 (class 1259 OID 34593)
-- Dependencies: 1664
-- Name: category_pages; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX category_pages ON category USING btree (cat_pages);


--
-- TOC entry 2135 (class 1259 OID 34592)
-- Dependencies: 1664
-- Name: category_title; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE UNIQUE INDEX category_title ON category USING btree (cat_title);


--
-- TOC entry 2136 (class 1259 OID 34601)
-- Dependencies: 1665 1665
-- Name: change_tag_log_tag; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE UNIQUE INDEX change_tag_log_tag ON change_tag USING btree (ct_log_id, ct_tag);


--
-- TOC entry 2137 (class 1259 OID 34600)
-- Dependencies: 1665 1665
-- Name: change_tag_rc_tag; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE UNIQUE INDEX change_tag_rc_tag ON change_tag USING btree (ct_rc_id, ct_tag);


--
-- TOC entry 2138 (class 1259 OID 34602)
-- Dependencies: 1665 1665
-- Name: change_tag_rev_tag; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE UNIQUE INDEX change_tag_rev_tag ON change_tag USING btree (ct_rev_id, ct_tag);


--
-- TOC entry 2139 (class 1259 OID 34603)
-- Dependencies: 1665 1665 1665 1665
-- Name: change_tag_tag_id; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX change_tag_tag_id ON change_tag USING btree (ct_tag, ct_rc_id, ct_rev_id, ct_log_id);


--
-- TOC entry 2056 (class 1259 OID 34206)
-- Dependencies: 1631 1631
-- Name: cl_from; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE UNIQUE INDEX cl_from ON categorylinks USING btree (cl_from, cl_to);


--
-- TOC entry 2057 (class 1259 OID 34207)
-- Dependencies: 1631 1631 1631
-- Name: cl_sortkey; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX cl_sortkey ON categorylinks USING btree (cl_to, cl_sortkey, cl_from);


--
-- TOC entry 2060 (class 1259 OID 34229)
-- Dependencies: 1633
-- Name: eu_external_id; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE UNIQUE INDEX eu_external_id ON external_user USING btree (eu_external_id);


--
-- TOC entry 2058 (class 1259 OID 34219)
-- Dependencies: 1632 1632
-- Name: externallinks_from_to; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX externallinks_from_to ON externallinks USING btree (el_from, el_to);


--
-- TOC entry 2059 (class 1259 OID 34220)
-- Dependencies: 1632
-- Name: externallinks_index; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX externallinks_index ON externallinks USING btree (el_index);


--
-- TOC entry 2080 (class 1259 OID 34361)
-- Dependencies: 1642 1642
-- Name: fa_dupe; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX fa_dupe ON filearchive USING btree (fa_storage_group, fa_storage_key);


--
-- TOC entry 2081 (class 1259 OID 34360)
-- Dependencies: 1642 1642
-- Name: fa_name_time; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX fa_name_time ON filearchive USING btree (fa_name, fa_timestamp);


--
-- TOC entry 2082 (class 1259 OID 34362)
-- Dependencies: 1642
-- Name: fa_notime; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX fa_notime ON filearchive USING btree (fa_deleted_timestamp);


--
-- TOC entry 2083 (class 1259 OID 34363)
-- Dependencies: 1642
-- Name: fa_nouser; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX fa_nouser ON filearchive USING btree (fa_deleted_user);


--
-- TOC entry 2055 (class 1259 OID 34194)
-- Dependencies: 1630 1630
-- Name: il_from; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE UNIQUE INDEX il_from ON imagelinks USING btree (il_to, il_from);


--
-- TOC entry 2074 (class 1259 OID 34310)
-- Dependencies: 1639
-- Name: img_sha1; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX img_sha1 ON image USING btree (img_sha1);


--
-- TOC entry 2075 (class 1259 OID 34308)
-- Dependencies: 1639
-- Name: img_size_idx; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX img_size_idx ON image USING btree (img_size);


--
-- TOC entry 2076 (class 1259 OID 34309)
-- Dependencies: 1639
-- Name: img_timestamp_idx; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX img_timestamp_idx ON image USING btree (img_timestamp);


--
-- TOC entry 2067 (class 1259 OID 34288)
-- Dependencies: 1638 1638 1638 1638
-- Name: ipb_address_unique; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE UNIQUE INDEX ipb_address_unique ON ipblocks USING btree (ipb_address, ipb_user, ipb_auto, ipb_anon_only);


--
-- TOC entry 2068 (class 1259 OID 34290)
-- Dependencies: 1638 1638
-- Name: ipb_range; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX ipb_range ON ipblocks USING btree (ipb_range_start, ipb_range_end);


--
-- TOC entry 2069 (class 1259 OID 34289)
-- Dependencies: 1638
-- Name: ipb_user; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX ipb_user ON ipblocks USING btree (ipb_user);


--
-- TOC entry 2125 (class 1259 OID 34538)
-- Dependencies: 1659 1659 1659
-- Name: job_cmd_namespace_title; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX job_cmd_namespace_title ON job USING btree (job_cmd, job_namespace, job_title);


--
-- TOC entry 2147 (class 1259 OID 34647)
-- Dependencies: 1670 1670
-- Name: l10n_cache_lc_lang_key; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX l10n_cache_lc_lang_key ON l10n_cache USING btree (lc_lang, lc_key);


--
-- TOC entry 2063 (class 1259 OID 34242)
-- Dependencies: 1634 1634
-- Name: langlinks_lang_title; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX langlinks_lang_title ON langlinks USING btree (ll_lang, ll_title);


--
-- TOC entry 2064 (class 1259 OID 34241)
-- Dependencies: 1634 1634
-- Name: langlinks_unique; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE UNIQUE INDEX langlinks_unique ON langlinks USING btree (ll_from, ll_lang);


--
-- TOC entry 2111 (class 1259 OID 34499)
-- Dependencies: 1654 1654
-- Name: logging_page_id_time; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX logging_page_id_time ON logging USING btree (log_page, log_timestamp);


--
-- TOC entry 2112 (class 1259 OID 34496)
-- Dependencies: 1654 1654 1654
-- Name: logging_page_time; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX logging_page_time ON logging USING btree (log_namespace, log_title, log_timestamp);


--
-- TOC entry 2115 (class 1259 OID 34497)
-- Dependencies: 1654
-- Name: logging_times; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX logging_times ON logging USING btree (log_timestamp);


--
-- TOC entry 2116 (class 1259 OID 34494)
-- Dependencies: 1654 1654
-- Name: logging_type_name; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX logging_type_name ON logging USING btree (log_type, log_timestamp);


--
-- TOC entry 2117 (class 1259 OID 34495)
-- Dependencies: 1654 1654
-- Name: logging_user_time; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX logging_user_time ON logging USING btree (log_timestamp, log_user);


--
-- TOC entry 2118 (class 1259 OID 34498)
-- Dependencies: 1654 1654 1654
-- Name: logging_user_type_time; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX logging_user_type_time ON logging USING btree (log_user, log_type, log_timestamp);


--
-- TOC entry 2121 (class 1259 OID 34509)
-- Dependencies: 1655
-- Name: ls_log_id; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX ls_log_id ON log_search USING btree (ls_log_id);


--
-- TOC entry 2086 (class 1259 OID 34396)
-- Dependencies: 1644 1644 1644
-- Name: new_name_timestamp; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX new_name_timestamp ON recentchanges USING btree (rc_new, rc_namespace, rc_timestamp);


--
-- TOC entry 2106 (class 1259 OID 34467)
-- Dependencies: 1651
-- Name: objectcacache_exptime; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX objectcacache_exptime ON objectcache USING btree (exptime);


--
-- TOC entry 2077 (class 1259 OID 34333)
-- Dependencies: 1640 1640
-- Name: oi_name_archive_name; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX oi_name_archive_name ON oldimage USING btree (oi_name, oi_archive_name);


--
-- TOC entry 2078 (class 1259 OID 34332)
-- Dependencies: 1640 1640
-- Name: oi_name_timestamp; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX oi_name_timestamp ON oldimage USING btree (oi_name, oi_timestamp);


--
-- TOC entry 2079 (class 1259 OID 34334)
-- Dependencies: 1640
-- Name: oi_sha1; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX oi_sha1 ON oldimage USING btree (oi_sha1);


--
-- TOC entry 2021 (class 1259 OID 34057)
-- Dependencies: 1618
-- Name: page_len_idx; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX page_len_idx ON page USING btree (page_len);


--
-- TOC entry 2022 (class 1259 OID 34051)
-- Dependencies: 1618 1618
-- Name: page_main_title; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX page_main_title ON page USING btree (page_title) WHERE (page_namespace = 0);


--
-- TOC entry 2025 (class 1259 OID 34055)
-- Dependencies: 1618 1618
-- Name: page_project_title; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX page_project_title ON page USING btree (page_title) WHERE (page_namespace = 4);


--
-- TOC entry 2048 (class 1259 OID 34130)
-- Dependencies: 1625
-- Name: page_props_propname; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX page_props_propname ON page_props USING btree (pp_propname);


--
-- TOC entry 2026 (class 1259 OID 34056)
-- Dependencies: 1618
-- Name: page_random_idx; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX page_random_idx ON page USING btree (page_random);


--
-- TOC entry 2027 (class 1259 OID 34052)
-- Dependencies: 1618 1618
-- Name: page_talk_title; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX page_talk_title ON page USING btree (page_title) WHERE (page_namespace = 1);


--
-- TOC entry 2028 (class 1259 OID 34050)
-- Dependencies: 1618 1618
-- Name: page_unique_name; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE UNIQUE INDEX page_unique_name ON page USING btree (page_namespace, page_title);


--
-- TOC entry 2029 (class 1259 OID 34053)
-- Dependencies: 1618 1618
-- Name: page_user_title; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX page_user_title ON page USING btree (page_title) WHERE (page_namespace = 2);


--
-- TOC entry 2030 (class 1259 OID 34054)
-- Dependencies: 1618 1618
-- Name: page_utalk_title; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX page_utalk_title ON page USING btree (page_title) WHERE (page_namespace = 3);


--
-- TOC entry 2052 (class 1259 OID 34169)
-- Dependencies: 1628 1628 1628
-- Name: pagelink_unique; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE UNIQUE INDEX pagelink_unique ON pagelinks USING btree (pl_from, pl_namespace, pl_title);


--
-- TOC entry 2128 (class 1259 OID 34555)
-- Dependencies: 1660 1660
-- Name: pf_name_server; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE UNIQUE INDEX pf_name_server ON profiling USING btree (pf_name, pf_server);


--
-- TOC entry 2129 (class 1259 OID 34568)
-- Dependencies: 1661 1661
-- Name: protected_titles_unique; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE UNIQUE INDEX protected_titles_unique ON protected_titles USING btree (pt_namespace, pt_title);


--
-- TOC entry 2100 (class 1259 OID 34435)
-- Dependencies: 1648 1648
-- Name: querycache_type_value; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX querycache_type_value ON querycache USING btree (qc_type, qc_value);


--
-- TOC entry 2103 (class 1259 OID 34456)
-- Dependencies: 1650 1650 1650
-- Name: querycachetwo_title; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX querycachetwo_title ON querycachetwo USING btree (qcc_type, qcc_namespace, qcc_title);


--
-- TOC entry 2104 (class 1259 OID 34457)
-- Dependencies: 1650 1650 1650
-- Name: querycachetwo_titletwo; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX querycachetwo_titletwo ON querycachetwo USING btree (qcc_type, qcc_namespacetwo, qcc_titletwo);


--
-- TOC entry 2105 (class 1259 OID 34455)
-- Dependencies: 1650 1650
-- Name: querycachetwo_type_value; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX querycachetwo_type_value ON querycachetwo USING btree (qcc_type, qcc_value);


--
-- TOC entry 2087 (class 1259 OID 34395)
-- Dependencies: 1644
-- Name: rc_cur_id; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX rc_cur_id ON recentchanges USING btree (rc_cur_id);


--
-- TOC entry 2088 (class 1259 OID 34397)
-- Dependencies: 1644
-- Name: rc_ip; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX rc_ip ON recentchanges USING btree (rc_ip);


--
-- TOC entry 2089 (class 1259 OID 34394)
-- Dependencies: 1644 1644
-- Name: rc_namespace_title; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX rc_namespace_title ON recentchanges USING btree (rc_namespace, rc_title);


--
-- TOC entry 2090 (class 1259 OID 34392)
-- Dependencies: 1644
-- Name: rc_timestamp; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX rc_timestamp ON recentchanges USING btree (rc_timestamp);


--
-- TOC entry 2091 (class 1259 OID 34393)
-- Dependencies: 1644 1644
-- Name: rc_timestamp_bot; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX rc_timestamp_bot ON recentchanges USING btree (rc_timestamp) WHERE (rc_bot = 0);


--
-- TOC entry 2051 (class 1259 OID 34157)
-- Dependencies: 1627 1627 1627
-- Name: redirect_ns_title; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX redirect_ns_title ON redirect USING btree (rd_namespace, rd_title, rd_from);


--
-- TOC entry 2032 (class 1259 OID 34084)
-- Dependencies: 1620
-- Name: rev_text_id_idx; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX rev_text_id_idx ON revision USING btree (rev_text_id);


--
-- TOC entry 2033 (class 1259 OID 34085)
-- Dependencies: 1620
-- Name: rev_timestamp_idx; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX rev_timestamp_idx ON revision USING btree (rev_timestamp);


--
-- TOC entry 2034 (class 1259 OID 34086)
-- Dependencies: 1620
-- Name: rev_user_idx; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX rev_user_idx ON revision USING btree (rev_user);


--
-- TOC entry 2035 (class 1259 OID 34087)
-- Dependencies: 1620
-- Name: rev_user_text_idx; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX rev_user_text_idx ON revision USING btree (rev_user_text);


--
-- TOC entry 2038 (class 1259 OID 34083)
-- Dependencies: 1620 1620
-- Name: revision_unique; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE UNIQUE INDEX revision_unique ON revision USING btree (rev_page, rev_id);


--
-- TOC entry 2140 (class 1259 OID 34611)
-- Dependencies: 1666
-- Name: tag_summary_log_id; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE UNIQUE INDEX tag_summary_log_id ON tag_summary USING btree (ts_log_id);


--
-- TOC entry 2141 (class 1259 OID 34610)
-- Dependencies: 1666
-- Name: tag_summary_rc_id; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE UNIQUE INDEX tag_summary_rc_id ON tag_summary USING btree (ts_rc_id);


--
-- TOC entry 2142 (class 1259 OID 34612)
-- Dependencies: 1666
-- Name: tag_summary_rev_id; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE UNIQUE INDEX tag_summary_rev_id ON tag_summary USING btree (ts_rev_id);


--
-- TOC entry 2053 (class 1259 OID 34182)
-- Dependencies: 1629
-- Name: templatelinks_from; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX templatelinks_from ON templatelinks USING btree (tl_from);


--
-- TOC entry 2054 (class 1259 OID 34181)
-- Dependencies: 1629 1629 1629
-- Name: templatelinks_unique; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE UNIQUE INDEX templatelinks_unique ON templatelinks USING btree (tl_namespace, tl_title, tl_from);


--
-- TOC entry 2122 (class 1259 OID 34526)
-- Dependencies: 1657
-- Name: trackback_page; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX trackback_page ON trackbacks USING btree (tb_page);


--
-- TOC entry 2041 (class 1259 OID 34544)
-- Dependencies: 1622
-- Name: ts2_page_text; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX ts2_page_text ON pagecontent USING gin (textvector);


--
-- TOC entry 2031 (class 1259 OID 34543)
-- Dependencies: 1618
-- Name: ts2_page_title; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX ts2_page_title ON page USING gin (titlevector);


--
-- TOC entry 2017 (class 1259 OID 34009)
-- Dependencies: 1614
-- Name: user_email_token_idx; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX user_email_token_idx ON mwuser USING btree (user_email_token);


--
-- TOC entry 2018 (class 1259 OID 34021)
-- Dependencies: 1615 1615
-- Name: user_groups_unique; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE UNIQUE INDEX user_groups_unique ON user_groups USING btree (ug_user, ug_group);


--
-- TOC entry 2019 (class 1259 OID 34033)
-- Dependencies: 1616
-- Name: user_newtalk_id_idx; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX user_newtalk_id_idx ON user_newtalk USING btree (user_id);


--
-- TOC entry 2020 (class 1259 OID 34034)
-- Dependencies: 1616
-- Name: user_newtalk_ip_idx; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX user_newtalk_ip_idx ON user_newtalk USING btree (user_ip);


--
-- TOC entry 2145 (class 1259 OID 34633)
-- Dependencies: 1668
-- Name: user_properties_property; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX user_properties_property ON user_properties USING btree (up_property);


--
-- TOC entry 2146 (class 1259 OID 34632)
-- Dependencies: 1668 1668
-- Name: user_properties_user_property; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE UNIQUE INDEX user_properties_user_property ON user_properties USING btree (up_user, up_property);


--
-- TOC entry 2094 (class 1259 OID 34411)
-- Dependencies: 1645
-- Name: wl_user; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE INDEX wl_user ON watchlist USING btree (wl_user);


--
-- TOC entry 2095 (class 1259 OID 34410)
-- Dependencies: 1645 1645 1645
-- Name: wl_user_namespace_title; Type: INDEX; Schema: public; Owner: website; Tablespace: 
--

CREATE UNIQUE INDEX wl_user_namespace_title ON watchlist USING btree (wl_namespace, wl_title, wl_user);


--
-- TOC entry 2176 (class 2620 OID 34059)
-- Dependencies: 1618 20
-- Name: page_deleted; Type: TRIGGER; Schema: public; Owner: website
--

CREATE TRIGGER page_deleted
    AFTER DELETE ON page
    FOR EACH ROW
    EXECUTE PROCEDURE page_deleted();


--
-- TOC entry 2178 (class 2620 OID 34542)
-- Dependencies: 1622 22
-- Name: ts2_page_text; Type: TRIGGER; Schema: public; Owner: website
--

CREATE TRIGGER ts2_page_text
    BEFORE INSERT OR UPDATE ON pagecontent
    FOR EACH ROW
    EXECUTE PROCEDURE ts2_page_text();


--
-- TOC entry 2177 (class 2620 OID 34540)
-- Dependencies: 21 1618
-- Name: ts2_page_title; Type: TRIGGER; Schema: public; Owner: website
--

CREATE TRIGGER ts2_page_title
    BEFORE INSERT OR UPDATE ON page
    FOR EACH ROW
    EXECUTE PROCEDURE ts2_page_title();


--
-- TOC entry 2154 (class 2606 OID 34139)
-- Dependencies: 1626 1614 2013
-- Name: archive_ar_user_fkey; Type: FK CONSTRAINT; Schema: public; Owner: website
--

ALTER TABLE ONLY archive
    ADD CONSTRAINT archive_ar_user_fkey FOREIGN KEY (ar_user) REFERENCES mwuser(user_id) ON DELETE SET NULL;


--
-- TOC entry 2159 (class 2606 OID 34201)
-- Dependencies: 2023 1631 1618
-- Name: categorylinks_cl_from_fkey; Type: FK CONSTRAINT; Schema: public; Owner: website
--

ALTER TABLE ONLY categorylinks
    ADD CONSTRAINT categorylinks_cl_from_fkey FOREIGN KEY (cl_from) REFERENCES page(page_id) ON DELETE CASCADE;


--
-- TOC entry 2160 (class 2606 OID 34214)
-- Dependencies: 2023 1618 1632
-- Name: externallinks_el_from_fkey; Type: FK CONSTRAINT; Schema: public; Owner: website
--

ALTER TABLE ONLY externallinks
    ADD CONSTRAINT externallinks_el_from_fkey FOREIGN KEY (el_from) REFERENCES page(page_id) ON DELETE CASCADE;


--
-- TOC entry 2167 (class 2606 OID 34350)
-- Dependencies: 1614 2013 1642
-- Name: filearchive_fa_deleted_user_fkey; Type: FK CONSTRAINT; Schema: public; Owner: website
--

ALTER TABLE ONLY filearchive
    ADD CONSTRAINT filearchive_fa_deleted_user_fkey FOREIGN KEY (fa_deleted_user) REFERENCES mwuser(user_id) ON DELETE SET NULL;


--
-- TOC entry 2168 (class 2606 OID 34355)
-- Dependencies: 2013 1642 1614
-- Name: filearchive_fa_user_fkey; Type: FK CONSTRAINT; Schema: public; Owner: website
--

ALTER TABLE ONLY filearchive
    ADD CONSTRAINT filearchive_fa_user_fkey FOREIGN KEY (fa_user) REFERENCES mwuser(user_id) ON DELETE SET NULL;


--
-- TOC entry 2164 (class 2606 OID 34303)
-- Dependencies: 2013 1639 1614
-- Name: image_img_user_fkey; Type: FK CONSTRAINT; Schema: public; Owner: website
--

ALTER TABLE ONLY image
    ADD CONSTRAINT image_img_user_fkey FOREIGN KEY (img_user) REFERENCES mwuser(user_id) ON DELETE SET NULL;


--
-- TOC entry 2158 (class 2606 OID 34189)
-- Dependencies: 1630 1618 2023
-- Name: imagelinks_il_from_fkey; Type: FK CONSTRAINT; Schema: public; Owner: website
--

ALTER TABLE ONLY imagelinks
    ADD CONSTRAINT imagelinks_il_from_fkey FOREIGN KEY (il_from) REFERENCES page(page_id) ON DELETE CASCADE;


--
-- TOC entry 2163 (class 2606 OID 34283)
-- Dependencies: 2013 1614 1638
-- Name: ipblocks_ipb_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: website
--

ALTER TABLE ONLY ipblocks
    ADD CONSTRAINT ipblocks_ipb_by_fkey FOREIGN KEY (ipb_by) REFERENCES mwuser(user_id) ON DELETE CASCADE;


--
-- TOC entry 2162 (class 2606 OID 34278)
-- Dependencies: 1638 2013 1614
-- Name: ipblocks_ipb_user_fkey; Type: FK CONSTRAINT; Schema: public; Owner: website
--

ALTER TABLE ONLY ipblocks
    ADD CONSTRAINT ipblocks_ipb_user_fkey FOREIGN KEY (ipb_user) REFERENCES mwuser(user_id) ON DELETE SET NULL;


--
-- TOC entry 2161 (class 2606 OID 34236)
-- Dependencies: 1634 1618 2023
-- Name: langlinks_ll_from_fkey; Type: FK CONSTRAINT; Schema: public; Owner: website
--

ALTER TABLE ONLY langlinks
    ADD CONSTRAINT langlinks_ll_from_fkey FOREIGN KEY (ll_from) REFERENCES page(page_id) ON DELETE CASCADE;


--
-- TOC entry 2172 (class 2606 OID 34489)
-- Dependencies: 1614 1654 2013
-- Name: logging_log_user_fkey; Type: FK CONSTRAINT; Schema: public; Owner: website
--

ALTER TABLE ONLY logging
    ADD CONSTRAINT logging_log_user_fkey FOREIGN KEY (log_user) REFERENCES mwuser(user_id) ON DELETE SET NULL;


--
-- TOC entry 2166 (class 2606 OID 34327)
-- Dependencies: 1640 2072 1639
-- Name: oldimage_oi_name_fkey_cascaded; Type: FK CONSTRAINT; Schema: public; Owner: website
--

ALTER TABLE ONLY oldimage
    ADD CONSTRAINT oldimage_oi_name_fkey_cascaded FOREIGN KEY (oi_name) REFERENCES image(img_name) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2165 (class 2606 OID 34322)
-- Dependencies: 2013 1614 1640
-- Name: oldimage_oi_user_fkey; Type: FK CONSTRAINT; Schema: public; Owner: website
--

ALTER TABLE ONLY oldimage
    ADD CONSTRAINT oldimage_oi_user_fkey FOREIGN KEY (oi_user) REFERENCES mwuser(user_id) ON DELETE SET NULL;


--
-- TOC entry 2153 (class 2606 OID 34123)
-- Dependencies: 2023 1618 1625
-- Name: page_props_pp_page_fkey; Type: FK CONSTRAINT; Schema: public; Owner: website
--

ALTER TABLE ONLY page_props
    ADD CONSTRAINT page_props_pp_page_fkey FOREIGN KEY (pp_page) REFERENCES page(page_id) ON DELETE CASCADE;


--
-- TOC entry 2152 (class 2606 OID 34110)
-- Dependencies: 2023 1618 1624
-- Name: page_restrictions_pr_page_fkey; Type: FK CONSTRAINT; Schema: public; Owner: website
--

ALTER TABLE ONLY page_restrictions
    ADD CONSTRAINT page_restrictions_pr_page_fkey FOREIGN KEY (pr_page) REFERENCES page(page_id) ON DELETE CASCADE;


--
-- TOC entry 2156 (class 2606 OID 34164)
-- Dependencies: 2023 1628 1618
-- Name: pagelinks_pl_from_fkey; Type: FK CONSTRAINT; Schema: public; Owner: website
--

ALTER TABLE ONLY pagelinks
    ADD CONSTRAINT pagelinks_pl_from_fkey FOREIGN KEY (pl_from) REFERENCES page(page_id) ON DELETE CASCADE;


--
-- TOC entry 2174 (class 2606 OID 34563)
-- Dependencies: 1661 1614 2013
-- Name: protected_titles_pt_user_fkey; Type: FK CONSTRAINT; Schema: public; Owner: website
--

ALTER TABLE ONLY protected_titles
    ADD CONSTRAINT protected_titles_pt_user_fkey FOREIGN KEY (pt_user) REFERENCES mwuser(user_id) ON DELETE SET NULL;


--
-- TOC entry 2170 (class 2606 OID 34387)
-- Dependencies: 2023 1644 1618
-- Name: recentchanges_rc_cur_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: website
--

ALTER TABLE ONLY recentchanges
    ADD CONSTRAINT recentchanges_rc_cur_id_fkey FOREIGN KEY (rc_cur_id) REFERENCES page(page_id) ON DELETE SET NULL;


--
-- TOC entry 2169 (class 2606 OID 34382)
-- Dependencies: 1644 1614 2013
-- Name: recentchanges_rc_user_fkey; Type: FK CONSTRAINT; Schema: public; Owner: website
--

ALTER TABLE ONLY recentchanges
    ADD CONSTRAINT recentchanges_rc_user_fkey FOREIGN KEY (rc_user) REFERENCES mwuser(user_id) ON DELETE SET NULL;


--
-- TOC entry 2155 (class 2606 OID 34152)
-- Dependencies: 1627 2023 1618
-- Name: redirect_rd_from_fkey; Type: FK CONSTRAINT; Schema: public; Owner: website
--

ALTER TABLE ONLY redirect
    ADD CONSTRAINT redirect_rd_from_fkey FOREIGN KEY (rd_from) REFERENCES page(page_id) ON DELETE CASCADE;


--
-- TOC entry 2150 (class 2606 OID 34073)
-- Dependencies: 1620 2023 1618
-- Name: revision_rev_page_fkey; Type: FK CONSTRAINT; Schema: public; Owner: website
--

ALTER TABLE ONLY revision
    ADD CONSTRAINT revision_rev_page_fkey FOREIGN KEY (rev_page) REFERENCES page(page_id) ON DELETE CASCADE;


--
-- TOC entry 2151 (class 2606 OID 34078)
-- Dependencies: 2013 1620 1614
-- Name: revision_rev_user_fkey; Type: FK CONSTRAINT; Schema: public; Owner: website
--

ALTER TABLE ONLY revision
    ADD CONSTRAINT revision_rev_user_fkey FOREIGN KEY (rev_user) REFERENCES mwuser(user_id) ON DELETE RESTRICT;


--
-- TOC entry 2157 (class 2606 OID 34176)
-- Dependencies: 2023 1629 1618
-- Name: templatelinks_tl_from_fkey; Type: FK CONSTRAINT; Schema: public; Owner: website
--

ALTER TABLE ONLY templatelinks
    ADD CONSTRAINT templatelinks_tl_from_fkey FOREIGN KEY (tl_from) REFERENCES page(page_id) ON DELETE CASCADE;


--
-- TOC entry 2173 (class 2606 OID 34521)
-- Dependencies: 2023 1618 1657
-- Name: trackbacks_tb_page_fkey; Type: FK CONSTRAINT; Schema: public; Owner: website
--

ALTER TABLE ONLY trackbacks
    ADD CONSTRAINT trackbacks_tb_page_fkey FOREIGN KEY (tb_page) REFERENCES page(page_id) ON DELETE CASCADE;


--
-- TOC entry 2148 (class 2606 OID 34016)
-- Dependencies: 1615 1614 2013
-- Name: user_groups_ug_user_fkey; Type: FK CONSTRAINT; Schema: public; Owner: website
--

ALTER TABLE ONLY user_groups
    ADD CONSTRAINT user_groups_ug_user_fkey FOREIGN KEY (ug_user) REFERENCES mwuser(user_id) ON DELETE CASCADE;


--
-- TOC entry 2149 (class 2606 OID 34028)
-- Dependencies: 2013 1616 1614
-- Name: user_newtalk_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: website
--

ALTER TABLE ONLY user_newtalk
    ADD CONSTRAINT user_newtalk_user_id_fkey FOREIGN KEY (user_id) REFERENCES mwuser(user_id) ON DELETE CASCADE;


--
-- TOC entry 2175 (class 2606 OID 34627)
-- Dependencies: 1668 2013 1614
-- Name: user_properties_up_user_fkey; Type: FK CONSTRAINT; Schema: public; Owner: website
--

ALTER TABLE ONLY user_properties
    ADD CONSTRAINT user_properties_up_user_fkey FOREIGN KEY (up_user) REFERENCES mwuser(user_id) ON DELETE CASCADE;


--
-- TOC entry 2171 (class 2606 OID 34405)
-- Dependencies: 2013 1645 1614
-- Name: watchlist_wl_user_fkey; Type: FK CONSTRAINT; Schema: public; Owner: website
--

ALTER TABLE ONLY watchlist
    ADD CONSTRAINT watchlist_wl_user_fkey FOREIGN KEY (wl_user) REFERENCES mwuser(user_id) ON DELETE CASCADE;


--
-- TOC entry 2229 (class 0 OID 0)
-- Dependencies: 3
-- Name: public; Type: ACL; Schema: -; Owner: website
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


-- Completed on 2011-02-16 18:50:05

--
-- PostgreSQL database dump complete
--
