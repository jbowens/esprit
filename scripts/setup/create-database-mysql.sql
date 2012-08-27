-- Create the languages table
CREATE TABLE IF NOT EXISTS `languages` (
    languageid      INT NOT NULL AUTO_INCREMENT,
    parentid        INT,
    identifier      CHAR(6) NOT NULL,
    name            CHAR(20) NOT NULL,
    PRIMARY KEY (languageid))
ENGINE=MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;

-- Create an index on the language identifer
CREATE UNIQUE INDEX strid_index ON languages (identifier) USING BTREE;

-- Insert us-english, the root of the language tree
INSERT INTO languages(identifier, name) VALUES('en-US', 'English (US)');

-- Create the translations table
CREATE TABLE IF NOT EXISTS `translations` (
    translationid           SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    translationIdentifier   VARCHAR(60),
    languageid              INT NOT NULL,
    translation             TEXT,
    PRIMARY KEY(translationid)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;

-- Create an index on the translation identifier and language id
ALTER TABLE translations ADD INDEX translation_lookup (languageid, translationIdentifier) USING BTREE;

-- Create the actions table
CREATE TABLE IF NOT EXISTS `actions` (
    actionid                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    typeid                  MEDIUMINT UNSIGNED NOT NULL,
    dateRecorded            INT UNSIGNED NOT NULL,
    sessionid               CHAR(32) NOT NULL,
    PRIMARY KEY(actionid)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;

-- Create an index on the typeid
ALTER TABLE actions ADD INDEX actions_type (typeid) USING BTREE;

-- Create an idnex on the session
ALTER TABLE actions ADD INDEX actions_session (sessionid) USING BTREE;

-- Create the action types table
CREATE TABLE IF NOT EXISTS `action_types` (
    typeid                  MEDIUMINT UNSIGNED NOT NULL,
    identifier              VARCHAR(60) NOT NULL,
    PRIMARY KEY(typeid)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;

-- Create a unique index on the action identifier
CREATE UNIQUE INDEX action_identifier ON action_types (identifier) USING BTREE;

