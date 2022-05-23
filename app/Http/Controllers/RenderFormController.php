<?php
namespace jazmy\FormBuilder\Controllers;

use App\Http\Controllers\Controller;
use jazmy\FormBuilder\Helper;
use jazmy\FormBuilder\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class RenderFormController extends Controller
{
    public function __construct()
    {
        $this->middleware('public-form-access');
    }

    public function render($identifier)
    {
        $form = Form::where('identifier', $identifier)->firstOrFail();
        $pageTitle = "{$form->name}";
        return view('formbuilder::render.index', compact('form', 'pageTitle'));
    }

    public function submit(Request $request, $identifier)
    {
        $form = Form::where('identifier', $identifier)->firstOrFail();
        DB::beginTransaction();

        try {
            $input = $request->except('_token');
            // check if files were uploaded and process them
            $uploadedFiles = $request->allFiles();
            foreach ($uploadedFiles as $key => $file) {
                // store the file and set it's path to the value of the key holding it
                if ($file->isValid()) {
                    $input[$key] = $file->store('fb_uploads', 'public');
                }
            }

            $user_id = auth()->user()->id ?? null;
            $form->submissions()->create([
                'user_id' => $user_id,
                'content' => $input,
            ]);

            DB::commit();
            return redirect()->route('formbuilder::form.feedback', $identifier)->with('success', 'Form successfully submitted.');
        } catch (Throwable $e) {
            info($e);
            DB::rollback();
            return back()->withInput()->with('error', Helper::wtf());
        }
    }

    public function feedback($identifier)
    {
        $form = Form::where('identifier', $identifier)->firstOrFail();
        $pageTitle = "Form Submitted!";
        return view('formbuilder::render.feedback', compact('form', 'pageTitle'));
    }
}
