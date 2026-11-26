PRAGMA foreign_keys = ON;
BEGIN;
CREATE TABLE t_category (id_cat INTEGER PRIMARY KEY, name_cat TEXT NOT NULL);
CREATE TABLE t_reporter (
    id_rep INTEGER PRIMARY KEY, firstname_rep TEXT NOT NULL, lastname_rep TEXT NOT NULL
);
CREATE TABLE t_article (
    ident_art INTEGER PRIMARY KEY,
    title_art TEXT NOT NULL,
    date_art TEXT NOT NULL,
    hook_art TEXT NOT NULL,
    content_art TEXT NOT NULL,
    image_art TEXT,
    readtime_art INTEGER NOT NULL,
    fk_category_art INTEGER NOT NULL REFERENCES t_category(id_cat),
    reporter_art INTEGER NOT NULL REFERENCES t_reporter(id_rep)
);
CREATE TABLE t_static (slug_sta TEXT PRIMARY KEY, content_sta TEXT NOT NULL);

INSERT INTO t_category VALUES (1, 'Environnement'), (2, 'Culture'), (3, 'Science');
INSERT INTO t_reporter VALUES (1, 'Camille', 'Rivage'), (2, 'Noah', 'Delta');
INSERT INTO t_article VALUES
    (1, 'Un jardin au port', '2026-05-06', 'Des plantes locales gagnent le quai.',
     '<p>Le premier jardin collectif du port accueille les habitants ce samedi.</p>', NULL, 2, 1, 1),
    (2, 'La nuit des ateliers', '2026-05-05', 'Des artistes ouvrent leurs portes.',
     '<p>Trois ateliers exposent leurs travaux au public dans le quartier du phare.</p>', NULL, 3, 2, 2),
    (3, 'Carnet de bord scientifique', '2026-05-04', 'Un bateau recueille des mesures locales.',
     '<p>L equipe de recherche suit la temperature de l eau le long du littoral.</p>', NULL, 2, 3, 1),
    (4, 'Un nouveau sentier sur la dune', '2026-05-03', 'Les promeneurs suivent un parcours balise.',
     '<p>Le balisage protege la vegetation et garde la plage accessible a pied.</p>', NULL, 4, 1, 2),
    (5, 'La radio du quartier prend le large', '2026-05-02', 'Une emission est enregistree au port.',
     '<p>Les riverains racontent leurs souvenirs du quartier depuis le vieux quai.</p>', NULL, 3, 2, 1);
INSERT INTO t_static VALUES ('about', '<main><p>Presse Ocean est un projet de cours.</p></main>');
COMMIT;
