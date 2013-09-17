SELECT setval('cusers_id_seq', (SELECT MAX(id) FROM cusers) + 1); 
SELECT setval('projects_id_seq', (SELECT MAX(id) FROM projects) + 1);
SELECT setval('flagged_projects_id_seq', (SELECT MAX(id) FROM flagged_projects) + 1);
SELECT setval('wordlist_id_seq', (SELECT MAX(id) FROM wordlist) + 1);
SELECT setval('unapproved_words_in_projects_id_seq', (SELECT MAX(id) FROM unapproved_words_in_projects) + 1);
