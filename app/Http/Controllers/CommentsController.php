<?php

namespace App\Http\Controllers;

use DB;
use App\Comments;
use Exception;
use App\Libraries\TReq;
use App\Libraries\Res;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentsController extends Controller
{
    public function select(Request $request, $post){
        try {
            $query = TReq::multiple($request, Comments::class);
            $data = DB::table('tb_paylasimlar')->where('takipEdilenID', $request->user()->ID)->count();
            $result = [
                'metadata' => [
                    'count' => 1,
                    'offset' => $query['offset'],
                    'limit' => $query['limit'],
                ],
                'data' => $data
            ];

            return Res::success(200, 'Users', $result);
        } catch (Exception $e) {
            $error = new \stdClass();
            $error->errors = [
                'exception' => [
                    $e->getMessage()
                ]
            ];
            $message = 'An error has occured!';
            return Res::fail(500, $message, $error);
        }
    }

    public function create(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'yorum'       => 'required|filled',
                'icerik_tipi' => 'required|filled',
                'paylasim_id' => 'required|filled',
            ]);

            if($validator->fails()){
                throw new Exception($validator->errors(), 400);
            }

            $create = Comments::insert([
                'yorum'       => $request->yorum,
                'kullanici_id'=> $request->user()->ID,
                'paylasim_id' => $request->paylasim_id,
                'icerik_tipi' => $request->icerik_tipi,
            ]);

            if(!$create){
                throw new Exception($validator->errors(), 400);
            }

            return Res::success(200, 'comments', 'success');

        }catch(Exception $e) {
            $error = new \stdClass();
            $error->errors = [
                'exception' => [
                    $e->getMessage()
                ]
            ];
            $message = 'An error has occured!';
            return Res::fail(500, $message, $error);
        }
    }

    public function update(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
               'yorum'    => 'required|filled',
               'yorum_id' => 'required|filled',
            ]);

            if($validator->fails()){
                throw new Exception($validator->errors(), 400);
            }

            $update = Comments::where([
                'yorum_id'     => $request->yorum_id,
                'kullanici_id' => $request->user()->ID,
            ])->update([
                'yorum' => $request->yorum
            ]);

            if(!$update){
                throw new Exception($validator->errors(), 400);
            }

            return res::success(200, 'comment', 'success');

        }catch(Exception $e) {
            $error = new \stdClass();
            $error->errors = [
                'exception' => [
                    $e->getMessage()
                ]
            ];
            $message = 'An error has occured!';
            return Res::fail(500, $message, $error);
        }
    }

    public function delete(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'yorum_id' => 'required'
            ]);

            if($validator->fails()){
                throw new Exception($validator->errors(), 400);
            }

            if(!Comments::where(['kullanici_id' => $request->user()->ID, 'yorum_id' => $request->yorum_id])->delete()){
                throw new Exception('an error', 400);
            }
            return Res::success(200,'success', 'success');
        }catch(Exception $e) {
            $error = new \stdClass();
            $error->errors = [
                'exception' => [
                    $e->getMessage()
                ]
            ];
            $message = 'An error has occured!';
            return Res::fail(500, $message, $error);
        }
    }
}
