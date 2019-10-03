<?php
 header('Access-Control-Allow-Origin: *');
header( 'Access-Control-Allow-Headers: Authorization, Content-Type' );
header('Access-Control-Allow-Headers: Content-Type, x-xsrf-token, x_csrftoken');

////////////////////////////////////////////////
////////////////API ROUTES//////////////////////
////////////////////////////////////////////////
 
Route::group(['middleware'=>'cors','prefix' => 'api'], function () {
	Route::get('debug','ApiController@debug');
	Route::post('login','ApiController@login');
	Route::get('appversion','ApiController@appversion');
	Route::post('signup','ApiController@signup');
	Route::get('userkey/{crypto}/{uid}/{tokenAPI}','ApiController@userPrivateKey');
	Route::post('resendemail','ApiController@resendemail');
	Route::post('reset_password','ApiController@reset_password');
	Route::post('change_password','ApiController@change_password');
	Route::get('state','ApiController@state');
	Route::post('users','ApiController@userInfo'); 
	Route::get('trans/{crypto}/{label}/{tokenAPI}','ApiController@transaction');
	Route::get('dashView/{crypto}/{uid}/{tokenAPI}','ApiController@dash_view');
	Route::get('dashboard/{uid}/{tokenAPI}','ApiController@dashboard');
	Route::get('maxCrypto/{crypto}/{uid}','ApiController@maxCrypto');
	Route::post('sendCrypto','ApiController@sendCrypto');
	Route::post('convert','ApiController@convert');
	Route::post('send_secretpin','ApiController@send_secretpin');
	Route::post('edit_powerpin','ApiController@send_powerpin');
	Route::post('edit_powerauth','ApiController@send_powerauth');
	Route::post('edit_powerfp','ApiController@send_powerfp');
	Route::post('edit_secretpin','ApiController@edit_secretpin');
	Route::get('remarkTrans/{txid}/{crypto}/{uid}/{token}','ApiController@remark_trans');
	Route::get('currency/{uid}/{token}','ApiController@getcurrency');
	Route::post('edit_currency','ApiController@update_currency');
	Route::get('crypto/{uid}/{token}','ApiController@getcrypto');
	Route::post('add_crypto','ApiController@create_asset');
	Route::post('rename_crypto','ApiController@rename_asset');
	Route::post('createInvoice','ApiController@create_inv');
	Route::get('transLND/{crypto}/{uid}/{token}','ApiController@transactionLND');
	Route::get('transInvLND/{crypto}/{uid}/{token}','ApiController@transactionInvLND');
	Route::post('sendLND','ApiController@sendLND');
	Route::post('sendLNDBTC','ApiController@sendLNDBTC');
	Route::post('sendBTCLND','ApiController@sendBTCLND');
	Route::post('listChannel','ApiController@list_channel');
	Route::post('addChannel','ApiController@create_channel');
	Route::post('closeChannel','ApiController@close_channel');
	Route::post('mail_trans','ApiController@mail_transaction'); 
	Route::post('mail_key','ApiController@mail_keys'); 
	Route::post('displayValue','ApiController@display_value'); 
	Route::get('mnemonic','ApiController@mnemonic_user'); 
});
  

	Route::get('/', 'Auth\LoginController@showLoginForm');
	Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
	Route::post('/login', 'Auth\LoginController@login')->name('login');
	Route::get('logout', 'Auth\LoginController@logout')->name('logout');
	Route::get('verify/email/{hash}','MainController@activEmail');
	Route::get('/password/reset', 'MainController@resetPassword');
	Route::post('/password/reset_submit', 'MainController@resetPasswordsubmit')->name('pword.email');
	Route::get('password/reset/{token}', 'MainController@showResetForm'); 
	Route::post('submit/reset','MainController@submitPassword')->name('submit.reset');
	Route::get('secretpin/reset/{token}', 'MainController@showResetpinForm'); 
	Route::post('submit/resetpin','MainController@submitSecretpin')->name('submit.resetpin');

Auth::routes();

////////////////////////////////////////////////
////////////////WALLET USER////////////////////
////////////////////////////////////////////////


Route::group(['middleware'=>['auth']], function() {
	Route::get('/authy', 'SupportController@authy')->name('authy');
	Route::get('home', 'SupportController@support_list'); 
	Route::get('users/home', 'SupportController@support_list')->name('support.list'); 
	Route::post('/authy/submit', 'SupportController@SubmitAuthy')->name('submit.authy');
	Route::get('/get/data/{type}','SupportController@ajax')->name('get.data');
	Route::get('support/edit/{id}','SupportController@support_edit')->name('support.edit');
	Route::post('support/update','SupportController@support_update')->name('support.update');
	Route::post('support/delete','SupportController@support_delete')->name('support.delete');
	Route::get('support/new','SupportController@support_new')->name('support.new'); 
	Route::post('support/store','SupportController@support_store')->name('support.store'); 
  
});


////////////////////////////////////////////////
////////////////WALLET ADMIN////////////////////
////////////////////////////////////////////////

Route::group(['prefix' => 'admin'], function () { 

	Route::get('/', 'AuthAdmin\LoginController@showLoginForm')->name('admin.login');
	Route::get('/login', 'AuthAdmin\LoginController@showLoginForm')->name('admin.login');
	Route::post('/submit/login', 'AuthAdmin\LoginController@login')->name('admin.login.submit');
	Route::get('logout', 'AuthAdmin\LoginController@logout')->name('admin.logout');


 Route::middleware(['admin'])->group(function () { 
	Route::get('testPage','AdminController@testPage')->name('admin.test');
	
	//setting
	Route::get('/setting','SettingController@view')->name('admin.setting.view');
	Route::post('/setting/edit','SettingController@setting_update')->name('admin.setting.update');	 
  
    //dashboard
	Route::get('/authy/admin','AdminController@authyAdmin')->name('authy.admin');
	Route::post('/authy','AdminController@SubmitAuthyAdmin')->name('admin.submit.authy');
	Route::get('dashboard', 'AdminController@index')->name('admin.dashboard');
	
	//member
	Route::get('member/list', 'MemberController@member_list')->name('admin.member.list'); 
	Route::post('member/new','MemberController@member_new')->name('admin.member.new');
	Route::post('member/update','MemberController@member_update')->name('admin.member.update');
	Route::post('member/password','MemberController@member_password')->name('admin.member.password');
	Route::post('member/delete','MemberController@member_delete')->name('admin.member.delete');
	
	//personal
	Route::get('person', 'AdminPersonController@admin_person')->name('admin.personal'); 
	Route::post('password','AdminPersonController@personal_password')->name('admin.personal.password');
	
	//transaction admin
	Route::get('/transactions/{crypto}', 'AdminController@transactions')->name('admin.transactions');
 
	//transaction users
	Route::get('/list/users/{crypto}', 'AdminController@listUsers')->name('users.list');
	Route::get('/transactions/users/{crypto}/{label}', 'AdminController@transactionsUsers')->name('users.transactions');
 
	//send
	Route::get('/send/{crypto}', 'AdminController@send')->name('admin.send');
	Route::post('/send/submit', 'AdminController@sendSubmit')->name('admin.send.submit');
	
	//support
	Route::get('support/list', 'SupportAdminController@support_list')->name('admin.support.list'); 
	Route::get('support/edit/{id}','SupportAdminController@support_edit')->name('admin.support.edit');
	Route::post('support/update','SupportAdminController@support_update')->name('admin.support.update');
	Route::post('support/delete','SupportAdminController@support_delete')->name('admin.support.delete');
	Route::post('support/closed','SupportAdminController@support_closed')->name('admin.support.closed');
	
	//user
	Route::get('user/list', 'UserController@member_list')->name('admin.user.list');  
	Route::post('user/update/resetpin','UserController@resetpin_update')->name('admin.user.update.resetpin'); 
	Route::get('user/transaction/{label}', 'UserController@user_transaction')->name('admin.user.transaction'); 
	
});


});
