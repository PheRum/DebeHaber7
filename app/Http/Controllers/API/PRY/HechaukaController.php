<?php

namespace App\Http\Controllers\API\PRY;

use App\Transaction;
use App\TransactionDetail;
use App\Taxpayer;
use App\Cycle;
use App\TaxpayerSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Auth;
use DB;
use File;
use ZipArchive;

class HechaukaController extends Controller
{
    public function generateFiles(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate)
    {
        //Get the Integration Once. No need to bring it into the Query.
        $integration = TaxpayerSetting::where('taxpayer_id', $taxPayer->id)
        ->first();

        //TODO: This function is wrong. It will take all files from a path.
        //$files = File::allFiles($path);

        $zipname = 'Hechauka | ' . $taxPayer->name . '-' . Carbon::now()->toDateTimeString() . '.zip';

        $zip = new ZipArchive;
        $zip->open($zipname, ZipArchive::CREATE);

        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        $this->generateSales($startDate, $endDate, $taxPayer, $integration, $zip);
        $this->generatePurchases($startDate, $endDate, $taxPayer, $integration, $zip);
        $zip->close();

        return response()->download($zipname)->deleteFileAfterSend(true);

        return redirect()->back();
    }

    public function generateSales($startDate, $endDate, $taxPayer, $integration, $zip)
    {
        $raw = DB::select('
        select
        max(t.id) as ID,
        max(customer.name) Partner,
        max(customer.taxid) PartnerTaxID,
        max(customer.code) PartnerTaxCode,
        max(t.date) as Date,
        max(t.number) as Number,
        max(t.code) as Code,
        max(t.payment_condition) as PaymentCondition,
        max(t.code_expiry) as CodeExpiry,
        max(t.document_type) as DocumentType,
        ROUND(sum(td.ValueInZero / t.rate)) as ValueInZero,
        ROUND(sum(td.ValueInFive / t.rate)) as ValueInFive,
        ROUND(sum(td.ValueInTen / t.rate)) as ValueInTen
        from transactions as t
        join
        ( select
        max(transaction_id) as transaction_id,
        sum(value) as value,
        max(c.coefficient) as coefficient,
        round(if(max(c.coefficient) = 0, sum(value), 0)) as ValueInZero,
        round(if(max(c.coefficient) = 0.05, sum(value), 0)) as ValueInFive,
        round(if(max(c.coefficient) = 0.1, sum(value), 0)) as ValueInTen
        from transaction_details
        join charts as c on transaction_details.chart_vat_id = c.id
        group by transaction_id, transaction_details.chart_vat_id
        ) as td on td.transaction_id = t.id
        join taxpayers as customer on t.customer_id = customer.id
        where (t.supplier_id = ' . $taxPayer->id . '
        and t.deleted_at is null
        and t.date between "' . $startDate . '" and "' . $endDate . '" and t.type in (3, 4))
        group by t.id');

        $raw = collect($raw);
        $i = 1;

        foreach ($raw->chunk(15000) as $data)
        {
            $taxPayerTaxID = $taxPayer->taxid;
            $taxPayerTaxCode = $taxPayer->code;

            if (isset($integration))
            {
                $agentName = $integration->agent_name;
                $agentTaxID = $integration->agent_taxid;
                $agentTaxCode = $this->calculateTaxCode($integration->agent_taxid);
            }

            $obligationCode = 921;
            $formCode = 221;

            $date = Carbon::parse($data->first()->Date);
            $dateCode = $date->format('Y') . $date->format('m');

            $fileName = 'Hechauka Ventas #' . $i . ' | ' . Carbon::now()->toDateTimeString() . '.txt';

            $detail = '';

            $count = 0;

            //todo this is wrong. Your foreachs hould be smaller
            if ($data->where('PartnerTaxID', '44444401')->count() > 0)
            {
                $count += 1;
                $date = Carbon::parse($data->first()->Date);

                $Total10 = $data->where('PartnerTaxID', '44444401')->sum('ValueInTen');
                $VAT10 = round($Total10 / 11);
                $Taxable10 = $Total10 - $VAT10;

                $Total5 = $data->where('PartnerTaxID', '44444401')->sum('ValueInFive');
                $VAT5 = round($Total5 / 21);
                $Taxable5 = $Total5 - $VAT5;

                //Check if Partner has TaxID and TaxCode properly coded, or else substitute for generic user.
                $detail = $detail .
                /* 1 */ ' 2 ' .
                /* 2 */ " \t " . '44444401' .
                /* 3 */ " \t " . '7' .
                /* 4 */ " \t " . 'Consumidor Final' .
                /* 5 */ " \t " . '0' . //($data->first()->DocumentType) .
                /* 6 */ " \t " . '0' . //($data->first()->Number) .
                /* 7 */ " \t " . (date_format($date, 'd/m/Y') ).
                /* 8 */ " \t " . ($Taxable10) .
                /* 9 */ " \t " . ($VAT10) .// ($data->where('PartnerTaxID', '44444401')->sum('VATInTen')).
                /* 10 */ " \t " . ($Taxable5).
                /* 11 */ " \t " . ($VAT5) .
                /* 12 */ " \t " . ($data->where('PartnerTaxID', '44444401')->sum('ValueInZero')) .
                /* 13 */ " \t " . ($data->where('PartnerTaxID', '44444401')->sum('ValueInTen') + $data->where('PartnerTaxID', '44444401')->sum('ValueInFive') + $data->where('PartnerTaxID', '44444401')->sum('ValueInZero')) .
                /* 14 */ " \t " . ($data->first()->PaymentCondition == 0 ? 1 : 2) .
                /* 15 */ " \t " . ($data->first()->PaymentCondition ).
                /* 16 */ " \t " . '0' /*($data->first()->Code)*/ . " \r\n ";
            }

            //todo this is wrong. Your foreachs hould be smaller
            foreach ($data->where('PartnerTaxID', '!=', '44444401') as  $row)
            {
                $count += 1;

                $Total10 = $row->ValueInTen;
                $VAT10 = round($Total10 / 11);
                $Taxable10 = $Total10 - $VAT10;

                $Total5 = $row->ValueInFive;
                $VAT5 = round($Total5 / 21);
                $Taxable5 = $Total5 - $VAT5;

                $date = Carbon::parse($row->Date);
                //Check if Partner has TaxID and TaxCode properly coded, or else substitute for generic user.
                $detail = $detail .
                /* 1 */ ' 2 ' .
                /* 2 */ " \t " . ($row->PartnerTaxID) .
                /* 3 */ " \t " . ($this->calculateTaxCode($row->PartnerTaxID)) .
                /* 4 */ " \t " . ($row->Partner) .
                /* 5 */ " \t " . ($row->DocumentType) .
                /* 6 */ " \t " . ($row->Number) .
                /* 7 */ " \t " . (date_format($date, 'd/m/Y')) .
                /* 8 */ " \t " . ($Taxable10) .
                /* 9 */ " \t " . ($VAT10) .
                /* 10 */ " \t " . ($Taxable5).
                /* 11 */ " \t " . ($VAT5) .
                /* 12 */ " \t " . ($row->ValueInZero) .
                /* 13 */ " \t " . ($row->ValueInTen + $row->ValueInFive + $row->ValueInZero) .
                /* 14 */ " \t " . ($row->PaymentCondition == 0 ? 1 : 2) .
                /* 15 */ " \t " . ($row->PaymentCondition ).
                /* 16 */ " \t " . ($row->Code) . " \r\n ";
            }

            $header =
            /* 1 */ ' 1 ' .
            /* 2 */ " \t " . $dateCode .
            /* 3 */ " \t " . '1' .
            /* 4 */ " \t " . $obligationCode .
            /* 5 */ " \t " . $formCode .
            /* 6 */ " \t " . $taxPayerTaxID .
            /* 7 */ " \t " . $taxPayerTaxCode .
            /* 8 */ " \t " . $taxPayer->name .
            /* 9 */ " \t " . $agentTaxID .
            /* 10 */ " \t " . $agentTaxCode .
            /* 11 */ " \t " . $agentName .
            /* 12 */ " \t " . ($count) .
            /* 13 */ " \t " . (($data->sum('ValueInTen') ?? 0) + ($data->sum('ValueInFive') ?? 0 ) + ($data->sum('ValueInZero') ?? 0)).
            /* 14 */ " \t " . "2 \r\n ";


            //Improve Naming convention, also add Taxpayer Folder.
            Storage::disk('local')->append($fileName, $header);

            //Maybe save to string variable frist, and then append at the end.
            Storage::disk('local')->append($fileName, $detail);

            $file = Storage::disk('local');

            $path = $file->getDriver()->getAdapter()->getPathPrefix();

            $zip->addFile($path . $fileName, $fileName);

            //$file->delete($fileName);

            $i += 1;
        }
    }

    public function generatePurchases($startDate, $endDate, $taxPayer, $integration, $zip)
    {
        $raw = DB::select('select
        max(t.id) as ID,
        max(supplier.name) as Partner,
        max(supplier.taxid) as PartnerTaxID,
        max(supplier.code) as PartnerTaxCode,
        max(t.date) as Date,
        max(t.number) as Number,
        max(t.code) as Code,
        max(t.code_expiry) as CodeExpiry,
        max(t.payment_condition) as PaymentCondition,
        max(t.document_type) as DocumentType,
        ROUND(sum(td.ValueInZero / t.rate)) as ValueInZero,
        ROUND(sum(td.ValueInFive / t.rate)) as ValueInFive,
        ROUND(sum(td.ValueInTen / t.rate)) as ValueInTen
        from transactions as t
        join
        ( select
        max(transaction_id) as transaction_id,
        sum(value) as value,
        max(c.coefficient) as coefficient,
        if(max(c.coefficient) = 0, sum(value), 0) as ValueInZero,
        if(max(c.coefficient) = 0.05, sum(value), 0) as ValueInFive,
        if(max(c.coefficient) = 0.1, sum(value), 0) as ValueInTen
        from transaction_details
        join charts as c on transaction_details.chart_vat_id = c.id
        group by transaction_id, transaction_details.chart_vat_id
        ) as td on td.transaction_id = t.id
        join taxpayers as supplier on t.supplier_id = supplier.id
        where
        ( t.customer_id = ' . $taxPayer->id . ' and
        t.deleted_at is null and
        t.date between "' . $startDate . '" and "' . $endDate . '" and
        t.type in (1, 2, 5) )
        group by t.id');

        $raw = collect($raw);
        $i = 1;

        foreach ($raw->chunk(15000) as $data)
        {
            $taxPayerTaxID = $taxPayer->taxid;
            $taxPayerTaxCode = $taxPayer->code;

            if (isset($integration))
            {
                $agentName = $integration->agent_name;
                $agentTaxID = $integration->agent_taxid;
                $agentTaxCode = $this->calculateTaxCode($integration->agent_taxid);;
            }

            $obligationCode = 911;
            $formCode = 211;

            $date = Carbon::parse($data->first()->Date);
            $dateCode = $date->format('Y') . $date->format('m');

            $fileName = 'Hechauka Compras #' . $i . ' | ' . Carbon::now()->toDateTimeString() . '.txt';

            $totalTotal10 = $data->sum('ValueInTen');
            $totalVAT10 = round($totalTotal10 / 11);
            $totalTaxable10 = ($totalTotal10 - $totalVAT10) ?? 0;

            $totalTotal5 = $data->sum('ValueInFive');
            $totalVAT5 = round($totalTotal5 / 21);
            $totalTaxable5 = ($totalTotal5 - $totalVAT5) ?? 0;

            $header =
            /* 1 */ ' 1 ' .
            /* 2 */ " \t " . ($dateCode) .
            /* 3 */ " \t " . '1' .
            /* 4 */ " \t " . ($obligationCode) .
            /* 5 */ " \t " . ($formCode) .
            /* 6 */ " \t " . ($taxPayerTaxID) .
            /* 7 */ " \t " . ($taxPayerTaxCode) .
            /* 8 */ " \t " . ($taxPayer->name) .
            /* 9 */ " \t " . ($agentTaxID) .
            /* 10 */ " \t " . ($agentTaxCode) .
            /* 11 */ " \t " . ($agentName) .
            /* 12 */ " \t " . ($data->count() ?? 0) .
            /* 13 */ " \t " . ($totalTaxable10 + $totalTaxable5 + ($data->sum('ValueInZero') ?? 0)).
            /* 14 */ " \t " . ($integration->regime_type == 1 ? 'Si' : 'No' ) .
            /* 15 */ " \t " . "2 \r\n ";


            //Improve Naming convention, also add Taxpayer Folder.
            Storage::disk('local')->append($fileName, $header);

            $detail = '';

            //todo this is wrong. Your foreachs hould be smaller
            foreach ($data as  $row)
            {
                $Total10 = $row->ValueInTen;
                $VAT10 = round($Total10 / 11);
                $Taxable10 = $Total10 - $VAT10;

                $Total5 = $row->ValueInFive;
                $VAT5 = round($Total5 / 21);
                $Taxable5 = $Total5 - $VAT5;

                $date = Carbon::parse($row->Date);
                //Check if Partner has TaxID and TaxCode properly coded, or else substitute for generic user.
                $detail = $detail .
                /* 1 */ ' 2 ' .
                /* 2 */ " \t " . ($row->PartnerTaxID) .
                /* 3 */ " \t " . ($row->PartnerTaxCode) .
                /* 4 */ " \t " . ($row->Partner) .
                /* 5 */ " \t " . ($row->Code) .
                /* 6 */ " \t " . ($row->DocumentType) .
                /* 7 */ " \t " . ($row->Number) .
                /* 8 */ " \t " . (date_format($date, 'd/m/Y')) .
                /* 9 */ " \t " . ($Taxable10) .
                /* 10 */ " \t " . ($VAT10) .
                /* 11 */ " \t " . ($Taxable5) .
                /* 12 */ " \t " . ($VAT5) .
                /* 13 */ " \t " . ($row->ValueInZero) .
                /* 14 */ " \t " . 0 .
                /* 15 */ " \t " . ($row->PaymentCondition == 0 ? 1 : 2) .
                /* 16 */ " \t " . ($row->PaymentCondition > 0 ? 1 : 0) . " \r\n ";
            }

            //Maybe save to string variable frist, and then append at the end.
            Storage::disk('local')->append($fileName, $detail);

            $file = Storage::disk('local');
            $path = $file->getDriver()->getAdapter()->getPathPrefix();

            $zip->addFile($path . $fileName, $fileName);

            //$file->delete($fileName);

            $i += 1;
        }
    }

    public function dividirCodigo($codigo)
    {
        return $code = explode("-", $codigo);
    }
}
