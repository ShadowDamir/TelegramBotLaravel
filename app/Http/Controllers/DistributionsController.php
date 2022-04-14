<?php

namespace App\Http\Controllers;

use App\Models\Distribution;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

class DistributionsController extends Controller
{
    public function create() {
        $distributions = Distribution::orderByDesc('sendingDate')->get();
        return view('distributions.distributions',compact('distributions'));
    }

    public function getInfo($Id) {
        $distribution = Distribution::find($Id)->first();
        if($distribution != null) return view("distributions.distributionInfo",compact('distribution'));
        else return back();
    }

    public function editInfo(Request $request) {
        Distribution::find($request['id'])->first();
    }

    public function createDistribution(){
        return view('distributions.createDistribution');
    }

    public function delete($id){
        $distr = Distribution::find($id)->first();
        $distr->delete();
        return $this->create();
    }

    public function createDistributionPost(Request $request){
         if ($request['messageText'] == null) {
             return back()->withErrors([ 'messageText' => 'Должно быть введено сообщение.',])->onlyInput('messageText');
         }
         if($request['sendingDate'] == null) {
             return back()->withErrors([ 'sendingDate' => 'Необходимо указать дату отправки рассылки.',])->onlyInput('sendingDate');
         }

         $params = [
             'messageText'=>$request['messageText'],
             'sendingDate'=>$request['sendingDate'],
             'creatorId'=>auth()->user()->id,
             ];
        if($request->hasFile('image')) {
            if(Str::length($request['messageText']) > 1024)  return back()->withErrors([ 'messageText' => 'Если добавлено изображение, то количество символов сообщения не должно превышать 1024.',])->onlyInput('messageText');

            $size = $request->file('image')->getSize();
            if($size >= 10485760) {
                return back()->withErrors([ 'image' => $size.'Можно вставить изображение размером до 10 МБ.',])->onlyInput('image');
            }
            $fileId = $this->fileUpload($request['image']);
            $params = Arr::add($params,'image',$fileId);
        }
        $distribution = new Distribution($params);
        $distribution->save();
        return $this->create();
    }

    private function fileUpload($image)  {
        $fileModel = new File;
        if($image) {
            $fileName = time().'_'.$image->getClientOriginalName();
            $filePath = $image->storeAs('uploads', $fileName, 'public');
            $fileModel->name = time().'_'.$image->getClientOriginalName();
            $fileModel->file_path = 'public/' . $filePath;
            $fileModel->save();
            return $fileModel->id;
        }
        return null;
    }
}
