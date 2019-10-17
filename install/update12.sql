ALTER TABLE `shua_tools`
ADD COLUMN `prices` VARCHAR(100) DEFAULT NULL;

DROP TABLE IF EXISTS `shua_cart`;
CREATE TABLE `shua_cart` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userid` varchar(32) NOT NULL,
  `zid` int(11) unsigned NOT NULL DEFAULT '1',
  `tid` int(11) NOT NULL,
  `input` text NOT NULL,
  `num` int(11) unsigned NOT NULL DEFAULT '1',
  `money` varchar(32) NULL,
  `addtime` datetime NULL,
  `endtime` datetime NULL,
  `status` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY userid (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `shua_config` VALUES ('shoppingcart', '1');