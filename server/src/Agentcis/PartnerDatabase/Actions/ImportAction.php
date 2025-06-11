<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\Imports\PartnerDatabaseImport;
use Agentcis\PartnerDatabase\Jobs\ImportPartnerDatabaseFromImportEvent;
use Agentcis\PartnerDatabase\Model\ImportEvent;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class ImportAction
{
    use ValidatesRequests;

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function __invoke(Request $request)
    {
        $this->validate($request, [
            'file' => 'required'
        ]);

        $file = $request->file('file');
        $sheetName = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $file->storePubliclyAs('import/sheets/', $sheetName);

        $importEvent = new ImportEvent();
        $importEvent->setAttribute('id', Str::orderedUuid());
        $importEvent->setAttribute('file_path', 'import/sheets/'. $sheetName);
        $importEvent->setAttribute('user_id', $request->user()->id);

        $importEvent->save();
        ImportPartnerDatabaseFromImportEvent::dispatch($importEvent)->onQueue('import');

        return new JsonResponse();
    }
}
