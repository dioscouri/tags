-- -----------------------------------------------------
-- HOW TO USE THIS FILE:
-- Replace all instances of #_ with your prefix
-- In PHPMYADMIN or the equiv, run the entire SQL
-- -----------------------------------------------------

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

drop table if exists `#__tags_addresses`;
drop table if exists `#__tags_carts`;
drop table if exists `#__tags_categories`;
drop table if exists `#__tags_config`;
drop table if exists `#__tags_countries`;
drop table if exists `#__tags_currencies`;
drop table if exists `#__tags_geozones`;
drop table if exists `#__tags_geozonetypes`;
drop table if exists `#__tags_manufacturers`;
drop table if exists `#__tags_ordercoupons`;
drop table if exists `#__tags_orderhistory`;
drop table if exists `#__tags_orderinfo`;
drop table if exists `#__tags_orderitems`;
drop table if exists `#__tags_orderitemattributes`;
drop table if exists `#__tags_orderpayments`;
drop table if exists `#__tags_orders`;
drop table if exists `#__tags_ordershippings`;
drop table if exists `#__tags_orderstates`;
drop table if exists `#__tags_ordertaxclasses`;
drop table if exists `#__tags_ordertaxrates`;
drop table if exists `#__tags_ordervendors`;
drop table if exists `#__tags_productattributeoptions`;
drop table if exists `#__tags_productattributes`;
drop table if exists `#__tags_productcategoryxref`;
drop table if exists `#__tags_productcomments`;
drop table if exists `#__tags_productcommentshelpfulness`;
drop table if exists `#__tags_productdownloadlogs`;
drop table if exists `#__tags_productdownloads`;
drop table if exists `#__tags_productfiles`;
drop table if exists `#__tags_productprices`;
drop table if exists `#__tags_productquantities`;
drop table if exists `#__tags_productrelations`;
drop table if exists `#__tags_productreviews`;
drop table if exists `#__tags_products`;
drop table if exists `#__tags_productvotes`;
drop table if exists `#__tags_shippingmethods`;
drop table if exists `#__tags_shippingrates`;
drop table if exists `#__tags_subscriptions`;
drop table if exists `#__tags_subscriptionhistory`;
drop table if exists `#__tags_taxclasses`;
drop table if exists `#__tags_taxrates`;
drop table if exists `#__tags_userinfo`;
drop table if exists `#__tags_zonerelations`;
drop table if exists `#__tags_zones`;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;