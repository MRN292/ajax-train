<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserValidationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    //
    public function store(Request $request)
    {


        $validator = Validator::make($request->all(), [

            'name' => "required|min:3|max:255|regex:/^([a-zA-Z' ]+)$/",
            'password' => 'required|max:255|min:8|regex:$\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$ ',
            'email' => 'required|email|max:255|unique:users',
            'file' => 'required|file|max:2048|mimes:png,jpg,jpeg',

        ]);

        if ($validator->passes()) {

            $data = new User();
            // echo request->
            // $data->name = $request->input('name');
            $data->name =  $validator->validated()['name'];
            // $data->email = $request->input('email');
            $data->email = $validator->validated()['email'];
            // $data->password = Hash::make($request->input('password'));
            $data->password = Hash::make($validator->validated()['password']);

            $imageName = time() . '.' . $validator->validated()['file']->extension();

            $request->file->move(public_path('Users'), $imageName);

            // Save the file name in the user's record
            $data->image = $imageName;

            $data->save();


            return response()->json(['status' => 200]);
        }
        return response()->json(['errors' => $validator->errors()->all()]);




        // echo json_encode(array("status" => 200, "user" => $data));




    }

    public function table()
    {
        $data = User::all();
        return $data;
    }

    public function update(Request $request)
    {

        $value  = $request->value;
        $field = $request->field;
        $id = $request->id;


        if ($field == "name") {

            $name = [
                'name' => $value
            ];
            $validator = Validator::make($name, [

                'name' => "required|min:3|max:255|regex:/^([a-zA-Z' ]+)$/",
            ]);
            // success validation
            if ($validator->passes()) {
                User::where('id', $id)->update(['name' => $validator->validated()['name']]);
                return response()->json(['status' => 200]);
            }
            // failed validation
            else {
                return response()->json(['errors' => $validator->errors()->all()]);
            }
        } else if ($field == "email") {

            $email = [
                'email' => $value
            ];
            $validator = Validator::make($email, [

                'email' => 'required|email|max:255',

            ]);
            // success validation
            if ($validator->passes()) {
                User::where('id', $id)->update(['email' => $validator->validated()['email']]);
                return response()->json(['status' => 200]);
            }
            // failed validation
            else {
                return response()->json(['errors' => $validator->errors()->all()]);
            }
        }
    }
    public function delete(Request $request)
    {
        User::where('id', $request->id)->delete();

        return response()->json(["status" => 200]);
    }

}
