SELECT setval('cusers_id_seq', (SELECT MAX(id) FROM cusers) + 1);
SELECT setval('projects_id_seq', (SELECT MAX(id) FROM projects) + 1);
SELECT setval('wordlist_id_seq', (SELECT MAX(id) FROM wordlist) + 1);
SELECT setval('featured_projects_id_seq', (SELECT MAX(id) FROM featured_projects) + 1);
SELECT setval('starter_projects_id_seq', (SELECT MAX(id) FROM starter_projects) + 1);
