# SMS BroadCast Integration

#### org.civicrm.smsbroadcast

# Overview

This extension allows delivering short message service (SMS) messages through its 'SMS Broadcast' Gateway to mobile phone users.

## Installation

1. If you have not already done so, setup Extensions Directory
    1. Go to Administer >> System Settings >> Directories
        1. Set an appropriate value for CiviCRM Extensions Directory. For example, for Drupal, [civicrm.files]/ext/
        1. In a different window, ensure the directory exists and is readable by your web server process.
    1. Click Save.
1. If you have not already done so, setup Extensions Resources URL
    1. Go to Administer >> System Settings >> Resource URLs
        1. Beside Extension Resource URL, enter an appropriate values such as [civicrm.files]/ext/
    1. Click Save.
1. Install SMS BroadCast Integration extension
    1. Go to Administer >> Customize Data and Screens >> Manage Extensions.
    1. Click on Add New tab.
    1. If SMS BroadCast Integration is not in the list of extensions, manually download it and unzip it into the extensions direction setup above, then return to this page.
    1. Beside SMS BroadCast Integration, click Download.
    1. Review the information, then click Download and Install.
1. After installation go to "Administer -> System Settings -> SMS Providers".
    1. Click on "Add New Provider" button.
    1. Select the "SMS Broadcast" from select field.
    1. In Username field give the valid username for SMS Broadcast account.
    1. In Password field give the valid password for SMS Broadcast account.
    1. Select "http" for the API Type.
    1. Under API Parameters, enter "from=sender phone number or sender name of SMS Broadcast account".
1. The inbound SMS option must be activated by SMS Broadcast. If you require this function, please contact SMS Broadcast and
provide them with the one of the below URL on your server to send the data.
    1. For Drupal
        1. <yourdomain.org>/civicrm/sms/callback?provider=org.civicrm.smsbroadcast
    1. For Wordpress
        1. <yourdomain.org>/?page=CiviCRM&q=/civicrm/sms/callback&provider=org.civicrm.smsbroadcast
    1. For Joomla
        1. <yourdomain.org>/index.php?option=com_civicrm&task=civicrm/sms/callback&provider=org.civicrm.smsbroadcast

This extension has been developed and is being maintained by [Pradeep Nayak](https://github.com/pradpnayak/).
