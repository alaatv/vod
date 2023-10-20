<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 11/9/2016
 * Time: 4:41 PM
 */

use Illuminate\Http\Response;

return [
    // Default Roles
    'ROLE_ADMIN' => 'admin',
    'ROLE_CONSULTANT' => 'consultant',
    'ROLE_TECH' => 'tech',
    'ROLE_FINANCE_EMPLOYEE' => 'financeEmployee',
    'ROLE_STUDIO_MANAGER' => 'studioManager',
    'ROLE_CAMERA_EMPLOYEE' => 'cameraEmployee',
    'ROLE_CAMERA_EMPLOYEE_ID' => 48,
    'ROLE_EMPLOYEE' => 'employee',
    'ROLE_EMPLOYEE_ID' => 6,
    'ROLE_TEACHER' => 'teacher',
    'ROLE_TEACHER_ID' => 10,
    'ROLE_EDUCATION_SUPPORT' => 'educationSupport',
    'ROLE_CONTENT_MANAGER' => 'contentManagement',
    'ROLE_GRAPHIC_EMPLOYEE' => 'graphicEmployee',
    'ROLE_GRAPHIC_MANAGER' => 'graphicManager',
    'ROLE_GRAPHIC_EMPLOYEE_ID' => 50,
    'ROLE_CONTENT_EMPLOYEE' => 'educationEmployee',
    'ROLE_CONTENT_EMPLOYEE_ID' => 15,
    'ROLE_KOMITE_STAFF' => 'komiteStaff',
    'ROLE_KOMITE_STAFF_ID' => 51,
    'ROLE_PUBLIC_RELATION_EMPLOYEE' => 'publicRelationEmployee',
    'ROLE_PUBLIC_RELATION_EMPLOYEE_ID' => 56,
    'ROLE_PUBLIC_RELATION_MANAGER' => 'publicRelationManager',
    'ROLE_3A_MANAGER' => '3aManager',
    '3A_ROLE_TYPIST_MANAGER' => '3aTypistManager',
    '3A_ROLE_TYPIST_EMPLOYEE' => '3aTypistEmployee',
    'ALAA_ROLE_TYPIST_EMPLOYEE' => 'alaaTypistEmployee',
    'ALAA_ROLE_TYPIST_MANAGER' => 'alaaTypistManager',
    'ROLE_TYPIST_EMPLOYEE_ID' => 60,
    'ROLE_SUPPORT_MANAGER' => 'supportManagement',
    'ROLE_CONTENT_TIMEPOINT_INSERTOR' => 'contentTimepointInsertor',
    'ROLE_CONTENT_DESCRIPTION_EDITOR' => 'editContentDescription',
    'ROLE_DEVELOPER' => 'developer',
    'ROLE_TECHNICAL_SUPPORT' => 'technicalSupport',
    'ROLE_3A_EDUCATIONAL_EMPLOYEE' => '3aEducationalEmployee',
    'BOOK_POST_MAN_ROLE' => 'bookPostMan',
    'SHARIF_SCHOOL_REGISTER' => 'sharifSchoolRegister',
    'ROLE_AUDITOR' => 'accountant',
    'ROLE_ACCOUNTANT' => 'accountant',
    'ROLE_3A_REPORT_MANAGER' => '3aReportManager',
    'ROLE_WALLET_GIVER' => 'walletGiver',
    'ROLE_SEO_MANAGER' => 'seoManager',
    'ROLE_MOTION_EMOLOYEE' => 'motionGraphist',
    'ROLE_MOTION_MANAGER' => 'motionManager',
    'ROLE_MOTEVASETE_DOVOM_MANAGER' => 'motevaseteDovomManager',
    'ROLE_EDUCATION' => 'education',
    'ROLE_RAHE_ABRISHAM_MANAGER' => 'raheAbrishamManager',
    'ROLE_RAHE_ABRISHAM_ASSISTANT' => 'raheAbrishamAssitant',
    'ROLE_3A_HEAD_MANAGER' => '3aHeadManager',
    'ROLE_BONYAD_EHSAN_MANAGER' => 'bonyadEhsanManager',
    'ROLE_BONYAD_EHSAN_USER' => 'bonyadEhsanUser',
    'ROLE_BONYAD_EHSAN_MOSHAVER' => 'bonyadEhsanMoshaver',
    'ROLE_BONYAD_EHSAN_NETWORK' => 'bonyadEhsanNetwork',
    'ROLE_BONYAD_EHSAN_SUB_NETWORK' => 'bonyadEhsanSubNetwork',
    'ROLE_PROJECT_CONTROLLER' => 'projectController',
    'ROLE_EDITOR_MANAGER' => 'editorManager',
    'ROLE_AD_MANAGER' => 'adManager',
    'ROLE_PRODUCT_MANAGEMENT' => 'productManagement',
    'ROLE_VOUCHER_MANAGER' => 'voucherManager',
    'STUDY_PLAN_EMPLOYEE' => 'studyPlanEmployee',

    // Permissions :
    'ADMIN_PANEL_ACCESS' => 'adminPanel',
    'GIVE_SYSTEM_ROLE' => 'giveSystemRole',
    'USER_ADMIN_PANEL_ACCESS' => 'userAdminPanel',
    'PRODUCT_ADMIN_PANEL_ACCESS' => 'productAdminPanel',
    'CONTENT_ADMIN_PANEL_ACCESS' => 'contentAdminPanel',
    'SMS_ADMIN_PANEL_ACCESS' => 'smsAdminPanel',     // Changed to database
    'SEND_SMS_ADMIN_PANEL_ACCESS' => 'sendSMSAdminPanel', // No exist in database
    'LOG_SMS_ADMIN_PANEL_ACCESS' => 'logSMSAdminPanel',
    'ORDER_ADMIN_PANEL_ACCESS' => 'orderAdminPanel',
    'SITE_CONFIG_ADMIN_PANEL_ACCESS' => 'siteConfigAdminPanel',
    'PARTICULAR_ADMIN_PANELS' => 'particularAdminPanel',
    'WALLET_ADMIN_PANEL' => 'walletAdminPanel',
    'ALAA_FAMILTY' => 'alaaFamily',
    'LIST_ASSIGNMENT_ACCESS' => 'listAssignment',
    'INSERT_ASSIGNMENT_ACCESS' => 'insertAssignment',
    'EDIT_ASSIGNMENT_ACCESS' => 'editAssignment',
    'REMOVE_ASSIGNMENT_ACCESS' => 'removeAssignment',
    'SHOW_ASSIGNMENT_ACCESS' => 'showAssignment',
    'LIST_CONSULTATION_ACCESS' => 'listConsultation',
    'INSERT_CONSULTATION_ACCESS' => 'insertConsultation',
    'EDIT_CONSULTATION_ACCESS' => 'editConsultation',
    'REMOVE_CONSULTATION_ACCESS' => 'removeConsultation',
    'SHOW_CONSULTATION_ACCESS' => 'showConsultation',
    'LIST_USER_ACCESS' => 'listUser',
    'INSERT_USER_ACCESS' => 'insertUser',
    'EDIT_USER_ACCESS' => 'editUser',
    'REMOVE_USER_ACCESS' => 'removeUser',
    'SHOW_USER_ACCESS' => 'showUser',
    'SEND_SMS_TO_USER_ACCESS' => 'sendSMSUser',
    'INSERT_USER_BON_ACCESS' => 'insertUserBon',
    'LIST_USER_BON_ACCESS' => 'listUserBon',
    'REMOVE_USER_BON_ACCESS' => 'removeUserBon',
    'DOWNLOAD_ASSIGNMENT_ACCESS' => 'downloadAssignment',
    'DOWNLOAD_PRODUCT_FILE' => 'downloadProductFile',
    'LIST_PRODUCT_ACCESS' => 'listProduct',
    'SET_DISCOUNT_FOR_PRODUCT' => 'setProductDiscount',
    'CONFIG_GROUP_OF-PRODUCTS' => 'configGroupOfProducts',
    'INSERT_PRODUCT_ACCESS' => 'insertProduct',
    'EDIT_PRODUCT_ACCESS' => 'editProduct',
    'REMOVE_PRODUCT_ACCESS' => 'removeProduct',
    'COPY_PRODUCT_ACCESS' => 'copyProduct',
    'SHOW_PRODUCT_ACCESS' => 'showProduct',
    'LIST_ORDER_ACCESS' => 'listOrder',
    'INSERT_ORDER_ACCESS' => 'insertOrder',
    'EDIT_ORDER_ACCESS' => 'editOrder',
    'REMOVE_ORDER_ACCESS' => 'removeOrder',
    'SHOW_ORDER_ACCESS' => 'showOrder',
    'SHOW_OPENBYADMIN_ORDER' => 'showOpenByAdminOrders',
    'LIST_PERMISSION_ACCESS' => 'listPermission',
    'INSERT_PERMISSION_ACCESS' => 'insertPermission',
    'EDIT_PERMISSION_ACCESS' => 'editPermission',
    'REMOVE_PERMISSION_ACCESS' => 'removePermission',
    'SHOW_PERMISSION_ACCESS' => 'showPermission',
    'INSET_USER_ROLE' => 'insertUserRole',
    'SHOW_USER_ROLE' => 'showUserRole',
    'LIST_COUPON_ACCESS' => 'listCoupon',
    'INSERT_COUPON_ACCESS' => 'insertCoupon',
    'EDIT_COUPON_ACCESS' => 'editCoupon',
    'REMOVE_COUPON_ACCESS' => 'removeCoupon',
    'SHOW_COUPON_ACCESS' => 'showCoupon',
    'LIST_QUESTION_ACCESS' => 'listStudentQuestion',
    'CONSULTANT_PANEL_ACCESS' => 'consultantPanel',
    'SHOW_QUESTION_ACCESS' => 'showStudentQuestion',
    'LIST_ATTRIBUTE_ACCESS' => 'listAttribute',
    'INSERT_ATTRIBUTE_ACCESS' => 'insertAttribute',
    'EDIT_ATTRIBUTE_ACCESS' => 'editAttribute',
    'REMOVE_ATTRIBUTE_ACCESS' => 'removeAttribute',
    'SHOW_ATTRIBUTE_ACCESS' => 'showAttribute',
    'UPDATE_ATTRIBUTE_ACCESS' => 'updateAttribute',
    'LIST_ATTRIBUTESET_ACCESS' => 'listAttributeset',
    'INSERT_ATTRIBUTESET_ACCESS' => 'insertAttributeset',
    'EDIT_ATTRIBUTESET_ACCESS' => 'editAttributeset',
    'REMOVE_ATTRIBUTESET_ACCESS' => 'removeAttributeset',
    'SHOW_ATTRIBUTESET_ACCESS' => 'showAttributeset',
    'UPDATE_ATTRIBUTESET_ACCESS' => 'updateAttributeset',
    'LIST_ATTRIBUTEVALUE_ACCESS' => 'listAttributevalue',
    'INSERT_ATTRIBUTEVALUE_ACCESS' => 'insertAttributevalue',
    'EDIT_ATTRIBUTEVALUE_ACCESS' => 'editAttributevalue',
    'REMOVE_ATTRIBUTEVALUE_ACCESS' => 'removeAttributevalue',
    'SHOW_ATTRIBUTEVALUE_ACCESS' => 'showAttributevalue',
    'UPDATE_ATTRIBUTEVALUE_ACCESS' => 'updateAttributevalue',
    'LIST_ATTRIBUTEGROUP_ACCESS' => 'listAttributegroup',
    'INSERT_ATTRIBUTEGROUP_ACCESS' => 'insertAttributegroup',
    'EDIT_ATTRIBUTEGROUP_ACCESS' => 'editAttributegroup',
    'REMOVE_ATTRIBUTEGROUP_ACCESS' => 'removeAttributegroup',
    'SHOW_ATTRIBUTEGROUP_ACCESS' => 'showAttributegroup',
    'UPDATE_ATTRIBUTEGROUP_ACCESS' => 'updateAttributegroup',
    'LIST_TRANSACTION_ACCESS' => 'listTransaction',
    'SHOW_TRANSACTION_TOTAL_COST_ACCESS' => 'showTransactionTotalCost',
    'SHOW_TRANSACTION_TOTAL_FILTERED_COST_ACCESS' => 'showTransactionTotalFilteredCost',
    'EDIT_TRANSACTION_ACCESS' => 'editTransaction',
    'INSERT_TRANSACTION_ACCESS' => 'insertTransaction',
    'SHOW_TRANSACTION_ACCESS' => 'showTransaction',
    'REMOVE_TRANSACTION_ACCESS' => 'removeTransaction',
    'EDIT_TRANSACTION_ORDERID_ACCESS' => 'editTransactionOrderID',
    'LIST_MBTIANSWER_ACCESS' => 'listMBTIAnswer',
    'LIST_CONTACT_ACCESS' => 'listContact',
    'INSERT_CONTACT_ACCESS' => 'insertContact',
    'EDIT_CONTACT_ACCESS' => 'editContact',
    'REMOVE_CONTACT_ACCESS' => 'removeContact',
    'SHOW_USER_EMAIL' => 'showUserEmail',
    'SHOW_USER_MOBILE' => 'showUserMobile',
    'SHOW_ARTICLE_ACCESS' => 'showArticle',
    'LIST_ARTICLE_ACCESS' => 'listArticle',
    'INSERT_ARTICLE_ACCESS' => 'insertArticle',
    'EDIT_ARTICLE_ACCESS' => 'editArticle',
    'REMOVE_ARTICLE_ACCESS' => 'removeArticle',
    'SHOW_ARTICLECATEGORY_ACCESS' => 'showArticlecategory',
    'LIST_ARTICLECATEGORY_ACCESS' => 'listArticlecategory',
    'INSERT_ARTICLECATEGORY_ACCESS' => 'insertArticlecategory',
    'EDIT_ARTICLECATEGORY_ACCESS' => 'editArticlecategory',
    'REMOVE_ARTICLECATEGORY_ACCESS' => 'removeArticlecategory',
    'LIST_SLIDESHOW_ACCESS' => 'listSlideShow',
    'INSERT_SLIDESHOW_ACCESS' => 'insertSlideShow',
    'EDIT_SLIDESHOW_ACCESS' => 'editSlideShow',
    'REMOVE_SLIDESHOW_ACCESS' => 'removeSlideShow',
    'SHOW_SLIDESHOW_ACCESS' => 'showSlideShow',
    'LIST_CONFIGURE_PRODUCT_ACCESS' => 'listConfigureProduct',
    'INSERT_CONFIGURE_PRODUCT_ACCESS' => 'insertConfigureProduct',
    'EDIT_CONFIGURE_PRODUCT_ACCESS' => 'editConfigureProduct',
    'REMOVE_CONFIGURE_PRODUCT_ACCESS' => 'removeConfigureProduct',
    'CHANGE_PRODUCT_ATTRIBUTE_ACCESS' => 'changeProductAttributeAccess',
    'SHOW_CONFIGURE_PRODUCT_ACCESS' => 'showConfigureProduct',
    'LIST_PRODUCT_FILE_ACCESS' => 'listProductFile',
    'LIST_PRODUCT_SAMPLE_PHOTO_ACCESS' => 'listProductSamplePhoto',
    'INSERT_PRODUCT_SAMPLE_PHOTO_ACCESS' => 'insertProductSamplePhoto',
    'EDIT_PRODUCT_SAMPLE_PHOTO_ACCESS' => 'editProductSamplePhoto',
    'REMOVE_PRODUCT_SAMPLE_PHOTO_ACCESS' => 'removeProductSamplePhoto',
    'INSERT_PRODUCT_FILE_ACCESS' => 'insertProductFile',
    'EDIT_PRODUCT_FILE_ACCESS' => 'editProductFile',
    'REMOVE_PRODUCT_FILE_ACCESS' => 'removeProductFile',
    'SHOW_PRODUCT_FILE_ACCESS' => 'showProductFile',
    'SEE_PRODUCT_SELECT_FILTERS_IN_SMS_PANEL' => 'seeProductSelectFiltersInSMSPanel',
    'SEE_PRODUCT_SELECT_FILTERS_IN_USER_PANEL' => 'seeProductSelectFiltersInUserPanel',
    'LIST_SITE_CONFIG_ACCESS' => 'listSiteSetting',
    'INSERT_SITE_CONFIG_ACCESS' => 'insertSiteSetting',
    'EDIT_SITE_CONFIG_ACCESS' => 'editSiteSetting',
    'REMOVE_SITE_CONFIG_ACCESS' => 'removeSiteSetting',
    'SHOW_SITE_CONFIG_ACCESS' => 'showSiteSetting',
    'LIST_EVENTRESULT_ACCESS' => 'listEventResult',
    'GET_EVENTRESULT_ACCESS' => 'getEventResult',
    'INSET_EVENTRESULT_ACCESS' => 'insertEventResult',
    'LIST_SHARIF_REGISTER_ACCESS' => 'listSharifRegister',
    'LIST_BELONGING_ACCESS' => 'listBelonging',
    'INSERT_BELONGING_ACCESS' => 'insertBelonging',
    'REMOVE_BELONGING_ACCESS' => 'removeBelonging',
    'LIST_EDUCATIONAL_CONTENT_ACCESS' => 'listEducationalContent',
    'INSERT_EDUCATIONAL_CONTENT_ACCESS' => 'insertEducationalContent',
    'EDIT_EDUCATIONAL_CONTENT' => 'editEducationalContent',
    'COPY_EDUCATIONAL_CONTENT' => 'copyEducationalContent',
    'REMOVE_EDUCATIONAL_CONTENT_ACCESS' => 'removeEducationalContent',
    'SHOW_EDUCATIONAL_CONTENT_ACCESS' => 'showEducationalContent',
    'REDIRECT_EDUCATIONAL_CONTENT_ACCESS' => 'redirectEducationalContent',
    'REMOVE_EDUCATIONAL_CONTENT_FILE_ACCESS' => 'removeEducationalContentFile',
    'ACCEPT_CONTENT_TMP_DESCRIPTION_ACCESS' => 'acceptContentTmpDescription',
    'REPORT_ADMIN_PANEL_ACCESS' => 'reportAdminPanelAccess',
    'FIX_UNKNOWN_CITY_ADMIN_PANEL_ACCESS' => 'fixUnknownCityAdminPanelAccess',
    'INSERT_EMPLOPYEE_WORK_SHEET' => 'insertEmployeeWorkSheet',
    'INSERT_EMPLOPYEE_WORK_SHEET_FOR_SELF' => 'insertEmployeeWorkSheetForSelf',
    'LIST_EMPLOPYEE_WORK_SHEET' => 'listEmployeeWorkSheet',
    'EDIT_EMPLOPYEE_WORK_SHEET' => 'editEmployeeWorkSheet',
    'EDIT_EMPLOPYEE_WORK_SHEET_FOR_SELF' => 'editEmployeeWorkSheetForSelf',
    'REMOVE_EMPLOPYEE_WORK_SHEET' => 'removeEmployeeWorkSheet',
    'ORDER_ANY_THING' => 'orderAnyThing',
    'GET_BOOK_SELL_REPORT' => 'getBookSellReport',
    'SEE_PAID_COST' => 'seePaidCost',
    'INSERT_MAJOR' => 'insertMajor',
    'GET_USER_REPORT' => 'getUserReport',
    'TELEMARKETING_PANEL_ACCESS' => 'telemarketingPanel',
    'LIST_ORDERPRODUCT_ACCESS' => 'listOrderproduct',
    'INSERT_ORDERPRODUCT_ACCESS' => 'insertOrderproduct',
    'REMOVE_ORDERPRODUCT_ACCESS' => 'removeOrderproduct',
    'UPDATE_ORDERPRODUCT_ACCESS' => 'updateOrderproduct',
    'SHOW_ORDERPRODUCT_ACCESS' => 'showOrderproduct',
    'RESTORE_ORDERPRODUCT_ACCESS' => 'restoreOrderproduct',
    'CHECKOUT_ORDERPRODUCT_ACCESS' => 'checkoutOrderproduct',
    'UPDATE_ORDER_SHARE_COST_ACCESS' => 'updateOrderProductShareCost',
    'SHOW_USER_TOTAL_BON_NUMBER' => 'showUserTotalBonNumber',
    'INSERT_ACTIVE_CONTENT' => 'insertActiveContent',
    'CHANGE_TO_PAID_CONTENT' => 'changeToPaidContent',
    'SHOW_CONTENT_SET_ACCESS' => 'showContentset',
    'LIST_CONTENT_SET_ACCESS' => 'listContentset',
    'LIST_CONTENTS_OF_CONTENT_SET_ACCESS' => 'listContentsOfContentset',
    'INSERT_CONTENT_SET_ACCESS' => 'insertContentset',
    'EDIT_CONTENT_SET_ACCESS' => 'editContentset',
    'REMOVE_CONTENT_SET_ACCESS' => 'removeContentset',
    'ADD_PRODUCT_TO_SET_ACCESS' => 'addProductToSet',
    'SHOW_SALES_REPORT' => 'showSalesReport',
    'LIST_BLOCK_ACCESS' => 'listBlock',
    'INSERT_BLOCK_ACCESS' => 'insertBlock',
    'EDIT_BLOCK_ACCESS' => 'editBlock',
    'REMOVE_BLOCK_ACCESS' => 'removeBlock',
    'LIVE_STOP_ACCESS' => 'stopLive',
    'LIVE_PLAY_ACCESS' => 'playLive',
    'GIVE_WALLET_CREDIT' => 'giveWalletCredit',
    'SEE_FILTERS_EXECPT_USERFILTERS_IN_ORDER_ADMIN_' => 'seeFiltersExceptUserFiltersInOrderAdmin',
    'SEE_ORDER_IDENTITY_FILTERS' => 'seeOrderIdentityFilters',
    'SEE_FILTERS_EXECPT_USERFILTERS_IN_USER_ADMIN_' => 'seeFiltersExceptUserFiltersInUserAdmin',
    'SEE_USER_IDENTITY_FILTERS' => 'seeUserIdentityFilters',
    'SEE_ORDER_STATUS_FILTERS_IN_SMS_PANEL' => 'seeOrderStatusFiltersInSMSPanel',
    'SEE_ORDER_STATUS_FILTERS_IN_USER_PANEL' => 'seeOrderStatusFiltersInUserPanel',
    'LIST_LIVE_DESCRIPTION_ACCESS' => 'listLiveDescription',
    'INSERT_LIVE_DESCRIPTION_ACCESS' => 'insertLiveDescription',
    'FAQS' => 'faqs',
    'UPDATE_LIVE_DESCRIPTION_ACCESS' => 'updateLiveDescription',
    'SHOW_LIVE_DESCRIPTION_ACCESS' => 'showLiveDescription',
    'DELETE_LIVE_DESCRIPTION_ACCESS' => 'removeLiveDescription',
    'PIN_LIVE_DESCRIPTION_ACCESS' => 'pinLiveDescription',
    'UNPIN_LIVE_DESCRIPTION_ACCESS' => 'unpinLiveDescription',
    'LIST_PERIOD_DESCRIPTION_ACCESS' => 'listPeriodDescription',
    'INSERT_PERIOD_DESCRIPTION_ACCESS' => 'insertPeriodDescription',
    'UPDATE_PERIOD_DESCRIPTION_ACCESS' => 'updatePeriodDescription',
    'SHOW_PERIOD_DESCRIPTION_ACCESS' => 'showPeriodDescription',
    'DELETE_PERIOD_DESCRIPTION_ACCESS' => 'removePeriodDescription',
    'SHOW_SITE_FAQ_ACCESS' => 'showSiteFaq',
    'EDIT_SITE_FAQ_ACCESS' => 'editSiteFaq',
    'SHOW_USER_CITY' => 'showUserCity',
    'SHOW_KONKOOT_RESULT_INFO' => 'showKonkoorResultInfo',
    'SHOW_KONKOOT_RESULT_FILE' => 'showKonkoorResultFile',
    'VERIFY_HEKMAT_VOUCHER' => 'verifyVoucher',
    'DISABLE_HEKMAT_VOUCHER' => 'disableVoucher',
    'INSERT_PLAN' => 'insertPlan',
    'UPDATE_PLAN' => 'updatePlan',
    'DELETE_PLAN' => 'deletePlan',
    'INSERT_STUDY_PLAN' => 'insertStudyPlan',
    'UPDATE_STUDY_PLAN' => 'updateStudyPlan',
    'DELETE_STUDY_PLAN' => 'deleteStudyPlan',
    'INSERT_TICKET_ACCESS' => 'insertTicket',
    'INDEX_TICKET_ACCESS' => 'indexTicket',
    'SHOW_TICKET_ACCESS' => 'showTicket',
    'EDIT_TICKET_ACCESS' => 'editTicket',
    'REMOVE_TICKET_ACCESS' => 'removeTicket',
    'SHOW_TICKET_LOGS_ACCESS' => 'showTicketlogs',
    'SHOW_TICKET_RATE_ACCESS' => 'showTicketRate',
    'SHOW_TICKET_MESSAGE_REPORT_ACCESS' => 'showTicketMessageReport',
    'INSERT_BATCH_ORDERS' => 'insertBatchOrders',
    'EMPLOYEE_TIME_MANAGER' => 'employeeTimeManagement',
    'ANSWER_TICKET' => 'answerTicket',
    'FILTER_TICKET_RESPONDER' => 'filterTicketResponder',
    'SEND_TICKET_STATUS_NOTICE' => 'sendTicketStatusNotice',
    'ASSIGN_TICKET' => 'assignTicket',
    'CREATE_TICKET' => 'createTicket',
    'FILTER_TICKET_ASSIGNEE' => 'filterTicketAssignee',
    'LIST_USER_ORDERS' => 'listUserOrders',
    'INSERT_CONENT_TIMEPOINT' => 'insertContentTimepoint',
    'INSERT_MAP_DETAIL' => 'insertMapDetail',
    'EXCEL_PANEL_ACCESS' => 'excelPanelAccess',
    'SHOW_USER_BY_CREDENTIALS' => 'showUserByCredentials',
    'CHANGE_USER_OF_ORDER' => 'changeUserOfOrder',
    'TRANSFER_ORDERS_OF_USER' => 'transferOrdersOfUser',
    'LIST_SMSPANEL_USER_ACCESS' => 'listSMSPanelUser',
    'ENTITY_CACHE_CLEAR_ACCESS' => 'entityCacheClear',
    'UPDATE_EMPLOYEE_SCHEDULE' => 'updateEmployeeSchedule',
    'LIST_PHONE_BOOK_ACCESS' => 'listPhoneBook',
    'LIST_PHONE_NUMBER_ACCESS' => 'listPhoneNumber',
    'LIST_CHANNEL_ACCESS' => 'listChannel',
    'INSERT_CHANNEL_ACCESS' => 'insertChannel',
    'UPDATE_CHANNEL_ACCESS' => 'updateChannel',
    'DELETE_CHANNEL_ACCESS' => 'deleteChannel',
    // TODO: Add following permission to db
    'STORE_EMPLOYEE_SCHEDULE' => 'storeEmployeeSchedule',
    'INDEX_TICKET_DEPARTMENT_ACCESS' => 'indexTicketDepartment',
    'INSERT_TICKET_DEPARTMENT_ACCESS' => 'insertTicketDepartment',
    'UPDATE_TICKET_DEPARTMENT_ACCESS' => 'updateTicketDepartment',
    'REMOVE_TICKET_DEPARTMENT_ACCESS' => 'removeTicketDepartment',
    'CHANGE_ADVERTISEMENTS_BANNERS_ACCESS' => 'changeAdvertisementBanners',
    'VAST_PANEL_ACCESS' => 'vastPanel',
    'INSERT_VAST_ACCESS' => 'insertVast',
    'UPDATE_VAST_ACCESS' => 'updateVast',
    'DELETE_VAST_ACCESS' => 'deleteVast',
    'CONFIRM_EMPLOYEE_OVER_TIME' => 'confirmEmployeeOverTime',
    'INSERT_CONTENT_SECTION' => 'insertContentSection',
    'UPDATE_CONTENT_SECTION' => 'updateContentSection',
    'REMOVE_CONTENT_SECTION' => 'removeContentSection',
    'LIST_CONTENT_SECTION' => 'indexContentSection',
    'WATCH_ALAA_CONTENTS' => 'watchAlaaContents',
    'COPY_TIME_POINT_OF_CONTENT' => 'copyTimePointOfContent',
    'SHOW_ABRISHAM_ANALYTICS' => 'showAbrishamAnalytics',
    'GENERATE_GIFT_CARD_PANEL' => 'generateGiftCardPanel',
    'EARNING_DASHBOARD' => 'earningDashboard',
    'LIST_WITHDRAW_TRANSACTIONS' => 'listWithdrawTransactions',
    'LIST_BILLING_ACCESS' => 'listBilling',
    'VOUCHER_ADMIN_PANEL' => 'voucherAdminPanel',
    'INSERT_BATCH_CONTENT' => 'insertBatchContent',
    'LIST_NEWSLETTER' => 'listNewsLetter',
    'TRANSFER_PRODUCT_TO_DANA' => 'transferProductToDana',
    'TRANSFER_SET_TO_DANA' => 'transferSetToDana',
    'TRANSFER_CONTENT_TO_DANA' => 'transferContentToDana',
    'PUT_MINIO_UPLOAD' => 'putMinioUpload',
    'STORE_SETTING' => 'insertSiteSetting',
    'UPDATE_SETTING' => 'editSiteSetting',
    'DESTROY_SETTING' => 'removeSiteSetting',
    'INDEX_SETTING' => 'showSiteSetting',
    'MARKETING_REPORT' => 'createMarketingReport',
    'LIVE_CONDUCTOR_REPORT' => 'getLiveConductorReport',
    'GET_USER_ENTEKHAB_RESHTE' => 'getUserEntekhabReshte',

    'INDEX_REPORT' => 'indexReport',
    'CREATE_REPORT' => 'createReport',
    'SHOW_REPORT' => 'showReport',
    'DELETE_REPORT' => 'deleteReport',

    'BONYAD_EHSAN_LIST_ORDER' => 'bonyadEhsanListOrder',
    'BONYAD_EHSAN_REMOVE_ORDER' => 'bonyadEhsanRemoveOrder',
    'BONYAD_EHSAN_FILTER_ORDER_CREATOR' => 'bonyadEhsanFilterOrderCreator',
    'BONYAD_EHSAN_INSERT_USER' => 'bonyadEhsanInsertUser',
    'BONYAD_EHSAN_UPDATE_USER' => 'bonyadEhsanUpdateUser',
    'BONYAD_EHSAN_INSERT_MOSHAVER' => 'bonyadEhsanInsertMoshaver',
    'BONYAD_EHSAN_INSERT_NETWORK' => 'bonyadEhsanInsertNetwork',
    'BONYAD_EHSAN_INSERT_SUB_NETWORK' => 'bonyadEhsanInsertSubNetwork',
    'BONYAD_EHSAN_PANEL_ACCESS' => 'accessBonyadEhsanPanel',
    'BONYAD_EHSAN_ADMIN_PANEL_ACCESS' => 'accessBonyadEhsanAdminPanel',
    'BONYAD_EHSAN_CONSULTANT_SHOW' => 'bonyadEhsanConsultantShow',
    'BONYAD_EHSAN_SHOW_NETWORKS' => 'bonyadShowNetworks',
    'BONYAD_EHSAN_SHOW_SUBNETWORKS' => 'bonyadShowSubnetworks',
    'BONYAD_EHSAN_SHOW_MOSHAVERS' => 'bonyadShowMoshavers',
    'BONYAD_EHSAN_SHOW_STUDENTS' => 'bonyadShowStudents',
    'BONYAD_EHSAN_UPDATE_STUDENT_LIMIT' => 'bonyadUpdateRegisterLimit',
    'BONYAD_EHSAN_DELETE_USERS' => 'bonyadDeleteUsers',
    'BONYAD_EHSAN_NOTIFICATION_READ' => 'bonyadReadNotification',
    'BONYAD_PRODUCT_ACCESS_SELECT_OPTION' => 'bonyadProductAccessSelectOption',


    //report
    'REPORT_STATUS_CREATING' => 1,
    'REPORT_STATUS_CREATED' => 2,
    'REPORT_STATUS_FAILED' => 3,

    'REPORT_TYPE_HESBRESI' => 1,

    //Technician
    'SET_TECH_CODE' => 'insertTechCode',
    'UPDATE_TECH_CODE' => 'updateTechCode',

    //bons
    'BON1' => 'alaa',
    'BON2' => 'alaaPoint',

    //Profile default image
    'PROFILE_DEFAULT_IMAGE' => 'default_avatar.jpg',
    'CONSULTATION_DEFAULT_IMAGE' => 'default_consultant_thumbnail.jpg',
    'ARTICLE_DEFAULT_IMAGE' => 'default_article_image.png',

    //Mobile verification code wait expiration (in minutes)
    'MOBILE_VERIFICATION_TIME_LIMIT' => '30',
    'MOBILE_VERIFICATION_WAIT_TIME' => '4',
    //waiting time between sending two mobile verification code sms
    'GENERATE_PASSWORD_WAIT_TIME' => '14',
    //waiting time between sending two password sms

    //Number of mbti questions (it is temporary)
    'MBTI_NUMBER_OF_QUESTIONS' => '80',

    //loading gif
    'FILTER_LOADING_GIF' => '/acm/extra/loading-cogs.gif',
    'ADMIN_LOADING_BAR_GIF' => '/acm/extra/filter-loading-bar.gif',

    //sms payment
    'COST_PER_SMS_1' => 100,
    'COST_PER_SMS_2' => 110,
    'COST_PER_SMS_3' => 130,

    'google' => [
        'analytics' => env('GOOGLE_ANALYTICS', 'UA-43695756-1'),
    ],


    'UI_META_TITLE_LIMIT' => 70,
    'UI_META_KEYWORD_LIMIT' => 155,
    'UI_META_DESCRIPTION_LIMIT' => 155,
    'META_TITLE_LIMIT' => 129,
    'META_KEYWORDS_LIMIT' => 286,
    'META_DESCRIPTION_LIMIT' => 286,

    'WORKDAY_ID_USUAL' => 1,
    'WORKDAY_ID_EXTRA' => 2,
    'ORDOO_GHEIRE_HOZOORI_NOROOZ_97_PRODUCT' => [
        196,
        200,
        201,
        203,
        204,
        205,
        206,
    ],
    'ORDOO_GHEIRE_HOZOORI_NOROOZ_97_PRODUCT_ROOT' => 196,
    'ORDOO_GHEIRE_HOZOORI_NOROOZ_97_PRODUCT_ALLTOGHETHER' => 206,
    'ORDOO_GHEIRE_HOZOORI_NOROOZ_97_PRODUCT_DEFAULT' => 204,
    'ORDOO_GHEIRE_HOZOORI_NOROOZ_97_PRODUCT_NOT_DEFAULT' => [
        200,
        201,
        203,
        205,
    ],
    'ORDOO_HOZOORI_NOROOZ_97_PRODUCT' => [
        184,
        185,
        186,
    ],
    'EDUCATIONAL_CONTENT_EXCLUDED_PRODUCTS' => [
        110,
        112,
    ],
    'PRODUCT_SEARCH_EXCLUDED_PRODUCTS' => [
        119,
        123,
        127,
        131,
        135,
        139,
        143,
        147,
        151,
        155,
        159,
        163,
    ],

    'ASIATECH_FREE_ADSL' => 224,
    'LOTTERY_NAME' => '10tir',
    'HAMAYESH_LOTTERY_EXCHANGE_AMOUNT' => 30000,

    //Cache
    'CACHE_600' => env('CACHE_600', 0),
    'CACHE_300' => env('CACHE_300', 0),
    'CACHE_60' => env('CACHE_60', 0),
    'CACHE_10' => env('CACHE_10', 0),
    'CACHE_5' => env('CACHE_5', 0),
    'CACHE_3' => env('CACHE_3', 0),
    'CACHE_1' => env('CACHE_1', 0),
    'CACHE_0' => env('CACHE_0', 0),
    'OCTANE_CONCURRENTLY_TIME_OUT' => env('OCTANE_CONCURRENTLY_TIME_OUT', 60000),
    //
    'TAG_API_URL' => env('TAG_API_URL'),
    'SERVER' => env('SERVER'),
    'CDN_SERVER_NAME' => env('CDN_SERVER_NAME'),
    'PAID_SERVER_NAME' => env('PAID_SERVER_NAME'),
    'DOWNLOAD_SERVER_ROOT' => env('DOWNLOAD_SERVER_ROOT'),
    'DOWNLOAD_SERVER_PROTOCOL' => env('DOWNLOAD_SERVER_PROTOCOL'),
    'DOWNLOAD_SERVER_MEDIA_PARTIAL_PATH' => env('DOWNLOAD_SERVER_MEDIA_PARTIAL_PATH'),
    'PROFILE_IMAGE_PATH' => env('PROFILE_IMAGE_PATH'),

    'MINIO_UPLOAD_DEFAULT_BUCKET' => env('AWS_PUBLIC_BUCKET'),
    'DOWNLOAD_MINIO_ENDPOINT' => env('DOWNLOAD_MINIO_ENDPOINT'),
    'MINIO_UPLOAD_PATH_CONTENTSET' => '/upload/contentset/departmentlesson/',
    'MINIO_UPLOAD_PATH_PRODUCT' => '/upload/images/product/',
    'MINIO_UPLOAD_PATH_SOURCE' => '/upload/images/source/',
    'MINIO_UPLOAD_PATH_SLIDESHOW' => '/upload/images/slideShow/',
    'MINIO_UPLOAD_PATH_ORDER' => '/upload/images/order/',
    'MINIO_UPLOAD_PATH_VAST' => [
        'vastVideoHqSFTP' => '/upload/vastVideos/hq/',
        'vastVideoHd720pSFTP' => '/upload/vastVideos/HD_720p/',
        'vastVideo240pSFTP' => '/upload/vastVideos/240p/',
    ],

    //OrderStatus
    'ORDER_STATUS_OPEN' => 1,
    'ORDER_STATUS_CLOSED' => 2,
    'ORDER_STATUS_CANCELED' => 3,
    'ORDER_STATUS_OPEN_BY_ADMIN' => 4,
    'ORDER_STATUS_POSTED' => 5,
    'ORDER_STATUS_REFUNDED' => 6,
    'ORDER_STATUS_READY_TO_POST' => 7,
    'ORDER_STATUS_OPEN_DONATE' => 8,
    'ORDER_STATUS_PENDING' => 9,
    'ORDER_STATUS_BLOCKED' => 10,
    'ORDER_STATUS_OPEN_3A' => 11,
    'ORDER_STATUS_OPEN_IRANCELL' => 12,

    'OPEN_ORDER_STATUSES' => [1, 4, 8],

    //TRANSACTION STATUSES CONSTANTS
    'TRANSACTION_STATUS_TRANSFERRED_TO_PAY' => 1,
    'TRANSACTION_STATUS_UNSUCCESSFUL' => 2,
    'TRANSACTION_STATUS_SUCCESSFUL' => 3,
    'TRANSACTION_STATUS_PENDING' => 4,
    'TRANSACTION_STATUS_ARCHIVED_SUCCESSFUL' => 5,
    'TRANSACTION_STATUS_UNPAID' => 6,
    'TRANSACTION_STATUS_SUSPENDED' => 7,
    'TRANSACTION_STATUS_ORGANIZATIONAL_UNPAID' => 8,

    //PAYMENT METHODS CONSTANTS
    'PAYMENT_METHOD_ONLINE' => 1,
    'PAYMENT_METHOD_ATM' => 2,
    'PAYMENT_METHOD_POS' => 3,
    'PAYMENT_METHOD_PAYCHECK' => 4,
    'PAYMENT_METHOD_WALLET' => 5,

    //DEVICES CONSTANTS
    'DEVICE_TYPE_DESKTOP' => 1,
    'DEVICE_TYPE_ANDROID' => 2,
    'DEVICE_TYPE_IOS' => 3,
    'DEVICE_TYPE_SMS' => 4,

    //PAYMENT STATUSES CONSTANTS
    'PAYMENT_STATUS_UNPAID' => 1,
    'PAYMENT_STATUS_INDEBTED' => 2,
    'PAYMENT_STATUS_PAID' => 3,
    'PAYMENT_STATUS_VERIFIED_INDEBTED' => 4,
    'PAYMENT_STATUS_ORGANIZATIONAL_PAID' => 5,

    'ORDERPRODUCT_CHECKOUT_STATUS_UNPAID' => 1,
    'ORDERPRODUCT_CHECKOUT_STATUS_PAID' => 2,

    //USER BON STATUSES CONSTANTS
    'USERBON_STATUS_ACTIVE' => 1,
    'USERBON_STATUS_EXPIRED' => 2,
    'USERBON_STATUS_USED' => 3,

    //PRODUCT TYPES CONSTANTS
    'PRODUCT_TYPE_SIMPLE' => 1,
    'PRODUCT_TYPE_CONFIGURABLE' => 2,
    'PRODUCT_TYPE_SELECTABLE' => 3,
    'PRODUCT_TYPE_SUBSCRIPTION' => 4,

    //CONTROLS CONSTANTS
    'CONTROL_SELECT' => 1,
    'CONTROL_GROUPED_CHECKBOX' => 2,
    'CONTROL_SWITCH' => 3,

    //ORDERPRODUCT TYPES CONSTANTS
    'ORDER_PRODUCT_TYPE_DEFAULT' => 1,
    'ORDER_PRODUCT_GIFT' => 2,
    'ORDER_PRODUCT_HIDDEN' => 3,
    'ORDER_PRODUCT_LOCKED' => 4,
    'ORDER_PRODUCT_EXCHANGE' => 5,

    //ORDERPRODUCT INTERRELATIONS
    'ORDER_PRODUCT_INTERRELATION_PARENT_CHILD' => 1,

    //TRANSACTION INTERRELATIONS
    'TRANSACTION_INTERRELATION_PARENT_CHILD' => 1,

    //PRODUCT INTERRELATIONS
    'PRODUCT_INTERRELATION_GIFT' => 1,
    'PRODUCT_INTERRELATION_UPGRADE' => 2,
    'PRODUCT_INTERRELATION_ITEM' => 3,

    //DISCOUNT TYPES
    'DISCOUNT_TYPE_PERCENTAGE' => 1,
    'DISCOUNT_TYPE_COST' => 2,

    //PRODUCT FILE TYPES
    'PRODUCT_FILE_TYPE_PAMPHLET' => 1,
    'PRODUCT_FILE_TYPE_VIDEO' => 2,

    //CONTENT TYPES
    'CONTENT_TYPE_PAMPHLET' => 1,
    'CONTENT_TYPE_VIDEO' => 8,
    'CONTENT_TYPE_ARTICLE' => 9,
    'CONTENT_TYPE_VOICE' => 10,

    //WALLET TYPES
    'WALLET_TYPE_MAIN' => 1,
    'WALLET_TYPE_GIFT' => 2,

    //USER STATUSES
    'USER_STATUS_ACTIVE' => 1,
    'USER_STATUS_INACTIVE' => 2,

    //COUPON TYPES
    'COUPON_TYPE_OVERALL' => 1,
    'COUPON_TYPE_PARTIAL' => 2,


    'ATTRIBUTE_TYPE_MAIN' => 1,
    'ATTRIBUTE_TYPE_EXTRA' => 2,
    'ATTRIBUTE_TYPE_INFORMATION' => 3,
    'ATTRIBUTE_TYPE_SUBSCRIPTION' => 4,

    'EMPLOYEE_OVERTIME_STATUS_UNCONFIRMED' => 1,
    'EMPLOYEE_OVERTIME_STATUS_CONFIRMED' => 2,
    'EMPLOYEE_OVERTIME_STATUS_REJECTED' => 3,

    'JALALI_CALENDER' => [
        [
            'month' => 'مهر',
            'periodBegin' => '2021-09-23',
            'periodEnd' => '2021-10-23',
            'periodMid' => '2021-10-07',
        ],
        [
            'month' => 'آبان',
            'periodBegin' => '2021-10-23',
            'periodEnd' => '2021-11-22',
            'periodMid' => '2021-11-06',
        ],
        [
            'month' => 'آذر',
            'periodBegin' => '2021-11-22',
            'periodEnd' => '2021-12-22',
            'periodMid' => '2021-12-06',
        ],
        [
            'month' => 'دی',
            'periodBegin' => '2021-12-22',
            'periodEnd' => '2021-01-21',
            'periodMid' => '2021-01-05',
        ],
        [
            'month' => 'بهمن',
            'periodBegin' => '2022-01-21',
            'periodEnd' => '2021-02-20',
            'periodMid' => '2021-02-04',
        ],
        [
            'month' => 'اسفند',
            'periodBegin' => '2022-02-20',
            'periodEnd' => '2021-03-21',
            'periodMid' => '2021-03-05',
        ],
        [
            'month' => 'فروردین',
            'periodBegin' => '2022-03-21',
            'periodEnd' => '2021-04-21',
            'periodMid' => '2021-04-04',
        ],
        [
            'month' => 'اردیبهشت',
            'periodBegin' => '2022-04-21',
            'periodEnd' => '2021-05-22',
            'periodMid' => '2021-05-05',
        ],
        [
            'month' => 'خرداد',
            'periodBegin' => '2022-05-22',
            'periodEnd' => '2021-06-22',
            'periodMid' => '2021-06-05',
        ],
        [
            'month' => 'تیر',
            'periodBegin' => '2022-06-22',
            'periodEnd' => '2021-07-23',
            'periodMid' => '2021-07-06',
        ],
        [
            'month' => 'مرداد',
            'periodBegin' => '2022-07-23',
            'periodEnd' => '2021-08-23',
            'periodMid' => '2021-08-06',
        ],
        [
            'month' => 'شهریور',
            'periodBegin' => '2022-08-23',
            'periodEnd' => '2021-09-23',
            'periodMid' => '2021-09-06',
        ],
    ],
    'JALALI_ALL_MONTHS' => [
        'مهر',
        'آبان',
        'آذر',
        'دی',
        'بهمن',
        'اسفند',
        'فروردین',
        'اردیبهشت',
        'خرداد',
        'تیر',
        'مرداد',
        'شهریور',
    ],
    'ALL_DAYS_OF_MONTH' => [
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9',
        '10',
        '11',
        '12',
        '13',
        '14',
        '15',
        '16',
        '17',
        '18',
        '19',
        '20',
        '21',
        '22',
        '23',
        '24',
        '25',
        '26',
        '27',
        '28',
        '29',
        '30',
    ],
    'ALAA_IP' => ['80.210.26.206', '79.127.123.246'],
    'ALAA_IP_URL' => env('APP_IP'),
    'SOALAA_APP_IP' => env('SOALAA_APP_IP'),
    'GAMING_SERVICE_IP' => env('GAMING_SERVICE_IP'),
    'APP_URL' => env('APP_URL'),
    'SOALAA_API_BASE_URL' => env('SOALAA_APP_URL').'/api/v1',
    'DOWNLOAD_HASHING_SECRET' => env('DOWNLOAD_HASHING_SECRET'),
    'RAHE_ABRISHAM_MAP_VERSION' => env('RAHE_ABRISHAM_MAP_VERSION'),


    'NUMBER_OF_SMS_RECIPIENTS_FOR_SEND_SMS_WITHOUT_JOB' => 10,
    'REDIRECT_HTTP_RESPONSE_TYPES' => [
        Response::HTTP_MOVED_PERMANENTLY => ['desc' => 'دائمی'],  // 301
        Response::HTTP_FOUND => ['desc' => 'موقتی'],              // 302
    ],

    'AlAA_MONTHLY_SPEND' => 208333333,
    'ALAA_YEARLY_SPEND' => 2500000000,
    'DONATE_PAGE_LATEST_WEEK_NUMBER' => 3,
    'DONATE_PAGE_LATEST_MAX_NUMBER' => 3,

    'MORPH_MAP_MODELS' => [
        'content' => [
            'model' => App\Content::class,
            'default_resource' => App\Http\Resources\AbrishamContentResource::class,
            'resource' => [
//                'blockable' => App\Http\Resources\Content::class,
//                'commentable' => App\Http\Resources\Content::class,
//                'watchable' => App\Http\Resources\Content::class,
            ],
        ],
        'set' => [
            'model' => App\Contentset::class,
            'default_resource' => App\Http\Resources\SetWithoutPaginationV2::class,
            'resource' => [
//                'blockable' => App\Http\Resources\TestResource::class,
//                'commentable' => App\Http\Resources\TestResource::class,
//                'watchable' => App\Http\Resources\TestResource::class,
            ],
        ],
        'product' => [
            'model' => App\Product::class,
            'default_resource' => App\Http\Resources\Product::class,
            'resource' => [
//                'blockable' => App\Http\Resources\Product::class,
//                'commentable' => App\Http\Resources\Product::class,
//                'watchable' => App\Http\Resources\Product::class,
            ],
        ],
    ],

    'EVENTS' => [
        'YALDA_1400' => 1,
        'BEGIN' => '2021-12-21 23:30:00',
        'END' => '2022-01-01 00:00:00',
        'COUPON' => 'Yalda1400',
    ],
    'REFERRAL_CODE_DISCOUNT' => 100000,

    'MIN_AMOUNT_UNTIL_SETTLEMENT' => 10000,

    'AZMOUN_ATTRIBUTE_SET' => 8,
    'JOZVE_ATTRIBUTE_SET' => 4,
    'HAMAYESH_ATTRIBUTE_SET' => 2,

    'ALAA_SELLER' => 1,
    'SOALAA_SELLER' => 2,

    'ALAA_OWNER' => 1,
    'BONYAD_OWNER' => 2,
    'ACCEPT_OWNER_FOR_VALIDATION' => [1, 2],
    'ATTRIBUTE_VALUE_INFINITE' => -1,

    'FATHER_RELATIVE_ID' => 1,
    'MOTHER_RELATIVE_ID' => 2,

    'BONYAD_USER_TYPES' => ['network', 'subnetwork', 'moshaver', 'student'],

    'PENALTY_TOKEN' => env('PENALTY_TOKEN'),

    'REFERRAL_CODE_USING_MIN_PRICE' => 100000,

    'BONYAD_EXCEL_EXPORT_PER_USER_TIME' => 0.04,

    'IRNACELL_REFERRAL_REQUEST_ID' => env('IRNACELL_REFERRAL_REQUEST_ID'),
    'BONYAD_COUPON_ID' => 174452,

    'USER_COMMISSION_REASON' => 'سود کارت هدیه',
    'USER_COMMISSION_REASON_TYPE' => 'referral',
    'USER_COMMISSION_DESCRIPTION' => 'واریز سود استفاده از کد کارت هدیه توسط کاربران دیگر.',

    'APP_IDS' => [
        'ALAA' => ['KEY' => 1, 'DISPLAY_NAME' => 'آلا'],
    ],

    'SERVICE_IDS' => [
        'AUTH' => ['KEY' => 1, 'DISPLAY_NAME' => 'احراز هویت'],
    ],

    'PRESIGNED_REQUEST_ATTEMPTS_MINIO' => env('PRESIGNED_REQUEST_ATTEMPTS_MINIO'),
    'PRESIGNED_REQUEST_EXPIRY_MINIO' => 60,

    'CONTENT_STATUS_PENDING' => 1,
    'CONTENT_STATUS_DRAFT' => 2,
    'CONTENT_STATUS_COMPLETED' => 2,

    'ALAA_DISK_NAME' => 'alaa',

    'MAXIMUM_BULK_PRODUCTS_GET_BY_IDS' => 100,

    'EWANO_WEBSERVICE' => env('EWANO_WEBSERVICE'),
    'EWANO_CLIENTID' => env('EWANO_CLIENTID'),
    'EWANO_CLIENT_SECRET' => env('EWANO_CLIENT_SECRET'),

    'EWANO_REDIRECT_URL' => env('EWANO_REDIRECT_URL'),

    'MINIMUM_SMS_SENDING_INTERVAL' => 7,
];
