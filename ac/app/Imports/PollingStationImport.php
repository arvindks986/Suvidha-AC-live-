<?php

namespace App\Imports;

use App\models\Admin\PollingStation;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class PollingStationImport implements ToModel, WithValidation, WithHeadingRow, WithStartRow
{
    use Importable;

    public $ele_details = null;

    public function  __construct($ele_details)
    {
        $this->ele_details = $ele_details;
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        PollingStation::updateOrCreate([
            'election_id' => $this->ele_details->ELECTION_ID,
            'ST_CODE' => $this->ele_details->ST_CODE,
            'AC_NO' => $this->ele_details->CONST_NO,
            'PS_NO'    => trim($row['ps_no']),
            'scheduleid' => $this->ele_details->ScheduleID,
        ], [
            'PART_NO'     => trim($row['part_no']),
            'PART_NAME' => $row['part_name'],
            'PS_NAME_EN' => $row['ps_name_en'],
            'PS_TYPE' => trim($row['ps_type']),
            'PS_CATEGORY' => trim($row['ps_category']),
            'LOCN_TYPE' => trim($row['locn_type']),
            'electors_male' => trim($row['electors_male']),
            'electors_female' => trim($row['electors_female']),
            'electors_other' => trim($row['electors_other']),
            'electors_total' => trim($row['electors_total']),
        ]);
    }

    public function rules(): array
    {
        return [
            'part_no' => ['required', 'alpha_num'],
            '*.part_no' => ['required', 'alpha_num'],
            'ps_no' => ['required', 'alpha_num'],
            '*.ps_no' => ['required', 'alpha_num'],
            'part_name' => ['required', 'string'],
            '*.part_name' => ['required', 'string'],
            'ps_name_en' => ['required', 'string'],
            '*.ps_name_en' => ['required', 'string'],
            // 'ps_type' => ['required', 'string', 'in:M,A'],
            // '*.ps_type' => ['required', 'string', 'in:M,A'],
            // 'ps_category' => ['required', 'string', 'in:G,N'],
            // '*.ps_category' => ['required', 'string', 'in:G,N'],
            // 'locn_type' => ['required', 'string', 'in:U,R'],
            // '*.locn_type' => ['required', 'string', 'in:U,R'],
            // 'electors_male' => ['required', 'integer'],
            // '*.electors_male' => ['required', 'integer'],
            // 'electors_female' => ['required', 'integer'],
            // '*.electors_female' => ['required', 'integer'],
            // 'electors_other' => ['required', 'integer'],
            // '*.electors_other' => ['required', 'integer'],
            // 'electors_total' => ['required', 'integer'],
            // '*.electors_total' => ['required', 'integer'],

        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.ps_type.in' => 'Invalid PS Type.',
            'ps_type.in' => 'Invalid PS Type.',
            '*.ps_category.in' => 'Invalid PS Category.',
            'ps_category.in' => 'Invalid PS Category',
            '*.locn_type.in' => 'Invalid Location Type',
            'locn_type.in' => 'Invalid Location Type',
        ];
    }
}
