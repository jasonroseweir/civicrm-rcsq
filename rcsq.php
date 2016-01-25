<?php

require_once 'rcsq.civix.php';
require_once 'lib/JustGivingAPI/JustGivingClient.php';

$CHARITY_CONTACT_SUBTYPE = 'Charity';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function rcsq_civicrm_config(&$config) {
    _rcsq_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function rcsq_civicrm_xmlMenu(&$files) {
    _rcsq_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function rcsq_civicrm_install() {
    _rcsq_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function rcsq_civicrm_uninstall() {
    _rcsq_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function rcsq_civicrm_enable() {
    _rcsq_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function rcsq_civicrm_disable() {
    _rcsq_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function rcsq_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
    return _rcsq_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function rcsq_civicrm_managed(&$entities) {
    _rcsq_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function rcsq_civicrm_caseTypes(&$caseTypes) {
    _rcsq_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function rcsq_civicrm_angularModules(&$angularModules) {
    _rcsq_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function rcsq_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
    _rcsq_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Functions below this ship commented out. Uncomment as required.
 *

  /**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
  function rcsq_civicrm_preProcess($formName, &$form) {

  }

 */
/* * a
 * 
 * @param type $op
 * @param type $objectName
 * @param type $objectId
 * @param type $objectRef
 */
function rcsq_civicrm_post($op, $objectName, $objectId, &$objectRef) {
    global $CHARITY_CONTACT_SUBTYPE;

    //FIXME: Refactor Charity Create/Edit check into it's own function
    if (($op == 'create' || $op == 'edit') &&
            $objectName == 'Organization' &&
            strcmp($CHARITY_CONTACT_SUBTYPE, $objectRef->contact_sub_type)) {
        //Org is a Charity - Do Stuff!!   
        $custom_fieldvalues = rcsq_util_getCharityCustomFieldValues($objectRef->id);
    }
}

function rcsq_util_getCharityCustomFieldNames() {
    require_once 'CRM/Core/BAO/CustomField.php';
    $customFieldID_charityNumber = CRM_Core_BAO_CustomField::getCustomFieldID('Charity Number', 'Charity Info');
    $customFieldID_JG_charityId = CRM_Core_BAO_CustomField::getCustomFieldID('JG Charity ID', 'Charity Info');
    $customFieldID_JG_charityUrl = CRM_Core_BAO_CustomField::getCustomFieldID('JG Charity URL', 'Charity Info');

    return $custom_fields = [
        "Charity_Number" => "custom_" . $customFieldID_charityNumber,
        "JG_Charity_ID" => "custom_" . $customFieldID_JG_charityId,
        "JG_Charity_URL" => "custom_" . $customFieldID_JG_charityUrl,
    ];
}

function rcsq_util_getCharityCustomFieldValues($entity_id) {
    $custom_fieldnames = rcsq_util_getCharityCustomFieldNames();
    
    $get_params = array('entityID' => intval($entity_id),
        $custom_fieldnames['Charity_Number'] => 1, 
        $custom_fieldnames['JG_Charity_ID'] => 1, 
        $custom_fieldnames['JG_Charity_URL'] => 1);
    
    require_once 'CRM/Core/BAO/CustomValueTable.php';
    $values = CRM_Core_BAO_CustomValueTable::getValues($get_params);
    $my_charityNumber = $values[$custom_fieldnames['Charity_Number']];
    $my_charityJGID = $values[$custom_fieldnames['JG_Charity_ID']];
    $my_charityJGURL = $values[$custom_fieldnames['JG_Charity_URL']];
    
    return $custom_fieldvalues = [
        "Charity_Number" => $my_charityNumber,
        "JG_Charity_ID" => $my_charityJGID,
        "JG_Charity_URL" => $my_charityJGURL,
    ];
}
