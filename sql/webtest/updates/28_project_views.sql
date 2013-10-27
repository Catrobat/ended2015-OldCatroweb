--Views und Indizes anlegen
--=========================
--View fuer visible=true Projekte anlegen
--View fuer projects und username
--Statistiken aktualisieren

create or replace view public.visible_projects_v
as
  select p.*
    from projects p
   where visible = true
;

create or replace view public.visible_projects_with_uploaded_by_v
as
  select p.*, u.username AS uploaded_by
    from visible_projects_v p inner join cusers u
      on p.user_id = u.id
;

analyze;