<?php

require_once 'smsbroadcast.civix.php';

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function smsbroadcast_civicrm_config(&$config) {
  _smsbroadcast_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function smsbroadcast_civicrm_xmlMenu(&$files) {
  _smsbroadcast_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function smsbroadcast_civicrm_install() {
  _smsbroadcast_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function smsbroadcast_civicrm_uninstall() {
  _smsbroadcast_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function smsbroadcast_civicrm_enable() {
  _smsbroadcast_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function smsbroadcast_civicrm_disable() {
  _smsbroadcast_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function smsbroadcast_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _smsbroadcast_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function smsbroadcast_civicrm_managed(&$entities) {
  $entities[] = [
    'name' => 'SMS Broadcast',
    'module' => 'org.civicrm.smsbroadcast',
    'entity' => 'OptionValue',
    'update' => 'never',
    'params' => [
      'version' => 3,
      'option_group_id' => 'sms_provider_name',
      'label' => 'SMS Broadcast',
      'value' => 'org.civicrm.smsbroadcast',
      'name'  => 'sms_broadcast',
      'is_active'  => 1,
    ],
  ];
  _smsbroadcast_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function smsbroadcast_civicrm_caseTypes(&$caseTypes) {
  _smsbroadcast_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function smsbroadcast_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _smsbroadcast_civix_civicrm_alterSettingsFolders($metaDataFolders);
}
