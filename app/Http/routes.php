<?php



/*

|--------------------------------------------------------------------------

| Application Routes

|--------------------------------------------------------------------------

|

| Here is where you can register all of the routes for an application.

| It's a breeze. Simply tell Laravel the URIs it should respond to

| and give it the controller to call when that URI is requested.

|

*/



Route::get('/clearCache', function()

{

    Cache::flush();

    return Redirect::to('/');

});



Route::get('/admin/clearCache', function(){

    Cache::flush();

    return Redirect::to('/admin');

});





Route::get('/', function () {

    return view('admin.index');

});





Route::get('/admin', array('as' => 'admin', 'uses' => 'LoginController@index'));



/*Admin Panel*/

Route::group(array('namespace'=>'admin'), function(){



    /**********************LoginController***********************/

    Route::get('/admin/', array('as' => 'admin', 'uses' => 'LoginController@index'));



    Route::get('/admin/index', array('as' => 'admin', 'uses' => 'LoginController@index'));

	

    Route::get('/admin/404', array('as' => 'admin', 'uses' => 'LoginController@errorpage'));

	

    Route::get('/admin/login', array('as' => 'admin', 'uses' => 'LoginController@index'));

	

    Route::get('/admin/logout', array('as' => 'admin', 'uses' => 'LoginController@logout'));



	Route::get('/admin/dashboard', array('as' => 'admin', 'uses' => 'LoginController@dashboard'));

	

	Route::post('/admin/loginform/', array('as' => 'admin', 'uses' => 'LoginController@loginform'));

	

	Route::post('/admin/forgotPasswordAdmin/', array('as' => 'admin', 'uses' => 'LoginController@forgotPasswordAdmin'));



	/************ProfileController****************/

	

    Route::get('/admin/profile', array('as' => 'admin', 'uses' => 'ProfileController@profile'));

	

    Route::post('/admin/profileUpdate', array('as' => 'admin', 'uses' => 'ProfileController@profileUpdate'));

	

    Route::post('/admin/updateAdminAvatar', array('as' => 'admin', 'uses' => 'ProfileController@updateAdminAvatar'));

	

    Route::post('/admin/passwordUpdate', array('as' => 'admin', 'uses' => 'ProfileController@passwordUpdate'));

	

	/************ProductController****************/



    Route::get('/admin/brandList', array('as' => 'admin', 'uses' => 'ProductController@brandList'));



    Route::post('/admin/addBrand', array('as' => 'admin', 'uses' => 'ProductController@addBrand'));

	

    Route::get('/admin/editBrand/{id}', array('as' => 'admin', 'uses' => 'ProductController@editBrand'));

	

    Route::post('/admin/updateBrand', array('as' => 'admin', 'uses' => 'ProductController@updateBrand'));

	

    Route::get('/admin/deleteBrand/{id}', array('as' => 'admin', 'uses' => 'ProductController@deleteBrand'));

	

    Route::get('/admin/productCategoriesList', array('as' => 'admin', 'uses' => 'ProductController@productCategoriesList'));

	

    Route::post('/admin/productCategoriesAdd', array('as' => 'admin', 'uses' => 'ProductController@productCategoriesAdd'));

	
    Route::get('/admin/editCategory/{id}', array('as' => 'admin', 'uses' => 'ProductController@editCategory'));

	

    Route::get('/admin/deleteCategory/{id}', array('as' => 'admin', 'uses' => 'ProductController@deleteCategory'));

	

    Route::post('/admin/updateCategory', array('as' => 'admin', 'uses' => 'ProductController@updateCategory'));

	

    Route::get('/admin/productList', array('as' => 'admin', 'uses' => 'ProductController@productList'));

	

    Route::get('/admin/productDetail', array('as' => 'admin', 'uses' => 'ProductController@productDetail'));

	

    Route::get('/admin/editProducts/{id}', array('as' => 'admin', 'uses' => 'ProductController@editProducts'));

	

    Route::get('/admin/addProducts', array('as' => 'admin', 'uses' => 'ProductController@addProducts'));

	

    Route::post('/admin/addProductsDB', array('as' => 'admin', 'uses' => 'ProductController@addProductsDB'));

	

    Route::post('/admin/updateProductsinfo', array('as' => 'admin', 'uses' => 'ProductController@updateProductsinfo'));

	

    Route::post('/admin/updateVariation', array('as' => 'admin', 'uses' => 'ProductController@updateVariation'));

	

    Route::post('/admin/updateProductimage', array('as' => 'admin', 'uses' => 'ProductController@updateProductimage'));

	

    Route::post('/admin/addGroupToProducts', array('as' => 'admin', 'uses' => 'ProductController@addGroupToProducts'));

	

    Route::post('/admin/addProductsVariation', array('as' => 'admin', 'uses' => 'ProductController@addProductsVariation'));

   

   Route::get('/admin/editvariation/{id}', array('as' => 'admin', 'uses' => 'ProductController@editvariation'));



   Route::get('/admin/deletevariation/{id}', array('as' => 'admin', 'uses' => 'ProductController@deletevariation'));

   

   Route::get('/admin/deleteProducts/{id}', array('as' => 'admin', 'uses' => 'ProductController@deleteProducts'));

   

   Route::get('/admin/deleteatributes/{id}', array('as' => 'admin', 'uses' => 'ProductController@deleteAtributes'));

   

   Route::post('/admin/addProductsAttributes', array('as' => 'admin', 'uses' => 'ProductController@addproductsattributes'));

   

   Route::post('/admin/addproductsvariationdirect', array('as' => 'admin', 'uses' => 'ProductController@addProductsVariationDirect'));

   

    Route::get('/admin/addvariation', array('as' => 'admin', 'uses' => 'ProductController@addVariation'));

	

	/***************Accesoory Controller*************************/

	

    Route::get('/admin/accessorycategorieslist', array('as' => 'admin', 'uses' => 'AccessoryController@accessoryCategoriesList'));

	

    Route::post('/admin/accessorycategoriesadd', array('as' => 'admin', 'uses' => 'AccessoryController@accessoryCategoriesAdd'));

	

    Route::get('/admin/accessoryeditcategory/{id}', array('as' => 'admin', 'uses' => 'AccessoryController@accessoryEditCategory'));

	

    Route::get('/admin/accessorydeletecategory/{id}', array('as' => 'admin', 'uses' => 'AccessoryController@accessoryDeleteCategory'));

	

    Route::post('/admin/accessoryupdatecategory', array('as' => 'admin', 'uses' => 'AccessoryController@accessoryUpdateCategory'));

	

	

    Route::get('/admin/accessorylist', array('as' => 'admin', 'uses' => 'AccessoryController@accessoryList'));

	

    Route::get('/admin/addaccessory', array('as' => 'admin', 'uses' => 'AccessoryController@addAccessory'));

	

    Route::post('/admin/saveaccesory', array('as' => 'admin', 'uses' => 'AccessoryController@addAccessoryDB'));

	

	Route::get('/admin/editaccessory/{id}', array('as' => 'admin', 'uses' => 'AccessoryController@editAccessory'));

	

	Route::post('/admin/updateaccessory', array('as' => 'admin', 'uses' => 'AccessoryController@updateAccessory'));

	

	/***************AttributeController*************************/

	Route::get('/admin/attributeList/', array('as' => 'admin', 'uses' => 'AttributeController@index'));

	

	Route::get('/admin/addAttribute/', array('as' => 'admin', 'uses' => 'AttributeController@addattribute'));

	

	Route::post('/admin/saveAttribute/', array('as' => 'admin', 'uses' => 'AttributeController@saveData'));

	

	Route::get('/admin/editAttribute/{id}', array('as' => 'admin', 'uses' => 'AttributeController@editAttribute'));

	

	Route::post('/admin/editData/', array('as' => 'admin', 'uses' => 'AttributeController@editData'));

	 

	/***************invoice*************************/

	Route::post('/admin/generateinvoice/', array('as' => 'admin', 'uses' => 'OrderController@generateInvoice'));
	
	Route::post('/admin/paidinvoice/', array('as' => 'admin', 'uses' => 'OrderController@paidInvoice'));
	
	Route::post('/admin/getallinvoice/', array('as' => 'admin', 'uses' => 'OrderController@getAllInvoice'));
	
	Route::get('/admin/pastpaidorder/', array('as' => 'admin', 'uses' => 'OrderController@pastPaidOrderAutoComplete'));

	/***************invoice*************************/

	

	

	/***************OrderController*************************/

	

    Route::get('/admin/orderList', array('as' => 'admin', 'uses' => 'OrderController@orderList'));

	

    Route::get('/admin/orderDetials/{id}', array('as' => 'admin', 'uses' => 'OrderController@orderDetials'));

	

    Route::get('/admin/orderPrint/{id}', array('as' => 'admin', 'uses' => 'OrderController@orderPrint'));

	

    Route::get('/admin/adminEditOrder/{id}', array('as' => 'admin', 'uses' => 'OrderController@admineditorder'));

	

    Route::post('/admin/adminUpdateOrder/', array('as' => 'admin', 'uses' => 'OrderController@adminupdateorder'));

	

    Route::get('/admin/adminOrderInvoice/{id}', array('as' => 'admin', 'uses' => 'OrderController@adminorderinvoice'));

	

    Route::get('/admin/adminorderinvoiceedit/{id}', array('as' => 'admin', 'uses' => 'OrderController@adminOrderInvoiceEdit'));

	

    Route::post('/admin/adminorderinvoiceupdate/', array('as' => 'admin', 'uses' => 'OrderController@adminOrderInvoiceUpdate'));

	

    Route::post('/admin/bookedfordeliveryorder/', array('as' => 'admin', 'uses' => 'OrderController@bookedForDeliveryOrder'));
	
    
	Route::get('/admin/invoicelist/', array('as' => 'admin', 'uses' => 'OrderController@invoiceList'));
	
	Route::get('/admin/createinvoice/', array('as' => 'admin', 'uses' => 'OrderController@createInvoice'));
	
	Route::get('/admin/orderdeliveryrotalist/', array('as' => 'admin', 'uses' => 'OrderController@orderDeliveryRotaList'));
	
	Route::get('/admin/orderdeliveryrotaedit/', array('as' => 'admin', 'uses' => 'OrderController@orderDeliveryRotaEdit'));
	
	Route::get('/admin/orderdeliveryvehicle/', array('as' => 'admin', 'uses' => 'OrderController@orderDeliveryVehicle')); 
	
	Route::get('/admin/vehicleedit/{id}', array('as' => 'admin', 'uses' => 'OrderController@deliveryVehicleEditData'));
	
	Route::post('/admin/editabledataget', array('as' => 'admin', 'uses' => 'OrderController@editableDataPostGetData'));
	
	Route::post('/admin/deliveryrotaupdate', array('as' => 'admin', 'uses' => 'OrderController@deliveryRotaUpdate'));
	
	Route::post('/admin/deliveryvehicleupdate', array('as' => 'admin', 'uses' => 'OrderController@deliveryVehicleDataSave'));
	
	Route::post('/admin/deletevehicle', array('as' => 'admin', 'uses' => 'OrderController@deleteVehicle'));
	
	Route::post('/admin/deliveryvehicledata', array('as' => 'admin', 'uses' => 'OrderController@deliveryVehicleData'));
	
	Route::get('/admin/adminorderlistrota/', array('as' => 'admin', 'uses' => 'OrderController@adminOrderListRota'));
	
	Route::get('/admin/createserviceinvoice/', array('as' => 'admin', 'uses' => 'OrderController@createServiceInvoice'));
	
	Route::get('/admin/deliveryrotamap/', array('as' => 'admin', 'uses' => 'OrderController@deliveryRotaMap'));
	
	Route::post('/admin/generateserviceinvoicedata/', array('as' => 'admin', 'uses' => 'OrderController@generateServiceInvoiceData'));
	
	Route::get('/admin/maprotadeliverylocation/', array('as' => 'admin', 'uses' => 'OrderController@mapGeoGoogleDataGet'));
	
	Route::post('/admin/editabledatapost/', array('as' => 'admin', 'uses' => 'OrderController@editableDataPostSave'));
	
	Route::post('/admin/generateserviceinvoicedataupdate/', array('as' => 'admin', 'uses' => 'OrderController@generateServiceInvoiceDataUpdate'));



    Route::get('/admin/eventCalender/{id}', array('as' => 'admin', 'uses' => 'CalendarController@eventCalender'));

	

    Route::get('/admin/financeorder/', array('as' => 'admin', 'uses' => 'OrderController@financeOrder'));

	

    Route::get('/admin/financeorderdetail/{id}', array('as' => 'admin', 'uses' => 'OrderController@financeOrderDetail'));

	

    Route::post('/admin/addordernotes/', array('as' => 'admin', 'uses' => 'OrderController@addOrderNotes'));

	

    Route::post('/admin/inproductionorder/', array('as' => 'admin', 'uses' => 'OrderController@inProductionOrder'));

   

    Route::post('/admin/addspecialordernotes/', array('as' => 'admin', 'uses' => 'ManageSpecialOrderController@addSpecialOrdernotes'));

	

	Route::post('/admin/addadmincustomername/', array('as' => 'admin', 'uses' => 'OrderController@addAdminCustomerName'));

	

	Route::post('/admin/swaporder/', array('as' => 'admin', 'uses' => 'OrderController@swapOrder'));

	

	Route::post('/admin/instockorderdate/', array('as' => 'admin', 'uses' => 'OrderController@inStockOrderDate'));

	

	Route::get('/admin/accessoryorderslist/', array('as' => 'admin', 'uses' => 'AccessoryOrderController@adminAccessoryOrderList'));

	

	Route::get('/admin/accessoryordersdetail/{id}', array('as' => 'admin', 'uses' => 'AccessoryOrderController@adminAccessoryOrderdetail'));

	

	Route::post('/admin/adminupdateaccesoryorder/', array('as' => 'admin', 'uses' => 'AccessoryOrderController@adminUpdateAccesoryOrder'));

	

	Route::post('/admin/admindeleteaccesoryorder/', array('as' => 'admin', 'uses' => 'AccessoryOrderController@adminDeleteAccesoryOrder'));

	

	Route::get('/admin/completedorders/', array('as' => 'admin', 'uses' => 'OrderController@completedOrders'));
	
	Route::get('/admin/adminorderlistpending/', array('as' => 'admin', 'uses' => 'OrderController@adminorderlistPending'));
	
	Route::get('/admin/adminorderlistbooked/', array('as' => 'admin', 'uses' => 'OrderController@adminOrderlistBooked'));
	
	Route::get('/admin/adminorderlistcompleteordersingle/', array('as' => 'admin', 'uses' => 'OrderController@adminOrderListCompleteOrderSingle'));
	
	Route::get('/admin/adminorderlistinvoiced/', array('as' => 'admin', 'uses' => 'OrderController@adminOrderListInvoiced'));
	
	Route::get('/admin/adminorderlistpaid/', array('as' => 'admin', 'uses' => 'OrderController@adminOrderListPaid'));
	
	Route::get('/admin/adminorderlistcomplete/', array('as' => 'admin', 'uses' => 'OrderController@adminOrderListComplete'));
	
	Route::post('/admin/ordernoteslistadmin/', array('as' => 'admin', 'uses' => 'OrderController@orderNotesListAdmin'));
	
	Route::post('/admin/adminorderlistinvoicedpopup/', array('as' => 'admin', 'uses' => 'OrderController@adminOrderListInvoicedPopup'));

	







    /***************Stock System -- start *************************/

	 Route::get('/admin/stockManage', array('as' => 'admin', 'uses' => 'NewStockController@index'));

	 

	 Route::post('/admin/updateStockd', array('as' => 'admin', 'uses' => 'NewStockController@updatestock'));

	 

	 Route::post('/admin/updateStock', array('as' => 'admin', 'uses' => 'NewStockController@updatestock'));

	 

	 Route::post('/admin/updateinproductionstock', array('as' => 'admin', 'uses' => 'NewStockController@updateInProductionStock'));

	 

	 Route::post('/admin/updateproductiondate', array('as' => 'admin', 'uses' => 'NewStockController@updateProductionDate'));

	 

	 Route::get('/admin/inproductionstockmanage', array('as' => 'admin', 'uses' => 'NewStockController@inProductionStockManage'));

	 

	 Route::get('/admin/inseaarrivalukstockmanage', array('as' => 'admin', 'uses' => 'NewStockController@inSeaArrivalUKStockManage'));

	 Route::get('/admin/factorystockmanage', array('as' => 'admin', 'uses' => 'NewStockController@factoryStockManage'));

	 

	 Route::get('/admin/batchwisestockmanage', array('as' => 'admin', 'uses' => 'NewStockController@batchWiseStockManage'));

	

    /***************Stock System -- end **************************/

    /***************Group System -- Jignesh *************************/



    Route::get('/admin/grouplist', array('as' => 'admin', 'uses' => 'GroupController@index'));



    Route::get('/admin/groupadd', array('as' => 'admin', 'uses' => 'GroupController@addGroup'));



    Route::post('/admin/groupSave', array('as' => 'admin', 'uses' => 'GroupController@saveData'));



    Route::get('/admin/edit-group/{groupID}', array('as' => 'admin', 'uses' => 'GroupController@editGroup'));



    Route::post('/admin/groupedit', array('as' => 'admin', 'uses' => 'GroupController@editData'));





    /***************Ends Group System*************************/





    /***************Dealer System -- Jignesh *************************/



    Route::get('/admin/dealerlist', array('as' => 'admin', 'uses' => 'ManageDealerController@index'));



    Route::get('/admin/dealeradd', array('as' => 'admin', 'uses' => 'ManageDealerController@addDealer'));



    Route::post('/admin/dealerSave', array('as' => 'admin', 'uses' => 'ManageDealerController@saveData'));



    Route::get('/admin/edit-dealer/{id}', array('as' => 'admin', 'uses' => 'ManageDealerController@editDealer'));



    Route::post('/admin/dealeredit', array('as' => 'admin', 'uses' => 'ManageDealerController@editData'));

	

    Route::get('/admin/dealerOrderList/{id}', array('as' => 'admin', 'uses' => 'ManageDealerController@dealerorderlist'));

	

	Route::get('/admin/specialorderslist/', array('as' => 'admin', 'uses' => 'ManageSpecialOrderController@index'));



	Route::get('/admin/specialorderdetail/{id}', array('as' => 'admin', 'uses' => 'ManageSpecialOrderController@specialOrderDetail'));





    Route::get('/admin/password-dealer/{id}', array('as' => 'admin', 'uses' => 'ManageDealerController@passwordDealer'));



    Route::post('/admin/dealerpassword', array('as' => 'admin', 'uses' => 'ManageDealerController@updatePassworddata'));

	

	Route::post('/admin/adminupdatespecialorder', array('as' => 'admin', 'uses' => 'ManageSpecialOrderController@adminUpdateSpecialOrder'));



    Route::get('/admin/login-dealer/{id}', array('as' => 'admin', 'uses' => 'ManageDealerController@loginDealer'));

	





    /***************Ends Dealer  System*************************/

	

	

	/*************** Staff System*************************/

    Route::get('/admin/stafflist', array('as' => 'admin', 'uses' => 'ManageStaffController@index'));



    Route::get('/admin/staffadd', array('as' => 'admin', 'uses' => 'ManageStaffController@addStaff'));



    Route::post('/admin/staffSave', array('as' => 'admin', 'uses' => 'ManageStaffController@saveData'));



    Route::get('/admin/edit-staff/{id}', array('as' => 'admin', 'uses' => 'ManageStaffController@editStaff'));



    Route::post('/admin/staffedit', array('as' => 'admin', 'uses' => 'ManageStaffController@editData'));



    Route::get('/admin/password-staff/{id}', array('as' => 'admin', 'uses' => 'ManageStaffController@passwordStaff'));



    Route::post('/admin/staffpassword', array('as' => 'admin', 'uses' => 'ManageStaffController@updatePassworddata'));

	



    /***************Ends Staff System*************************/

	 Route::post('admin/getProductsOrder', array('as' => 'admin', 'uses' => 'NewStockController@getproductsorder'));



	 /***************Customer System -- Jignesh *************************/



    Route::get('/admin/customerlist', array('as' => 'admin', 'uses' => 'ManageCustomerController@index'));



    Route::get('/admin/customeradd', array('as' => 'admin', 'uses' => 'ManageCustomerController@addCustomer'));



    Route::post('/admin/customerSave', array('as' => 'admin', 'uses' => 'ManageCustomerController@saveData'));



    Route::get('/admin/edit-customer/{id}', array('as' => 'admin', 'uses' => 'ManageCustomerController@editCustomer'));



    Route::post('/admin/customeredit', array('as' => 'admin', 'uses' => 'ManageCustomerController@editData'));



    Route::get('/admin/password-customer/{id}', array('as' => 'admin', 'uses' => 'ManageCustomerController@passwordCustomer'));



    Route::post('/admin/customerpassword', array('as' => 'admin', 'uses' => 'ManageCustomerController@updatePassworddata'));



    /***************Ends Customer  System*************************/



	  /*Admin Warranty*/



    Route::get('/admin/warranty', array('as' => 'admin', 'uses' => 'WarrantyAdminController@index'));



    Route::get('/admin/warrantyadd', array('as' => 'admin', 'uses' => 'WarrantyAdminController@warrantyAdd'));



    Route::post('/admin/warrantysave', array('as' => 'admin', 'uses' => 'WarrantyAdminController@warrantySaveData'));



    Route::post('/admin/warrantyfile', array('as' => 'admin', 'uses' => 'WarrantyAdminController@fileupload'));



    Route::post('/admin/warrantyremove', array('as' => 'admin', 'uses' => 'WarrantyAdminController@fileRemove'));



    Route::get('/admin/warrantyedit/{editid}', array('as' => 'admin', 'uses' => 'WarrantyAdminController@warrantyEdit'));



    Route::post('admin/warrantygetimages', array('as' => 'admin', 'uses' => 'WarrantyAdminController@getFileWarranty'));



    Route::get('admin/warrantypdf/{id}', array('as' => 'admin', 'uses' => 'WarrantyAdminController@warrantyPDF'));



    Route::get('admin/warrantyprint/{id}', array('as' => 'admin', 'uses' => 'WarrantyAdminController@warrantyPrint'));



    /*Media Category*/

    Route::get('/admin/mediacategorylist', array('as' => 'admin', 'uses' => 'MediaCategoryController@index'));



    Route::get('/admin/mediacategoryadd', array('as' => 'admin', 'uses' => 'MediaCategoryController@addMediaCategory'));



    Route::post('/admin/mediacategorySave', array('as' => 'admin', 'uses' => 'MediaCategoryController@saveData'));



    Route::get('/admin/edit-mediacategory/{ID}', array('as' => 'admin', 'uses' => 'MediaCategoryController@editMediaCategory'));



    Route::post('/admin/mediacategoryedit', array('as' => 'admin', 'uses' => 'MediaCategoryController@editData'));



    Route::post('/admin/getcategoryimages', array('as' => 'admin', 'uses' => 'MediaCategoryController@getFileCategory'));



    /*Media Sub Category*/

    Route::get('/admin/mediacategorysublist', array('as' => 'admin', 'uses' => 'MediaSubCategoryController@index'));



    Route::get('/admin/mediasubcategoryadd', array('as' => 'admin', 'uses' => 'MediaSubCategoryController@addMediaSubCategory'));



    Route::post('/admin/mediasubcategorySave', array('as' => 'admin', 'uses' => 'MediaSubCategoryController@saveData'));



    Route::get('/admin/edit-mediasubcategory/{ID}', array('as' => 'admin', 'uses' => 'MediaSubCategoryController@editMediaSubCategory'));



    Route::post('/admin/mediasubcategoryedit', array('as' => 'admin', 'uses' => 'MediaSubCategoryController@editData'));





    /*Media File*/

    Route::get('/admin/mediacategoryfile', array('as' => 'admin', 'uses' => 'MediaCategoryFileController@index'));



    Route::get('/admin/mediacategoryfilelist/{id}', array('as' => 'admin', 'uses' => 'MediaCategoryFileController@mediaList'));



    Route::get('/admin/mediacategoryfileadd', array('as' => 'admin', 'uses' => 'MediaCategoryFileController@medialFileAdd'));



    Route::post('/admin/mediafileadd', array('as' => 'admin', 'uses' => 'MediaCategoryFileController@fileupload'));



    Route::post('/admin/mediafileremove', array('as' => 'admin', 'uses' => 'MediaCategoryFileController@fileRemove'));



    Route::post('/admin/mediafileSave', array('as' => 'admin', 'uses' => 'MediaCategoryFileController@saveData'));



    /*************** Employee System*************************/

    Route::get('/admin/employeelist', array('as' => 'admin', 'uses' => 'ManageEmployeeController@index'));



    Route::get('/admin/employeeadd', array('as' => 'admin', 'uses' => 'ManageEmployeeController@addEmployee'));



    Route::post('/admin/employeeSave', array('as' => 'admin', 'uses' => 'ManageEmployeeController@saveData'));



    Route::get('/admin/edit-employee/{id}', array('as' => 'admin', 'uses' => 'ManageEmployeeController@editEmployee'));



    Route::post('/admin/employeeedit', array('as' => 'admin', 'uses' => 'ManageEmployeeController@editData'));



    Route::get('/admin/password-employee/{id}', array('as' => 'admin', 'uses' => 'ManageEmployeeController@passwordEmployee'));



    Route::post('/admin/employeepassword', array('as' => 'admin', 'uses' => 'ManageEmployeeController@updatePassworddata'));

	

    /*************** Ajax *************************/

		Route::group(array('prefix'=>'admin/ajax'),function(){



        Route::get('log/groupdata', array('as' => 'admin', 'uses' => 'GroupController@dataGroup'));

		

        Route::get('log/attributedata', array('as' => 'admin', 'uses' => 'AttributeController@dataattribute'));



        Route::post('log/groupdelete', array('as' => 'admin', 'uses' => 'GroupController@deleteData'));

		

        Route::post('log/attributedelete', array('as' => 'admin', 'uses' => 'AttributeController@deleteData'));



        Route::get('log/dealerdata', array('as' => 'admin', 'uses' => 'ManageDealerController@dataDealer'));



        Route::get('log/variationdata/', array('as' => 'admin', 'uses' => 'ProductController@variationdata'));



        Route::get('log/productdatalist', array('as' => 'admin', 'uses' => 'ProductController@productdatalist'));

		

        Route::get('log/stockproductdatalist', array('as' => 'admin', 'uses' => 'NewStockController@stockproductdatalist'));

		

        Route::post('log/stockproductdatalistfilter', array('as' => 'admin', 'uses' => 'NewStockController@stockproductdatalistfilter'));

		

        Route::post('log/getProducts', array('as' => 'admin', 'uses' => 'NewStockController@getproducts'));

		

        Route::post('log/getProductsColor', array('as' => 'admin', 'uses' => 'NewStockController@getproductscolor'));

		      		

        Route::get('log/dealerorderlist', array('as' => 'admin', 'uses' => 'OrderController@dealerOrderList'));

		

        Route::get('log/adminOrderList', array('as' => 'admin', 'uses' => 'OrderController@adminorderlist'));

		

        Route::get('log/adminDealerOrderList/', array('as' => 'admin', 'uses' => 'OrderController@admindealerorderlist'));

		

		Route::get('log/specialdata', array('as' => 'admin', 'uses' => 'ManageSpecialOrderController@dataSpecialOrder'));

		

		Route::post('log/specialorderdelete', array('as' => 'admin', 'uses' => 'ManageSpecialOrderController@specialOrderDelete'));



        Route::get('log/staffdata', array('as' => 'admin', 'uses' => 'ManageStaffController@dataStaff'));



        Route::post('log/staffdelete', array('as' => 'admin', 'uses' => 'ManageStaffController@deleteData'));

		

        Route::post('log/deleteaminorder', array('as' => 'admin', 'uses' => 'OrderController@deleteaminorder'));

		

        Route::post('log/deleteamininvoice', array('as' => 'admin', 'uses' => 'OrderController@deleteAminInvoice'));

		

        Route::post('log/deleteaminpaid', array('as' => 'admin', 'uses' => 'OrderController@deleteAminPaid'));

		

        Route::post('log/getproductscoloreditorder', array('as' => 'admin', 'uses' => 'OrderController@getProductsColorEditOrder'));

		

        Route::post('log/getproductsstatuseditorder', array('as' => 'admin', 'uses' => 'OrderController@getProductsStatusEditOrder'));

		

        Route::post('log/getproductsbatcheditorder', array('as' => 'admin', 'uses' => 'OrderController@getProductsBatchEditOrder'));

		

        Route::post('log/deleteaccessory', array('as' => 'admin', 'uses' => 'ProductController@deleteAccessory'));

		

        Route::post('log/getproductscoloreditspeacilorder', array('as' => 'admin', 'uses' => 'ManageSpecialOrderController@getProductsColorEditSpeacilOrder'));

		

		 /* New Module Warranty and Media Category*/



        Route::get('log/customerdata', array('as' => 'admin', 'uses' => 'ManageCustomerController@dataCustomer'));



        Route::post('log/customerdelete', array('as' => 'admin', 'uses' => 'ManageCustomerController@deleteData'));



        Route::post('log/getusers', array('as' => 'admin', 'uses' => 'WarrantyAdminController@getUsers'));



        Route::post('log/warrantynote', array('as' => 'admin', 'uses' => 'WarrantyAdminController@noteDataForm'));



        Route::post('log/addwarrantynote', array('as' => 'admin', 'uses' => 'WarrantyAdminController@noteAdd'));



        Route::post('log/warrantydelete', array('as' => 'admin', 'uses' => 'WarrantyAdminController@deleteData'));


		Route::post('log/checkpart', array('as' => 'admin', 'uses' => 'WarrantyAdminController@checkPartRequired'));

 
         Route::post('log/getassignuser', array('as' => 'admin', 'uses' => 'WarrantyAdminController@getAssignUser'));



        /*Media Main Category*/

        Route::get('log/mediacategorydata', array('as' => 'admin', 'uses' => 'MediaCategoryController@dataMediaCateogry'));



        Route::post('log/mediacategorydelete', array('as' => 'admin', 'uses' => 'MediaCategoryController@deleteData'));



        /*Media Sub Category*/

        Route::post('log/mediasubcategorydelete', array('as' => 'admin', 'uses' => 'MediaSubCategoryController@deleteData'));



        Route::post('log/mediafiledelete', array('as' => 'admin', 'uses' => 'MediaCategoryFileController@deleteData'));



        /*Employee Data*/

        Route::get('log/employeedata', array('as' => 'admin', 'uses' => 'ManageEmployeeController@dataEmployee'));



        Route::post('log/employeedelete', array('as' => 'admin', 'uses' => 'ManageEmployeeController@deleteData'));

		

        Route::post('log/dealerdelete', array('as' => 'admin', 'uses' => 'ManageDealerController@dealerDelete'));



    });



});



/***************Dealer System -- Jignesh 19/7/2016 *************************/



Route::get('/dealer', array('as' => 'dealer', 'uses' => 'DealerController@index'));





Route::group(array('namespace'=>'dealer'), function(){



    /********************** Login DealerController***********************/



    Route::get('/dealer/', array('as' => 'dealer', 'uses' => 'DealerController@index'));



    Route::get('/dealer/login', array('as' => 'dealer', 'uses' => 'DealerController@index'));



    Route::get('/dealer/logout', array('as' => 'dealer', 'uses' => 'DealerController@logout'));



    Route::get('/dealer/dashboard', array('as' => 'dealer', 'uses' => 'DealerController@dashboard'));



    Route::post('/dealer/loginform/', array('as' => 'dealer', 'uses' => 'DealerController@loginform'));



    Route::post('/dealer/forgotPasswordAdmin/', array('as' => 'dealer', 'uses' => 'DealerController@forgotPasswordAdmin'));



    Route::get('/dealer/profiledata/', array('as' => 'dealer', 'uses' => 'DealerController@profile'));



    Route::post('/dealer/profileupdate/', array('as' => 'dealer', 'uses' => 'DealerController@profileUpdate'));



    Route::post('/dealer/passwordupdate/', array('as' => 'dealer', 'uses' => 'DealerController@passwordUpdate'));



    Route::post('/dealer/updateAvatar/', array('as' => 'dealer', 'uses' => 'DealerController@updateDealerAvatar'));



    Route::post('/dealer/billingaddress/', array('as' => 'dealer', 'uses' => 'DealerController@billingAddress'));

  

    /**********************Ends  Login DealerController***********************/



    /**********************DealerProductController***********************/



    Route::get('/dealer/product', array('as' => 'dealer', 'uses' => 'DealerProductController@index'));



    Route::get('/dealer/productdetail/{id}', array('as' => 'dealer', 'uses' => 'DealerProductController@productDetail'));

	

	Route::post('/dealer/productbycategory', array('as' => 'dealer', 'uses' => 'DealerProductController@productByCategory'));

	  

    /**********************Ends DealerProductController***********************/

	

    /********************** Dealer Accessory Controller***********************/

	

	 Route::get('/dealer/accessorylist', array('as' => 'dealer', 'uses' => 'DealerAccessoryController@accessoryList'));

	

	//Route::get('/dealer/accessoryitemlist/{id}', array('as' => 'dealer', 'uses' => 'DealerAccessoryController@accessoryItemList'));

	

	Route::get('/dealer/accessoryitemlist/{id}', array('as' => 'dealer', 'uses' => 'DealerAccessoryController@accessorySubList'));

	

	Route::get('/dealer/accessoryorder/', array('as' => 'dealer', 'uses' => 'DealerAccessoryController@dealerAccessoryOrder'));

	

	Route::get('/dealer/accessoryorderdetail/{id}', array('as' => 'dealer', 'uses' => 'DealerAccessoryController@dealerAccessoryOrderDetail'));

	

    /**********************Ends Dealer Accessory Controller***********************/

	

	/**********************CartProductController***********************/

	Route::post('/dealer/addtocart/', array('as' => 'dealer', 'uses' => 'CartController@index'));

	

    Route::post('/dealer/updateCart/', array('as' => 'dealer', 'uses' => 'CartController@updateCart'));

	

    Route::post('/dealer/placeOrder/', array('as' => 'dealer', 'uses' => 'CartController@placeOrder'));

	

    Route::get('/dealer/viewCart/', array('as' => 'dealer', 'uses' => 'CartController@viewcart'));

	

    Route::get('/dealer/checkout/', array('as' => 'dealer', 'uses' => 'CartController@checkout'));

	

    //Route::get('/dealer/removeCartItem/{id}', array('as' => 'dealer', 'uses' => 'CartController@removecartitem'));

    

	Route::post('/dealer/removeCartItem/', array('as' => 'dealer', 'uses' => 'CartController@removecartitem'));

		

    /**********************Ends CartProductController***********************/

	

    /**********************OrderProductController***********************/

	

	Route::get('/dealer/dealerorders/', array('as' => 'dealer', 'uses' => 'OrderController@index'));
	
	Route::get('/dealer/dealerordersinvoicelist/', array('as' => 'dealer', 'uses' => 'OrderController@dealerOrdersInvoiceList'));

	

	Route::get('/dealer/dealerOrderList', array('as' => 'dealer', 'uses' => 'OrderController@dealerorderlist'));

	

	Route::get('/dealer/editOrder/{id}', array('as' => 'dealer', 'uses' => 'OrderController@editorder'));

	

	Route::post('/dealer/updateOrder/', array('as' => 'dealer', 'uses' => 'OrderController@updateorder'));

	

	Route::get('/dealer/CancelOrder/{id}', array('as' => 'dealer', 'uses' => 'OrderController@cancelorder'));

	

	Route::post('/dealer/addNotes/', array('as' => 'dealer', 'uses' => 'OrderController@addnotes'));

	

	Route::get('/dealer/dealerplaceorder/', array('as' => 'dealer', 'uses' => 'OrderController@dealerPlaceOrder'));

	

	Route::post('/dealer/placeordercheckout/', array('as' => 'dealer', 'uses' => 'OrderController@placeOrderCheckout'));

	

	Route::post('/dealer/addordernotestoadmin/', array('as' => 'dealer', 'uses' => 'OrderController@addOrderNotesToAdmin'));

	

	Route::get('/dealer/dealerorderdetials/{id}', array('as' => 'dealer', 'uses' => 'OrderController@dealerOrderDetials'));

	 

	Route::get('/dealer/delaerfinanceorders/', array('as' => 'dealer', 'uses' => 'OrderController@delaerFinanceOrders'));

	Route::get('/dealer/dealerrotalist/', array('as' => 'dealer', 'uses' => 'OrderController@dealerOrderRotaList'));

	

	Route::post('/dealer/addcustomername/', array('as' => 'dealer', 'uses' => 'OrderController@addCustomerName'));
	
	Route::post('/dealer/editabledatapostcomment/', array('as' => 'dealer', 'uses' => 'OrderController@ediTableDataPostComment'));
	
	Route::post('/dealer/placeorderaccessoriesredirect/', array('as' => 'dealer', 'uses' => 'OrderController@placeOrderAccessoriesRedirect'));

	 

	

	

    /**********************Ends OrderProductController***********************/

	/**********************Special Order Controller***********************/



    Route::get('/dealer/specialorders/', array('as' => 'dealer', 'uses' => 'SpecialOrderController@index'));



    Route::get('/dealer/specialorderadd/', array('as' => 'dealer', 'uses' => 'SpecialOrderController@addSpecialOrder'));



    Route::post('/dealer/specialordersadd/', array('as' => 'dealer', 'uses' => 'SpecialOrderController@saveData'));



	Route::get('/dealer/dealerspecialorderdetail/{id}', array('as' => 'dealer', 'uses' => 'SpecialOrderController@dealerSpecialOrderDetail'));



    /**********************Ends Special Order Controller***********************/



    /***************************************** Dealer Task Reports***************************************/

    Route::get('/dealer/task/', array('as' => 'dealer', 'uses' => 'DealerTaskController@index'));



    Route::get('/dealer/taskadd/', array('as' => 'dealer', 'uses' => 'DealerTaskController@addTask'));



    Route::post('/dealer/taskSave', array('as' => 'dealer', 'uses' => 'DealerTaskController@saveData'));



    Route::get('/dealer/edit-task/{id}', array('as' => 'dealer', 'uses' => 'DealerTaskController@editTask'));



    Route::post('/dealer/taskedit', array('as' => 'dealer', 'uses' => 'DealerTaskController@editData'));



    Route::get('/dealer/taskcalender/', array('as' => 'dealer', 'uses' => 'DealerCalenderController@index'));



    /**************************************Ends Dealer Task Reports***************************************/





    /***************************************** Dealer Leads Reports***************************************/

    Route::get('/dealer/leadsreport/', array('as' => 'dealer', 'uses' => 'DealerLeadsController@index'));



    Route::get('/dealer/leadsreportadd/', array('as' => 'dealer', 'uses' => 'DealerLeadsController@addLeadreport'));



    Route::post('/dealer/leadreportSave', array('as' => 'dealer', 'uses' => 'DealerLeadsController@saveData'));



    Route::get('/dealer/edit-leadreport/{id}', array('as' => 'dealer', 'uses' => 'DealerLeadsController@editLeadreport'));



    Route::post('/dealer/leadreportedit', array('as' => 'dealer', 'uses' => 'DealerLeadsController@editData'));



    /**************************************Ends Dealer Leads Reports***************************************/



    /*Dealer Warranty*/

	

    Route::get('/dealer/warranty', array('as' => 'dealer', 'uses' => 'WarrantyDealerController@index'));



    Route::get('/dealer/warrantyadd', array('as' => 'dealer', 'uses' => 'WarrantyDealerController@warrantyAdd'));



    Route::post('/dealer/warrantysave', array('as' => 'dealer', 'uses' => 'WarrantyDealerController@warrantySaveData'));



    Route::post('/dealer/warrantyfile', array('as' => 'dealer', 'uses' => 'WarrantyDealerController@fileupload'));



    Route::post('/dealer/warrantyremove', array('as' => 'dealer', 'uses' => 'WarrantyDealerController@fileRemove'));



    Route::get('/dealer/warrantyedit/{editid}', array('as' => 'dealer', 'uses' => 'WarrantyDealerController@warrantyEdit'));



    Route::post('dealer/warrantygetimages', array('as' => 'dealer', 'uses' => 'WarrantyDealerController@getFileWarranty'));



    /*Media File*/

    Route::get('/dealer/mediacategoryfile', array('as' => 'dealer', 'uses' => 'DealerMediaCategoryFileController@index'));



    Route::get('/dealer/mediacategoryfilelist/{id}', array('as' => 'dealer', 'uses' => 'DealerMediaCategoryFileController@mediaList'));



    /*News Latter*/

    Route::get('/dealer/newslatter', array('as' => 'dealer', 'uses' => 'DealerNewsLatterController@index'));



    Route::get('dealer/campaigncontent/{id}', array('as' => 'dealer', 'uses' => 'DealerNewsLatterController@getContain'));





    /**********************Ajax***********************/

	Route::group(array('prefix'=>'dealer/ajax'),function(){



        Route::get('log/specialdata', array('as' => 'dealer', 'uses' => 'SpecialOrderController@dataSpecialOrder'));



        Route::post('log/colordata', array('as' => 'dealer', 'uses' => 'SpecialOrderController@dataColor'));

		

        Route::post('log/attributedata', array('as' => 'dealer', 'uses' => 'SpecialOrderController@attributeData'));



        Route::post('log/specialorderdelete', array('as' => 'dealer', 'uses' => 'SpecialOrderController@deleteData'));

		

        Route::post('log/deleteorder', array('as' => 'dealer', 'uses' => 'OrderController@deleteOrder'));

		

        Route::post('log/deleteorderaccessory', array('as' => 'dealer', 'uses' => 'OrderController@deleteOrderAccessory'));

		

        Route::post('log/getdelaerproducs', array('as' => 'dealer', 'uses' => 'DealerProductController@getDelaerProducs'));

		

		Route::post('log/accessoryfilter', array('as' => 'dealer', 'uses' => 'DealerAccessoryController@accessoryFilter'));

		

		/* Dealer New Module */



        Route::get('log/task', array('as' => 'dealer', 'uses' => 'DealerTaskController@dataTask'));



        Route::post('log/changetaks', array('as' => 'dealer', 'uses' => 'DealerCalenderController@changeTask'));



        Route::post('log/readtaks', array('as' => 'dealer', 'uses' => 'DealerCalenderController@readTask'));



        Route::post('log/taskdelete', array('as' => 'dealer', 'uses' => 'DealerTaskController@deleteData'));


        Route::post('log/taskstatuschange', array('as' => 'dealer', 'uses' => 'DealerTaskController@changeStatusData'));



        Route::get('log/leadsreport', array('as' => 'dealer', 'uses' => 'DealerLeadsController@dataLeadsReport'));



        Route::post('log/leadsdelete', array('as' => 'dealer', 'uses' => 'DealerLeadsController@deleteData'));



        Route::post('log/warrantynote', array('as' => 'dealer', 'uses' => 'WarrantyDealerController@noteDataForm'));



        Route::post('log/addwarrantynote', array('as' => 'dealer', 'uses' => 'WarrantyDealerController@noteAdd'));



        Route::post('log/warrantydelete', array('as' => 'dealer', 'uses' => 'WarrantyDealerController@deleteData'));
		
        Route::post('log/placeorderaccessories', array('as' => 'dealer', 'uses' => 'OrderController@placeOrderAccessories'));
		
        Route::post('log/placeorderaccessoriesremove', array('as' => 'dealer', 'uses' => 'OrderController@placeOrderAccessoriesRemove'));

    });



    /**********************Ends Ajax***********************/

});





/***************Ends Dealer System*************************/



	



/*************** Staff System*************************/

Route::get('/staff/', array('as' => 'staff', 'uses' => 'StaffController@index'));



Route::group(array('namespace'=>'staff'), function() {



    /********************** Login DealerController***********************/



    Route::get('/staff/', array('as' => 'staff', 'uses' => 'StaffController@index'));



    Route::get('/staff/login', array('as' => 'staff', 'uses' => 'StaffController@index'));



    Route::get('/staff/logout', array('as' => 'staff', 'uses' => 'StaffController@logout'));



    Route::get('/staff/dashboard', array('as' => 'staff', 'uses' => 'StaffController@index'));



    Route::post('/staff/loginform/', array('as' => 'staff', 'uses' => 'StaffController@loginform'));



    Route::post('/staff/forgotPasswordstaff/', array('as' => 'staff', 'uses' => 'StaffController@forgotPasswordStaff'));



    Route::get('/staff/profiledata/', array('as' => 'staff', 'uses' => 'StaffController@profile'));



    Route::post('/staff/profileupdate/', array('as' => 'staff', 'uses' => 'StaffController@profileUpdate'));



    Route::post('/staff/passwordupdate/', array('as' => 'staff', 'uses' => 'StaffController@passwordUpdate'));



    Route::post('/staff/updateAvatar/', array('as' => 'staff', 'uses' => 'StaffController@updateStaffAvatar'));





    /*****************************************Leads Reports***************************************/

    Route::get('/staff/leadsreport/', array('as' => 'staff', 'uses' => 'StaffLeadsController@index'));



    Route::get('/staff/leadsreportadd/', array('as' => 'staff', 'uses' => 'StaffLeadsController@addLeadreport'));



    Route::post('/staff/leadreportSave', array('as' => 'staff', 'uses' => 'StaffLeadsController@saveData'));



    Route::get('/staff/edit-leadreport/{id}', array('as' => 'staff', 'uses' => 'StaffLeadsController@editLeadreport'));



    Route::post('/staff/leadreportedit', array('as' => 'staff', 'uses' => 'StaffLeadsController@editData'));



    /**************************************Ends Leads Reports***************************************/





    /*****************************************Task Reports***************************************/

    Route::get('/staff/task/', array('as' => 'staff', 'uses' => 'StaffTaskController@index'));



    Route::get('/staff/taskadd/', array('as' => 'staff', 'uses' => 'StaffTaskController@addTask'));



    Route::post('/staff/taskSave', array('as' => 'staff', 'uses' => 'StaffTaskController@saveData'));



    Route::get('/staff/edit-task/{id}', array('as' => 'staff', 'uses' => 'StaffTaskController@editTask'));



    Route::post('/staff/taskedit', array('as' => 'staff', 'uses' => 'StaffTaskController@editData'));



    Route::get('/staff/taskcalender/', array('as' => 'staff', 'uses' => 'StaffCalenderController@index'));



    /**************************************Ends Leads Reports***************************************/



	/*Staff Warranty*/

	

    Route::get('/staff/warranty', array('as' => 'staff', 'uses' => 'WarrantyStaffController@index'));



    Route::get('/staff/warrantyadd', array('as' => 'staff', 'uses' => 'WarrantyStaffController@warrantyAdd'));



    Route::post('/staff/warrantysave', array('as' => 'staff', 'uses' => 'WarrantyStaffController@warrantySaveData'));



    Route::post('/staff/warrantyfile', array('as' => 'staff', 'uses' => 'WarrantyStaffController@fileupload'));



    Route::post('/staff/warrantyremove', array('as' => 'staff', 'uses' => 'WarrantyStaffController@fileRemove'));



    Route::get('/staff/warrantyedit/{editid}', array('as' => 'staff', 'uses' => 'WarrantyStaffController@warrantyEdit'));



    Route::post('staff/warrantygetimages', array('as' => 'staff', 'uses' => 'WarrantyStaffController@getFileWarranty'));



    Route::get('staff/warrantypdf/{id}', array('as' => 'staff', 'uses' => 'WarrantyStaffController@warrantyPDF'));



    Route::get('staff/warrantyprint/{id}', array('as' => 'staff', 'uses' => 'WarrantyStaffController@warrantyPrint'));



   

    /**********************Ajax***********************/

    Route::group(array('prefix'=>'staff/ajax'),function(){



        Route::get('log/leadsreport', array('as' => 'staff', 'uses' => 'StaffLeadsController@dataLeadsReport'));



        Route::post('log/leadsdelete', array('as' => 'staff', 'uses' => 'StaffLeadsController@deleteData'));



        Route::get('log/task', array('as' => 'staff', 'uses' => 'StaffTaskController@dataTask'));



        Route::post('log/changetaks', array('as' => 'staff', 'uses' => 'StaffCalenderController@changeTask'));



        Route::post('log/readtaks', array('as' => 'staff', 'uses' => 'StaffCalenderController@readTask'));



        Route::post('log/taskdelete', array('as' => 'staff', 'uses' => 'StaffTaskController@deleteData'));

        Route::post('log/taskstatuschange', array('as' => 'staff', 'uses' => 'StaffTaskController@changeStatusData'));

		

		Route::post('log/getusers', array('as' => 'staff', 'uses' => 'WarrantyStaffController@getUsers'));

  

		Route::post('log/warrantynote', array('as' => 'staff', 'uses' => 'WarrantyStaffController@noteDataForm'));



        Route::post('log/addwarrantynote', array('as' => 'staff', 'uses' => 'WarrantyStaffController@noteAdd'));



        Route::post('log/warrantydelete', array('as' => 'staff', 'uses' => 'WarrantyStaffController@deleteData'));

 

		Route::post('log/checkpart', array('as' => 'staff', 'uses' => 'WarrantyStaffController@checkPartRequired'));

 

        Route::post('log/getassignuser', array('as' => 'staff', 'uses' => 'StaffTaskController@getAssignUser'));

    });



    /**********************Ends Ajax***********************/



});

/***************Ends Staff System*************************/





Route::group(array('namespace'=>'logdata'), function() {



    Route::get('logdata/loglistdata', array('as' => 'logdata', 'uses' => 'LogController@loglist'));

		

    Route::group(array('prefix'=>'logdata/ajax'),function(){



    Route::get('log/loglistdata', array('as' => 'logdata', 'uses' => 'LogController@dataLog'));



    });

});



Route::get('/customer', array('as' => 'customer', 'uses' => 'CustomerController@index'));





Route::group(array('namespace'=>'customer'), function()

{

    Route::get('/customer', array('as' => 'customer', 'uses' => 'CustomerController@index'));



    Route::get('/customer/index', array('as' => 'customer', 'uses' => 'CustomerController@index'));



    Route::get('/customer/dashboard', array('as' => 'customer', 'uses' => 'CustomerController@index'));





    /********************** Login CustomerController***********************/



   Route::get('/customer/register', array('as' => 'customer', 'uses' => 'CustomerController@registerForm'));



    Route::post('/customer/registerdata', array('as' => 'customer', 'uses' => 'CustomerController@registerData'));



    Route::get('/customer/', array('as' => 'customer', 'uses' => 'CustomerController@index'));



    Route::get('/customer/login', array('as' => 'customer', 'uses' => 'CustomerController@index'));



    Route::get('/customer/logout', array('as' => 'customer', 'uses' => 'CustomerController@logout'));



    Route::get('/customer/dashboard', array('as' => 'customer', 'uses' => 'CustomerController@index'));



    Route::post('/customer/loginform/', array('as' => 'customer', 'uses' => 'CustomerController@loginform'));



    Route::post('/customer/forgotPasswordcustomer/', array('as' => 'customer', 'uses' => 'CustomerController@forgotPasswordCustomer'));



    Route::get('/customer/profiledata/', array('as' => 'customer', 'uses' => 'CustomerController@profile'));



    Route::post('/customer/profileupdate/', array('as' => 'customer', 'uses' => 'CustomerController@profileUpdate'));



    Route::post('/customer/passwordupdate/', array('as' => 'customer', 'uses' => 'CustomerController@passwordUpdate'));



    Route::post('/customer/updateAvatar/', array('as' => 'customer', 'uses' => 'CustomerController@updateCustomerAvatar'));





	Route::get('/customer/warranty', array('as' => 'customer', 'uses' => 'WarrantyCustomerController@index'));



    Route::get('/customer/warrantyadd', array('as' => 'customer', 'uses' => 'WarrantyCustomerController@warrantyAdd'));



    Route::post('/customer/warrantysave', array('as' => 'customer', 'uses' => 'WarrantyCustomerController@warrantySaveData'));



    Route::post('/customer/warrantyfile', array('as' => 'customer', 'uses' => 'WarrantyCustomerController@fileupload'));



    Route::post('/customer/warrantyremove', array('as' => 'customer', 'uses' => 'WarrantyCustomerController@fileRemove'));



    Route::get('/customer/warrantyedit/{editid}', array('as' => 'customer', 'uses' => 'WarrantyCustomerController@warrantyEdit'));



    Route::post('customer/warrantygetimages', array('as' => 'customer', 'uses' => 'WarrantyCustomerController@getFileWarranty'));



    Route::group(array('prefix'=>'customer/ajax'),function(){



       Route::post('log/warrantynote', array('as' => 'customer', 'uses' => 'WarrantyCustomerController@noteDataForm'));



        Route::post('log/addwarrantynote', array('as' => 'customer', 'uses' => 'WarrantyCustomerController@noteAdd'));



        Route::post('log/warrantydelete', array('as' => 'customer', 'uses' => 'WarrantyCustomerController@deleteData'));

    });



});





/*************** Employee System*************************/

Route::get('/employee/', array('as' => 'employee', 'uses' => 'EmployeeController@index'));



Route::group(array('namespace'=>'employee'), function()

{



    /********************** Login DealerController***********************/



    Route::get('/employee/', array('as' => 'employee', 'uses' => 'EmployeeController@index'));



    Route::get('/employee/login', array('as' => 'employee', 'uses' => 'EmployeeController@index'));



    Route::get('/employee/logout', array('as' => 'employee', 'uses' => 'EmployeeController@logout'));



    Route::get('/employee/dashboard', array('as' => 'employee', 'uses' => 'EmployeeController@index'));



    Route::post('/employee/loginform/', array('as' => 'employee', 'uses' => 'EmployeeController@loginform'));



    Route::post('/employee/forgotPasswordemployee/', array('as' => 'employee', 'uses' => 'EmployeeController@forgotPasswordEmployee'));



    Route::get('/employee/profiledata/', array('as' => 'employee', 'uses' => 'EmployeeController@profile'));



    Route::post('/employee/profileupdate/', array('as' => 'employee', 'uses' => 'EmployeeController@profileUpdate'));



    Route::post('/employee/passwordupdate/', array('as' => 'employee', 'uses' => 'EmployeeController@passwordUpdate'));



    Route::post('/employee/updateAvatar/', array('as' => 'employee', 'uses' => 'EmployeeController@updateEmployeeAvatar'));





    /*****************************************Task Reports***************************************/

    Route::get('/employee/task/', array('as' => 'employee', 'uses' => 'EmployeeTaskController@index'));



    Route::get('/employee/taskadd/', array('as' => 'employee', 'uses' => 'EmployeeTaskController@addTask'));



    Route::post('/employee/taskSave', array('as' => 'employee', 'uses' => 'EmployeeTaskController@saveData'));



    Route::get('/employee/edit-task/{id}', array('as' => 'employee', 'uses' => 'EmployeeTaskController@editTask'));



    Route::post('/employee/taskedit', array('as' => 'employee', 'uses' => 'EmployeeTaskController@editData'));



    Route::get('/employee/taskcalender/', array('as' => 'employee', 'uses' => 'EmployeeCalenderController@index'));



    /**************************************Ends Leads Reports***************************************/



    /*Employee Warranty*/



    Route::get('/employee/warranty', array('as' => 'employee', 'uses' => 'WarrantyEmployeeController@index'));



    Route::get('/employee/warrantyadd', array('as' => 'employee', 'uses' => 'WarrantyEmployeeController@warrantyAdd'));



    Route::post('/employee/warrantysave', array('as' => 'employee', 'uses' => 'WarrantyEmployeeController@warrantySaveData'));



    Route::post('/employee/warrantyfile', array('as' => 'employee', 'uses' => 'WarrantyEmployeeController@fileupload'));



    Route::post('/employee/warrantyremove', array('as' => 'employee', 'uses' => 'WarrantyEmployeeController@fileRemove'));



    Route::get('/employee/warrantyedit/{editid}', array('as' => 'employee', 'uses' => 'WarrantyEmployeeController@warrantyEdit'));



    Route::post('employee/warrantygetimages', array('as' => 'employee', 'uses' => 'WarrantyEmployeeController@getFileWarranty'));



    Route::get('employee/warrantypdf/{id}', array('as' => 'employee', 'uses' => 'WarrantyEmployeeController@warrantyPDF'));



    Route::get('employee/warrantyprint/{id}', array('as' => 'employee', 'uses' => 'WarrantyEmployeeController@warrantyPrint'));



    /**********************Ajax***********************/

    Route::group(array('prefix'=>'employee/ajax'),function()

    {

        Route::get('log/task', array('as' => 'employee', 'uses' => 'EmployeeTaskController@dataTask'));



        Route::post('log/taskdelete', array('as' => 'employee', 'uses' => 'EmployeeTaskController@deleteData'));



        Route::post('log/changetaks', array('as' => 'employee', 'uses' => 'EmployeeCalenderController@changeTask'));



        Route::post('log/readtaks', array('as' => 'employee', 'uses' => 'EmployeeCalenderController@readTask'));

        Route::post('log/taskstatuschange', array('as' => 'employee', 'uses' => 'EmployeeTaskController@changeStatusData'));



         /* Warranty */



        Route::post('log/warrantynote', array('as' => 'employee', 'uses' => 'WarrantyEmployeeController@noteDataForm'));



        Route::post('log/addwarrantynote', array('as' => 'employee', 'uses' => 'WarrantyEmployeeController@noteAdd'));



        Route::post('log/warrantydelete', array('as' => 'employee', 'uses' => 'WarrantyEmployeeController@deleteData'));

 
		Route::post('log/checkpart', array('as' => 'employee', 'uses' => 'WarrantyEmployeeController@checkPartRequired'));

    });



    /**********************Ends Ajax***********************/



});

/***************Ends Employee System*************************/

/*Back up Module*/
Route::get('databasebackup', 'SuperBackupController@index');

Route::get('creatbackup', 'SuperBackupController@dbBackup');

Route::get('importbackup/{name}', 'SuperBackupController@dbImport');

Route::post('deletebackup', 'SuperBackupController@dbDelete');

Route::get('testing','TestingController@index');



Route::get('phpinfo',function (){

    phpinfo();

});

