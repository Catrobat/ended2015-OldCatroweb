/*For tags sequence*/
SELECT setval('tags_id_seq', (SELECT MAX(id) FROM tags) + 1);