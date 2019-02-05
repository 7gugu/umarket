# Host: localhost  (Version: 5.5.53)
# Date: 2019-02-05 21:56:54
# Generator: MySQL-Front 5.3  (Build 4.234)

/*!40101 SET NAMES utf8 */;

#
# Structure for table "umarket_account"
#

DROP TABLE IF EXISTS `umarket_account`;
CREATE TABLE `umarket_account` (
  `accountid` int(11) NOT NULL COMMENT '账户id',
  `username` text NOT NULL COMMENT '账户名',
  `password` text NOT NULL COMMENT '账户密码',
  `email` text NOT NULL COMMENT '邮箱',
  `status` int(11) NOT NULL COMMENT '状态',
  `regtime` int(11) NOT NULL COMMENT '注册时间',
  `token` text NOT NULL COMMENT '验证码',
  `token_exptime` int(11) NOT NULL COMMENT '验证码期限',
  `steamid` text NOT NULL COMMENT 'steamid',
  `tradepw` int(11) NOT NULL COMMENT '交易密码',
  `tradelink` text NOT NULL COMMENT '交易链接',
  `phone` text NOT NULL,
  `exp` int(11) NOT NULL COMMENT '经验值',
  `access` int(11) NOT NULL COMMENT '权限',
  `token_status` int(11) NOT NULL,
  `avatar` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=gbk COMMENT='商城账户';

#
# Structure for table "umarket_bot_account"
#

DROP TABLE IF EXISTS `umarket_bot_account`;
CREATE TABLE `umarket_bot_account` (
  `accountid` int(11) NOT NULL COMMENT '账户数',
  `username` text NOT NULL COMMENT '账户名',
  `password` text NOT NULL COMMENT '账户密码',
  `twofastate` int(11) NOT NULL COMMENT '二步状态',
  `shared_serect` text NOT NULL COMMENT '二步公钥',
  `accountstate` int(11) NOT NULL COMMENT '账户状态',
  `accountport` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=gbk COMMENT='机器人账户';

#
# Structure for table "umarket_broadcast"
#

DROP TABLE IF EXISTS `umarket_broadcast`;
CREATE TABLE `umarket_broadcast` (
  `broadcast_id` int(11) NOT NULL COMMENT '公告ID',
  `broadcast_title` text NOT NULL COMMENT '公告标题',
  `broadcast_body` text NOT NULL COMMENT '公告主题内容',
  `broadcast_time` text NOT NULL COMMENT '公告发出时间',
  `broadcast_author` text NOT NULL COMMENT '公告作者'
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

#
# Structure for table "umarket_goods_304930"
#

DROP TABLE IF EXISTS `umarket_goods_304930`;
CREATE TABLE `umarket_goods_304930` (
  `item_id` int(11) NOT NULL DEFAULT '0' COMMENT '货品ID',
  `goods_context` text NOT NULL COMMENT '货品信息',
  `goods_price` float NOT NULL DEFAULT '99999' COMMENT '货品价格',
  `goods_count` int(11) NOT NULL DEFAULT '1' COMMENT '货品个数',
  `goods_seller_id` text NOT NULL COMMENT '商品出售者STEAM-ID',
  `goods_id` text NOT NULL COMMENT '饰品的steamid'
) ENGINE=MyISAM DEFAULT CHARSET=gbk COMMENT='上架货品';

#
# Structure for table "umarket_goods_570"
#

DROP TABLE IF EXISTS `umarket_goods_570`;
CREATE TABLE `umarket_goods_570` (
  `item_id` int(11) NOT NULL DEFAULT '0' COMMENT '货品ID',
  `goods_context` text NOT NULL COMMENT '货品信息',
  `goods_price` float NOT NULL DEFAULT '99999' COMMENT '货品价格',
  `goods_count` int(11) NOT NULL DEFAULT '1' COMMENT '货品个数',
  `goods_seller_id` text NOT NULL COMMENT '商品出售者STEAM-ID',
  `goods_id` int(11) NOT NULL COMMENT '饰品的steamid'
) ENGINE=MyISAM DEFAULT CHARSET=gbk COMMENT='上架货品';

#
# Structure for table "umarket_goods_730"
#

DROP TABLE IF EXISTS `umarket_goods_730`;
CREATE TABLE `umarket_goods_730` (
  `item_id` int(11) NOT NULL DEFAULT '0' COMMENT '货品ID',
  `goods_context` text NOT NULL COMMENT '货品信息',
  `goods_price` float NOT NULL DEFAULT '99999' COMMENT '货品价格',
  `goods_count` int(11) NOT NULL DEFAULT '1' COMMENT '货品个数',
  `goods_seller_id` text NOT NULL COMMENT '商品出售者STEAM-ID',
  `goods_id` int(11) NOT NULL COMMENT '饰品的steamid'
) ENGINE=MyISAM DEFAULT CHARSET=gbk COMMENT='上架货品';

#
# Structure for table "umarket_goods_map_304930"
#

DROP TABLE IF EXISTS `umarket_goods_map_304930`;
CREATE TABLE `umarket_goods_map_304930` (
  `map_id` int(11) NOT NULL,
  `goods_id` text NOT NULL,
  `goods_name` text NOT NULL,
  `goods_img` text NOT NULL,
  `goods_min_price` float NOT NULL,
  `goods_count` int(11) NOT NULL,
  `goods_type` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=gbk COMMENT='存储ID游戏物品种类';

#
# Structure for table "umarket_goods_map_570"
#

DROP TABLE IF EXISTS `umarket_goods_map_570`;
CREATE TABLE `umarket_goods_map_570` (
  `map_id` int(11) NOT NULL,
  `goods_id` int(11) NOT NULL,
  `goods_name` text NOT NULL,
  `goods_img` text NOT NULL,
  `goods_min_price` int(11) NOT NULL,
  `goods_count` int(11) NOT NULL,
  `goods_type` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=gbk COMMENT='存储ID游戏物品种类';

#
# Structure for table "umarket_goods_map_730"
#

DROP TABLE IF EXISTS `umarket_goods_map_730`;
CREATE TABLE `umarket_goods_map_730` (
  `map_id` int(11) NOT NULL,
  `goods_id` int(11) NOT NULL,
  `goods_name` text NOT NULL,
  `goods_img` text NOT NULL,
  `goods_max_price` int(11) NOT NULL,
  `goods_count` int(11) NOT NULL,
  `goods_type` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=gbk COMMENT='存储ID游戏物品种类';

#
# Structure for table "umarket_inventory"
#

DROP TABLE IF EXISTS `umarket_inventory`;
CREATE TABLE `umarket_inventory` (
  `inventory_id` int(11) NOT NULL,
  `goods_price` float NOT NULL,
  `goods_count` int(11) NOT NULL DEFAULT '1',
  `goods_steam_id` text NOT NULL COMMENT '饰品的steamid',
  `goods_buyer_id` text NOT NULL,
  `goods_seller_id` text NOT NULL,
  `goods_submit_time` text NOT NULL,
  `goods_state` int(11) NOT NULL DEFAULT '0',
  `goods_name` text NOT NULL,
  `order_market_id` text NOT NULL,
  `goods_game_id` text NOT NULL,
  `goods_img` text NOT NULL,
  `bot_accountid` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

#
# Structure for table "umarket_inventory_order"
#

DROP TABLE IF EXISTS `umarket_inventory_order`;
CREATE TABLE `umarket_inventory_order` (
  `order_market_id` int(11) NOT NULL,
  `order_stat` int(11) NOT NULL,
  `order_context` text NOT NULL,
  `order_game_id` text NOT NULL,
  `order_steamid` text NOT NULL,
  `order_outtime` int(11) NOT NULL,
  `order_icon_url` text NOT NULL,
  `order_item_name` text NOT NULL,
  `player_steam_id` text NOT NULL,
  `bot_accountid` int(11) NOT NULL,
  `order_time` text NOT NULL COMMENT '订单创建时间',
  FULLTEXT KEY `order_context` (`order_context`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk COMMENT='库存交接订单';

#
# Structure for table "umarket_option"
#

DROP TABLE IF EXISTS `umarket_option`;
CREATE TABLE `umarket_option` (
  `option_name` text NOT NULL,
  `option_context` text NOT NULL,
  `option_status` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

#
# Structure for table "umarket_order"
#

DROP TABLE IF EXISTS `umarket_order`;
CREATE TABLE `umarket_order` (
  `order_id` int(11) NOT NULL,
  `order_state` int(11) NOT NULL,
  `payment_method` int(11) NOT NULL DEFAULT '1',
  `order_context` text NOT NULL,
  `order_request_steamid` text NOT NULL,
  `order_email` text NOT NULL,
  `order_phone` int(11) NOT NULL,
  `order_time` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

#
# Structure for table "umarket_plugin"
#

DROP TABLE IF EXISTS `umarket_plugin`;
CREATE TABLE `umarket_plugin` (
  `name` text NOT NULL,
  `status` int(11) NOT NULL,
  `ver` text NOT NULL,
  `option` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

#
# Structure for table "umarket_query_list"
#

DROP TABLE IF EXISTS `umarket_query_list`;
CREATE TABLE `umarket_query_list` (
  `order_id` int(11) NOT NULL COMMENT '订单编号',
  `order_stat` int(11) NOT NULL COMMENT '订单状态',
  `order_token` text NOT NULL COMMENT '卖方密钥',
  `order_context` text NOT NULL COMMENT '买卖内容',
  `order_serect` text NOT NULL COMMENT '交易暗号',
  `order_partner` int(11) NOT NULL COMMENT '卖方ID',
  `order_market_id` int(11) NOT NULL COMMENT '订单在商城的ID',
  `order_steam_id` text NOT NULL COMMENT '储存订单位于steam的ID',
  `order_time` int(11) NOT NULL COMMENT '发起订单的时间',
  `bot_accountid` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=gbk COMMENT='交易机器人的执行序列';

#
# Structure for table "umarket_wallet"
#

DROP TABLE IF EXISTS `umarket_wallet`;
CREATE TABLE `umarket_wallet` (
  `wallet_id` int(11) NOT NULL,
  `wallet_steamid` text NOT NULL,
  `wallet_balance` float NOT NULL,
  `wallet_balance_alipay` float NOT NULL COMMENT '可提取至支付宝的金额',
  `wallet_alipay_realname` text NOT NULL,
  `wallet_alipay_account` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

#
# Structure for table "umarket_wallet_key"
#

DROP TABLE IF EXISTS `umarket_wallet_key`;
CREATE TABLE `umarket_wallet_key` (
  `key_id` int(11) NOT NULL,
  `key_name` text NOT NULL,
  `key_password` text NOT NULL,
  `key_balance` float NOT NULL,
  `key_state` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=gbk;
