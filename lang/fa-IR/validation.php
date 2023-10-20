<?php

use App\Models\Coupon;

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */
    Coupon::COUPON_VALIDATION_INTERPRETER[Coupon::COUPON_VALIDATION_STATUS_NOT_FOUND] => 'کد وارد شده صحیح نمیباشد.',
    Coupon::COUPON_VALIDATION_INTERPRETER[Coupon::COUPON_VALIDATION_STATUS_DISABLED] => 'کد وارد شده غیرفعال شده است.',
    Coupon::COUPON_VALIDATION_INTERPRETER[Coupon::COUPON_VALIDATION_STATUS_USAGE_LIMIT_FINISHED] => 'تعداد دفعات استفاده از این کد به اتمام رسیده است.',
    Coupon::COUPON_VALIDATION_INTERPRETER[Coupon::COUPON_VALIDATION_STATUS_EXPIRED] => 'تاریخ اعتبار کد وارد شده به اتمام رسیده است.',
    Coupon::COUPON_VALIDATION_INTERPRETER[Coupon::COUPON_VALIDATION_STATUS_USAGE_TIME_NOT_BEGUN] => 'زمان استفاده از کد وارد شده هنوز فرا نرسیده است.',
    'accepted' => 'The :attribute must be accepted.',
    'active_url' => 'The :attribute is not a valid URL.',
    'after' => 'The :attribute must be a date after :date.',
    'alpha' => 'The :attribute may only contain letters.',
    'alpha_dash' => ':attribute تنها میتواند شامل حروف، عدد و ـ باشد',
    'alpha_num' => ':attribute فقط می تواند شامل عدد باشد.',
    'array' => 'The :attribute must be an array.',
    'before' => 'The :attribute must be a date before :date.',
    'between' => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file' => 'The :attribute must be between :min and :max kilobytes.',
        'string' => 'The :attribute must be between :min and :max characters.',
        'array' => 'The :attribute must have between :min and :max items.',
    ],
    'boolean' => 'The :attribute field must be true or false.',
    'confirmed' => ':attribute و تکرار آن یکسان نیستند',
    'date' => ':attribute به صورت استاندارد معتبر وارد نشده است',
    'date_format' => 'The :attribute does not match the format :format.',
    'different' => 'The :attribute and :other must be different.',
    'digits' => ':attribute باید :digits رقم باشد.',
    'digits_between' => ':attribute می تواند بین :min تا :max رقم باشد.',
    'email' => ':attribute باید معتبر باشد.',
    'exists' => 'مقدار انتخاب شده برای  :attribute معتبر نمی باشد.',
    'filled' => 'The :attribute field is required.',
    'image' => 'لطفا یک :attribute انتخاب کنید.',
    'in' => ':attribute انتخاب شده معتبر نیست',
    'integer' => ':attribute باید یک عدد باشد',
    'ip' => 'The :attribute must be a valid IP address.',
    'json' => 'The :attribute must be a valid JSON string.',
    'max' => [
        'numeric' => ':attribute نمی تواند از :max بیشتر باشد.',
        'file' => 'فایل :attribute می تواند حداکثر :max کیلوبایت باشد.',
        'string' => ':attribute نمی تواند بیشتر از :max کاراکتر باشد.',
        'array' => 'The :attribute may not have more than :max items.',
    ],
    'mimes' => 'فرمت های مجاز :attribute : :values',
    'min' => [
        'numeric' => ':attribute باید حداقل :min باشد',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'string' => ':attribute باید حداقل :min کاراکتر باشد.',
        'array' => 'The :attribute must have at least :min items.',
    ],
    'not_in' => 'The selected :attribute is invalid.',
    'numeric' => ':attribute باید رشته ی عددی باشد.',
    'regex' => 'The :attribute format is invalid.',
    'required' => 'وارد کردن :attribute الزامیست.',
    'present' => 'وارد کردن :attribute الزامیست.',
    'required_if' => 'وارد کردن :attribute هنگامی که :other برابر :value است الزامیست.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'فیلد :attribute الزامی است زمانی که فیلد(های) :values مقدار دارند.',
    'required_with_all' => 'The :attribute field is required when :values is present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => ':attribute باید وارد شود زیرا هیچکدام از  :values وارد نشده اند',
    'same' => 'The :attribute and :other must match.',
    'size' => [
        'numeric' => 'The :attribute must be :size.',
        'file' => 'The :attribute must be :size kilobytes.',
        'string' => 'The :attribute must be :size characters.',
        'array' => 'The :attribute must contain :size items.',
    ],
    'string' => ':attribute باید رشته ای از حروف باشد.',
    'timezone' => 'The :attribute must be a valid zone.',
    'unique' => ':attribute قبلا ثبت شده است',
    'uploaded' => 'آپلود :attribute نا موفق بود.',
    'url' => ' فرمت :attribute معتبر نمی باشد',
    'validate' => 'مقدار :attribute معتبر نمی باشد.',
    'array2d' => 'مقدار :attribute باید آرایه دو بعدی باشد.',
    'recaptcha' => 'مقدار :attribute معتبر نمی باشد.',
    'own' => ':attribute متعلق به کاربر نمی باشد.',
    'enable' => ':attribute غیر فعال می باشد.',
    'notEmptyArray' => ':attribute  نباید خالی باشد.',
    'NotEmptyString' => ':attribute  نباید خالی باشد.',
    'gte' => 'مقدار :attribute بایستی بزرگتر یا برابر :value باشد',
    'lte' => 'مقدار :attribute بایستی کمتر یا برابر  :value باشد',
    'owner' => 'شما نمیتوانید از :attribute خودتان استفاده کنید.',
    'should_auth' => 'برای استفاده از کد معرف ابتدا بایستی وارد حساب کاربری خود شودید',
    'is_used' => 'هر کاربر تنها یک بار مجاز به استفاده از :attribute  است',
    'unique_yalda_code' => 'شما قبلا از کد معرف استفاده کرده اید',
    'audit_report_is_soon' => 'برای دریافت گزارش حسابرسی نوبت اول بایستی از نیمه ماه گذشته باشید و برای گزارش نیمه دوم باید در روز آخر ماه و یا ماه بعد اقدام کنید',


    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'report_gateway' => [
            'duplication' => 'برای این عنوان گزارش و درگاه قبلا گزارش گرفته شده است.',
        ],
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
        'report_title' => [
            'unique' => 'این گزارش قبلا ایجاد شده است',
        ],
        'assignmentstatus_id' => [
            'min' => 'وضعیت تمرین باید مشخص شود',
        ],
        'consultationstatus_id' => [
            'min' => 'وضعیت مشاوره باید مشخص شود',
        ],
        'major_id' => [
            'min' => 'رشته باید مشخص شود',
            'exists' => 'رشته باید مشخص شود',
        ],
        'grade_id' => [
            'exists' => 'مقطع باید مشخص شود',
        ],
        'userstatus_id' => [
            'min' => 'وضعیت کاربر باید مشخص شود',
        ],
        'nationalCode' => [
            'validate' => 'کد ملی معتبر نمی باشد',
        ],
        'orderstatus_id' => [
            'exists' => 'وضعیت انتخاب شده معتبر نمی باشد',
        ],
        'transactionID' => [
            'max' => 'این تراکنش شماره ندارد.',
            'required_if' => 'وارد کردن شماره تراکنش الزامی است.',
        ],
        'usageLimit' => [
            'required_if' => 'در حالت محدود ، تعیین تعداد مجاز برای استفاده از کپن الزامیست.',
        ],
        'report_order' => [
            'required_if' => 'برای دریافت گزارش حسابرسی بایستی نوبت گزارش را انتخاب کنید',
        ],
        'report_month' => [
            'required_if' => 'برای دریافت گزارش حسابرسی بایستی ماه گزارش را انتخاب کنید',
        ],
        'amount' => [
            'required_if' => 'در حالت محدود ، تعیین تعداد موجود برای کالا الزامیست.',
        ],
        'cost' => [
            'required_if' => 'وارد کردن مبلغ الزامیست.',
        ],
        'products' => [
            'required_if' => 'انتخاب محصول الزامیست.',
            'required' => 'انتخاب محصول الزامیست.',
        ],
        'phoneNumber.*' => [
            'numeric' => 'شماره تلفن باید رشته عددی باشد.',
            'required' => 'وارد کردن شماره تلفن الزامیست.',
        ],
        'priority.*' => [
            'numeric' => 'الویت باید رشته عددی باشد.',
        ],
        'phonetype_id.*' => [
            'exists' => 'نوع وارد شده معتبر نیست.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'coupon' => 'کد تخفیف',
        'report_type' => 'نوع گزارش',
        'ids' => 'شناسه',
        'more_info_link' => 'لینک اطلاعات بیشتر',
        'click_id' => 'شناسه کلیک',
        'click_name' => 'عنوان کلیک',
        'password' => 'رمز عبور',
        'oldPassword' => 'رمز عبور قدیم',
        'email' => 'ایمیل',
        'firstName' => 'نام',
        'lastName' => 'نام خانوادگی',
        'fullName' => 'نام',
        'mobile' => 'شماره موبایل',
        'phone' => 'تلفن',
        'nationalCode' => 'کد ملی',
        'photo' => 'عکس',
        'questionFile' => 'فایل سؤال',
        'solutionFile' => 'فایل پاسخ',
        'major_id' => 'رشته',
        'majors' => 'رشته ها',
        'assignmentstatus_id' => 'وضعیت تمرین',
        'consultationstatus_id' => 'وضعیت مشاوره',
        'numberOfQuestions' => 'تعداد سؤالات',
        'userstatus_id' => 'وضعیت کاربر',
        'postalCode' => 'کد پستی',
        'g-recaptcha-response' => 'عبارت امنیتی',
        'cost' => 'مبلغ',
        'transactionID' => 'شماره تراکنش',
        'transactionstatus_id' => 'وضعیت تراکنش',
        'name' => 'نام',
        'shortName' => 'نام کوتاه',
        'code' => 'کد',
        'discount' => 'تخفیف',
        'usageNumber' => 'تعداد استفاده',
        'amount' => 'تعداد',
        'basePrice' => 'قیمت پایه',
        'image' => 'عکس',
        'attributeset_id' => 'دسته صفت',
        'title' => 'عنوان',
        'message' => 'پیام',
        'mobileNumber' => 'شماره موبایل',
        'displayName' => 'نام قابل نمایش',
        'display_name' => 'نام قابل نمایش',
        'attributecontrol_id' => 'نوع کنترل صفت',
        'totalNumber' => 'تعداد بن',
        'gender_id' => 'جنسیت',
        'genders' => 'جنسیت',
        'bonPlus' => 'تعداد بن',
        'bonDiscount' => 'تخفیف بن',
        'paymentmethod_id' => 'روش پرداخت',
        'referenceNumber' => 'شماره مرجع',
        'traceNumber' => 'شماره پیگیری',
        'paycheckNumber' => 'شماره چک',
        'consultingAudioQuestions' => 'فایل صوتی سوال مشاوره ای',
        'contacttype_id' => 'نوع مخاطب',
        'relative_id' => 'نسبت مخاطب',
        'order' => 'ترتیب',
        'brief' => 'خلاصه',
        'keyword' => 'کلمات کلیدی',
        'complimentaryproducts' => 'اشانتیون',
        'producttype_id' => 'نوع محصول',
        'file' => 'فایل',
        'files' => 'فایل',
        'rank' => 'رتبه کشوری',
        'participationCode' => 'کد داوطلبی',
        'reportFile' => 'فایل کارنامه',
        'phoneNumber' => 'شماره تلفن',
        'grades' => 'مقطع',
        'grade_id' => 'مقطع',
        'content_id' => 'شناسه محتوا',
        'contenttypes' => 'نوع محتوا',
        'user_id' => 'کاربر',
        'date' => 'تاریخ',
        'score' => 'نمره',
        'province' => 'استان',
        'city' => 'شهر',
        'school' => 'مدرسه',
        'introducedBy' => 'معرف',
        'address' => 'آدرس',
        'credit' => 'اعتبار',
        'context' => 'متن',
        'birthdate' => 'تاریخ تولد',
        'ticket_id' => 'تیکت',
        'orderproduct_id' => 'محصول سفارش',
        'department_id' => 'دپارتمان',
        'priority_id' => 'اولویت',
        'map_id' => 'نقضه',
        'type_id' => 'نوع',
        'rate' => 'امتیاز',
        'assignees' => 'مسئولین',
        'has_reported' => 'گزارش شده',
        'body' => 'متن',
        'voice' => 'صدا',
        'parent_id' => 'آیدی محصول پرنت',
        'ostan_id' => 'استان',
        'shahr_id' => 'شهر',
        'discountPercentage' => 'درصد تخفیف',
        'product_id' => 'محصول',
        'redirectUrl' => 'آدرس ریدایرکت',
        'redirect_url' => 'آدرس ریدایرکت',
        'redirectCode' => 'کد ریدایرکت',
        'redirect_code' => 'کد ریدایرکت',
        'info_link' => 'لینک اطلاعات بیشتر',
        'info_id' => 'شناسه اطلاعات بیشتر',
        'info_name' => 'نام اطلاعات بیشتر',
        'attributetype' => 'نوع صفت',
        'attributetype_id' => 'شناسه نوع صفت',
        'expirationdatetime' => 'تاریخ انقضاء',
        'father_mobile' => 'شماره موبایل پدر',
        'mother_mobile' => 'شماره موبایل مادر',
        'student_register_limit' => 'محدودیت تعداد ثبت نامی',
        'referral_code' => 'کد کارت هدیه',
        'access' => 'دسترسی',
        'related_product_id' => 'محصول ارتباطی',
        'related_product_ids' => 'محصولات ارتباطی',
        'relation' => 'ارتباط',
        'required_when' => 'نیاز محصول',
        'service_id' => 'سرویس',
        'content_ids' => 'آیدی محتواها',
        'column' => 'ستون',
        'operation' => 'عملیات',
        'text' => 'متن',
        'replace' => 'جایگذاری',
        'replacing_text' => 'متن در حال جایگزین شدن',
        'tags' => 'برچسب ها',
        'tag' => 'برچسب',
        'replacing_tag' => 'تگ در حال جایگزین شدن',
        'type' => 'نوع',
        'limit' => 'محدودیت',
        'productId' => 'محصول',
        'comment' => 'یادداشت',
        'set_ids' => 'ست',
        'watchable_id' => 'محتوا',
        'exam_id' => 'آزمون',
        'consultant_firstname' => 'نام مشاور',
        'consultant_lastname' => 'نام خانوادگی مشاور',
        'consultant_mobile' => 'موبایل مشاور',
        'nomre_taraz_kol' => 'نمره تراز کل',
        'nomre_taraz_moadel' => 'نمره تراز معدل',
        'nomre_taraz_tir' => 'نمره تراز دی',
        'nomre_taraz_dey' => 'نمره تراز تیر',
        'rank_in_region' => 'رتبه در سهمیه',
        'rank_in_district' => 'رتبه در منطقه',
        'region_id' => 'سهمیه',
        'event_id' => 'رخداد',
        'shahrha' => 'شهرها',
        'university_types' => 'نوع دانشگاه',
        'shabaNumber' => 'شماره شبا',
        'preShabaNumber' => 'پیشوند شماره شبا',
        'cardNumber' => 'شماره کارت اعتباری',
        'accountNumber' => 'شماره حساب',
    ],

    'values' => [
        'operation' =>
            [
                'replace' => 'جایگذاری',
                'add' => 'اضافه کردن',
                'delete' => 'حذف کردن',
            ],
    ],

    'FileArray' => [
        'name should be set' => 'برای هر آیتم، name باید مقدار دهی شده باشد. ',
        'each field should be string' => 'مقادیر هر آیتم باید string باشد.',
        'each item in array should be instance of std class' => 'هر آیتم باید از نوع stdclass باشد.',
        'should be An array' => ':attribute باید آرایه باشد.',
    ],

    'phone' => 'شماره :attribute به درستی وارد نشده است.',
    'region_match' => 'فیلد :attribute با فیلد استان مطابقت ندارد.',
];
