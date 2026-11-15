CREATE TABLE IF NOT EXISTS galleries (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    slug        TEXT NOT NULL UNIQUE,
    title       TEXT NOT NULL,
    description TEXT NOT NULL DEFAULT '',
    style       TEXT NOT NULL DEFAULT 'masonry',
    cover_photo_id INTEGER,
    is_published INTEGER NOT NULL DEFAULT 1,
    sort_order  INTEGER NOT NULL DEFAULT 0,
    created_at  TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS photos (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    gallery_id  INTEGER NOT NULL REFERENCES galleries(id) ON DELETE CASCADE,
    filename    TEXT NOT NULL,
    title       TEXT NOT NULL DEFAULT '',
    caption     TEXT NOT NULL DEFAULT '',
    width       INTEGER NOT NULL DEFAULT 0,
    height      INTEGER NOT NULL DEFAULT 0,
    sort_order  INTEGER NOT NULL DEFAULT 0,
    created_at  TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_photos_gallery ON photos(gallery_id, sort_order);
