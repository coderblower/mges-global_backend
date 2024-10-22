<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CandidateMedicalTestController;
use App\Http\Controllers\CandidateSkillTestController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DemandLetterController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\FinalTestController;
use App\Http\Controllers\MedicalTestListController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PreDemandLetterController;
use App\Http\Controllers\PreSkilledTestController;
use App\Http\Controllers\QuotaController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SkillListController;
use App\Http\Controllers\SkillTestController;
use App\Http\Controllers\TestByCountryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DemandLetterIssueUserController;
use App\Http\Controllers\VideoCallController;
use App\Models\DemandLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::get('/optimize', function() {

    Artisan::call('optimize');
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    Artisan::call('config:clear');
    return "Cleared!";
});
Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
//    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
//    Route::post('optimize', 'optimize');
    Route::post('send_otp', 'passResetOTP');
    Route::post('verify_otp', 'verifyOTP');
});
Route::get('/send-sms', [CandidateController::class, 'send_sms']);
Route::get('/table_upside_down', [UserController::class, 'approveCandidatesWithAllDocuments']);
Route::group(['name'=>'User','middleware' => 'api','prefix' => 'user'], function () {
    Route::post('/create', [UserController::class, 'create']);
    Route::post('/update', [UserController::class, 'update']);
    Route::post('/profile_update', [UserController::class, 'profileUpdate']);
    Route::post('/destroy', [UserController::class, 'destroy']);
    Route::post('/soft_destroy', [UserController::class, 'softDestroy']);
    Route::post('/all', [UserController::class, 'all']);
    Route::post('/get_user', [UserController::class, 'getUser']);
    Route::post('/get_user01', [UserController::class, 'getUser01']);
    Route::post('/count', [UserController::class, 'count']);
    Route::post('/group_by', [UserController::class, 'groupBy']);
    Route::post('/search_candidate', [UserController::class, 'searchCandidate']);
});
Route::group(['name'=>'Role','middleware' => 'api','prefix' => 'role'], function () {
    Route::post('/all', [RoleController::class, 'all']);
});
Route::group(['name'=>'Partner','middleware' => 'api','prefix' => 'partner'], function () {
    Route::post('/create', [PartnerController::class, 'create']);
    Route::post('/update', [PartnerController::class, 'update']);
    Route::post('/quota_update', [PartnerController::class, 'quotaUpdate']);
    Route::post('/destroy', [PartnerController::class, 'destroy']);
    Route::post('/all', [PartnerController::class, 'all']);
    Route::post('/get_partners', [PartnerController::class, 'getPartners']);
});
Route::group(['name'=>'Agents','middleware' => 'api','prefix' => 'agent'], function () {

    Route::get('/get_demand_letters/{id}', [PartnerController::class, 'get_demand_letters']);
    Route::post('/percentages', [PartnerController::class, 'percentages']);
    Route::post('/quota_create', [QuotaController::class, 'create']);
    Route::post('/quota_update', [QuotaController::class, 'update']);
    Route::post('/quota_all', [QuotaController::class, 'all']);

});
Route::group(['name'=>'Candidate','middleware' => 'api','prefix' => 'candidate'], function () {
    Route::post('/create', [CandidateController::class, 'create']);
    Route::post('/update', [CandidateController::class, 'update']);
    Route::post('/update_pif', [CandidateController::class, 'updatePIF']);
    Route::post('/delete_pif', [CandidateController::class, 'deletePIF']);
    Route::post('/update_approval_status', [CandidateController::class, 'updateApprovalStatus']);
    Route::post('/destroy', [CandidateController::class, 'destroy']);
    Route::post('/all', [CandidateController::class, 'all']);
    Route::post('/get_all', [CandidateController::class, 'getAll']);
    Route::post('/candidate_by_creator', [CandidateController::class, 'candidateByCreator']);
    Route::post('/candidate_by_creator_count', [CandidateController::class, 'candidateByCreatorCount']);
    Route::post('/get_candidate', [CandidateController::class, 'getCandidateInfo']);
    Route::post('/get_candidate_by_id', [CandidateController::class, 'getCandidateById']);
    Route::post('/candidate_qr_save', [CandidateController::class, 'saveQr']);
});
Route::group(['name'=>'Payment','middleware' => 'api','prefix' => 'payment'], function () {
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::post('/make-payment', [PaymentController::class, 'store']);
});
Route::group(['name'=>'Skill List','middleware' => 'api','prefix' => 'skill_list'], function () {
    Route::post('/create', [SkillListController::class, 'create']);
    Route::post('/update', [SkillListController::class, 'update']);
    Route::post('/destroy', [SkillListController::class, 'destroy']);
    Route::post('/all', [SkillListController::class, 'all']);
});
Route::group(['name'=>'Candidate Skill Test','middleware' => 'api','prefix' => 'candidate_skill_test'], function () {
    Route::post('/create', [CandidateSkillTestController::class, 'create']);
    Route::post('/update', [CandidateSkillTestController::class, 'update']);
    Route::post('/destroy', [CandidateSkillTestController::class, 'destroy']);
    Route::post('/all', [CandidateSkillTestController::class, 'all']);
});
Route::group(['name'=>'Pre Skill Test','middleware' => 'api','prefix' => 'pre_skill_test'], function () {
    Route::post('/create', [PreSkilledTestController::class, 'create']);
    Route::post('/update', [PreSkilledTestController::class, 'update']);
    Route::post('/destroy', [PreSkilledTestController::class, 'destroy']);
    Route::post('/all', [PreSkilledTestController::class, 'all']);
});
Route::group(['name'=>'Skill Test','middleware' => 'api','prefix' => 'skill_test'], function () {
    Route::post('/create', [SkillTestController::class, 'create']);
    Route::post('/update', [SkillTestController::class, 'update']);
    Route::post('/destroy', [SkillTestController::class, 'destroy']);
    Route::post('/all', [SkillTestController::class, 'all']);
    Route::post('/all0', [SkillTestController::class, 'all0']);
    Route::post('/all1', [SkillTestController::class, 'all1']);
});
Route::group(['name'=>'Final Test','middleware' => 'api','prefix' => 'final_test'], function () {
    Route::post('/create', [FinalTestController::class, 'create']);
    Route::post('/update', [FinalTestController::class, 'update']);
    Route::post('/destroy', [FinalTestController::class, 'destroy']);
    Route::post('/all', [FinalTestController::class, 'all']);
    Route::post('/all0', [FinalTestController::class, 'all0']);
    Route::post('/all1', [FinalTestController::class, 'all1']);
    Route::post('/training_centers', [FinalTestController::class, 'getTrainingCenters']);
    Route::post('/filter', [FinalTestController::class, 'filterTrainingReport']);
});
Route::group(['name'=>'Medical Test List','middleware' => 'api','prefix' => 'medical_test_list'], function () {
    Route::post('/create', [MedicalTestListController::class, 'create']);
    Route::post('/update', [MedicalTestListController::class, 'update']);
    Route::post('/destroy', [MedicalTestListController::class, 'destroy']);
    Route::post('/all', [MedicalTestListController::class, 'all']);
});
Route::group(['name'=>'Test By Country List','middleware' => 'api','prefix' => 'test_by_country'], function () {
    Route::post('/create', [TestByCountryController::class, 'create']);
    Route::post('/update', [TestByCountryController::class, 'update']);
    Route::post('/destroy', [TestByCountryController::class, 'destroy']);
    Route::post('/all', [TestByCountryController::class, 'all']);
});
Route::group(['name'=>'Candidate Medical Test','middleware' => 'api','prefix' => 'candidate_medical_test'], function () {
    Route::post('/create', [CandidateMedicalTestController::class, 'create']);
    Route::post('/update', [CandidateMedicalTestController::class, 'update']);
    Route::post('/destroy', [CandidateMedicalTestController::class, 'destroy']);
    Route::post('/all', [CandidateMedicalTestController::class, 'all']);
    Route::post('/count', [CandidateMedicalTestController::class, 'count']);
    Route::post('/filter', [CandidateMedicalTestController::class, 'filterMedicalReport']);
    Route::post('/medical_report_data', [CandidateMedicalTestController::class, 'reportData']);
});
Route::group(['name'=>'Country','middleware' => 'api','prefix' => 'country'], function () {
    Route::post('/create', [CountryController::class, 'create']);
    Route::post('/update', [CountryController::class, 'update']);
    Route::post('/destroy', [CountryController::class, 'destroy']);
    Route::post('/all', [CountryController::class, 'all']);
});
Route::group(['name'=>'Designation','middleware' => 'api','prefix' => 'designation'], function () {
    Route::post('/create', [DesignationController::class, 'create']);
//    Route::post('/update', [CountryController::class, 'update']);
//    Route::post('/destroy', [CountryController::class, 'destroy']);
    Route::post('/all', [DesignationController::class, 'all']);
});

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['name'=>'Demand Letter','middleware' => 'api','prefix' => 'demand_letter'], function () {
    Route::get('/index', [DemandLetterController::class, 'index']);
    Route::post('/store', [DemandLetterController::class, 'store']);
    Route::get('/{id}', [DemandLetterController::class, 'show']);
    Route::get('/list', [DemandLetterController::class, 'agentView']);
    Route::post('status_approve/{id}', [DemandLetterController::class, 'changeStatus'])->name('demand_letter.changeStatus');



});


Route::group(['name'=>'Pre Demand Letter','middleware' => 'api','prefix' => 'pre_demand_letter'], function () {

    Route::post('/assignAgent', [PreDemandLetterController::class, 'adminAssignAgentForPreDemandLetter']);
    Route::post('/admin_approve_agent_agreed_pre_demand/{id}', [PreDemandLetterController::class, 'adminApprovedAgentAgreedPreDemand']);

    Route::get('/getAllAgent', [PreDemandLetterController::class, 'getAllAgent']);
    // Admin panel
    Route::get('get_demand_list_by_admin', [PreDemandLetterController::class, 'getFilteredDemandLettersWithUser']);
    Route::post('approve_demand_letter/{id}', [PreDemandLetterController::class, 'approve_demand_letter']);
    Route::post('already', [PreDemandLetterController::class, 'already']);

    Route::get('show_demand_letter/{id}', [PreDemandLetterController::class, 'show_demand_letter']);
    Route::get('agreed_pdl_to_agency_single/{id}', [PreDemandLetterController::class, 'agreed_pdl_to_agency_single']);
    Route::get('agreed_pdl_to_agency', [PreDemandLetterController::class, 'agreed_pdl_to_agency']);
    Route::get('/index', [PreDemandLetterController::class, 'index']);
    Route::post('/store', [PreDemandLetterController::class, 'store']);
    Route::post('/list', [PreDemandLetterController::class, 'agentView']);
    Route::post('/agreed_list', [PreDemandLetterController::class, 'agreed_predemand_letter']);
    Route::get('/agent_list_in_agreed/{id}', [PreDemandLetterController::class, 'getUsersFromBdAgencyAgree']);
    Route::get('/{id}', [PreDemandLetterController::class, 'show']);
    Route::post('status_approve/{id}', [PreDemandLetterController::class, 'changeStatus'])->name('pre_demand_letter.changeStatus');
    Route::post('demand_single/{id}/{preId}', [PreDemandLetterController::class, 'demand_letter_make']);

    Route::post('agency_agreement_status_change/{id}/{userId}', [PreDemandLetterController::class, 'agencyAgreementStatusChange'])->name('pre_demand_letter.agencyAgreementStatusChange');



});


Route::group(['name'=>'Notification','middleware' => 'api'], function () {


   Route::get('/notifications', [NotificationController::class, 'index']);

   Route::get('/notifications/seen', [NotificationController::class, 'markAsSeen']);




});

Route::group(['name'=>'sf','middleware' => 'api', 'prefix'=>'candidate_assign'], function () {


    Route::post('/candidate_list_to_assign', [DemandLetterIssueUserController::class, 'candidateListToAssign']);

    Route::post('/store', [DemandLetterIssueUserController::class, 'store']);

    Route::get('/all', [DemandLetterIssueUserController::class, 'index']);

    Route::post('/get_all_Selected_Candidate/{demand_letter_id}', [DemandLetterIssueUserController::class, 'getSelectedCandidate']);



 });

 Route::group(['name'=>'sf','middleware' => 'api', 'prefix'=>'video_call'], function () {


  // routes/api.php
Route::post('/start_call', [VideoCallController::class, 'startCall']);



 });








