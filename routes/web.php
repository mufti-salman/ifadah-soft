<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\AdmissionController;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\InstitutionController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\SectionController;

use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\IncomeExpenseReportController;
use App\Http\Controllers\Admin\LedgerController;
use App\Http\Controllers\Admin\SubLedgerController;
use App\Http\Controllers\Admin\FundController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\CashierController;

use App\Http\Controllers\Admin\MonthSettingController;
use App\Http\Controllers\Admin\FeeSettingController;
use App\Http\Controllers\Admin\FeeGroupController;
use App\Http\Controllers\Admin\FeeCollectionController;
use App\Http\Controllers\Admin\FeeTypeController;
use App\Http\Controllers\Admin\StudentFeeCollectionController;
use App\Http\Controllers\Admin\FeeReportController;

use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\SettingController;

use App\Http\Controllers\Admin\SystemPanelController;
use App\Http\Controllers\Admin\SystemPanelManagerController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RolePermissionController;

use App\Http\Controllers\Admin\SupportAdminController;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| Public AJAX Routes
|--------------------------------------------------------------------------
*/

Route::get(
    'get-districts/{divisionId}',
    [UserController::class, 'getDistricts']
)->name('get.districts');

Route::get(
    'get-upazilas/{districtId}',
    [UserController::class, 'getUpazilas']
)->name('get.upazilas');


/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| All authenticated dashboard users come through:
| auth + institution middleware.
|
*/

Route::prefix('dashboard')
    ->name('dashboard.')
    ->middleware(['auth', 'institution'])
    ->group(function () {


        /*
        |--------------------------------------------------------------------------
        | 1. DASHBOARD
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/',
            [AdminDashboardController::class, 'index']
        )
            ->middleware('permission:dashboard.view')
            ->name('index');


        /*
        |--------------------------------------------------------------------------
        | 2. PROFILE
        |--------------------------------------------------------------------------
        */

        Route::prefix('profile')->group(function () {

            Route::get(
                '/',
                [ProfileController::class, 'adminProfile']
            )->name('profile');

            Route::post(
                '/',
                [ProfileController::class, 'adminProfileUpdate']
            )->name('profile.update');

            Route::get(
                'account',
                [ProfileController::class, 'adminAccount']
            )->name('account');

            Route::post(
                'account/password',
                [ProfileController::class, 'adminPasswordUpdate']
            )->name('account.password');

        });


        /*
        |--------------------------------------------------------------------------
        | 3. NOTIFICATIONS
        |--------------------------------------------------------------------------
        */

        Route::prefix('notifications')->group(function () {

            Route::get(
                'poll',
                [NotificationController::class, 'poll']
            )->name('notifications.poll');

            Route::post(
                'mark-all-read',
                [NotificationController::class, 'markAllRead']
            )->name('notifications.markAllRead');

            Route::post(
                'clear-all',
                [NotificationController::class, 'clearAll']
            )->name('notifications.clearAll');

        });


        /*
        |--------------------------------------------------------------------------
        | 4. GENERAL SETTINGS
        |--------------------------------------------------------------------------
        */

        Route::get(
            'settings',
            [SettingController::class, 'index']
        )
            ->middleware('permission:general.view')
            ->name('settings.index');


        /*
        |--------------------------------------------------------------------------
        | 5. USER MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::prefix('users')
            ->middleware('permission:user.view')
            ->group(function () {

                /*
                |--------------------------------------------------------------------------
                | User List
                |--------------------------------------------------------------------------
                */

                Route::get(
                    '/',
                    [UserController::class, 'index']
                )->name('user.index');


                /*
                |--------------------------------------------------------------------------
                | Create User
                |--------------------------------------------------------------------------
                */

                Route::get(
                    'create',
                    [UserController::class, 'create']
                )
                    ->middleware('permission:user.create')
                    ->name('user.create');


                /*
                |--------------------------------------------------------------------------
                | Store User
                |--------------------------------------------------------------------------
                */

                Route::post(
                    'store',
                    [UserController::class, 'store']
                )
                    ->middleware('permission:user.create')
                    ->name('user.store');


                /*
                |--------------------------------------------------------------------------
                | User Search
                |--------------------------------------------------------------------------
                */

                Route::get(
                    'search',
                    [UserController::class, 'search']
                )->name('user.search');


                /*
                |--------------------------------------------------------------------------
                | Advanced Search
                |--------------------------------------------------------------------------
                */

                Route::get(
                    'advanced-search',
                    [UserController::class, 'advancedSearch']
                )->name('user.advanced-search');


                /*
                |--------------------------------------------------------------------------
                | Preview Institution User ID
                |--------------------------------------------------------------------------
                |
                | Create Blade AJAX uses GET request.
                |
                */

                Route::get(
                    'preview-id',
                    [UserController::class, 'previewId']
                )
                    ->middleware('permission:user.create')
                    ->name('user.preview-id');


                /*
                |--------------------------------------------------------------------------
                | User Specific Routes
                |--------------------------------------------------------------------------
                */

                Route::prefix('{id}')->group(function () {

                    /*
                    |--------------------------------------------------------------------------
                    | Show User
                    |--------------------------------------------------------------------------
                    */

                    Route::get(
                        '/',
                        [UserController::class, 'show']
                    )->name('user.show');


                    /*
                    |--------------------------------------------------------------------------
                    | Edit User
                    |--------------------------------------------------------------------------
                    */

                    Route::get(
                        'edit',
                        [UserController::class, 'edit']
                    )
                        ->middleware('permission:user.edit')
                        ->name('user.edit');


                    /*
                    |--------------------------------------------------------------------------
                    | Update User
                    |--------------------------------------------------------------------------
                    */

                    Route::put(
                        '/',
                        [UserController::class, 'update']
                    )
                        ->middleware('permission:user.edit')
                        ->name('user.update');


                    /*
                    |--------------------------------------------------------------------------
                    | Delete User
                    |--------------------------------------------------------------------------
                    */

                    Route::delete(
                        '/',
                        [UserController::class, 'destroy']
                    )
                        ->middleware('permission:user.delete')
                        ->name('user.destroy');


                    /*
                    |--------------------------------------------------------------------------
                    | Toggle Status
                    |--------------------------------------------------------------------------
                    */

                    Route::patch(
                        'toggle-status',
                        [UserController::class, 'toggleStatus']
                    )
                        ->middleware('permission:user.edit')
                        ->name('user.toggle-status');

                });

            });


        /*
        |--------------------------------------------------------------------------
        | 6. STUDENT MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::prefix('students')
            ->middleware('permission:student.view')
            ->group(function () {

                Route::get(
                    '/',
                    [StudentController::class, 'index']
                )->name('students.index');

                Route::get(
                    'search',
                    [StudentController::class, 'search']
                )->name('students.search');

                Route::get(
                    'create',
                    [StudentController::class, 'create']
                )
                    ->middleware('permission:student.create')
                    ->name('students.create');

                Route::post(
                    'store',
                    [StudentController::class, 'store']
                )
                    ->middleware('permission:student.create')
                    ->name('students.store');


                Route::prefix('{id}')->group(function () {

                    Route::get(
                        '/',
                        [StudentController::class, 'show']
                    )->name('students.show');

                    Route::get(
                        'edit',
                        [StudentController::class, 'edit']
                    )
                        ->middleware('permission:student.edit')
                        ->name('students.edit');

                    Route::put(
                        '/',
                        [StudentController::class, 'update']
                    )
                        ->middleware('permission:student.edit')
                        ->name('students.update');

                    Route::delete(
                        '/',
                        [StudentController::class, 'destroy']
                    )
                        ->middleware('permission:student.delete')
                        ->name('students.destroy');

                });

            });


        /*
        |--------------------------------------------------------------------------
        | 7. ADMISSION MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::prefix('admissions')
            ->middleware('permission:admission.view')
            ->group(function () {

                Route::get(
                    '/',
                    [AdmissionController::class, 'index']
                )->name('admissions.index');

                Route::get(
                    'search',
                    [AdmissionController::class, 'search']
                )->name('admissions.search');

                Route::get(
                    'fee-structure/{classId}',
                    [AdmissionController::class, 'getFeeStructure']
                )->name('admissions.fee-structure');

                Route::get(
                    'create',
                    [AdmissionController::class, 'create']
                )
                    ->middleware('permission:admission.create')
                    ->name('admissions.create');

                Route::post(
                    'store',
                    [AdmissionController::class, 'store']
                )
                    ->middleware('permission:admission.create')
                    ->name('admissions.store');


                Route::prefix('{id}')->group(function () {

                    Route::get(
                        '/',
                        [AdmissionController::class, 'show']
                    )->name('admissions.show');

                    Route::get(
                        'edit',
                        [AdmissionController::class, 'edit']
                    )
                        ->middleware('permission:admission.edit')
                        ->name('admissions.edit');

                    Route::put(
                        '/',
                        [AdmissionController::class, 'update']
                    )
                        ->middleware('permission:admission.edit')
                        ->name('admissions.update');

                    Route::delete(
                        '/',
                        [AdmissionController::class, 'destroy']
                    )
                        ->middleware('permission:admission.delete')
                        ->name('admissions.destroy');

                    Route::patch(
                        'toggle-status',
                        [AdmissionController::class, 'toggleStatus']
                    )
                        ->middleware('permission:admission.edit')
                        ->name('admissions.toggle-status');

                });

            });


        /*
        |--------------------------------------------------------------------------
        | 8. CLASS MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::prefix('classes')
            ->middleware('permission:class.view')
            ->group(function () {

                Route::get(
                    '/',
                    [ClassController::class, 'index']
                )->name('classes.index');

                Route::get(
                    'create',
                    [ClassController::class, 'create']
                )
                    ->middleware('permission:class.create')
                    ->name('classes.create');

                Route::post(
                    'store',
                    [ClassController::class, 'store']
                )
                    ->middleware('permission:class.create')
                    ->name('classes.store');

                Route::post(
                    'store-ajax',
                    [ClassController::class, 'storeAjax']
                )
                    ->middleware('permission:class.create')
                    ->name('class.store-ajax');

                Route::get(
                    'all',
                    [ClassController::class, 'getAllClasses']
                )->name('classes.all');


                Route::prefix('{id}')->group(function () {

                    Route::get(
                        '/',
                        [ClassController::class, 'show']
                    )->name('classes.show');

                    Route::get(
                        'edit',
                        [ClassController::class, 'edit']
                    )
                        ->middleware('permission:class.edit')
                        ->name('classes.edit');

                    Route::put(
                        '/',
                        [ClassController::class, 'update']
                    )
                        ->middleware('permission:class.edit')
                        ->name('classes.update');

                    Route::delete(
                        '/',
                        [ClassController::class, 'destroy']
                    )
                        ->middleware('permission:class.delete')
                        ->name('classes.destroy');

                    Route::post(
                        'toggle-status',
                        [ClassController::class, 'toggleStatus']
                    )
                        ->middleware('permission:class.edit')
                        ->name('classes.toggle-status');

                    Route::get(
                        'sections',
                        [ClassController::class, 'getSections']
                    )->name('classes.sections');

                });

            });


        /*
        |--------------------------------------------------------------------------
        | 9. SECTION MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::prefix('sections')
            ->middleware('permission:section.view')
            ->group(function () {

                Route::get(
                    '/',
                    [SectionController::class, 'index']
                )->name('sections.index');

                Route::get(
                    'create',
                    [SectionController::class, 'create']
                )
                    ->middleware('permission:section.create')
                    ->name('sections.create');

                Route::post(
                    'store',
                    [SectionController::class, 'store']
                )
                    ->middleware('permission:section.create')
                    ->name('sections.store');

                Route::post(
                    'bulk-create',
                    [SectionController::class, 'bulkCreate']
                )
                    ->middleware('permission:section.create')
                    ->name('sections.bulk-create');

                Route::get(
                    'stats',
                    [SectionController::class, 'getSectionStats']
                )->name('sections.stats');

                Route::get(
                    'by-class/{classId}',
                    [SectionController::class, 'getSectionsByClass']
                )->name('sections.by-class');


                Route::prefix('{id}')->group(function () {

                    Route::get(
                        '/',
                        [SectionController::class, 'show']
                    )->name('sections.show');

                    Route::get(
                        'edit',
                        [SectionController::class, 'edit']
                    )
                        ->middleware('permission:section.edit')
                        ->name('sections.edit');

                    Route::put(
                        '/',
                        [SectionController::class, 'update']
                    )
                        ->middleware('permission:section.edit')
                        ->name('sections.update');

                    Route::delete(
                        '/',
                        [SectionController::class, 'destroy']
                    )
                        ->middleware('permission:section.delete')
                        ->name('sections.destroy');

                    Route::post(
                        'toggle-status',
                        [SectionController::class, 'toggleStatus']
                    )
                        ->middleware('permission:section.edit')
                        ->name('sections.toggle-status');

                });

            });


        /*
        |--------------------------------------------------------------------------
        | 10. ACADEMIC YEAR MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::prefix('academic-years')
            ->middleware('permission:academic.view')
            ->group(function () {

                Route::get(
                    '/',
                    [AcademicYearController::class, 'index']
                )->name('academic-years.index');

                Route::get(
                    'create',
                    [AcademicYearController::class, 'create']
                )
                    ->middleware('permission:academic.create')
                    ->name('academic-years.create');

                Route::post(
                    'store',
                    [AcademicYearController::class, 'store']
                )
                    ->middleware('permission:academic.create')
                    ->name('academic-years.store');

                Route::post(
                    'store-ajax',
                    [AcademicYearController::class, 'storeAjax']
                )
                    ->middleware('permission:academic.create')
                    ->name('academic-year.store-ajax');


                Route::prefix('{id}')->group(function () {

                    Route::get(
                        '/',
                        [AcademicYearController::class, 'show']
                    )->name('academic-years.show');

                    Route::get(
                        'edit',
                        [AcademicYearController::class, 'edit']
                    )
                        ->middleware('permission:academic.edit')
                        ->name('academic-years.edit');

                    Route::put(
                        '/',
                        [AcademicYearController::class, 'update']
                    )
                        ->middleware('permission:academic.edit')
                        ->name('academic-years.update');

                    Route::delete(
                        '/',
                        [AcademicYearController::class, 'destroy']
                    )
                        ->middleware('permission:academic.delete')
                        ->name('academic-years.destroy');

                    Route::post(
                        'toggle-status',
                        [AcademicYearController::class, 'toggleStatus']
                    )
                        ->middleware('permission:academic.edit')
                        ->name('academic-years.toggle-status');

                    Route::post(
                        'set-current',
                        [AcademicYearController::class, 'setCurrent']
                    )
                        ->middleware('permission:academic.edit')
                        ->name('academic-years.set-current');

                });

            });


        /*
        |--------------------------------------------------------------------------
        | 11. INSTITUTION MANAGEMENT
        |--------------------------------------------------------------------------
        |
        | Institution master management is Super Admin only.
        |
        */

        Route::prefix('institutions')
            ->middleware('permission:institution.view')
            ->group(function () {

                /*
                |--------------------------------------------------------------------------
                | Institution List
                |--------------------------------------------------------------------------
                */

                Route::get(
                    '/',
                    [InstitutionController::class, 'index']
                )->name('institutions.index');


                /*
                |--------------------------------------------------------------------------
                | Create Institution
                |--------------------------------------------------------------------------
                */

                Route::post(
                    '/',
                    [InstitutionController::class, 'store']
                )
                    ->middleware([
                        'role:super_admin',
                        'permission:institution.create',
                    ])
                    ->name('institutions.store');


                /*
                |--------------------------------------------------------------------------
                | Switch Active Institution
                |--------------------------------------------------------------------------
                */

                Route::post(
                    '/switch',
                    [InstitutionController::class, 'switchInstitution']
                )
                    ->middleware('role:super_admin')
                    ->name('institutions.switch');


                /*
                |--------------------------------------------------------------------------
                | Institution Information
                |--------------------------------------------------------------------------
                */

                Route::get(
                    '/info',
                    [InstitutionController::class, 'info']
                )
                    ->middleware('permission:institution.view')
                    ->name('institution.info');


                /*
                |--------------------------------------------------------------------------
                | Exit Institution Mode
                |--------------------------------------------------------------------------
                */

                Route::post(
                    '/exit',
                    [InstitutionController::class, 'exit']
                )
                    ->middleware('role:super_admin')
                    ->name('institutions.exit');


                /*
                |--------------------------------------------------------------------------
                | Enter Institution
                |--------------------------------------------------------------------------
                */

                Route::post(
                    '/{id}/enter',
                    [InstitutionController::class, 'enter']
                )
                    ->middleware('role:super_admin')
                    ->name('institutions.enter');


                /*
                |--------------------------------------------------------------------------
                | Institution Specific Routes
                |--------------------------------------------------------------------------
                */

                Route::prefix('{id}')->group(function () {

                    Route::get(
                        '/edit',
                        [InstitutionController::class, 'edit']
                    )
                        ->middleware([
                            'role:super_admin',
                            'permission:institution.edit',
                        ])
                        ->name('institutions.edit');


                    Route::put(
                        '/',
                        [InstitutionController::class, 'update']
                    )
                        ->middleware([
                            'role:super_admin',
                            'permission:institution.edit',
                        ])
                        ->name('institutions.update');


                    Route::delete(
                        '/',
                        [InstitutionController::class, 'destroy']
                    )
                        ->middleware([
                            'role:super_admin',
                            'permission:institution.delete',
                        ])
                        ->name('institutions.destroy');

                });

            });


        /*
        |--------------------------------------------------------------------------
        | 12. MONTH SETTINGS
        |--------------------------------------------------------------------------
        */

        Route::prefix('month')
            ->middleware('permission:month.view')
            ->group(function () {

                Route::get(
                    '/',
                    [MonthSettingController::class, 'index']
                )->name('month.index');

                Route::post(
                    '/',
                    [MonthSettingController::class, 'store']
                )
                    ->middleware('permission:month.create')
                    ->name('month.store');

                Route::get(
                    'suggest',
                    [MonthSettingController::class, 'suggest']
                )->name('month.suggest');


                Route::prefix('{monthSetting}')->group(function () {

                    Route::get(
                        'edit',
                        [MonthSettingController::class, 'edit']
                    )
                        ->middleware('permission:month.edit')
                        ->name('month.edit');

                    Route::put(
                        '/',
                        [MonthSettingController::class, 'update']
                    )
                        ->middleware('permission:month.edit')
                        ->name('month.update');

                    Route::delete(
                        '/',
                        [MonthSettingController::class, 'destroy']
                    )
                        ->middleware('permission:month.delete')
                        ->name('month.destroy');

                });

            });


        /*
        |--------------------------------------------------------------------------
        | 13. FEE TYPE
        |--------------------------------------------------------------------------
        */

        Route::prefix('fee-type')
            ->middleware('permission:fee-type.view')
            ->group(function () {

                Route::get(
                    '/',
                    [FeeTypeController::class, 'index']
                )->name('fee-type.index');

                Route::post(
                    'store',
                    [FeeTypeController::class, 'store']
                )
                    ->middleware('permission:fee-type.create')
                    ->name('fee-type.store');

                Route::get(
                    'edit/{id}',
                    [FeeTypeController::class, 'edit']
                )
                    ->middleware('permission:fee-type.edit')
                    ->name('fee-type.edit');

                Route::put(
                    'update/{id}',
                    [FeeTypeController::class, 'update']
                )
                    ->middleware('permission:fee-type.edit')
                    ->name('fee-type.update');

                Route::delete(
                    'destroy/{id}',
                    [FeeTypeController::class, 'destroy']
                )
                    ->middleware('permission:fee-type.delete')
                    ->name('fee-type.destroy');

                Route::post(
                    'toggle-status/{id}',
                    [FeeTypeController::class, 'toggleStatus']
                )
                    ->middleware('permission:fee-type.edit')
                    ->name('fee-type.toggle-status');

            });


        /*
        |--------------------------------------------------------------------------
        | 14. FEE SETTINGS
        |--------------------------------------------------------------------------
        */

        Route::prefix('fee-settings')
            ->middleware('permission:fee.view')
            ->group(function () {

                Route::get(
                    '/',
                    [FeeSettingController::class, 'index']
                )->name('fee-settings.index');

                Route::get(
                    'get',
                    [FeeSettingController::class, 'get']
                )->name('fee-settings.get');

                Route::post(
                    'save',
                    [FeeSettingController::class, 'save']
                )
                    ->middleware('permission:fee.create')
                    ->name('fee-settings.save');

                Route::post(
                    'reset',
                    [FeeSettingController::class, 'reset']
                )
                    ->middleware('permission:fee.delete')
                    ->name('fee-settings.reset');

            });


        /*
        |--------------------------------------------------------------------------
        | 15. FEE GROUP
        |--------------------------------------------------------------------------
        */

        Route::prefix('fees')
            ->middleware('permission:fee.view')
            ->group(function () {

                Route::get(
                    '/',
                    [FeeGroupController::class, 'index']
                )->name('fees.index');

                Route::get(
                    'create',
                    [FeeGroupController::class, 'create']
                )
                    ->middleware('permission:fee.create')
                    ->name('fees.create');

                Route::post(
                    'store',
                    [FeeGroupController::class, 'store']
                )
                    ->middleware('permission:fee.create')
                    ->name('fees.store');


                Route::get(
                    'get-ledgers/{fund_id}',
                    [FeeGroupController::class, 'getLedgers']
                )->name('fees.getLedgers');

                Route::get(
                    'get-sub-ledgers/{ledger_id}',
                    [FeeGroupController::class, 'getSubLedgers']
                )->name('fees.getSubLedgers');

                Route::get(
                    'get-student-fees',
                    [FeeGroupController::class, 'getStudentFees']
                )->name('fees.getStudentFees');

                Route::post(
                    'update-status',
                    [FeeGroupController::class, 'updateStatus']
                )
                    ->middleware('permission:fee.edit')
                    ->name('fees.updateStatus');


                Route::prefix('{fee}')->group(function () {

                    Route::get(
                        'edit',
                        [FeeGroupController::class, 'edit']
                    )
                        ->middleware('permission:fee.edit')
                        ->name('fees.edit');

                    Route::put(
                        'update',
                        [FeeGroupController::class, 'update']
                    )
                        ->middleware('permission:fee.edit')
                        ->name('fees.update');

                    Route::delete(
                        'delete',
                        [FeeGroupController::class, 'destroy']
                    )
                        ->middleware('permission:fee.delete')
                        ->name('fees.destroy');

                });

            });


        /*
        |--------------------------------------------------------------------------
        | 16. FEE COLLECTION
        |--------------------------------------------------------------------------
        */

        Route::prefix('fee-collection')
            ->middleware('permission:fee.view')
            ->group(function () {

                Route::get(
                    '/',
                    [StudentFeeCollectionController::class, 'index']
                )->name('fee-collection.index');

                Route::get(
                    'student-info',
                    [StudentFeeCollectionController::class, 'studentInfo']
                )->name('fee-collection.studentInfo');

                Route::get(
                    'payment-methods',
                    [StudentFeeCollectionController::class, 'paymentMethods']
                )->name('fee-collection.paymentMethods');

                Route::post(
                    'save-payment',
                    [StudentFeeCollectionController::class, 'savePayment']
                )
                    ->middleware('permission:fee.collect')
                    ->name('fee-collection.savePayment');

                Route::get(
                    'today-payments',
                    [StudentFeeCollectionController::class, 'todayPayments']
                )->name('fee-collection.todayPayments');

                Route::get(
                    'statement',
                    [StudentFeeCollectionController::class, 'statement']
                )->name('fee-collection.statement');

                Route::post(
                    'add-cashier',
                    [StudentFeeCollectionController::class, 'addCashier']
                )
                    ->middleware('permission:fee.collect')
                    ->name('fee-collection.addCashier');

                Route::get(
                    'print-receipt/{id}',
                    [StudentFeeCollectionController::class, 'printReceipt']
                )
                    ->middleware('permission:invoice.print')
                    ->name('fee-collection.printReceipt');

            });


        /*
        |--------------------------------------------------------------------------
        | 17. ACCOUNTING
        |--------------------------------------------------------------------------
        */

        Route::prefix('accounting')
            ->middleware('permission:income.view')
            ->group(function () {


                /*
                |--------------------------------------------------------------------------
                | Transactions
                |--------------------------------------------------------------------------
                */

                Route::prefix('transactions')->group(function () {

                    Route::get(
                        '/',
                        [TransactionController::class, 'index']
                    )->name('transactions.index');

                    Route::post(
                        'store',
                        [TransactionController::class, 'store']
                    )
                        ->middleware('permission:income.create')
                        ->name('transactions.store');

                    Route::delete(
                        '{id}',
                        [TransactionController::class, 'destroy']
                    )
                        ->middleware('permission:income.delete')
                        ->name('transactions.destroy');

                });


                /*
                |--------------------------------------------------------------------------
                | Income Expense Report
                |--------------------------------------------------------------------------
                */

                Route::get(
                    'reports/income-expense',
                    [IncomeExpenseReportController::class, 'incomeExpense']
                )
                    ->middleware('permission:accounting-report.view')
                    ->name('reports.income-expense');


                /*
                |--------------------------------------------------------------------------
                | Fee Report
                |--------------------------------------------------------------------------
                */

                Route::prefix('fee-report')
                    ->middleware('permission:fee.report')
                    ->group(function () {

                        Route::get(
                            '/',
                            [FeeReportController::class, 'index']
                        )->name('fee-report.index');

                        Route::get(
                            'print',
                            [FeeReportController::class, 'print']
                        )
                            ->middleware('permission:accounting-report.print')
                            ->name('fee-report.print');

                        Route::get(
                            'pdf',
                            [FeeReportController::class, 'pdf']
                        )
                            ->middleware('permission:accounting-report.export')
                            ->name('fee-report.pdf');

                        Route::get(
                            'excel',
                            [FeeReportController::class, 'excel']
                        )
                            ->middleware('permission:accounting-report.export')
                            ->name('fee-report.excel');

                    });


                /*
                |--------------------------------------------------------------------------
                | Ledger
                |--------------------------------------------------------------------------
                */

                Route::prefix('ledger')->group(function () {

                    Route::get(
                        '/',
                        [LedgerController::class, 'index']
                    )->name('ledger.index');

                    Route::post(
                        'store',
                        [LedgerController::class, 'store']
                    )
                        ->middleware('permission:income.create')
                        ->name('ledger.store');

                    Route::get(
                        'fund-ledgers/{id}',
                        [LedgerController::class, 'fundLedgers']
                    )->name('ledger.fundLedgers');

                });


                /*
                |--------------------------------------------------------------------------
                | Sub Ledger
                |--------------------------------------------------------------------------
                */

                Route::prefix('sub-ledger')->group(function () {

                    Route::get(
                        '/',
                        [SubLedgerController::class, 'index']
                    )->name('sub-ledger.index');

                    Route::post(
                        'store',
                        [SubLedgerController::class, 'store']
                    )
                        ->middleware('permission:income.create')
                        ->name('sub-ledger.store');

                    Route::get(
                        '{id}',
                        [SubLedgerController::class, 'getByLedger']
                    )->name('sub-ledger.getByLedger');

                });


                /*
                |--------------------------------------------------------------------------
                | Fund
                |--------------------------------------------------------------------------
                */

                Route::post(
                    'fund/store',
                    [FundController::class, 'store']
                )
                    ->middleware('permission:income.create')
                    ->name('fund.store');


                /*
                |--------------------------------------------------------------------------
                | Payment Method
                |--------------------------------------------------------------------------
                */

                Route::prefix('payment-method')->group(function () {

                    Route::get(
                        'list',
                        [PaymentMethodController::class, 'index']
                    )->name('payment-method.index');

                    Route::post(
                        'store',
                        [PaymentMethodController::class, 'store']
                    )
                        ->middleware('permission:payment.create')
                        ->name('payment-method.store');

                    Route::delete(
                        '{id}',
                        [PaymentMethodController::class, 'destroy']
                    )
                        ->middleware('permission:payment.delete')
                        ->name('payment-method.destroy');

                });


                /*
                |--------------------------------------------------------------------------
                | Cashier
                |--------------------------------------------------------------------------
                */

                Route::prefix('cashier')->group(function () {

                    Route::get(
                        'list',
                        [CashierController::class, 'index']
                    )->name('cashier.index');

                    Route::post(
                        'store',
                        [CashierController::class, 'store']
                    )
                        ->middleware('permission:payment.create')
                        ->name('cashier.store');

                    Route::delete(
                        '{id}',
                        [CashierController::class, 'destroy']
                    )
                        ->middleware('permission:payment.delete')
                        ->name('cashier.destroy');

                });

            });


        /*
        |--------------------------------------------------------------------------
        | 18. STUDENT ACCESS
        |--------------------------------------------------------------------------
        */

        Route::prefix('student')
            ->middleware('role:student')
            ->group(function () {

                Route::get(
                    'my-profile',
                    [StudentController::class, 'myProfile']
                )->name('student.profile');

                Route::get(
                    'my-admission',
                    [StudentController::class, 'myAdmission']
                )->name('student.admission');

            });


        /*
        |--------------------------------------------------------------------------
        | 19. GUARDIAN MODULE
        |--------------------------------------------------------------------------
        |
        | Guardian is NOT a role.
        | It is handled through guardian permissions.
        |
        */

        Route::prefix('guardian')
            ->middleware('permission:guardian.view')
            ->group(function () {

                Route::get(
                    'my-children',
                    [StudentController::class, 'myChildren']
                )->name('guardian.children');

                Route::get(
                    'my-children/{id}',
                    [StudentController::class, 'show']
                )->name('guardian.child.show');

            });


        /*
        |--------------------------------------------------------------------------
        | 20. TEACHER MODULE
        |--------------------------------------------------------------------------
        |
        | Teacher is NOT a role.
        | Teacher access is permission based.
        |
        */

        Route::prefix('teacher')
            ->middleware('permission:teacher.view')
            ->group(function () {

                /*
                |--------------------------------------------------------------------------
                | Teacher → Students
                |--------------------------------------------------------------------------
                */

                Route::get(
                    'students',
                    [StudentController::class, 'index']
                )
                    ->middleware('permission:student.view')
                    ->name('teacher.students.index');

                Route::get(
                    'students/{id}',
                    [StudentController::class, 'show']
                )
                    ->middleware('permission:student.view')
                    ->name('teacher.students.show');


                /*
                |--------------------------------------------------------------------------
                | Teacher → Admissions
                |--------------------------------------------------------------------------
                */

                Route::get(
                    'admissions',
                    [AdmissionController::class, 'index']
                )
                    ->middleware('permission:admission.view')
                    ->name('teacher.admissions.index');

                Route::get(
                    'admissions/{id}',
                    [AdmissionController::class, 'show']
                )
                    ->middleware('permission:admission.view')
                    ->name('teacher.admissions.show');

            });

    });


/*
|--------------------------------------------------------------------------
| Support Admin Management
|--------------------------------------------------------------------------
|
| IMPORTANT:
| Support Admin routes are OUTSIDE the dashboard group.
|
| Only Super Admin can manage Support Admins.
|
*/

Route::prefix('support-admins')
    ->name('dashboard.support-admins.')
    ->middleware([
        'auth',
        'role:super_admin',
    ])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | List
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/',
            [SupportAdminController::class, 'index']
        )->name('index');


        /*
        |--------------------------------------------------------------------------
        | Create
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/create',
            [SupportAdminController::class, 'create']
        )->name('create');


        /*
        |--------------------------------------------------------------------------
        | Store
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/',
            [SupportAdminController::class, 'store']
        )->name('store');


        /*
        |--------------------------------------------------------------------------
        | Toggle Status
        |--------------------------------------------------------------------------
        */

        Route::patch(
            '/{supportAdmin}/toggle-status',
            [SupportAdminController::class, 'toggleStatus']
        )->name('toggle-status');


        /*
        |--------------------------------------------------------------------------
        | Edit
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/{supportAdmin}/edit',
            [SupportAdminController::class, 'edit']
        )->name('edit');


        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        Route::put(
            '/{supportAdmin}',
            [SupportAdminController::class, 'update']
        )->name('update');


        /*
        |--------------------------------------------------------------------------
        | Permission Management
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/{supportAdmin}/permissions',
            [SupportAdminController::class, 'permissions']
        )->name('permissions');


        Route::put(
            '/{supportAdmin}/permissions',
            [SupportAdminController::class, 'updatePermissions']
        )->name('permissions.update');


        /*
        |--------------------------------------------------------------------------
        | Delete
        |--------------------------------------------------------------------------
        */

        Route::delete(
            '/{supportAdmin}',
            [SupportAdminController::class, 'destroy']
        )->name('destroy');

    });


/*
|--------------------------------------------------------------------------
| System Management
|--------------------------------------------------------------------------
|
| Super Admin only.
|
*/

Route::prefix('dashboard/system')
    ->name('dashboard.')
    ->middleware([
        'auth',
        'role:super_admin',
        'permission:system-panel.view',
    ])
    ->group(function () {


        /*
        |--------------------------------------------------------------------------
        | System Panels
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'panels',
            SystemPanelController::class
        )
            ->except(['show'])
            ->names([
                'index' => 'system-panels.index',
                'create' => 'system-panels.create',
                'store' => 'system-panels.store',
                'edit' => 'system-panels.edit',
                'update' => 'system-panels.update',
                'destroy' => 'system-panels.destroy',
            ]);


        /*
        |--------------------------------------------------------------------------
        | Panel Manager
        |--------------------------------------------------------------------------
        */

        Route::get(
            'panel-manager',
            [SystemPanelManagerController::class, 'index']
        )
            ->middleware('permission:role-panel.view')
            ->name('system-panel-manager.index');

        Route::post(
            'panel-manager/update',
            [SystemPanelManagerController::class, 'update']
        )
            ->middleware('permission:role-panel.edit')
            ->name('system-panel-manager.update');


        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'permissions',
            PermissionController::class
        )
            ->except(['show', 'create'])
            ->middleware('permission:permission.view')
            ->names([
                'index' => 'permissions.index',
                'edit' => 'permissions.edit',
                'update' => 'permissions.update',
                'destroy' => 'permissions.destroy',
            ]);


        /*
        |--------------------------------------------------------------------------
        | Institution Permission Manager
        |--------------------------------------------------------------------------
        */

        Route::get(
            'role-permissions',
            [RolePermissionController::class, 'index']
        )
            ->middleware([
                'permission:role.view',
                'permission:permission.view',
            ])
            ->name('role-permissions.index');

        Route::post(
            'role-permissions',
            [RolePermissionController::class, 'update']
        )
            ->middleware([
                'permission:role.view',
                'permission:permission.view',
            ])
            ->name('role-permissions.update');

    });


/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';