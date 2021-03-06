<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User;
class UserController extends Controller
{
    private $success_status = 200;
    public function registerUser(Request $request)
    {
        // --------------------- [ User Registration ] ---------------------------
        $validator  =   Validator::make($request->all(),
            [
                'name'              =>      'required',
                'email'             =>      'required',
                'password'          =>      'required'
            ]
        );
        // if validation fails
        if($validator->fails()) {
            return response()->json(["status" => "400", "error" => $validator->errors()]);
        }
        $input              =       array(
            'name'              =>        $request->name,
            'email'             =>        $request->email,
            'password'          =>        bcrypt($request->password)
        );
        try {
            $user               =           User::create($input);
        } catch (\Throwable $th) {
            return response()->json(["status" => "400", "error" => $th]);
        }
        $user->sendEmailVerificationNotification();
        return response()->json(["success" => true, "status" => "success"]);
    }
    // --------------------------- [ User Login ] ------------------------------
    public function loginUser(Request $request) {
        $validator = Validator::make($request->all(),
            [
                'email'            =>         'required',
                'password'         =>         'required',
            ]
        );
        // check if validation fails
        if($validator->fails()) {
            return response()->json(["status" => "400", "error" => $validator->errors()]);
        }
        $email  =   $request->email;
        $password = $request->password;
        $user   =   DB::table("users")->where("email", "=", $email)->first();
        if(is_null($user)) {
            return response()->json(["status" => "400", "error" => "User Doesn't Exist"]);
        }
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $token                  =       $user->createToken('token')->accessToken;
            $success['success']     =       true;
            $success['message']     =       "Success! you are logged in successfully";
            $success['token']       =       $token;
            return response()->json(['success' => $success, 'status' => '200' ], $this->success_status);
        }
        else {
            return response()->json(['error' => 'Unauthorised', 'status' => '400'], 401);
        }
    }
    // ---------------------------- [ Use Detail ] -------------------------------
    public function userDetail() {
        $user       =       Auth::user();
        return response()->json(['success' => $user, 'status' => '200'], $this->success_status);
    }

    // -------------------------- [ Edit Using Passport Auth ]--------------------
    public function update(Request $request) {
        $user           =             Auth::user();
        $validator      =            Validator::make($request->all(),
            [
                'name'             =>         'required',
                'email'            =>         'required',
                'password'         =>         'required',
            ]
        );
        // if validation fails
        if($validator->fails()) {
            return response()->json(["validation errors" => $validator->errors()]);
        }
        $userDataArray      =     array(
            'name'              =>          $request->name,
            'email'             =>          $request->email,
            'password'          =>          bcrypt($request->password)
        );
        $user           =       User::where('id', $user->id)->update($userDataArray);
        return response()->json(['success' => true, 'message' => 'User updated successfully']);
    }

// ----------------------------- [ Delete User ] -----------------------------
    public function destroy() {
        $user           =           Auth::user();
        $user           =           User::findOrFail($user->id);
        $user->delete();
        return response()->json(['success' => true, 'message' => 'User deleted successfully']);
    }

    // ----------------------------- [ Logout User ] -----------------------------
    public function logout() {
        $logout = auth()->user()->token()->revoke();
        if ($logout != 1) {
            return response()->json(['error' => true, 'message' => 'Could not authenticate']);
        }
        // $user           =           Auth::user();
        // $user           =           User::findOrFail($user->id);
        // $user->delete();
        return response()->json(['success' => true, 'message' => 'Successfully logged out']);
    }
}
