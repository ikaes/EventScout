<?php
/*
Plugin Name: Scout Events 
Plugin URI: #
description: This plugin manage events
Version: 1.0
Author: Scout Solution Ltd
Author URI: #
License: GPL2
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


define( 'SE_DIR', __DIR__ );
define( 'SE_FILE', __FILE__ );

define( 'SE_URL', plugins_url('',SE_FILE) );
define( 'SE_ASSETS_URL', SE_URL.'/assets' );

define( 'SCOUT_EVENTS_POST_TYPE', 'scout-event');
define( 'SCOUT_EVENTS_LNG_TAG', 'scout-event');
define( 'SCOUT_DISCOUNT_POST_TYPE', 'se-discount');
define( 'SE_TNX_STATUS_PAID', 'Paid');
define( 'SE_TNX_STATUS_PENDING', 'Pending');


$seTransactionStatusArr = array('pending','complete');
$transaction_pending_index = '0';
$transaction_complete_index = '1';
$se_registrationStatusArr = array('pending','downpaid','complete');
$registration_downpaid_index = '1';
$registration_complete_index = '2';



require 'tools/assets-manager.php';
require 'tools/functions.php';
require 'tools/countries.php';
require 'tools/email-notifier.php';

require 'frontend/transaction-pay.php';
require 'frontend/transaction-confirm.php';
require 'frontend/event-checkout.php';

require 'backend/new_ticket-template.php';
require 'backend/events.php';
require 'backend/settings.php';
require 'backend/email-notification.php';
require 'backend/discount.php';

require 'backend/registration.php';
require 'frontend/save-checkout-info.php';
require 'backend/transaction.php';

require 'credit-card-processor.php';
