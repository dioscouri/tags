-- --------------------------------------------------------
-- Dumping data for table `#__tags_scopes`
-- --------------------------------------------------------

INSERT IGNORE INTO `#__tags_scopes` (`scope_id`, `scope_name`, `scope_identifier`, `scope_url`, `scope_table`, `scope_table_field`, `scope_table_name_field`) VALUES
(1, 'Content Article', 'com_content.article', 'index.php?option=com_content&view=article&id=', '#__content', 'id', 'title'),
(2, 'User', 'com_users.user', 'index.php?option=com_users&view=user&id=', '#__users', 'id', 'name'),
(3, 'Tienda Product', 'com_tienda.products', 'index.php?option=com_tienda&view=products&task=view&id=', '#__tienda_products', 'product_id', 'product_name'),
(4, 'K2 Item', 'com_k2.item', 'index.php?option=com_k2&view=item&id=', '#__k2_items', 'id', 'title');
