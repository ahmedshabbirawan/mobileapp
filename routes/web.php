<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Organization\OrganizationController;
use App\Http\Controllers\Supplier\SupplierController;
use App\Http\Controllers\Brand\BrandController;
use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\Project\ProjectLedgerController;
use App\Http\Controllers\Warehouse\WarehouseController;
use App\Http\Controllers\Location\LocationController;
use App\Http\Controllers\Uom\UomController;
use App\Http\Controllers\Manager\ManagerController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Employee\DesignationController;
use App\Http\Controllers\Web\Product\ProductController;


use App\Http\Controllers\Post\PostController;
use App\Http\Controllers\Media\MediaController;

use App\Http\Controllers\Term\TermController;

// use App\Http\Controllers\Employee\EmployeeController;

use App\Http\Controllers\Web\Dashboard\DashboardController;
use App\Http\Controllers\Web\Auth\AuthController;
use App\Http\Controllers\Web\Categories\ParentCategoryController;
use App\Http\Controllers\Web\Categories\ProductCategoryController;
use App\Http\Controllers\Web\Categories\SubCategoryController;

use App\Http\Controllers\Web\Auth\RoleController;
use App\Http\Controllers\Web\Auth\PermissionController;
use App\Http\Controllers\Web\Auth\UserController;

use App\Http\Controllers\User\UserController as AppUserController;

use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Shop\ShopController;
use App\Http\Controllers\User\UserRoleController;

use App\Http\Controllers\Report\ProductStockController;
use App\Http\Controllers\Sale\Report\SaleReportController;
use App\Http\Controllers\Web\Product\SimpleProductController;
use App\Models\StockItem;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/clear', function () {

    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('optimize');

    return "Cleared!";
});


Route::get('/', function () {
    return view('site.home');
});



Route::get('/login', [AuthController::class, 'loginPageView'])->name('login');
Route::get('/login-page', [AuthController::class, 'loginPageView'])->name('loginpage');
Route::post('/loginpost', [AuthController::class, 'login'])->name('do-login');





Route::get('/check-test', [ProductStockController::class, 'checkModelAudit']);





Route::middleware(['web', 'auth'])->group(function () {

    Route::get('/', [DashboardController::class, 'view']);
    Route::get('/dashboard', [DashboardController::class, 'view'])->name('dashboard');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard_stats', [DashboardController::class, 'dashboardStats'])->name('dashboard_stats');
    Route::get('/update-product-name', [SimpleProductController::class, 'updateProductName']);
    Route::get('/create_first_purchase', [SimpleProductController::class, 'create_first_purchase']);

    Route::group(['prefix' => 'usermanagement', 'as' => 'usermanagement.'], function () {
        //Role
        Route::group(['prefix' => 'role/', 'as' => 'role.'], function () {
            Route::get('list', [RoleController::class, 'index'])->name('list')->middleware('permission:role.read');
            Route::get('create', [RoleController::class, 'create'])->name('create')->middleware('permission:role.create');
            Route::post('store', [RoleController::class, 'store'])->name('store')->middleware('permission:role.create');
            Route::get('edit/{id}', [RoleController::class, 'edit'])->name('edit')->middleware('permission:role.read');
            Route::post('update/{id}', [RoleController::class, 'update'])->name('update')->middleware('permission:role.update');
            Route::get('status/{id}', [RoleController::class, 'status'])->name('status')->middleware('permission:role.status');
            Route::get('delete/{id}', [RoleController::class, 'destroy'])->name('delete')->middleware('permission:role.delete');

            Route::get('permissions/{id}', [RoleController::class, 'permissions'])->name('permissions')->middleware('permission:user.read');
            Route::post('permission/{id}', [RoleController::class, 'permissionsStore'])->name('permissionStore')->middleware('permission:user.read');
        });

        //Permissions
        Route::group(['prefix' => 'permission/', 'as' => 'permission.'], function () {
            Route::get('list', [PermissionController::class, 'index'])->name('list')->middleware('permission:read.role');
            Route::get('/create', [RoleController::class, 'create'])->name('create')->middleware('permission:create.role');
            Route::get('/store', [RoleController::class, 'store'])->name('store')->middleware('permission:create.role');
            Route::get('/edit', [RoleController::class, 'edit'])->name('edit')->middleware('permission:update.role');
            Route::get('/update', [RoleController::class, 'update'])->name('update')->middleware('permission:update.role');
            Route::get('/status/{id}', [RoleController::class, 'status'])->name('status')->middleware('permission:changestatus.role');
            Route::get('/delete/{id}', [RoleController::class, 'destroy'])->name('delete')->middleware('permission:delete.role');
        });

        //User
        Route::group(['prefix' => 'user/', 'as' => 'user.'], function () {
            Route::get('list', [UserController::class, 'index'])->name('list')->middleware('permission:user.read');
            Route::get('create', [UserController::class, 'create'])->name('create')->middleware('permission:user.create');
            Route::post('store', [UserController::class, 'store'])->name('store')->middleware('permission:user.create');
            Route::get('edit/{id}', [UserController::class, 'edit'])->name('edit')->middleware('permission:user.update');
            Route::post('update/{id}', [UserController::class, 'update'])->name('update')->middleware('permission:user.update');
            Route::get('status/{id}', [UserController::class, 'status'])->name('status')->middleware('permission:user.status');
            Route::get('delete/{id}', [UserController::class, 'destroy'])->name('delete')->middleware('permission:user.delete');
            Route::get('change_password_modal_view/{id}', [UserController::class, 'changePasswordModalView'])->name('change_password_modal')->middleware('permission:user.update');
            Route::post('save_password', [UserController::class, 'savePassword'])->name('save_password')->middleware('permission:user.update');
        });

        Route::group(['prefix' => 'user-role/', 'as' => 'user_role.'], function () {
            Route::get('modal-view-user-shop-role/{id}', [UserRoleController::class, 'assignRoleView']);
            Route::post('save_user_shop_role', [UserRoleController::class, 'assignRolesave'])->name('save_user_shop_role');
            // 
            Route::get('change-user-current-shop', [UserRoleController::class, 'changeUserCurrentShopView']);
            Route::post('change-user-current-shop-save', [UserRoleController::class, 'changeUserCurrentShopSave'])->name('change_user_current_shop_save');
        });
    });

/*
    Route::group(['prefix' => 'product', 'as' => 'product.'], function () {

        Route::get('/list', [ProductController::class, 'index'])->name('list')->middleware('permission:product.read');
        Route::get('/create', [ProductController::class, 'create'])->name('create')->middleware('permission:product.create');
        Route::post('store', [ProductController::class, 'store'])->name('store')->middleware('permission:product.create');
        Route::get('/edit/{id}', [ProductController::class, 'edit'])->name('edit')->middleware('permission:product.update');
        Route::post('/update/{id}', [ProductController::class, 'update'])->name('update')->middleware('permission:product.update');
        Route::get('/status/{id}', [ProductController::class, 'status'])->name('status')->middleware('permission:product.status');
        Route::get('/delete/{id}', [ProductController::class, 'destroy'])->name('delete')->middleware('permission:product.delete');
        Route::get('/view/{id}', [ProductController::class, 'show'])->name('view')->middleware('permission:product.read');

        Route::get('/ajax/category/sub_category', [ProductController::class, 'getSubCategories'])->name('ajax_sub_cat');
        Route::get('/ajax/category/product_category', [ProductController::class, 'getProductCategories'])->name('ajax_product_cat');
        Route::get('/ajax/attribute/product_attribute', [ProductController::class, 'getProductAttribute'])->name('ajax_product_attribute');
        Route::get('/ajax/ajax_product_by_product_category', [ProductController::class, 'getProductByProductCategory'])->name('ajax_product_by_product_category');
        Route::get('/ajax/category/get_product_cat_detail', [ProductController::class, 'getProductCategoryDetail'])->name('get_product_cat_detail');
        Route::get('/ajax/products', [ProductController::class, 'getProducts'])->name('ajax_products');
    });
*/

Route::group(['prefix' => 'post', 'as' => 'post.'], function () {

    Route::get('/search-image', [PostController::class, 'searchImage'])->name('search_image'); 

    Route::group(['prefix' => 'logo', 'as' => 'logo.'], function () {
        Route::get('/list', [PostController::class, 'index'])->name('list'); //->middleware('permission:product.read');
        Route::get('/create', [PostController::class, 'create'])->name('create'); //->middleware('permission:product.create');
        Route::post('store', [PostController::class, 'store'])->name('store'); //->middleware('permission:product.create');
        Route::get('/edit/{id}', [PostController::class, 'edit'])->name('edit'); //->middleware('permission:product.update');
        Route::post('/update/{id}', [PostController::class, 'update'])->name('update'); //->middleware('permission:product.update');
        Route::get('/status/{id}', [PostController::class, 'status'])->name('status'); //->middleware('permission:product.status');
        Route::get('/delete/{id}', [PostController::class, 'destroy'])->name('delete'); //->middleware('permission:product.delete');
        Route::get('/view/{id}', [PostController::class, 'show'])->name('view'); //->middleware('permission:product.read');
        Route::get('/data-import', [PostController::class, 'dataImport'])->name('data_import');
        Route::post('/data-import', [PostController::class, 'dataImport'])->name('data_import_post');
    });

    

});



Route::group(['prefix' => 'media', 'as' => 'media.'], function () {
    Route::get('/list', [MediaController::class, 'index'])->name('list'); //->middleware('permission:product.read');
    Route::get('/create', [MediaController::class, 'create'])->name('create'); //->middleware('permission:product.create');
    Route::post('store', [MediaController::class, 'store'])->name('store'); //->middleware('permission:product.create');
    Route::get('/edit/{id}', [MediaController::class, 'edit'])->name('edit'); //->middleware('permission:product.update');
    Route::post('/update/{id}', [MediaController::class, 'update'])->name('update'); //->middleware('permission:product.update');
    Route::get('/status/{id}', [MediaController::class, 'status'])->name('status'); //->middleware('permission:product.status');
    Route::get('/delete/{id}', [MediaController::class, 'destroy'])->name('delete'); //->middleware('permission:product.delete');
    Route::get('/view/{id}', [MediaController::class, 'show'])->name('view'); //->middleware('permission:product.read');
    Route::get('/data-import', [MediaController::class, 'dataImport'])->name('data_import');
    Route::post('/data-import', [MediaController::class, 'dataImport'])->name('data_import_post');
});



Route::group(['prefix' => 'term', 'as' => 'term.'], function () {

    Route::group(['prefix' => 'logo_category', 'as' => 'logo_category.'], function () {
        Route::get('/list', [TermController::class, 'index'])->name('list'); //->middleware('permission:product.read');
        Route::get('/create', [TermController::class, 'create'])->name('create'); //->middleware('permission:product.create');
        Route::post('store', [TermController::class, 'store'])->name('store'); //->middleware('permission:product.create');
        Route::get('/edit/{id}', [TermController::class, 'edit'])->name('edit'); //->middleware('permission:product.update');
        Route::post('/update/{id}', [TermController::class, 'update'])->name('update'); //->middleware('permission:product.update');
        Route::get('/status/{id}', [TermController::class, 'status'])->name('status'); //->middleware('permission:product.status');
        Route::get('/delete/{id}', [TermController::class, 'destroy'])->name('delete'); //->middleware('permission:product.delete');
        Route::get('/view/{id}', [TermController::class, 'show'])->name('view'); //->middleware('permission:product.read');
    });

});





    Route::group(['prefix' => 'Settings', 'as' => 'Settings.'], function () {
        Route::resource('warehouse', WarehouseController::class)->middleware('permission:warehouse.read');
        Route::resource('location', LocationController::class)->middleware('permission:warehouse.read');
        Route::resource('uom', UomController::class)->middleware('permission:warehouse.read');
        Route::get('/uom/status/{id}', [UomController::class, 'changeStatus'])->name('uom.changeStatus');

        Route::group(['prefix' => 'employee', 'as' => 'employee.'], function () {
            Route::get('/list', [EmployeeController::class, 'index'])->name('index')->middleware('permission:employee.read');
            Route::get('/create', [EmployeeController::class, 'create'])->name('create')->middleware('permission:employee.create');
            Route::post('/store', [EmployeeController::class, 'store'])->name('store')->middleware('permission:employee.create');
            Route::get('/edit/{id}', [EmployeeController::class, 'edit'])->name('edit')->middleware('permission:employee.update');
            Route::post('/update/{id}', [EmployeeController::class, 'update'])->name('update')->middleware('permission:employee.update');
            Route::get('/status/{id}', [EmployeeController::class, 'status'])->name('status')->middleware('permission:employee.status');
            Route::get('/delete/{id}', [EmployeeController::class, 'destroy'])->name('delete')->middleware('permission:employee.delete');
            Route::get('/view/{id}', [EmployeeController::class, 'show'])->name('view')->middleware('permission:employee.read');
            Route::get('/search', [EmployeeController::class, 'search'])->name('search')->middleware('permission:employee.read');
            Route::get('/auto_complete', [EmployeeController::class, 'auto_complete'])->name('auto_complete')->middleware('permission:employee.read');
        });

        Route::group(['prefix' => 'Designations', 'as' => 'designations.'], function () {
            Route::get('/list', [DesignationController::class, 'index'])->name('list')->middleware('permission:designation.read');
            Route::get('/create', [DesignationController::class, 'create'])->name('create')->middleware('permission:designation.create');
            Route::post('/store', [DesignationController::class, 'store'])->name('store')->middleware('permission:designation.create');
            Route::get('/edit/{id}', [DesignationController::class, 'edit'])->name('edit')->middleware('permission:designation.update');
            Route::post('/update/{id}', [DesignationController::class, 'update'])->name('update')->middleware('permission:designation.update');
            Route::get('/status/{id}', [DesignationController::class, 'status'])->name('status')->middleware('permission:designation.status');
            Route::get('/delete/{id}', [DesignationController::class, 'destroy'])->name('delete')->middleware('permission:designation.delete');
            Route::get('/view/{id}', [DesignationController::class, 'show'])->name('view')->middleware('permission:designation.read');
        });

        Route::group(['prefix' => 'org', 'as' => 'org.'], function () {
            Route::get('/list', [OrganizationController::class, 'index'])->name('list')->middleware('permission:organization.read');
            Route::get('/create', [OrganizationController::class, 'create'])->name('create')->middleware('permission:organization.read');
            Route::post('/store', [OrganizationController::class, 'store'])->name('store')->middleware('permission:organization.read');
            Route::get('/edit/{id}', [OrganizationController::class, 'edit'])->name('edit')->middleware('permission:organization.read');
            Route::post('/update/{id}', [OrganizationController::class, 'update'])->name('update')->middleware('permission:organization.read');
            Route::get('/status/{id}', [OrganizationController::class, 'status'])->name('status')->middleware('permission:organization.read');
            Route::get('/delete/{id}', [OrganizationController::class, 'destroy'])->name('delete')->middleware('permission:organization.read');
            Route::get('/view/{id}', [OrganizationController::class, 'show'])->name('view')->middleware('permission:organization.read');
        });


        Route::group(['prefix' => 'project', 'as' => 'project.'], function () {
            Route::get('/list', [ProjectController::class, 'index'])->name('list')->middleware('permission:project.read');
            Route::get('/create', [ProjectController::class, 'create'])->name('create')->middleware('permission:project.create');
            Route::post('/store', [ProjectController::class, 'store'])->name('store')->middleware('permission:project.create');
            Route::get('/edit/{id}', [ProjectController::class, 'edit'])->name('edit')->middleware('permission:project.update');
            Route::post('/update/{id}', [ProjectController::class, 'update'])->name('update')->middleware('permission:project.update');
            Route::get('/status/{id}', [ProjectController::class, 'status'])->name('status')->middleware('permission:project.status');
            Route::get('/delete/{id}', [ProjectController::class, 'destroy'])->name('delete')->middleware('permission:project.delete');
            Route::get('/view/{id}', [ProjectController::class, 'show'])->name('view')->middleware('permission:project.read');
            Route::get('/by-available-stock-and-product-cat', [ProjectController::class, 'getProjectByAvailableStockItemAndProductCat'])->name('by-available-stock-and-product-cat');
            Route::get('sync-project-data', [ProjectController::class, 'sync_project_data'])->middleware('permission:project.update');
        });
    });

    Route::group(['prefix' => 'supplier', 'as' => 'supplier.'], function () {
        Route::get('/list', [SupplierController::class, 'index'])->name('list')->middleware('permission:supplier.read');
        Route::get('/create', [SupplierController::class, 'create'])->name('create')->middleware('permission:supplier.create');
        Route::post('/store', [SupplierController::class, 'store'])->name('store')->middleware('permission:supplier.create');
        Route::get('/edit/{id}', [SupplierController::class, 'edit'])->name('edit')->middleware('permission:supplier.update');
        Route::post('/update/{id}', [SupplierController::class, 'update'])->name('update')->middleware('permission:supplier.update');
        Route::get('/status/{id}', [SupplierController::class, 'status'])->name('status')->middleware('permission:supplier.status');
        Route::get('/delete/{id}', [SupplierController::class, 'destroy'])->name('delete')->middleware('permission:supplier.delete');
        Route::get('/view/{id}', [SupplierController::class, 'show'])->name('view')->middleware('permission:supplier.read');
    });


    Route::group(['prefix' => 'user/', 'as' => 'user.'], function () {
        Route::get('list', [UserController::class, 'index'])->name('list')->middleware('permission:user.read');
        Route::get('create', [UserController::class, 'create'])->name('create')->middleware('permission:user.create');
        Route::post('store', [UserController::class, 'store'])->name('store')->middleware('permission:user.create');
        Route::get('edit/{id}', [UserController::class, 'edit'])->name('edit')->middleware('permission:user.update');
        Route::post('update/{id}', [UserController::class, 'update'])->name('update')->middleware('permission:user.update');
        Route::get('status/{id}', [UserController::class, 'status'])->name('status')->middleware('permission:user.status');
        Route::get('delete/{id}', [UserController::class, 'destroy'])->name('delete')->middleware('permission:user.delete');
        Route::get('change_password_modal_view/{id}', [UserController::class, 'changePasswordModalView'])->name('change_password_modal')->middleware('permission:user.update');
        Route::post('save_password', [UserController::class, 'savePassword'])->name('save_password')->middleware('permission:user.update');
    });

    Route::group(['prefix' => 'shop', 'as' => 'shop.'], function () {
        Route::get('/list', [ShopController::class, 'index'])->name('list')->middleware('permission:supplier.read');
        Route::get('/create', [ShopController::class, 'create'])->name('create')->middleware('permission:supplier.create');
        Route::post('/store', [ShopController::class, 'store'])->name('store')->middleware('permission:supplier.create');
        Route::get('/edit/{id}', [ShopController::class, 'edit'])->name('edit')->middleware('permission:supplier.update');
        Route::post('/update/{id}', [ShopController::class, 'update'])->name('update')->middleware('permission:supplier.update');
        Route::get('/status/{id}', [ShopController::class, 'status'])->name('status')->middleware('permission:supplier.status');
        Route::get('/delete/{id}', [ShopController::class, 'destroy'])->name('delete')->middleware('permission:supplier.delete');
        Route::get('/view/{id}', [ShopController::class, 'show'])->name('view')->middleware('permission:supplier.read');
    });





    Route::prefix('suppliers')->group(function () {
        Route::get('datatable', [SupplierController::class, 'getDatatable'])->name('suppliers/datatable');
        Route::get('change-status/{eid}', [SupplierController::class, 'changeStatus']);
    });

    Route::prefix('locations')->group(function () {
        Route::get('datatable', [LocationController::class, 'getDatatable'])->name('locations/datatable');
        Route::get('change-status/{eid}', [LocationController::class, 'changeStatus']);
    });

    Route::prefix('stock')->group(function () {
        Route::get('list', [AddStockController::class, 'list']);
        Route::get('create', [AddStockController::class, 'create']);
    });

    Route::prefix('ajax_view')->group(function () {
        Route::get('location_child', [LocationController::class, 'location_child']);
        Route::get('get_province', [LocationController::class, 'getProvince']);
        Route::get('get_city', [LocationController::class, 'getCity']);
        Route::get('get_child_category', [CategoryController::class, 'get_child_category']);
        Route::get('get_child_category_select', [CategoryController::class, 'get_child_category_select']);
        Route::get('get_manager_by_organization', [ManagerController::class, 'getManagerByOrganization']);
    });

    Route::group(['prefix' => 'Category/', 'as' => 'category.'], function () {

        Route::group(['prefix' => 'Parent/', 'as' => 'parent.'], function () {

            Route::get('list', [ParentCategoryController::class, 'list'])->name('list');
            Route::get('create', [ParentCategoryController::class, 'create'])->name('create');
            Route::post('store', [ParentCategoryController::class, 'store'])->name('store');
            Route::get('edit/{id}', [ParentCategoryController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [ParentCategoryController::class, 'update'])->name('update');
            Route::get('status/{id}', [ParentCategoryController::class, 'status'])->name('status');
            Route::get('delete/{id}', [ParentCategoryController::class, 'destroy'])->name('destroy');
            Route::get('view/{id}', [ParentCategoryController::class, 'viewDetail'])->name('viewDetail');
        });

        Route::group(['prefix' => 'Sub/', 'as' => 'sub.'], function () {

            Route::get('list', [SubCategoryController::class, 'list'])->name('list');
            Route::get('create', [SubCategoryController::class, 'create'])->name('create');
            Route::post('store', [SubCategoryController::class, 'store'])->name('store');
            Route::get('edit/{id}', [SubCategoryController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [SubCategoryController::class, 'update'])->name('update');
            Route::get('status/{id}', [SubCategoryController::class, 'status'])->name('status');
            Route::get('delete/{id}', [SubCategoryController::class, 'destroy'])->name('destroy');
            Route::get('view/{id}', [SubCategoryController::class, 'viewDetail'])->name('viewDetail');
            Route::get('getSpecificSubCategories', [SubCategoryController::class, 'getSubCategoryByParentId'])->name('getSubCategoryByParentId'); //Ajax call


        });

        Route::group(['prefix' => 'Product/', 'as' => 'product.'], function () {

            Route::get('list', [ProductCategoryController::class, 'list'])->name('list');
            Route::get('create', [ProductCategoryController::class, 'create'])->name('create');
            Route::post('store', [ProductCategoryController::class, 'store'])->name('store');
            Route::get('edit/{id}', [ProductCategoryController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [ProductCategoryController::class, 'update'])->name('update');
            Route::get('status/{id}', [ProductCategoryController::class, 'status'])->name('status');
            Route::get('delete/{id}', [ProductCategoryController::class, 'destroy'])->name('destroy');
            Route::get('view/{id}', [ProductCategoryController::class, 'viewDetail'])->name('viewDetail');

            Route::get('/ajax/products', [ProductController::class, 'getProducts'])->name('ajax_products');
        });
    });




  







   










    Route::group(['prefix' => 'report/', 'as' => 'report.'], function () {
        Route::group(['prefix' => 'sale/', 'as' => 'sale.'], function () {
            Route::get('all', [SaleReportController::class, 'all'])->name('all');
        });

        // OLD
        Route::get('product', [ProductStockController::class, 'product'])->name('product');
        Route::get('product_wise', [ProductStockController::class, 'productWise'])->name('product_wise');
        Route::get('product_order_table', [ProductStockController::class, 'saleOrder'])->name('product_order_table');
        Route::get('product_purchase_table', [ProductStockController::class, 'purchase'])->name('product_purchase_table');
    });



    Route::group(['prefix' => 'report/', 'as' => 'report.'], function () {
        Route::group(['prefix' => 'sale/', 'as' => 'sale.'], function () {
            Route::get('all', [SaleReportController::class, 'all'])->name('all');
        });
    });
}); // main auth end
