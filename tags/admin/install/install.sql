-- -----------------------------------------------------
-- Table `#__tags_config`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__tags_config` (
  `config_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `config_name` VARCHAR(255) NOT NULL ,
  `value` TEXT NOT NULL ,
  PRIMARY KEY (`config_id`) 
)
ENGINE=MyISAM
DEFAULT CHARACTER SET = utf8;

-- --------------------------------------------------------
-- Table structure for table `#__tags_tags`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__tags_tags` (
  `tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(255) NOT NULL,
  `tag_alias` varchar(255) NOT NULL,
  `uses` int(11) NOT NULL,
  `admin_only` tinyint(1) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_datetime` datetime NOT NULL COMMENT 'Always in GMT',
  PRIMARY KEY (`tag_id`)
) 
ENGINE=MyISAM 
DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
-- Table structure for table `#__tags_scopes`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__tags_scopes` (
  `scope_id` int(11) NOT NULL AUTO_INCREMENT,
  `scope_name` varchar(255) NOT NULL COMMENT 'Plain English name for the scope',
  `scope_identifier` varchar(255) NOT NULL COMMENT 'String unique ID for the scope',
  `scope_url` varchar(255) NOT NULL COMMENT 'URL for the scope item',
  `scope_table` varchar(255) NOT NULL COMMENT 'The DB table to perform the JOIN',
  `scope_table_field` varchar(255) NOT NULL COMMENT 'The DB table field to use for the JOIN',
  `scope_table_name_field` varchar(255) NOT NULL COMMENT 'The DB table field to use for the item name',
  `scope_params` text NOT NULL COMMENT 'JSON-encoded object with any other information you want to store about the scope',
  PRIMARY KEY (`scope_id`),
  KEY `scope_identifier` (`scope_identifier`)
) 
ENGINE=MyISAM  
DEFAULT CHARSET=utf8;


-- --------------------------------------------------------
-- Table structure for table `#__tags_relationships`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__tags_relationships` (
  `relationship_id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) NOT NULL,
  `scope_id` int(11) NOT NULL,
  `item_value` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_datetime` datetime NOT NULL COMMENT 'Always in GMT',
  PRIMARY KEY (`relationship_id`)
) 
ENGINE=MyISAM 
DEFAULT CHARSET=utf8;
