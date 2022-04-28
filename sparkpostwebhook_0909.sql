/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : sparkpostwebhook

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2019-09-09 16:02:20
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `tbl_amp_click`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_amp_click`;
CREATE TABLE `tbl_amp_click` (
  `ab_test_id` varchar(64) DEFAULT NULL,
  `ab_test_version` varchar(64) DEFAULT NULL,
  `amp_enabled` varchar(64) DEFAULT NULL,
  `campaign_id` varchar(64) DEFAULT NULL,
  `click_tracking` varchar(64) DEFAULT NULL,
  `customer_id` varchar(64) DEFAULT NULL,
  `delv_method` varchar(64) DEFAULT NULL,
  `event_id` varchar(64) DEFAULT NULL,
  `friendly_from` varchar(64) DEFAULT NULL,
  `geo_ip` varchar(64) DEFAULT NULL,
  `injection_time` varchar(64) DEFAULT NULL,
  `initial_pixel` varchar(64) DEFAULT NULL,
  `ip_address` varchar(64) DEFAULT NULL,
  `ip_pool` varchar(64) DEFAULT NULL,
  `message_id` varchar(64) DEFAULT NULL,
  `msg_from` varchar(64) DEFAULT NULL,
  `msg_size` varchar(64) DEFAULT NULL,
  `num_retries` varchar(64) DEFAULT NULL,
  `open_tracking` varchar(64) DEFAULT NULL,
  `queue_time` varchar(64) DEFAULT NULL,
  `rcpt_meta` varchar(64) DEFAULT NULL,
  `rcpt_tags` varchar(64) DEFAULT NULL,
  `rcpt_to` varchar(64) DEFAULT NULL,
  `raw_rcpt_to` varchar(64) DEFAULT NULL,
  `rcpt_type` varchar(64) DEFAULT NULL,
  `routing_domain` varchar(64) DEFAULT NULL,
  `sending_ip` varchar(64) DEFAULT NULL,
  `subaccount_id` varchar(64) DEFAULT NULL,
  `subject` varchar(64) DEFAULT NULL,
  `target_link_name` varchar(64) DEFAULT NULL,
  `target_link_url` varchar(64) DEFAULT NULL,
  `template_id` varchar(64) DEFAULT NULL,
  `template_version` varchar(64) DEFAULT NULL,
  `timestamp` varchar(64) DEFAULT NULL,
  `transactional` varchar(64) DEFAULT NULL,
  `transmission_id` varchar(64) DEFAULT NULL,
  `type` varchar(64) NOT NULL,
  `user_agent` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_amp_click
-- ----------------------------

-- ----------------------------
-- Table structure for `tbl_amp_initial_open`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_amp_initial_open`;
CREATE TABLE `tbl_amp_initial_open` (
  `ab_test_id` varchar(64) DEFAULT NULL,
  `ab_test_version` varchar(64) DEFAULT NULL,
  `amp_enabled` varchar(64) DEFAULT NULL,
  `campaign_id` varchar(64) DEFAULT NULL,
  `click_tracking` varchar(64) DEFAULT NULL,
  `customer_id` varchar(64) DEFAULT NULL,
  `delv_method` varchar(64) DEFAULT NULL,
  `event_id` varchar(64) DEFAULT NULL,
  `friendly_from` varchar(64) DEFAULT NULL,
  `geo_ip` varchar(64) DEFAULT NULL,
  `injection_time` varchar(64) DEFAULT NULL,
  `initial_pixel` varchar(64) DEFAULT NULL,
  `ip_address` varchar(64) DEFAULT NULL,
  `ip_pool` varchar(64) DEFAULT NULL,
  `message_id` varchar(64) DEFAULT NULL,
  `msg_from` varchar(64) DEFAULT NULL,
  `msg_size` varchar(64) DEFAULT NULL,
  `num_retries` varchar(64) DEFAULT NULL,
  `open_tracking` varchar(64) DEFAULT NULL,
  `queue_time` varchar(64) DEFAULT NULL,
  `rcpt_meta` varchar(64) DEFAULT NULL,
  `rcpt_tags` varchar(64) DEFAULT NULL,
  `rcpt_to` varchar(64) DEFAULT NULL,
  `raw_rcpt_to` varchar(64) DEFAULT NULL,
  `rcpt_type` varchar(64) DEFAULT NULL,
  `routing_domain` varchar(64) DEFAULT NULL,
  `sending_ip` varchar(64) DEFAULT NULL,
  `subaccount_id` varchar(64) DEFAULT NULL,
  `subject` varchar(64) DEFAULT NULL,
  `template_id` varchar(64) DEFAULT NULL,
  `template_version` varchar(64) DEFAULT NULL,
  `timestamp` varchar(64) DEFAULT NULL,
  `transactional` varchar(64) DEFAULT NULL,
  `transmission_id` varchar(64) DEFAULT NULL,
  `type` varchar(64) NOT NULL,
  `user_agent` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_amp_initial_open
-- ----------------------------

-- ----------------------------
-- Table structure for `tbl_amp_open`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_amp_open`;
CREATE TABLE `tbl_amp_open` (
  `ab_test_id` varchar(64) DEFAULT NULL,
  `ab_test_version` varchar(64) DEFAULT NULL,
  `amp_enabled` varchar(64) DEFAULT NULL,
  `campaign_id` varchar(64) DEFAULT NULL,
  `click_tracking` varchar(64) DEFAULT NULL,
  `customer_id` varchar(64) DEFAULT NULL,
  `delv_method` varchar(64) DEFAULT NULL,
  `event_id` varchar(64) DEFAULT NULL,
  `friendly_from` varchar(64) DEFAULT NULL,
  `geo_ip` varchar(64) DEFAULT NULL,
  `injection_time` varchar(64) DEFAULT NULL,
  `initial_pixel` varchar(64) DEFAULT NULL,
  `ip_address` varchar(64) DEFAULT NULL,
  `ip_pool` varchar(64) DEFAULT NULL,
  `message_id` varchar(64) DEFAULT NULL,
  `msg_from` varchar(64) DEFAULT NULL,
  `msg_size` varchar(64) DEFAULT NULL,
  `num_retries` varchar(64) DEFAULT NULL,
  `open_tracking` varchar(64) DEFAULT NULL,
  `queue_time` varchar(64) DEFAULT NULL,
  `rcpt_meta` varchar(64) DEFAULT NULL,
  `rcpt_tags` varchar(64) DEFAULT NULL,
  `rcpt_to` varchar(64) DEFAULT NULL,
  `raw_rcpt_to` varchar(64) DEFAULT NULL,
  `rcpt_type` varchar(64) DEFAULT NULL,
  `routing_domain` varchar(64) DEFAULT NULL,
  `sending_ip` varchar(64) DEFAULT NULL,
  `subaccount_id` varchar(64) DEFAULT NULL,
  `subject` varchar(64) DEFAULT NULL,
  `template_id` varchar(64) DEFAULT NULL,
  `template_version` varchar(64) DEFAULT NULL,
  `timestamp` varchar(64) DEFAULT NULL,
  `transactional` varchar(64) DEFAULT NULL,
  `transmission_id` varchar(64) DEFAULT NULL,
  `type` varchar(64) NOT NULL,
  `user_agent` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_amp_open
-- ----------------------------

-- ----------------------------
-- Table structure for `tbl_bounce`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_bounce`;
CREATE TABLE `tbl_bounce` (
  `amp_enabled` varchar(64) DEFAULT NULL,
  `bounce_class` varchar(64) DEFAULT NULL,
  `campaign_id` varchar(64) DEFAULT NULL,
  `click_tracking` varchar(64) DEFAULT NULL,
  `customer_id` varchar(64) DEFAULT NULL,
  `delv_method` varchar(64) DEFAULT NULL,
  `device_token` varchar(64) DEFAULT NULL,
  `error_code` varchar(64) DEFAULT NULL,
  `event_id` varchar(64) DEFAULT NULL,
  `friendly_from` varchar(64) DEFAULT NULL,
  `initial_pixel` varchar(64) DEFAULT NULL,
  `injection_time` varchar(64) DEFAULT NULL,
  `ip_address` varchar(64) DEFAULT NULL,
  `ip_pool` varchar(64) DEFAULT NULL,
  `message_id` varchar(64) DEFAULT NULL,
  `msg_from` varchar(64) DEFAULT NULL,
  `msg_size` varchar(64) DEFAULT NULL,
  `num_retries` varchar(64) DEFAULT NULL,
  `open_tracking` varchar(64) DEFAULT NULL,
  `rcpt_meta` varchar(64) DEFAULT NULL,
  `rcpt_tags` varchar(64) DEFAULT NULL,
  `rcpt_to` varchar(64) DEFAULT NULL,
  `raw_rcpt_to` varchar(64) DEFAULT NULL,
  `rcpt_type` varchar(64) DEFAULT NULL,
  `raw_reason` varchar(64) DEFAULT NULL,
  `reason` varchar(64) DEFAULT NULL,
  `recv_method` varchar(64) DEFAULT NULL,
  `routing_domain` varchar(64) DEFAULT NULL,
  `sending_ip` varchar(64) DEFAULT NULL,
  `sms_coding` varchar(64) DEFAULT NULL,
  `sms_dst` varchar(64) DEFAULT NULL,
  `sms_dst_npi` varchar(64) DEFAULT NULL,
  `sms_dst_ton` varchar(64) DEFAULT NULL,
  `sms_src` varchar(64) DEFAULT NULL,
  `sms_src_npi` varchar(64) DEFAULT NULL,
  `sms_src_ton` varchar(64) DEFAULT NULL,
  `subaccount_id` varchar(64) DEFAULT NULL,
  `subject` varchar(64) DEFAULT NULL,
  `template_id` varchar(64) DEFAULT NULL,
  `template_version` varchar(64) DEFAULT NULL,
  `timestamp` varchar(64) DEFAULT NULL,
  `transactional` varchar(64) DEFAULT NULL,
  `transmission_id` varchar(64) DEFAULT NULL,
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_bounce
-- ----------------------------

-- ----------------------------
-- Table structure for `tbl_click`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_click`;
CREATE TABLE `tbl_click` (
  `ab_test_id` varchar(64) DEFAULT NULL,
  `ab_test_version` varchar(64) DEFAULT NULL,
  `amp_enabled` varchar(64) DEFAULT NULL,
  `campaign_id` varchar(64) DEFAULT NULL,
  `click_tracking` varchar(64) DEFAULT NULL,
  `customer_id` varchar(64) DEFAULT NULL,
  `delv_method` varchar(64) DEFAULT NULL,
  `event_id` varchar(64) DEFAULT NULL,
  `friendly_from` varchar(64) DEFAULT NULL,
  `geo_ip` varchar(64) DEFAULT NULL,
  `injection_time` varchar(64) DEFAULT NULL,
  `initial_pixel` varchar(64) DEFAULT NULL,
  `ip_address` varchar(64) DEFAULT NULL,
  `ip_pool` varchar(64) DEFAULT NULL,
  `message_id` varchar(64) DEFAULT NULL,
  `msg_from` varchar(64) DEFAULT NULL,
  `msg_size` varchar(64) DEFAULT NULL,
  `num_retries` varchar(64) DEFAULT NULL,
  `open_tracking` varchar(64) DEFAULT NULL,
  `queue_time` varchar(64) DEFAULT NULL,
  `rcpt_meta` varchar(64) DEFAULT NULL,
  `rcpt_tags` varchar(64) DEFAULT NULL,
  `rcpt_to` varchar(64) DEFAULT NULL,
  `raw_rcpt_to` varchar(64) DEFAULT NULL,
  `rcpt_type` varchar(64) DEFAULT NULL,
  `routing_domain` varchar(64) DEFAULT NULL,
  `sending_ip` varchar(64) DEFAULT NULL,
  `subaccount_id` varchar(64) DEFAULT NULL,
  `subject` varchar(64) DEFAULT NULL,
  `target_link_name` varchar(64) DEFAULT NULL,
  `target_link_url` varchar(64) DEFAULT NULL,
  `template_id` varchar(64) DEFAULT NULL,
  `template_version` varchar(64) DEFAULT NULL,
  `timestamp` varchar(64) DEFAULT NULL,
  `transactional` varchar(64) DEFAULT NULL,
  `transmission_id` varchar(64) DEFAULT NULL,
  `type` varchar(64) NOT NULL,
  `user_agent` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_click
-- ----------------------------

-- ----------------------------
-- Table structure for `tbl_delay`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_delay`;
CREATE TABLE `tbl_delay` (
  `amp_enabled` varchar(64) DEFAULT NULL,
  `bounce_class` varchar(64) DEFAULT NULL,
  `campaign_id` varchar(64) DEFAULT NULL,
  `click_tracking` varchar(64) DEFAULT NULL,
  `customer_id` varchar(64) DEFAULT NULL,
  `delv_method` varchar(64) DEFAULT NULL,
  `device_token` varchar(64) DEFAULT NULL,
  `error_code` varchar(64) DEFAULT NULL,
  `event_id` varchar(64) DEFAULT NULL,
  `friendly_from` varchar(64) DEFAULT NULL,
  `injection_time` varchar(64) DEFAULT NULL,
  `initial_pixel` varchar(64) DEFAULT NULL,
  `ip_address` varchar(64) DEFAULT NULL,
  `ip_pool` varchar(64) DEFAULT NULL,
  `message_id` varchar(64) DEFAULT NULL,
  `msg_from` varchar(64) DEFAULT NULL,
  `msg_size` varchar(64) DEFAULT NULL,
  `num_retries` varchar(64) DEFAULT NULL,
  `open_tracking` varchar(64) DEFAULT NULL,
  `queue_time` varchar(64) DEFAULT NULL,
  `rcpt_meta` varchar(64) DEFAULT NULL,
  `rcpt_tags` varchar(64) DEFAULT NULL,
  `rcpt_to` varchar(64) DEFAULT NULL,
  `raw_rcpt_to` varchar(64) DEFAULT NULL,
  `rcpt_type` varchar(64) DEFAULT NULL,
  `raw_reason` varchar(64) DEFAULT NULL,
  `reason` varchar(64) DEFAULT NULL,
  `recv_method` varchar(64) DEFAULT NULL,
  `routing_domain` varchar(64) DEFAULT NULL,
  `sending_ip` varchar(64) DEFAULT NULL,
  `sms_coding` varchar(64) DEFAULT NULL,
  `sms_dst` varchar(64) DEFAULT NULL,
  `sms_dst_npi` varchar(64) DEFAULT NULL,
  `sms_dst_ton` varchar(64) DEFAULT NULL,
  `sms_src` varchar(64) DEFAULT NULL,
  `sms_src_npi` varchar(64) DEFAULT NULL,
  `sms_src_ton` varchar(64) DEFAULT NULL,
  `subaccount_id` varchar(64) DEFAULT NULL,
  `subject` varchar(64) DEFAULT NULL,
  `template_id` varchar(64) DEFAULT NULL,
  `template_version` varchar(64) DEFAULT NULL,
  `timestamp` varchar(64) DEFAULT NULL,
  `transactional` varchar(64) DEFAULT NULL,
  `transmission_id` varchar(64) DEFAULT NULL,
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_delay
-- ----------------------------

-- ----------------------------
-- Table structure for `tbl_delivery`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_delivery`;
CREATE TABLE `tbl_delivery` (
  `ab_test_id` varchar(64) DEFAULT NULL,
  `ab_test_version` varchar(64) DEFAULT NULL,
  `amp_enabled` varchar(64) DEFAULT NULL,
  `campaign_id` varchar(64) DEFAULT NULL,
  `click_tracking` varchar(64) DEFAULT NULL,
  `customer_id` varchar(64) DEFAULT NULL,
  `delv_method` varchar(64) DEFAULT NULL,
  `device_token` varchar(64) DEFAULT NULL,
  `event_id` varchar(64) DEFAULT NULL,
  `friendly_from` varchar(64) DEFAULT NULL,
  `initial_pixel` varchar(64) DEFAULT NULL,
  `injection_time` varchar(64) DEFAULT NULL,
  `ip_address` varchar(64) DEFAULT NULL,
  `ip_pool` varchar(64) DEFAULT NULL,
  `message_id` varchar(64) DEFAULT NULL,
  `msg_from` varchar(64) DEFAULT NULL,
  `msg_size` varchar(64) DEFAULT NULL,
  `num_retries` varchar(64) DEFAULT NULL,
  `open_tracking` varchar(64) DEFAULT NULL,
  `queue_time` varchar(64) DEFAULT NULL,
  `rcpt_meta` varchar(64) DEFAULT NULL,
  `rcpt_tags` varchar(64) DEFAULT NULL,
  `rcpt_to` varchar(64) DEFAULT NULL,
  `raw_rcpt_to` varchar(64) DEFAULT NULL,
  `rcpt_type` varchar(64) DEFAULT NULL,
  `recv_method` varchar(64) DEFAULT NULL,
  `routing_domain` varchar(64) DEFAULT NULL,
  `sending_ip` varchar(64) DEFAULT NULL,
  `subaccount_id` varchar(64) DEFAULT NULL,
  `subject` varchar(64) DEFAULT NULL,
  `sms_coding` varchar(64) DEFAULT NULL,
  `sms_dst` varchar(64) DEFAULT NULL,
  `sms_dst_npi` varchar(64) DEFAULT NULL,
  `sms_dst_ton` varchar(64) DEFAULT NULL,
  `sms_remoteids` varchar(64) DEFAULT NULL,
  `sms_segments` varchar(64) DEFAULT NULL,
  `sms_src` varchar(64) DEFAULT NULL,
  `sms_src_npi` varchar(64) DEFAULT NULL,
  `sms_src_ton` varchar(64) DEFAULT NULL,
  `template_id` varchar(64) DEFAULT NULL,
  `template_version` varchar(64) DEFAULT NULL,
  `timestamp` varchar(64) DEFAULT NULL,
  `transactional` varchar(64) DEFAULT NULL,
  `transmission_id` varchar(64) DEFAULT NULL,
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_delivery
-- ----------------------------

-- ----------------------------
-- Table structure for `tbl_event`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_event`;
CREATE TABLE `tbl_event` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` int(10) NOT NULL,
  `event_id` varchar(20) NOT NULL,
  `timestamp` varchar(20) NOT NULL DEFAULT '',
  `campaign_id` varchar(20) DEFAULT NULL,
  `event_type` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_event
-- ----------------------------

-- ----------------------------
-- Table structure for `tbl_generation_failure`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_generation_failure`;
CREATE TABLE `tbl_generation_failure` (
  `ab_test_id` varchar(64) DEFAULT NULL,
  `ab_test_version` varchar(64) DEFAULT NULL,
  `campaign_id` varchar(64) DEFAULT NULL,
  `customer_id` varchar(64) DEFAULT NULL,
  `error_code` varchar(64) DEFAULT NULL,
  `event_id` varchar(64) DEFAULT NULL,
  `friendly_from` varchar(64) DEFAULT NULL,
  `ip_pool` varchar(64) DEFAULT NULL,
  `rcpt_meta` varchar(64) DEFAULT NULL,
  `rcpt_subs` varchar(64) DEFAULT NULL,
  `rcpt_tags` varchar(64) DEFAULT NULL,
  `rcpt_to` varchar(64) DEFAULT NULL,
  `raw_rcpt_to` varchar(64) DEFAULT NULL,
  `raw_reason` varchar(64) DEFAULT NULL,
  `reason` varchar(64) DEFAULT NULL,
  `recv_method` varchar(64) DEFAULT NULL,
  `routing_domain` varchar(64) DEFAULT NULL,
  `sending_ip` varchar(64) DEFAULT NULL,
  `subaccount_id` varchar(64) DEFAULT NULL,
  `template_id` varchar(64) DEFAULT NULL,
  `template_version` varchar(64) DEFAULT NULL,
  `timestamp` varchar(64) DEFAULT NULL,
  `transactional` varchar(64) DEFAULT NULL,
  `transmission_id` varchar(64) DEFAULT NULL,
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_generation_failure
-- ----------------------------

-- ----------------------------
-- Table structure for `tbl_generation_rejection`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_generation_rejection`;
CREATE TABLE `tbl_generation_rejection` (
  `ab_test_id` varchar(64) DEFAULT NULL,
  `ab_test_version` varchar(64) DEFAULT NULL,
  `campaign_id` varchar(64) DEFAULT NULL,
  `customer_id` varchar(64) DEFAULT NULL,
  `error_code` varchar(64) DEFAULT NULL,
  `event_id` varchar(64) DEFAULT NULL,
  `friendly_from` varchar(64) DEFAULT NULL,
  `ip_pool` varchar(64) DEFAULT NULL,
  `rcpt_meta` varchar(64) DEFAULT NULL,
  `rcpt_subs` varchar(64) DEFAULT NULL,
  `rcpt_tags` varchar(64) DEFAULT NULL,
  `rcpt_to` varchar(64) DEFAULT NULL,
  `raw_rcpt_to` varchar(64) DEFAULT NULL,
  `raw_reason` varchar(64) DEFAULT NULL,
  `reason` varchar(64) DEFAULT NULL,
  `recv_method` varchar(64) DEFAULT NULL,
  `routing_domain` varchar(64) DEFAULT NULL,
  `sending_ip` varchar(64) DEFAULT NULL,
  `subaccount_id` varchar(64) DEFAULT NULL,
  `subject` varchar(64) DEFAULT NULL,
  `template_id` varchar(64) DEFAULT NULL,
  `template_version` varchar(64) DEFAULT NULL,
  `timestamp` varchar(64) DEFAULT NULL,
  `transactional` varchar(64) DEFAULT NULL,
  `transmission_id` varchar(64) DEFAULT NULL,
  `type` varchar(64) NOT NULL,
  `bounce_class` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_generation_rejection
-- ----------------------------

-- ----------------------------
-- Table structure for `tbl_initial_open`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_initial_open`;
CREATE TABLE `tbl_initial_open` (
  `ab_test_id` varchar(64) DEFAULT NULL,
  `ab_test_version` varchar(64) DEFAULT NULL,
  `amp_enabled` varchar(64) DEFAULT NULL,
  `campaign_id` varchar(64) DEFAULT NULL,
  `click_tracking` varchar(64) DEFAULT NULL,
  `customer_id` varchar(64) DEFAULT NULL,
  `delv_method` varchar(64) DEFAULT NULL,
  `event_id` varchar(64) DEFAULT NULL,
  `friendly_from` varchar(64) DEFAULT NULL,
  `geo_ip` varchar(64) DEFAULT NULL,
  `injection_time` varchar(64) DEFAULT NULL,
  `initial_pixel` varchar(64) DEFAULT NULL,
  `ip_address` varchar(64) DEFAULT NULL,
  `ip_pool` varchar(64) DEFAULT NULL,
  `message_id` varchar(64) DEFAULT NULL,
  `msg_from` varchar(64) DEFAULT NULL,
  `msg_size` varchar(64) DEFAULT NULL,
  `num_retries` varchar(64) DEFAULT NULL,
  `open_tracking` varchar(64) DEFAULT NULL,
  `queue_time` varchar(64) DEFAULT NULL,
  `rcpt_meta` varchar(64) DEFAULT NULL,
  `rcpt_tags` varchar(64) DEFAULT NULL,
  `rcpt_to` varchar(64) DEFAULT NULL,
  `raw_rcpt_to` varchar(64) DEFAULT NULL,
  `rcpt_type` varchar(64) DEFAULT NULL,
  `routing_domain` varchar(64) DEFAULT NULL,
  `sending_ip` varchar(64) DEFAULT NULL,
  `subaccount_id` varchar(64) DEFAULT NULL,
  `subject` varchar(64) DEFAULT NULL,
  `template_id` varchar(64) DEFAULT NULL,
  `template_version` varchar(64) DEFAULT NULL,
  `timestamp` varchar(64) DEFAULT NULL,
  `transactional` varchar(64) DEFAULT NULL,
  `transmission_id` varchar(64) DEFAULT NULL,
  `type` varchar(64) NOT NULL,
  `user_agent` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_initial_open
-- ----------------------------

-- ----------------------------
-- Table structure for `tbl_injection`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_injection`;
CREATE TABLE `tbl_injection` (
  `ab_test_id` varchar(64) DEFAULT NULL,
  `ab_test_version` varchar(64) DEFAULT NULL,
  `amp_enabled` varchar(64) DEFAULT NULL,
  `campaign_id` varchar(64) DEFAULT NULL,
  `click_tracking` varchar(64) DEFAULT NULL,
  `customer_id` varchar(64) DEFAULT NULL,
  `event_id` varchar(64) DEFAULT NULL,
  `friendly_from` varchar(64) DEFAULT NULL,
  `initial_pixel` varchar(64) DEFAULT NULL,
  `injection_time` varchar(64) DEFAULT NULL,
  `ip_pool` varchar(64) DEFAULT NULL,
  `message_id` varchar(64) DEFAULT NULL,
  `msg_from` varchar(64) DEFAULT NULL,
  `msg_size` varchar(64) DEFAULT NULL,
  `open_tracking` varchar(64) DEFAULT NULL,
  `rcpt_meta` varchar(64) DEFAULT NULL,
  `rcpt_tags` varchar(64) DEFAULT NULL,
  `rcpt_to` varchar(64) DEFAULT NULL,
  `raw_rcpt_to` varchar(64) DEFAULT NULL,
  `rcpt_type` varchar(64) DEFAULT NULL,
  `recv_method` varchar(64) DEFAULT NULL,
  `routing_domain` varchar(64) DEFAULT NULL,
  `sending_ip` varchar(64) DEFAULT NULL,
  `sms_coding` varchar(64) DEFAULT NULL,
  `sms_dst` varchar(64) DEFAULT NULL,
  `sms_dst_npi` varchar(64) DEFAULT NULL,
  `sms_dst_ton` varchar(64) DEFAULT NULL,
  `sms_segments` varchar(64) DEFAULT NULL,
  `sms_src` varchar(64) DEFAULT NULL,
  `sms_src_npi` varchar(64) DEFAULT NULL,
  `sms_src_ton` varchar(64) DEFAULT NULL,
  `sms_text` varchar(64) DEFAULT NULL,
  `subaccount_id` varchar(64) DEFAULT NULL,
  `subject` varchar(64) DEFAULT NULL,
  `template_id` varchar(64) DEFAULT NULL,
  `template_version` varchar(64) DEFAULT NULL,
  `timestamp` varchar(64) DEFAULT NULL,
  `transactional` varchar(64) DEFAULT NULL,
  `transmission_id` varchar(64) DEFAULT NULL,
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_injection
-- ----------------------------

-- ----------------------------
-- Table structure for `tbl_link_unsubscribe`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_link_unsubscribe`;
CREATE TABLE `tbl_link_unsubscribe` (
  `ab_test_id` varchar(64) DEFAULT NULL,
  `ab_test_version` varchar(64) DEFAULT NULL,
  `amp_enabled` varchar(64) DEFAULT NULL,
  `campaign_id` varchar(64) DEFAULT NULL,
  `click_tracking` varchar(64) DEFAULT NULL,
  `customer_id` varchar(64) DEFAULT NULL,
  `delv_method` varchar(64) DEFAULT NULL,
  `event_id` varchar(64) DEFAULT NULL,
  `friendly_from` varchar(64) DEFAULT NULL,
  `injection_time` varchar(64) DEFAULT NULL,
  `initial_pixel` varchar(64) DEFAULT NULL,
  `ip_address` varchar(64) DEFAULT NULL,
  `ip_pool` varchar(64) DEFAULT NULL,
  `mailfrom` varchar(64) DEFAULT NULL,
  `message_id` varchar(64) DEFAULT NULL,
  `msg_from` varchar(64) DEFAULT NULL,
  `msg_size` varchar(64) DEFAULT NULL,
  `num_retries` varchar(64) DEFAULT NULL,
  `open_tracking` varchar(64) DEFAULT NULL,
  `queue_time` varchar(64) DEFAULT NULL,
  `raw_rcpt_to` varchar(64) DEFAULT NULL,
  `rcpt_meta` varchar(64) DEFAULT NULL,
  `rcpt_tags` varchar(64) DEFAULT NULL,
  `rcpt_to` varchar(64) DEFAULT NULL,
  `rcpt_type` varchar(64) DEFAULT NULL,
  `recv_method` varchar(64) DEFAULT NULL,
  `routing_domain` varchar(64) DEFAULT NULL,
  `sending_ip` varchar(64) DEFAULT NULL,
  `subaccount_id` varchar(64) DEFAULT NULL,
  `subject` varchar(64) DEFAULT NULL,
  `template_id` varchar(64) DEFAULT NULL,
  `template_version` varchar(64) DEFAULT NULL,
  `timestamp` varchar(64) DEFAULT NULL,
  `transactional` varchar(64) DEFAULT NULL,
  `transmission_id` varchar(64) DEFAULT NULL,
  `type` varchar(64) NOT NULL,
  `user_agent` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_link_unsubscribe
-- ----------------------------

-- ----------------------------
-- Table structure for `tbl_list_unsubscribe`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_list_unsubscribe`;
CREATE TABLE `tbl_list_unsubscribe` (
  `ab_test_id` varchar(64) DEFAULT NULL,
  `ab_test_version` varchar(64) DEFAULT NULL,
  `amp_enabled` varchar(64) DEFAULT NULL,
  `campaign_id` varchar(64) DEFAULT NULL,
  `click_tracking` varchar(64) DEFAULT NULL,
  `customer_id` varchar(64) DEFAULT NULL,
  `event_id` varchar(64) DEFAULT NULL,
  `friendly_from` varchar(64) DEFAULT NULL,
  `injection_time` varchar(64) DEFAULT NULL,
  `initial_pixel` varchar(64) DEFAULT NULL,
  `ip_address` varchar(64) DEFAULT NULL,
  `ip_pool` varchar(64) DEFAULT NULL,
  `mailfrom` varchar(64) DEFAULT NULL,
  `message_id` varchar(64) DEFAULT NULL,
  `msg_from` varchar(64) DEFAULT NULL,
  `msg_size` varchar(64) DEFAULT NULL,
  `num_retries` varchar(64) DEFAULT NULL,
  `open_tracking` varchar(64) DEFAULT NULL,
  `queue_time` varchar(64) DEFAULT NULL,
  `raw_rcpt_to` varchar(64) DEFAULT NULL,
  `rcpt_meta` varchar(64) DEFAULT NULL,
  `rcpt_tags` varchar(64) DEFAULT NULL,
  `rcpt_to` varchar(64) DEFAULT NULL,
  `rcpt_type` varchar(64) DEFAULT NULL,
  `recv_method` varchar(64) DEFAULT NULL,
  `routing_domain` varchar(64) DEFAULT NULL,
  `sending_ip` varchar(64) DEFAULT NULL,
  `subaccount_id` varchar(64) DEFAULT NULL,
  `subject` varchar(64) DEFAULT NULL,
  `template_id` varchar(64) DEFAULT NULL,
  `template_version` varchar(64) DEFAULT NULL,
  `timestamp` varchar(64) DEFAULT NULL,
  `transactional` varchar(64) DEFAULT NULL,
  `transmission_id` varchar(64) DEFAULT NULL,
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_list_unsubscribe
-- ----------------------------

-- ----------------------------
-- Table structure for `tbl_open`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_open`;
CREATE TABLE `tbl_open` (
  `ab_test_id` varchar(64) DEFAULT NULL,
  `ab_test_version` varchar(64) DEFAULT NULL,
  `amp_enabled` varchar(64) DEFAULT NULL,
  `campaign_id` varchar(64) DEFAULT NULL,
  `click_tracking` varchar(64) DEFAULT NULL,
  `customer_id` varchar(64) DEFAULT NULL,
  `delv_method` varchar(64) DEFAULT NULL,
  `event_id` varchar(64) DEFAULT NULL,
  `friendly_from` varchar(64) DEFAULT NULL,
  `geo_ip` varchar(64) DEFAULT NULL,
  `injection_time` varchar(64) DEFAULT NULL,
  `initial_pixel` varchar(64) DEFAULT NULL,
  `ip_address` varchar(64) DEFAULT NULL,
  `ip_pool` varchar(64) DEFAULT NULL,
  `message_id` varchar(64) DEFAULT NULL,
  `msg_from` varchar(64) DEFAULT NULL,
  `msg_size` varchar(64) DEFAULT NULL,
  `num_retries` varchar(64) DEFAULT NULL,
  `open_tracking` varchar(64) DEFAULT NULL,
  `queue_time` varchar(64) DEFAULT NULL,
  `rcpt_meta` varchar(64) DEFAULT NULL,
  `rcpt_tags` varchar(64) DEFAULT NULL,
  `rcpt_to` varchar(64) DEFAULT NULL,
  `raw_rcpt_to` varchar(64) DEFAULT NULL,
  `rcpt_type` varchar(64) DEFAULT NULL,
  `routing_domain` varchar(64) DEFAULT NULL,
  `sending_ip` varchar(64) DEFAULT NULL,
  `subaccount_id` varchar(64) DEFAULT NULL,
  `subject` varchar(64) DEFAULT NULL,
  `template_id` varchar(64) DEFAULT NULL,
  `template_version` varchar(64) DEFAULT NULL,
  `timestamp` varchar(64) DEFAULT NULL,
  `transactional` varchar(64) DEFAULT NULL,
  `transmission_id` varchar(64) DEFAULT NULL,
  `type` varchar(64) NOT NULL,
  `user_agent` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_open
-- ----------------------------

-- ----------------------------
-- Table structure for `tbl_out_of_band`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_out_of_band`;
CREATE TABLE `tbl_out_of_band` (
  `ab_test_id` varchar(64) DEFAULT NULL,
  `ab_test_version` varchar(64) DEFAULT NULL,
  `bounce_class` varchar(64) DEFAULT NULL,
  `campaign_id` varchar(64) DEFAULT NULL,
  `customer_id` varchar(64) DEFAULT NULL,
  `delv_method` varchar(64) DEFAULT NULL,
  `device_token` varchar(64) DEFAULT NULL,
  `error_code` varchar(64) DEFAULT NULL,
  `event_id` varchar(64) DEFAULT NULL,
  `friendly_from` varchar(64) DEFAULT NULL,
  `injection_time` varchar(64) DEFAULT NULL,
  `ip_address` varchar(64) DEFAULT NULL,
  `ip_pool` varchar(64) DEFAULT NULL,
  `message_id` varchar(64) DEFAULT NULL,
  `msg_from` varchar(64) DEFAULT NULL,
  `msg_size` varchar(64) DEFAULT NULL,
  `num_retries` varchar(64) DEFAULT NULL,
  `queue_time` varchar(64) DEFAULT NULL,
  `raw_rcpt_to` varchar(64) DEFAULT NULL,
  `raw_reason` varchar(64) DEFAULT NULL,
  `rcpt_meta` varchar(64) DEFAULT NULL,
  `rcpt_tags` varchar(64) DEFAULT NULL,
  `rcpt_to` varchar(64) DEFAULT NULL,
  `reason` varchar(64) DEFAULT NULL,
  `recv_method` varchar(64) DEFAULT NULL,
  `routing_domain` varchar(64) DEFAULT NULL,
  `sending_ip` varchar(64) DEFAULT NULL,
  `subaccount_id` varchar(64) DEFAULT NULL,
  `subject` varchar(64) DEFAULT NULL,
  `template_id` varchar(64) DEFAULT NULL,
  `template_version` varchar(64) DEFAULT NULL,
  `timestamp` varchar(64) DEFAULT NULL,
  `transactional` varchar(64) DEFAULT NULL,
  `transmission_id` varchar(64) DEFAULT NULL,
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_out_of_band
-- ----------------------------

-- ----------------------------
-- Table structure for `tbl_policy_rejection`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_policy_rejection`;
CREATE TABLE `tbl_policy_rejection` (
  `ab_test_id` varchar(64) DEFAULT NULL,
  `ab_test_version` varchar(64) DEFAULT NULL,
  `campaign_id` varchar(64) DEFAULT NULL,
  `customer_id` varchar(64) DEFAULT NULL,
  `error_code` varchar(64) DEFAULT NULL,
  `event_id` varchar(64) DEFAULT NULL,
  `friendly_from` varchar(64) DEFAULT NULL,
  `injection_time` varchar(64) DEFAULT NULL,
  `ip_pool` varchar(64) DEFAULT NULL,
  `message_id` varchar(64) DEFAULT NULL,
  `msg_from` varchar(64) DEFAULT NULL,
  `rcpt_meta` varchar(64) DEFAULT NULL,
  `rcpt_tags` varchar(64) DEFAULT NULL,
  `rcpt_to` varchar(64) DEFAULT NULL,
  `raw_rcpt_to` varchar(64) DEFAULT NULL,
  `rcpt_type` varchar(64) DEFAULT NULL,
  `raw_reason` varchar(64) DEFAULT NULL,
  `reason` varchar(64) DEFAULT NULL,
  `recv_method` varchar(64) DEFAULT NULL,
  `remote_addr` varchar(64) DEFAULT NULL,
  `sending_ip` varchar(64) DEFAULT NULL,
  `subaccount_id` varchar(64) DEFAULT NULL,
  `template_id` varchar(64) DEFAULT NULL,
  `template_version` varchar(64) DEFAULT NULL,
  `timestamp` varchar(64) DEFAULT NULL,
  `transactional` varchar(64) DEFAULT NULL,
  `transmission_id` varchar(64) DEFAULT NULL,
  `type` varchar(64) NOT NULL,
  `bounce_class` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_policy_rejection
-- ----------------------------

-- ----------------------------
-- Table structure for `tbl_relay_delivery`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_relay_delivery`;
CREATE TABLE `tbl_relay_delivery` (
  `event_id` varchar(64) DEFAULT NULL,
  `routing_domain` varchar(64) DEFAULT NULL,
  `rcpt_to` varchar(64) DEFAULT NULL,
  `raw_rcpt_to` varchar(64) DEFAULT NULL,
  `type` varchar(64) NOT NULL,
  `message_id` varchar(64) DEFAULT NULL,
  `msg_from` varchar(64) DEFAULT NULL,
  `recv_method` varchar(64) DEFAULT NULL,
  `subaccount_id` varchar(64) DEFAULT NULL,
  `ip_pool` varchar(64) DEFAULT NULL,
  `queue_time` varchar(64) DEFAULT NULL,
  `customer_id` varchar(64) DEFAULT NULL,
  `timestamp` varchar(64) DEFAULT NULL,
  `num_retries` varchar(64) DEFAULT NULL,
  `origination` varchar(64) DEFAULT NULL,
  `delv_method` varchar(64) DEFAULT NULL,
  `sending_ip` varchar(64) DEFAULT NULL,
  `injection_time` varchar(64) DEFAULT NULL,
  `relay_id` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_relay_delivery
-- ----------------------------

-- ----------------------------
-- Table structure for `tbl_relay_injection`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_relay_injection`;
CREATE TABLE `tbl_relay_injection` (
  `event_id` varchar(64) DEFAULT NULL,
  `rcpt_to` varchar(64) DEFAULT NULL,
  `raw_rcpt_to` varchar(64) DEFAULT NULL,
  `msg_size` varchar(64) DEFAULT NULL,
  `routing_domain` varchar(64) DEFAULT NULL,
  `customer_id` varchar(64) DEFAULT NULL,
  `sending_ip` varchar(64) DEFAULT NULL,
  `type` varchar(64) NOT NULL,
  `subaccount_id` varchar(64) DEFAULT NULL,
  `timestamp` varchar(64) DEFAULT NULL,
  `ip_pool` varchar(64) DEFAULT NULL,
  `origination` varchar(64) DEFAULT NULL,
  `message_id` varchar(64) DEFAULT NULL,
  `msg_from` varchar(64) DEFAULT NULL,
  `recv_method` varchar(64) DEFAULT NULL,
  `injection_time` varchar(64) DEFAULT NULL,
  `relay_id` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_relay_injection
-- ----------------------------

-- ----------------------------
-- Table structure for `tbl_relay_permfail`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_relay_permfail`;
CREATE TABLE `tbl_relay_permfail` (
  `event_id` varchar(64) DEFAULT NULL,
  `routing_domain` varchar(64) DEFAULT NULL,
  `rcpt_to` varchar(64) DEFAULT NULL,
  `raw_rcpt_to` varchar(64) DEFAULT NULL,
  `type` varchar(64) NOT NULL,
  `message_id` varchar(64) DEFAULT NULL,
  `msg_from` varchar(64) DEFAULT NULL,
  `recv_method` varchar(64) DEFAULT NULL,
  `ip_pool` varchar(64) DEFAULT NULL,
  `queue_time` varchar(64) DEFAULT NULL,
  `subaccount_id` varchar(64) DEFAULT NULL,
  `customer_id` varchar(64) DEFAULT NULL,
  `timestamp` varchar(64) DEFAULT NULL,
  `num_retries` varchar(64) DEFAULT NULL,
  `delv_method` varchar(64) DEFAULT NULL,
  `origination` varchar(64) DEFAULT NULL,
  `raw_reason` varchar(64) DEFAULT NULL,
  `reason` varchar(64) DEFAULT NULL,
  `sending_ip` varchar(64) DEFAULT NULL,
  `error_code` varchar(64) DEFAULT NULL,
  `injection_time` varchar(64) DEFAULT NULL,
  `relay_id` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_relay_permfail
-- ----------------------------

-- ----------------------------
-- Table structure for `tbl_relay_rejection`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_relay_rejection`;
CREATE TABLE `tbl_relay_rejection` (
  `raw_reason` varchar(64) DEFAULT NULL,
  `reason` varchar(64) DEFAULT NULL,
  `rcpt_to` varchar(64) DEFAULT NULL,
  `raw_rcpt_to` varchar(64) DEFAULT NULL,
  `error_code` varchar(64) DEFAULT NULL,
  `subaccount_id` varchar(64) DEFAULT NULL,
  `event_id` varchar(64) DEFAULT NULL,
  `message_id` varchar(64) DEFAULT NULL,
  `msg_from` varchar(64) DEFAULT NULL,
  `recv_method` varchar(64) DEFAULT NULL,
  `remote_addr` varchar(64) DEFAULT NULL,
  `timestamp` varchar(64) DEFAULT NULL,
  `customer_id` varchar(64) DEFAULT NULL,
  `origination` varchar(64) DEFAULT NULL,
  `type` varchar(64) NOT NULL,
  `bounce_class` varchar(64) DEFAULT NULL,
  `injection_time` varchar(64) DEFAULT NULL,
  `relay_id` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_relay_rejection
-- ----------------------------

-- ----------------------------
-- Table structure for `tbl_relay_tempfail`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_relay_tempfail`;
CREATE TABLE `tbl_relay_tempfail` (
  `event_id` varchar(64) DEFAULT NULL,
  `routing_domain` varchar(64) DEFAULT NULL,
  `rcpt_to` varchar(64) DEFAULT NULL,
  `raw_rcpt_to` varchar(64) DEFAULT NULL,
  `type` varchar(64) NOT NULL,
  `message_id` varchar(64) DEFAULT NULL,
  `msg_from` varchar(64) DEFAULT NULL,
  `recv_method` varchar(64) DEFAULT NULL,
  `ip_pool` varchar(64) DEFAULT NULL,
  `queue_time` varchar(64) DEFAULT NULL,
  `customer_id` varchar(64) DEFAULT NULL,
  `subaccount_id` varchar(64) DEFAULT NULL,
  `timestamp` varchar(64) DEFAULT NULL,
  `num_retries` varchar(64) DEFAULT NULL,
  `delv_method` varchar(64) DEFAULT NULL,
  `origination` varchar(64) DEFAULT NULL,
  `raw_reason` varchar(64) DEFAULT NULL,
  `reason` varchar(64) DEFAULT NULL,
  `sending_ip` varchar(64) DEFAULT NULL,
  `error_code` varchar(64) DEFAULT NULL,
  `injection_time` varchar(64) DEFAULT NULL,
  `relay_id` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_relay_tempfail
-- ----------------------------

-- ----------------------------
-- Table structure for `tbl_spam_complaint`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_spam_complaint`;
CREATE TABLE `tbl_spam_complaint` (
  `ab_test_id` varchar(64) DEFAULT NULL,
  `ab_test_version` varchar(64) DEFAULT NULL,
  `campaign_id` varchar(64) DEFAULT NULL,
  `customer_id` varchar(64) DEFAULT NULL,
  `delv_method` varchar(64) DEFAULT NULL,
  `event_id` varchar(64) DEFAULT NULL,
  `fbtype` varchar(64) DEFAULT NULL,
  `friendly_from` varchar(64) DEFAULT NULL,
  `injection_time` varchar(64) DEFAULT NULL,
  `ip_address` varchar(64) DEFAULT NULL,
  `ip_pool` varchar(64) DEFAULT NULL,
  `message_id` varchar(64) DEFAULT NULL,
  `msg_from` varchar(64) DEFAULT NULL,
  `msg_size` varchar(64) DEFAULT NULL,
  `num_retries` varchar(64) DEFAULT NULL,
  `queue_time` varchar(64) DEFAULT NULL,
  `rcpt_meta` varchar(64) DEFAULT NULL,
  `rcpt_tags` varchar(64) DEFAULT NULL,
  `rcpt_to` varchar(64) DEFAULT NULL,
  `raw_rcpt_to` varchar(64) DEFAULT NULL,
  `rcpt_type` varchar(64) DEFAULT NULL,
  `report_by` varchar(64) DEFAULT NULL,
  `report_to` varchar(64) DEFAULT NULL,
  `routing_domain` varchar(64) DEFAULT NULL,
  `sending_ip` varchar(64) DEFAULT NULL,
  `subaccount_id` varchar(64) DEFAULT NULL,
  `subject` varchar(64) DEFAULT NULL,
  `template_id` varchar(64) DEFAULT NULL,
  `template_version` varchar(64) DEFAULT NULL,
  `timestamp` varchar(64) DEFAULT NULL,
  `transactional` varchar(64) DEFAULT NULL,
  `transmission_id` varchar(64) DEFAULT NULL,
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_spam_complaint
-- ----------------------------
