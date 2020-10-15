<?php
namespace WeDevs\ERP\HRM\Update;


/*
 * Add transaction_charge column in `erp_acct_expenses` table
 */
function erp_acct_alter_acct_expenses_1_6_4() {
    global $wpdb;

    $table = $wpdb->prefix . 'erp_acct_expenses';
    $cols  = $wpdb->get_col( "DESC $table" );

    if ( !in_array( 'transaction_charge', $cols ) ) {
        $wpdb->query(
            $wpdb->prepare(
                "ALTER TABLE $table ADD `transaction_charge` decimal(20,2) DEFAULT 0 AFTER `trn_by`;"
            )
        );
    }

}

/*
 *  Add transaction_charge and ref column in `erp_acct_pay_purchase` table
 */
function erp_acct_alter_pay_purchase_1_6_4() {
    global $wpdb;

    $table = $wpdb->prefix . 'erp_acct_pay_purchase';
    $cols  = $wpdb->get_col( "DESC $table" );

    if ( !in_array( 'transaction_charge', $cols ) ) {
        $wpdb->query(
            $wpdb->prepare(
                "ALTER TABLE $table ADD `transaction_charge` decimal(20,2) DEFAULT 0 AFTER `trn_by`;"
            )
        );
    }

    if (!in_array('ref', $cols)) {
        $wpdb->query(
            $wpdb->prepare(
                "ALTER TABLE $table ADD `ref` varchar(255) NULL DEFAULT NULL AFTER `trn_by`;"
            )
        );
    }

}


/*
 * Add transaction_charge column in `erp_acct_pay_bill` table
 */
function erp_acct_alter_pay_bill_1_6_4() {
    global $wpdb;

    $table = $wpdb->prefix . 'erp_acct_pay_bill';
    $cols  = $wpdb->get_col( "DESC $table" );

    if ( !in_array( 'ref', $cols ) ) {
        $wpdb->query(
            $wpdb->prepare(
                "ALTER TABLE $table ADD `ref` varchar(255) NULL DEFAULT NULL  AFTER `particulars`;"
            )
        );
    }


}

/*
 * Add transaction_charge column in `erp_acct_pay_bill` table
 */
function erp_acct_alter_purchase_1_6_4() {
    global $wpdb;

    $table = $wpdb->prefix . 'erp_acct_purchase';
    $cols  = $wpdb->get_col( "DESC $table" );

    if ( !in_array( 'tax', $cols ) ) {
        $wpdb->query(
            $wpdb->prepare(
                "ALTER TABLE $table ADD `tax` decimal(20,2)  NULL DEFAULT NULL  AFTER `amount`;"
            )
        );
    }

    if ( !in_array( 'tax_zone_id', $cols ) ) {
        $wpdb->query(
            $wpdb->prepare(
                "ALTER TABLE $table ADD `tax_zone_id` integer  NULL DEFAULT NULL  AFTER `tax`;"
            )
        );
    }


}

/*
 * Add transaction_charge column in `erp_acct_pay_bill` table
 */
function erp_acct_alter_purchase_details_1_6_4() {
    global $wpdb;

    $table = $wpdb->prefix . 'erp_acct_purchase_details';
    $cols  = $wpdb->get_col( "DESC $table" );

    if ( !in_array( 'tax', $cols ) ) {
        $wpdb->query(
            $wpdb->prepare(
                "ALTER TABLE $table ADD `tax` decimal(20,2)  NULL DEFAULT NULL  AFTER `amount`;"
            )
        );
    }


}


 function crate_erp_acct_purchase_details_tax_table() {

    global $wpdb;
    $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}erp_acct_purchase_details_tax (
              `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT,
              `invoice_details_id` int(20) NOT NULL,
              `agency_id` int(20) DEFAULT NULL,
              `tax_rate` decimal(20,2) NOT NULL,
              `created_at` timestamp DEFAULT NULL,
              `created_by` int(20) DEFAULT NULL,
              `updated_at` timestamp DEFAULT NULL,
              `updated_by` int(20) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) DEFAULT CHARSET=utf8";

    dbDelta($sql);
}


erp_acct_alter_acct_expenses_1_6_4();
erp_acct_alter_pay_purchase_1_6_4();
erp_acct_alter_pay_bill_1_6_4();
