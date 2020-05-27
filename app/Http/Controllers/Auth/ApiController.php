<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendPasswordResetMail;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    /**
     * @api {post} auth/login auth login
     * @apiGroup Auth
     *  
     * @apiHeaderExample {json} Header-Example:
     * {
     *    "Accept":"application/json",
     *    "Content-lang":"en"
     * }
     * 
     * @apiParam {String} username
     * @apiParam {String} password
     * @apiSuccessExample Success-Response:
     *{
     *    "status": true,
     *    "data": {
     *        "id": 1,
     *        "username": "admin",
     *        "email": "admin@test.com",
     *        "email_verified_at": null,
     *        "gender": "male",
     *        "birth": null,
     *        "created_at": "2020-04-28T12:48:45.000000Z",
     *        "updated_at": "2020-04-28T13:51:09.000000Z",
     *        "roles": [
     *            {
     *                "id": 1,
     *                "name": "admin",
     *                "pivot": {
     *                    "user_id": 1,
     *                    "role_id": 1
     *                }
     *            }
     *        ],
     *        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiZThlZjQ2Mzk0N2VhOWVkM2Q4ZWVmMTE2ZDU0MWEwOTQ1ZDQxNDBlZDIxNzZkM2Y1ZTZmNjYwYTgzODVmZDExYTdiNzI2MmQ0YzYzMjllYWUiLCJpYXQiOjE1ODgwODI4NzQsIm5iZiI6MTU4ODA4Mjg3NCwiZXhwIjoxNjE5NjE4ODc0LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.tQrmYX4KUqOR1oN-LH_rVmrqqpntvuOxQQC2Baz-IcgdV8l8ql9jL0LBV4eQ-H1m52MyJVIQ_uXMtMU5AxQQXanMeBOlBkXkl89z86_FHfYKALyEKufYi6dTX1dFtcfzg1FeubKQj04sA5qgiWSFR3v4d-TzSH9KppeuasFUWJPy506qLknUTp5uST0SQPMmfVbUtxpKLfHUCRAmB2AoedSCF966CAzQpxBodLSL_nzE7IsGseJSCGXj8ud9ZdCefHDZtReyj5Wu4CNx8XMAs4pd5gtmenrTqedsLwyUXu5Rzces7BHlvO1yLOKZr6DTQ3E504atsFqGaJB9kspSKrC0czUNDA28HwiF333DAvJ8uHPINelHC0-7nShUeHX71rGtoO8x7a6vpn5ZwnBZzVC7CP2PturucRHOceUAzeIgSiWp6qR3eNcInMEd5coxKx0Bdos6car7E-vBayQeomJ9ccSkXKpnORukvQvh39iCZIPmDXsFu61AJPAcG1lykAQ0Q85XKs6oCMVelAHiCGOwpv5VFHdNIZXdZqUGQrGYqJE5bq7NGY4M1SJqBI5MdA572d8_xebaiM5eLPPA_8hLr1-vXa-dAwLVRdBpWFKIN_bGt1d0Xk-QmUYBWv1ml3auG8WYL9ErtdOVbJ2hcl013adGqE6Dz-5yfGYrtj0"
     *    }
     *}
     */
    public function login()
    {
        $validator = validator(request()->all(), [
            'username' => 'required|string|min:3',
            'password' => 'required|string|min:6'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        if (!auth()->attempt(['username' => request('username'), 'password' => request('password')])) {
            return $this->sendError('Login ve ya parol sehvdir');
        }
        $user = auth()->user();
        $user->load('roles');
        $user['token'] = $user->createToken('T-shirt')->accessToken;
        return $this->sendSuccess($user);
    }

    /**
     * @api {get} auth/me auth me
     * @apiGroup Auth
     *  
     * @apiHeaderExample {json} Header-Example:
     * {
     *    "Accept":"application/json",
     *     "Authorization":"Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiODg3MzBmODc3YWM5ODZmMDQ0OGIyYmZjZTEwMDQxNzk4Njc4MGUxZDE5MTUwNGMxMjdiODgzNjQ4YjczY2QxZTAyNjQxZjlhYjQzOTE1ZTQiLCJpYXQiOjE1ODQ4OTk0OTQsIm5iZiI6MTU4NDg5OTQ5NCwiZXhwIjoxNjE2NDM1NDk0LCJzdWIiOiIyOSIsInNjb3BlcyI6W119.BP7BYGWBzG_o7GEkTvwMWOIc3OPGnzXMFfqcTPSIFDAoEyy5omsxHfSGjZ5xD1VNLD90hEQph3_G_VWXFDohgsq3UIyBA-rLcXwCQN_ZMCthZRA9jxlj8ZpNgb60-cSLjuFTeeFWsYOaszlxSa1nuAu_9mMEvcByv10XXiC5p-clgpSH_e23Fparh2VJrHoeKJrlqGwHSLR6Z-Gjf4OgeCvKpS4v4MEWFz1_Y_XgbZSRH2YCYWzCwBRn9SHzDSpjZ-QmA2o0lsszn9LaqQ-jC_pUpA031mNkDyLliD08mHcouyijUlB0_hmK2dtxGrEmfDd2XKfKTSMzVM3rfv8h9qNtv-WcWJA_llXiv-d1spl1qk5QpShFUtqO8aqMOzI1s3CgZ8gnL6RKghSTOsB3KQJavsR76i9qExeh_GFp10PxbZeobdcIIsvbDiD43SghVejfJZDc07hshjIoOT6Yt6BUkqPTb8QyrmTHkboP1gF4rTyDR1dMwAyIX2-J8P2XnkPPDjhYrTist4nXBa0EON10KYuK8GJTR5rHQLqzyDYWveiM_dMLDQizcknY00ZCtLoYa0HzQ08UlyYRH6BD1aDzdmLinzqGNCgZgkDCEqHRQZPR8is5iHBc9D1xPiS2JKGXUJeXslh0NhWx3t8WFnbqJiZAmP0xe7KRqmkjlwo",
     * "Content-lang":"en"
     * }
     * 
     * @apiSuccessExample Success-Response:
     *{
     *    "status": true,
     *    "data": {
     *        "id": 1,
     *        "username": "admin",
     *        "email": "admin@test.com",
     *        "email_verified_at": null,
     *        "gender": "male",
     *        "birth": null,
     *        "created_at": "2020-04-28T12:48:45.000000Z",
     *        "updated_at": "2020-04-28T13:51:09.000000Z",
     *        "roles": [
     *            {
     *                "id": 1,
     *                "name": "admin",
     *                "pivot": {
     *                    "user_id": 1,
     *                    "role_id": 1
     *                }
     *            }
     *        ]
     *    }
     *}
     */
    public function me()
    {
        $user = auth()->user();
        $user->load('roles');
        return $this->sendSuccess($user);
    }

    /**
     * @api {post} auth/register auth register
     * @apiGroup Auth
     *  
     * @apiHeaderExample {json} Header-Example:
     * {
     *      "Accept":"application/json",
     *      "Content-lang":"en"
     * }
     *  @apiParam {String} username
     *  @apiParam {String} email
     *  @apiParam {String} password
     *  @apiParam {String} password_confirmation
     * 
     * @apiSuccessExample Success-Response:
     *   {
     *       "status": true,
     *       "data": {
     *          "inserted_id":2
     * }
     * }
     */
    public function register()
    {
        $validator = validator(request()->all(), [
            'username' => 'required|string|min:3|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|string|min:6'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $user = new User;
        $user->username = request('username');
        $user->email = request('email');
        $user->password = bcrypt(request('password'));
        $user->save();
        return $this->sendSuccess(['inserted_id' => $user->id]);
    }

    /**
     * @api {post} auth/logout auth logout
     * @apiGroup Auth
     *  
     * @apiHeaderExample {json} Header-Example:
     * {
     *      "Accept":"application/json",
     *      "Authorization":"Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiODg3MzBmODc3YWM5ODZmMDQ0OGIyYmZjZTEwMDQxNzk4Njc4MGUxZDE5MTUwNGMxMjdiODgzNjQ4YjczY2QxZTAyNjQxZjlhYjQzOTE1ZTQiLCJpYXQiOjE1ODQ4OTk0OTQsIm5iZiI6MTU4NDg5OTQ5NCwiZXhwIjoxNjE2NDM1NDk0LCJzdWIiOiIyOSIsInNjb3BlcyI6W119.BP7BYGWBzG_o7GEkTvwMWOIc3OPGnzXMFfqcTPSIFDAoEyy5omsxHfSGjZ5xD1VNLD90hEQph3_G_VWXFDohgsq3UIyBA-rLcXwCQN_ZMCthZRA9jxlj8ZpNgb60-cSLjuFTeeFWsYOaszlxSa1nuAu_9mMEvcByv10XXiC5p-clgpSH_e23Fparh2VJrHoeKJrlqGwHSLR6Z-Gjf4OgeCvKpS4v4MEWFz1_Y_XgbZSRH2YCYWzCwBRn9SHzDSpjZ-QmA2o0lsszn9LaqQ-jC_pUpA031mNkDyLliD08mHcouyijUlB0_hmK2dtxGrEmfDd2XKfKTSMzVM3rfv8h9qNtv-WcWJA_llXiv-d1spl1qk5QpShFUtqO8aqMOzI1s3CgZ8gnL6RKghSTOsB3KQJavsR76i9qExeh_GFp10PxbZeobdcIIsvbDiD43SghVejfJZDc07hshjIoOT6Yt6BUkqPTb8QyrmTHkboP1gF4rTyDR1dMwAyIX2-J8P2XnkPPDjhYrTist4nXBa0EON10KYuK8GJTR5rHQLqzyDYWveiM_dMLDQizcknY00ZCtLoYa0HzQ08UlyYRH6BD1aDzdmLinzqGNCgZgkDCEqHRQZPR8is5iHBc9D1xPiS2JKGXUJeXslh0NhWx3t8WFnbqJiZAmP0xe7KRqmkjlwo",
     *      "Content-lang":"en"
     * }
     * 
     * @apiSuccessExample Success-Response:
     *   {
     *       "status": true,
     *       "data": null
     * }
     */
    public function logout()
    {
        $check = auth()->user()->token()->revoke();
        if (!$check) {
            return $this->sendError();
        }
        return $this->sendSuccess();
    }

    public function sendPasswordReset()
    {
        $validator = validator(request()->all(), [
            'email' => 'required|email|exists:users,email'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $token = Str::slug(Str::random(100));
        DB::table('password_resets')->updateOrInsert(
            ['email' => request('email')],
            ['token' => $token, 'created_at' => now()]
        );
        Mail::to(request('email'))->send(new SendPasswordResetMail($token));
        return $this->sendSuccess();
    }

    public function confirmPassword()
    {
        $validator = validator(request()->all(), [
            'password' => 'required|min:6|confirmed',
            'confirm_token' => 'required|string'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $email = DB::table('password_resets')
            ->where('token', request('confirm_token'))
            ->where('created_at', '>=', now()->subMinutes(5))
            ->value('email');
        if (!$email) {
            return $this->sendError();
        }
        $user = User::where('email', $email)->firstOrFail();
        $user->password = bcrypt(request('password'));
        $user->save();
        DB::table('password_resets')->where('token', request('confirm_token'))->delete();
        return $this->sendSuccess();
    }
}
