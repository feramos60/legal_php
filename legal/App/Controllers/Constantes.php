<?php

define('WPST_HY3_VERSION', '3.0') ;
define('WPST_HY3_UNUSED', '') ;
define('WPST_HY3_NO_VALUE', '') ;
define('WPST_HY3_SOFTWARE_NAME', 'Hy-Tek, Ltd') ;
//define('WPST_HY3_SOFTWARE_VERSION', WPST_VERSION) ;
define('WPST_HY3_SOFTWARE_VERSION', 'Win-TM 8.0Fc') ;

//  HY3 record terminator
define('WPST_HY3_RECORD_TERMINATOR', chr(13) . chr(10)) ;

//  HY3 checksum record
define('WPST_HY3_CHECKSUM_RECORD', '%-128.128s%01.1d%01.1d') ;

//  Define A1 record
define('WPST_HY3_A1_RECORD', 'A1%2.2s%-25.25s%-15.15s%-14.14s%-8.8s%-1.1s%8.8s%-53.53s%-2.2s') ;

//  Define B1 record
define('WPST_HY3_B1_RECORD', 'B1%-45.45s%-45.45s%8.8s%8.8s%8.8s%-12.12s%-2.2s') ;

//  Define B2 record
define('WPST_HY3_B2_RECORD', 'B2%92.92s%2.2s%2.2s%1.1s%7.7s%2.2s%20.20s%-2.2s') ;

//  Define C1 record
define('WPST_HY3_C1_RECORD', 'C1%-5.5s%-30.30s%-16.16s%-66.66s%-3.3s%-6.6s%-2.2s') ;

//  Define C2 record
define('WPST_HY3_C2_RECORD', 'C2%-30.30s%-30.30s%-30.30s%-2.2s%-10.10s%-3.3s%-1.1s%-4.4s%-16.16s%-2.2s') ;

//  Define C3 record
define('WPST_HY3_C3_RECORD', 'C3%-30.30s%-20.20s%-20.20s%-20.20s%-36.36s%-2.2s') ;

//  Define D1 record
define('WPST_HY3_D1_RECORD', 'D1%1.1s%5.5s%-20.20s%-20.20s%-20.20s%1.1s%-14.14s%-5.5s%8.8s%1.1s%2.2s%29.29s%-2.2s') ;

//  Define HY3 E1 record
define('WPST_HY3_E1_RECORD', 'E1%1.1s%5.5s%-5.5s%1.1s%1.1s%6.6s%1.1s%3.3s%3.3s%4.4s%6.2f%4.4s%8.8s%1.1s%8.8s%1.1s%68.68s%-2.2s') ;


/**
 *  File Type Code
 *
 *       02   Meet Entries
 *       03   Team Roster
 *       07   Meet Results (MM to TM)
 */

//  Define the labels used in the GUI
define('WPST_HY3_TEAM_LIGA', 'Liga de Natacion de Bogota ') ;
define('WPST_HY3_FTC_MEET_TEAM_ROSTER_LABEL', 'Team Roster') ;
define('WPST_HY3_FTC_MEET_ENTRIES_LABEL', 'Meet Entries') ;
define('WPST_HY3_FTC_MEET_RESULTS_MM_TO_TM_LABEL', 'Meet Results (MM to TM)') ;

//  Define the values used in the records
define('WPST_HY3_FTC_MERGE_MEET_ENTRIES_VALUE', '01') ;
define('WPST_HY3_FTC_MEET_ENTRIES_VALUE', '02') ;
define('WPST_HY3_FTC_MEET_TEAM_ROSTER_VALUE', '03') ;
define('WPST_HY3_FTC_MEET_RESULTS_MM_TO_TM_VALUE', '07') ;
define('WPST_HY3_CEROS', '0,00') ;
define('WPST_HY3_CEROS_P', '0.00') ;

//  Define the values used in the records
define('WPST_HY3_TTC_AGE_GROUP_VALUE', 'AGE') ;
define('WPST_HY3_TTC_HIGH_SCHOOL_VALUE', 'HS') ;
define('WPST_HY3_TTC_COLLEGE_VALUE', 'COL') ;
define('WPST_HY3_TTC_MASTERS_VALUE', 'MAS') ;
define('WPST_HY3_TTC_OTHERS_VALUE', 'OTH') ;
define('WPST_HY3_TTC_RECREATION_VALUE', 'REC') ;

?>