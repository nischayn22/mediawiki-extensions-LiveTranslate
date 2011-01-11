-- MySQL version of the database schema for the Live Translate extension.

-- Special translations table.
CREATE TABLE IF NOT EXISTS /*$wgDBprefix*/live_translate (
  word_id                 INT(8) unsigned   NOT NULL,
  word_language           VARCHAR(255)      NOT NULL,
  word_translation        VARCHAR(255)      NOT NULL,
  word_primary            INT(1) unsigned   NOT NULL,
  memory_id               INT(4) unsigned   NOT NULL
) /*$wgDBTableOptions*/; 

-- Table to keep track of translation memories for the special words.
CREATE TABLE IF NOT EXISTS /*$wgDBprefix*/live_translate_memories (
  memory_id               INT(4) unsigned   NOT NULL auto_increment PRIMARY KEY,
  memory_type             INT(2) unsigned   NOT NULL,
  memory_location         VARCHAR(255)      NOT NULL,  
  memory_local            INT(1) unsigned   NOT NULL
) /*$wgDBTableOptions*/;

INSERT INTO /*$wgDBprefix*/live_translate_memories VALUES (NULL, 0, "Live Translate Dictionary", 1);