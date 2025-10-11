<?php

return [
    'direction' => 'rtl',
    
    'actions' => [
        'attach' => 'إرفاق',
        'attach_and_attach_another' => 'إرفاق وإرفاق آخر',
        'attach_another' => 'إرفاق آخر',
        'cancel' => 'إلغاء',
        'create' => 'إنشاء',
        'create_and_create_another' => 'إنشاء وإنشاء آخر',
        'create_another' => 'إنشاء آخر',
        'delete' => 'حذف',
        'detach' => 'فصل',
        'edit' => 'تعديل',
        'filter' => 'تصفية',
        'force_delete' => 'حذف نهائي',
        'open' => 'فتح',
        'replicate' => 'نسخ',
        'restore' => 'استعادة',
        'save' => 'حفظ',
        'save_changes' => 'حفظ التغييرات',
        'search' => 'بحث',
        'view' => 'عرض',
    ],

    'components' => [
        'pagination' => [
            'label' => 'التنقل بين الصفحات',
            'overview' => 'عرض :first إلى :last من أصل :total نتيجة|عرض :first إلى :last من أصل :total نتائج',
            'fields' => [
                'records_per_page' => [
                    'label' => 'لكل صفحة',
                ],
            ],
            'actions' => [
                'go_to_page' => [
                    'label' => 'الذهاب إلى الصفحة :page',
                ],
                'next' => [
                    'label' => 'التالي',
                ],
                'previous' => [
                    'label' => 'السابق',
                ],
            ],
        ],
    ],

    'fields' => [
        'search_query' => [
            'label' => 'بحث',
            'placeholder' => 'بحث',
        ],
    ],

    'notifications' => [
        'saved' => [
            'title' => 'تم الحفظ',
        ],
        'created' => [
            'title' => 'تم الإنشاء',
        ],
        'deleted' => [
            'title' => 'تم الحذف',
        ],
    ],

    'pages' => [
        'dashboard' => [
            'title' => 'لوحة التحكم',
        ],
    ],

    'resources' => [
        'create' => [
            'title' => 'إنشاء :label',
        ],
        'edit' => [
            'title' => 'تعديل :label',
        ],
        'list' => [
            'title' => ':label',
        ],
    ],

    'table' => [
        'actions' => [
            'filter' => [
                'label' => 'تصفية',
            ],
            'open_bulk_actions' => [
                'label' => 'الإجراءات',
            ],
            'toggle_columns' => [
                'label' => 'تبديل الأعمدة',
            ],
        ],
        'bulk_actions' => [
            'delete' => [
                'label' => 'حذف المحدد',
            ],
        ],
        'columns' => [
            'text' => [
                'more_list_items' => 'و :count أكثر',
            ],
        ],
        'empty' => [
            'heading' => 'لا توجد :model',
            'description' => 'قم بإنشاء :model للبدء.',
        ],
        'filters' => [
            'actions' => [
                'remove' => [
                    'label' => 'إزالة التصفية',
                ],
                'remove_all' => [
                    'label' => 'إزالة جميع التصفيات',
                ],
            ],
            'heading' => 'التصفيات',
            'indicator' => 'التصفيات النشطة',
        ],
        'reorder' => [
            'indicator' => 'إعادة الترتيب',
        ],
        'search' => [
            'label' => 'بحث',
            'placeholder' => 'بحث',
        ],
        'selection_indicator' => [
            'selected_count' => 'تم تحديد :count|تم تحديد :count',
            'actions' => [
                'select_all' => [
                    'label' => 'تحديد الكل :count',
                ],
                'deselect_all' => [
                    'label' => 'إلغاء تحديد الكل',
                ],
            ],
        ],
    ],

    'user_menu' => [
        'account' => [
            'label' => 'الحساب',
        ],
        'sign_out' => [
            'label' => 'تسجيل الخروج',
        ],
    ],
];
