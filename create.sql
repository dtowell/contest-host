CREATE TABLE contestants (
    id              INTEGER PRIMARY KEY,
    name            TEXT    NOT NULL,
    password        TEXT    NOT NULL,
    judge           INTEGER NOT NULL DEFAULT 0, -- boolean
    last_login      INTEGER,
    UNIQUE(name));
    
CREATE TABLE contests (
    id              INTEGER PRIMARY KEY,
    name            TEXT    NOT NULL UNIQUE,
    live            INTEGER NOT NULL DEFAULT 0, -- boolean
    path            TEXT    NOT NULL,
    start_time      INTEGER NOT NULL,
    freeze_time     INTEGER NOT NULL,
    stop_time       INTEGER NOT NULL,
    languages       TEXT    NOT NULL);
    
CREATE TABLE problems (
    id              INTEGER PRIMARY KEY,
    contest         INTEGER NOT NULL REFERENCES contests(id),
    number          INTEGER NOT NULL,
    name            TEXT    NOT NULL,
    UNIQUE(contest,number),
    UNIQUE(contest,name));
    
CREATE TABLE attempts (
    id              INTEGER PRIMARY KEY,
    time            INTEGER NOT NULL,
    contestant      INTEGER NOT NULL REFERENCES contestants(id),
    problem         INTEGER NOT NULL REFERENCES problems(id),
    language        TEXT    NOT NULL,
    code            TEXT    NOT NULL,
    savepath        TEXT    NOT NULL,
    filename        TEXT    NOT NULL,
    compile_output  TEXT,
    run_errors      TEXT,
    run_output      TEXT,
    result          TEXT);

CREATE TABLE notes (
    id              INTEGER PRIMARY KEY,
    contest         INTEGER NOT NULL REFERENCES contests(id),
    problem         INTEGER          REFERENCES problems(id),
    note            TEXT);

INSERT INTO contestants (name,password,judge) VALUES ('admin','secret',1);
INSERT INTO contests (name,live,path,start_time,freeze_time,stop_time,languages) VALUES  
    ('Sample',1,'../contests/sample',datetime('now'),datetime('now','2 hours'),datetime('now','3 hours'),'{java,c,cpp,php,py,txt,q}');
INSERT INTO problems (contest,number,name) VALUES ((select id from contests where name='Sample'),1,'Hello');
INSERT INTO problems (contest,number,name) VALUES ((select id from contests where name='Sample'),2,'Squares');
