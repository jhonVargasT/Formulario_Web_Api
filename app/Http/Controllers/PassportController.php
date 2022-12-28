<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;
use App\Http\Controllers\FormularioController;
use App\Http\Controllers\ProyectoController;

class PassportController extends Controller
{
    public function register(Request $request)
    {
        $input = $request->only(['name', 'email', 'password']);

        $validate_data = [
            'name' => 'required|string|min:4',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ];

        $validator = Validator::make($input, $validate_data);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Porfavor verifica los parametros ingresados',
                'errors' => $validator->errors()
            ]);
        }

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password'])
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado correctamente'
        ], 200);
    }

    /**
     * Login user.
     *
     * @return json
     */
    public function login(Request $request)
    {

        $inputForm= $request->only(['form']);
        $input = $request->only(['name', 'password']);

        $validate_data = [
            'name' => 'required',
            'password' => 'required|min:8',
        ];

        $validator = Validator::make($input, $validate_data);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Porfavor verifica los parametros ingresados.',
                'errors' => $validator->errors()
            ]);
        }
        $input['name']=$input['name']. $inputForm['form'];

        //authentication attempt
        if (auth()->attempt($input)) {
            $token = auth()->user()->createToken('passport_token')->accessToken;
            $first_character = mb_substr($inputForm['form'], 2, 1);
            $formulario= new FormularioController();
            $proyecto= new ProyectoController();
            $respForm=null;
            if($first_character)
                $respForm = $formulario->searchFormulario($first_character);
         return response()->json([
                'success' => true,
                'message' => 'Usuario logueado correctamente.',
                'token' => $token,
                'formulario'=>$respForm,
                "proyecto"=>$proyecto->proyecto(),
                'user' => auth()->user()
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Usuario o contraseña incorrectos.'
            ]);
        }
    }

    /**
     * Access method to authenticate.
     *
     * @return json
     */
    public function userDetail()
    {
        return response()->json([
            'success' => true,
            'message' => 'Data fetched successfully.',
           'data' => auth()->user()
        ], 200);
    }

    /**
     * Logout user.
     *
     * @return json
     */
    public function logout()
    {
        $access_token = auth()->user()->token();

        // logout from only current device
        $tokenRepository = app(TokenRepository::class);
        $tokenRepository->revokeAccessToken($access_token->id);

        // use this method to logout from all devices
        // $refreshTokenRepository = app(RefreshTokenRepository::class);
        // $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($$access_token->id);

        return response()->json([
            'success' => true,
            'message' => 'User logout successfully.'
        ], 200);
    }
}
