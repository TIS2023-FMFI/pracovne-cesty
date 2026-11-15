-- Minimal LOCAL stand-in for the external Pritomnost ("dochadzka") database.
-- Only the tables/columns this app reads or writes (PritomnostUser, PritomnostAbsence).
-- Not the real schema; production uses the faculty's existing database.
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    personal_id VARCHAR(20)  NOT NULL,
    username    VARCHAR(255) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    name        VARCHAR(255) NOT NULL DEFAULT '',
    surname     VARCHAR(255) NOT NULL DEFAULT '',
    email       VARCHAR(255) NULL,
    status      TINYINT      NOT NULL DEFAULT 1,
    last_login  DATETIME     NULL
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS absence (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    user_id      INT          NOT NULL,
    date_time    DATE         NOT NULL,
    from_time    TIME         NULL,
    to_time      TIME         NULL,
    description  VARCHAR(255) NULL,
    type         TINYINT      NOT NULL,
    public       TINYINT(1)   NOT NULL DEFAULT 0,
    confirmation TINYINT(1)   NOT NULL DEFAULT 0,
    cesty_id     INT          NULL,
    insert_time  DATETIME     NULL DEFAULT CURRENT_TIMESTAMP
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
