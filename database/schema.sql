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

-- Categorías de las fotos: se guardan para reutilizarlas (máx. 3 por foto, ver App\Services\Categories)
CREATE TABLE IF NOT EXISTS categories (
    id    INTEGER PRIMARY KEY AUTOINCREMENT,
    name  TEXT NOT NULL UNIQUE COLLATE NOCASE
);
CREATE TABLE IF NOT EXISTS photo_categories (
    photo_id     INTEGER NOT NULL REFERENCES photos(id) ON DELETE CASCADE,
    category_id  INTEGER NOT NULL REFERENCES categories(id) ON DELETE CASCADE,
    pos          INTEGER NOT NULL DEFAULT 0,
    PRIMARY KEY (photo_id, category_id)
);
CREATE INDEX IF NOT EXISTS idx_pc_category ON photo_categories(category_id);

CREATE TABLE IF NOT EXISTS settings (
    key   TEXT PRIMARY KEY,
    value TEXT NOT NULL DEFAULT ''
);

CREATE TABLE IF NOT EXISTS users (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    username      TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    created_at    TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS login_attempts (
    ip TEXT NOT NULL,
    at INTEGER NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_login_attempts ON login_attempts(ip, at);

-- Passkeys (huella, Face ID, Windows Hello, llaves de seguridad): solo se guarda la clave PÚBLICA
CREATE TABLE IF NOT EXISTS passkeys (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id    INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    cred_id    TEXT NOT NULL UNIQUE,      -- identificador de la credencial (base64url)
    public_key TEXT NOT NULL,             -- PEM (SPKI)
    alg        INTEGER NOT NULL,          -- -7 ES256 | -257 RS256
    counter    INTEGER NOT NULL DEFAULT 0,
    name       TEXT NOT NULL DEFAULT '',
    created_at INTEGER NOT NULL,
    last_used  INTEGER
);
