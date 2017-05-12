<?php
    return [
        'DATAXERO' =>[
            'ACCOUNT_URL' => 'https://api.xero.com/api.xro/2.0/',
            'CONSUMER_KEY' => '',
            'CONSUMER_SECRET' => '',
            'REQUEST_TOKEN' => 'https://api.xero.com/oauth/RequestToken',
            'AUTHORISE_URL' => 'https://api.xero.com/oauth/Authorize',
            'ACCESS_TOKEN' => 'https://api.xero.com/oauth/AccessToken',
        ],
        'TASK_STATUS' =>[
            'PENDING' => 'Pending',
            'OVERDUE' => 'Overdue',
            'CALL' => 'Call',
            'COLLECTION' => 'Collection',
            'WRAP' => 'Wrap',
            'COMPLETE' => 'Complete',
            'PROBLEM' => 'Problem',
            'CANCEL' => 'Cancel',
        ],
        'TASK_STATUS_COLOR' =>[
            'PENDING' => '#F9690E',
            'OVERDUE' => '#F7CA18',
            'CALL' => '#21B9BB',
            'COLLECTION' => '#18A689',
            'WRAP' => '#18A689',
            'COMPLETE' => '#F8AC59',
            'PROBLEM' => '#F22613',
            'CANCEL' => '#F22613',
        ],
        'TASK_PRIORITY' =>[
            'HIGH' => 'High',
            'MEDIUM' => 'Medium',
            'LOW' => 'Low',
        ],
        'TASK_PRIORITY_COLOR' =>[
            'HIGH' => '#CF000F',
            'MEDIUM' => '#1AB394',
            'LOW' => '#F8AC59',
        ],
        'PERMISSION_TYPE' =>[
            'GROUP' => 'Manage Group',
            'CATEGORY' => 'Manage Category',
            'BRAND' => 'Manage Brand',
            'CUSTOMER' => 'Manage Customer',
            'PRODUCT' => 'Manage Product',
            'STORE' => 'Manage Store',
        ],
        'LEAD_STATUS' => [
            'open_new' => 'Open - New',
            'open_attempted_contact' => 'Open - Attempted Contact',
            'open_contacted' => 'Open -Contacted',
            'closed_sale' => 'Closed - Sale',
            'closed_not_interested' => 'Closed - Not Interested',
        ],
        'LEAD_STATUS_COLOR' => [
            'open_new' => '#18A689',
            'open_attempted_contact' => '#18A689',
            'open_contacted' => '#21B9BB',
            'closed_sale' => '#F22613',
            'closed_not_interested' => '#F9690E',
        ]
    ];
?>